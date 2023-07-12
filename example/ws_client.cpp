// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../poseidon/precompiled.ipp"
#include "../poseidon/easy/easy_ws_client.hpp"
#include "../poseidon/easy/easy_timer.hpp"
#include "../poseidon/utils.hpp"
namespace {
using namespace ::poseidon;

extern Easy_WS_Client my_client;
extern Easy_Timer my_timer;

void
event_callback(shptrR<WS_Client_Session> session, Abstract_Fiber& /*fiber*/, WebSocket_Event event, linear_buffer&& data)
  {
    switch(event) {
      case websocket_open:
        POSEIDON_LOG_WARN(("example WS client connected to server: $1"), session->remote_address());
        break;

      case websocket_text:
        POSEIDON_LOG_WARN(("example WS client received TEXT data: $1"), data);
        break;

      case websocket_binary:
        POSEIDON_LOG_WARN(("example WS client received BINARY data: $1"), data);
        break;

      case websocket_pong:
        POSEIDON_LOG_WARN(("example WS client received PONG data: $1"), data);
        break;

      case websocket_closed:
        POSEIDON_LOG_WARN(("example WS client shut down connection: $1"), data);
        break;
    }
  }

void
timer_callback(shptrR<Abstract_Timer> /*timer*/, Abstract_Fiber& /*fiber*/, steady_time /*now*/)
  {
    static uint32_t state;
    switch(++ state) {
      case 1: {
        Socket_Address addr("127.0.0.1:3806");
        my_client.connect(addr);
        POSEIDON_LOG_INFO(("example WS client connecting: addr = $1"), addr);
        return;
      }

      case 2: {
        const char data[] = "some text data";
        my_client.ws_send_text(data);
        POSEIDON_LOG_INFO(("example WS client sent TEXT frame: $1"), data);
        return;
      }

      case 3: {
        const char data[] = "some binary data";
        my_client.ws_send_binary(data);
        POSEIDON_LOG_INFO(("example WS client sent BINARY frame: $1"), data);
        return;
      }

      case 4: {
        const char data[] = "some ping data";
        my_client.ws_ping(data);
        POSEIDON_LOG_INFO(("example WS client sent PING frame: $1"), data);
        return;
      }

      case 5: {
        // HACKS; DO NOT PLAY WITH THESE AT HOME.
        WebSocket_Frame_Header header;
        header.mask = 1;
        header.mask_key_u32 = 0x87654321;

        // fragment 1
        header.fin = 0;
        header.opcode = 1;
        char data1[] = "fragmented";
        header.payload_len = sizeof(data1) - 1;

        tinyfmt_ln fmt;
        header.encode(fmt);
        header.mask_payload(data1, sizeof(data1) - 1);
        fmt.putn(data1, sizeof(data1) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // nested PING
        header.fin = 1;
        header.opcode = 9;
        char ping1[] = "PING";
        header.payload_len = sizeof(ping1) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(ping1, sizeof(ping1) - 1);
        fmt.putn(ping1, sizeof(ping1) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // fragment 2
        header.fin = 0;
        header.opcode = 0;
        char data2[] = " text";
        header.payload_len = sizeof(data2) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(data2, sizeof(data2) - 1);
        fmt.putn(data2, sizeof(data2) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // fragment 3
        header.fin = 1;
        header.opcode = 0;
        char data3[] = " data";
        header.payload_len = sizeof(data3) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(data3, sizeof(data3) - 1);
        fmt.putn(data3, sizeof(data3) - 1);
        my_client.session_opt()->tcp_send(fmt);
        return;
      }

      case 6: {
        // HACKS; DO NOT PLAY WITH THESE AT HOME.
        WebSocket_Frame_Header header;
        header.mask = 1;
        header.mask_key_u32 = 0x87654321;

        // fragment 1
        header.fin = 0;
        header.opcode = 2;
        char data1[] = "fragmented";
        header.payload_len = sizeof(data1) - 1;

        tinyfmt_ln fmt;
        header.encode(fmt);
        header.mask_payload(data1, sizeof(data1) - 1);
        fmt.putn(data1, sizeof(data1) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // fragment 2
        header.fin = 0;
        header.opcode = 0;
        char data2[] = " binary";
        header.payload_len = sizeof(data2) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(data2, sizeof(data2) - 1);
        fmt.putn(data2, sizeof(data2) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // nested PING
        header.fin = 1;
        header.opcode = 9;
        char ping1[] = "PING";
        header.payload_len = sizeof(ping1) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(ping1, sizeof(ping1) - 1);
        fmt.putn(ping1, sizeof(ping1) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // fragment 3
        header.fin = 1;
        header.opcode = 0;
        char data3[] = " data";
        header.payload_len = sizeof(data3) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(data3, sizeof(data3) - 1);
        fmt.putn(data3, sizeof(data3) - 1);
        my_client.session_opt()->tcp_send(fmt);
        return;
      }

      case 7: {
        // HACKS; DO NOT PLAY WITH THESE AT HOME.
        WebSocket_Frame_Header header;
        header.mask = 1;
        header.mask_key_u32 = 0x87654321;

        // fragment 1
        header.fin = 0;
        header.opcode = 1;
        char data1[] = "should never";
        header.payload_len = sizeof(data1) - 1;

        tinyfmt_ln fmt;
        header.encode(fmt);
        header.mask_payload(data1, sizeof(data1) - 1);
        fmt.putn(data1, sizeof(data1) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // nested CLOSE
        header.fin = 1;
        header.opcode = 8;
        char reason1[] = "\x03\xE8""CLOSE";
        header.payload_len = sizeof(reason1) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(reason1, sizeof(reason1) - 1);
        fmt.putn(reason1, sizeof(reason1) - 1);
        my_client.session_opt()->tcp_send(fmt);

        // fragment 2
        header.fin = 1;
        header.opcode = 0;
        char data2[] = " see this";
        header.payload_len = sizeof(data2) - 1;

        fmt.clear_buffer();
        header.encode(fmt);
        header.mask_payload(data2, sizeof(data2) - 1);
        fmt.putn(data2, sizeof(data2) - 1);
        my_client.session_opt()->tcp_send(fmt);
        return;
      }

      default:
        POSEIDON_LOG_INFO(("example WS client shutting down"));
        my_client.ws_close(3456, "bye");
        state = 0;
    }
  }

int
start_timer()
  {
    my_timer.start((seconds) 1, (seconds) 2);
    return 0;
  }

// Start the client when this shared library is being loaded.
Easy_WS_Client my_client(event_callback);
Easy_Timer my_timer(timer_callback);
int dummy = start_timer();

}  // namespace
