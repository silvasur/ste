<?php

namespace kch42\ste;

use Exception;

/**
 * The Core of STE
 */
class STECore
{
    /** @var callable[] */
    private $tags;

    /** @var StorageAccess */
    private $storage_access;

    /** @var string */
    private $cur_tpl_dir;

    /** @var Scope */
    public $scope;

    /**
     * @var array
     * Associative array of blocks (see the language definition).
     */
    public $blocks;

    /**
     * @var array
     * The order of the blocks (an array)
     */
    public $blockorder;

    /**
     * @var bool
     * If true (default) a {@see RuntimeError} exception will result in no
     * output from the tag, if false a error message will be written to output.
     */
    public $mute_runtime_errors = true;

    /**
     * @var bool
     * If true, STE will throw a {@see FatalRuntimeError} if a tag was called
     * that was not registered, otherwise (default) a regular
     * {@see RuntimeError} will be thrown and automatically handled by STE
     * (see {@see STECore::$mute_runtime_errors}).
     */
    public $fatal_error_on_missing_tag = false;

    /**
     * @var array
     * Variables in the top scope of the template.
     */
    public $vars;

    /**
     * @param StorageAccess $storage_access
     */
    public function __construct(StorageAccess $storage_access)
    {
        $this->storage_access = $storage_access;
        $this->cur_tpl_dir = "/";
        STEStandardLibrary::_register_lib($this);
        $this->blockorder = [];
        $this->blocks = [];

        $this->vars = [];
        $this->scope = new Scope();
        $this->scope->vars =& $this->vars;
    }

    /**
     * Register a custom tag.
     *
     * Parameters:
     * @param string $name The name of the tag.
     * @param callable $callback A callable function
     *                           Must take three parameters:
     *
     *                           The {@see STECore} instance,
     *                           an associative array of parameters,
     *                           and a function representing the tags content
     *                           (This expects the {@see STECore} instance as
     *                           its only parameter and returns its text result,
     *                           i.e to get the text, you need to call this
     *                           function with the {@see STECore} instance as a
     *                           parameter).
     *
     * @throws Exception If the tag could not be registered (if $callback is not callable or if $name is empty)
     */
    public function register_tag($name, $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception("Can not register tag \"$name\", not callable.");
        }
        if (empty($name)) {
            throw new Exception("Can not register tag, empty name.");
        }
        $this->tags[$name] = $callback;
    }

    /**
     * Calling a custom tag (builtin ones can not be called)
     *
     * @param string $name The Tag's name
     * @param array $params Associative array of parameters
     * @param callable $sub A callable function (expecting an {@see STECore} instance as it's parameter) that represents the tag's content.
     *
     * @throws FatalRuntimeError see {@see STECore::$fatal_error_on_missing_tag}.
     *
     * @return string The output of the tag or, if a {@see RuntimeError} was thrown, the appropriate result
     *                (see {@see STECore::$mute_runtime_errors}).
     */
    public function call_tag($name, $params, $sub)
    {
        try {
            if (!isset($this->tags[$name])) {
                if ($this->fatal_error_on_missing_tag) {
                    throw new FatalRuntimeError("Can not call tag \"$name\": Does not exist.");
                } else {
                    throw new RuntimeError("Can not call tag \"$name\": Does not exist.");
                }
            }
            return call_user_func($this->tags[$name], $this, $params, $sub);
        } catch (RuntimeError $e) {
            if (!$this->mute_runtime_errors) {
                return "RuntimeError occurred on tag '$name': " . $e->getMessage();
            }
        }

        return "";
    }

    /**
     * {@see Calc::calc()}
     *
     * @param string $expression
     * @return float|int
     * @throws RuntimeError
     */
    public function calc($expression)
    {
        return Calc::calc($expression);
    }

    /**
     * Executes a template and returns the result.
     *
     * The huge difference to {@see STECore::load()} is that this function will also output all blocks.
     *
     * @param string $tpl The name of the template to execute.
     *
     * @throws CantLoadTemplate If the template could not be loaded.
     * @throws ParseCompileError If the template could not be parsed or compiled.
     * @throws FatalRuntimeError If a tag threw it or if a tag was not found and <$fatal_error_on_missing_tag> is true.
     *
     * Might also throw different exceptions, if a external tag threw it
     * (but they should use {@see RuntimeError} or {@see FatalRuntimeError} to make it possible for STE to handle them correctly).
     *
     * @return string The output of the template.
     */
    public function exectemplate($tpl)
    {
        $output = "";
        $lastblock = $this->load($tpl);

        foreach ($this->blockorder as $blockname) {
            $output .= $this->blocks[$blockname];
        }

        return $output . $lastblock;
    }

    /**
     * Get a reference to a template variable using a variable name.
     * This can be used,if your custom tag takes a variable name as a parameter.
     *
     * @param string $name The variables name.
     * @param bool $create_if_not_exist Should the variable be created, if it does not exist? Otherwise NULL will be returned, if the variable does not exist.
     *
     * @throws RuntimeError If the variable name can not be parsed (e.g. unbalanced brackets).
     * @return mixed A Reference to the variable.
     */
    public function &get_var_reference($name, $create_if_not_exist)
    {
        $ref = &$this->scope->get_var_reference($name, $create_if_not_exist);
        return $ref;
    }

    /**
     * Set a template variable by its name.
     * This can be used,if your custom tag takes a variable name as a parameter.
     *
     * @param string $name The variables name.
     * @param mixed $val The new value.
     *
     * @throws RuntimeError If the variable name can not be parsed (e.g. unbalanced brackets).
     */
    public function set_var_by_name($name, $val)
    {
        $this->scope->set_var_by_name($name, $val);
    }

    /**
     * Like {@see STECore::set_var_by_name}, but only sets the variable in the global scope
     * ({@see STECore::set_var_by_name} will overwrite the variable in the parent scope, if it's defined there) .
     *
     * @param string $name The variables name.
     * @param mixed $val The new value.
     *
     * @throws RuntimeError If the variable name can not be parsed (e.g. unbalanced brackets).
     */
    public function set_local_var($name, $val)
    {
        $this->scope->set_local_var($name, $val);
    }

    /**
     * Get a template variable by its name.
     * This can be used,if your custom tag takes a variable name as a parameter.
     *
     * @param string $name The variables name.
     *
     * @throws RuntimeError If the variable name can not be parsed (e.g. unbalanced brackets).
     * @return mixed The variables value
     */
    public function get_var_by_name($name)
    {
        return $this->scope->get_var_by_name($name);
    }

    /**
     * Load a template and return its result (blocks not included, use {@see STECore::exectemplate} for this).
     *
     * @param string $tpl The name of the template to be loaded.
     * @param bool $quiet If true, do not output anything and do not modify the blocks. This can be useful to load custom tags that are programmed in the STE Template Language. Default: false.
     *
     * @throws CantLoadTemplate If the template could not be loaded.
     * @throws CantSaveTemplate If the template could not be saved.
     * @throws ParseCompileError If the template could not be parsed or compiled.
     * @throws FatalRuntimeError If a tag threw it or if a tag was not found and {@see STECore::$fatal_error_on_missing_tag} is true.
     *
     * Might also throw different exceptions, if a external tag threw it
     * (but they should use {@see RuntimeError} or {@see FatalRuntimeError} to make it possible for STE to handle them correctly).
     *
     * @return string|null The result of the template (if $quiet == false).
     */
    public function load($tpl, $quiet = false)
    {
        $tpldir_b4 = $this->cur_tpl_dir;

        /* Resolve ".", ".." and protect from possible LFI */
        $tpl = str_replace("\\", "/", $tpl);
        if ($tpl[0] != "/") {
            $tpl = $this->cur_tpl_dir . "/" . $tpl;
        }
        $pathex = array_filter(explode("/", $tpl), function ($s) {
            return $s != "." && !empty($s);
        });
        $pathex = array_merge($pathex);
        while (($i = array_search("..", $pathex)) !== false) {
            if ($i == 0) {
                $pathex = array_slice($pathex, 1);
            } else {
                $pathex = array_merge(array_slice($pathex, 0, $i), array_slice($pathex, $i + 2));
            }
        }
        $tpl = implode("/", $pathex);
        $this->cur_tpl_dir = dirname($tpl);

        if ($quiet) {
            $blocks_back     = $this->blocks;
            $blockorder_back = $this->blockorder;
        }

        $mode = StorageAccess::MODE_TRANSCOMPILED;
        $content = $this->storage_access->load($tpl, $mode);
        if ($mode == StorageAccess::MODE_SOURCE) {
            try {
                $ast     = Parser::parse($content, $tpl);
                $transc  = Transcompiler::transcompile($ast);
            } catch (ParseCompileError $e) {
                $e->rewrite($content);
                throw $e;
            }
            $this->storage_access->save($tpl, $transc, StorageAccess::MODE_TRANSCOMPILED);
            eval("\$content = $transc;");
        }

        $output = $content($this);

        $this->cur_tpl_dir = $tpldir_b4;

        if ($quiet) {
            $this->blocks     = $blocks_back;
            $this->blockorder = $blockorder_back;

            return null;
        } else {
            return $output;
        }
    }

    /*
     * Test, if a text represents false (an empty / only whitespace text) or true (everything else).
     *
     * @param string $txt The text to test.
     *
     * @return bool
     */
    public function evalbool($txt)
    {
        return trim(@(string)$txt) != "";
    }

    /**
     * Internal function for implementing tag content and custom tags.
     *
     * @param callable $fx
     * @return \Closure
     */
    public function make_closure($fx)
    {
        $bound_scope = $this->scope;
        return function () use ($bound_scope, $fx) {
            $args = func_get_args();
            $ste = $args[0];

            $prev = $ste->scope;
            $scope = $bound_scope->new_subscope();
            $ste->scope = $scope;

            try {
                $result = call_user_func_array($fx, $args);
                $ste->scope = $prev;
                return $result;
            } catch (Exception $e) {
                $ste->scope = $prev;
                throw $e;
            }
        };
    }
}
