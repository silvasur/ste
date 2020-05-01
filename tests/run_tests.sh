#!/bin/sh

set -e

run() {
    ( # So we don't have to cd ..
        cd "$1" || return 1

        php ../test.php > have

        printf '\e[1m%s\e[0m: ' "$1"
        if sed 's/\s*//' < have | grep -v '^$' | cmp -s want; then
            echo "OK"
            rm ./*.transc.php
        else
            echo "FAILED"
            for tpl in *.tpl; do
                php ../dump_ast.php < "$tpl" > "$tpl.ast"
            done
            return 1
        fi
    )
}

run_many() {
    retval=0
    while [ $# -gt 0 ]; do
        if ! run "$1"; then
            retval=1
        fi

        shift
    done

    return $retval
}

if [ $# -eq 0 ]; then
    run_many test_*
else
    run_many "$@"
fi
