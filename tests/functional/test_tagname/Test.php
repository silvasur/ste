<?php

namespace tests\functional\test_tagname;

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
        $names = [
            "foo",
            "ab_cd",
            "foo123baz",
            "x0123",
        ];

        foreach ($names as $name) {
            $ste->register_tag(
                $name,
                function ($ste, $params, $sub) use ($name) {
                    return $name;
                }
            );
        }
    }
}
