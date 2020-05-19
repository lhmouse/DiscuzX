// This file is part of Poseidon.
// Copyleft 2020, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_FWD_HPP_
#define POSEIDON_FWD_HPP_

#ifdef HAVE_CONFIG_H
#  include <config.h>
#endif

#include <asteria/fwd.hpp>
#include <rocket/unique_posix_fd.hpp>
#include <rocket/unique_posix_file.hpp>
#include <rocket/unique_posix_dir.hpp>
#include <rocket/mutex.hpp>
#include <rocket/recursive_mutex.hpp>
#include <rocket/condition_variable.hpp>
#include <string>
#include <vector>
#include <deque>

namespace poseidon {
namespace noadl = poseidon;

// Macros
#define POSEIDON_STATIC_CLASS_DECLARE(C)  \
    private:  \
      struct C##_self;  \
      static C##_self* const self;  \
      constexpr C() noexcept = default;  \
      C(const C&) = delete;  \
      C& operator=(const C&) = delete;  \
      ~C() = default  // no semicolon

#define POSEIDON_STATIC_CLASS_DEFINE(C)  \
    template<typename TmIkbXn1>  \
    ROCKET_ARTIFICIAL_FUNCTION static inline  \
    TmIkbXn1* C##_instance()  \
      { static TmIkbXn1 instance[1] = { };  \
        return instance;  }  \
    class C;  \
    struct C::C##_self* const C::self =  \
        C##_instance<struct C::C##_self>();  \
    struct C::C##_self : C  \
      // add members here

// Aliases
using ::std::initializer_list;
using ::std::integer_sequence;
using ::std::index_sequence;
using ::std::nullptr_t;
using ::std::max_align_t;
using ::std::int8_t;
using ::std::uint8_t;
using ::std::int16_t;
using ::std::uint16_t;
using ::std::int32_t;
using ::std::uint32_t;
using ::std::int64_t;
using ::std::uint64_t;
using ::std::intptr_t;
using ::std::uintptr_t;
using ::std::intmax_t;
using ::std::uintmax_t;
using ::std::ptrdiff_t;
using ::std::size_t;
using ::std::wint_t;
using ::std::exception;
using ::std::exception_ptr;
using ::std::type_info;
using ::std::pair;

using ::rocket::unique_posix_fd;
using ::rocket::unique_posix_file;
using ::rocket::unique_posix_dir;

using ::asteria::nullopt_t;
using ::asteria::cow_string;
using ::asteria::cow_u16string;
using ::asteria::cow_u32string;
using ::asteria::phsh_string;
using ::asteria::tinybuf;
using ::asteria::tinyfmt;

using ::asteria::cbegin;
using ::asteria::cend;
using ::asteria::begin;
using ::asteria::end;
using ::asteria::swap;
using ::asteria::xswap;
using ::asteria::nullopt;

using ::asteria::uptr;
using ::asteria::rcptr;
using ::asteria::rcfwdp;
using ::asteria::cow_vector;
using ::asteria::cow_bivector;
using ::asteria::sso_vector;
using ::asteria::array;
using ::asteria::opt;
using ::asteria::refp;

using Si_Mutex = ::rocket::mutex;
using Rc_Mutex = ::rocket::recursive_mutex;
using Cond_Var = ::rocket::condition_variable;

// Core
class Config_File;
class Abstract_Timer;

// Network
enum IO_Result : ptrdiff_t;
enum Connection_State : uint8_t;
class Socket_Address;
class Abstract_Socket;
class Abstract_Accept_Socket;
class Abstract_Stream_Socket;
class Abstract_Datagram_Socket;
class Abstract_TCP_Socket;
class Abstract_TLS_TCP_Socket;
class Abstract_UDP_Socket;

// Singletons
class Main_Config;
class Async_Logger;
class Timer_Driver;
class Network_Driver;

// Log levels
// Note each level has a hardcoded name and number.
// Don't change their values or reorder them.
enum Log_Level : uint8_t
  {
    log_level_fatal  = 0,
    log_level_error  = 1,
    log_level_warn   = 2,
    log_level_info   = 3,
    log_level_debug  = 4,
    log_level_trace  = 5,
  };

}  // namespace poseidon

#endif
