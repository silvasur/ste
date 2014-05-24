<?php

// File: RuntimeError.php

// Namespace: kch42\ste
namespace kch42\ste;

/*
 * Class: RuntimeError
 * An Exception that a tag can throw, if a non-fatal runtime error occurred.
 * By default this will return in no output at all. But if <STECore::$mute_runtime_errors> is false, this will generate a error message instead of the tag's output.
 */
class RuntimeError      extends \Exception {}
