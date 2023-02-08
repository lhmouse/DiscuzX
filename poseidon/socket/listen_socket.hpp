// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_SOCKET_LISTEN_SOCKET_
#define POSEIDON_SOCKET_LISTEN_SOCKET_

#include "../fwd.hpp"
#include "abstract_socket.hpp"

namespace poseidon {

class Listen_Socket
  : public Abstract_Socket
  {
  private:
    friend class Network_Driver;

  protected:
    // Server-side constructor:
    // Creates a socket that is bound onto the given address.
    explicit
    Listen_Socket(const Socket_Address& addr);

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
    do_abstract_socket_on_oob_readable() override;

    virtual
    void
    do_abstract_socket_on_writable() override;

    // This callback is invoked by the network thread when a connection has been
    // received, and is intended to be overriden by derived classes. This function
    // should return a pointer to a socket object, constructed from the given FD.
    virtual
    shared_ptr<Abstract_Socket>
    do_on_listen_new_client_opt(unique_posix_fd&& fd) = 0;

  public:
    ASTERIA_NONCOPYABLE_VIRTUAL_DESTRUCTOR(Listen_Socket);

    // This class adds no public function.
  };

}  // namespace poseidon


#endif
