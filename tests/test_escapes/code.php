<?php

function test_func($ste) {
    $ste->register_tag("my_echo", function($ste, $params, $sub) {
        return $params["text"];
    });
}
