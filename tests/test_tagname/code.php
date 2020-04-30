<?php

use kch42\ste\STECore;

function test_func(STECore $ste)
{
    $names = array(
        "foo",
        "ab_cd",
        "foo123baz",
        "x0123",
    );

    foreach ($names as $name) {
        $ste->register_tag(
            $name,
            function ($ste, $params, $sub) use ($name) {
                return $name;
            }
        );
    }
}
