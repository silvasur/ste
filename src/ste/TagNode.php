<?php

namespace r7r\ste;

class TagNode extends ASTNode
{
    /** @var string */
    public $name;

    /** @var ASTNode[][] */
    public $params = [];

    /** @var ASTNode[] */
    public $sub = [];

    /**
     * @param string $tpl
     * @param int $off
     * @param string $name
     * @param ASTNode[][] $params
     * @param ASTNode[] $sub
     */
    public function __construct(string $tpl, int $off, string $name = "", array $params = [], array $sub = [])
    {
        parent::__construct($tpl, $off);
        $this->name = $name;
        $this->params = $params;
        $this->sub = $sub;
    }
}
