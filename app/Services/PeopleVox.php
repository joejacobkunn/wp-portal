<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PeopleVox
{
    protected $payload;

    public function __construct()
    {
    }

    public function sync($webhook)
    {
        $this->payload = $webhook->payload;
        Log::channel('webhook')->info($webhook->payload);
    }


}
