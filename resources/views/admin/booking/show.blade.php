<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Booking {{ $booking->kode_booking ?? '' }} — Admin TEKSIKA</title>
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; }
        .main-container { padding: 24px 32px; max-width: 1200px; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; font-size: 0.9rem; margin-bottom: 20px; transition: color 0.2s; }
        .btn-back:hover { color: #007bff; }
        .info-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
        .meta-item { margin-bottom: 14px; }
        .meta-label { font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px; }
        .meta-value { font-size: 1rem; color: #0f172a; font-weight: 600; }
        .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; }
        .status-pill.menunggu { background: #fef3c7; color: #b45309; }
        .status-pill.diterima { background: #dcfce7; color: #15803d; }
        .status-pill.ditolak { background: #fee2e2; color: #b91c1c; }
        .photo-thumb { width: 90px; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <!-- Sidebar Admin -->
    <div class="sidebar" id="sidebar">
        <img src="{{ asset('images/logo-putih.png') }}" alt="Logo" class="sidebar-logo">

        <a href="{{ route('dashboard_admin') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="logo" class="icon" width="22" height="22">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('booking') }}" class="menu-item active">
            <img src="{{ asset('images/booking.png') }}" alt="logo" class="icon" width="22" height="22">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('transaksi') }}" class="menu-item">
            <img src="{{ asset('images/pembayaran.png') }}" alt="logo" class="icon" width="22" height="22">
            <span class="menu-text">Pembayaran</span>
        </a>
        <a href="{{ route('worker') }}" class="menu-item">
            <img src="{{ asset('images/worker.png') }}" alt="logo" class="icon" width="22" height="22">
            <span class="menu-text">Worker</span>
        </a>
        <a href="{{ route('admin.pricing.index') }}" class="menu-item">
            <i class="fa-solid fa-sliders" style="font-size:20px; width:28px; text-align:center;"></i>
            <span class="menu-text">Aturan Tarif</span>
        </a>
        <a href="{{ route('chat') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon" width="22" height="22">
            <span class="menu-text">Chat</span>
        </a>

        <div class="logout">
            <a href="{{ route('logout') }}" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main">
        <div class="header" style="background:#fff; padding:16px 32px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="menu-toggle" id="menuToggle" style="cursor:pointer; font-size:1.2rem;">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <h2 style="margin:0; font-size:1.25rem; font-weight:700;">Detail Booking & Rekomendasi</h2>
            </div>
            <a href="{{ route('booking') }}" class="btn-back" style="margin:0;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Booking
            </a>
        </div>

        <div class="main-container">

            @if(session('success'))
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600;">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Ringkasan Informasi Request -->
            <div class="info-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:20px; border-bottom:1px solid #f1f5f9; padding-bottom:16px;">
                    <div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <h2 style="margin:0; font-size:1.4rem; color:#0f172a;">{{ $booking->layanan }}</h2>
                            <span class="status-pill {{ $booking->status_admin === 'diterima' ? 'diterima' : ($booking->status_admin === 'ditolak' ? 'ditolak' : 'menunggu') }}">
                                <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i>
                                {{ $booking->status_admin === 'diterima' ? 'Dikonfirmasi (Aktif)' : ($booking->status_admin === 'ditolak' ? 'Ditolak' : 'Menunggu Penugasan') }}
                            </span>
                        </div>
                        <div style="color:#64748b; font-size:0.9rem; margin-top:4px;">
                            Kode Booking: <b>{{ $booking->kode_booking ?? 'BK-' . str_pad($booking->id_request, 3, '0', STR_PAD_LEFT) }}</b> &bull; Diajukan pada: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }}
                        </div>
                    </div>

                    <div style="text-align:right;">
                        <div style="font-size:0.8rem; color:#64748b; text-transform:uppercase; font-weight:600;">Total Estimasi Biaya</div>
                        <div style="font-size:1.6rem; font-weight:800; color:#007bff;">
                            Rp {{ number_format($booking->estimasi_total ?? $booking->jumlah_pembayaran ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <!-- Kolom 1: Data Pemesan & Lokasi -->
                    <div>
                        <div class="meta-item">
                            <div class="meta-label">Pemesan (Customer)</div>
                            <div class="meta-value">{{ $booking->user_name ?? 'User #' . $booking->id_user }} ({{ $booking->user_email ?? '-' }})</div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">Lokasi / Alamat Pemesan</div>
                            <div class="meta-value">{{ $booking->lokasi ?? '-' }}</div>
                            @if(!empty($booking->latitude) && !empty($booking->longitude))
                                <small style="color:#0284c7; font-weight:600;">
                                    <i class="fa-solid fa-location-crosshairs"></i> GPS: {{ $booking->latitude }}, {{ $booking->longitude }}
                                </small>
                            @endif
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">Tingkat Kesulitan & Urgensi</div>
                            <div class="meta-value">
                                Kesulitan: <b>{{ ucfirst($booking->kompleksitas ?? 'Sedang') }}</b> &bull; 
                                Urgensi: <b>{{ ucfirst($booking->urgensi ?? 'Normal') }}</b>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">Metode Pembayaran</div>
                            <div class="meta-value">{{ $booking->metode_pembayaran ?? 'COD' }}</div>
                        </div>
                    </div>

                    <!-- Kolom 2: Detail Pengerjaan & Rincian Algoritma -->
                    <div>
                        <div class="meta-item">
                            <div class="meta-label">Rincian Perhitungan Algoritma Harga & Waktu</div>
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; font-size:0.88rem;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                    <span style="color:#64748b;">Harga Dasar Layanan:</span>
                                    <b>Rp {{ number_format($booking->harga_layanan ?? 0, 0, ',', '.') }}</b>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                    <span style="color:#64748b;">Faktor Urgensi:</span>
                                    <b>x{{ number_format($booking->faktor_urgensi ?? 1.0, 2) }}</b>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                    <span style="color:#64748b;">Biaya Jarak ({{ $booking->jarak_km ?? 0 }} km):</span>
                                    <b>Rp {{ number_format($booking->biaya_jarak ?? 0, 0, ',', '.') }}</b>
                                </div>
                                <div style="display:flex; justify-content:space-between; padding-top:6px; border-top:1px dashed #cbd5e1; color:#0284c7;">
                                    <span>Estimasi Total Durasi:</span>
                                    <b>{{ $booking->estimasi_durasi_menit ?? '-' }} Menit</b>
                                </div>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">Tukang Ditugaskan Saat Ini</div>
                            <div class="meta-value" style="color:{{ $booking->worker_name ? '#15803d' : '#94a3b8' }};">
                                <i class="fa-solid fa-helmet-safety"></i> {{ $booking->worker_name ?? 'Belum ada tukang yang ditugaskan' }}
                                @if(!empty($booking->worker_phone)) ({{ $booking->worker_phone }}) @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Kerusakan -->
                <div style="margin-top:18px; padding-top:16px; border-top:1px solid #f1f5f9;">
                    <div class="meta-label">Deskripsi Masalah dari Pemesan</div>
                    <div style="background:#f8fafc; padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0; font-size:0.95rem; white-space:pre-wrap;">{{ $booking->detail ?? '-' }}</div>
                </div>

                <!-- Foto Kerusakan -->
                @if(!empty($booking->foto))
                    <div style="margin-top:18px;">
                        <div class="meta-label">Foto Kerusakan Terlampir</div>
                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:8px;">
                            @foreach(explode(',', $booking->foto) as $pic)
                                @if(!empty(trim($pic)))
                                    <a href="{{ asset('storage/' . trim($pic)) }}" target="_blank">
                                        <img src="{{ asset('storage/' . trim($pic)) }}" alt="Foto Kerusakan" class="photo-thumb">
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Panel Rekomendasi Tukang (Algoritma Haversine + Scoring) -->
            @include('admin.booking._rekomendasi')

        </div>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>

