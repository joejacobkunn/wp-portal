<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class Fortis
{
    private $developer_id;

    private $user_id;

    private $user_api_key;

    private $endpoint;

    public function __construct()
    {
        $this->developer_id = config('fortis.developer_id');

        $this->user_id = config('fortis.user_id');

        $this->user_api_key = config('fortis.user_api_key');

        $this->endpoint = config('fortis.endpoint');
    }

    private function headers()
    {
        return [
            'user-id' => $this->user_id,
            'user-api-key' => $this->user_api_key,
            'developer-id' => $this->developer_id
        ];
    }

    public function fetchLocations()
    {
        $response = Http::withHeaders($this->headers())
            ->acceptJson()
            ->get($this->endpoint.'/v1/locations');
        
            return $response->body();
    }

    public function fetchTerminals($location_id)
    {
        $response = Http::withHeaders($this->headers())
            ->acceptJson()
            ->get($this->endpoint.'/v1/terminals',['filter[location_id]' => $location_id]);
        
            return $response->body();
    }

}