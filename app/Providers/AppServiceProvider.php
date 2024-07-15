<?php

namespace App\Providers;

use App\Services\Environment\Host;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (! app()->runningInConsole()) {
            Host::setEnviroment();

            app('url')->defaults(['route_subdomain' => Host::getSubdomain()]);

            Validator::extend('ends_with_csv', function ($attribute, $value, $parameters, $validator) {
                return strtolower(substr($value->getClientOriginalName(), -4)) === '.csv';
            });
        }
    }
}
