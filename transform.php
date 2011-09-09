<?php

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
		throw new Exception("Missing closing \"$closing\". Stop.");
	
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
		throw new Exception("Missing closing '>' in \"" . $tag->name . "\"-Tag. Stop. (:::CODE START:::$code:::CODE END:::)");
	
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
			throw new Exception("Missing closing \"ste:" . $tag->name . "\"-Tag. Stop.");
		
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
define("MODE_AST", 1);
define("MODE_TRANSCOMPILED", 2);

/*interface StorageAccess
{
	abstract public function get_load_options();
	abstract public function get_save_options();
	
	abstract public function load($tpl);
	abstract public function save($tpl, $data, $mode);
}

class FilesystemStorageAccess implements StorageAccess
{
	protected $sourcedir;
	protected $astdir;
	protected $transcompileddir;
	
	protected $load_options;
	protected $save_options;
	
	public function __construct($src, $ast, $transc)
	{
		$this->sourcedir        = $src;
		$this->astdir           = $ast;
		$this->transcompileddir = $transc;
		
		if(!empty($this->sourcedir))
		{
			if(is_readable($this->sourcedir))
				$this->load_options[] = MODE_SOURCE;
			if(is_writeableable($this->sourcedir))
				$this->save_options[] = MODE_SOURCE;
		}
		
		if(!empty($this->astdir))
		{
			if(is_readable($this->astdir))
				$this->load_options[] = MODE_AST;
			if(is_writeableable($this->astdir))
				$this->save_options[] = MODE_AST;
		}
		
		if(!empty($this->transcompileddir))
		{
			if(is_readable($this->transcompileddir))
				$this->load_options[] = MODE_TRANSCOMPILED;
			if(is_writeableable($this->transcompileddir))
				$this->save_options[] = MODE_TRANSCOMPILED;
		}
		
		if(empty($this->save_options) and empty($this->load_options))
			throw new Exception("No dir read-/writeable!");
	}
	
	public function get_load_options() { return $this->load_options; }
	public function get_save_options() { return $this->save_options; }
	
	public function load($tpl)
	{
		if(@stat($this->sourcedir)
	}
}*/

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
		"+" => array()
	);
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
			throw new Exception("Transcompile error: Missing <ste:else> in <ste:if>. Stop.");
		
		$output .= transcompile($condition);
		$output .= "\$outputstack_i--;\nif(\$ste->bool(array_pop(\$outputstack)))\n{\n";
		$output .= indent_code(transcompile($then));
		$output .= "}\n";
		if(!empty($else))
		{
			$output .= "else\n{\n";
			$output .= indent_code(transcompile($else));
			$output .= "}\n";
		}
		return $output;
	},
	"cmp" => function($ast)
	{
		
	},
	"not" => function($ast)
	{
		
	},
	"even" => function($ast)
	{
		
	},
	"for" => function($ast)
	{
		if(empty($ast->params["start"]))
			throw new Exception("Transcompile error: Missing 'start' parameter in <ste:for>. Stop.");
		if(empty($ast->params["end"]))
			throw new Exception("Transcompile error: Missing 'end' parameter in <ste:for>. Stop.");
		
	},
	"foreach" => function($ast)
	{
	
	},
	"infloop" => function($ast)
	{
	
	},
	"break" => function($ast)
	{
		return "break;\n";
	}
	"continue" => function($ast)
	{
		return "continue\n";
	}
	"block" => function($ast)
	{
	
	},
	"load" => function($ast)
	{
	
	},
	"mktag" => function($ast)
	{
	
	},
	"set" => function($ast)
	{
	
	},
	"calc" => function($ast)
	{
	
	}
);

function transcompile($ast)
{
	
}

?>
