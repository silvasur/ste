<?php

require(dirname(__FILE__) . "/../stupid_template_engine.php");

var_dump(\ste\Parser::parse(file_get_contents("php://stdin"), "-"));
