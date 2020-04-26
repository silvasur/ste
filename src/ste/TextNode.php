<?php

namespace kch42\ste;

class TextNode extends ASTNode {
    public $text;
    public function __construct($tpl, $off, $text = "") {
        parent::__construct($tpl, $off);
        $this->text = $text;
    }
}