<?php

use Illuminate\Support\Facades\Route;

Route::group(['domain' => 'webhook.'.config('app.domain'), 'prefix' => 'v1', 'as' => 'webhook.'], function () {

    Route::webhooks('sx', 'sx');
    Route::webhooks('people-vox', 'people-vox');
});
