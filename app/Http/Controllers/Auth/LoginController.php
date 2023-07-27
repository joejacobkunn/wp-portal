<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function attemptLogin()
    {
        if (Auth::check()) {
            return redirect()->route('core.dashboard.index');
        }

        return view('auth.login');
    }
}
