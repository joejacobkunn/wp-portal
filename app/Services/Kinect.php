<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Kinect{

    private function headers()
    {
        return ['x-api-key' => config('kenect.key'), 'x-api-token' => config('kenect.token')];
    }

    public function send($to, $message)
    {
        //3442
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->post(config('kenect.endpoint').'/v2/conversations/messages', ['contactPhone' => $to, 'messageBody' => $message, 'locationId' => 3442]);

        return $response->body();

    }

    public function locations()
    {
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->get(config('kenect.endpoint').'/v1/locations');

        return $response->body();


    }


}