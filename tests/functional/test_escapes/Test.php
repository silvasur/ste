<?php

namespace tests\functional\test_escapes;

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
        $ste->register_tag("my_echo", function ($ste, $params, $sub) {
            return $params["text"];
        });
    }
}
