<?php

// File: Parser.php

// Namespace: kch42\ste
namespace kch42\ste;

/*
 * Class: Parser
 * The class, where the parser lives in. Can not be constructed manually.
 * Use the static method parse.
 */
class Parser {
    private $text;
    private $name;
    private $off;
    private $len;

    const PARSE_SHORT = 1;
    const PARSE_TAG   = 2;

    const ESCAPES_DEFAULT = '$?~{}|\\';

    private function __construct($text, $name) {
        $this->text = $text;
        $this->name = $name;
        $this->off  = 0;
        $this->len  = mb_strlen($text);
    }

    private function next($n = 1) {
        if($n <= 0) {
            throw new \InvalidArgumentException("\$n must be > 0");
        }
        $c = mb_substr($this->text, $this->off, $n);
        $this->off = min($this->off + $n, $this->len);
        return $c;
    }

    private function eof() {
        return ($this->off == $this->len);
    }

    private function back($n = 1) {
        if($n <= 0) {
            throw new \InvalidArgumentException("\$n must be > 0");
        }
        $this->off = max($this->off - $n, 0);
    }

    private function search_off($needle) {
        return mb_strpos($this->text, $needle, $this->off);
    }

    private function search_multi($needles) {
        $oldoff = $this->off;

        $minoff = $this->len;
        $which = NULL;

        foreach($needles as $key => $needle) {
            if(($off = $this->search_off($needle)) === false) {
                continue;
            }

            if($off < $minoff) {
                $minoff = $off;
                $which = $key;
            }
        }

        $this->off = $minoff + (($which === NULL) ? 0 : mb_strlen((string) $needles[$which]));

        return array($which, $minoff, mb_substr($this->text, $oldoff, $minoff - $oldoff), $oldoff);
    }

    private function search($needle) {
        $oldoff = $this->off;

        $off = $this->search_off($needle);
        if($off === false) {
            $this->off = $this->len;
            return array(false, mb_substr($this->text, $oldoff), $oldoff);
        }

        $this->off = $off + mb_strlen($needle);
        return array($off, mb_substr($this->text, $oldoff, $off - $oldoff), $oldoff);
    }

    private function take_while($cb) {
        $s = "";
        while(($c = $this->next()) !== "") {
            if(!call_user_func($cb, $c)) {
                $this->back();
                return $s;
            }
            $s .= $c;
        }
        return $s;
    }

    private function skip_ws() {
        $this->take_while("ctype_space");
    }

    private function get_name() {
        $off = $this->off;
        $name = $this->take_while(function($c) { return ctype_alnum($c) || ($c == "_"); });
        if(mb_strlen($name) == 0) {
            throw new ParseCompileError("Expected a name (alphanumeric chars + '_', at least one char)", $this->name, $off);
        }
        return $name;
    }

    /*
     * Function: parse
     * Parses the input into an AST.
     *
     * You only need this function, if you want to manually trnascompile a template.
     *
     * Parameters:
     *  $text - The input code.
     *  $name - The name of the template.
     *
     * Returns:
     *  An array of <ASTNode> objects.
     *
     * Throws:
     *  <ParseCompileError>
     */
    public static function parse($text, $name) {
        $obj = new self($text, $name);
        $res = $obj->parse_text(
            self::ESCAPES_DEFAULT, /* Escapes */
            self::PARSE_SHORT | self::PARSE_TAG /* Flags */
        );
        return self::tidyup_ast($res[0]);
    }

    private static function tidyup_ast($ast) {
        $out = array();

        $prevtext = NULL;
        $first = true;

        foreach($ast as $node) {
            if($node instanceof TextNode) {
                if($prevtext === NULL) {
                    $prevtext = $node;
                } else {
                    $prevtext->text .= $node->text;
                }
            } else {
                if($prevtext !== NULL) {
                    if($first) {
                        $prevtext->text = ltrim($prevtext->text);
                    }
                    if($prevtext->text != "") {
                        $out[] = $prevtext;
                    }
                }
                $prevtext = NULL;
                $first = false;

                if($node instanceof TagNode) {
                    $node->sub = self::tidyup_ast($node->sub);
                    foreach($node->params as $k => &$v) {
                        $v = self::tidyup_ast($v);
                    }
                    unset($v);
                } else { /* VariableNode */
                    foreach($node->arrayfields as &$v) {
                        $v = self::tidyup_ast($v);
                    }
                    unset($v);
                }

                $out[] = $node;
            }
        }

        if($prevtext !== NULL) {
            if($first) {
                $prevtext->text = ltrim($prevtext->text);
            }
            if($prevtext->text != "") {
                $out[] = $prevtext;
            }
        }

        return $out;
    }

    private function parse_text($escapes, $flags, $breakon = NULL, $separator = NULL, $nullaction = NULL, $opentag = NULL, $openedat = -1) {
        $elems = array();
        $astlist = array();

        $needles = array(
            "commentopen" => "<ste:comment>",
            "rawopen" => "<ste:rawtext>",
            "escape" => '\\',
            "varcurlyopen" => '${',
            "var" => '$',
        );

        if($flags & self::PARSE_TAG) {
            $needles["tagopen"] = '<ste:';
            $needles["closetagopen"] = '</ste:';
        }
        if($flags & self::PARSE_SHORT) {
            $needles["shortifopen"] = '?{';
            $needles["shortcompopen"] = '~{';
        }

        if($separator !== NULL) {
            $needles["sep"] = $separator;
        }
        if($breakon !== NULL) {
            $needles["break"] = $breakon;
        }

        for(;;) {
            list($which, $off, $before, $offbefore) = $this->search_multi($needles);

            $astlist[] = new TextNode($this->name, $offbefore, $before);

            switch($which) {
            case NULL:
                if($nullaction === NULL) {
                    $elems[] = $astlist;
                    return $elems;
                } else {
                    call_user_func($nullaction);
                }
                break;
            case "commentopen":
                list($off, $before, $offbefore) = $this->search("</ste:comment>");
                if($off === false) {
                    throw new ParseCompileError("ste:comment was not closed", $this->name, $offbefore);
                }
                break;
            case "rawopen":
                $off_start = $off;
                list($off, $before, $offbefore) = $this->search("</ste:rawtext>");
                if($off === false) {
                    throw new ParseCompileError("ste:rawtext was not closed", $this->name, $off_start);
                }
                $astlist[] = new TextNode($this->name, $off_start, $before);
                break;
            case "tagopen":
                $astlist[] = $this->parse_tag($off);
                break;
            case "closetagopen":
                $off_start = $off;
                $name = $this->get_name();
                $this->skip_ws();
                $off = $this->off;
                if($this->next() != ">") {
                    throw new ParseCompileError("Expected '>' in closing ste-Tag", $this->name, $off);
                }

                if($opentag === NULL) {
                    throw new ParseCompileError("Found closing ste:$name tag, but no tag was opened", $this->name, $off_start);
                }
                if($opentag != $name) {
                    throw new ParseCompileError("Open ste:$opentag was not closed", $this->name, $openedat);
                }

                $elems[] = $astlist;
                return $elems;
            case "escape":
                $c = $this->next();
                if(mb_strpos($escapes, $c) !== false) {
                    $astlist[] = new TextNode($this->name, $off, $c);
                } else {
                    $astlist[] = new TextNode($this->name, $off, '\\');
                    $this->back();
                }
                break;
            case "shortifopen":
                $shortelems = $this->parse_short("?{", $off);
                if(count($shortelems) != 3) {
                    throw new ParseCompileError("A short if tag must have the form ?{..|..|..}", $this->name, $off);
                }

                list($cond, $then, $else) = $shortelems;
                $thentag = new TagNode($this->name, $off);
                $thentag->name = "then";
                $thentag->sub = $then;

                $elsetag = new TagNode($this->name, $off);
                $elsetag->name = "else";
                $elsetag->sub = $else;

                $iftag = new TagNode($this->name, $off);
                $iftag->name = "if";
                $iftag->sub = $cond;
                $iftag->sub[] = $thentag;
                $iftag->sub[] = $elsetag;

                $astlist[] = $iftag;
                break;
            case "shortcompopen":
                $shortelems = $this->parse_short("~{", $off);
                if(count($shortelems) != 3) {
                    throw new ParseCompileError("A short comparasion tag must have the form ~{..|..|..}", $this->name, $off);
                }

                // TODO: What will happen, if a tag was in one of the elements?
                list($a, $op, $b) = $shortelems;
                $cmptag = new TagNode($this->name, $off);
                $cmptag->name = "cmp";
                $cmptag->params["text_a"] = $a;
                $cmptag->params["op"] = $op;
                $cmptag->params["text_b"] = $b;

                $astlist[] = $cmptag;
                break;
            case "sep":
                $elems[] = $astlist;
                $astlist = array();
                break;
            case "varcurlyopen":
                $astlist[] = $this->parse_var($off, true);
                break;
            case "var":
                $astlist[] = $this->parse_var($off, false);
                break;
            case "break":
                $elems[] = $astlist;
                return $elems;
            }
        }

        $elems[] = $astlist;
        return $elems;
    }

    private function parse_short($shortname, $openedat) {
        $tplname = $this->name;

        return $this->parse_text(
            self::ESCAPES_DEFAULT, /* Escapes */
            self::PARSE_SHORT | self::PARSE_TAG, /* Flags */
            '}', /* Break on */
            '|', /* Separator */
            function() use ($shortname, $tplname, $openedat) { /* NULL action */
                throw new ParseCompileError("Unclosed $shortname", $tplname, $openedat);
            },
            NULL, /* Open tag */
            $openedat /* Opened at */
        );
    }

    private function parse_var($openedat, $curly) {
        $varnode = new VariableNode($this->name, $openedat);
        $varnode->name = $this->get_name();
        if(!$this->eof()) {
            $varnode->arrayfields = $this->parse_array();
        }

        if(($curly) && ($this->next() != "}")) {
            throw new ParseCompileError("Unclosed '\${'", $this->name, $openedat);
        }
        return $varnode;
    }

    private function parse_array() {
        $tplname = $this->name;

        $arrayfields = array();

        while($this->next() == "[") {
            $openedat = $this->off - 1;
            $res = $this->parse_text(
                self::ESCAPES_DEFAULT, /* Escapes */
                0, /* Flags */
                ']', /* Break on */
                NULL, /* Separator */
                function() use ($tplname, $openedat) { /* NULL action */
                    throw new ParseCompileError("Unclosed array access '[...]'", $tplname, $openedat);
                },
                NULL, /* Open tag */
                $openedat /* Opened at */
            );
            $arrayfields[] = $res[0];
        }

        $this->back();
        return $arrayfields;
    }

    private function parse_tag($openedat) {
        $tplname = $this->name;

        $this->skip_ws();
        $tag = new TagNode($this->name, $openedat);
        $name = $tag->name = $this->get_name();
        $tag->params = array();
        $tag->sub = array();

        for(;;) {
            $this->skip_ws();

            switch($this->next()) {
            case '/': /* Self-closing tag */
                $this->skip_ws();
                if($this->next() != '>') {
                    throw new ParseCompileError("Unclosed opening <ste: tag (expected >)", $this->name, $openedat);
                }

                return $tag;
            case '>':
                $sub = $this->parse_text(
                    self::ESCAPES_DEFAULT, /* Escapes */
                    self::PARSE_SHORT | self::PARSE_TAG, /* Flags */
                    NULL, /* Break on */
                    NULL, /* Separator */
                    function() use ($name, $tplname, $openedat) { /* NULL action */
                        throw new ParseCompileError("Open ste:$name tag was not closed", $tplname, $openedat);
                    },
                    $tag->name, /* Open tag */
                    $openedat /* Opened at */
                );
                $tag->sub = $sub[0];
                return $tag;
            default:
                $this->back();

                $param = $this->get_name();

                $this->skip_ws();
                if($this->next() != '=') {
                    throw new ParseCompileError("Expected '=' after tag parameter name", $this->name, $this->off - 1);
                }
                $this->skip_ws();

                $quot = $this->next();
                if(($quot != '"') && ($quot != "'")) {
                    throw new ParseCompileError("Expected ' or \" after '=' of tag parameter", $this->name, $this->off - 1);
                }

                $off = $this->off - 1;
                $paramval = $this->parse_text(
                    self::ESCAPES_DEFAULT . $quot, /* Escapes */
                    0, /* Flags */
                    $quot, /* Break on */
                    NULL, /* Separator */
                    function() use ($quot, $tplname, $off) { /* NULL action */
                        throw new ParseCompileError("Open tag parameter value ($quot) was not closed", $tplname, $off);
                    },
                    NULL, /* Open tag */
                    $off /* Opened at */
                );
                $tag->params[$param] = $paramval[0];
            }
        }
    }
}
