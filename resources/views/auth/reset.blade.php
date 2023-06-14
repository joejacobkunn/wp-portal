@extends('layouts.auth')

@php
    $title = $title ?? 'Reset Password';
@endphp

@section('title', $title . ' | W&P Portal')

@section('left_column')
<div>
    <h4 class="auth-title">{{ $title }}.</h4>

    <ul class="text-danger">
            @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    <form method="POST" action="/reset">
        @csrf
        <input type="hidden" name="code" value="{{ $code }}" />
        <div class="form-group position-relative has-icon-left mb-4">
            <input type="password" name="password" class="form-control form-control-xl" placeholder="Password">
            <div class="form-control-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
        </div>
        <div class="form-group position-relative has-icon-left mb-4">
            <input type="password" name="password_confirmation" class="form-control form-control-xl" placeholder="Confirm Password">
            <div class="form-control-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
        </div>
        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">UPDATE</button>
    </form>
</div>
@endsection
