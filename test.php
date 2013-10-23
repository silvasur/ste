<?php

require("stupid_template_engine.php");

$file = file_get_contents("example/templates/src/master.html");

try {
	$ast = \ste\Parser::parse($file, "master.html");
} catch(\ste\ParseCompileError $e) {
	$e->rewrite($file);
	echo "Could not parse:\n";
	echo $e->getMessage();
	echo "\n";
	exit(1);
}

echo "~~~~~~~~~~~\n";
var_dump($ast);