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
            'theme_color' => '#8936FF',
            'display' => 'standalone',
            'orientation'=> 'portrait',
            'status_bar'=> 'white',
            'icons' => [
                [
                    "purpose" => "maskable",
                    "src" => "/assets/icons/icon512_maskable.png",
                    "sizes" => "512x512",
                    "type" => "image/png",
                ],
                [
                    "purpose" => "any",
                    "src" => "/assets/icons/icon512_rounded.png",
                    "sizes" => "512x512",
                    "type" => "image/png",
                ]
            ],
            'custom' => []
        ]
    ],
];
