// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../precompiled.ipp"
#include "easy_ssl_client.hpp"
#include "../static/network_driver.hpp"
#include "../fiber/abstract_fiber.hpp"
#include "../static/fiber_scheduler.hpp"
#include "../utils.hpp"
namespace poseidon {
namespace {

struct Event_Queue
  {
    mutable plain_mutex mutex;
    wkptr<SSL_Socket> wsocket;  // read-only; no locking needed
    linear_buffer fiber_private_buffer;  // by fibers only; no locking needed

    struct Event
      {
        Connection_Event type;
        linear_buffer data;
      };

    deque<Event> events;
    bool fiber_active = false;
  };

struct Shared_cb_args
  {
    wkptr<void> wobj;
    callback_thunk_ptr<shptrR<SSL_Socket>, Connection_Event, linear_buffer&> thunk;
    wkptr<Event_Queue> wqueue;
  };

struct Final_Fiber final : Abstract_Fiber
  {
    Shared_cb_args m_cb;

    explicit
    Final_Fiber(const Shared_cb_args& cb)
      : m_cb(cb)  { }

    virtual
    void
    do_abstract_fiber_on_work()
      {
        for(;;) {
          auto cb_obj = this->m_cb.wobj.lock();
          if(!cb_obj)
            return;

          // The event callback may stop this client, so we have to check for
          // expiry in every iteration.
          auto queue = this->m_cb.wqueue.lock();
          if(!queue)
            return;

          auto socket = queue->wsocket.lock();
          if(!socket)
            return;

          // We are in the main thread here.
          plain_mutex::unique_lock lock(queue->mutex);

          if(queue->events.empty()) {
            // Leave now.
            queue->fiber_active = false;
            return;
          }

          ROCKET_ASSERT(queue->fiber_active);
          auto event = ::std::move(queue->events.front());
          queue->events.pop_front();
          auto fiber_private_buffer = &(queue->fiber_private_buffer);

          if(event.type != connection_event_stream)
            fiber_private_buffer = nullptr;

          lock.unlock();
          queue = nullptr;

          try {
            // Invoke the user-defined event callback.
            // `connection_event_stream` is special. We append new data to the
            // private buffer which is then passed to the callback instead of
            // `event.data`. The private buffer is preserved across callbacks
            // and may be consumed partially by user code.
            if(event.type == connection_event_stream)
              this->m_cb.thunk(cb_obj.get(), socket, event.type,
                      fiber_private_buffer->empty()
                         ? fiber_private_buffer->swap(event.data)
                         : fiber_private_buffer->putn(event.data.data(), event.data.size()));
            else
              this->m_cb.thunk(cb_obj.get(), socket, event.type, event.data);
          }
          catch(exception& stdex) {
            POSEIDON_LOG_ERROR((
                "Unhandled exception thrown from easy SSL client: $1"),
                stdex);

            socket->quick_shut_down();
            continue;
          }
        }
      }
  };

struct Final_SSL_Socket final : SSL_Socket
  {
    Shared_cb_args m_cb;

    explicit
    Final_SSL_Socket(Shared_cb_args&& cb)
      : SSL_Socket(network_driver.default_client_ssl_ctx()), m_cb(::std::move(cb))  { }

    void
    do_push_event_common(Connection_Event type, linear_buffer&& data) const
      {
        auto queue = this->m_cb.wqueue.lock();
        if(!queue)
          return;

        // We are in the network thread here.
        plain_mutex::unique_lock lock(queue->mutex);

        if(!queue->fiber_active) {
          // Create a new fiber, if none is active. The fiber shall only reset
          // `m_fiber_private_buffer` if no event is pending.
          fiber_scheduler.launch(new_sh<Final_Fiber>(this->m_cb));
          queue->fiber_active = true;
        }

        auto& event = queue->events.emplace_back();
        event.type = type;
        event.data = ::std::move(data);
      }

    virtual
    void
    do_on_ssl_connected() override
      {
        linear_buffer data;
        this->do_push_event_common(connection_event_open, ::std::move(data));
      }

    virtual
    void
    do_on_ssl_stream(linear_buffer& data) override
      {
        this->do_push_event_common(connection_event_stream, ::std::move(data));
        data.clear();
      }

    virtual
    void
    do_abstract_socket_on_closed(int err) override
      {
        linear_buffer data;
        if(err != 0) {
          char msg_buf[256];
          const char* msg = ::strerror_r(err, msg_buf, sizeof(msg_buf));
          data.puts(msg);
        }
        this->do_push_event_common(connection_event_closed, ::std::move(data));
      }
  };

}  // namespace

struct Easy_SSL_Client::X_Event_Queue : Event_Queue
  {
  };

Easy_SSL_Client::
~Easy_SSL_Client()
  {
  }

void
Easy_SSL_Client::
start(const Socket_Address& addr)
  {
    auto queue = new_sh<X_Event_Queue>();
    Shared_cb_args cb = { this->m_cb_obj, this->m_cb_thunk, queue };
    auto socket = new_sh<Final_SSL_Socket>(::std::move(cb));
    socket->connect(addr);
    queue->wsocket = socket;

    network_driver.insert(socket);
    this->m_queue = ::std::move(queue);
    this->m_socket = ::std::move(socket);
  }

void
Easy_SSL_Client::
stop() noexcept
  {
    this->m_queue = nullptr;
    this->m_socket = nullptr;
  }

const Socket_Address&
Easy_SSL_Client::
local_address() const noexcept
  {
    if(!this->m_socket)
      return ipv6_invalid;

    return this->m_socket->local_address();
  }

const Socket_Address&
Easy_SSL_Client::
remote_address() const noexcept
  {
    if(!this->m_socket)
      return ipv6_invalid;

    return this->m_socket->remote_address();
  }

bool
Easy_SSL_Client::
ssl_send(const char* data, size_t size)
  {
    if(!this->m_socket)
      return false;

    return this->m_socket->ssl_send(data, size);
  }

bool
Easy_SSL_Client::
ssl_shut_down() noexcept
  {
    if(!this->m_socket)
      return false;

    return this->m_socket->ssl_shut_down();
  }

}  // namespace poseidon
