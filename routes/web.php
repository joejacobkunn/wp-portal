<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['domain' => '{route_subdomain}.' . config('app.domain'), 'middleware' => 'subdomain'], function () {
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'attemptLogin'])->name('auth.login.view');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'authenticate'])->name('auth.login.attempt');
    Route::get('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('auth.login.logout');
    Route::get('set-password', [\App\Http\Controllers\Auth\ResetController::class, 'showReset'])->name('auth.password.show_reset');
    Route::get('forgot-password', [\App\Http\Controllers\Auth\ResetController::class, 'showForgotPasswordPage'])->name('auth.forgot.show');
    Route::post('forgot-password', [\App\Http\Controllers\Auth\ResetController::class, 'processForgotPasswordPage'])->name('auth.forgot.process');
    Route::post('reset', [\App\Http\Controllers\Auth\ResetController::class, 'reset'])->name('auth.password.reset');

    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('core.dashboard.index');
        });

        Route::get('dashboard', \App\Http\Livewire\Dashboard\Index::class)->name('core.dashboard.index');

        Route::get('users', \App\Http\Livewire\Core\User\Index::class)->name('core.user.index');
        Route::get('users/{user}/show', \App\Http\Livewire\Core\User\Show::class)->name('core.user.show');

        Route::get('roles', \App\Http\Livewire\Core\Role\Index::class)->name('core.role.index');
        Route::get('roles/{role}/show', \App\Http\Livewire\Core\Role\Show::class)->name('core.role.show');

        Route::get('accounts', \App\Http\Livewire\Core\Account\Index::class)->name('core.account.index');
        Route::get('accounts/{account}/show', \App\Http\Livewire\Core\Account\Show::class)->name('core.account.show');

        Route::get('vehicles', \App\Http\Livewire\Vehicle\Vehicle\Index::class)->name('vehicle.index');
        //Route::get('vehicles/{vehicle}/show', \App\Http\Livewire\Vehicle\Vehicle\Show::class)->name('vehicle.show');
    });
});
