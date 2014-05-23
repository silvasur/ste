<?php

require(dirname(__FILE__) . "/../steloader.php");
require("code.php");

use \kch42\ste;

class TestStorage implements ste\StorageAccess {
	public function load($tpl, &$mode) {
		$mode = ste\StorageAccess::MODE_SOURCE;
		return file_get_contents($tpl);
	}
	
	public function save($tpl, $data, $mode) {
		if($mode != ste\StorageAccess::MODE_TRANSCOMPILED) {
			return;
		}
		
		file_put_contents("$tpl.transc.php", $data);
	}
}

$ste = new ste\STECore(new TestStorage());
$ste->mute_runtime_errors = false;
test_func($ste);
echo $ste->exectemplate("test.tpl");
