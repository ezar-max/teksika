@extends('layouts.app')

@section('content')
@if(session('login_error') == 'wrong')
    <div id="toast" class="toast-warning">Email atau password salah!</div>
@endif
@if(session('login_error') == 'notfound')
    <div id="toast" class="toast-warning">Akun belum terdaftar!</div>
@endif
@if(session('success'))
    <div id="success-alert" class="alert alert-success" style="background:#dcfce7;color:#166534;padding:10px 16px;border-radius:8px;margin-bottom:12px;">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(function(){
            var el = document.getElementById('success-alert');
            if(el) el.style.display = 'none';
        }, 3000);
    </script>
@endif

<div class="login-wrapper">
    <!-- Left -->
    <div class="login-left">
        <div class="logo">
            <img src="{{ asset('images/teksika.png') }}" alt="Logo TEKSIKA">
            <h2>TEKSIKA</h2>
            <p>Selamat datang kembali! Masuk ke akun Anda</p>
        </div>

        <div class="login-box">
            <div class="tab-menu">
                <button class="tab-link active" onclick="showTab('customer')">Customers</button>
                <button class="tab-link" onclick="showTab('admin')">Admin</button>
            </div>

            <!-- Customer -->
            <div id="customer" class="tab-content active">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="role" value="customer">
                    <div class="form-group">
                        <label>Username / Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">LOGIN</button>
                </form>
                <div class="register-link">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
                </div>
            </div>

            <!-- Admin -->
            <div id="admin" class="tab-content">
                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <input type="hidden" name="role" value="admin">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">LOGIN</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right -->
    <div class="login-right">
        <div class="overlay"></div>
        <div class="right-text">
            <h3>Temukan Tukang Terpercaya</h3>
            <p>Layanan perbaikan rumah yang cepat, aman, dan profesional.</p>
        </div>
    </div>
</div>


<!-- Link to external CSS -->
<link rel="stylesheet" href="{{ asset('css/menu_login.css') }}">

<script>
    function showTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
        document.getElementById(tab).classList.add('active');
        event.target.classList.add('active');
    }
</script>
@endsection
