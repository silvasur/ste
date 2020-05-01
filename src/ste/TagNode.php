<?php

namespace kch42\ste;

class TagNode extends ASTNode
{
    /** @var string */
    public $name;

    /** @var ASTNode[][] */
    public $params = array();

    /** @var ASTNode[] */
    public $sub = array();

    /**
     * @param string $tpl
     * @param int $off
     * @param string $name
     */
    public function __construct($tpl, $off, $name = "")
    {
        parent::__construct($tpl, $off);
        $this->name = $name;
    }
}
