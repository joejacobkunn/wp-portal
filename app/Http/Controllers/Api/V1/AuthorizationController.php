<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Core\AccountAccessToken;
use App\Models\Core\AccountApiKey;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthorizationController extends Controller
{
    public function issueToken(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required',
            'client_secret' => 'required',
            'grant_type' => ['required', \Illuminate\Validation\Rule::in(['authorization_code'])],
        ]);

        $reqCount = RateLimiter::tooManyAttempts($request->client_id, config('auth.oauth.throttle.count'), function () {
        });
        if ($reqCount) {
            return response()->json([
                'error' => 'Too many attempts',
            ], 429);
        }

        $client = AccountApiKey::where('client_key', $request->client_id)->first();
        if (! $client || ! Hash::check($request->client_secret, $client->client_secret)) {
            //rate limiter on invalid attempts
            RateLimiter::hit($request->client_id, config('auth.oauth.throttle.decay') * 60);

            return response()->json([
                'error' => 'Invalid client credentials',
            ], 401);
        }

        //clear invalid attempt hits
        RateLimiter::clear($request->client_id);

        //rate limiter on key creations
        RateLimiter::hit($request->client_id.'_key', config('auth.oauth.expiry') * 60);
        $keyGenerationCount = RateLimiter::tooManyAttempts($request->client_id.'_key', 10, function () {
        });
        if ($keyGenerationCount) {
            return response()->json([
                'error' => 'Too many attempts',
            ], 429);
        }

        $accountAccess = AccountAccessToken::create([
            'account_id' => $client->account_id,
            'access_token' => Str::random(60),
            'expires_at' => Carbon::now()->addMinutes(config('auth.oauth.expiry')),
        ]);

        return response()->json([
            'access_token' => $this->prepareBearerToken($accountAccess->access_token),
            'expiry' => $accountAccess->expires_at->timestamp,
        ]);
    }

    protected function prepareBearerToken($token)
    {
        $payload = [
            'iss' => config('app.domain'),
            'aud' => config('app.domain'),
            'iat' => time(),
            'nbf' => time(),
            'uid' => $token,
        ];

        return JWT::encode(
            $payload,
            openssl_pkey_get_private(file_get_contents(storage_path(config('auth.oauth.rsa.private')))),
            'RS256'
        );
    }
}
