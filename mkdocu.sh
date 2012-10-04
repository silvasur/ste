#!/usr/bin/env bash

if ! ndpath=$(which NaturalDocs 2>/dev/null); then
if ! ndpath=$(which naturaldocs 2>/dev/null); then
echo "NaturalDocs could not be found!" >/dev/stderr
exit 1
fi; fi


if [ ! -d docu/nd ]; then mkdir docu/nd; fi
if [ ! -d docu/nd_project_dir ]; then mkdir docu/nd_project_dir; fi
$ndpath -i . -o html docu/nd -p docu/nd_project_dir
