<?php

use kch42\ste\STECore;

function test_func(STECore $ste)
{
    $ste->set_var_by_name("foo", array(
        "foo",
        "bar",
        "baz",
    ));
}
