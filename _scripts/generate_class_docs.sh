#!/bin/bash

cd "$( dirname $0)/.."
APPDIR=$(pwd)
DOCDIR=$APPDIR/php-class

# works on axels dev env only
cd /home/axel/data/opensource/php-class/class-phpdoc || exit

set -vx
./parse-class.php --out md \
    --source "https://github.com/iml-it/appmonitor-api-client/blob/main/php-class/appmonitorapi.class.php"\
    "$APPDIR/php-class/appmonitorapi.class.php" \
    >"$APPDIR/php-class/appmonitorapi.class.php.md"
set +vx
cp "$APPDIR/php-class/appmonitorapi.class.php.md" "$APPDIR/docs/40_PHP_class/"
echo "Dome."
