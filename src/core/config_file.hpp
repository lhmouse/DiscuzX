// This file is part of Poseidon.
// Copyleft 2022, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_CORE_CONFIG_FILE_
#define POSEIDON_CORE_CONFIG_FILE_

#include "../fwd.hpp"
#include <asteria/value.hpp>

namespace poseidon {

class Config_File
  {
  private:
    cow_string m_path;  // file path
    ::asteria::V_object m_root;  // contents

  public:
    constexpr
    Config_File() noexcept
      { }

    explicit
    Config_File(const cow_string& path)
      {
        this->reload(path);
      }

    Config_File&
    swap(Config_File& other) noexcept
      {
        this->m_path.swap(other.m_path);
        this->m_root.swap(other.m_root);
        return *this;
      }

  public:
    ~Config_File();

    // Returns the absolute file path.
    // If no file has been loaded, an empty string is returned.
    const cow_string&
    path() const noexcept
      { return this->m_path;  }

    // Accesses the contents.
    const ::asteria::V_object&
    root() const noexcept
      { return this->m_root;  }

    bool
    empty() const noexcept
      { return this->m_root.empty();  }

    Config_File&
    clear() noexcept
      {
        this->m_path.clear();
        this->m_root.clear();
        return *this;
      }

    // Loads the file denoted by `path`.
    // This function provides strong exception guarantee. In case of failure,
    // an exception is thrown, and the contents of this object are unchanged.
    Config_File&
    reload(const cow_string& path);

    // Gets a value denoted by a path, which shall not be empty.
    // If the path does not denote an existent value, a statically allocated
    // null value is returned. If during path resolution, an attempt is made
    // to get a field of a non-object, an exception is thrown.
    const ::asteria::Value&
    query(const char* const* psegs, size_t nsegs) const;

    const ::asteria::Value&
    query(initializer_list<const char*> path) const
      { return this->query(path.begin(), path.size());  }
  };

inline
void
swap(Config_File& lhs, Config_File& rhs) noexcept
  { lhs.swap(rhs);  }

}  // namespace poseidon

#endif
