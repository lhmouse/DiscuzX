// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../precompiled.ipp"
#include "https_server_session.hpp"
#include "../base/config_file.hpp"
#include "../static/main_config.hpp"
#include "../utils.hpp"
namespace poseidon {

HTTPS_Server_Session::
HTTPS_Server_Session(unique_posix_fd&& fd, const SSL_CTX_ptr& ssl_ctx)
  : SSL_Socket(::std::move(fd), ssl_ctx)  // server constructor
  {
    ::http_parser_init(this->m_parser, HTTP_REQUEST);
    this->m_parser->data = this;
  }

HTTPS_Server_Session::
~HTTPS_Server_Session()
  {
  }

void
HTTPS_Server_Session::
do_on_ssl_stream(linear_buffer& data, bool eof)
  {
    if(this->m_upgrade_ack.load()) {
      // Deallocate the body buffer, as it is no longer useful.
      linear_buffer().swap(this->m_body);
      this->do_on_https_upgraded_stream(data, eof);
      return;
    }

    if(HTTP_PARSER_ERRNO(this->m_parser) != HPE_OK) {
      // This can't be recovered. All further data will be discarded. There is
      // not much we can do.
      POSEIDON_LOG_WARN((
          "HTTP parser error `$3`: $4",
          "[HTTPS server session `$1` (class `$2`)]"),
          this, typeid(*this), HTTP_PARSER_ERRNO(this->m_parser),
          ::http_errno_description(HTTP_PARSER_ERRNO(this->m_parser)));

      data.clear();
      return;
    }

    // Parse incoming data and remove parsed bytes from the queue. Errors are
    // passed via exceptions.
#define this   static_cast<HTTPS_Server_Session*>(ps->data)
    static constexpr ::http_parser_settings settings[1] =
      {{
        // on_message_begin
        +[](::http_parser* ps)
          {
            this->m_req.clear();
            this->m_body.clear();
            return 0;
          },

        // on_url
        +[](::http_parser* ps, const char* str, size_t len)
          {
            this->m_req.method = sref(::http_method_str((::http_method) ps->method));
            this->m_req.uri.append(str, len);
            return 0;
          },

        // on_status
        nullptr,

        // on_header_field
        +[](::http_parser* ps, const char* str, size_t len)
          {
            if(this->m_req.headers.empty())
              this->m_req.headers.emplace_back();

            if(this->m_body.size() != 0) {
              // If `m_body` is not empty, a previous header is now complete,
              // so commit it.
              const char* value_str = this->m_body.data() + 1;
              ROCKET_ASSERT(value_str[-1] == '\n');  // magic character
              size_t value_len = this->m_body.size() - 1;

              auto& value = this->m_req.headers.mut_back().second;
              if(value.parse(value_str, value_len) != value_len)
                value.set_string(cow_string(value_str, value_len));

              // Create the next header.
              this->m_req.headers.emplace_back();
              this->m_body.clear();
            }

            // Append the header name to the last key, as this callback might
            // be invoked repeatedly.
            this->m_req.headers.mut_back().first.append(str, len);
            return 0;
          },

        // on_header_value
        +[](::http_parser* ps, const char* str, size_t len)
          {
            // Add a magic character to indicate that a part of the value has
            // been received. This makes `m_body` non-empty.
            if(this->m_body.empty())
               this->m_body.putc('\n');

            // Abuse `m_body` to store the header value.
            this->m_body.putn(str, len);
            return 0;
          },

        // on_headers_complete
        +[](::http_parser* ps)
          {
            if(this->m_body.size() != 0) {
              // If `m_body` is not empty, a previous header is now complete,
              // so commit it.
              const char* value_str = this->m_body.data() + 1;
              ROCKET_ASSERT(value_str[-1] == '\n');  // magic character
              size_t value_len = this->m_body.size() - 1;

              auto& value = this->m_req.headers.mut_back().second;
              if(value.parse(value_str, value_len) != value_len)
                value.set_string(cow_string(value_str, value_len));

              // Finish abuse of `m_body`.
              this->m_body.clear();
            }

            // The headers are complete, so determine whether a request body
            // should be expected.
            auto body_type = this->do_on_https_request_headers(this->m_req);
            switch(body_type) {
              case http_message_body_normal:
                return 0;

              case http_message_body_empty:
                return 1;

              case http_message_body_connect:
                return 2;

              default:
                POSEIDON_THROW((
                    "Invalid body type `$3` returned from `do_http_parser_on_headers_complete()`",
                    "[HTTPS server session `$1` (class `$2`)]"),
                    this, typeid(*this), body_type);
            }
          },

        // on_body
        +[](::http_parser* ps, const char* str, size_t len)
          {
            if(is_none_of(ps->method, { HTTP_GET, HTTP_HEAD, HTTP_DELETE, HTTP_CONNECT })) {
              // Only these methods have bodies with defined semantics.
              this->m_body.putn(str, len);
              this->do_on_https_request_body_stream(this->m_body);
            }
            return 0;
          },

        // on_message_complete
        +[](::http_parser* ps)
          {
            bool close_now = ::http_should_keep_alive(ps);
            this->do_on_https_request_finish(::std::move(this->m_req), ::std::move(this->m_body), close_now);

            if(this->m_upgrade_ack.load()) {
              // If the user has switched to another protocol, all further data
              // will not be HTTP, so halt.
              ps->http_errno = HPE_PAUSED;
            }
            return 0;
          },

        // on_chunk_header
        nullptr,

        // on_chunk_complete
        nullptr,
      }};
#undef this

    if(data.size() != 0)
      data.discard(::http_parser_execute(this->m_parser, settings, data.data(), data.size()));

    if(eof)
      ::http_parser_execute(this->m_parser, settings, "", 0);

    // Assuming the error code won't be clobbered by the second call for EOF
    // above. Not sure.
    switch((uint32_t) HTTP_PARSER_ERRNO(this->m_parser)) {
      case HPE_OK:
        break;

      case HPE_HEADER_OVERFLOW:
        this->do_on_https_request_error(HTTP_STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE);
        break;

      case HPE_INVALID_VERSION:
        this->do_on_https_request_error(HTTP_STATUS_HTTP_VERSION_NOT_SUPPORTED);
        break;

      case HPE_INVALID_METHOD:
        this->do_on_https_request_error(HTTP_STATUS_METHOD_NOT_ALLOWED);
        break;

      case HPE_INVALID_TRANSFER_ENCODING:
        this->do_on_https_request_error(HTTP_STATUS_LENGTH_REQUIRED);
        break;

      default:
        this->do_on_https_request_error(HTTP_STATUS_BAD_REQUEST);
        break;
    }

    POSEIDON_LOG_TRACE(("HTTP parser done: data.size `$1`, eof `$2`"), data.size(), eof);
  }

char256
HTTPS_Server_Session::
do_on_ssl_alpn_request(cow_vector<char256>&& protos)
  {
    // Select HTTP/1.1.
    for(const auto& proto : protos)
      if(proto == "http/1.1")
        return proto;

    return "";
  }

HTTP_Message_Body_Type
HTTPS_Server_Session::
do_on_https_request_headers(HTTP_Request_Headers& req)
  {
    if((req.method == sref("CONNECT")) || !req.uri.starts_with(sref("/"))) {
      // Reject proxy requests.
      this->do_on_https_request_error(HTTP_STATUS_NOT_IMPLEMENTED);
      return http_message_body_normal;
    }

    POSEIDON_LOG_INFO((
        "HTTPS server received request: $3 $4",
        "[HTTPS server session `$1` (class `$2`)]"),
        this, typeid(*this), req.method, req.uri);

    // The default handler doesn't handle Upgrade requests.
    return http_message_body_normal;
  }

void
HTTPS_Server_Session::
do_on_https_request_body_stream(linear_buffer& data)
  {
    // Leave `data` alone for consumption by `do_on_https_request_finish()`,
    // but perform some safety checks, so we won't be affected by compromized
    // 3rd-party servers.
    const auto conf_file = main_config.copy();
    int64_t max_request_content_length = 1048576;

    auto value = conf_file.query("network", "http", "max_request_content_length");
    if(value.is_integer())
      max_request_content_length = value.as_integer();
    else if(!value.is_null())
      POSEIDON_LOG_WARN((
          "Ignoring `network.http.max_request_content_length`: expecting an `integer`, got `$1`",
          "[in configuration file '$2']"),
          value, conf_file.path());

    if(max_request_content_length < 0)
      POSEIDON_THROW((
          "`network.http.max_request_content_length` value `$1` out of range",
          "[in configuration file '$2']"),
          max_request_content_length, conf_file.path());

    if(data.size() > (uint64_t) max_request_content_length)
      POSEIDON_THROW((
          "HTTP request body too large: `$3` > `$4`",
          "[HTTPS server session `$1` (class `$2`)]"),
          this, typeid(*this), data.size(), max_request_content_length);
  }

__attribute__((__noreturn__))
void
HTTPS_Server_Session::
do_on_https_upgraded_stream(linear_buffer& data, bool eof)
  {
    POSEIDON_THROW((
        "`do_on_http_upgraded_stream()` not implemented: data.size `$3`, eof `$4`",
        "[HTTPS server session `$1` (class `$2`)]"),
        this, typeid(*this), data.size(), eof);
  }

bool
HTTPS_Server_Session::
do_https_raw_response(const HTTP_Response_Headers& resp, const char* data, size_t size)
  {
    // Compose the message and send it as a whole.
    tinyfmt_str fmt;
    fmt.reserve(1023 + size);
    resp.encode(fmt);
    fmt.putn(data, size);
    bool sent = this->ssl_send(fmt.data(), fmt.size());

    if(resp.status == HTTP_STATUS_SWITCHING_PROTOCOLS) {
      // For server sessions, this indicates that the server has switched to
      // another protocol. The client might have sent more data before this,
      // which would violate RFC 6455 anyway, so we don't care.
      this->m_upgrade_ack.store(true);
    }

    // The return value indicates whether no error has occurred. There is no
    // guarantee that data will eventually arrive, due to network flapping.
    return sent;
  }

bool
HTTPS_Server_Session::
https_response_headers_only(HTTP_Response_Headers&& resp)
  {
    if(this->m_upgrade_ack.load())
      POSEIDON_THROW((
          "HTTPS connection switched to another protocol",
          "[HTTPS server session `$1` (class `$2`)]"),
          this, typeid(*this));

    return this->do_https_raw_response(resp, "", 0);
  }

bool
HTTPS_Server_Session::
https_response(HTTP_Response_Headers&& resp, const char* data, size_t size)
  {
    if(this->m_upgrade_ack.load())
      POSEIDON_THROW((
          "HTTPS connection switched to another protocol",
          "[HTTPS server session `$1` (class `$2`)]"),
          this, typeid(*this));

    // Erase bad headers.
    for(size_t hindex = 0;  hindex < resp.headers.size();  hindex ++)
      if(ascii_ci_equal(resp.headers.at(hindex).first, sref("Content-Length"))
         || ascii_ci_equal(resp.headers.at(hindex).first, sref("Transfer-Encoding")))
        resp.headers.erase(hindex --);

    // Some responses are required to have no payload body and require no
    // `Content-Length` header.
    if((resp.status <= 199) || is_any_of(resp.status, { HTTP_STATUS_NO_CONTENT, HTTP_STATUS_NOT_MODIFIED }))
      return this->do_https_raw_response(resp, "", 0);

    // Otherwise, a `Content-Length` is required; otherwise the response would
    // be interpreted as terminating by closure ofthe connection.
    resp.headers.emplace_back(sref("Content-Length"), (int64_t) size);

    return this->do_https_raw_response(resp, data, size);
  }

bool
HTTPS_Server_Session::
https_chunked_response_start(HTTP_Response_Headers&& resp)
  {
    if(this->m_upgrade_ack.load())
      POSEIDON_THROW((
          "HTTPS connection switched to another protocol",
          "[HTTPS server session `$1` (class `$2`)]"),
          this, typeid(*this));

    // Erase bad headers.
    for(size_t hindex = 0;  hindex < resp.headers.size();  hindex ++)
      if(ascii_ci_equal(resp.headers.at(hindex).first, sref("Transfer-Encoding")))
        resp.headers.erase(hindex --);

    // Write a chunked header.
    resp.headers.emplace_back(sref("Transfer-Encoding"), sref("chunked"));

    return this->do_https_raw_response(resp, "", 0);
  }

bool
HTTPS_Server_Session::
https_chunked_response_send(const char* data, size_t size)
  {
    if(this->m_upgrade_ack.load())
      POSEIDON_THROW((
          "HTTPS connection switched to another protocol",
          "[HTTPS server session `$1` (class `$2`)]"),
          this, typeid(*this));

    // Ignore empty chunks, which would mark the end of the body.
    if(size == 0)
      return this->socket_state() <= socket_state_established;

    // Compose a chunk and send it as a whole. The length of this chunk is
    // written as a hexadecimal integer without the `0x` prefix.
    tinyfmt_str fmt;
    fmt.reserve(1023);
    ::rocket::ascii_numput nump;
    nump.put_XU(size);
    fmt.putn(nump.data() + 2, nump.size() - 2);
    fmt << "\r\n";
    fmt.putn(data, size);
    fmt << "\r\n";
    return this->ssl_send(fmt.data(), fmt.size());
  }

bool
HTTPS_Server_Session::
https_chunked_respnse_finish()
  {
    if(this->m_upgrade_ack.load())
      POSEIDON_THROW((
          "HTTPS connection switched to another protocol",
          "[HTTPS server session `$1` (class `$2`)]"),
          this, typeid(*this));

    return this->ssl_send("0\r\n\r\n", 5);
  }

}  // namespace poseidon
