#!/bin/sh

r=0
for t in test_*; do
	cd $t
	php ../test.php > have
	echo -ne "\e[1m$t\e[0m: "
	if cmp -s want have; then
		echo "OK"
		rm *.transc.php
	else
		echo "FAILED"
		for tpl in *.tpl; do
			php ../dump_ast.php < $tpl > $tpl.ast
		done
		r=1
	fi
	cd ..
done

exit $r
