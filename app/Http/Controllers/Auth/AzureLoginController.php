<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Traits\AzureAuthTrait;
use App\Http\Controllers\Controller;

class AzureLoginController extends Controller
{
    use AzureAuthTrait;

    public $routeLogin = 'auth.login.view';

    public $routeGlobalCallback = 'host.azure.callback';

    public $routeGlobalRedirect = 'host.azure.redirect';

    public $routeCallback = 'auth.azure.callback';

    public $routeSuccess = 'core.dashboard.index';

    public $routeFailure = 'auth.login.view';
}
