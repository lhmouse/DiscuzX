// This file is part of Poseidon.
// Copyleft 2022, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_SOCKET_SSL_SOCKET_
#define POSEIDON_SOCKET_SSL_SOCKET_

#include "../fwd.hpp"
#include "abstract_socket.hpp"
#include "ssl_ptr.hpp"

namespace poseidon {

class SSL_Socket
  : public Abstract_Socket
  {
  private:
    SSL_ptr m_ssl;

    mutable once_flag m_peername_once;
    mutable Socket_Address m_peername;

  protected:
    // Server-side constructor:
    // Takes ownership of an accepted socket.
    explicit
    SSL_Socket(unique_posix_fd&& fd, const SSL_CTX_ptr& ssl_ctx);

    // Client-side constructor:
    // Creates a new non-blocking socket to the target host.
    explicit
    SSL_Socket(const Socket_Address& addr, const SSL_CTX_ptr& ssl_ctx);

  protected:
    // These callbacks implement `Abstract_Socket`.
    virtual
    void
    do_abstract_socket_on_closed(int err) override;

    virtual
    void
    do_abstract_socket_on_readable() override;

    virtual
    void
    do_abstract_socket_on_writable() override;

    virtual
    void
    do_abstract_socket_on_exception(exception& stdex) override;

    // This callback is invoked by the network thread when an outgoing (from
    // client) full-duplex connection has been established. It is not called for
    // incoming connections.
    // The default implemention merely prints a message.
    virtual
    void
    do_on_ssl_established();

    // This callback is invoked by the network thread when some bytes have been
    // received, and is intended to be overriden by derived classes.
    // The argument contains all data that have been accumulated so far. Callees
    // should remove processed bytes.
    virtual
    void
    do_on_ssl_stream(linear_buffer& data)
      = 0;

  public:
    ASTERIA_NONCOPYABLE_VIRTUAL_DESTRUCTOR(SSL_Socket);

    // Gets the SSL structure.
    ::SSL*
    ssl() const noexcept
      { return this->m_ssl.get();  }

    // Gets the remote or connected address of this socket.
    // This function is thread-safe.
    const Socket_Address&
    get_remote_address() const;

    // Enqueues some bytes for sending.
    // The return value merely indicates whether the attempt has succeeded. The
    // bytes may or may never arrive at the destination host.
    // This function is thread-safe.
    bool
    ssl_send(const char* data, size_t size);

    bool
    ssl_send(const linear_buffer& data);

    bool
    ssl_send(const cow_string& data);

    bool
    ssl_send(const string& data);

    // Shuts the socket down gracefully.
    // This function is thread-safe.
    bool
    ssl_shut_down() noexcept;
  };

}  // namespace poseidon

#endif