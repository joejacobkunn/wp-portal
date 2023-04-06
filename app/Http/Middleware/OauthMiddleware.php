<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Services\Environment\Domain;
use App\Models\Core\AccountAccessToken;
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
        $payload = $this->decodeToken($request->bearerToken());

        if (!empty($payload->uid)) {
            $accessKeyRecord = AccountAccessToken::with('account')->where('access_token', $payload->uid)->active()->first();
        }

        if (empty($accessKeyRecord->account)) {
            return response([
                'status' => 'Unauthenticated'
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
