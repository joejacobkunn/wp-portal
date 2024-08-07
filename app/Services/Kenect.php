<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Kenect{

    private function headers()
    {
        return ['x-api-key' => config('kenect.key'), 'x-api-token' => config('kenect.token')];
    }

    public function send($to, $message, $locationId = null)
    {
        if(!config('sx.mock'))
        {
            $to =  app()->environment('production') ? $to : '5863658884';
            $response = Http::acceptJson()
            ->withHeaders($this->headers())
            ->post(config('kenect.endpoint').'/v2/conversations/messages', ['contactPhone' => trim($to), 'messageBody' => trim(preg_replace('/\s+/', ' ', $message)), 'locationId' => $locationId ?? config('kenect.location')]);
    
            return $response->ok() ? 'success' : 'error';
        }

        $responses = ['success', 'error'];

        return $responses[array_rand($responses)];

    }

    public function locations()
    {
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->get(config('kenect.endpoint').'/v1/locations');

        return $response->body();


    }


}