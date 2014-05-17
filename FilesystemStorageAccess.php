<?php

namespace kch42\ste;

/*
 * Class: FilesystemStorageAccess
 * The default <StorageAccess> implementation for loading / saving templates into a directory structure.
 */
class FilesystemStorageAccess implements StorageAccess {
	protected $sourcedir;
	protected $transcompileddir;
	
	/*
	 * Constructor: __construct
	 * 
	 * Parameters:
	 * 	$src - The directory with the sources (Writing permissions are not mandatory, because STE does not save template sources).
	 * 	$transc - The directory with the transcompiled templates (the PHP instance / the HTTP Server needs writing permissions to this directory).
	 */
	public function __construct($src, $transc) {
		$this->sourcedir        = $src;
		$this->transcompileddir = $transc;
	}
	
	public function load($tpl, &$mode) {
		$src_fn    = $this->sourcedir        . "/" . $tpl;
		$transc_fn = $this->transcompileddir . "/" . $tpl . ".php";
		
		if($mode == StorageAccess::MODE_SOURCE) {
			$content = @file_get_contents($src_fn);
			if($content === false) {
				throw new CantLoadTemplate("Template not found.");
			}
			return $content;
		}
		
		$src_stat    = @stat($src_fn);
		$transc_stat = @stat($transc_fn);
		
		if(($src_stat === false) and ($transc_stat === false)) {
			throw new CantLoadTemplate("Template not found.");
		} else if($transc_stat === false) {
			$mode = StorageAccess::MODE_SOURCE;
			return file_get_contents($src_fn);
		} else if($src_stat === false) {
			include($transc_fn);
			return $transcompile_fx;
		} else {
			if($src_stat["mtime"] > $transc_stat["mtime"]) {
				$mode = StorageAccess::MODE_SOURCE;
				return file_get_contents($src_fn);
			} else {
				include($transc_fn);
				return $transcompile_fx;
			}
		}
	}
	
	public function save($tpl, $data, $mode) {
		$fn = (($mode == StorageAccess::MODE_SOURCE) ? $this->sourcedir : $this->transcompileddir) . "/" . $tpl . (($mode == StorageAccess::MODE_TRANSCOMPILED) ? ".php" : "");
		@mkdir(dirname($fn), 0777, true);
		if(file_put_contents($fn, "<?php \$transcompile_fx = $data; ?>") === false) {
			throw new CantSaveTemplate("Unable to save template.");
		}
	}
}
