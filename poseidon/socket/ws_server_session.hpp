// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_SOCKET_WS_SERVER_SESSION_
#define POSEIDON_SOCKET_WS_SERVER_SESSION_

#include "../fwd.hpp"
#include "http_server_session.hpp"
#include "../http/websocket_frame_parser.hpp"
namespace poseidon {

class WS_Server_Session
  : public HTTP_Server_Session
  {
  private:
    WebSocket_Frame_Parser m_parser;
    shptr<WebSocket_Deflator> m_pmce_opt;
    linear_buffer m_msg;
    bool m_closure_notified = false;

  public:
    // Constructs a socket for incoming connections.
    explicit
    WS_Server_Session();

  private:
    void
    do_call_on_ws_close_once(uint16_t status, chars_proxy reason);

  protected:
    // This function implements `HTTP_Server_Session`.
    virtual
    void
    do_abstract_socket_on_closed() override;

    virtual
    void
    do_on_http_request_payload_stream(linear_buffer& data) override;

    virtual
    void
    do_on_http_request_finish(HTTP_Request_Headers&& req, linear_buffer&& data, bool close_now) override;

    virtual
    void
    do_on_http_request_error(uint32_t status) override;

    virtual
    void
    do_on_http_upgraded_stream(linear_buffer& data, bool eof) override;

    // This callback is invoked by the network thread when a WebSocket connection
    // has been accepted. The argument is the request URI of the client.
    // The default implementation does nothing.
    virtual
    void
    do_on_ws_accepted(cow_string&& uri);

    // These callbacks are invoked by the network thread for each fragment of a
    // data message. As with `TCP_Connection::do_on_tcp_stream()`, the argument
    // buffer contains all data that have been accumulated so far and callees are
    // supposed to remove bytes that have been processed. `fin` indicates whether
    // this is the last fragment of this message.
    // The default implementations leave all data alone for consumption by
    // `do_on_ws_text_data()`. For security reasons, the length of the message
    // payload is checked; an error is reported if it exceeds the
    // `network.http.max_websocket_message_length` limit in 'main.conf'.
    virtual
    void
    do_on_ws_text_stream(linear_buffer& data);

    virtual
    void
    do_on_ws_binary_stream(linear_buffer& data);

    // These callbacks are invoked by the network thread at the end of a data
    // message. Arguments have the same semantics with the other callbacks.
    virtual
    void
    do_on_ws_text(linear_buffer&& data) = 0;

    virtual
    void
    do_on_ws_binary(linear_buffer&& data) = 0;

    // This callback is invoked by the network thread after a PONG frame has
    // been received.
    // The default implementation does nothing.
    virtual
    void
    do_on_ws_pong(linear_buffer&& data);

    // This callback is invoked by the network thread when an error occurs, or
    // after a CLOSE frame has been received. The connection will be closed after
    // this function returns.
    // The default implementation does nothing.
    virtual
    void
    do_on_ws_close(uint16_t status, chars_proxy reason);

    // Sends a raw frame (not a message). No error checking is performed. This
    // function is provided for convenience only, and maybe isn't very useful
    // unless for some low-level hacks.
    bool
    do_ws_send_raw_frame(uint8_t opcode, chars_proxy data);

    // Sends a raw data frame (not a message). No error checking is performed.
    // The payload of a data frame may be (but is not always) compressed. This
    // function is provided for convenience only, and maybe isn't very useful
    // unless for some low-level hacks.
    bool
    do_ws_send_raw_data_frame(uint8_t opcode, chars_proxy data);

  public:
    ASTERIA_NONCOPYABLE_VIRTUAL_DESTRUCTOR(WS_Server_Session);

    // Sends a text message to the other peer.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    ws_send_text(chars_proxy data);

    // Sends a binary message to the other peer.
    // If this function throws an exception, there is no effect.
    // This function is thread-safe.
    bool
    ws_send_binary(chars_proxy data);

    // Sends a PING frame. The payload string will be truncated to 125 bytes if
    // it's too long.
    bool
    ws_ping(chars_proxy data);

    // Sends a CLOSE frame with an optional error message, then shuts down the
    // connection. The reason string will be truncated to 123 bytes if it's too
    // long.
    // This function is thread-safe.
    bool
    ws_shut_down(uint16_t status = 1000, chars_proxy reason = "") noexcept;
  };

}  // namespace poseidon
#endif
