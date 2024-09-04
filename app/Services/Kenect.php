<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Kenect{

    private function headers()
    {
        return ['x-api-key' => config('kenect.key'), 'x-api-token' => config('kenect.token')];
    }

    public function send($to, $message, $locationId = null, $teamId = null)
    {
        if(!config('sx.mock'))
        {
            $to =  app()->environment('production') ? $to : '5863658884';
            
            $payload = [
                'contactPhone' => trim($to), 
                'messageBody' => trim(preg_replace('/\s+/', ' ', $message)),
                'locationId' => $locationId ?? config('kenect.location'), 
            ];

            if(!empty($teamId)) $payload['assignedTeamId'] = $teamId;

            $response = Http::acceptJson()
            ->withHeaders($this->headers())
            ->post(config('kenect.endpoint').'/v2/conversations/messages', $payload);

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

    public function teams($locationId)
    {
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->get(config('kenect.endpoint').'/v1/locations/'.$locationId.'/teams');

        return $response->body();

    }


}
