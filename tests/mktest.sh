#!/bin/sh

mkdir "$1"
touch "$1/test.tpl"
touch "$1/want"

echo '<?php

use kch42\ste\STECore;

function test_func(STECore $ste)
{

}' > "$1/code.php"

echo 'have
*.ast
*.transc.php' > "$1/.gitignore"
