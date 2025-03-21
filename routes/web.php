<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

//Route::post('livewire/message/{name}', [\Livewire\Controllers\HttpConnectionHandler::class, '__invoke'])->name('livewire.message')->middleware('subdomain');
Route::any('livewire/update', [\Livewire\Mechanisms\HandleRequests\HandleRequests::class, 'handleUpdate'])->name('livewire.update')->middleware('subdomain');
// Route::get('sample',function(){
//    dd(Route::current()->subdomain);
// });
/** Global urls ends */

//reporting broadcast url
Route::get('reporting-dashboard/{dashboard}/broadcast', \App\Http\Livewire\ReportingDashboard\Broadcast::class)->name('reporting-dashboard.broadcast');

/** Azure urls */
Route::group(['domain' => config('constants.azure_auth_domain')], function () {
    Route::get('/azure/login/redirect', [\App\Http\Controllers\Auth\AzureLoginController::class, 'attemptLogin'])->name('host.azure.redirect');
    Route::get('/azure/callback', [\App\Http\Controllers\Auth\AzureLoginController::class, 'callback'])->name('host.azure.callback');
    Route::get('/azure/pwa-login/redirect', [\App\Http\Controllers\Auth\PwaAzureLoginController::class, 'attemptLogin'])->name('host.azure_pwa.redirect');
    Route::get('/azure/pwa-callback', [\App\Http\Controllers\Auth\PwaAzureLoginController::class, 'callback'])->name('host.azure_pwa.callback');
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

        Route::get('peoplevox/{mode}/{ponumber}/{suffix?}', function (string $mode, string $ponumber) {

            $exitCode = Artisan::call('app:process-purchase-order-receipts', [
                '--mode' => Route::current()->parameter('mode'), '--po' => Route::current()->parameter('ponumber'), '--suffix' => Route::current()->parameter('suffix')
            ]);

            return Artisan::output();
        });
        
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

        Route::get('orders/email-templates', \App\Http\Livewire\Order\NotificationTemplate\Index::class)->name('order.email-template.index');
        Route::get('orders/email-templates/{template}/show', \App\Http\Livewire\Order\NotificationTemplate\Show::class)->name('order.email-template.show');

        Route::get('reporting', \App\Http\Livewire\Reporting\Index::class)->name('reporting.index');
        Route::get('reporting/{report}/show', \App\Http\Livewire\Reporting\Show::class)->name('reporting.show');

        Route::get('reporting-dashboard', \App\Http\Livewire\ReportingDashboard\Index::class)->name('reporting-dashboard.index');
        Route::get('reporting-dashboard/{dashboard}/show', \App\Http\Livewire\ReportingDashboard\Show::class)->name('reporting-dashboard.show');

        Route::get('pos', \App\Http\Livewire\POS\Index::class)->name('pos.index');
        //Route::get('orders/{order}/show', \App\Http\Livewire\Order\Show::class)->name('order.show');

        Route::get('products', \App\Http\Livewire\Product\Index::class)->name('products.index');

        Route::get('equipment/unavailable', \App\Http\Livewire\Equipment\Unavailable\Index::class)->name('equipment.unavailable.index');
        Route::get('equipment/unavailable/report', \App\Http\Livewire\Equipment\Unavailable\Report\Index::class)->name('equipment.unavailable.report.index');
        Route::get('equipment/unavailable/report/{report}/show', \App\Http\Livewire\Equipment\Unavailable\Report\Show::class)->name('equipment.unavailable.report.show');
        Route::get('equipment/unavailable/{unavailable_unit}/show', \App\Http\Livewire\Equipment\Unavailable\Show::class)->name('equipment.unavailable.show');
        Route::get('equipment/warranty', \App\Http\Livewire\Equipment\Warranty\Index::class)->name('equipment.warranty.index');
        Route::get('equipment/warranty/{warranty}/show', \App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Show::class)->name('equipment.warranty.show');
        Route::get('equipment/floor-model-inventory/', \App\Http\Livewire\Equipment\FloorModelInventory\Index::class)->name('equipment.floor-model-inventory.index');
        Route::get('equipment/floor-model-inventory/{floorModel}/show', \App\Http\Livewire\Equipment\FloorModelInventory\Inventory\Show::class)->name('equipment.floor-model-inventory.show');


        Route::get('marketing/sms-marketing/', \App\Http\Livewire\Marketing\SMSMarketing\Index::class)->name('marketing.sms-marketing.index');


        //pwa
        Route::prefix('fortis/app')->group(base_path('routes/web/pwa.php'));
        Route::get('sales-rep-override', \App\Http\Livewire\SalesRepOverride\Index::class)->name('sales-rep-override.index');
        Route::get('sales-rep-override/{salesRepOverride}/show', \App\Http\Livewire\SalesRepOverride\Show::class)->name('sales-rep-override.show');

        Route::get('scheduler', \App\Http\Livewire\Scheduler\ServiceArea\Index::class)->name('service-area.index');
        Route::get('scheduler/zones/{zone}/show', \App\Http\Livewire\Scheduler\ServiceArea\Zones\Show::class)->name('service-area.zones.show');
        Route::get('scheduler/zip-codes/{zipcode}/show', \App\Http\Livewire\Scheduler\ServiceArea\ZipCode\Show::class)->name('service-area.zipcode.show');

        Route::get('scheduler/schedule', \App\Http\Livewire\Scheduler\Schedule\Index::class)->name('schedule.calendar.index');
        Route::get('scheduler/schedule-list', \App\Http\Livewire\Scheduler\Schedule\ListIndex::class)->name('schedule.list.index');

        Route::get('scheduler/drivers', \App\Http\Livewire\Scheduler\Drivers\Index::class)->name('schedule.driver.index');
        Route::get('scheduler/drivers/{user}/show', \App\Http\Livewire\Scheduler\Drivers\Show::class)->name('schedule.driver.show');

        Route::get('scheduler/template', \App\Http\Livewire\Scheduler\NotificationTemplate\Index::class)->name('schedule.email-template.index');
        Route::get('scheduler/template/{template}/show', \App\Http\Livewire\Scheduler\NotificationTemplate\Show::class)->name('schedule.email-template.show');
        Route::get('scheduler/shift', \App\Http\Livewire\Scheduler\Shifts\Index::class)->name('schedule.shift.index');

        // Scheduler Trucks
        Route::get('scheduler/trucks', \App\Http\Livewire\Scheduler\Truck\Index::class)->name('scheduler.truck.index');
        Route::get('scheduler/trucks/{truck}/show', \App\Http\Livewire\Scheduler\Truck\Truck\Show::class)->name('scheduler.truck.show');

        Route::get('scheduler/cargo', \App\Http\Livewire\Scheduler\Truck\Cargo\Index::class)->name('scheduler.truck.cargo.index');
        Route::get('scheduler/cargo/{cargoConfigurator}/show', \App\Http\Livewire\Scheduler\Truck\Cargo\Show::class)->name('scheduler.truck.cargo.show');
    });

    //pwa
    Route::prefix('fortis/app')->group(base_path('routes/web/pwa.php'));
});

