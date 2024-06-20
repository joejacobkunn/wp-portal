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

    public static function validJson($jsonString)
    {
        json_decode($jsonString);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
