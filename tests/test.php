<?php

require(dirname(__FILE__) . "/../ste.php");
require("code.php");

class TestStorage implements \ste\StorageAccess {
	public function load($tpl, &$mode) {
		$mode = \ste\MODE_SOURCE;
		return file_get_contents($tpl);
	}
	
	public function save($tpl, $data, $mode) {
		if($mode != \ste\MODE_TRANSCOMPILED) {
			return;
		}
		
		file_put_contents("$tpl.transc.php", $data);
	}
}

$ste = new \ste\STECore(new TestStorage());
test_func($ste);
echo $ste->exectemplate("test.tpl");
