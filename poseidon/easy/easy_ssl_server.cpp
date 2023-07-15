// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../precompiled.ipp"
#include "easy_ssl_server.hpp"
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
        shptr<SSL_Socket> socket;
        cacheline_barrier xcb_1;

        // fiber-private fields; no locking needed
        linear_buffer data_stream;
        cacheline_barrier xcb_2;

        // shared fields between threads
        struct Event
          {
            Connection_Event type;
            linear_buffer data;
            int code;
          };

        deque<Event> events;
        bool fiber_active = false;
      };

    mutable plain_mutex mutex;
    unordered_map<const volatile SSL_Socket*, Event_Queue> client_map;
  };

struct Final_Fiber final : Abstract_Fiber
  {
    Easy_SSL_Server::thunk_type m_thunk;
    wkptr<Client_Table> m_wtable;
    const volatile SSL_Socket* m_refptr;

    explicit
    Final_Fiber(const Easy_SSL_Server::thunk_type& thunk,
          const shptr<Client_Table>& table, const volatile SSL_Socket* refptr)
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
          auto socket = queue->socket;
          auto event = ::std::move(queue->events.front());
          queue->events.pop_front();

          if(ROCKET_UNEXPECT(event.type == connection_closed)) {
            // This will be the last event on this socket.
            queue = nullptr;
            table->client_map.erase(client_iter);
          }
          client_iter = table->client_map.end();
          lock.unlock();

          try {
            if(event.type == connection_stream) {
              // `connection_stream` is special. We append new data to
              // `data_stream` which is then passed to the callback instead of
              // `event.data`. `data_stream` may be consumed partially by user
              // code, and shall be preserved across callbacks.
              queue->data_stream.putn(event.data.data(), event.data.size());
              this->m_thunk(socket, *this, event.type, queue->data_stream, event.code);
            }
            else
              this->m_thunk(socket, *this, event.type, event.data, event.code);
          }
          catch(exception& stdex) {
            // Shut the connection down asynchronously. Pending output data
            // are discarded, but the user-defined callback will still be called
            // for remaining input data, in case there is something useful.
            socket->quick_close();

            POSEIDON_LOG_ERROR((
                "Unhandled exception thrown from easy SSL client: $1"),
                stdex);
          }
        }
      }
  };

struct Final_SSL_Socket final : SSL_Socket
  {
    Easy_SSL_Server::thunk_type m_thunk;
    wkptr<Client_Table> m_wtable;

    explicit
    Final_SSL_Socket(unique_posix_fd&& fd,
          const Easy_SSL_Server::thunk_type& thunk, const shptr<Client_Table>& table)
      : SSL_Socket(::std::move(fd)), m_thunk(thunk), m_wtable(table)
      { }

    void
    do_push_event_common(Connection_Event type, linear_buffer&& data, int code) const
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
        event.type = type;
        event.data = ::std::move(data);
        event.code = code;
      }

    virtual
    void
    do_on_ssl_connected() override
      {
        linear_buffer data;
        this->do_push_event_common(connection_open, ::std::move(data), 0);
      }

    virtual
    void
    do_on_ssl_stream(linear_buffer& data, bool eof) override
      {
        this->do_push_event_common(connection_stream, ::std::move(data), eof);
        data.clear();
      }

    virtual
    void
    do_abstract_socket_on_closed() override
      {
        int sys_errno = errno;
        linear_buffer data;
        char sbuf[1024];
        data.puts(::strerror_r(sys_errno, sbuf, sizeof(sbuf)));
        this->do_push_event_common(connection_closed, ::std::move(data), sys_errno);
      }
  };

struct Final_Listen_Socket final : Listen_Socket
  {
    Easy_SSL_Server::thunk_type m_thunk;
    wkptr<Client_Table> m_wtable;

    explicit
    Final_Listen_Socket(const Socket_Address& addr, const Easy_SSL_Server::thunk_type& thunk, const shptr<Client_Table>& table)
      : Listen_Socket(addr), m_thunk(thunk), m_wtable(table)
      { }

    virtual
    shptr<Abstract_Socket>
    do_on_listen_new_client_opt(Socket_Address&& addr, unique_posix_fd&& fd) override
      {
        auto table = this->m_wtable.lock();
        if(!table)
          return nullptr;

        auto socket = new_sh<Final_SSL_Socket>(::std::move(fd), this->m_thunk, table);
        (void) addr;

        // We are in the network thread here.
        plain_mutex::unique_lock lock(table->mutex);

        auto r = table->client_map.try_emplace(socket.get());
        ROCKET_ASSERT(r.second);
        r.first->second.socket = socket;
        return socket;
      }
  };

}  // namespace

POSEIDON_HIDDEN_X_STRUCT(Easy_SSL_Server, Client_Table);

Easy_SSL_Server::
~Easy_SSL_Server()
  {
  }

void
Easy_SSL_Server::
start(const Socket_Address& addr)
  {
    auto table = new_sh<X_Client_Table>();
    auto socket = new_sh<Final_Listen_Socket>(addr, this->m_thunk, table);

    network_driver.insert(socket);
    this->m_client_table = ::std::move(table);
    this->m_socket = ::std::move(socket);
  }

void
Easy_SSL_Server::
stop() noexcept
  {
    this->m_client_table = nullptr;
    this->m_socket = nullptr;
  }

const Socket_Address&
Easy_SSL_Server::
local_address() const noexcept
  {
    if(!this->m_socket)
      return ipv6_invalid;

    return this->m_socket->local_address();
  }

}  // namespace poseidon
