// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_SOCKET_SOCKET_ADDRESS_
#define POSEIDON_SOCKET_SOCKET_ADDRESS_

#include "../fwd.hpp"
#include <netinet/in.h>
namespace poseidon {

class Socket_Address
  {
  private:
    ::in6_addr m_addr;
    uint16_t m_port;

  public:
    // Initializes an unspecified (all-zero) address.
    constexpr
    Socket_Address() noexcept
      : m_addr(), m_port(0)  { }

    // Initializes an address from a foreign source.
    constexpr
    Socket_Address(const ::in6_addr& addr, uint16_t port = 0) noexcept
      : m_addr(addr), m_port(port)  { }

    constexpr
    Socket_Address(const Socket_Address& other, uint16_t port) noexcept
      : m_addr(other.m_addr), m_port(port)  { }

    // Parses an address from a string, like `parse()`.
    // An exception is thrown if the address string is not valid.
    explicit
    Socket_Address(const char* str, size_t len);

    explicit
    Socket_Address(const char* str);

    explicit
    Socket_Address(stringR str);

  public:
    // Accesses raw data.
    constexpr
    const ::in6_addr&
    addr() const noexcept
      { return this->m_addr;  }

    constexpr
    uint16_t
    port() const noexcept
      { return this->m_port;  }

    const uint8_t*
    data() const noexcept
      { return (const uint8_t*) &(this->m_addr);  }

    uint8_t*
    mut_data() noexcept
      { return (uint8_t*) &(this->m_addr);  }

    ::in6_addr&
    mut_addr() noexcept
      { return this->m_addr;  }

    Socket_Address&
    set_addr(const ::in6_addr& addr) noexcept
      {
        this->m_addr = addr;
        return *this;
      }

    Socket_Address&
    set_port(uint16_t port) noexcept
      {
        this->m_port = port;
        return *this;
      }

    Socket_Address&
    clear() noexcept
      {
        this->m_addr = ::in6_addr();
        this->m_port = 0;
        return *this;
      }

    Socket_Address&
    swap(Socket_Address& other) noexcept
      {
        ::std::swap(this->m_addr, other.m_addr);
        ::std::swap(this->m_port, other.m_port);
        return *this;
      }

    // Returns the address class, which is shared by both IPv4 and IPv6.
    ROCKET_PURE
    IP_Address_Class
    classify() const noexcept;

    // Parses an address from a string, which may be an IPv4 address, or
    // an IPv6 address in brackets, followed by a port number. Examples
    // are `127.0.0.1:80` and `[::1]:1300`. If `true` is returned and
    // `outlen_opt` is not null, the number of characters that have been
    // accepted will be stored into `*outlen_opt`.
    // If `false` is returned or an exception is thrown, the contents of
    // this object are unspecified.
    bool
    parse(const char* str, size_t len, size_t* outlen_opt = nullptr);

    bool
    parse(const char* str, size_t* outlen_opt = nullptr);

    bool
    parse(stringR str, size_t* outlen_opt = nullptr);

    // Converts this address to its string form. The caller should supply
    // a buffer for 48 characters, which is capable of storing the longest
    // string `[fedc:ba98:7654:3210:fedc:ba98:7654:3210]:65535`.
    size_t
    print_partial(char* str) const noexcept;

    tinyfmt&
    print(tinyfmt& fmt) const;

    cow_string
    print_to_string() const;
  };

extern const Socket_Address ipv6_unspecified;  // ::
extern const Socket_Address ipv6_loopback;     // ::1
extern const Socket_Address ipv6_invalid;      // 100::

extern const Socket_Address ipv4_unspecified;  // ::ffff::0.0.0.0
extern const Socket_Address ipv4_loopback;     // ::ffff::127.0.0.1
extern const Socket_Address ipv4_broadcast;    // ::ffff::255.255.255.255

inline
bool
operator==(const Socket_Address& lhs, const Socket_Address& rhs) noexcept
  {
    return (::memcmp(lhs.data(), rhs.data(), sizeof(::in6_addr)) == 0) && (lhs.port() == rhs.port());
  }

inline
bool
operator!=(const Socket_Address& lhs, const Socket_Address& rhs) noexcept
  {
    return (::memcmp(lhs.data(), rhs.data(), sizeof(::in6_addr)) != 0) || (lhs.port() != rhs.port());
  }

inline
void
swap(Socket_Address& lhs, Socket_Address& rhs) noexcept
  {
    lhs.swap(rhs);
  }

inline
tinyfmt&
operator<<(tinyfmt& fmt, const Socket_Address& addr)
  {
    return addr.print(fmt);
  }

}  // namespace poseidon
#endif
