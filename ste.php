<?php

namespace ste;

/* This file is for backwards compatibility only. Use an autoloader and the \r7r\ste namespace in new applications instead! */

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

class ASTNode extends \r7r\ste\ASTNode
{
}
class TagNode extends \r7r\ste\TagNode
{
}
class TextNode extends \r7r\ste\TextNode
{
}
class VariableNode extends \r7r\ste\VariableNode
{
}
class ParseCompileError extends \r7r\ste\ParseCompileError
{
}
class RuntimeError extends \r7r\ste\RuntimeError
{
}
class FatalRuntimeError extends \r7r\ste\FatalRuntimeError
{
}
class StorageAccessFailure extends \r7r\ste\StorageAccessFailure
{
}
class CantLoadTemplate extends \r7r\ste\CantLoadTemplate
{
}
class CantSaveTemplate extends \r7r\ste\CantSaveTemplate
{
}
class FilesystemStorageAccess extends \r7r\ste\FilesystemStorageAccess
{
}
class Parser extends \r7r\ste\Parser
{
}
class Transcompiler extends \r7r\ste\Transcompiler
{
}
class STECore extends \r7r\ste\STECore
{
}

interface StorageAccess extends \r7r\ste\StorageAccess
{
}

/* We also put the storage mode constants here (they were outside of the interface before for some reason I can't remember...) */

const MODE_SOURCE = \r7r\ste\StorageAccess::MODE_SOURCE;
const MODE_TRANSCOMPILED = \r7r\ste\StorageAccess::MODE_TRANSCOMPILED;
