<?php

use kch42\ste\STECore;

function test_func($ste) {
	$ste->vars['test'] = 'foo"&<bar>';
	
	$ste->register_tag('echoarg', function ($ste, $params, $sub) {
		return $params['echo'];
	});
	
	// Autoescaping enabled by default
	$ste->escape_method = STECore::ESCAPE_HTML;
}
