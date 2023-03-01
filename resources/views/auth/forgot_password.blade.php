@extends('layouts.auth')

@section('title', 'Forgot Password | Pathways')

@section('left_column')
<div>
    <h4 class="auth-title">Forgot Password.</h4>

    <ul class="text-danger">
            @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>

    @if(session('forgot_success'))
    <div class="alert alert-success">
        <p>We've sent a link in your email, please follow the instructions to reset your password!</p>
    </div>
    @endif

    <form method="POST" action="/forgot-password">
        @csrf
        <p>To reset your password type the full email address you use to sign in to pathways account.</p>
        <div class="form-group position-relative has-icon-left mb-0">
            <input type="email" name="email" class="form-control form-control-xl" placeholder="Email" autocomplete="off" required>
            <div class="form-control-icon">
                <i class="bi bi-envelope"></i>
            </div>
        </div>
        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-4">Submit</button>
    </form>

    <h5 class="mt-5 pt-2 text-center"><a href="{{ route('auth.login.view') }}"><i class="bi bi-arrow-left-circle"></i> Back to Login</a></h5>
</div>
@endsection
