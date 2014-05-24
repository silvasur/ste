<?php

namespace ste;

/* This file is for backwards compatibility only. Use an autoloader and the \kch42\ste namespace in new applications instead! */

require_once(__DIR__ . "/src/Misc.php");

require_once(__DIR__ . "/src/ASTNode.php");
require_once(__DIR__ . "/src/TagNode.php");
require_once(__DIR__ . "/src/TextNode.php");
require_once(__DIR__ . "/src/VariableNode.php");

require_once(__DIR__ . "/src/ParseCompileError.php");
require_once(__DIR__ . "/src/RuntimeError.php");
require_once(__DIR__ . "/src/FatalRuntimeError.php");

require_once(__DIR__ . "/src/BreakException.php");
require_once(__DIR__ . "/src/ContinueException.php");

require_once(__DIR__ . "/src/StorageAccessFailure.php");
require_once(__DIR__ . "/src/CantLoadTemplate.php");
require_once(__DIR__ . "/src/CantSaveTemplate.php");

require_once(__DIR__ . "/src/StorageAccess.php");
require_once(__DIR__ . "/src/FilesystemStorageAccess.php");

require_once(__DIR__ . "/src/Calc.php");

require_once(__DIR__ . "/src/Parser.php");
require_once(__DIR__ . "/src/Transcompiler.php");

require_once(__DIR__ . "/src/VarNotInScope.php");
require_once(__DIR__ . "/src/Scope.php");

require_once(__DIR__ . "/src/STEStandardLibrary.php");
require_once(__DIR__ . "/src/STECore.php");

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
