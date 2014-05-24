<?php

namespace ste;

/* This file is for backwards compatibility only. Use an autoloader and the \kch42\ste namespace in new applications instead! */

require_once(__DIR__ . "/src/ste/Misc.php");

require_once(__DIR__ . "/src/ste/ASTNode.php");
require_once(__DIR__ . "/src/ste/TagNode.php");
require_once(__DIR__ . "/src/ste/TextNode.php");
require_once(__DIR__ . "/src/ste/VariableNode.php");

require_once(__DIR__ . "/src/ste/ParseCompileError.php");
require_once(__DIR__ . "/src/ste/RuntimeError.php");
require_once(__DIR__ . "/src/ste/FatalRuntimeError.php");

require_once(__DIR__ . "/src/ste/BreakException.php");
require_once(__DIR__ . "/src/ste/ContinueException.php");

require_once(__DIR__ . "/src/ste/StorageAccessFailure.php");
require_once(__DIR__ . "/src/ste/CantLoadTemplate.php");
require_once(__DIR__ . "/src/ste/CantSaveTemplate.php");

require_once(__DIR__ . "/src/ste/StorageAccess.php");
require_once(__DIR__ . "/src/ste/FilesystemStorageAccess.php");

require_once(__DIR__ . "/src/ste/Calc.php");

require_once(__DIR__ . "/src/ste/Parser.php");
require_once(__DIR__ . "/src/ste/Transcompiler.php");

require_once(__DIR__ . "/src/ste/VarNotInScope.php");
require_once(__DIR__ . "/src/ste/Scope.php");

require_once(__DIR__ . "/src/ste/STEStandardLibrary.php");
require_once(__DIR__ . "/src/ste/STECore.php");

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
