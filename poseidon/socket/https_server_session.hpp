// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_SOCKET_HTTPS_SERVER_SESSION_
#define POSEIDON_SOCKET_HTTPS_SERVER_SESSION_

#include "../fwd.hpp"
#include "ssl_socket.hpp"
namespace poseidon {

class HTTPS_Server_Session
  : public SSL_Socket
  {
  private:
    friend class Network_Driver;

  public:
    ASTERIA_NONCOPYABLE_VIRTUAL_DESTRUCTOR(HTTPS_Server_Session);

  };

}  // namespace poseidon
#endif
