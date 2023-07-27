<?php

namespace App\Services\Environment;

class Host
{
    private static $host;

    private static $domain;

    private static $subdomain;

    public static function setEnviroment()
    {
        static::$host = $_SERVER['HTTP_HOST'];

        $hostParts = explode('.', static::$host);

        if (count($hostParts) > 2) {
            static::$subdomain = $hostParts[0] ?? null;
            static::$domain = $hostParts[1] ?? null;
        } else {
            static::$domain = $hostParts[1] ?? null;
        }
    }

    public static function getDomain()
    {
        return static::$domain;
    }

    public static function getSubdomain()
    {
        return static::$subdomain;
    }
}
