<?php

return [

    'auth_endpoint' => env('SX_AUTH_ENDPOINT'),

    'endpoint' => env('SX_ENDPOINT'),

    'client_id' => env('SX_CLIENT_ID'),

    'client_secret' => env('SX_CLIENT_SECRET'),

    'username' => env('SX_USERNAME'),

    'password' => env('SX_PASSWORD'),

    'grant_type' => env('SX_GRANT_TYPE', 'password'),

    'mock' => env('SX_MOCK_API', false)

];
