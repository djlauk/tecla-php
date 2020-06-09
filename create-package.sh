#!/bin/bash

BASEDIR=$(dirname $0)
VERSION=${VERSION:-$(date +%Y%m%d%H%M%S)}
ARCHIVE=tecla-$VERSION.zip
cd $BASEDIR/src
zip -9 -r ../$ARCHIVE * .htaccess -x config.local.php
