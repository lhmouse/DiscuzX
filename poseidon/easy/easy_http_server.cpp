// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../precompiled.ipp"
#include "easy_http_server.hpp"
#include "../socket/listen_socket.hpp"
#include "../static/network_driver.hpp"
#include "../fiber/abstract_fiber.hpp"
#include "../static/fiber_scheduler.hpp"
#include "../utils.hpp"
namespace poseidon {
namespace {

struct Client_Table
  {
    struct Event_Queue
      {
        // read-only fields; no locking needed
        shptr<HTTP_Server_Session> session;
        cacheline_barrier xcb_1;

        // shared fields between threads
        struct Event
          {
            HTTP_Request_Headers req;
            linear_buffer data;
            bool close_now;
            uint32_t has_status;
          };

        deque<Event> events;
        bool fiber_active = false;
      };

    mutable plain_mutex mutex;
    unordered_map<const volatile HTTP_Server_Session*, Event_Queue> client_map;
  };

struct Final_Fiber final : Abstract_Fiber
  {
    Easy_HTTP_Server::thunk_type m_thunk;
    wkptr<Client_Table> m_wtable;
    const volatile HTTP_Server_Session* m_refptr;

    explicit
    Final_Fiber(const Easy_HTTP_Server::thunk_type& thunk, const shptr<Client_Table>& table, const volatile HTTP_Server_Session* refptr)
      : m_thunk(thunk), m_wtable(table), m_refptr(refptr)
      { }

    virtual
    void
    do_abstract_fiber_on_work()
      {
        for(;;) {
          // The event callback may stop this server, so we have to check for
          // expiry in every iteration.
          auto table = this->m_wtable.lock();
          if(!table)
            return;

          // Pop an event and invoke the user-defined callback here in the
          // main thread. Exceptions are ignored.
          plain_mutex::unique_lock lock(table->mutex);

          auto client_iter = table->client_map.find(this->m_refptr);
          if(client_iter == table->client_map.end())
            return;

          if(client_iter->second.events.empty()) {
            // Terminate now.
            client_iter->second.fiber_active = false;
            return;
          }

          // After `table->mutex` is unlocked, other threads may modify
          // `table->client_map` and invalidate all iterators, so maintain a
          // reference outside it for safety.
          auto queue = &(client_iter->second);
          ROCKET_ASSERT(queue->fiber_active);
          auto session = queue->session;
          auto event = ::std::move(queue->events.front());
          queue->events.pop_front();

          if(ROCKET_UNEXPECT(event.close_now)) {
            // This will be the last event on this session.
            queue = nullptr;
            table->client_map.erase(client_iter);
          }
          client_iter = table->client_map.end();
          lock.unlock();

          try {
            if(event.has_status == 0) {
              // Process a request.
              this->m_thunk(session, *this, ::std::move(event.req), ::std::move(event.data));
            }
            else {
              // Send a bad request response.
              HTTP_Response_Headers resp;
              resp.status = event.has_status;
              resp.headers.emplace_back(sref("Connection"), sref("close"));
              session->http_response(::std::move(resp), "", 0);
            }

            if(event.close_now)
              session->tcp_close();
          }
          catch(exception& stdex) {
            // Shut the connection down with a message.
            // XXX: The user-defined callback may have sent a response...?
            HTTP_Response_Headers resp;
            resp.status = HTTP_STATUS_INTERNAL_SERVER_ERROR;
            resp.headers.emplace_back(sref("Connection"), sref("close"));
            session->http_response(::std::move(resp), "", 0);

            session->tcp_close();

            POSEIDON_LOG_ERROR((
                "Unhandled exception thrown from easy HTTP client: $1"),
                stdex);
          }
        }
      }
  };

struct Final_HTTP_Server_Session final : HTTP_Server_Session
  {
    Easy_HTTP_Server::thunk_type m_thunk;
    wkptr<Client_Table> m_wtable;

    explicit
    Final_HTTP_Server_Session(unique_posix_fd&& fd, const Easy_HTTP_Server::thunk_type& thunk, const shptr<Client_Table>& table)
      : HTTP_Server_Session(::std::move(fd)), m_thunk(thunk), m_wtable(table)
      { }

    virtual
    void
    do_abstract_socket_on_closed() override
      {
        HTTP_Server_Session::do_abstract_socket_on_closed();

        auto table = this->m_wtable.lock();
        if(!table)
          return;

        // We are in the network thread here.
        plain_mutex::unique_lock lock(table->mutex);

        table->client_map.erase(this);
      }

    virtual
    void
    do_on_http_request_finish(HTTP_Request_Headers&& req, linear_buffer&& data, bool close_now) override
      {
        auto table = this->m_wtable.lock();
        if(!table)
          return;

        // We are in the network thread here.
        plain_mutex::unique_lock lock(table->mutex);

        auto client_iter = table->client_map.find(this);
        if(client_iter == table->client_map.end())
          return;

        if(!client_iter->second.fiber_active) {
          // Create a new fiber, if none is active. The fiber shall only reset
          // `m_fiber_private_buffer` if no event is pending.
          fiber_scheduler.launch(new_sh<Final_Fiber>(this->m_thunk, table, this));
          client_iter->second.fiber_active = true;
        }

        auto& event = client_iter->second.events.emplace_back();
        event.req = ::std::move(req);
        event.data = ::std::move(data);
        event.close_now = close_now;
      }

    virtual
    void
    do_on_http_request_error(uint32_t status) override
      {
        auto table = this->m_wtable.lock();
        if(!table)
          return;

        // We are in the network thread here.
        plain_mutex::unique_lock lock(table->mutex);

        auto client_iter = table->client_map.find(this);
        if(client_iter == table->client_map.end())
          return;

        if(!client_iter->second.fiber_active) {
          // Create a new fiber, if none is active. The fiber shall only reset
          // `m_fiber_private_buffer` if no event is pending.
          fiber_scheduler.launch(new_sh<Final_Fiber>(this->m_thunk, table, this));
          client_iter->second.fiber_active = true;
        }

        auto& event = client_iter->second.events.emplace_back();
        event.has_status = status;
        event.close_now = true;
      }
  };

struct Final_Listen_Socket final : Listen_Socket
  {
    Easy_HTTP_Server::thunk_type m_thunk;
    wkptr<Client_Table> m_wtable;

    explicit
    Final_Listen_Socket(const Socket_Address& addr, const Easy_HTTP_Server::thunk_type& thunk, const shptr<Client_Table>& table)
      : Listen_Socket(addr), m_thunk(thunk), m_wtable(table)
      { }

    virtual
    shptr<Abstract_Socket>
    do_on_listen_new_client_opt(Socket_Address&& addr, unique_posix_fd&& fd) override
      {
        auto table = this->m_wtable.lock();
        if(!table)
          return nullptr;

        auto session = new_sh<Final_HTTP_Server_Session>(::std::move(fd), this->m_thunk, table);
        (void) addr;

        // We are in the network thread here.
        plain_mutex::unique_lock lock(table->mutex);

        auto r = table->client_map.try_emplace(session.get());
        ROCKET_ASSERT(r.second);
        r.first->second.session = session;
        return session;
      }
  };

}  // namespace

struct Easy_HTTP_Server::X_Client_Table : Client_Table
  {
  };

Easy_HTTP_Server::
~Easy_HTTP_Server()
  {
  }

void
Easy_HTTP_Server::
start(const Socket_Address& addr)
  {
    auto table = new_sh<X_Client_Table>();
    auto socket = new_sh<Final_Listen_Socket>(addr, this->m_thunk, table);

    network_driver.insert(socket);
    this->m_client_table = ::std::move(table);
    this->m_socket = ::std::move(socket);
  }

void
Easy_HTTP_Server::
stop() noexcept
  {
    this->m_client_table = nullptr;
    this->m_socket = nullptr;
  }

const Socket_Address&
Easy_HTTP_Server::
local_address() const noexcept
  {
    if(!this->m_socket)
      return ipv6_invalid;

    return this->m_socket->local_address();
  }

}  // namespace poseidon