<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PWA Defaults
    |--------------------------------------------------------------------------
    */

    'fortis' => [
        'name' => 'Fortis Assist',
        'manifest' => [
            'name' => 'Fortis Assist',
            'short_name' => 'Fortis Assist',
            'start_url' => '/',
            'background_color' => '#ffffff',
            'theme_color' => '#62a737',
            'display' => 'standalone',
            'orientation'=> 'portrait',
            'status_bar'=> 'white',
            'icons' => [
                [
                    "purpose" => "maskable",
                    "sizes" => "512x512",
                    "src" => "/assets/icons/icon_maskable.png",
                    "type" => "image/png",
                ],
                [
                    "purpose" => "any",
                    "src" => "/assets/icons/icon_maskable.png",
                    "sizes" => "512x512",
                    "type" => "image/png",
                ]
            ],
            'custom' => []
        ]
    ],
];
