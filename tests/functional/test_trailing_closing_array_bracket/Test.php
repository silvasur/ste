<?php

namespace tests\functional\test_trailing_closing_array_bracket;

use r7r\ste\STECore;
use tests\functional\BaseTest;

class Test extends BaseTest
{
    protected function getDirectory(): string
    {
        return __DIR__;
    }

    protected function setUpSte(STECore $ste): void
    {
        $ste->set_var_by_name("foo", [
            "foo",
            "bar",
            "baz",
        ]);
    }
}
