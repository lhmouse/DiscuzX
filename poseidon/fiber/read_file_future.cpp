// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "../precompiled.ipp"
#include "read_file_future.hpp"
#include "../utils.hpp"
#include <sys/stat.h>
namespace poseidon {

Read_File_Future::
Read_File_Future(cow_stringR path, int64_t offset, size_t limit)
  {
    this->m_result.path = path;
    this->m_result.offset = offset;
    this->m_result.limit = limit;
  }

Read_File_Future::
~Read_File_Future()
  {
  }

void
Read_File_Future::
do_abstract_task_on_execute()
  try {
    // Open the file and get basic information.
    unique_posix_fd fd(::open(this->m_result.path.safe_c_str(), O_RDONLY | O_NOCTTY, 0));
    if(!fd)
      POSEIDON_THROW((
          "Could not open file `$1` for reading",
          "[`open()` failed: ${errno:full}]"),
          this->m_result.path);

    struct ::stat64 st;
    if(::fstat64(fd, &st) != 0)
      POSEIDON_THROW((
          "Could not get information about file `$1`",
          "[`fstat64()` failed: ${errno:full}]"),
          this->m_result.path);

    if(S_ISREG(st.st_mode) == false)
      POSEIDON_THROW((
          "Reading non-regular file `$1` not allowed"),
          this->m_result.path);

    this->m_result.accessed_on = (system_time)(seconds) st.st_atim.tv_sec + (nanoseconds) st.st_atim.tv_nsec;
    this->m_result.modified_on = (system_time)(seconds) st.st_mtim.tv_sec + (nanoseconds) st.st_mtim.tv_nsec;
    this->m_result.file_size = st.st_size;

    if(this->m_result.offset != 0) {
      // Seek to the given offset. Negative values denote offsets from the end.
      ::off_t abs_off = ::lseek64(fd, this->m_result.offset, (this->m_result.offset >= 0) ? SEEK_SET : SEEK_END);
      if(abs_off == -1)
        POSEIDON_THROW((
            "Could not reposition file `$1`",
            "[`lseek64()` failed: ${errno:full}]"),
            this->m_result.path);

      // Update `m_result.offset` to the absolute value.
      this->m_result.offset = abs_off;
    }

    while(this->m_result.data.size() < this->m_result.limit) {
      // Read bytes and append them to `m_result.data`.
      uint32_t step_limit = (uint32_t) ::std::min<size_t>(this->m_result.limit - this->m_result.data.size(), INT_MAX);
      this->m_result.data.reserve_after_end(step_limit);
      ::ssize_t step_size = POSEIDON_SYSCALL_LOOP(::read(fd, this->m_result.data.mut_end(), step_limit));
      if(step_size == 0)
        break;
      else if(step_size < 0)
        POSEIDON_THROW((
            "Could not read file `$1`",
            "[`read()` failed: ${errno:full}]"),
            this->m_result.path);

      // Accept bytes that have been read.
      this->m_result.data.accept((size_t) step_size);
    }

    // Set the future as a success.
    this->do_set_ready(nullptr);
  }
  catch(exception& stdex) {
    POSEIDON_LOG_WARN(("Could not read file `$1`: $2"), this->m_result.path, stdex);

    // Set the future as a failure.
    this->do_set_ready(::std::current_exception());
  }

}  // namespace poseidon
