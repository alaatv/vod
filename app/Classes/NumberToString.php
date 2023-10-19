<?php

namespace App\Classes;

use Hekmatinasser\Notowo\Notowo;

class NumberToString extends Notowo
{

    public static function convertToRial($number, $lang = 'fa')
    {
        return 'ریال';
        return parent::parse($number, $lang)->getWord($number).' ریال';
    }
}
