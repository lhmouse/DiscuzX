// This file is part of Poseidon.
// Copyleft 2020, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_CORE_ABSTRACT_FUTURE_HPP_
#define POSEIDON_CORE_ABSTRACT_FUTURE_HPP_

#include "../fwd.hpp"

namespace poseidon {

class Abstract_Future
  : public ::asteria::Rcfwd<Abstract_Future>
  {
    template<typename> friend class Promise;
    template<typename> friend class Future;
    friend Fiber_Scheduler;

  private:
    mutable mutex m_mutex;
    atomic_relaxed<bool> m_ready;

    // These are scheduler data.
    mutable Abstract_Fiber* m_sched_ready_head = nullptr;

  public:
    Abstract_Future()
    noexcept
      { }

    ASTERIA_NONCOPYABLE_DESTRUCTOR(Abstract_Future);

  private:
    // Checks whether a value or exception has been set.
    // This functions is called by the fiber scheduler with the global mutex locked.
    ROCKET_PURE_FUNCTION
    bool
    do_is_ready_weak()
    const noexcept
      { return this->m_ready.load();  }

  public:
    // Gets the state, which is any of `future_state_empty`, `future_state_value`
    // or `future_state_except`.
    ROCKET_PURE_FUNCTION virtual
    Future_State
    state()
    const noexcept
      = 0;
  };

}  // namespace poseidon

#endif
