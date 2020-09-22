<?php


namespace tests\functional\test_user_tag_returns_nothing;

use tests\functional\BaseTest;
use r7r\ste\STECore;

class Test extends BaseTest
{
    protected function setUpSte(STECore $ste): void
    {
        $ste->register_tag('foobar', function ($ste, $params, $sub) {
            // nop
        });
    }

    protected function getDirectory(): string
    {
        return __DIR__;
    }
}
