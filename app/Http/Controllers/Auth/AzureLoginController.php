<?php

namespace App\Http\Controllers\Auth;

use App\Models\Core\User;
use App\Models\Core\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Microsoft\Graph\Graph;
use App\Models\Core\Role;



class AzureLoginController extends Controller
{
    public function attemptLogin(Request $request)
    {
        $hosturl = config('app.scheme') .'://' . config('constants.azure_auth_domain');
        if ($request->route_subdomain) {
            return redirect()->to($hosturl.route('host.azure.redirect', ['wp_domain' => $request->route_subdomain], false));
        }

        session(['azure.login.domain' => $request->wp_domain]);
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
            $queryParams['wp_name'] = base64_encode($azureData['user']->name);
            $queryParams['wp_title'] = base64_encode($azureData['user']->user['jobTitle']);
            $queryParams['wp_office_location'] = base64_encode($azureData['user']->user['officeLocation']);
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
        $user = User::where('email', $email);

        if (!empty($account)) {
            $user->where('account_id', $account->id);
        } else {
            $user->whereNull('account_id');
        }

        $user = $user->first();

        //return error if invalid master account
        if (!$user && empty($account)) {
            return $this->processFailedAuth($request, 'Error', 'Invalid Account');
        }

        //create account if not already exist in portal
        if (!$user && !empty($account)) {
            $user = new User();
            $user->name = base64_decode($request->wp_name);
            $user->email = $email;
            $user->office_location = base64_decode($request->wp_office_location);
            $user->title = base64_decode($request->wp_title);
            $user->is_active = 1;
            $user->account_id = $account->id ?? null;
            $user->abbreviation = $user->getAbbreviation();
            $user->save();

            $user->metadata()->create([
                'invited_by' => null,
            ]);

            //Default Role Assign
            $user->assignRole(Role::getDefaultRole());
        }

        //check if the user access is disabled
        if (empty($user->is_active)) {
            return $this->processFailedAuth($request, 'Error', 'Account is inactive, please contact administrator.');
        }

        Auth::guard('web')->login($user);

        //make sure to fetch latest oper id from ms graph when logging in
        $this->updateOperId($user);

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

    private function updateOperId($user)
    {
                //update oper id fromms graph

            $guzzle = new \GuzzleHttp\Client();
            $url = 'https://login.microsoftonline.com/' . config('services.azure.tenant') . '/oauth2/v2.0/token';
            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => config('services.azure.client_id'),
                    'client_secret' => config('services.azure.client_secret'),
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ],
            ])->getBody()->getContents());
            $accessToken = $token->access_token;

            $graph = new Graph();
            $graph->setAccessToken($accessToken);
            $response = $graph->createRequest('GET', '/users' . sprintf("('%s')", $user->email) . '?$select=employeeid')->execute()->getBody();

            if(isset($response['employeeId']) and !empty($response['employeeId'])){
                $user->update(['sx_operator_id' => $response['employeeId']]);
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
