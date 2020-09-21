<?php

namespace r7r\ste;

/**
 * An exception thrown by the parser or compiler
 */
class ParseCompileError extends \Exception
{
    public $msg;
    public $tpl;
    public $off;

    public function __construct($msg, $tpl, $offset, $code = 0, $previous = null)
    {
        $this->msg = $msg;
        $this->tpl = $tpl;
        $this->off = $offset;

        parent::__construct("$msg (Template $tpl, Offset $offset)", $code, $previous);
    }

    /**
     * Update the message to include a human readable offset.
     * @param string $code
     */
    public function rewrite(string $code): void
    {
        $line = substr_count(str_replace("\r\n", "\n", substr($code, 0, $this->off)), "\n") + 1;
        $this->message = "{$this->msg} (Template {$this->tpl}, Line $line)";
    }
}
