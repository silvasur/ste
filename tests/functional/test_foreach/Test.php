<?php

namespace tests\functional\test_foreach;

use kch42\ste\STECore;
use tests\functional\BaseTest;

class Test extends BaseTest
{
    protected function getDirectory(): string
    {
        return __DIR__;
    }

    protected function setUpSte(STECore $ste): void
    {
        $ste->vars["foo"] = array(
            "a" => array("a" => 100, "b" => 200),
            "b" => array("a" => 1, "b" => 2),
            "c" => array("a" => 42, "b" => 1337)
        );
    }
}
