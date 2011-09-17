<?php

namespace ste;

class TextNode
{
	public $text;
}

class TagNode
{
	public $name;
	public $params;
	public $sub;
}

class VariableNode
{
	public $name;
	public $arrayfields;
	public function transcompile()
	{
		$varaccess = '@$ste->vars[' . (is_numeric($this->name) ? $this->name : '\'' . escape_text($this->name) . '\''). ']';
		foreach($this->arrayfields as $af)
		{
			if((count($af) == 1) and ($af[0] instanceof TextNode) and is_numeric($af->text))
				$varaccess .= '[' . $af->text . ']';
			else
				$varaccess .= '[' . implode(".",
					array_map(
						function($node)
						{
							if($node instanceof TextNode)
								return "\"" . escape_text($node->text) . "\"";
							else if($node instanceof VariableNode)
								return $node->transcompile;
						}, $af
					)
				). ']';
		}
		return $varaccess;
	}
}

/* $text must start after the first opening bracket */
function find_closing_bracket($text, $opening, $closing)
{
	$counter = 1;
	$len = strlen($text);
	for($i = 0; $i < $len; ++$i)
	{
		switch($text[$i])
		{
			case $opening:
				++$counter;
				break;
			case $closing:
				--$counter;
				break;
		}
		if($counter == 0)
			break;
	}
	
	if($counter > 0)
		throw new \Exception("Missing closing \"$closing\". Stop.");
	
	return $i;
}

function unescape_text($text)
{
	$text = preg_replace("/(?:(?<!\\\\)\\\\\\\$)/s", "$", $text);
	return str_replace("\\\\", "\\", $text);
}

function tokenize_text($text)
{
	$tokens = array();
	/* Find next non-escaped $-char */
	if(preg_match("/(?:(?<!\\\\)\\$)/s", $text, $match, PREG_OFFSET_CAPTURE) == 0)
	{
		$node = new TextNode();
		$node->text = unescape_text($text);
		return (strlen($node->text) == 0) ? array() : array($node);
	}
	
	if($match[0][1] > 0)
	{
		$node = new TextNode();
		$node->text = unescape_text(substr($text, 0, $match[0][1]));
		$tokens[] = $node;
	}
	
	if($text[$match[0][1] + 1] == "{")
	{
		$varend = find_closing_bracket(substr($text, $match[0][1] + 2), "{", "}") + $match[0][1] + 2;
		return array_merge(
			$tokens,
			tokenize_text("\$" . substr($text, $match[0][1] + 2, ($varend - 1) - ($match[0][1] + 1))),
			tokenize_text(substr($text, $varend + 1))
		);
	}
	
	$text = substr($text, $match[0][1] + 1);
	if(preg_match("/^[a-zA-Z0-9_]+/s", $text, $match, PREG_OFFSET_CAPTURE) == 0)
	{
		$nexttokens = tokenize_text($text);
		if($nexttokens[0] instanceof Text)
			$nexttokens[0]->text = "\$" . $nexttokens[0]->text;
		else
		{
			$node = new TextNode();
			$node->text = "\$";
			$tokens[] = $node;
		}
		return array_merge($tokens, $nexttokens);
	}
	
	$node = new VariableNode();
	$node->name = $match[0][0];
	$node->arrayfields = array();
	
	$text = substr($text, $match[0][1] + strlen($match[0][0]));
	while(@$text[0] == "[")
	{
		$text = substr($text, 1);
		$fieldend = find_closing_bracket($text, "[", "]");
		$node->arrayfields[] = tokenize_text(substr($text, 0, $fieldend));
		$text = substr($text, $fieldend + 1);
	}
	
	$tokens[] = $node;
	
	return strlen($text) > 0 ? array_merge($tokens, tokenize_text($text)) : $tokens;
}

function mk_ast($code)
{
	$ast = array();
	
	if(preg_match("/\\<\\s*ste:([a-zA-Z0-9_]*)/s", $code, $matches, PREG_OFFSET_CAPTURE) == 0)
		return tokenize_text($code);
	
	$ast = tokenize_text(substr($code, 0, $matches[0][1]));
	
	$tag = new TagNode();
	$tag->name = $matches[1][0];
	
	$code = substr($code, $matches[0][1] + strlen($matches[0][0]));
	
	$tag->params = array();
	
	while(preg_match("/^\\s+([a-zA-Z0-9_]+)=((?:\"(?:.*?)(?<!\\\\)\")|(?:'(?:.*?)(?<!\\\\)'))/s", $code, $matches, PREG_OFFSET_CAPTURE) > 0)
	{
		$paramval = substr($code, $matches[2][1] + 1, strlen($matches[2][0]) - 2);
		$paramval = str_replace("\\\"", "\"", $paramval);
		$paramval = str_replace("\\'", "'", $paramval);
		$tag->params[$matches[1][0]] = tokenize_text($paramval);
		$code = substr($code, strlen($matches[0][0]));
	}
	
	if(preg_match("/^\\s*([\\/]?)\\s*\\>/s", $code, $matches) == 0)
		throw new \Exception("Missing closing '>' in \"" . $tag->name . "\"-Tag. Stop. (:::CODE START:::$code:::CODE END:::)");
	
	$code = substr($code, strlen($matches[0]));
	
	$tag->sub = array();
	
	if($matches[1][0] != "/")
	{
		$tags_open = 1;
		$off = 0;
		$last_tag_start = 0;
		while(preg_match("/\\<((?:\\s*)|(?:\\s*\\/\\s*))ste:([a-zA-Z0-9_]*)(?:\\s+(?:[a-zA-Z0-9_]+)=(?:(?:\"(?:.*?)(?<!\\\\)\")|(?:'(?:.*?)(?<!\\\\)')))*((?:\\s*)|(?:\\s*\\/\\s*))\\>/s", $code, $matches, PREG_OFFSET_CAPTURE, $off) > 0) /* RegEx from hell! Matches all  <ste:> Tags. Opening, closing and self-closing ones. */
		{
			if(trim($matches[3][0]) != "/")
			{
				$closingtag = trim($matches[1][0]);
				if($closingtag[0] == "/")
					$tags_open--;
				else
					$tags_open++;
			}
			$last_tag_start = $matches[0][1];
			$off = $last_tag_start + strlen($matches[0][0]);
			if($tags_open == 0)
				break;
		}
		
		if(($tags_open != 0) or ($tag->name != $matches[2][0]))
			throw new \Exception("Missing closing \"ste:" . $tag->name . "\"-Tag. Stop.");
		
		if($tag->name == "rawtext")
		{
			$tag = new TextNode();
			$tag->text = substr($code, 0, $last_tag_start);
		}
		else
			$tag->sub = mk_ast(substr($code, 0, $last_tag_start));
		$code = substr($code, $off);
	}
	
	$ast[] = $tag;
	return array_merge($ast, strlen($code) > 0 ? mk_ast($code) : array());
}

function parse($code)
{
	/* Precompiling... */
	$code = preg_replace("/\\<\\s*ste:comment\\s*\\>.*?\\<\\s*\\/\\s*ste:comment\\s*\\>/s", "", $code); /* Remove comments */
	$code = preg_replace( /* Transform short form of if-clause (?{cond|then|else}) to long form */
		"/(?:(?<!\\\\)\\?)(?:(?<!\\\\)\\{)(.*?)(?:(?<!\\\\)\\|)(.*?)(?:(?<!\\\\)\\|)(.*?)(?:(?<!\\\\)\\})/s",
		"<ste:if>\$1<ste:then>\$2</ste:then><ste:else>\$3</ste:else></ste:if>",
		$code
	);
	/* Unescape \? \{ \} \| */
	$code = preg_replace("/(?:(?<!\\\\)\\\\\\?)/s", "?", $code);
	$code = preg_replace("/(?:(?<!\\\\)\\\\\\{)/s", "{", $code);
	$code = preg_replace("/(?:(?<!\\\\)\\\\\\})/s", "}", $code);
	$code = preg_replace("/(?:(?<!\\\\)\\\\\\|)/s", "|", $code);
	
	/* Create abstract syntax tree */
	return mk_ast($code);
}

define("MODE_SOURCE", 0);
define("MODE_TRANSCOMPILED", 1);

interface StorageAccess
{
	public function load($tpl, &$mode);
	public function save($tpl, $data, $mode);
}

class FilesystemStorageAccess implements StorageAccess
{
	protected $sourcedir;
	protected $transcompileddir;
	
	public function __construct($src, $transc)
	{
		$this->sourcedir        = $src;
		$this->transcompileddir = $transc;
	}
	
	public function load($tpl, &$mode)
	{
		$src_fn    = $this->sourcedir        . "/" . $tpl;
		$transc_fn = $this->transcompileddir . "/" . $tpl . ".php";
		
		if($mode == MODE_SOURCE)
		{
			$content = @file_get_contents($src_fn);
			if($content === False)
				throw new \Exception("Template not found.");
			return $content;
		}
		
		$src_stat    = @stat($src_fn);
		$transc_stat = @stat($transc_fn);
		
		if(($src_stat === False) and ($transc_stat === False))
			throw new \Exception("Template not found.");
		else if($transc_stat === False)
		{
			$mode = MODE_SOURCE;
			return file_get_contents($src_fn);
		}
		else if($src_stat === False)
		{
			include($transc_fn);
			return $transcompile_fx;
		}
		else
		{
			if($src_stat["mtime"] > $transc_stat["mtime"])
			{
				$mode = MODE_SOURCE;
				return file_get_contents($src_fn);
			}
			else
			{
				include($transc_fn);
				return $transcompile_fx;
			}
		}
	}
	
	public function save($tpl, $data, $mode)
	{
		$fn = (($mode == MODE_SOURCE) ? $this->sourcedir : $this->transcompileddir) . "/" . $tpl . (($mode == MODE_TRANSCOMPILED) ? ".php" : "");
		@mkdir(dirname($fn), 0777, True);
		file_put_contents($fn, "<?php \$transcompile_fx = $data; ?>");
		chmod($fn, 0777); /* FIXME: Remove this line after debugging... */
	}
}

function indent_code($code)
{
	return implode(
		"\n",
		array_map(
			function($line) { return "\t$line"; },
			explode("\n", $code)
		)
	);
}

/* We could also just eval() the $infix_math code, but this is much cooler :-D (Parser inception) */
function shunting_yard($infix_math)
{
	$operators = array(
		"+" => array("l", 2),
		"-" => array("l", 2),
		"*" => array("l", 3),
		"/" => array("l", 3),
		"^" => array("r", 4),
		"_" => array("r", 5),
		"(" => array("", 0), 
		")" => array("", 0)
	);
	
	preg_match_all("/\s*(?:(?:[+\\-\\*\\/\\^\\(\\)])|(\\d*[\\.]?\\d*))\\s*/s", $infix_math, $tokens, PREG_PATTERN_ORDER);
	$tokens_raw = array_filter(array_map('trim', $tokens[0]), function($x) { return ($x === "0") or (!empty($x)); });
	$output_queue = array();
	$op_stack     = array();
	
	$lastpriority = NULL;
	/* Make - unary, if neccessary */
	$tokens = array();
	foreach($tokens_raw as $token)
	{
		$priority = isset($operators[$token]) ? $operators[$token][1] : -1;
		if(($token == "-") and (($lastpriority === NULL) or ($lastpriority >= 0)))
		{
			$priority = $operators["_"][1];
			$tokens[] = "_";
		}
		else
			$tokens[] = $token;
		$lastpriority = $priority;
	}
	
	while(!empty($tokens))
	{
		$token = array_shift($tokens);
		if(is_numeric($token))
			$output_queue[] = $token;
		else if($token == "(")
			$op_stack[] = $token;
		else if($token == ")")
		{
			$lbr_found = False;
			while(!empty($op_stack))
			{
				$op = array_pop($op_stack);
				if($op == "(")
				{
					$lbr_found = True;
					break;
				}
				$output_queue[] = $op;
			}
			if(!$lbr_found)
				throw new \Exception("Bracket mismatch.");
		}
		else if(!isset($operators[$token]))
			throw new \Exception("Invalid token ($token): Not a number, bracket or operator. Stop.");
		else
		{
			$priority = $operators[$token][1];
			if($operators[$token][0] == "l")
				while((!empty($op_stack)) and ($priority <= $operators[$op_stack[count($op_stack)-1]][1]))
					$output_queue[] = array_pop($op_stack);
			else
				while((!empty($op_stack)) and ($priority < $operators[$op_stack[count($op_stack)-1]][1]))
					$output_queue[] = array_pop($op_stack);
			$op_stack[] = $token;
		}
	}
	
	while(!empty($op_stack))
	{
		$op = array_pop($op_stack);
		if($op == "(")
			throw new \Exception("Bracket mismatch...");
		$output_queue[] = $op;
	}
	
	return $output_queue;
}

function pop2(&$array)
{
	$rv = array(array_pop($array), array_pop($array));
	if(array_search(NULL, $rv, True) !== False)
		throw new \Exception("Not enough numbers on stack. Invalid formula.");
	return $rv;
}

function calc_rpn($rpn)
{
	$stack = array();
	foreach($rpn as $token)
	{
		switch($token)
		{
			case "+":
				list($b, $a) = pop2($stack);
				$stack[] = $a + $b;
				break;
			case "-":
				list($b, $a) = pop2($stack);
				$stack[] = $a - $b;
				break;
			case "*":
				list($b, $a) = pop2($stack);
				$stack[] = $a * $b;
				break;
			case "/":
				list($b, $a) = pop2($stack);
				$stack[] = $a / $b;
				break;
			case "^":
				list($b, $a) = pop2($stack);
				$stack[] = pow($a, $b);
				break;
			case "_":
				$a = array_pop($stack);
				if($a === NULL)
					throw new \Exception("Not enough numbers on stack. Invalid formula.");
				$stack[] = -$a;
				break;
			default:
				$stack[] = $token;
				break;
		}
	}
	return array_pop($stack);
}

function loopbody($code)
{
	return "try\n{\n" . indent_code($code) . "\n}\ncatch(\ste\BreakException \$e) { break; }\ncatch(\ste\ContinueException \$e) { continue; }\n";
}

$ste_builtins = array(
	"if" => function($ast)
	{
		$output = "";
		$condition = array();
		$then = array();
		$else = array();
		
		foreach($ast->sub as $node)
		{
			if(($node instanceof TagNode) and ($node->name == "then"))
				$then = $node->sub;
			else if(($node instanceof TagNode) and ($node->name == "else"))
				$else = $node->sub;
			else
				$condition[] = $node;
		}
		
		if(empty($then))
			throw new \Exception("Transcompile error: Missing <ste:else> in <ste:if>. Stop.");
		
		$output .= "\$outputstack[] = \"\";\n\$outputstack_i++;\n";
		$output .= _transcompile($condition);
		$output .= "\$outputstack_i--;\nif(\$ste->evalbool(array_pop(\$outputstack)))\n{\n";
		$output .= indent_code(_transcompile($then));
		$output .= "\n}\n";
		if(!empty($else))
		{
			$output .= "else\n{\n";
			$output .= indent_code(_transcompile($else));
			$output .= "\n}\n";
		}
		return $output;
	},
	"cmp" => function($ast)
	{
		$code = "";
		
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		if(isset($ast->params["var_b"]))
		{
			$code .= _transcompile($ast->params["var_b"]);
			$b = '$ste->get_var_by_name(array_pop($outputstack))';
		}
		else if(isset($ast->params["text_b"]))
		{
			$code .= _transcompile($ast->params["text_b"]);
			$b = 'array_pop($outputstack)';
		}
		else
			throw new \Exception("Transcompile error: neiter var_b nor text_b set in <ste:cmp>. Stop.");
		
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		if(isset($ast->params["var_a"]))
		{
			$code .= _transcompile($ast->params["var_a"]);
			$a = '$ste->get_var_by_name(array_pop($outputstack))';
		}
		else if(isset($ast->params["text_a"]))
		{
			$code .= _transcompile($ast->params["text_a"]);
			$a = 'array_pop($outputstack)';
		}
		else
			throw new \Exception("Transcompile error: neiter var_a nor text_a set in <ste:cmp>. Stop.");
		
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		if(isset($ast->params["op"]))
			$code .= _transcompile($ast->params["op"]);
		else
			throw new \Exception("Transcompile error: op not given in <ste:cmp>. Stop.");
		
		$code .= "\$outputstack_i -= 3;\nswitch(trim(array_pop(\$outputstack)))\n{\n\t";
		$code .= implode("", array_map(
				function($op) use ($a,$b)
				{
					list($op_stetpl, $op_php) = $op;
					return "case '$op_stetpl':\n\t\$outputstack[\$outputstack_i] .= (($a) $op_php ($b)) ? 'yes' : '';\n\tbreak;\n\t";
				},
				array(
					array('eq', '=='),
					array('neq', '!='),
					array('lt', '<'),
					array('lte', '<='),
					array('gt', '>'),
					array('gte', '>=')
				)
			));
		$code .= "default: throw new \Exception('Runtime Error: Unknown operator in <ste:cmp>.');\n}\n";
		return $code;
	},
	"not" => function($ast)
	{
		$code = "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->sub);
		$code .= "\$outputstack_i--;\n\$outputstack[\$outputstack_i] .= (!\$ste->evalbool(array_pop(\$outputstack))) ? 'yes' : '';\n";
		return $code;
	},
	"even" => function($ast)
	{
		$code = "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->sub);
		$code .= "\$outputstack_i--;\n\$tmp_even = array_pop(\$outputstack);\n\$outputstack[\$outputstack_i] .= (is_numeric(\$tmp_even) and (\$tmp_even % 2 == 0)) ? 'yes' : '';\n";
		return $code;
	},
	"for" => function($ast)
	{
		$code = "";
		$loopname = "forloop_" . str_replace(".", "_", uniqid("",True));
		if(empty($ast->params["start"]))
			throw new \Exception("Transcompile error: Missing 'start' parameter in <ste:for>. Stop.");
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["start"]);
		$code .= "\$outputstack_i--;\n\$${loopname}_start = array_pop(\$outputstack);\n";
		
		if(empty($ast->params["stop"]))
			throw new \Exception("Transcompile error: Missing 'end' parameter in <ste:for>. Stop.");
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["stop"]);
		$code .= "\$outputstack_i--;\n\$${loopname}_stop = array_pop(\$outputstack);\n";
		
		if(empty($ast->params["step"]))
			$code .= "\$${loopname}_step = 1;\n";
		else
		{
			$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
			$code .= _transcompile($ast->params["step"]);
			$code .= "\$outputstack_i--;\n\$${loopname}_step = array_pop(\$outputstack);\n";
		}
		
		if(!empty($ast->params["counter"]))
		{
			$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
			$code .= _transcompile($ast->params["counter"]);
			$code .= "\$outputstack_i--;\n\$${loopname}_countername = array_pop(\$outputstack);\n";
		}
		
		$loopbody = empty($ast->params["counter"]) ? "" : "\$ste->set_var_by_name(\$${loopname}_countername, \$${loopname}_counter);\n";
		$loopbody .= _transcompile($ast->sub);
		$loopbody = indent_code("{\n" . loopbody(indent_code($loopbody)) . "\n}\n");
		
		$code .= "if(\$${loopname}_step == 0)\n\tthrow new \Exception('Runtime Error: step can not be 0 in <ste:for>. Stop.');\n";
		$code .= "if(\$${loopname}_step > 0)\n{\n";
		$code .= "\tfor(\$${loopname}_counter = \$${loopname}_start; \$${loopname}_counter <= \$${loopname}_stop; \$${loopname}_counter += \$${loopname}_step)\n";
		$code .= $loopbody;
		$code .= "\n}\nelse\n{\n";
		$code .= "\tfor(\$${loopname}_counter = \$${loopname}_start; \$${loopname}_counter >= \$${loopname}_stop; \$${loopname}_counter += \$${loopname}_step)\n";
		$code .= $loopbody;
		$code .= "\n}\n";
		
		return $code;
	},
	"foreach" => function($ast)
	{
		$loopname = "foreachloop_" . str_replace(".", "_", uniqid("",True));
		$code = "";
		
		if(empty($ast->params["array"]))
			throw new \Exception("Transcompile Error: array not givein in <ste:foreach>. Stop.");
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["array"]);
		$code .= "\$outputstack_i--;\n\$${loopname}_arrayvar = array_pop(\$outputstack);\n";
		
		if(empty($ast->params["value"]))
			throw new \Exception("Transcompile Error: value not givein in <ste:foreach>. Stop.");
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["value"]);
		$code .= "\$outputstack_i--;\n\$${loopname}_valuevar = array_pop(\$outputstack);\n";
		
		if(!empty($ast->params["key"]))
		{
			$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
			$code .= _transcompile($ast->params["key"]);
			$code .= "\$outputstack_i--;\n\$${loopname}_keyvar = array_pop(\$outputstack);\n";
		}
		
		if(!empty($ast->params["counter"]))
		{
			$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
			$code .= _transcompile($ast->params["counter"]);
			$code .= "\$outputstack_i--;\n\$${loopname}_countervar = array_pop(\$outputstack);\n";
		}
		
		$code .= "\$${loopname}_array = \$ste->get_var_by_name(\$${loopname}_arrayvar);\n";
		$code .= "if(!is_array(\$${loopname}_array))\n\t\$${loopname}_array = array();\n";
		$code .= "\$${loopname}_counter = 0;\n";
		
		$loopbody = "\$${loopname}_counter++;\n\$ste->set_var_by_name(\$${loopname}_valuevar, \$${loopname}_value);\n";
		if(!empty($ast->params["key"]))
			$loopbody .= "\$ste->set_var_by_name(\$${loopname}_keyvar, \$${loopname}_key);\n";
		if(!empty($ast->params["counter"]))
			$loopbody .= "\$ste->set_var_by_name(\$${loopname}_countervar, \$${loopname}_counter);\n";
		$loopbody .= "\n";
		$loopbody .= _transcompile($ast->sub);
		$loopbody = "{\n" . loopbody(indent_code($loopbody)) . "\n}\n";
		
		$code .= "foreach(\$${loopname}_array as \$${loopname}_key => \$${loopname}_value)\n$loopbody\n";
		
		return $code;
	},
	"infloop" => function($ast)
	{
		return "while(True)\n{\n" . loopbody(indent_code(_transcompile($ast->sub)) . "\n}") . "\n";
	},
	"break" => function($ast)
	{
		return "\$ste->break_loop();\n";
	},
	"continue" => function($ast)
	{
		return "\$ste->continue_loop();\n";
	},
	"block" => function($ast)
	{
		if(empty($ast->name))
			throw new \Exception("Transcompile Error: name missing in <ste:block>. Stop.");
		
		$blknamevar = "blockname_" . str_replace(".", "_", uniqid("", True));
		
		$code = "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["name"]);
		$code .= "\$outputstack_i--;\n\$${blknamevar} = array_pop(\$outputstack);\n";
		
		$tmpblk = uniqid("", True);
		$code .= "\$ste->blocks['$tmpblk'] = array_pop(\$outputstack);\n\$ste->blockorder[] = '$tmpblk';\n\$outputstack = array('');\n\$outputstack_i = 0;\n";
		
		$code .= _transcompile($ast->sub);
		
		$code .= "\$ste->blocks[\$${blknamevar}] = array_pop(\$outputstack);\n";
		$code .= "if(array_search(\$${blknamevar}, \$ste->blockorder) === FALSE)\n\t\$ste->blockorder[] = \$${blknamevar};\n\$outputstack = array('');\n\$outputstack_i = 0;\n";
		
		return $code;
	},
	"load" => function($ast)
	{
		if(empty($ast->params["name"]))
			throw new \Exception("Transcompile Error: name missing in <ste:load>. Stop.");
		
		$code = "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["name"]);
		$code .= "\$outputstack_i--;\n\$outputstack[\$outputstack_i] .= \$ste->load(array_pop(\$outputstack));\n";
		
		return $code;
	},
	"mktag" => function($ast)
	{
		if(empty($ast->params["name"]))
			throw new \Exception("Transcompile Error: name missing in <ste:mktag>. Stop.");
		
		$code = "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["name"]);
		$code .= "\$outputstack_i--;\n\$tagname = array_pop(\$outputstack);\n";
		
		$fxbody = "\$outputstack = array(); \$outputstack_i = 0;\$ste->vars['_tag_parameters'] = \$params;\n";
		
		if(!empty($ast->params["mandatory"]))
		{
			$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
			$code .= _transcompile($ast->params["mandatory"]);
			$code .= "\$outputstack_i--;\n\$mandatory_params = explode('|', array_pop(\$outputstack));\n";
			
			$fxbody .= "foreach(\$mandatory_params as \$mp)\n{\n\tif(!isset(\$params[\$mp]))\n\t\tthrow new \Exception(\"Runtime Error: \$mp missing in <ste:\$tagname>. Stop.\");\n}";
		}
		
		$fxbody .= _transcompile($ast->sub);
		$fxbody .= "return array_pop(\$outputstack);";
		
		$code .= "\$tag_fx = function(\$ste, \$params, \$sub) use (\$tagname, \$mandatory_params)\n{\n" . indent_code($fxbody) . "\n};\n";
		$code .= "\$ste->register_tag(\$tagname, \$tag_fx);\n";
		
		return $code;
	},
	"tagcontent" => function($ast)
	{
		return "\$outputstack[\$outputstack_i] .= \$sub(\$ste);";
	},
	"set" => function($ast)
	{
		if(empty($ast->params["var"]))
			throw new \Exception("Transcompile Error: var missing in <ste:set>. Stop.");
		
		$code = "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->params["var"]);
		
		$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->sub);
		
		$code .= "\$outputstack_i -= 2;\n\$newvartext = array_pop(\$outputstack);\n\$varname = array_pop(\$outputstack);\n";
		$code .= "\$ste->set_var_by_name(\$varname, \$newvartext);\n";
		
		return $code;
	},
	"calc" => function($ast)
	{
		$code = "\$outputstack[] = '';\n\$outputstack_i++;\n";
		$code .= _transcompile($ast->sub);
		$code .= "\$outputstack_i--;\n\$outputstack[\$outputstack_i] .= \$ste->calc(array_pop(\$outputstack));\n";
		
		return $code;
	}
);

function escape_text($text)
{
	return addcslashes($text, "\r\n\t\$\0..\x1f\\'\x7f..\xff");
}

function _transcompile($ast) /* The real transcompile function, does not add boilerplate code. */
{
	$code = "";
	global $ste_builtins;
	
	foreach($ast as $node)
	{
		if($node instanceof TextNode)
			$code .= "\$outputstack[\$outputstack_i] .= \"" . escape_text($node->text) . "\";\n";
		else if($node instanceof VariableNode)
			$code .= "\$outputstack[\$outputstack_i] .= " . $node->transcompile() . ";\n";
		else if($node instanceof TagNode)
		{
			if(isset($ste_builtins[$node->name]))
				$code .= $ste_builtins[$node->name]($node);
			else
			{
				$paramarray = "parameters_" . str_replace(".", "_", uniqid("", True));
				$code .= "\$$paramarray = array();\n";
				
				foreach($node->params as $pname => $pcontent)
				{
					$code .= "\$outputstack[] = '';\n\$outputstack_i++;\n";
					$code .= _transcompile($pcontent);
					$code .= "\$outputstack_i--;\n\$${paramarray}['" . escape_text($pname) . "'] = array_pop(\$outputstack);\n";
				}
				
				$code .= "\$outputstack[\$outputstack_i] .= \$ste->call_tag('" . escape_text($node->name) . "', \$${paramarray}, " . transcompile($node->sub) . ");\n";
			}
		}
	}
	
	return $code;
}

$ste_transc_boilerplate = "\$outputstack = array('');\n\$outputstack_i = 0;\n";

function transcompile($ast) /* Transcompile and add some boilerplate code. */
{
	global $ste_transc_boilerplate;
	return "function(\$ste)\n{\n" . indent_code($ste_transc_boilerplate . _transcompile($ast) . "return array_pop(\$outputstack);") . "\n}";
}

class BreakException    extends \Exception { }
class ContinueException extends \Exception { }

class STECore
{
	private $tags;
	private $storage_access;
	private $cur_tpl_dir;
	public $blocks;
	public $blockorder;
	public $vars;
	
	public function __construct($storage_access)
	{
		$this->storage_access = $storage_access;
		$this->cur_tpl_dir = "/";
		STEStandardLibrary::_register_lib($this);
		$this->vars = array();
		$this->blockorder = array();
		$this->blocks = array();
	}
	
	public function register_tag($name, $callback)
	{
		if(!is_callable($callback))
			throw new \Exception("Can not register tag \"$name\", not callable.");
		if(empty($name))
			throw new \Exception("Can not register tag, empty name.");
		$this->tags[$name] = $callback;
	}
	
	public function call_tag($name, $params, $sub)
	{
		if(!isset($this->tags[$name]))
			throw new \Exception("Can not call tag \"$name\": Does not exist.");
		return call_user_func($this->tags[$name], $this, $params, $sub);
	}
	
	public function break_loop()
	{
		throw new BreakException();
	}
	
	public function continue_loop()
	{
		throw new ContinueException();
	}
	
	public function calc($expression)
	{
		return calc_rpn(shunting_yard($expression));
	}
	
	public function exectemplate($tpl)
	{
		$output = "";
		$lastblock = $this->load($tpl);
		
		foreach($this->blockorder as $blockname)
			$output .= $this->blocks[$blockname];
		
		return $output . $lastblock;
	}
	
	private function &get_var_reference(&$from, $name, $create_if_not_exist)
	{
		$bracket_open = strpos($name, "[");
		if($bracket_open === False)
		{
			if(isset($from[$name]) or $create_if_not_exist)
			{
				$ref = &$from[$name];
				return $ref;
			}
			else
				return NULL;
		}
		else
		{
			$old_varname = $varname;
			$bracket_close = strpos($name, "]", $bracket_open);
			if($bracket_close === FALSE)
				throw new Excpeption("Runtime Error: Invalid varname \"$varname\". Missing closing \"]\".");
			$varname = substr($name, 0, $bracket_open);
			$name    = substr($name, $bracket_open + 1, $bracket_close - $bracket_open - 1) . substr($name, $bracket_close + 1);
			if(!is_array($from[$varname]))
			{
				if($create_if_not_exist)
					$from[$varname] = array();
				else
					return NULL;
			}
			try
			{
				$ref = &$self->get_var_reference($from[$varname], $name, $create_if_not_exist);
				return $ref;
			}
			catch(Exception $e)
			{
				throw new Excpeption("Runtime Error: Invalid varname \"$old_varname\". Missing closing \"]\".");
			}
		}
	}
	
	public function set_var_by_name($name, $val)
	{
		$ref = &$this->get_var_reference($this->vars, $name, True);
		$ref = $val;
	}
	
	public function get_var_by_name($name)
	{
		$ref = $this->get_var_reference($this->vars, $name, False);
		return $ref === NULL ? "" : $ref;
	}
	
	public function load($tpl, $quiet=False)
	{
		$tpldir_b4 = $this->cur_tpl_dir;
		
		/* Resolve ".", ".." and protect from possible LFI */
		$tpl = str_replace("\\", "/", $tpl);
		if($tpl[0] != "/")
			$tpl = $this->cur_tpl_dir . "/" . $tpl;
		$pathex = array_filter(array_slice(explode("/", $tpl), 1), function($s) { return ($s != ".") and (!empty($s)); });
		$pathex = array_merge($pathex);
		while(($i = array_search("..", $pathex)) !== False)
		{
			if($i == 0)
				$pathex = array_slice($pathex, 1);
			else
				$pathex = array_merge(array_slice($pathex, 0, $i), array_slice($pathex, $i + 2));
		}
		$tpl = implode("/", $pathex);
		
		if($quiet)
		{
			$blocks_back     = clone $this->blocks;
			$blockorder_back = clone $this->blockorder;
		}
		
		$mode = MODE_TRANSCOMPILED;
		$content = $this->storage_access->load($tpl, $mode);
		if($mode == MODE_SOURCE)
		{
			$ast    = parse($content);
			$transc = transcompile($ast);
			$this->storage_access->save($tpl, $transc, MODE_TRANSCOMPILED);
			eval("\$content = $transc;");
		}
		
		$output = $content($this);
		
		$this->cur_tpl_dir = $tpldir_b4;
		
		if($quiet)
		{
			$this->blocks     = $blocks_back;
			$this->blockorder = $blockorder_back;
		}
		else
			return $output;
	}
	
	public function evalbool($txt)
	{
		return trim($txt) != "";
	}
}

class STEStandardLibrary
{
	static public function _register_lib($ste)
	{
		foreach(get_class_methods(__CLASS__) as $method)
			if($method[0] != "_")
				$ste->register_tag($method, array(__CLASS__, $method));
	}
	
	static public function escape($ste, $params, $sub)
	{
		return htmlentities($sub($ste), ENT_QUOTES, "UTF-8");
	}
	
	static public function strlen($ste, $params, $sub)
	{
		return strlen($sub($ste));
	}
	
	static public function array_len($ste, $params, $sub)
	{
		if(empty($params["var"]))
			throw new \Exception("Runtime Error: missing var parameter in <ste:array_len>.");
		$a = $ste->get_var_by_name($params["var"]);
		return (is_array($a)) ? count($a) : "";
	}
}

?>
