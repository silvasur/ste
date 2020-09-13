<?php

namespace tests\functional\test_short_for_additional_attribs;

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
        $ste->set_var_by_name("data", [
            ['content' => 'foo', 'foo' => true],
            ['content' => 'bar', 'foo' => false],
            ['content' => 'baz', 'foo' => false],
        ]);
    }
}
