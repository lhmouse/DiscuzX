#!/bin/bash -xe

# setup
export CXX=${CXX:-"g++"}
export CXXFLAGS='-O2 -g0 -std=gnu++14 -fno-gnu-keywords -Wno-zero-as-null-pointer-constant'

# build
${CXX} --version
mkdir -p m4
autoreconf -ifv
./configure --disable-silent-rules --enable-debug-checks --disable-static
make -j$(nproc)

# test
make -j$(nproc) check || (cat ./test-suite.log; false)
