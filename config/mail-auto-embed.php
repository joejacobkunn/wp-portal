<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail auto embed
    |--------------------------------------------------------------------------
    |
    | If true, images will be automatically embedded.
    | If false, only images with the 'data-auto-embed' attribute will be embedded
    |
    */

    'enabled' => env('MAIL_AUTO_EMBED', false),

    /*
    |--------------------------------------------------------------------------
    | Mail embed method
    |--------------------------------------------------------------------------
    |
    | Supported: "attachment", "base64"
    |
    */

    'method' => env('MAIL_AUTO_EMBED_METHOD', 'attachment'),

    'curl' => [
        'connect_timeout' => 5, // Seconds
        'timeout' => 10, // Seconds
        'cache' => false,
        'cache_ttl' => 3600,
    ]
];
