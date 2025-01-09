<?php

return [

    'api_key' => env('GOOGLE_API_KEY'),
    'distance_matrix_endpoint' => 'https://maps.googleapis.com/maps/api/distancematrix/json',
    'validation_url' => 'https://addressvalidation.googleapis.com/v1:validateAddress?key='.env('GOOGLE_API_KEY')

];

