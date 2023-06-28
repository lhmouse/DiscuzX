// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_EASY_EASY_HTTPS_SERVER_
#define POSEIDON_EASY_EASY_HTTPS_SERVER_

#include "../fwd.hpp"
#include "../socket/https_server_session.hpp"
namespace poseidon {

class Easy_HTTPS_Server
  {
  private:
    shptr<void> m_cb_obj;
    callback_thunk_ptr<shptrR<HTTPS_Server_Session>, Abstract_Fiber&,
        HTTP_Request_Headers&&, linear_buffer&&> m_cb_thunk;

    struct X_Client_Table;
    shptr<X_Client_Table> m_client_table;
    shptr<Listen_Socket> m_socket;

  public:
    // Constructs a server. The argument shall be an invocable object taking
    // `(shptrR<HTTPS_Server_Session> session, Abstract_Fiber& fiber,
    // HTTP_Request_Headers&& req, linear_buffer&& data)`, where `session` is
    // a pointer to a client session object, and `req` and `data` are the
    // headers and body of a request respectively. The server object owns all
    // client session objects. As a recommendation, applications should store
    // only `wkptr`s to client sessions, and call `.lock()` as needed. This
    // server object stores a copy of the callback object, which is invoked
    // accordingly in the main thread. The callback object is never copied,
    // and is allowed to modify itself.
    template<typename CallbackT,
    ROCKET_DISABLE_SELF(Easy_HTTPS_Server, CallbackT)>
    explicit
    Easy_HTTPS_Server(CallbackT&& cb)
      : m_cb_obj(new_sh<::std::decay_t<CallbackT>>(::std::forward<CallbackT>(cb))),
        m_cb_thunk(callback_thunk<::std::decay_t<CallbackT>>)
      { }

  public:
    ASTERIA_NONCOPYABLE_DESTRUCTOR(Easy_HTTPS_Server);

    // Starts listening the given address and port for incoming connections.
    void
    start(const Socket_Address& addr);

    // Shuts down the listening socket, if any. All existent clients are also
    // disconnected immediately.
    void
    stop() noexcept;

    // Gets the bound address of this server for incoming connections. In case
    // of errors, `ipv6_invalid` is returned.
    ROCKET_PURE
    const Socket_Address&
    local_address() const noexcept;
  };

}  // namespace poseidon
#endif
