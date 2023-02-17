// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#ifndef POSEIDON_BASE_DEFLATOR_
#define POSEIDON_BASE_DEFLATOR_

#include "../fwd.hpp"
#include "../third/zlib_fwd.hpp"
namespace poseidon {

class Deflator
  {
  private:
    zlib_Deflate_Stream m_strm;

  public:
    // Constructs a data compressor. `format` shall be `zlib_format_raw`,
    // `zlib_format_deflate` or `zlib_format_gzip`. `level` shall be an integer
    // between 0 (no compression) and 9 (best compression). `wbits` shall be an
    // integer between 9 and 15, inclusively.
    explicit
    Deflator(zlib_Format format, int level = 8, int wbits = 15);

  private:
    inline
    void
    do_deflate_prepare(const char* data);

    inline
    int
    do_deflate(uint8_t*& end_out, int flush);

    inline
    void
    do_deflate_cleanup(uint8_t* end_out);

  protected:
    // This callback is invoked to request an output buffer if none has been
    // requested, or when the previous output buffer is full. Derived classes
    // shall return a temporary memory region where compressed data will be
    // written, or throw an exception if the request cannot be honored. Note
    // that if this function returns a buffer that is too small (for example
    // 3 or 4 bytes) then `finish()` will fail.
    // If an exception is thrown, the state of this stream is unspecified.
    virtual
    pair<char*, size_t>
    do_on_deflate_get_output_buffer() = 0;

    // This callback is invoked to inform derived classes that all input data
    // have been compressed but the output buffer is not full. `nbackup` is the
    // number of uninitialized bytes in the end of the previous buffer. All the
    // output buffers, other than these uninitialized bytes, have been filled
    // with compressed data.
    // If an exception is thrown, the state of this stream is unspecified.
    virtual
    void
    do_on_deflate_truncate_output_buffer(size_t nbackup) = 0;

  public:
    ASTERIA_NONCOPYABLE_VIRTUAL_DESTRUCTOR(Deflator);

    // Gets the deflate stream.
    ::z_stream*
    z_stream() noexcept
      { return this->m_strm;  }

    // Clears internal states. Pending data are discarded.
    Deflator&
    clear() noexcept;

    // Compresses some data and returns the number of bytes that have been
    // consumed. This function returns zero if `finish()` has been called to
    // complete the current stream.
    size_t
    deflate(const char* data, size_t size);

    // Completes the current deflate block. The effect of this function is
    // described by zlib about its `Z_SYNC_FLUSH` argument.
    bool
    sync_flush();

    // Completes the current stream. No data shall be written any further. The
    // effect of this function is described by zlib about its `Z_FINISH`
    // argument.
    bool
    finish();
  };

}  // namespace poseidon
#endif