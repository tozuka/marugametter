#!/bin/bash
BOTDIR=/home/tozuka/twitter/marugametter

pushd . > /dev/null
cd $BOTDIR
/usr/bin/env php chooser.php
popd > /dev/null

