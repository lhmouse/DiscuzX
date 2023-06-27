// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_HTTP_HTTP_REQUEST_HEADERS_
#define POSEIDON_HTTP_HTTP_REQUEST_HEADERS_

#include "../fwd.hpp"
#include "http_value.hpp"
namespace poseidon {

struct HTTP_Request_Headers
  {
    cow_string verb;
    cow_string uri;
    cow_bivector<cow_string, HTTP_Value> headers;

    // Define some helper functions.
    constexpr
    HTTP_Request_Headers() noexcept = default;

    HTTP_Request_Headers&
    swap(HTTP_Request_Headers& other) noexcept
      {
        this->verb.swap(other.verb);
        this->uri.swap(other.uri);
        this->headers.swap(other.headers);
        return *this;
      }

    bool
    header_name_equals(size_t index, cow_stringR cmp) const
      {
        const auto& my = this->headers.at(index).first;
        return ::rocket::ascii_ci_equal(my.data(), my.size(), cmp.data(), cmp.size());
      }

    // Writes request headers in raw format, which can be sent through a
    // stream socket. Lines are separated by CR LF pairs. Headers with empty
    // names are ignored silently.
    tinyfmt&
    print(tinyfmt& fmt) const;

    cow_string
    print_to_string() const;
  };

inline
void
swap(HTTP_Request_Headers& lhs, HTTP_Request_Headers& rhs) noexcept
  {
    lhs.swap(rhs);
  }

inline
tinyfmt&
operator<<(tinyfmt& fmt, const HTTP_Request_Headers& req)
  {
    return req.print(fmt);
  }

}  // namespace poseidon
#endif
