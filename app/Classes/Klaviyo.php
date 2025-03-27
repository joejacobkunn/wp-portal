<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class Klaviyo
{
    private $api_token;

    private $end_point;

    public function __construct()
    {
        $this->api_token = config('klaviyo.token');

        $this->end_point = config('klaviyo.endpoint');
    }

    private function headers()
    {
        return [
            'Authorization' => 'Klaviyo-API-Key '.$this->api_token,
            'accept' => 'application/vnd.api+json',
            'revision' => '2025-01-15',
            'content-type' => 'application/vnd.api+json'
        ];
    }

    public function getProfiles($query)
    {
        $response = Http::withHeaders($this->headers())
        ->acceptJson()
        ->get($this->end_point.'/profiles',$query);

        return $response->body();
    }

    public function addToList($email,$listId)
    {

        $profile_data = [];
        $profile_data[] = [
            'type' => 'profile',
            'attributes' => [
                'subscriptions' => [
                    'email' => [
                        'marketing' => [
                            'consent' => 'SUBSCRIBED'
                        ]
                    ]
                ],
                'email' => $email,
            ]
            ];

        $payload = [
            'data' => [
                'type' => 'profile-subscription-bulk-create-job',
                'attributes' => [
                    'custom_source' => 'Portal => Scheduler App',
                    'profiles' => [
                        'data' => $profile_data
                        ]
                    ],
                    'relationships' => [
                        'list' => [
                            'data' => [
                                'type' => 'list',
                                'id' => $listId
                            ]
                        ]
                    ]
            ]
        ];

        //dd(json_encode($payload));


        $response = Http::withHeaders($this->headers())
                    ->post($this->end_point.'/profile-subscription-bulk-create-jobs',$payload);
        
        return $response->body();
    }


    private function split_name($name) {
        $name = trim(preg_replace('/[^A-Za-z0-9 ]/', '', $name));
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim( preg_replace('#'.preg_quote($last_name,'#').'#', '', $name ) );
        return array($first_name, $last_name);
    }
    




}