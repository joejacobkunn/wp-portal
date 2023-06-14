<?php

namespace App\Http\Middleware;

use App\Models\Core\Account;
use App\Services\Environment\Domain;
use Closure;
use Illuminate\Support\Facades\App;

class SubdomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($request->route_subdomain && ! in_array($request->route_subdomain, config('constants.reserved_subdomain'))) {
            $subdomain = Account::select('id', 'name', 'admin_user', 'is_active')->where('subdomain', $request->route_subdomain)->firstOrFail();

            $domain = Domain::init($subdomain);
            App::instance('domain', $domain);
        }

        return $next($request);
    }
}
