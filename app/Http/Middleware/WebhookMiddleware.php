<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('Signature')) {
            return $next($request);
        }

        if (!$request->hasHeader('Signature')) {
            return $next($request);
        }

        return response([
            'status' => 'Unauthorized! Missing signature in header',
        ], 401);
    }
}
