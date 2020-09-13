<?php

namespace kch42\ste;

class Misc
{
    /**
     * @param string $text
     * @return string
     */
    public static function escape_text(string $text): string
    {
        return addcslashes($text, "\r\n\t\$\0..\x1f\\\"\x7f..\xff");
    }
}
