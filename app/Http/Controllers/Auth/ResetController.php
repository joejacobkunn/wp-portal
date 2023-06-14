<?php

namespace App\Http\Controllers\Auth;

use App\Events\User\ForgotPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\Core\User;
use App\Models\Core\UserMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ResetController extends Controller
{
    public function showForgotPasswordPage()
    {
        return view('auth.forgot_password');
    }

    public function processForgotPasswordPage(Request $request)
    {
        $client = app('domain')->getClient();
        $user = User::where('email', $request->email)->where('account_id', $client->id)->first();

        if ($user && ((empty($client) && empty($user->account_id)) || (! empty($client) && $client->id == $user->account_id))) {
            //send notifications
            ForgotPassword::dispatch($user);
        }

        return redirect()->back()->with(['forgot_success' => true]);
    }

    public function showReset(Request $request)
    {
        $code = base64_decode($request->c);

        if (! $code) {
            abort(403);
        }

        $metadata = UserMetadata::where('user_token', $code)->firstOrFail();

        return view('auth.reset', ['title' => 'Set Password', 'code' => $request->c]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $metadata = UserMetadata::with('user')
            ->whereNotNull('user_token')
            ->where('user_token', base64_decode($request->code))
            ->firstOrFail();

        $metadata->user_token = null;
        $metadata->save();

        $metadata->user->password = $request->password;
        $metadata->user->save();

        Session::flash('message', 'Password reset successfully, login to continue..');

        return redirect()->route('auth.login.view')->with(['message' => 'Password reset successfully, please login to continue..']);
    }
}
