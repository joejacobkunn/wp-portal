<?php

namespace App\Services;

use App\Contracts\DistanceInterface;
use Illuminate\Support\Facades\Http;

class LocationSearchService implements DistanceInterface{

    public function findDistance($origin, $destination)
    {
            $payload = [
                'destinations' => $destination,
                'origins' => $origin,
                'mode' => 'driving',
                'units' => 'imperial',
                'key' => config('google.api_key'),
            ];

            $response = Http::acceptJson()
            ->get(config('google.distance_matrix_endpoint'), $payload);

            $data = $response->json();
            return $response;
    }
}
