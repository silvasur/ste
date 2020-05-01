<?php

require(dirname(__FILE__) . "/../ste.php");

ini_set("xdebug.var_display_max_depth", 1000);
var_dump(\ste\Parser::parse(file_get_contents("php://stdin"), "-"));
