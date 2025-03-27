<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class OpenAI
{
    private $api_key;

    private $end_point;

    public function __construct()
    {
        $this->api_key = config('openai.key');

        $this->end_point = config('openai.endpoint');
    }

    private function headers()
    {
        return [
            'Authorization' => 'Bearer '.$this->api_key,
            'content-type' => 'application/json'
        ];
    }

    public function generateImage($prompt,$size)
    {
        $data = [
            'prompt' => $prompt,
            'num_images' => 1,
            'size' => $size
        ];


        $response = Http::withHeaders($this->headers())
        ->post($this->end_point.'/images/generations',$data);

        return $response->body();
    }

    public function prompt($prompt)
    {
        $data = [
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => $prompt,
            'max_tokens' => 500,
        ];


        $response = Http::withHeaders($this->headers())
        ->post($this->end_point.'/completions',$data);

        return $response->body();

    }

    




}