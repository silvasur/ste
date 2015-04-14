<?php

use kch42\ste\STECore;

$ste_initializer = function($sa) {
	return new STECore($sa, STECore::ESCAPE_HTML);
};

function test_func($ste) {
	$ste->vars['test'] = 'foo"&<bar>';

	$ste->register_tag('echoarg', function ($ste, $params, $sub) {
		return $params['echo'];
	});
}
