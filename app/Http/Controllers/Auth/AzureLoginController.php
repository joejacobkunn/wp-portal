<?php

namespace App\Http\Controllers\Auth;

use App\Models\Core\User;
use App\Models\Core\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AzureLoginController extends Controller
{
    public function attemptLogin(Request $request)
    {
        $hosturl = config('app.url');
        if (str_contains($request->getHttpHost(), 'localhost')) {
            $hosturl = 'http://localhost';
        }

        if ($request->route_subdomain) {
            session(['azure.login.domain' => $request->route_subdomain]);
            return redirect()->to($hosturl . route('host.azure.redirect', [], false));
        }

        $url = Socialite::driver('azure')->redirect();
        $urlParts = parse_url($url->getTargetUrl());
        parse_str($urlParts['query'], $queryParams);
        $url->setTargetUrl($urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?' . http_build_query($queryParams));
        
        return $url;
    }

    public function callback(Request $request)
    {
        if ($request->route()->getName() == 'host.azure.callback') {
            $domain = session('azure.login.domain');
            $queryParams = $request->all();
            $request->request->add(['route_subdomain' => $domain]); 
            $azureData = $this->getAzureUser();
            if (empty($azureData['status'])) {
                return $this->processFailedAuth($request, $azureData['title'], $azureData['message']);
            }

            $queryParams = [];
            $queryParams['wp_domain'] = $domain;
            $token = bin2hex(random_bytes(60));
            $queryParams['wp_tk'] = base64_encode($token);
            $queryParams['wp_mail'] = base64_encode($azureData['user']->email);
            $queryParams['checksum'] = Hash::make($domain . $token);
            
            return redirect()->route('auth.azure.callback', [ 'route_subdomain' => $domain] + $queryParams);
        }

        $validateChecksum = Hash::check($request->wp_domain . base64_decode($request->wp_tk), $request->checksum);
        if (!$validateChecksum) {
            return $this->processFailedAuth($request, 'Error', 'Unauthorized');
        }

        $domain = $request->route_subdomain;
        if ($domain != config('constants.admin_subdomain')) {
            $account = Account::where('subdomain', $domain)->first();
        }

        $email = base64_decode($request->wp_mail);
        $user = User::active()->where('email', $email);

        if (!empty($account)) {
            $user->where('account_id', $account->id);
        } else {
            $user->whereNull('account_id');
        }

        $user = $user->first();
        
        if (!$user) {
            return $this->processFailedAuth($request, 'Error', 'Invalid Account');
        }

        Auth::guard('web')->login($user);

        return redirect()->route('core.dashboard.index');
    }

    public function getAzureUser()
    {
        try {
            $user = Socialite::driver('azure')->user();

            return [
                'status' => true,
                'user' => $user
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => false
            ];

            if ($e instanceof \Laravel\Socialite\Two\InvalidStateException) {
                $response['title'] = 'Session Expired';
                $response['message'] = 'Requst session has been expired, please try again!';
            } else {
                $response['title'] = 'Unauthorized';
                $response['message'] = $e->getMessage();
            }

            return $response;
        }
    }

    public function logout(Request $request)
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login.view');
    }


    public function processFailedAuth(Request $request, $title = 'Unauthorized Access', $message = null)
    {
        return redirect()->route('auth.login.view', ['route_subdomain' => $request->route_subdomain])->withErrors([
            'error_title' => $title,
            'message' => $message
        ]);
    }
    
}