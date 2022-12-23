#!/usr/bin/env bash

ARG=$*
EXEC="vendor/bin/phpunit --prepend ./tests/functions.php --colors tests $ARG"
exec $EXEC