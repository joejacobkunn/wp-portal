<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function attemptLogin(Request $request)
    {
        $intendedPath = $request->ref ? base64_decode($request->ref) : route('core.dashboard.index');

        if (Auth::check()) {
            return redirect()->to($intendedPath);
        }

        return view('auth.login');
    }
}
