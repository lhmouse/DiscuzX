// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_BASE_CHARBUF_256_
#define POSEIDON_BASE_CHARBUF_256_

#include "../fwd.hpp"
namespace poseidon {

class charbuf_256
  {
  private:
    char m_data[256];

  public:
    // Constructs a null-terminated string of zero characters.
    // This constructor is not explicit as it doesn't allocate memory.
    inline
    charbuf_256() noexcept
      {
        this->m_data[0] = 0;
      }

    // Constructs a null-terminated string.
    // This constructor is not explicit as it doesn't allocate memory.
    inline
    charbuf_256(const char* str_opt)
      {
        const char* str = str_opt;
        if(!str)
          str = "";

        size_t len = ::strlen(str);
        if(len >= 256)
          ::rocket::sprintf_and_throw<::std::length_error>(
              "charbuf_256: string `%s` (length `%lld`) too long",
              str, (long long) len);

        ::memcpy(this->m_data, str, len + 1);
      }

    // Swaps two buffers.
    charbuf_256&
    swap(charbuf_256& other) noexcept
      {
        for(size_t k = 0;  k != 16;  ++k) {
          alignas(16) char temp[16];
          ::memcpy(temp, other.m_data + k * 16, sizeof(temp));
          ::memcpy(other.m_data + k * 16, this->m_data + k * 16, sizeof(temp));
          ::memcpy(this->m_data + k * 16, temp, sizeof(temp));
        }
        return *this;
      }

  public:
    // Performs 3-way comparison of two buffers.
    int
    compare(const charbuf_256& other) const noexcept
      { return ::strcmp(this->m_data, other.m_data);  }

    int
    compare(const char* other) const noexcept
      { return ::strcmp(this->m_data, other);  }

    // Returns a pointer to internal storage so a buffer can be passed as
    // an argument for `char*`.
    constexpr operator
    const char*() const noexcept
      { return this->m_data;  }

    operator
    char*() noexcept
      { return this->m_data;  }
  };

inline
void
swap(charbuf_256& lhs, charbuf_256& rhs) noexcept
  {
    lhs.swap(rhs);
  }

inline
tinyfmt&
operator<<(tinyfmt& fmt, const charbuf_256& cbuf)
  {
    return fmt << (const char*) cbuf;
  }

inline
bool
operator==(const charbuf_256& lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) == 0;
  }

inline
bool
operator==(const char* lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) == 0;
  }

inline
bool
operator==(const charbuf_256& lhs, const char* rhs) noexcept
  {
    return ::strcmp(lhs, rhs) == 0;
  }

inline
bool
operator!=(const charbuf_256& lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) != 0;
  }

inline
bool
operator!=(const char* lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) != 0;
  }

inline
bool
operator!=(const charbuf_256& lhs, const char* rhs) noexcept
  {
    return ::strcmp(lhs, rhs) != 0;
  }

inline
bool
operator<(const charbuf_256& lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) < 0;
  }

inline
bool
operator<(const char* lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) < 0;
  }

inline
bool
operator<(const charbuf_256& lhs, const char* rhs) noexcept
  {
    return ::strcmp(lhs, rhs) < 0;
  }

inline
bool
operator>(const charbuf_256& lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) > 0;
  }

inline
bool
operator>(const char* lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) > 0;
  }

inline
bool
operator>(const charbuf_256& lhs, const char* rhs) noexcept
  {
    return ::strcmp(lhs, rhs) > 0;
  }

inline
bool
operator<=(const charbuf_256& lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) <= 0;
  }

inline
bool
operator<=(const char* lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) <= 0;
  }

inline
bool
operator<=(const charbuf_256& lhs, const char* rhs) noexcept
  {
    return ::strcmp(lhs, rhs) <= 0;
  }

inline
bool
operator>=(const charbuf_256& lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) >= 0;
  }

inline
bool
operator>=(const char* lhs, const charbuf_256& rhs) noexcept
  {
    return ::strcmp(lhs, rhs) >= 0;
  }

inline
bool
operator>=(const charbuf_256& lhs, const char* rhs) noexcept
  {
    return ::strcmp(lhs, rhs) >= 0;
  }

}  // namespace poseidon
#endif
