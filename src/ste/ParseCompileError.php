<?php

namespace kch42\ste;

class ParseCompileError extends \Exception {
	public $msg;
	public $tpl;
	public $off;

	public function __construct($msg, $tpl, $offset, $code = 0, $previous = NULL) {
		$this->msg = $msg;
		$this->tpl = $tpl;
		$this->off = $offset;
		$this->message = "$msg (Template $tpl, Offset $offset)";
	}

	public function rewrite($code) {
		$line = substr_count(str_replace("\r\n", "\n", substr($code, 0, $this->off)), "\n") + 1;
		$this->message = "{$this->msg} (Template {$this->tpl}, Line $line)";
		$this->is_rewritten = true;
	}
}
