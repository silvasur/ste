#!/usr/bin/env bash

if [ ! -d docu/nd ]; then mkdir docu/nd; fi
NaturalDocs -i . -o html docu/nd -p docu/nd
