<?php

use kch42\ste\STECore;

function test_func(STECore $ste)
{
    $ste->set_var_by_name("data", array(
        array('content' => 'foo', 'foo' => true),
        array('content' => 'bar', 'foo' => false),
        array('content' => 'baz', 'foo' => false),
    ));
}
