// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_HTTP_HTTP_RESPONSE_HEADERS_
#define POSEIDON_HTTP_HTTP_RESPONSE_HEADERS_

#include "../fwd.hpp"
#include "http_value.hpp"
namespace poseidon {

struct HTTP_Response_Headers
  {
    uint32_t status = 0;
    cow_string reason;
    cow_bivector<cow_string, HTTP_Value> headers;

    // Define some helper functions.
    constexpr
    HTTP_Response_Headers() noexcept = default;

    HTTP_Response_Headers&
    swap(HTTP_Response_Headers& other) noexcept
      {
        ::std::swap(this->status, other.status);
        this->reason.swap(other.reason);
        this->headers.swap(other.headers);
        return *this;
      }

    bool
    header_name_equals(size_t index, cow_stringR cmp) const
      {
        const auto& my = this->headers.at(index).first;
        return ::rocket::ascii_ci_equal(my.data(), my.size(), cmp.data(), cmp.size());
      }

    // Writes response headers in raw format, which can be sent through a
    // stream socket. Lines are separated by CR LF pairs. Headers with empty
    // names are ignored silently.
    tinyfmt&
    print(tinyfmt& fmt) const;

    cow_string
    print_to_string() const;
  };

inline
void
swap(HTTP_Response_Headers& lhs, HTTP_Response_Headers& rhs) noexcept
  {
    lhs.swap(rhs);
  }

inline
tinyfmt&
operator<<(tinyfmt& fmt, const HTTP_Response_Headers& resp)
  {
    return resp.print(fmt);
  }

}  // namespace poseidon
#endif
