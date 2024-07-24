<?php
namespace App\Services;

use App\Contracts\SmsInterface;
use Illuminate\Support\Facades\Http;

class KenectSms implements SmsInterface {
    private function headers()
    {
        return ['x-api-key' => config('kenect.key'), 'x-api-token' => config('kenect.token')];
    }

    public function create($body)
    {
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->post(config('kenect.endpoint').'/v1/contacts', $body);

        return [
            'status' => $response->status(),
            'body' => json_decode($response->body())
        ];
    }

    public function getUser($params)
    {
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->get(config('kenect.endpoint').'/v1/contacts',
        $params);

        return [
            'status' => $response->status(),
            'body' => json_decode($response->body())
        ];
    }

    public function getMessages($params)
    {
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->get(config('kenect.endpoint').'/v1/conversations',
        $params);

        return [
            'status' => $response->status(),
            'body' => json_decode($response->body())
        ];
    }

    public function send($body)
    {
        $response = Http::acceptJson()
        ->withHeaders($this->headers())
        ->post(config('kenect.endpoint').'/v3/messages', $body);

        return [
            'status' => $response->status(),
            'body' => json_decode($response->body())
        ];
    }
}
