<?php

namespace kch42\ste;

class TextNode extends ASTNode
{
    /** @var string */
    public $text;

    /**
     * @param string $tpl
     * @param int $off
     * @param string $text
     */
    public function __construct($tpl, $off, $text = "")
    {
        parent::__construct($tpl, $off);
        $this->text = $text;
    }
}
