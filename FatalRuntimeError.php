<?php

namespace kch42\ste;

/*
 * Class: FatalRuntimeError
 * An Exception a tag can throw, if a fatal (irreparable) runtime error occurred.
 * This Exception will always "bubble up" so you probably want to catch them. Remember that this exception is also in the namespace ste!
 */
class FatalRuntimeError extends \Exception {}
