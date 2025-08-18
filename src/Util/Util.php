<?php

declare(strict_types=1);

namespace FocusNFe\Util;

class Util {
    public static function numbersOfString(string $str): string
    {
        return preg_replace("/[^0-9]/i", "", $str);
    }
}