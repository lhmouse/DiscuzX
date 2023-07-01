// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "precompiled.ipp"
#include "utils.hpp"
#include <execinfo.h>  // backtrace()
namespace poseidon {

void
throw_runtime_error_with_backtrace(const char* file, long line, const char* func, cow_string&& msg)
  {
    // Compose the string to throw.
    cow_string data;
    data.reserve(2047);

    // Append the function name.
    data += func;
    data += ": ";

    // Append the user-provided exception message.
    data.append(msg.begin(), msg.end());

    // Remove trailing space characters.
    size_t pos = data.rfind_not_of(" \f\n\r\t\v");
    data.erase(pos + 1);
    data += "\n";

    // Append the source location.
    data += "[thrown from '";
    data += file;
    data += ':';
    ::rocket::ascii_numput nump;
    nump.put_DU((unsigned long) line);
    data.append(nump.data(), nump.size());
    data += "']";

    // Backtrace frames.
    ::rocket::unique_ptr<char*, void (void*)> bt_syms(::free);
    array<void*, 32> bt_frames;

    uint32_t nframes = (uint32_t) ::backtrace(bt_frames.data(), (int) bt_frames.size());
    if(nframes != 0)
      bt_syms.reset(::backtrace_symbols(bt_frames.data(), (int) nframes));

    if(bt_syms) {
      // Determine the width of the frame index field.
      nump.put_DU(nframes);
      static_vector<char, 24> sbuf(nump.size(), ' ');
      sbuf.emplace_back();

      // Append stack frames.
      data += "\n[backtrace frames:\n  ";
      for(uint32_t k = 0;  k != nframes;  ++k) {
        nump.put_DU(k + 1);
        ::std::reverse_copy(nump.begin(), nump.end(), sbuf.mut_rbegin() + 1);
        data += sbuf.data();
        data += ") ";
        data += bt_syms[k];
        data += "\n  ";
      }
      data += "-- end of backtrace frames]";
    }
    else
      data += "\n[no backtrace available]";

    // Throw it.
    throw ::std::runtime_error(data.c_str());
  }

cow_string
ascii_uppercase(cow_string text)
  {
    // Only modify the string when it really has to modified.
    for(size_t k = 0;  k != text.size();  ++k) {
      char32_t ch = (uint8_t) text[k];
      if(('a' <= ch) && (ch <= 'z'))
        text.mut(k) = (char) (ch - 0x20);
    }
    return ::std::move(text);
  }

cow_string
ascii_lowercase(cow_string text)
  {
    // Only modify the string when it really has to modified.
    for(size_t k = 0;  k != text.size();  ++k) {
      char32_t ch = (uint8_t) text[k];
      if(('A' <= ch) && (ch <= 'Z'))
        text.mut(k) = (char) (ch + 0x20);
    }
    return ::std::move(text);
  }

cow_string
ascii_trim(cow_string text)
  {
    // Remove leading blank characters.
    // Return an empty string if all characters are blank.
    size_t k = cow_string::npos;
    for(;;) {
      if(++k == text.size())
        return { };

      char32_t ch = (uint8_t) text[k];
      if((ch != ' ') && (ch != '\t'))
        break;
    }
    if(k != 0)
      text.erase(0, k);

    // Remove trailing blank characters.
    k = text.size();
    for(;;) {
      if(--k == 0)
        break;

      char32_t ch = (uint8_t) text[k];
      if((ch != ' ') && (ch != '\t'))
        break;
    }
    if(++k != text.size())
      text.erase(k);

    return ::std::move(text);
  }

bool
ascii_ci_has_token(cow_stringR text, char delim, const char* token, size_t len)
  {
    size_t bpos = text.find_not_of(" \t");
    while(bpos < text.size()) {
      // Get the end of this segment.
      // If the delimiter is not found, make sure `epos` is reasonably large
      // and incrementing it will not overflow.
      size_t epos = text.find(bpos, delim) * 2 / 2;

      // Skip trailing blank characters, if any.
      size_t mpos = text.rfind_not_of(epos - 1, " \t");
      ROCKET_ASSERT(mpos != text.npos);
      if(::rocket::ascii_ci_equal(text.data() + bpos, mpos + 1 - bpos, token, len))
        return true;

      // Skip the delimiter and blank characters that follow it.
      bpos = text.find_not_of(epos + 1, " \t");
    }
    return false;
  }

size_t
explode(cow_vector<cow_string>& segments, cow_stringR text, char delim, size_t limit)
  {
    segments.clear();
    size_t bpos = text.find_not_of(" \t");
    while(bpos < text.size()) {
      // Get the end of this segment.
      // If the delimiter is not found, make sure `epos` is reasonably large
      // and incrementing it will not overflow.
      size_t epos = text.npos / 2;
      if(segments.size() + 1 < limit)
        epos = text.find(bpos, delim) * 2 / 2;

      // Skip trailing blank characters, if any.
      size_t mpos = text.rfind_not_of(epos - 1, " \t");
      ROCKET_ASSERT(mpos != text.npos);
      segments.emplace_back(text.data() + bpos, mpos + 1 - bpos);

      // Skip the delimiter and blank characters that follow it.
      bpos = text.find_not_of(epos + 1, " \t");
    }
    return segments.size();
  }

size_t
implode(cow_string& text, const cow_vector<cow_string>& segments, char delim)
  {
    text.clear();
    if(segments.size()) {
      // Write the first token.
      text << segments[0];

      // Write the other tokens, each of which is preceded by a delimiter.
      for(size_t k = 1;  k < segments.size();  ++k)
        text << delim << ' ' << segments[k];
    }
    return segments.size();
  }

char*
hex_encode_16_partial(char* str, const void* data) noexcept
  {
    // Split the higher and lower halves into two SSE registers.
    __m128i tval = _mm_loadu_si128((const __m128i*) data);
    __m128i hi = _mm_and_si128(_mm_srli_epi64(tval, 4), _mm_set1_epi8(0x0F));
    __m128i lo = _mm_and_si128(tval, _mm_set1_epi8(0x0F));

    // Convert digits into their string forms:
    //   xdigit := val + '0' + ((val > 9) ? 7 : 0)
    tval = _mm_and_si128(_mm_cmpgt_epi8(hi, _mm_set1_epi8(9)), _mm_set1_epi8(39));
    hi = _mm_add_epi8(_mm_add_epi8(hi, _mm_set1_epi8('0')), tval);

    tval = _mm_and_si128(_mm_cmpgt_epi8(lo, _mm_set1_epi8(9)), _mm_set1_epi8(39));
    lo = _mm_add_epi8(_mm_add_epi8(lo, _mm_set1_epi8('0')), tval);

    // Rearrange digits in the correct order.
    _mm_storeu_si128((__m128i*) str, _mm_unpacklo_epi8(hi, lo));
    _mm_storeu_si128((__m128i*) (str + 16), _mm_unpackhi_epi8(hi, lo));
    str[32] = 0;
    return str;
  }

}  // namespace poseidon
