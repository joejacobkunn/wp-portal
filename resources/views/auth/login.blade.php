@extends('layouts.auth')

@section('title', 'Login | W&P Portal')

@section('left_column')
<div>

    @if(session('message'))
    <div class="alert alert-success" role="alert">
        <p class="mb-0">{{ session('message') }}</p>
    </div>
    @endif

    <ul class="text-danger">
        @foreach ($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
    </ul>

    <div class="d-grid gap-2">
        <a href="{{ route('auth.azure.login', array_filter(['ref' => request('ref') ])) }}" class="btn btn-outline-primary btn-lg mt-2 d-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-microsoft"
                viewBox="0 0 16 16">
                <path
                    d="M7.462 0H0v7.19h7.462V0zM16 0H8.538v7.19H16V0zM7.462 8.211H0V16h7.462V8.211zm8.538 0H8.538V16H16V8.211z" />
            </svg>
            Login using Azure/AD
        </a>
    </div>

</div>
@endsection