<?php

function test_func($ste) {
    $ste->vars["foo"] = array(
        "a" => array("a" => 100, "b" => 200),
        "b" => array("a" => 1, "b" => 2),
        "c" => array("a" => 42, "b" => 1337)
    );
}
