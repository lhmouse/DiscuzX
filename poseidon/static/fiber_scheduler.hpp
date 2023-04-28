// This file is part of Poseidon.
// Copyleft 2023 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_STATIC_FIBER_SCHEDULER_
#define POSEIDON_STATIC_FIBER_SCHEDULER_

#include "../fwd.hpp"
#include <ucontext.h>  // ucontext_t
namespace poseidon {

class Fiber_Scheduler
  {
  private:
    struct X_Queued_Fiber;

    mutable plain_mutex m_conf_mutex;
    uint32_t m_conf_stack_vm_size = 0;
    seconds m_conf_warn_timeout = zero_duration;
    seconds m_conf_fail_timeout = zero_duration;

    mutable plain_mutex m_pq_mutex;
    vector<shptr<X_Queued_Fiber>> m_pq;
    ::timespec m_pq_wait[1] = { 0, 0, };

    mutable recursive_mutex m_sched_mutex;
    wkptr<X_Queued_Fiber> m_sched_self_opt;
    void* m_sched_asan_save;  // private data for address sanitizer
    ::ucontext_t m_sched_outer[1];  // yield target

  public:
    // Constructs an empty scheduler.
    explicit
    Fiber_Scheduler();

  public:
    ASTERIA_NONCOPYABLE_DESTRUCTOR(Fiber_Scheduler);

    // Reloads configuration from 'main.conf'.
    // If this function fails, an exception is thrown, and there is no effect.
    // This function is thread-safe.
    void
    reload(const Config_File& file);

    // Schedules fibers.
    // This function should be called by the fiber thread repeatedly.
    void
    thread_loop();

    // Returns the number of fibers that are being scheduled.
    // This function is thread-safe.
    ROCKET_PURE
    size_t
    size() const noexcept;

    // Takes ownership of a fiber, and schedules it for execution. The fiber
    // can only be deleted after it finishes execution.
    // This function is thread-safe.
    void
    launch(shptrR<Abstract_Fiber> fiber);

    // Gets the current fiber if one is being scheduled.
    // This function shall be called from the same thread as `thread_loop()`.
    ROCKET_CONST
    Abstract_Fiber*
    self_opt() const noexcept;

    // Suspends the current fiber until a future becomes satisfied. `self_opt()`
    // must not return a null pointer when this function is called. If
    // `fail_timeout_override` is non-zero, it overrides `fiber.fail_timeout`
    // in 'main.conf'. Suspension may not exceed the fail timeout.
    void
    check_and_yield(const Abstract_Fiber* self, shptrR<Abstract_Future> futr_opt, milliseconds fail_timeout_override);
  };

}  // namespace poseidon
#endif
