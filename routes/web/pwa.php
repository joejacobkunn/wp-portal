<?php

use Illuminate\Support\Facades\Route;

Route::group([], function() {
    Route::get('/', \App\Http\Livewire\Pwa\Index::class)->name('pwa.index');
});