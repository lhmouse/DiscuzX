// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../precompiled.ipp"
#include "async_task_executor.hpp"
#include "../base/abstract_async_task.hpp"
#include "../utils.hpp"
namespace poseidon {

Async_Task_Executor::
Async_Task_Executor()
  {
  }

Async_Task_Executor::
~Async_Task_Executor()
  {
  }

void
Async_Task_Executor::
thread_loop()
  {
    plain_mutex::unique_lock lock(this->m_queue_mutex);
    while(this->m_queue.empty())
      this->m_queue_avail.wait(lock);

    auto task = this->m_queue.front().lock();
    this->m_queue.pop_front();
    lock.unlock();

    if(!task)
      return;

    // Execute it.
    // Exceptions are ignored.
    POSEIDON_LOG_TRACE(("Executing task `$1` (class `$2`)"), task, typeid(*task));
    task->m_state.store(async_state_running);

    try {
      task->do_abstract_task_on_execute();
    }
    catch(exception& stdex) {
      POSEIDON_LOG_ERROR((
          "Unhandled exception thrown from asynchronous task: $1",
          "[task class `$2`]"),
          stdex, typeid(*task));
    }

    ROCKET_ASSERT(task->m_state.load() == async_state_running);
    task->m_state.store(async_state_finished);
  }

void
Async_Task_Executor::
enqueue(shptrR<Abstract_Async_Task> task)
  {
    if(!task)
      POSEIDON_THROW(("Null task pointer not valid"));

    // Insert the task.
    plain_mutex::unique_lock lock(this->m_queue_mutex);
    this->m_queue.emplace_back(task);
    this->m_queue_avail.notify_one();
  }

}  // namespace poseidon
