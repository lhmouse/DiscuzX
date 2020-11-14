#!/bin/bash -e

if test "$1" == "" || test "$1" == --help
then
  cat <<END_OF_MESSGAE
Usage:
  ./update_submodule.sh NAME [TAG]
END_OF_MESSGAE
  exit 0
fi

_submodule="$1"
_tag="${2:-master}"
_message="submodule/${_submodule}: Update to ${_tag}"

# Update to target branch
git submodule update --init -- "${_submodule}"
pushd "${_submodule}"
git checkout -f "${_tag}"
git pull --ff-only
popd

# Commit if there are diffs
if ! git diff HEAD --exit-code -- "${_submodule}"
then
  git commit -sm "${_message}" -- "${_submodule}"
fi
