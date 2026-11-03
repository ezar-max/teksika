@extends('layouts.app')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/menu_login.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        
        <!-- Left Side: Form -->
        <div class="auth-form-col">
            <!-- Back to Home -->
            <a href="{{ route('landingpageuser') }}" class="auth-back-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>

            <!-- Brand Header -->
            <div class="auth-brand-header">
                <h1 class="auth-title">Selamat Datang Kembali</h1>
                <p class="auth-subtitle">Masuk ke akun TEKSIKA Anda untuk mengelola perbaikan</p>
            </div>

            @php
                $activeTab = session('active_tab') ?? (old('role') === 'admin' ? 'admin' : (request('tab') === 'admin' ? 'admin' : 'customer'));
            @endphp

            <!-- Alerts -->
            @if(session('login_error') == 'wrong')
                <div class="auth-alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Email atau password yang Anda masukkan salah.</span>
                </div>
            @endif

            @if(session('login_error') == 'notfound')
                <div class="auth-alert alert-error">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>Akun belum terdaftar. Silakan periksa kembali email Anda atau daftar terlebih dahulu.</span>
                </div>
            @endif

            @if(session('login_error') == 'is_customer')
                <div class="auth-alert alert-error">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Akun ini terdaftar sebagai Customer. Silakan masuk melalui tab Customer.</span>
                </div>
            @endif

            @if(session('login_error') == 'unauthorized_admin')
                <div class="auth-alert alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Anda tidak memiliki hak akses administrator.</span>
                </div>
            @endif

            @if(session('login_error') == 'auth_required')
                <div class="auth-alert alert-error">
                    <i class="fa-solid fa-lock"></i>
                    <span>Sesi Anda telah berakhir atau silakan login terlebih dahulu untuk mengakses halaman.</span>
                </div>
            @endif

            @if(session('success'))
                <div id="success-alert" class="auth-alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Role Selector Tabs -->
            <div class="auth-role-tabs">
                <button type="button" class="tab-btn {{ $activeTab === 'customer' ? 'active' : '' }}" id="tabCustomerBtn" onclick="switchTab('customer')">
                    <i class="fa-solid fa-user"></i>
                    <span>Customer</span>
                </button>
                <button type="button" class="tab-btn {{ $activeTab === 'admin' ? 'active' : '' }}" id="tabAdminBtn" onclick="switchTab('admin')">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Admin</span>
                </button>
            </div>

            <!-- Form Customer -->
            <div id="customerTab" class="tab-pane {{ $activeTab === 'customer' ? 'active' : '' }}">
                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="role" value="customer">

                    <div class="form-field">
                        <label for="cust-email">Email / Username</label>
                        <div class="input-wrap">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" id="cust-email" name="email" value="{{ $activeTab === 'customer' ? old('email') : '' }}" placeholder="nama@email.com" required {{ $activeTab === 'customer' ? 'autofocus' : '' }}>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="cust-password">Kata Sandi</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="cust-password" name="password" placeholder="Masukkan kata sandi" required>
                            <button type="button" class="btn-toggle-pwd" onclick="togglePassword('cust-password', this)" aria-label="Tampilkan Password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth-submit">
                        <span>Masuk Sebagai Customer</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-footer-nav">
                    <span>Belum memiliki akun?</span>
                    <a href="{{ route('register') }}" class="auth-switch-link">Daftar Akun Baru</a>
                </div>
            </div>

            <!-- Form Admin -->
            <div id="adminTab" class="tab-pane {{ $activeTab === 'admin' ? 'active' : '' }}">
                <form method="POST" action="{{ route('admin.login') }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="role" value="admin">

                    <div class="form-field">
                        <label for="admin-email">Email Administrator</label>
                        <div class="input-wrap">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" id="admin-email" name="email" value="{{ $activeTab === 'admin' ? old('email') : '' }}" placeholder="admin@gmail.com" required {{ $activeTab === 'admin' ? 'autofocus' : '' }}>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="admin-password">Kata Sandi Admin</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="admin-password" name="password" placeholder="Masukkan kata sandi admin" required>
                            <button type="button" class="btn-toggle-pwd" onclick="togglePassword('admin-password', this)" aria-label="Tampilkan Password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth-submit btn-admin">
                        <span>Masuk Portal Admin</span>
                        <i class="fa-solid fa-shield-halved"></i>
                    </button>
                </form>

                <div class="auth-footer-nav">
                    <span class="admin-notice"><i class="fa-solid fa-lock"></i> Akses terbatas untuk pengelola sistem TEKSIKA</span>
                </div>
            </div>

        </div>

        <!-- Right Side: Visual Showcase Banner -->
        <div class="auth-banner-col">
            <div class="banner-overlay"></div>
            <div class="banner-content">
                <h2 class="banner-headline">Layanan Perbaikan Rumah Cepat, Aman &amp; Bergaransi</h2>
                <p class="banner-sub">
                  Teknisi tersertifikasi siap datang ke lokasi Anda untuk perbaikan instalasi listrik, perawatan AC, dan perbaikan pemipaan.
                </p>
            </div>
        </div>

    </div>
</div>

<script>
    function switchTab(role) {
        const custTab = document.getElementById('customerTab');
        const adminTab = document.getElementById('adminTab');
        const custBtn = document.getElementById('tabCustomerBtn');
        const adminBtn = document.getElementById('tabAdminBtn');

        if (role === 'customer') {
            custTab.classList.add('active');
            adminTab.classList.remove('active');
            custBtn.classList.add('active');
            adminBtn.classList.remove('active');
        } else {
            adminTab.classList.add('active');
            custTab.classList.remove('active');
            adminBtn.classList.add('active');
            custBtn.classList.remove('active');
        }
    }

    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Auto dismiss success alert
    setTimeout(function() {
        const el = document.getElementById('success-alert');
        if (el) {
            el.style.transition = 'opacity 0.4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }
    }, 3500);
</script>
@endsection
