// This file is part of Poseidon.
// Copyleft 2020, LH_Mouse. All wrongs reserved.

#include "../precompiled.hpp"
#include "main_config.hpp"
#include "../utilities.hpp"

namespace poseidon {

POSEIDON_STATIC_CLASS_DEFINE(Main_Config)
  {
    ::rocket::mutex m_mutex;
    Config_File m_data;
  };

void
Main_Config::
clear()
noexcept
  {
    // Create an empty file.
    Config_File temp;

    // During destruction of `temp` the mutex should have been unlocked.
    // The swap operation is presumed to be fast, so we don't hold the mutex
    // for too long.
    ::rocket::mutex::unique_lock lock(self->m_mutex);
    self->m_data.swap(temp);
  }

void
Main_Config::
reload()
  {
    // Load the global config file by relative path.
    // An exception is thrown upon failure.
    Config_File temp("etc/poseidon/main.conf");

    // During destruction of `temp` the mutex should have been unlocked.
    // The swap operation is presumed to be fast, so we don't hold the mutex
    // for too long.
    ::rocket::mutex::unique_lock lock(self->m_mutex);
    self->m_data.swap(temp);
  }

Config_File
Main_Config::
copy()
noexcept
  {
    // Note again config files are copy-on-write and cheap to copy.
    ::rocket::mutex::unique_lock lock(self->m_mutex);
    return self->m_data;
  }

}  // poseidon
