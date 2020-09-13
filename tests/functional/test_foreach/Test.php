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
        $ste->vars["foo"] = [
            "a" => ["a" => 100, "b" => 200],
            "b" => ["a" => 1, "b" => 2],
            "c" => ["a" => 42, "b" => 1337]
        ];
    }
}
