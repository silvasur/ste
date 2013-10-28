<?php

require(dirname(__FILE__) . "/../ste.php");

var_dump(\ste\Parser::parse(file_get_contents("php://stdin"), "-"));
