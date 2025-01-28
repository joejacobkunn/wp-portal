<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {
    Route::post('oauth/authorize', [\App\Http\Controllers\Api\V1\AuthorizationController::class, 'issueToken'])->name('auth.token');

    Route::group(['middleware' => 'auth.api'], function () {

        //order apis
        Route::post('order/create', [\App\Http\Controllers\Api\V1\OrderController::class, 'create'])->name('order.create');
        Route::post('order/pending-payment', [\App\Http\Controllers\Api\V1\OrderController::class, 'pendingPayment'])->name('order.pending-payment');

        //fortis apis
        Route::post('fortis/get-user', [\App\Http\Controllers\Api\V1\FortisController::class, 'getUser'])->name('fortis.get-user');
        Route::post('fortis/create-contact', [\App\Http\Controllers\Api\V1\FortisController::class, 'createContact'])->name('fortis.create-contact');
        Route::post('fortis/list-terminals', [\App\Http\Controllers\Api\V1\FortisController::class, 'listTerminals'])->name('fortis.list-terminals');

        Route::get('vehicles', [\App\Http\Controllers\Api\V1\VehicleController::class, 'index'])->name('vehicle.list');
        Route::get('vehicles/{id}', [\App\Http\Controllers\Api\V1\VehicleController::class, 'show'])->name('vehicle.get');

    });
});
