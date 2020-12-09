// This file is part of Poseidon.
// Copyleft 2020, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_SOCKET_ABSTRACT_TLS_SOCKET_HPP_
#define POSEIDON_SOCKET_ABSTRACT_TLS_SOCKET_HPP_

#include "abstract_stream_socket.hpp"
#include "openssl.hpp"

namespace poseidon {

class Abstract_TLS_Socket
  : public ::asteria::Rcfwd<Abstract_TLS_Socket>,
    public Abstract_Stream_Socket
  {
  private:
    unique_SSL m_ssl;

  protected:
    // Adopts a foreign or accepted socket.
    explicit
    Abstract_TLS_Socket(unique_FD&& fd, ::SSL_CTX* ctx);

    // Creates a new non-blocking socket.
    explicit
    Abstract_TLS_Socket(::sa_family_t family, ::SSL_CTX* ctx);

  private:
    // Calls `::SSL_read()`.
    IO_Result
    do_stream_read_unlocked(char*& data, size_t size)
      final;

    // Calls `::SSL_write()`.
    IO_Result
    do_stream_write_unlocked(const char*& data, size_t size)
      final;

  protected:
    // Notifies a full-duplex channel has been established.
    // The default implementation prints a message but does nothing otherwise.
    // Please mind thread safety, as this function is called by the network thread.
    void
    do_socket_on_establish()
      override;

    // Consumes incoming data.
    // Please mind thread safety, as this function is called by the network thread.
    void
    do_socket_on_receive(linear_buffer&& rqueue)
      override
      = 0;

    // Notifies a full-duplex channel has been closed.
    // The default implementation prints a message but does nothing otherwise.
    // Please mind thread safety, as this function is called by the network thread.
    void
    do_socket_on_close(int err)
      override;

  public:
    ASTERIA_NONCOPYABLE_DESTRUCTOR(Abstract_TLS_Socket);

    using Abstract_Socket::get_fd;
    using Abstract_Socket::kill;
    using Abstract_Socket::get_local_address;

    using Abstract_Stream_Socket::get_remote_address;
    using Abstract_Stream_Socket::close;
  };

}  // namespace poseidon

#endif
