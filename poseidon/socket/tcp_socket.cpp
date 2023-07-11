// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../precompiled.ipp"
#include "tcp_socket.hpp"
#include "../utils.hpp"
#include <sys/socket.h>
namespace poseidon {

TCP_Socket::
TCP_Socket(unique_posix_fd&& fd)
  : Abstract_Socket(::std::move(fd))
  {
  }

TCP_Socket::
TCP_Socket()
  : Abstract_Socket(SOCK_STREAM, IPPROTO_TCP)
  {
  }

TCP_Socket::
~TCP_Socket()
  {
    POSEIDON_LOG_INFO(("Destroying `$1` (class `$2`)"), this, typeid(*this));
  }

void
TCP_Socket::
do_abstract_socket_on_closed()
  {
    POSEIDON_LOG_INFO((
        "TCP connection to `$3` closed: ${errno:full}",
        "[TCP socket `$1` (class `$2`)]"),
        this, typeid(*this), this->remote_address());
  }

void
TCP_Socket::
do_abstract_socket_on_readable()
  {
    recursive_mutex::unique_lock io_lock;
    auto& queue = this->do_abstract_socket_lock_read_queue(io_lock);
    ::ssize_t io_result = 0;

    for(;;) {
      // Read bytes and append them to `queue`.
      queue.reserve_after_end(0xFFFFU);
      io_result = ::recv(this->do_get_fd(), queue.mut_end(), queue.capacity_after_end(), 0);

      if(io_result < 0) {
        if((errno == EAGAIN) || (errno == EWOULDBLOCK))
          break;

        POSEIDON_LOG_ERROR((
            "Error reading TCP socket",
            "[`recv()` failed: ${errno:full}]",
            "[TCP socket `$1` (class `$2`)]"),
            this, typeid(*this));

        this->quick_close();
        return;
      }

      if(io_result == 0)
        break;

      // Accept incoming data.
      queue.accept((size_t) io_result);
    }

    // Process received data.
    this->do_on_tcp_stream(queue, io_result == 0);

    POSEIDON_LOG_TRACE((
        "TCP socket `$1` (class `$2`): `do_on_tcp_stream()` done"),
        this, typeid(*this));

    if(io_result == 0) {
      // If the end of stream has been reached, shut the connection down anyway.
      // Half-open connections are not supported.
      POSEIDON_LOG_INFO(("Closing TCP connection: remote = $1"), this->remote_address());
      ::shutdown(this->do_get_fd(), SHUT_RDWR);
    }
  }

void
TCP_Socket::
do_abstract_socket_on_oob_readable()
  {
    recursive_mutex::unique_lock io_lock;
    this->do_abstract_socket_lock_driver(io_lock);
    ::ssize_t io_result;

    // Try reading. When there is no OOB byte, `recv()` fails with `EINVAL`.
    char data;
    io_result = ::recv(this->do_get_fd(), &data, 1, MSG_OOB);
    if(io_result <= 0)
      return;

    // Process received byte.
    this->do_on_tcp_oob_byte(data);

    POSEIDON_LOG_TRACE((
        "TCP socket `$1` (class `$2`): `do_on_tcp_oob_byte()` done"),
        this, typeid(*this));
  }

void
TCP_Socket::
do_abstract_socket_on_writable()
  {
    recursive_mutex::unique_lock io_lock;
    auto& queue = this->do_abstract_socket_lock_write_queue(io_lock);
    ::ssize_t io_result = 0;

    for(;;) {
      // Write bytes from `queue` and remove those written.
      if(queue.size() == 0)
        break;

      io_result = ::send(this->do_get_fd(), queue.begin(), queue.size(), 0);

      if(io_result < 0) {
        if((errno == EAGAIN) || (errno == EWOULDBLOCK))
          break;

        POSEIDON_LOG_ERROR((
            "Error writing TCP socket",
            "[`send()` failed: ${errno:full}]",
            "[TCP socket `$1` (class `$2`)]"),
            this, typeid(*this));

        this->quick_close();
        return;
      }

      // Discard data that have been sent.
      queue.discard((size_t) io_result);
    }

    if(this->do_abstract_socket_change_state(socket_pending, socket_established)) {
      // Deliver the establishment notification.
      POSEIDON_LOG_DEBUG(("TCP connection established: remote = $1"), this->remote_address());
      this->do_on_tcp_connected();
    }

    if(queue.empty() && this->do_abstract_socket_change_state(socket_closing, socket_closed)) {
      // If the socket has been marked closing and there are no more data, perform
      // complete shutdown.
      ::shutdown(this->do_get_fd(), SHUT_RDWR);
    }
  }

void
TCP_Socket::
do_on_tcp_connected()
  {
    POSEIDON_LOG_INFO((
        "TCP connection to `$3` established",
        "[TCP socket `$1` (class `$2`)]"),
        this, typeid(*this), this->remote_address());
  }

void
TCP_Socket::
do_on_tcp_oob_byte(char data)
  {
    POSEIDON_LOG_INFO((
        "TCP connection received out-of-band data: $3 ($4)",
        "[TCP socket `$1` (class `$2`)]"),
        this, typeid(*this), (int) data, (char) data);
  }

const Socket_Address&
TCP_Socket::
remote_address() const noexcept
  {
    if(this->m_peername_ready.load())
      return this->m_peername;

    // Try getting the address now.
    static plain_mutex s_mutex;
    plain_mutex::unique_lock lock(s_mutex);

    if(this->m_peername_ready.load())
      return this->m_peername;

    ::sockaddr_in6 sa;
    ::socklen_t salen = sizeof(sa);
    if(::getpeername(this->do_get_fd(), (::sockaddr*) &sa, &salen) != 0)
      return ipv6_invalid;

    ROCKET_ASSERT(sa.sin6_family == AF_INET6);
    ROCKET_ASSERT(salen == sizeof(sa));

    if(sa.sin6_port == htobe16(0))
      return ipv6_unspecified;

    // Cache the address.
    this->m_peername.set_addr(sa.sin6_addr);
    this->m_peername.set_port(be16toh(sa.sin6_port));
    ::std::atomic_thread_fence(::std::memory_order_release);
    this->m_peername_ready.store(true);
    return this->m_peername;
  }

bool
TCP_Socket::
tcp_send(const char* data, size_t size)
  {
    if((data == nullptr) && (size != 0))
      POSEIDON_THROW((
          "Null data pointer",
          "[TCP socket `$1` (class `$2`)]"),
          this, typeid(*this));

    // If this socket has been marked closed, fail immediately.
    if(this->socket_state() >= socket_closing)
      return false;

    recursive_mutex::unique_lock io_lock;
    auto& queue = this->do_abstract_socket_lock_write_queue(io_lock);
    ::ssize_t io_result = 0;

    // Reserve backup space in case of partial writes.
    size_t nskip = 0;
    queue.reserve_after_end(size);

    if(queue.size() != 0) {
      // If there have been data pending, append new data to the end.
      ::memcpy(queue.mut_end(), data, size);
      queue.accept(size);
      return true;
    }

    for(;;) {
      // Try writing until the operation would block. This is essential for the
      // edge-triggered epoll to work reliably.
      if(nskip == size)
        break;

      io_result = ::send(this->do_get_fd(), data + nskip, size - nskip, 0);

      if(io_result < 0) {
        if((errno == EAGAIN) || (errno == EWOULDBLOCK))
          break;

        POSEIDON_LOG_ERROR((
            "Error writing TCP socket",
            "[`send()` failed: ${errno:full}]",
            "[TCP socket `$1` (class `$2`)]"),
            this, typeid(*this));

        this->quick_close();
        return false;
      }

      // Discard data that have been sent.
      nskip += (size_t) io_result;
    }

    // If the operation has completed only partially, buffer remaining data.
    // Space has already been reserved so this will not throw exceptions.
    ::memcpy(queue.mut_end(), data + nskip, size - nskip);
    queue.accept(size - nskip);
    return true;
  }

bool
TCP_Socket::
tcp_send_oob(char data) noexcept
  {
    return ::send(this->do_get_fd(), &data, 1, MSG_OOB) > 0;
  }

bool
TCP_Socket::
tcp_close() noexcept
  {
    recursive_mutex::unique_lock io_lock;
    auto& queue = this->do_abstract_socket_lock_write_queue(io_lock);

    // If there are data pending, mark this socket as being closed. If a full
    // connection has been established, wait until all pending data have been
    // sent. The connection should be closed thereafter.
    if(!queue.empty() && this->do_abstract_socket_change_state(socket_established, socket_closing))
      return true;

    // If there are no data pending, close it immediately.
    this->do_abstract_socket_set_state(socket_closed);
    return ::shutdown(this->do_get_fd(), SHUT_RDWR) == 0;
  }

}  // namespace poseidon
