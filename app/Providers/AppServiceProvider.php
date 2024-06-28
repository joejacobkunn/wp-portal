<?php

namespace App\Providers;

use App\Contracts\SmsInterface;
use App\Services\Environment\Host;
use App\Services\KenectSms;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsInterface::class, KenectSms::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (! app()->runningInConsole()) {
            Host::setEnviroment();

            app('url')->defaults(['route_subdomain' => Host::getSubdomain()]);
        }
    }
}
