// This file is part of Poseidon.
// Copyleft 2020, LH_Mouse. All wrongs reserved.

#include "../precompiled.hpp"
#include "abstract_accept_socket.hpp"
#include "../static/network_driver.hpp"
#include "../util.hpp"

namespace poseidon {
namespace {

IO_Result
do_translate_syscall_error(const char* func, int err)
  {
    if(err == EINTR)
      return io_result_partial_work;

    if(::rocket::is_any_of(err, { EAGAIN, EWOULDBLOCK }))
      return io_result_would_block;

    POSEIDON_THROW("Failed to accept incoming connection\n"
                   "[`$1()` failed: $2]",
                   func, format_errno(err));
  }

}  // namespace

Abstract_Accept_Socket::
~Abstract_Accept_Socket()
  {
  }

void
Abstract_Accept_Socket::
do_set_common_options()
  {
    // Enable reusing addresses.
    static constexpr int yes[] = { -1 };
    int res = ::setsockopt(this->get_fd(), SOL_SOCKET, SO_REUSEADDR, yes, sizeof(yes));
    ROCKET_ASSERT(res == 0);
  }

IO_Result
Abstract_Accept_Socket::
do_socket_on_poll_read(simple_mutex::unique_lock& /*lock*/, char* /*hint*/, size_t /*size*/)
  try {
    // Try accepting a socket.
    Socket_Address::storage addrst;
    ::socklen_t addrlen = sizeof(addrst);
    unique_FD fd(::accept4(this->get_fd(), addrst, &addrlen, SOCK_NONBLOCK));
    if(!fd)
      return do_translate_syscall_error("accept4", errno);

    // Create a new socket object.
    auto sock = this->do_socket_on_accept(::std::move(fd));
    if(!sock)
      POSEIDON_THROW("Null pointer returned from `do_socket_on_accept()`\n"
                     "[listen socket class `$1`]",
                     typeid(*this).name());

    // Register the socket.
    POSEIDON_LOG_INFO("Accepted incoming connection from '$1'\n"
                      "[server socket class `$2` listening on '$3']\n"
                      "[accepted socket class `$4`]",
                      Socket_Address(addrst, addrlen),
                      typeid(*this).name(), this->get_local_address(),
                      typeid(*sock).name());

    this->do_socket_on_register(Network_Driver::insert(::std::move(sock)));

    // Report success.
    return io_result_partial_work;
  }
  catch(exception& stdex) {
    // It is probably bad to let the exception propagate to network driver and kill
    // this server socket... so we catch and ignore this exception.
    POSEIDON_LOG_ERROR("Socket accept error: $1\n"
                       "[socket class `$2`]",
                       stdex, typeid(*this));

    // Accept other connections. The error is considered non-fatal.
    return io_result_partial_work;
  }

size_t
Abstract_Accept_Socket::
do_write_queue_size(simple_mutex::unique_lock& /*lock*/)
  const
  {
    return 0;
  }

IO_Result
Abstract_Accept_Socket::
do_socket_on_poll_write(simple_mutex::unique_lock& /*lock*/, char* /*hint*/, size_t /*size*/)
  {
    return io_result_end_of_stream;
  }

void
Abstract_Accept_Socket::
do_socket_on_poll_close(int err)
  {
    POSEIDON_LOG_INFO("Listen socket closed: local '$1', $2",
                      this->get_local_address(), format_errno(err));
  }

void
Abstract_Accept_Socket::
do_listen(const Socket_Address& addr, int backlog)
  {
    // Bind onto `addr`.
    if(::bind(this->get_fd(), addr.data(), addr.ssize()) != 0)
      POSEIDON_THROW("Failed to bind accept socket onto '$2'\n"
                     "[`bind()` failed: $1]",
                     format_errno(errno), addr);

    // Start listening.
    if(::listen(this->get_fd(), ::rocket::clamp(backlog, 1, SOMAXCONN)) != 0)
      POSEIDON_THROW("Failed to set up listen socket on '$2'\n"
                     "[`listen()` failed: $1]",
                     format_errno(errno), this->get_local_address());

    POSEIDON_LOG_INFO("Listen socket opened: local '$1'",
                      this->get_local_address());
  }

}  // namespace poseidon
