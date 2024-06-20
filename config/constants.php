<?php

return [
    'reserved_subdomain' => [
        'admin',
        'api',
        'azure',
    ],

    'admin_subdomain' => env('ADMIN_SUBDOMAIN', 'admin'),

    'default_logo_path' => env('APP_LOGO_PATH', '/assets/images/logo.png'),

    "asset_version" => env('ASSET_VERSION', uniqid()),

    'azure_auth_domain' => env('AZURE_AUTH_DOMAIN', null),

];
