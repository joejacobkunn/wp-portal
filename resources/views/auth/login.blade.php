@extends('layouts.auth')

@section('title', 'Login | W&P Portal')

@section('left_column')
<div>

    @if(session('message'))
    <div class="alert alert-success" role="alert">
        <p class="mb-0">{{ session('message') }}</p>
    </div>
    @endif

    <h4 class="auth-title">Sign In</h4>

    <ul class="text-danger">
            @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    <form method="POST" action="/login">
        @csrf
        <div class="form-group position-relative has-icon-left mb-4">
            <input type="text" name="email" class="form-control form-control-xl" placeholder="Username">
            <div class="form-control-icon">
                <i class="bi bi-person"></i>
            </div>
        </div>
        <div class="form-group position-relative has-icon-left mb-4">
            <input type="password" name="password" class="form-control form-control-xl" placeholder="Password">
            <div class="form-control-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
        </div>
        <div class="form-check form-check-lg d-flex align-items-end">
            <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
            <label class="form-check-label text-gray-600" for="flexCheckDefault">
                Keep me logged in
            </label>
        </div>
        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log In</button>
    </form>
    <div class="text-center mt-5 text-lg fs-4">
        <p><a class="font-bold" href="{{ route('auth.forgot.show') }}">Forgot password?</a></p>
    </div>
</div>
@endsection
