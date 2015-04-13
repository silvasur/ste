<?php

// File: STECore.php

// Namespace: kch42\ste
namespace kch42\ste;

/*
 * Class: STECore
 * The Core of STE
 */
class STECore {
	const ESCAPE_NONE = "none";
	const ESCAPE_HTML = "html";
	
	private $tags;
	private $storage_access;
	private $cur_tpl_dir;
	public $escape_method = self::ESCAPE_NONE;
	public $scope;
	
	/*
	 * Variables: Public variables
	 * 
	 * $blocks - Associative array of blocks (see the language definition).
	 * $blockorder - The order of the blocks (an array)
	 * $mute_runtime_errors - If true (default) a <RuntimeError> exception will result in no output from the tag, if false a error message will be written to output.
	 * $fatal_error_on_missing_tag - If true, STE will throw a <FatalRuntimeError> if a tag was called that was not registered, otherwise (default) a regular <RuntimeError> will be thrown and automatically handled by STE (see <$mute_runtime_errors>).
	 * $vars - Variables in the top scope of the template.
	 */
	public $blocks;
	public $blockorder;
	public $mute_runtime_errors = true;
	public $fatal_error_on_missing_tag = false;
	public $vars;
	
	/*
	 * Constructor: __construct
	 * 
	 * Parameters:
	 * 	$storage_access - An Instance of a <StorageAccess> implementation.
	 */
	public function __construct($storage_access) {
		$this->storage_access = $storage_access;
		$this->cur_tpl_dir = "/";
		STEStandardLibrary::_register_lib($this);
		$this->blockorder = array();
		$this->blocks = array();
		
		$this->vars = array();
		$this->scope = new Scope();
		$this->scope->vars =& $this->vars;
	}
	
	/*
	 * Function: register_tag
	 * Register a custom tag.
	 * 
	 * Parameters:
	 * 	$name - The name of the tag.
	 * 	$callback - A callable function (This must take three parameters: The <STECore> instance, an associative array of parameters, and a function representing the tags content(This expects the <STECore> instance as its only parameter and returns its text result, i.e to get the text, you neeed to call this function with the <STECore> instance as a parameter)).
	 * 
	 * Throws:
	 * 	An Exception if the tag could not be registered (if $callback is not callable or if $name is empty)
	 */
	public function register_tag($name, $callback) {
		if(!is_callable($callback)) {
			throw new \Exception("Can not register tag \"$name\", not callable.");
		}
		if(empty($name)) {
			throw new \Exception("Can not register tag, empty name.");
		}
		$this->tags[$name] = $callback;
	}
	
	/*
	 * Function: call_tag
	 * Calling a custom tag (builtin ones can not be called)
	 * 
	 * Parameters:
	 * 	$name - The Tag's name
	 * 	$params - Associative array of parameters
	 * 	$sub - A callable function (expecting an <STECore> instance as it's parameter) that represents the tag's content.
	 * 
	 * Throws:
	 * 	Might throw a <FatalRuntimeError> (see <$fatal_error_on_missing_tag>.
	 * 
	 * Returns:
	 * 	The output of the tag or, if a <RuntimeError> was thrown, the appropiate result (see <$mute_runtime_errors>).
	 */
	public function call_tag($name, $params, $sub) {
		try {
			if(!isset($this->tags[$name])) {
				if($this->fatal_error_on_missing_tag) {
					throw new FatalRuntimeError("Can not call tag \"$name\": Does not exist.");
				} else {
					throw new RuntimeError("Can not call tag \"$name\": Does not exist.");
				}
			}
			return call_user_func($this->tags[$name], $this, $params, $sub);
		} catch(RuntimeError $e) {
			if(!$this->mute_runtime_errors) {
				return "RuntimeError occurred on tag '$name': " . $e->getMessage();
			}
		}
	}
	
	public function autoescape($content) {
		if ($this->escape_method == self::ESCAPE_HTML) {
			return htmlspecialchars($content);
		}
		return $content;
	}
	
	public function eval_sub_with_escaping($sub, $method) {
		$old_method = $this->escape_method;
		$this->escape_method = $method;
		$retval = $sub($this);
		$this->escape_method = $old_method;
		return $retval;
	}
	
	public function calc($expression) {
		return Calc::calc($expression);
	}
	
	/*
	 * Function: exectemplate
	 * Executes a template and returns the result. The huge difference to <load> is that this function will also output all blocks.
	 * 
	 * Parameters:
	 * 	$tpl - The name of the template to execute.
	 * 
	 * Throws:
	 * 	* A <CantLoadTemplate> exception if the template could not be loaded.
	 * 	* A <ParseCompileError> if the template could not be parsed or transcompiled.
	 * 	* A <FatalRuntimeError> if a tag threw it or if a tag was not found and <$fatal_error_on_missing_tag> is true.
	 * 	* Might also throw different exceptions, if a external tag threw it (but they should use <RuntimeError> or <FatalRuntimeError> to make it possible for STE to handle them correctly).
	 * 
	 * Returns:
	 * 	The output of the template.
	 */
	public function exectemplate($tpl) {
		$output = "";
		$lastblock = $this->load($tpl);
		
		foreach($this->blockorder as $blockname) {
			$output .= $this->blocks[$blockname];
		}
		
		return $output . $lastblock;
	}
	
	/*
	 * Function: get_var_reference
	 * Get a reference to a template variable using a variable name.
	 * This can be used,if your custom tag takes a variable name as a parameter.
	 * 
	 * Parameters:
	 * 	$name - The variables name.
	 * 	$create_if_not_exist - Should the variable be created, if it does not exist? Otherwise NULL will be returned, if the variable does not exist.
	 * 
	 * Throws:
	 * 	<RuntimeError> if the variable name can not be parsed (e.g. unbalanced brackets).
	 * 
	 * Returns:
	 * 	A Reference to the variable.
	 */
	public function &get_var_reference($name, $create_if_not_exist) {
		$ref = &$this->scope->get_var_reference($name, $create_if_not_exist);
		return $ref;
	}
	
	/*
	 * Function: set_var_by_name
	 * Set a template variable by its name.
	 * This can be used,if your custom tag takes a variable name as a parameter.
	 * 
	 * Parameters:
	 * 	$name - The variables name.
	 * 	$val - The new value.
	 * 
	 * Throws:
	 * 	<RuntimeError> if the variable name can not be parsed (e.g. unbalanced brackets).
	 */
	public function set_var_by_name($name, $val) {
		$this->scope->set_var_by_name($name, $val);
	}
	
	/*
	 * Function: set_local_var
	 * Like <set_var_by_name>, but only sets the variable in the global scope (<set_var_by_name> will overwrite the variable in the parent scope, if it's defined there) .
	 * 
	 * Parameters:
	 * 	$name - The variables name.
	 * 	$val - The new value.
	 * 
	 * Throws:
	 * 	<RuntimeError> if the variable name can not be parsed (e.g. unbalanced brackets).
	 */
	public function set_local_var($name, $val) {
		$this->scope->set_local_var($name, $val);
	}
	
	/*
	 * Function: get_var_by_name
	 * Get a template variable by its name.
	 * This can be used,if your custom tag takes a variable name as a parameter.
	 * 
	 * Parameters:
	 * 	$name - The variables name.
	 * 
	 * Throws:
	 * 	<RuntimeError> if the variable name can not be parsed (e.g. unbalanced brackets).
	 * 
	 * Returns:
	 * 	The variables value.
	 */
	public function get_var_by_name($name) {
		return $this->scope->get_var_by_name($name);
	}
	
	/*
	 * Function: load
	 * Load a template and return its result (blocks not included, use <exectemplate> for this).
	 * 
	 * Parameters:
	 * 	$tpl - The name of the template to be loaded.
	 * 	$quiet - If true, do not output anything and do not modify the blocks. This can be useful to load custom tags that are programmed in the STE Template Language. Default: false.
	 * 
	 * Throws:
	 * 	* A <CantLoadTemplate> exception if the template could not be loaded.
	 * 	* A <ParseCompileError> if the template could not be parsed or transcompiled.
	 * 	* A <FatalRuntimeError> if a tag threw it or if a tag was not found and <$fatal_error_on_missing_tag> is true.
	 * 	* Might also throw different exceptions, if a external tag threw it (but they should use <RuntimeError> or <FatalRuntimeError> to make it possible for STE to handle them correctly).
	 * 
	 * Returns:
	 * 	The result of the template (if $quiet == false).
	 */
	public function load($tpl, $quiet = false) {
		$tpldir_b4 = $this->cur_tpl_dir;
		
		/* Resolve ".", ".." and protect from possible LFI */
		$tpl = str_replace("\\", "/", $tpl);
		if($tpl[0] != "/") {
			$tpl = $this->cur_tpl_dir . "/" . $tpl;
		}
		$pathex = array_filter(explode("/", $tpl), function($s) { return ($s != ".") and (!empty($s)); });
		$pathex = array_merge($pathex);
		while(($i = array_search("..", $pathex)) !== false) {
			if($i == 0) {
				$pathex = array_slice($pathex, 1);
			} else {
				$pathex = array_merge(array_slice($pathex, 0, $i), array_slice($pathex, $i + 2));
			}
		}
		$tpl = implode("/", $pathex);
		$this->cur_tpl_dir = dirname($tpl);
		
		if($quiet) {
			$blocks_back     = clone $this->blocks;
			$blockorder_back = clone $this->blockorder;
		}
		
		$mode = StorageAccess::MODE_TRANSCOMPILED;
		$content = $this->storage_access->load($tpl, $mode);
		if($mode == StorageAccess::MODE_SOURCE) {
			try {
				$ast     = Parser::parse($content, $tpl);
				$transc  = Transcompiler::transcompile($ast);
			} catch(ParseCompileError $e) {
				$e->rewrite($content);
				throw $e;
			}
			$this->storage_access->save($tpl, $transc, StorageAccess::MODE_TRANSCOMPILED);
			eval("\$content = $transc;");
		}
		
		$output = $content($this);
		
		$this->cur_tpl_dir = $tpldir_b4;
		
		if($quiet) {
			$this->blocks     = $blocks_back;
			$this->blockorder = $blockorder_back;
		} else {
			return $output;
		}
	}
	
	/*
	 * Function: evalbool
	 * Test, if a text represents false (an empty / only whitespace text) or true (everything else).
	 * 
	 * Parameters:
	 * 	$txt - The text to test.
	 * 
	 * Returns:
	 * 	true/false.
	 */
	public function evalbool($txt) {
		return trim(@(string)$txt) != "";
	}
	
	public function make_closure($fx) {
		$bound_scope = $this->scope;
		return function() use($bound_scope, $fx) {
			$args = func_get_args();
			$ste = $args[0];
			
			$prev = $ste->scope;
			$scope = $bound_scope->new_subscope();
			$ste->scope = $scope;
			
			try {
				$result = call_user_func_array($fx, $args);
				$ste->scope = $prev;
				return $result;
			} catch(\Exception $e) {
				$ste->scope = $prev;
				throw $e;
			}
		};
	}
}
