#!/usr/bin/env bash

if [ ! -d docu/nd ]; then mkdir docu/nd; fi
if [ ! -d docu/nd_project_dir ]; then mkdir docu/nd_project_dir; fi
NaturalDocs -i . -o html docu/nd -p docu/nd_project_dir
