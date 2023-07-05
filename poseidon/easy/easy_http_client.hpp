// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_EASY_EASY_HTTP_CLIENT_
#define POSEIDON_EASY_EASY_HTTP_CLIENT_

#include "../fwd.hpp"
#include "../socket/http_client_session.hpp"
namespace poseidon {

class Easy_HTTP_Client
  {
  public:
    // This is also the prototype of callbacks for the constructor.
    using thunk_type =
      Thunk<
        shptrR<HTTP_Client_Session>,  // client data socket
        Abstract_Fiber&,              // fiber for current callback
        HTTP_Response_Headers&&,      // response status code and headers
        linear_buffer&&>;             // response payload body

  private:
    thunk_type m_thunk;

    struct X_Event_Queue;
    shptr<X_Event_Queue> m_queue;
    shptr<HTTP_Client_Session> m_session;

  public:
    // Constructs a client. The argument shall be an invocable object taking
    // `(shptrR<HTTP_Client_Session> session, Abstract_Fiber& fiber,
    // HTTP_Response_Headers&& resp, linear_buffer&& data)`, where `session` is
    // a pointer to a client socket object, and `req` and `data` are the headers
    // and body of a response respectively. This client object stores a copy of
    // the callback object, which is invoked accordingly in the main thread.
    // The callback object is never copied, and is allowed to modify itself.
    template<typename CallbackT,
    ROCKET_ENABLE_IF(thunk_type::is_invocable<CallbackT>::value)>
    explicit
    Easy_HTTP_Client(CallbackT&& cb)
      : m_thunk(new_sh(::std::forward<CallbackT>(cb)))
      { }

    Easy_HTTP_Client(thunk_type::function_type* fptr)
      : m_thunk(fptr)
      { }

  public:
    ASTERIA_NONCOPYABLE_DESTRUCTOR(Easy_HTTP_Client);

    // Initiates a new connection to the given address.
    void
    open(const Socket_Address& addr);

    // Destroys the current connection without graceful shutdown. This function
    // should only be called after all data from the server have been read and
    // processed properly. Specifically, it is an error to close the connection
    // as soon as a request with `Connection: close` has been sent; the client
    // must always wait for the server to close the connection.
    void
    close() noexcept;

    // Gets the local address of this HTTP client for incoming data. In case of
    // errors, `ipv6_unspecified` is returned.
    ROCKET_PURE
    const Socket_Address&
    local_address() const noexcept;

    // Gets the remote or connected address of this client for outgoing data. In
    // case of errors, `ipv6_unspecified` is returned.
    ROCKET_PURE
    const Socket_Address&
    remote_address() const noexcept;

    // Sends a GET request. The request cannot contain a payload body. The
    // caller should neither set `req.method` nor supply `Content-Length` or
    // `Transfer-Encoding` headers in `req.headers`, because they will be
    // overwritten.
    // If this function returns `true`, the request will have been enqueued;
    // however it is not guaranteed to arrive at the server. If this function
    // returns `false`, the connection will have been closed.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    http_GET(HTTP_Request_Headers&& req);

    // Sends a POST request. The request must contain a payload body. The
    // caller should neither set `req.method` nor supply `Content-Length` or
    // `Transfer-Encoding` headers in `req.headers`, because they will be
    // overwritten.
    // If this function returns `true`, the request will have been enqueued;
    // however it is not guaranteed to arrive at the server. If this function
    // returns `false`, the connection will have been closed.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    http_POST(HTTP_Request_Headers&& req, const char* data, size_t size);

    // Sends a PUT request. The request must contain a payload body. The
    // caller should neither set `req.method` nor supply `Content-Length` or
    // `Transfer-Encoding` headers in `req.headers`, because they will be
    // overwritten.
    // If this function returns `true`, the request will have been enqueued;
    // however it is not guaranteed to arrive at the server. If this function
    // returns `false`, the connection will have been closed.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    http_PUT(HTTP_Request_Headers&& req, const char* data, size_t size);

    // Sends a DELETE request. The request cannot contain a payload body. The
    // caller should neither set `req.method` nor supply `Content-Length` or
    // `Transfer-Encoding` headers in `req.headers`, because they will be
    // overwritten.
    // If this function returns `true`, the request will have been enqueued;
    // however it is not guaranteed to arrive at the server. If this function
    // returns `false`, the connection will have been closed.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    http_DELETE(HTTP_Request_Headers&& req);
  };

}  // namespace poseidon
#endif