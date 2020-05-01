#!/bin/sh

[ -d docu/phpdoc ] || mkdir docu/phpdoc
./vendor/bin/phpdoc -d src -t docu/phpdoc
