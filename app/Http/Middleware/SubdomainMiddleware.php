<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Core\Account;
use App\Models\Core\Affiliate;
use Illuminate\Support\Facades\App;
use App\Services\Environment\Domain;

class SubdomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($request->route_subdomain && !in_array($request->route_subdomain, config('constants.reserved_subdomain'))) {
            $subdomain = Account::select('id', 'name', 'admin_user', 'is_active')->where('subdomain', $request->route_subdomain)->firstOrFail();

            $domain = Domain::init($subdomain);
            App::instance('domain', $domain);
        }



        return $next($request);
    }
}
