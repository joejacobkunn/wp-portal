<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class PeopleVox
{
    protected $payload;

    public function __construct()
    {
    }

    public function sync($webhook)
    {
        $this->payload = $webhook->payload;

        Mail::raw($this->payload, function (Message $message) {
            $message->to('jkrefman@wandpmanagement.com')->subject('Webhook Response PeopleVox Receipt');
        });
    }


}
