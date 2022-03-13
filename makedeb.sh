#!/bin/bash -e

_pkgname=poseidon
_pkgversion=$(git describe 2>/dev/null || printf "0.%u.%s" "$(git rev-list --count HEAD)" "$(git rev-parse --short HEAD)")
_tempdir=$(readlink -f "./.makedeb")
_debiandir=${_tempdir}/DEBIAN

rm -rf ${_tempdir}
mkdir -p ${_tempdir}/etc/poseidon
cp -pr DEBIAN -T ${_tempdir}/DEBIAN
cp -pr poseidon/etc -T ${_tempdir}/etc/poseidon

make install DESTDIR=${_tempdir}
sed -i "s/{_pkgname}/${_pkgname}/" ${_debiandir}/control
sed -i "s/{_pkgversion}/${_pkgversion}/" ${_debiandir}/control

dpkg-deb --root-owner-group --build .makedeb "${_pkgname}_${_pkgversion}.deb"
