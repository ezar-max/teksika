@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/menu_login.css') }}">
@endpush

@section('content')
<div class="logo">
        <img src="{{ asset('images/teksika.png') }}" alt="Logo TEKSIKA" width="200" height="200">
</div>
<div class="login-container" style="margin-top: 18px;">
    <div class="title">Daftar Akun Customer</div>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="hidden" name="role" value="customer">
        <div class="form-group">
            <input type="text" name="name" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email address" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-login">Sign Up</button>
    </form>
    <div class="register-link">
        Already have an account? <a href="{{ route('login') }}">Login</a>
    </div>
</div>
@endsection