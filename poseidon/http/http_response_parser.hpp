// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_HTTP_HTTP_RESPONSE_PARSER_
#define POSEIDON_HTTP_HTTP_RESPONSE_PARSER_

#include "../fwd.hpp"
#include "http_response_headers.hpp"
#include <http_parser.h>
namespace poseidon {

class HTTP_Response_Parser
  {
  private:
    static const ::http_parser_settings s_settings[1];

    enum HRESP_State : uint8_t
      {
        hresp_new          = 0,
        hresp_header_done  = 1,
        hresp_body_done    = 2,
      };

    ::http_parser m_parser[1];
    HTTP_Response_Headers m_headers;
    linear_buffer m_body;

    HRESP_State m_hresp;
    bool m_close_after_body;
    char m_reserved_1;
    char m_reserved_2;

  public:
    // Constructs a parser for incoming responses.
    HTTP_Response_Parser() noexcept
      {
        this->clear();
      }

  public:
    ASTERIA_NONCOPYABLE_DESTRUCTOR(HTTP_Response_Parser);

    // Has an error occurred?
    bool
    error() const noexcept
      { return HTTP_PARSER_ERRNO(this->m_parser) != HPE_OK;  }

    // Clears all fields. This function shall not be called unless the parser is
    // to be reused for another stream.
    void
    clear() noexcept
      {
        ::http_parser_init(this->m_parser, HTTP_RESPONSE);
        this->m_parser->allow_chunked_length = true;
        this->m_parser->data = this;

        this->m_headers.clear();
        this->m_body.clear();

        this->m_hresp = hresp_new;
        this->m_close_after_body = false;
        this->m_reserved_1 = 0;
        this->m_reserved_2 = 0;
      }

    // Parses the response line and headers of an HTTP response from a stream.
    // `data` may be consumed partially, and must be preserved between calls. If
    // `headers_complete()` returns `true` before the call, this function does
    // nothing.
    void
    parse_headers_from_stream(linear_buffer& data, bool eof);

    // Informs the HTTP parser that the current message has no body, e.g. the
    // response to a HEAD or CONNECT request. This cannot be undone.
    void
    set_no_body() noexcept
      {
        ROCKET_ASSERT(this->m_hresp >= hresp_header_done);
        this->m_parser->flags |= F_SKIPBODY;
      }

    // Get the parsed headers.
    bool
    should_close_after_body() const noexcept
      { return this->m_close_after_body;  }

    bool
    headers_complete() const noexcept
      { return this->m_hresp >= hresp_header_done;  }

    const HTTP_Response_Headers&
    headers() const noexcept
      { return this->m_headers;  }

    HTTP_Response_Headers&
    mut_headers() noexcept
      { return this->m_headers;  }

    // Parses the body of an HTTP response from a stream. `data` may be consumed
    // partially, and must be preserved between calls. If `body_complete()`
    // returns `true` before the call, this function does nothing.
    void
    parse_body_from_stream(linear_buffer& data, bool eof);

    // Get the parsed body.
    bool
    body_complete() const noexcept
      { return this->m_hresp >= hresp_body_done;  }

    const linear_buffer&
    body() const noexcept
      { return this->m_body;  }

    linear_buffer&
    mut_body() noexcept
      { return this->m_body;  }

    // Clears the current complete message, so the parser can start the next one.
    void
    next_message() noexcept
      {
        ROCKET_ASSERT(this->m_hresp >= hresp_body_done);

        this->m_headers.clear();
        this->m_body.clear();
        this->m_hresp = hresp_new;
        this->m_close_after_body = false;
      }
  };

}  // namespace poseidon
#endif
