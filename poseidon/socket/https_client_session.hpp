// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_SOCKET_HTTPS_CLIENT_SESSION_
#define POSEIDON_SOCKET_HTTPS_CLIENT_SESSION_

#include "../fwd.hpp"
#include "ssl_socket.hpp"
#include "../http/http_request_headers.hpp"
#include "../http/http_response_headers.hpp"
#include <http_parser.h>
namespace poseidon {

class HTTPS_Client_Session
  : public SSL_Socket
  {
  private:
    friend class Network_Driver;

    ::http_parser m_parser[1];
    HTTP_Response_Headers m_resp;
    linear_buffer m_body;

  public:
    // Constructs a socket for outgoing connections.
    explicit
    HTTPS_Client_Session(const SSL_CTX_ptr& ssl_ctx);

  private:
    inline
    void
    do_http_parser_on_message_begin();

    inline
    void
    do_http_parser_on_status(uint32_t status, const char* str, size_t len);

    inline
    void
    do_http_parser_on_header_field(const char* str, size_t len);

    inline
    void
    do_http_parser_on_header_value(const char* str, size_t len);

    inline
    HTTP_Message_Body_Type
    do_http_parser_on_headers_complete();

    inline
    void
    do_http_parser_on_body(const char* str, size_t len);

    inline
    void
    do_http_parser_on_message_complete();

  protected:
    // This function implements `SSL_Socket`.
    virtual
    void
    do_on_ssl_stream(linear_buffer& data, bool eof) override;

    // This callback is invoked by the network thread after all headers of a
    // response have been received, just before the body of it. Returning
    // `http_message_body_normal` indicates that the response has a body whose
    // length is described by the `Content-Length` or `Transfer-Encoding`
    // header. Returning `http_message_body_empty` indicates that the message
    // does not have a body even if it appears so, such as the response to a
    // HEAD request. Returning `http_message_body_upgrade` causes all further
    // data on this connection to be delivered via `do_on_http_body_stream()`.
    // This callback is primarily used to examine the status code before
    // processing response data.
    // The default implementation does not check for HEAD or upgrade responses
    // and returns `http_message_body_normal`.
    virtual
    HTTP_Message_Body_Type
    do_on_http_response_headers(HTTP_Response_Headers& resp);

    // This callback is invoked by the network thread for each fragment of the
    // response body that has been received. As with `SSL_Connection::
    // do_on_ssl_stream`, the argument buffer contains all data that have been
    // accumulated so far and callees are supposed to remove bytes that have
    // been processed.
    // The default implementation leaves all data alone for consumption by
    // `do_on_http_response_finish()`, but it checks the total length of the
    // body so it will not exceed `network.http.max_response_content_length`
    // in 'main.conf'.
    virtual
    void
    do_on_http_response_body_stream(linear_buffer& data);

    // This callback is invoked by the network thread at the end of a response
    // message. Arguments have the same semantics with the other callbacks.
    virtual
    void
    do_on_http_response_finish(HTTP_Response_Headers&& resp, linear_buffer&& data) = 0;

  public:
    ASTERIA_NONCOPYABLE_VIRTUAL_DESTRUCTOR(HTTPS_Client_Session);

    // Sends a simple request, possibly with a complete body. Callers should
    // not supply `Content-Length` or `Transfer-Encoding` headers, as they
    // will be rewritten.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    http_request(HTTP_Request_Headers&& resp, const char* data, size_t size);

    // Initiates a request with a chunked body. Callers should not supply
    // a `Transfer-Encoding` header, as it will be rewritten.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    http_chunked_request_start(HTTP_Request_Headers&& resp);

    // Sends a chunk of the request body. The HTTP/1.1 specification says that
    // a chunk of length zero terminates the chunked body. Chunked trailers
    // have not yet been supported. This function shall only be called when a
    // chunked request is active, otherwise it will corrupt the connection.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    http_chunked_request_data(const char* data, size_t size);
  };

}  // namespace poseidon
#endif
