#!/bin/sh

DIR=`php -r "echo dirname(dirname(realpath('$0')));"`
VENDOR="$DIR/vendor"
VERSION=`cat "$DIR/VERSION"`
BUNDLES=$VENDOR/bundles

echo "> Atualizando Subm�dulos "
git submodule update --init

# Update submodules
$DIR/bin/vendors update