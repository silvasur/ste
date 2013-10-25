#!/bin/sh

mkdir "$1"
touch "$1/test.tpl"
touch "$1/want"

echo '<?php

function test_func($ste) {
	
}' > "$1/code.php"

echo 'have
*.ast
*.transc.php' > "$1/.gitignore"
