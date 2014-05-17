<?php

namespace ste;

/* This file is for backwards compatibility only. Use an autoloader and the \kch42\ste namespace in new applications instead! */

require_once(__DIR__ . "/Misc.php");

require_once(__DIR__ . "/ASTNode.php");
require_once(__DIR__ . "/TagNode.php");
require_once(__DIR__ . "/TextNode.php");
require_once(__DIR__ . "/VariableNode.php");

require_once(__DIR__ . "/ParseCompileError.php");
require_once(__DIR__ . "/RuntimeError.php");
require_once(__DIR__ . "/FatalRuntimeError.php");

require_once(__DIR__ . "/BreakException.php");
require_once(__DIR__ . "/ContinueException.php");

require_once(__DIR__ . "/StorageAccessFailure.php");
require_once(__DIR__ . "/CantLoadTemplate.php");
require_once(__DIR__ . "/CantSaveTemplate.php");

require_once(__DIR__ . "/StorageAccess.php");
require_once(__DIR__ . "/FilesystemStorageAccess.php");

require_once(__DIR__ . "/Calc.php");

require_once(__DIR__ . "/Parser.php");
require_once(__DIR__ . "/Transcompiler.php");

require_once(__DIR__ . "/STEStandardLibrary.php");
require_once(__DIR__ . "/STECore.php");

/* Providing "proxy classes", so old applications can continue using the ste namespace */

class ASTNode extends \kch42\ste\ASTNode {}
class TagNode extends \kch42\ste\TagNode {}
class TextNode extends \kch42\ste\TextNode {}
class VariableNode extends \kch42\ste\VariableNode {}
class ParseCompileError extends \kch42\ste\ParseCompileError {}
class RuntimeError extends \kch42\ste\RuntimeError {}
class FatalRuntimeError extends \kch42\ste\FatalRuntimeError {}
class StorageAccessFailure extends \kch42\ste\StorageAccessFailure {}
class CantLoadTemplate extends \kch42\ste\CantLoadTemplate {}
class CantSaveTemplate extends \kch42\ste\CantSaveTemplate {}
class FilesystemStorageAccess extends \kch42\ste\FilesystemStorageAccess {}
class Parser extends \kch42\ste\Parser {}
class Transcompiler extends \kch42\ste\Transcompiler {}
class STECore extends \kch42\ste\STECore {}

interface StorageAccess extends \kch42\ste\StorageAccess {}

/* We also put the storage mode constants here (they were outside of the interface before for some reason I can't remember...) */

const MODE_SOURCE = \kch42\ste\StorageAccess::MODE_SOURCE;
const MODE_TRANSCOMPILED = \kch42\ste\StorageAccess::MODE_TRANSCOMPILED;
