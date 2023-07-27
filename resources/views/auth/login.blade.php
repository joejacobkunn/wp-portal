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
        
    <div class="azure-login-div">
        <a href="{{ route('auth.azure.login') }}" class="azure-link mt-2 d-inline-block">
            <img src="/assets/images/logo/microsoft.png" />
            <span class="v-divider"></span>
            Login using Microsoft
        </a>
    </div>

</div>
@endsection
