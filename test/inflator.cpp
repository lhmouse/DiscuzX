// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "utils.hpp"
#include "../poseidon/easy/easy_inflator.hpp"
using namespace ::poseidon;

int
main()
  {
    // https://www.rfc-editor.org/rfc/rfc7692
    // compression test
    Easy_Inflator infl;
    POSEIDON_TEST_CHECK(infl.output_size() == 0);
    infl.start(zlib_format_raw);
    POSEIDON_TEST_CHECK(infl.inflate("\xf2\x48\xcd\xc9\xc9", 5) == 5);
    POSEIDON_TEST_CHECK(infl.inflate("\x07\x00\x00\x00\xFF\xFF", 6) == 6);
    POSEIDON_TEST_CHECK(infl.output_size() == 5);
    POSEIDON_TEST_CHECK(::memcmp(infl.output_data(), "Hello", 5) == 0);

    // context takeover
    infl.output_clear();
    POSEIDON_TEST_CHECK(infl.inflate("\xf2\x00\x11\x00\x00\x00\x00\xFF\xFF", 9) == 9);
    POSEIDON_TEST_CHECK(infl.output_size() == 5);
    POSEIDON_TEST_CHECK(::memcmp(infl.output_data(), "Hello", 5) == 0);

    // end of stream
    infl.output_clear();
    POSEIDON_TEST_CHECK(infl.inflate("\x03\x13\x00\x42", 4) == 3);
    POSEIDON_TEST_CHECK(infl.finish() == true);
    POSEIDON_TEST_CHECK(infl.output_size() == 5);
    POSEIDON_TEST_CHECK(::memcmp(infl.output_data(), "Hello", 5) == 0);

    infl.output_clear();
    POSEIDON_TEST_CHECK(infl.inflate("\xf2\x48\xcd\xc9\xc9", 5) == 0);
    POSEIDON_TEST_CHECK(infl.finish() == true);
    POSEIDON_TEST_CHECK(infl.output_size() == 0);

    // reset
    infl.clear();
    POSEIDON_TEST_CHECK(infl.output_size() == 0);
    infl.start(zlib_format_raw);
    POSEIDON_TEST_CHECK(infl.inflate("\xf3\x48\xcd\xc9\xc9\x07\x00\x42", 8) == 7);
    POSEIDON_TEST_CHECK(infl.output_size() == 5);
    POSEIDON_TEST_CHECK(::memcmp(infl.output_data(), "Hello", 5) == 0);

    // uncompressed data test
    infl.clear();
    POSEIDON_TEST_CHECK(infl.output_size() == 0);
    infl.start(zlib_format_raw);
    POSEIDON_TEST_CHECK(infl.inflate("\x00\x05\x00\xfa\xff\x48\x65\x6c\x6c", 9) == 9);
    POSEIDON_TEST_CHECK(infl.inflate("\x6f\x00\x00\x00\xFF\xFF", 6) == 6);
    POSEIDON_TEST_CHECK(infl.output_size() == 5);
    POSEIDON_TEST_CHECK(::memcmp(infl.output_data(), "Hello", 5) == 0);
  }