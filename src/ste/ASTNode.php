<?php

namespace kch42\ste;

abstract class ASTNode
{
    public $tpl;
    public $offset;
    public function __construct($tpl, $off)
    {
        $this->tpl    = $tpl;
        $this->offset = $off;
    }
}
