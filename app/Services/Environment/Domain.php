<?php

namespace App\Services\Environment;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Domain
{
    private static $entity;

    private static $logo;

    public static function setEnvironment(Model $entity)
    {
        static::$entity = $entity;
    }

    public static function getClientId()
    {
        $entity = static::getClient();
        if (empty($entity->id)) {
            throw new Exception("Subdomain config error!");
        }

        return $entity->id;
    }

    public static function getClient()
    {
        return static::$entity;
    }
}
