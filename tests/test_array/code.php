<?php

function test_func($ste) {
    $ste->vars["foo"] = array(
        "a" => array(
            "blabla" => "OK"
        ),
        "b" => "bla"
    );
    $ste->vars["bar"] = array(
        "baz" => "a"
    );
}
