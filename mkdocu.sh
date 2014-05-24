#!/bin/sh

if ! ndpath=`which NaturalDocs 2>/dev/null`; then
if ! ndpath=`which naturaldocs 2>/dev/null`; then
echo "NaturalDocs could not be found!" >/dev/stderr
exit 1
fi; fi


test -d docu/nd || mkdir docu/nd
test -d docu/nd_project_dir || mkdir docu/nd_project_dir
$ndpath -i src/ste -o html docu/nd -p docu/nd_project_dir
