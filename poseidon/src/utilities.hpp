// This file is part of Poseidon.
// Copyleft 2020, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_UTILITIES_HPP_
#define POSEIDON_UTILITIES_HPP_

#include "fwd.hpp"
#include "static/async_logger.hpp"
#include "core/abstract_timer.hpp"
#include "static/timer_driver.hpp"
#include "core/abstract_async_job.hpp"
#include "core/promise.hpp"
#include "core/future.hpp"
#include "static/worker_pool.hpp"
#include <asteria/utilities.hpp>
#include <cstdio>

namespace poseidon {

using ::asteria::utf8_encode;
using ::asteria::utf8_decode;
using ::asteria::utf16_encode;
using ::asteria::utf16_decode;

using ::asteria::format_string;
using ::asteria::weaken_enum;
using ::asteria::generate_random_seed;
using ::asteria::format_errno;

template<typename... ParamsT>
ROCKET_NOINLINE
bool
do_xlog_format(Log_Level level, const char* file, long line, const char* func,
               const char* templ, const ParamsT&... params)
noexcept
  try {
    // Compose the message.
    ::rocket::tinyfmt_str fmt;
    format(fmt, templ, params...);  // ADL intended
    auto text = fmt.extract_string();

    // Push a new log entry.
    Async_Logger::enqueue(level, file, line, func, ::std::move(text));
    return true;
  }
  catch(exception& stdex) {
    // Ignore this exception, but print a message.
    ::std::fprintf(stderr,
        "WARNING: %s: could not format log: %s\n[exception `%s` thrown from '%s:%ld'\n",
        func, stdex.what(), typeid(stdex).name(), file, line);
    return false;
  }

// Note the format string must be a string literal.
#define POSEIDON_XLOG_(level, ...)  \
    (::poseidon::Async_Logger::is_enabled(level) &&  \
         ::poseidon::do_xlog_format(level, __FILE__, __LINE__, __func__, "" __VA_ARGS__))

#define POSEIDON_LOG_FATAL(...)   POSEIDON_XLOG_(::poseidon::log_level_fatal,  __VA_ARGS__)
#define POSEIDON_LOG_ERROR(...)   POSEIDON_XLOG_(::poseidon::log_level_error,  __VA_ARGS__)
#define POSEIDON_LOG_WARN(...)    POSEIDON_XLOG_(::poseidon::log_level_warn,   __VA_ARGS__)
#define POSEIDON_LOG_INFO(...)    POSEIDON_XLOG_(::poseidon::log_level_info,   __VA_ARGS__)
#define POSEIDON_LOG_DEBUG(...)   POSEIDON_XLOG_(::poseidon::log_level_debug,  __VA_ARGS__)
#define POSEIDON_LOG_TRACE(...)   POSEIDON_XLOG_(::poseidon::log_level_trace,  __VA_ARGS__)

template<typename... ParamsT>
[[noreturn]] ROCKET_NOINLINE
bool
do_xthrow_format(const char* file, long line, const char* func,
                 const char* templ, const ParamsT&... params)
  {
    // Compose the message.
    ::rocket::tinyfmt_str fmt;
    format(fmt, templ, params...);  // ADL intended
    auto text = fmt.extract_string();

    // Push a new log entry.
    static constexpr auto level = log_level_warn;
    if(Async_Logger::is_enabled(level))
      Async_Logger::enqueue(level, file, line, func, "POSEIDON_THROW: " + text);

    // Throw the exception.
    ::rocket::sprintf_and_throw<::std::runtime_error>(
        "%s: %s\n[thrown from '%s:%ld']",
        func, text.c_str(), file, line);
  }

#define POSEIDON_THROW(...)  \
    (::poseidon::do_xthrow_format(__FILE__, __LINE__, __func__,  \
                                  "" __VA_ARGS__))

// Creates an asynchronous timer. The timer function will be called by
// the timer thread, so thread safety must be taken into account.
template<typename FuncT>
rcptr<Abstract_Timer>
create_async_timer(int64_t next, int64_t period, FuncT&& func)
  {
    // This is the concrete timer class.
    struct Concrete_Timer : Abstract_Timer
      {
        typename ::std::decay<FuncT>::type m_func;

        explicit
        Concrete_Timer(int64_t next, int64_t period, FuncT&& func)
          : Abstract_Timer(next, period),
            m_func(::std::forward<FuncT>(func))
          { }

        void
        do_on_async_timer(int64_t now)
        override
          { this->m_func(now);  }
      };

    // Allocate an abstract timer and insert it.
    auto timer = ::rocket::make_unique<Concrete_Timer>(next, period,
                                           ::std::forward<FuncT>(func));
    return Timer_Driver::insert(::std::move(timer));
  }

// Creates a one-shot timer. The timer is deleted after being triggered.
template<typename FuncT>
rcptr<Abstract_Timer>
create_async_timer_oneshot(int64_t next, FuncT&& func)
  {
    return noadl::create_async_timer(next, 0, ::std::forward<FuncT>(func));
  }

// Creates a periodic timer.
template<typename FuncT>
rcptr<Abstract_Timer>
create_async_timer_periodic(int64_t period, FuncT&& func)
  {
    return noadl::create_async_timer(period, period, ::std::forward<FuncT>(func));
  }

// Enqueues an asynchronous job and returns a future to its result.
// Functions with the same key will always be delivered to the same worker.
template<typename FuncT>
futp<typename ::std::result_of<FuncT ()>::type>
enqueue_async_job(uintptr_t key, FuncT&& func)
  {
    // This is the concrete function class.
    struct Concrete_Async_Job : Abstract_Async_Job
      {
        prom<typename ::std::result_of<FuncT ()>::type> m_prom;
        typename ::std::decay<FuncT>::type m_func;

        explicit
        Concrete_Async_Job(uintptr_t key, FuncT&& func)
          : Abstract_Async_Job(key),
            m_func(::std::forward<FuncT>(func))
          { }

        void
        do_execute()
        override
          try
            { this->m_prom.set_value(this->m_func());  }
          catch(...)
            { this->m_prom.set_exception(::std::current_exception());  }
      };

    // Allocate a function object.
    auto async = ::rocket::make_unique<Concrete_Async_Job>(key,
                                           ::std::forward<FuncT>(func));
    auto futr = async->m_prom.future();
    Worker_Pool::insert(::std::move(async));
    return futr;
  }

// Enqueues an asynchronous job and returns a future to its result.
// The function is delivered to a random worker.
template<typename FuncT>
futp<typename ::std::result_of<FuncT ()>::type>
enqueue_async_job(FuncT&& func)
  {
    // This is the concrete function class.
    struct Concrete_Async_Job : Abstract_Async_Job
      {
        prom<typename ::std::result_of<FuncT ()>::type> m_prom;
        typename ::std::decay<FuncT>::type m_func;

        explicit
        Concrete_Async_Job(FuncT&& func)
          : Abstract_Async_Job(reinterpret_cast<uintptr_t>(this) >> 4),
            m_func(::std::forward<FuncT>(func))
          { }

        void
        do_execute()
        override
          try
            { this->m_prom.set_value(this->m_func());  }
          catch(...)
            { this->m_prom.set_exception(::std::current_exception());  }
      };

    // Allocate a function object.
    auto async = ::rocket::make_unique<Concrete_Async_Job>(
                                           ::std::forward<FuncT>(func));
    auto futr = async->m_prom.future();
    Worker_Pool::insert(::std::move(async));
    return futr;
  }

// Hashes a string in a case-insensitive way.
struct ASCII_CI_Hash
  {
    constexpr
    size_t
    operator()(cow_string::shallow_type sh)
    const noexcept
      {
        auto bptr = sh.c_str();
        auto eptr = bptr + sh.length();

        // Implement the FNV-1a hash algorithm.
        char32_t reg = 0x811C9DC5;
        while(bptr != eptr) {
          char32_t ch = static_cast<uint8_t>(*(bptr++));

          // Upper-case letters are converted to corresponding lower-case
          // ones, so this algorithm is case-insensitive.
          if(('A' <= ch) && (ch <= 'Z'))
            ch |= 0x20;

          reg = (reg ^ ch) * 0x1000193;
        }
        return reg;
      }

    constexpr
    size_t
    operator()(const char* s)
    const noexcept
      { return (*this)(::rocket::sref(s));  }

    constexpr
    size_t
    operator()(const cow_string& s)
    const noexcept
      { return (*this)(::rocket::sref(s));  }
  };

template<typename StringT>
constexpr
size_t
ascii_ci_hash(const StringT& str)
noexcept
  { return ASCII_CI_Hash()(str);  }

// Compares two strings in a case-insensitive way.
struct ASCII_CI_Equal
  {
    constexpr
    bool
    operator()(cow_string::shallow_type s1, cow_string::shallow_type s2)
    const noexcept
      {
        if(s1.length() != s2.length())
          return false;

        auto bptr = s1.c_str();
        auto eptr = bptr + s1.length();
        auto kptr = s2.c_str();
        if(bptr == kptr)
          return true;

        // Perform character-wise comparison.
        while(bptr != eptr) {
          char32_t ch = static_cast<uint8_t>(*(bptr++));
          char32_t cmp = ch ^ static_cast<uint8_t>(*(kptr++));
          if(cmp == 0)
            continue;

          // Report inequality unless the two characters diff only in case.
          if(cmp != 0x20)
            return false;

          // Upper-case letters are converted to corresponding lower-case ones,
          // so this algorithm is case-insensitive.
          ch |= 0x20;
          if((ch < 'a') || ('z' < ch))
            return false;
        }
        return true;
      }

    constexpr
    bool
    operator()(cow_string::shallow_type s1, const char* s2)
    const noexcept
      { return (*this)(s1, ::rocket::sref(s2));  }

    constexpr
    bool
    operator()(cow_string::shallow_type s1, const cow_string& s2)
    const noexcept
      { return (*this)(s1, ::rocket::sref(s2));  }

    constexpr
    bool
    operator()(const char* s1, cow_string::shallow_type s2)
    const noexcept
      { return (*this)(::rocket::sref(s1), s2);  }

    constexpr
    bool
    operator()(const char* s1, const char* s2)
    const noexcept
      { return (*this)(::rocket::sref(s1), ::rocket::sref(s2));  }

    constexpr
    bool
    operator()(const char* s1, const cow_string& s2)
    const noexcept
      { return (*this)(::rocket::sref(s1), ::rocket::sref(s2));  }

    constexpr
    bool
    operator()(const cow_string& s1, cow_string::shallow_type s2)
    const noexcept
      { return (*this)(::rocket::sref(s1), s2);  }

    constexpr
    bool
    operator()(const cow_string& s1, const char* s2)
    const noexcept
      { return (*this)(::rocket::sref(s1), ::rocket::sref(s2));  }

    constexpr
    bool
    operator()(const cow_string& s1, const cow_string& s2)
    const noexcept
      { return (*this)(::rocket::sref(s1), ::rocket::sref(s2));  }
  };

template<typename StringT, typename OtherT>
constexpr
bool
ascii_ci_equal(const StringT& str, const OtherT& oth)
noexcept
  { return ASCII_CI_Equal()(str, oth);  }

// Converts all ASCII letters in a string into uppercase.
cow_string
ascii_uppercase(cow_string str);

// Converts all ASCII letters in a string into lowercase.
cow_string
ascii_lowercase(cow_string str);

// Removes all leading and trailing blank characters.
cow_string
ascii_trim(cow_string str);

}  // namespace asteria

#endif
