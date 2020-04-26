<?php

namespace kch42\ste;

class TagNode extends ASTNode {
    public $name;
    public $params = array();
    public $sub = array();
    public function __construct($tpl, $off, $name = "") {
        parent::__construct($tpl, $off);
        $this->name = $name;
    }
}
