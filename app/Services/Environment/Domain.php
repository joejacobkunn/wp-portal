<?php

namespace App\Services\Environment;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Domain
{
    private $entity;

    private $logo;

    private function __construct($entity)
    {
        $this->entity = $entity;
    }

    public static function init(Model $entity)
    {
        return new self($entity);
    }

    public function getClientId()
    {
        $entity = $this->getClient();
        if (empty($entity->id)) {
            throw new Exception("Subdomain config error!");
        }

        return $entity->id;
    }

    public function getClient()
    {
        return $this->entity;
    }
}
