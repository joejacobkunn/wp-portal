<?php

namespace App\Http\Middleware;

use App\Models\Core\AccountAccessToken;
use App\Services\Environment\Domain;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class OauthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty($request->bearerToken())) {
            return response([
                'status' => 'Unauthorized! Bearer token is required to use this endpoint',
            ], 401);

        }

        $payload = $this->decodeToken($request->bearerToken());

        if (! empty($payload->uid)) {
            $accessKeyRecord = AccountAccessToken::with('account')->where('access_token', $payload->uid)->active()->first();
        }

        if (empty($accessKeyRecord->account)) {
            return response([
                'status' => 'Unauthorized! Please generate the right token using the auth endpoint',
            ], 401);
        }

        $domain = Domain::init($accessKeyRecord->account);
        App::instance('domain', $domain);

        return $next($request);
    }

    private function decodeToken($token)
    {
        try {
            return JWT::decode($token, new Key(file_get_contents(storage_path(config('auth.oauth.rsa.public'))), 'RS256'));
        } catch (\Exception) {
        }
    }
}
