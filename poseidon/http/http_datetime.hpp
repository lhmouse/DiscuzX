// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_HTTP_HTTP_DATETIME_
#define POSEIDON_HTTP_HTTP_DATETIME_

#include "../fwd.hpp"
namespace poseidon {

class HTTP_DateTime
  {
  private:
    unix_time m_tp;

  public:
    // Initializes a timestamp of `1970-01-01 00:00:00 Z`.
    constexpr
    HTTP_DateTime() noexcept
      : m_tp()  { }

    // Initializes a timestamp from a foreign source.
    constexpr
    HTTP_DateTime(unix_time tp) noexcept
      : m_tp(tp)  { }

    constexpr
    HTTP_DateTime(seconds s) noexcept
      : m_tp(s)  { }

    // Parses a timestamp from an HTTP date/time string, like `parse()`.
    // An exception is thrown if the date/time string is not valid.
    explicit
    HTTP_DateTime(const char* str, size_t len);

    explicit
    HTTP_DateTime(const char* str);

    explicit
    HTTP_DateTime(stringR str);

  public:
    // Accesses raw data.
    constexpr
    unix_time
    as_time_point() const noexcept
      { return this->m_tp;  }

    constexpr
    seconds
    as_seconds() const noexcept
      { return this->m_tp.time_since_epoch();  }

    HTTP_DateTime&
    set_time_point(unix_time tp) noexcept
      {
        this->m_tp = tp;
        return *this;
      }

    HTTP_DateTime&
    set_seconds(seconds s) noexcept
      {
        this->m_tp = (unix_time) s;
        return *this;
      }

    HTTP_DateTime&
    swap(HTTP_DateTime& other) noexcept
      {
        ::std::swap(this->m_tp, other.m_tp);
        return *this;
      }

    // Try parsing an HTTP date/time in the formal RFC 1123 format. An example
    // is `Sun, 06 Nov 1994 08:49:37 GMT`. This function returns the number of
    // characters that have been accepted, which is 29 upon success, and 0 upon
    // failure.
    size_t
    parse_rfc1123_partial(const char* str) noexcept;

    // Converts this timestamp to its RFC 1123 format, with a null terminator.
    // There shall be at least 30 characters in the buffer which `str` points
    // to. This function returns the number of characters that have been
    // written, not including the null terminator, which is always 29.
    size_t
    print_rfc1123_partial(char* str) const noexcept;

    // Try parsing an HTTP date/time in the obsolete RFC 850 format. An example
    // is `Sunday, 06-Nov-94 08:49:37 GMT`. This function returns the number of
    // characters that have been accepted, which is within [30,33] upon success,
    // and 0 upon failure.
    size_t
    parse_rfc850_partial(const char* str) noexcept;

    // Converts this timestamp to its RFC 1123 format, with a null terminator.
    // There shall be at least 34 characters in the buffer which `str` points
    // to. This function returns the number of characters that have been
    // written, not including the null terminator, which is within [30,33].
    size_t
    print_rfc850_partial(char* str) const noexcept;

    // Try parsing an HTTP date/time in the obsolete asctime format. An example
    // is `Sun Nov  6 08:49:37 1994`. This function returns the number of
    // characters that have been accepted, which is 24 upon success, and 0 upon
    // failure.
    size_t
    parse_asctime_partial(const char* str) noexcept;

    // Converts this timestamp to its asctime format, with a null terminator.
    // There shall be at least 25 characters in the buffer which `str` points
    // to. This function returns the number of characters that have been
    // written, not including the null terminator, which is always 24.
    size_t
    print_asctime_partial(char* str) const noexcept;

    // Try parsing an HTTP date/time in any of the formats above. If `true` is
    // returned and `outlen_opt` is not null, the number of characters that
    // have been accepted will be stored into `*outlen_opt`.
    // If `false` is returned or an exception is thrown, the contents of this
    // object are unspecified.
    bool
    parse(const char* str, size_t len, size_t* outlen_opt = nullptr);

    bool
    parse(const char* str, size_t* outlen_opt = nullptr);

    bool
    parse(stringR str, size_t* outlen_opt = nullptr);

    // Converts this timestamp to its string form, according to RFC 1123.
    tinyfmt&
    print(tinyfmt& fmt) const;

    cow_string
    print_to_string() const;
  };

extern const HTTP_DateTime http_datetime_min;  // `Thu, 01 Jan 1970 00:00:00 GMT`; UNIX time `0`
extern const HTTP_DateTime http_datetime_max;  // `Fri, 01 Jan 9999 00:00:00 GMT`; UNIX time `253370764800`

constexpr
bool
operator==(const HTTP_DateTime& lhs, const HTTP_DateTime& rhs) noexcept
  {
    return lhs.as_time_point() == rhs.as_time_point();
  }

constexpr
bool
operator!=(const HTTP_DateTime& lhs, const HTTP_DateTime& rhs) noexcept
  {
    return lhs.as_time_point() != rhs.as_time_point();
  }

constexpr
bool
operator<(const HTTP_DateTime& lhs, const HTTP_DateTime& rhs) noexcept
  {
    return lhs.as_time_point() < rhs.as_time_point();
  }

constexpr
bool
operator>(const HTTP_DateTime& lhs, const HTTP_DateTime& rhs) noexcept
  {
    return lhs.as_time_point() > rhs.as_time_point();
  }

constexpr
bool
operator<=(const HTTP_DateTime& lhs, const HTTP_DateTime& rhs) noexcept
  {
    return lhs.as_time_point() <= rhs.as_time_point();
  }

constexpr
bool
operator>=(const HTTP_DateTime& lhs, const HTTP_DateTime& rhs) noexcept
  {
    return lhs.as_time_point() >= rhs.as_time_point();
  }

inline
void
swap(HTTP_DateTime& lhs, HTTP_DateTime& rhs) noexcept
  {
    lhs.swap(rhs);
  }

inline
tinyfmt&
operator<<(tinyfmt& fmt, const HTTP_DateTime& ts)
  {
    return ts.print(fmt);
  }

}  // namespace poseidon
#endif
