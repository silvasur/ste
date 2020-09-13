<?php

namespace tests\functional\test_array;

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
            "a" => [
                "blabla" => "OK"
            ],
            "b" => "bla"
        ];
        $ste->vars["bar"] = [
            "baz" => "a"
        ];
    }
}
