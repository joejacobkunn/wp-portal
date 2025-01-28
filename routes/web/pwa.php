<?php

use Illuminate\Support\Facades\Route;


Route::get('login', [\App\Http\Controllers\Auth\PwaLoginController::class, 'attemptLogin'])->name('pwa.login.view');
Route::get('/azure/login', [\App\Http\Controllers\Auth\PwaAzureLoginController::class, 'attemptLogin'])->name('pwa.azure.login');
Route::get('/azure/response', [\App\Http\Controllers\Auth\PwaAzureLoginController::class, 'callback'])->name('pwa.azure.callback');
Route::get('logout', [\App\Http\Controllers\Auth\PwaAzureLoginController::class, 'logout'])->name('pwa.login.logout');

Route::get('manifest.json', 'App\Http\Controllers\Pwa\ManifestController@manifest')->name('pwa.manifest');

Route::middleware(['auth.pwa'])->group(function () {
    Route::get('/', \App\Http\Livewire\Pwa\Index::class)->name('pwa.index');
});