<?php

/*
 * Very simple autoloader for ste. Will only load ste.
 * Should only be used for the examples and tests in this project.
 * Use a full-fledged PSR-3 autoloader (e.g. the composer loader) for production!
 */

function autoload_ste($cl) {
    $path = explode("\\", $cl);
    if(($path[0] == "kch42") && ($path[1] == "ste")) {
        require_once(__DIR__ . "/src/ste/" . $path[2] . ".php");
    }
}

spl_autoload_register("autoload_ste");
