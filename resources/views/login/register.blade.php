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
        
        <!-- Left Side: Form Register -->
        <div class="auth-form-col">
            <!-- Back to Login / Home -->
            <a href="{{ route('login') }}" class="auth-back-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Halaman Masuk</span>
            </a>

            <!-- Brand Header -->
            <div class="auth-brand-header">
                <h1 class="auth-title">Daftar Akun Baru</h1>
                <p class="auth-subtitle">Nikmati kemudahan memanggil teknisi profesional bergaransi</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="auth-alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form Register -->
            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                <input type="hidden" name="role" value="customer">

                <div class="form-field">
                    <label for="reg-name">Nama Lengkap</label>
                    <div class="input-wrap">
                        <i class="fa-regular fa-user input-icon"></i>
                        <input type="text" id="reg-name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
                    </div>
                </div>

                <div class="form-field">
                    <label for="reg-email">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="reg-email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="form-field">
                    <label for="reg-password">Kata Sandi</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="reg-password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                        <button type="button" class="btn-toggle-pwd" onclick="togglePassword('reg-password', this)" aria-label="Tampilkan Password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <span class="field-hint">Gunakan kombinasi huruf dan angka untuk keamanan optimal.</span>
                </div>

                <button type="submit" class="btn-auth-submit">
                    <span>Daftar Akun Sekarang</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="auth-footer-nav">
                <span>Sudah memiliki akun TEKSIKA?</span>
                <a href="{{ route('login') }}" class="auth-switch-link">Masuk ke Akun Anda</a>
            </div>
        </div>

        <!-- Right Side: Visual Showcase Banner -->
        <div class="auth-banner-col register-banner">
            <div class="banner-overlay"></div>
            <div class="banner-content">
                <h2 class="banner-headline">Keuntungan Bergabung Bersama TEKSIKA</h2>
                <p class="banner-sub">
                    Satu akun untuk seluruh kebutuhan perbaikan, pemeliharaan berkala, dan instalasi rumah Anda.
                </p>

                <div class="banner-perks-list">
                    <div class="b-perk-item">
                        <div class="b-perk-icon"><i class="fa-solid fa-user-check"></i></div>
                        <div>
                            <strong>Mitra Teknisi Terverifikasi</strong>
                            <p>Seluruh teknisi melewati seleksi keahlian dan standar keselamatan kerja.</p>
                        </div>
                    </div>
                    <div class="b-perk-item">
                        <div class="b-perk-icon"><i class="fa-solid fa-certificate"></i></div>
                        <div>
                            <strong>Garansi Resmi 30 Hari</strong>
                            <p>Jaminan pengerjaan ulang bebas biaya jika ada kendala pasca perbaikan.</p>
                        </div>
                    </div>
                    <div class="b-perk-item">
                        <div class="b-perk-icon"><i class="fa-solid fa-tags"></i></div>
                        <div>
                            <strong>Biaya Transparan di Awal</strong>
                            <p>Rincian pekerjaan dan biaya disepakati sebelum teknisi mulai bekerja.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
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
</script>
@endsection
