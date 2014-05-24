<?php

// File: StorageAccess.php

// Namespace: kch42\ste
namespace kch42\ste;

/*
 * Class: StorageAccess
 * An interface.
 * A StorageAccess implementation is used to access the templates from any storage.
 * This means, that you are not limited to store the Templates inside directories, you can also use a database or something else.
 */
interface StorageAccess {
	/*
	 * Constants: Template modes
	 * 
	 * MODE_SOURCE - The Templates source
	 * MODE_TRANSCOMPILED - The transcompiled template
	 */
	const MODE_SOURCE        = 0;
	const MODE_TRANSCOMPILED = 1;
	
	/*
	 * Function: load
	 * Loading a template.
	 * 
	 * Parameters:
	 * 	$tpl - The name of the template.
	 * 	&$mode - Which mode is preferred? One of the <Template modes>.
	 * 	         If <MODE_SOURCE>, the raw sourcecode is expected, if <MODE_TRANSCOMPILED> the transcompiled template *as a callable function* (expecting an <STECore> instance as first parameter) is expected.
	 * 	         If the transcompiled version is not available or older than the source, you can set this parameter to <MODE_SOURCE> and return the source.
	 * 
	 * Throws:
	 * 	A <CantLoadTemplate> exception if the template could not be loaded.
	 * 
	 * Returns:
	 * 	Either the sourcecode or a callable function (first, and only parameter: an <STECore> instance).
	 */
	public function load($tpl, &$mode);
	
	/*
	 * Function: save
	 * Saves a template.
	 * 
	 * Throws:
	 * 	A <CantSaveTemplate> exception if the template could not be saved.
	 * 
	 * Parameters:
	 * 	$tpl -The name of the template.
	 * 	$data - The data to be saved.
	 * 	$mode - A <Template mode> constant.
	 */
	public function save($tpl, $data, $mode);
}
