<?php

namespace App\Helpers;

class StringHelper
{
    public static function extractDigits($text)
    {
        preg_match_all('!\d+!', $text, $matches);
        
        $result = '';
        if (!empty($matches[0])) {
            $result = implode('', $matches[0]);
        }

        return $result;
    }
}
