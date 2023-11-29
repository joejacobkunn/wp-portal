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


/** Global urls */

Route::post('livewire/message/{name}', [\Livewire\Controllers\HttpConnectionHandler::class, '__invoke'])->name('livewire.message')->middleware('subdomain');

/** Global urls ends */

//reporting broadcast url
Route::get('reporting-dashboard/{dashboard}/broadcast', \App\Http\Livewire\ReportingDashboard\Broadcast::class)->name('reporting-dashboard.broadcast');

/** Azure urls */
Route::group(['domain' => config('constants.azure_auth_domain')], function () {
    Route::get('/azure/login/redirect', [\App\Http\Controllers\Auth\AzureLoginController::class, 'attemptLogin'])->name('host.azure.redirect');
    Route::get('/azure/callback', [\App\Http\Controllers\Auth\AzureLoginController::class, 'callback'])->name('host.azure.callback');
});

/** Azure urls ends */

Route::group(['domain' => '{route_subdomain}.'.config('app.domain'), 'middleware' => 'subdomain'], function () {
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'attemptLogin'])->name('auth.login.view');
    Route::get('/azure/login', [\App\Http\Controllers\Auth\AzureLoginController::class, 'attemptLogin'])->name('auth.azure.login');
    Route::get('/azure/response', [\App\Http\Controllers\Auth\AzureLoginController::class, 'callback'])->name('auth.azure.callback');
    Route::get('logout', [\App\Http\Controllers\Auth\AzureLoginController::class, 'logout'])->name('auth.login.logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('core.dashboard.index');
        });

        Route::mediaLibrary();

        Route::get('dashboard', \App\Http\Livewire\Dashboard\Index::class)->name('core.dashboard.index');

        Route::get('users', \App\Http\Livewire\Core\User\Index::class)->name('core.user.index');
        Route::get('users/authentication-log', \App\Http\Livewire\Core\User\AuthenticationLog::class)->name('core.user.authentication');
        Route::get('users/{user}/show', \App\Http\Livewire\Core\User\Show::class)->name('core.user.show');

        Route::get('customers', \App\Http\Livewire\Core\Customer\Index::class)->name('core.customer.index');
        Route::get('customers/{customer}/show', \App\Http\Livewire\Core\Customer\Show::class)->name('core.customer.show');

        Route::get('roles', \App\Http\Livewire\Core\Role\Index::class)->name('core.role.index');
        Route::get('roles/{role}/show', \App\Http\Livewire\Core\Role\Show::class)->name('core.role.show');

        Route::get('accounts', \App\Http\Livewire\Core\Account\Index::class)->name('core.account.index');
        Route::get('accounts/{account}/show', \App\Http\Livewire\Core\Account\Show::class)->name('core.account.show');

        Route::get('vehicles', \App\Http\Livewire\Vehicle\Vehicle\Index::class)->name('vehicle.index');
        Route::get('vehicles/{vehicle}/show', \App\Http\Livewire\Vehicle\Vehicle\Show::class)->name('vehicle.show');

        Route::get('orders', \App\Http\Livewire\Order\Index::class)->name('order.index');
        Route::get('orders/{order}/show', \App\Http\Livewire\Order\Show::class)->name('order.show');

        Route::get('reporting', \App\Http\Livewire\Reporting\Index::class)->name('reporting.index');
        Route::get('reporting/{report}/show', \App\Http\Livewire\Reporting\Show::class)->name('reporting.show');

        Route::get('reporting-dashboard', \App\Http\Livewire\ReportingDashboard\Index::class)->name('reporting-dashboard.index');
        Route::get('reporting-dashboard/{dashboard}/show', \App\Http\Livewire\ReportingDashboard\Show::class)->name('reporting-dashboard.show');

        Route::get('pos', \App\Http\Livewire\POS\Index::class)->name('pos.index');
        //Route::get('orders/{order}/show', \App\Http\Livewire\Order\Show::class)->name('order.show');

        Route::get('products', \App\Http\Livewire\Product\Index::class)->name('products.index');


    });
});

