<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Traits\AzureAuthTrait;
use App\Http\Controllers\Controller;

class PwaAzureLoginController extends Controller
{
    use AzureAuthTrait;

    public $routeLogin = 'pwa.login.view';

    public $routeGlobalCallback = 'host.azure_pwa.callback';

    public $routeGlobalRedirect = 'host.azure_pwa.redirect';

    public $routeCallback = 'pwa.azure.callback';

    public $routeSuccess = 'pwa.index';

    public $routeFailure = 'pwa.login.view';
    
}
