#!/bin/bash
BOTDIR=/home/tozuka/twitter/marugametter

pushd . > /dev/null
cd $BOTDIR
/usr/bin/env php timeline_catcher.php
popd > /dev/null

