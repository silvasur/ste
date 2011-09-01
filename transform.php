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
		while(preg_match("/\\<((?:\\s*)|(?:\\s*\\/\\s*))ste:([a-zA-Z0-9_]*)(?:\\s+(?:[a-zA-Z0-9_]+)=(?:(?:\"(?:.*?)(?<!\\\\)\")|(?:'(?:.*?)(?<!\\\\)')))*\\s*(?<!\\/)\\>/s", $code, $matches, PREG_OFFSET_CAPTURE, $off) > 0) /* RegEx from hell! Matches all non-selfclosing <ste:> Tags. Opening and closing ones. */
		{
			$closingtag = trim($matches[1][0]);
			if($closingtag[0] == "/")
				$tags_open--;
			else
				$tags_open++;
			$last_tag_start = $matches[0][1];
			$off = $last_tag_start + strlen($matches[0][0]);
			if($tags_open == 0)
				break;
		}
		
		if(($tags_open != 0) or ($tag->name != $matches[2][0]))
			throw new Exception("Missing closing \"ste:" + $tag->name + "\"-Tag. Stop.");
		
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

?>
