<?php

namespace r7r\ste;

abstract class ASTNode
{
    /** @var string */
    public $tpl;

    /** @var int */
    public $offset;

    /**
     * @param string $tpl
     * @param int $off
     */
    public function __construct(string $tpl, int $off)
    {
        $this->tpl    = $tpl;
        $this->offset = $off;
    }
}
