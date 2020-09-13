<?php


namespace tests\functional;

use kch42\ste\StorageAccess;

class TestStorage implements StorageAccess
{
    /** @var string */
    private $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function load($tpl, &$mode)
    {
        $mode = StorageAccess::MODE_SOURCE;
        return file_get_contents($this->dir . DIRECTORY_SEPARATOR . $tpl);
    }

    public function save($tpl, $data, $mode)
    {
        if ($mode != StorageAccess::MODE_TRANSCOMPILED) {
            return;
        }

        file_put_contents($this->dir . DIRECTORY_SEPARATOR . "$tpl.transc.php", $data);
    }
}
