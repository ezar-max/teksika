<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Booking {{ $booking->kode_booking ?? '' }} — Admin TEKSIKA</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
        }

        .detail-container {
            padding: 28px 36px 60px 36px;
            max-width: 1240px;
            margin: 0 auto;
        }

        @media (max-width: 640px) {
            .detail-container {
                padding: 18px 14px 40px;
            }

            .detail-container .info-card {
                padding: 20px 16px;
            }
        }

        .header-back-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.88rem;
            transition: all 0.2s ease;
        }

        .header-back-pill:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-3px);
            color: #ffffff;
        }

        .info-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 26px 28px;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
            transition: all 0.2s ease;
        }

        .info-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 860px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .meta-item {
            margin-bottom: 16px;
        }

        .meta-label {
            font-size: 0.78rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-value {
            font-size: 0.98rem;
            color: #0f172a;
            font-weight: 600;
            line-height: 1.5;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .status-pill.menunggu {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .status-pill.diterima {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .status-pill.ditolak {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .photo-thumb {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .photo-thumb:hover {
            transform: scale(1.05);
            border-color: #007bff;
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.18);
        }

        .photo-link {
            display: block;
            border: 0;
            padding: 0;
            background: transparent;
            cursor: zoom-in;
        }

        .photo-viewer {
            position: fixed;
            inset: 0;
            z-index: 12000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.88);
        }

        .photo-viewer.open {
            display: flex;
        }

        .photo-viewer-content {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            max-width: min(100%, 1100px);
            max-height: 100%;
        }

        .photo-viewer-image {
            display: block;
            max-width: 100%;
            max-height: calc(100vh - 110px);
            object-fit: contain;
            border-radius: 12px;
            background: #fff;
        }

        .photo-viewer-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .photo-viewer-link,
        .photo-viewer-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 9px 14px;
            border: 1px solid rgba(255,255,255,.35);
            border-radius: 10px;
            color: #fff;
            background: rgba(255,255,255,.12);
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .map-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 9px;
            padding: 7px 11px;
            border-radius: 9px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .82rem;
            font-weight: 700;
            text-decoration: none;
        }

        .form-control-modern {
            display: block;
            width: 100%;
            padding: 11px 14px;
            margin-top: 6px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.94rem;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafc;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .form-control-modern:focus {
            outline: none;
            background: #ffffff;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        .btn-modern-primary {
            background: linear-gradient(135deg, #007bff 0%, #005ecf 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            box-shadow: 0 4px 14px rgba(0, 123, 255, 0.28);
            transition: all 0.2s ease;
        }

        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.36);
        }

        .btn-modern-success {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 11px 22px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.28);
            transition: all 0.2s ease;
        }

        .btn-modern-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.36);
        }

        .worker-offer-card {
            padding: 16px 18px;
            border-radius: 14px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }

        .worker-offer-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        }

        .worker-offer-card.selected {
            border-color: #22c55e;
            background: #f0fdf4;
        }
    </style>
</head>
<body>

    <!-- Sidebar Admin Standar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand-wrapper">
            <img src="{{ asset('images/logo-putih.png') }}" alt="Logo TEKSIKA" class="sidebar-logo">
        </div>

        <a href="{{ route('dashboard_admin') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="Beranda" class="icon">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('booking') }}" class="menu-item active">
            <img src="{{ asset('images/booking.png') }}" alt="Booking" class="icon">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('transaksi') }}" class="menu-item">
            <img src="{{ asset('images/pembayaran.png') }}" alt="Pembayaran" class="icon">
            <span class="menu-text">Pembayaran</span>
        </a>
        <a href="{{ route('worker') }}" class="menu-item">
            <img src="{{ asset('images/worker.png') }}" alt="Worker" class="icon">
            <span class="menu-text">Worker</span>
        </a>
        <a href="{{ route('admin.pricing.index') }}" class="menu-item">
            <i class="fa-solid fa-sliders icon" aria-hidden="true"></i>
            <span class="menu-text">Aturan Tarif</span>
        </a>
        <a href="{{ route('chat') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="Chat" class="icon">
            <span class="menu-text">Chat</span>
        </a>

        <!-- Logout Tunggal Admin -->
        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item logout-link" title="Keluar">
                <i class="fa-solid fa-right-from-bracket icon-logout"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main">
        <!-- Modern Signature Blue Header -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle" title="Toggle Sidebar">
                <i class="fa-solid fa-bars"></i>
            </div>

            <div style="flex:1; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                <a href="{{ route('booking') }}" class="header-back-pill">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Booking</span>
                </a>
                <span style="font-size:1.15rem; font-weight:800; color:#ffffff; letter-spacing:-0.01em;">
                    Detail Booking {{ $booking->kode_booking ?? 'BK-' . str_pad($booking->id_request, 3, '0', STR_PAD_LEFT) }}
                </span>
            </div>

            <!-- Profile Pill Button Admin -->
            <div class="header-profile-pill" onclick="openAdminProfileModal()" title="Lihat Profil Admin">
                <img src="{{ asset('images/hero.png') }}" alt="Admin Avatar" class="header-admin-avatar">
                <div class="header-admin-info">
                    <span class="header-admin-name">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    <span class="header-admin-badge">Admin Super</span>
                </div>
                <i class="fa-solid fa-chevron-down header-chevron"></i>
            </div>
        </div>

        <div class="detail-container">

            @if(session('success'))
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:16px 20px; border-radius:14px; margin-bottom:22px; font-weight:600; display:flex; align-items:center; gap:12px; box-shadow:0 2px 10px rgba(0,0,0,0.03);">
                    <i class="fa-solid fa-circle-check" style="font-size:1.2rem;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:16px 20px; border-radius:14px; margin-bottom:22px; font-weight:600; display:flex; align-items:center; gap:12px; box-shadow:0 2px 10px rgba(0,0,0,0.03);">
                    <i class="fa-solid fa-circle-exclamation" style="font-size:1.2rem;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:16px 20px; border-radius:14px; margin-bottom:22px;">
                    <div style="font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Penawaran belum dapat disimpan:
                    </div>
                    <ul style="margin:8px 0 0; padding-left:24px; font-size:0.92rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Ringkasan Informasi Request -->
            <div class="info-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:22px; border-bottom:1px solid #f1f5f9; padding-bottom:18px;">
                    <div>
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:#0f172a;">{{ $booking->layanan }}</h2>
                            <span class="status-pill {{ in_array($booking->status, ['dikonfirmasi', 'dikerjakan', 'selesai'], true) ? 'diterima' : (in_array($booking->status, ['ditolak', 'dibatalkan'], true) ? 'ditolak' : 'menunggu') }}">
                                <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i>
                                @switch($booking->status)
                                    @case('menunggu') Menunggu Pemeriksaan @break
                                    @case('menunggu_persetujuan') Menunggu Persetujuan User @break
                                    @case('persetujuan_dikirim') User Setuju - Perlu Verifikasi @break
                                    @case('dikonfirmasi') Tukang Ditugaskan @break
                                    @case('dikerjakan') Sedang Dikerjakan @break
                                    @case('selesai') Selesai @break
                                    @case('ditolak') Ditolak @break
                                    @case('dibatalkan') Dibatalkan @break
                                    @default Menunggu Pemeriksaan
                                @endswitch
                            </span>
                        </div>
                        <div style="color:#64748b; font-size:0.9rem; margin-top:6px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span>Kode: <strong style="color:#0f172a;">{{ $booking->kode_booking ?? 'BK-' . str_pad($booking->id_request, 3, '0', STR_PAD_LEFT) }}</strong></span>
                            <span>&bull;</span>
                            <span>Diajukan: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }}</span>
                            @if(!empty($booking->tanggal))
                                <span>&bull;</span>
                                <span>Tanggal request: {{ \Carbon\Carbon::parse($booking->tanggal)->translatedFormat('d F Y, H:i') }}</span>
                            @endif
                        </div>
                    </div>

                    <div style="text-align:right;">
                        <div style="font-size:0.8rem; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:0.04em;">Total Estimasi Biaya</div>
                        @if($booking->id_penawaran_terpilih && $selectedOffer)
                            <div style="font-size:1.75rem; font-weight:800; color:#007bff; letter-spacing:-0.02em;">
                                Rp {{ number_format($selectedOffer->total_estimasi, 0, ',', '.') }}
                            </div>
                        @elseif($workerOffers->count())
                            <div style="font-size:1rem; font-weight:700; color:#007bff;">{{ $workerOffers->count() }} Opsi Penawaran</div>
                        @else
                            <div style="font-size:1rem; font-weight:700; color:#94a3b8;">Belum ada penawaran</div>
                        @endif
                    </div>
                </div>

                <div class="grid-2">
                    <!-- Kolom 1: Data Pemesan & Lokasi -->
                    <div>
                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-user" style="color:#007bff;"></i>
                                Pemesan (Customer)
                            </div>
                            <div class="meta-value">{{ $booking->user_name ?? 'User #' . $booking->id_user }} <span style="color:#64748b; font-weight:500;">({{ $booking->user_email ?? '-' }})</span></div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-location-dot" style="color:#ef4444;"></i>
                                Lokasi / Alamat Pemesan
                            </div>
                            <div class="meta-value">{{ $booking->lokasi ?? '-' }}</div>
                            @if(!empty($booking->latitude) && !empty($booking->longitude))
                                <div style="margin-top:4px;">
                                    <small style="color:#0284c7; font-weight:700; background:#e0f2fe; padding:2px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
                                        <i class="fa-solid fa-location-crosshairs"></i> GPS: {{ $booking->latitude }}, {{ $booking->longitude }}
                                    </small>
                                </div>
                                <a class="map-link" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($booking->latitude . ',' . $booking->longitude) }}" target="_blank" rel="noopener noreferrer">
                                    <i class="fa-solid fa-map-location-dot"></i> Buka titik di Google Maps
                                </a>
                            @endif
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-sliders" style="color:#8b5cf6;"></i>
                                Kriteria Pengerjaan
                            </div>
                            <div class="meta-value">
                                Kerusakan <b>{{ ucfirst($booking->tingkat_kerusakan ?? '-') }}</b>
                                &bull; Kompleksitas <b>{{ ucfirst($booking->kompleksitas ?? 'Sedang') }}</b>
                                &bull; Urgensi <b>{{ ucfirst($booking->urgensi ?? 'Normal') }}</b>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-credit-card" style="color:#10b981;"></i>
                                Status Pembayaran
                            </div>
                            <div class="meta-value">
                                @if((int) ($booking->bayar_sekarang ?? 0) === 1)
                                    <span style="color:#15803d;">Pembayaran sudah dilakukan</span>
                                @elseif(!empty($booking->jumlah_pembayaran))
                                    <span style="color:#b45309;">Menunggu pembayaran</span>
                                    <span style="color:#64748b; font-weight:500;"> — Rp {{ number_format($booking->jumlah_pembayaran, 0, ',', '.') }}</span>
                                @else
                                    <span style="color:#64748b;">Belum ada pembayaran</span>
                                @endif
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-wallet" style="color:#10b981;"></i>
                                Metode Pembayaran
                            </div>
                            <div class="meta-value">
                                <span style="background:#f1f5f9; padding:3px 10px; border-radius:6px; font-weight:700; color:#334155;">
                                    {{ $booking->metode_pembayaran ?? 'COD' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2: Detail Pengerjaan & Rincian Algoritma -->
                    <div>
                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-calculator" style="color:#007bff;"></i>
                                Rincian Penawaran Harga &amp; Waktu
                            </div>
                            <div style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px; padding:14px 18px; font-size:0.88rem;">
                                @if($booking->penawaran_dikirim_at)
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span style="color:#64748b;">Estimasi awal jasa:</span>
                                        <b style="color:#0f172a;">Rp {{ number_format($booking->harga_layanan ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span style="color:#64748b;">Estimasi transportasi:</span>
                                        <b style="color:#0f172a;">Rp {{ number_format($booking->biaya_jarak ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span style="color:#64748b;">Material dihitung per kandidat:</span>
                                        <b style="color:#0f172a;">Rp {{ number_format($booking->biaya_material ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span style="color:#64748b;">Biaya jarak kantor ({{ $booking->jarak_km ?? 0 }} km):</span>
                                        <b style="color:#0f172a;">Rp {{ number_format($workerOffers->first()->biaya_transportasi ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span style="color:#64748b;">Urgensi / Kompleksitas:</span>
                                        <b style="color:#0f172a;">{{ ucfirst($booking->urgensi) }} / {{ ucfirst($booking->kompleksitas) }}</b>
                                    </div>
                                    @if(!empty($booking->durasi_perjalanan_menit) || !empty($booking->estimasi_durasi_menit))
                                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                            <span style="color:#64748b;">Estimasi durasi perjalanan / pekerjaan:</span>
                                            <b style="color:#0f172a;">{{ $booking->durasi_perjalanan_menit ? $booking->durasi_perjalanan_menit . ' menit' : '-' }} / {{ $booking->estimasi_durasi_menit ? $booking->estimasi_durasi_menit . ' menit' : '-' }}</b>
                                        </div>
                                    @endif
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <span style="color:#64748b;">Keahlian:</span>
                                        <b style="color:#0f172a;">{{ $booking->penawaran_keahlian ?? $booking->layanan }}</b>
                                    </div>
                                    <div style="padding-top:8px; border-top:1px dashed #cbd5e1; color:#0284c7; font-size:0.82rem; font-weight:600;">
                                        <i class="fa-solid fa-info-circle"></i> Harga jasa + material bervariasi per tukang berdasarkan rating &amp; pengalaman.
                                    </div>
                                @else
                                    <div style="color:#64748b; font-size:0.88rem;">
                                        Belum ada penawaran final. Berikut estimasi awal dari data request:
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                                        <span style="color:#64748b;">Estimasi jasa:</span>
                                        <b style="color:#0f172a;">Rp {{ number_format($booking->harga_layanan ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-top:8px;">
                                        <span style="color:#64748b;">Estimasi transportasi ({{ $booking->jarak_km ?? 0 }} km):</span>
                                        <b style="color:#0f172a;">Rp {{ number_format($booking->biaya_jarak ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-top:8px;">
                                        <span style="color:#64748b;">Estimasi total:</span>
                                        <b style="color:#0f172a;">Rp {{ number_format($booking->estimasi_total ?? 0, 0, ',', '.') }}</b>
                                    </div>
                                    @if(!empty($booking->estimasi_durasi_menit))
                                        <div style="display:flex; justify-content:space-between; margin-top:8px;">
                                            <span style="color:#64748b;">Estimasi durasi:</span>
                                            <b style="color:#0f172a;">{{ $booking->estimasi_durasi_menit }} menit</b>
                                        </div>
                                    @endif
                                    <div style="padding-top:10px; margin-top:10px; border-top:1px dashed #cbd5e1; color:#64748b; font-size:0.82rem;">
                                        Analisis kerusakan dan penawaran final dapat dilengkapi melalui formulir di bawah.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-helmet-safety" style="color:#f59e0b;"></i>
                                Tukang Ditugaskan
                            </div>
                            <div class="meta-value" style="color:{{ $booking->worker_name ? '#15803d' : '#64748b' }}; font-weight:700;">
                                @if($booking->worker_name)
                                    <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i> {{ $booking->worker_name }}
                                    @if(!empty($booking->worker_phone))
                                        <span style="color:#64748b; font-weight:500;">({{ $booking->worker_phone }})</span>
                                    @endif
                                @else
                                    <span style="color:#94a3b8; font-weight:500;">Belum ada tukang yang ditugaskan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Kerusakan -->
                <div style="margin-top:20px; padding-top:18px; border-top:1px solid #f1f5f9;">
                    <div class="meta-label">
                        <i class="fa-solid fa-align-left" style="color:#64748b;"></i>
                        Deskripsi Masalah dari Pemesan
                    </div>
                    <div style="background:#f8fafc; padding:14px 18px; border-radius:12px; border:1.5px solid #e2e8f0; font-size:0.95rem; white-space:pre-wrap; color:#334155; line-height:1.6;">{{ $booking->detail ?? 'Tidak ada catatan tambahan dari pemesan.' }}</div>
                </div>

                <!-- Foto Kerusakan -->
                @if(!empty($booking->foto))
                    <div style="margin-top:20px; padding-top:18px; border-top:1px solid #f1f5f9;">
                        <div class="meta-label">
                            <i class="fa-solid fa-image" style="color:#007bff;"></i>
                            Foto Kerusakan Terlampir
                        </div>
                        <div style="display:flex; gap:14px; flex-wrap:wrap; margin-top:10px;">
                            @foreach(explode(',', $booking->foto) as $pic)
                                @if(!empty(trim($pic)))
                                    @php
                                        $photoPath = str_replace('\\', '/', trim($pic));
                                        $photoPath = preg_replace('#^(?:public/|storage/)#', '', $photoPath);
                                        $photoUrl = asset('storage/' . $photoPath);
                                    @endphp
                                    <a class="photo-link" href="{{ $photoUrl }}" data-photo-url="{{ $photoUrl }}" title="Klik untuk melihat foto ukuran penuh">
                                        <img src="{{ $photoUrl }}" alt="Foto kerusakan, klik untuk memperbesar" class="photo-thumb" loading="lazy">
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Analisis Kerusakan & Form Penawaran -->
            @if(in_array($booking->status, ['menunggu', 'menunggu_persetujuan'], true) && $booking->status_admin === 'belum_diterima')
                <div class="info-card">
                    <div style="margin-bottom:18px; border-bottom:1px solid #f1f5f9; padding-bottom:14px;">
                        <h3 style="margin:0 0 6px 0; font-size:1.2rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px;">
                            <i class="fa-solid fa-file-invoice-dollar" style="color:#007bff;"></i>
                            Analisis Kerusakan &amp; Form Penawaran Resmi
                        </h3>
                        <p style="margin:0; color:#64748b; font-size:0.88rem;">
                            Biaya jasa dihitung otomatis dengan faktor tingkat kerusakan, urgensi pemesan, dan formula jarak.
                        </p>
                    </div>

                    @if($booking->status === 'menunggu_persetujuan')
                        <div style="padding:14px 18px; margin-bottom:20px; background:#fffbeb; border:1.5px solid #fcd34d; border-radius:12px; color:#92400e; font-size:0.92rem; display:flex; align-items:center; gap:10px;">
                            <i class="fa-solid fa-clock" style="font-size:1.2rem; flex-shrink:0;"></i>
                            <span>Penawaran telah dikirim sebelumnya. Mengirimkan ulang form ini akan memperbarui rincian penawaran bagi user.</span>
                        </div>
                    @endif

                    <form action="{{ route('admin.booking.penawaran', $booking->id_request) }}" method="POST">
                        @csrf
                        <div class="grid-2">
                            <div>
                                <label style="font-size:0.86rem; font-weight:700; color:#334155;">
                                    Tingkat Kerusakan
                                    <select name="tingkat_kerusakan" required class="form-control-modern">
                                        @foreach(['ringan' => 'Ringan (+0%)', 'sedang' => 'Sedang (+5%)', 'berat' => 'Berat (+10%)'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('tingkat_kerusakan', $booking->tingkat_kerusakan ?? 'ringan') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div>
                                <label style="font-size:0.86rem; font-weight:700; color:#334155;">
                                    Kompleksitas Pekerjaan
                                    <select name="kompleksitas" required class="form-control-modern">
                                        @foreach(['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('kompleksitas', $booking->kompleksitas ?? 'sedang') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div>
                                <label style="font-size:0.86rem; font-weight:700; color:#334155;">
                                    Tingkat Urgensi
                                    <select name="urgensi" required class="form-control-modern">
                                        @foreach(['rendah' => 'Normal', 'sedang' => 'Mendesak', 'tinggi' => 'Darurat'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('urgensi', $booking->urgensi ?? 'rendah') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div>
                                <label style="font-size:0.86rem; font-weight:700; color:#334155;">
                                    Kebutuhan Keahlian Teknisi
                                    <input name="penawaran_keahlian" value="{{ old('penawaran_keahlian', $booking->penawaran_keahlian ?? $booking->layanan) }}" maxlength="150" required class="form-control-modern">
                                </label>
                            </div>

                            <div>
                                <label style="font-size:0.86rem; font-weight:700; color:#334155;">
                                    Estimasi Biaya Material / Suku Cadang (Rp)
                                    <input type="number" name="biaya_material" min="0" step="1000" value="{{ old('biaya_material', $booking->biaya_material ?? 0) }}" required class="form-control-modern">
                                </label>
                            </div>

                            <div>
                                <label style="font-size:0.86rem; font-weight:700; color:#334155;">
                                    Catatan / Rekomendasi Pengerjaan
                                    <textarea name="penawaran_catatan" maxlength="2000" rows="3" class="form-control-modern" style="resize:vertical;" placeholder="Tuliskan catatan teknis untuk pemesan...">{{ old('penawaran_catatan', $booking->penawaran_catatan) }}</textarea>
                                </label>
                            </div>
                        </div>

                        <div style="margin-top:22px; display:flex; justify-content:flex-end;">
                            <button type="submit" class="btn-modern-primary">
                                <i class="fa-solid fa-paper-plane"></i>
                                {{ $booking->penawaran_dikirim_at ? 'Perbarui & Kirim Penawaran' : 'Kirim Penawaran ke Pemesan' }}
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Opsi Penawaran per Tukang -->
                    @if($workerOffers->count())
                        <div style="margin-top:28px; padding-top:22px; border-top:1px solid #f1f5f9;">
                            <h4 style="margin:0 0 14px 0; font-size:1.05rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                                <i class="fa-solid fa-users" style="color:#007bff;"></i>
                                Opsi Penawaran per Tukang Terkalkulasi ({{ $workerOffers->count() }})
                            </h4>
                            <div style="display:grid; gap:12px;">
                                @foreach($workerOffers as $offer)
                                    <div class="worker-offer-card {{ (int) $booking->id_penawaran_terpilih === (int) $offer->id ? 'selected' : '' }}">
                                        <div style="display:flex; gap:14px; align-items:center; flex-wrap:wrap;">
                                            @if($offer->foto)
                                                <img src="{{ asset('storage/' . $offer->foto) }}" alt="{{ $offer->name }}" onerror="this.onerror=null;this.src='{{ asset('images/userabu.png') }}';" style="width:48px; height:48px; object-fit:cover; border-radius:50%; border:1.5px solid #e2e8f0;">
                                            @else
                                                <div style="width:48px; height:48px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b; font-weight:700;">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                            @endif

                                            <div style="flex:1; min-width:200px;">
                                                <div style="font-weight:800; color:#0f172a; font-size:0.98rem;">{{ $offer->name }}</div>
                                                <div style="font-size:0.83rem; color:#64748b; margin-top:2px;">
                                                    {{ $offer->keahlian_snapshot }} &bull; {{ $offer->pengalaman_snapshot }} thn &bull; ★ {{ number_format($offer->rating_snapshot, 1) }} ({{ $offer->jumlah_ulasan_snapshot }} ulasan)
                                                </div>
                                            </div>

                                            <div style="font-size:0.86rem; color:#475569;">
                                                <div>Jasa + Material: <b>Rp {{ number_format($offer->biaya_jasa_material, 0, ',', '.') }}</b></div>
                                                <div style="color:#64748b; font-size:0.8rem;">Transport: Rp {{ number_format($offer->biaya_transportasi, 0, ',', '.') }}</div>
                                            </div>

                                            <div style="text-align:right;">
                                                <div style="font-size:1.15rem; font-weight:800; color:#007bff;">
                                                    Rp {{ number_format($offer->total_estimasi, 0, ',', '.') }}
                                                </div>
                                                <span style="font-size:0.8rem; font-weight:700; color:{{ $offer->availability_label === 'Siap Bekerja' ? '#16a34a' : '#d97706' }};">
                                                    {{ $offer->availability_label }}
                                                </span>
                                            </div>

                                            @if($offer->status === 'dipilih')
                                                <span style="background:#dcfce7; color:#15803d; font-weight:700; font-size:0.82rem; padding:4px 12px; border-radius:999px; border:1px solid #86efac;">
                                                    <i class="fa-solid fa-check"></i> Dipilih User
                                                </span>
                                            @endif
                                            @if($offer->status === 'ditugaskan')
                                                <span style="background:#dbeafe; color:#1d4ed8; font-weight:700; font-size:0.82rem; padding:4px 12px; border-radius:999px; border:1px solid #bfdbfe;">
                                                    <i class="fa-solid fa-helmet-safety"></i> Ditugaskan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            @elseif($booking->status === 'persetujuan_dikirim')
                <div class="info-card" style="background:#f0fdf4; border-color:#86efac;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                        <i class="fa-solid fa-circle-check" style="color:#16a34a; font-size:1.3rem;"></i>
                        <span style="color:#166534; font-weight:800; font-size:1.1rem;">
                            User Telah Menyetujui &amp; Memilih Tukang
                        </span>
                    </div>
                    <p style="margin:0 0 16px 0; color:#166534; font-size:0.92rem;">
                        Pemesan memilih opsi tukang di bawah pada <b>{{ \Carbon\Carbon::parse($booking->persetujuan_user_at)->translatedFormat('d F Y, H:i') }}</b>. Silakan verifikasi dan tugaskan tukang untuk memulai pengerjaan.
                    </p>

                    @if($selectedOffer)
                        <div style="display:flex; gap:14px; align-items:center; flex-wrap:wrap; padding:16px; border:1.5px solid #86efac; border-radius:14px; background:#ffffff; box-shadow:0 2px 8px rgba(0,0,0,0.02);">
                            @if($selectedOffer->foto)
                                <img src="{{ asset('storage/' . $selectedOffer->foto) }}" alt="{{ $selectedOffer->name }}" onerror="this.onerror=null;this.src='{{ asset('images/userabu.png') }}';" style="width:52px; height:52px; object-fit:cover; border-radius:50%; border:2px solid #86efac;">
                            @else
                                <div style="width:52px; height:52px; border-radius:50%; background:#dcfce7; color:#166534; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            @endif

                            <div style="flex:1; min-width:200px;">
                                <div style="font-weight:800; color:#0f172a; font-size:1.05rem;">{{ $selectedOffer->name }}</div>
                                <div style="font-size:0.85rem; color:#64748b; margin-top:2px;">
                                    {{ $selectedOffer->keahlian_snapshot }} &bull; {{ $selectedOffer->pengalaman_snapshot }} thn &bull; ★ {{ number_format($selectedOffer->rating_snapshot, 1) }}
                                </div>
                                <div style="font-weight:700; color:{{ $selectedOffer->availability_label === 'Siap Bekerja' ? '#16a34a' : '#d97706' }}; font-size:0.84rem; margin-top:3px;">
                                    {{ $selectedOffer->availability_label }}
                                </div>
                            </div>

                            <div style="font-size:0.88rem; color:#475569;">
                                <div>Jasa + Material: <b>Rp {{ number_format($selectedOffer->biaya_jasa_material, 0, ',', '.') }}</b></div>
                                <div style="color:#64748b; font-size:0.8rem;">Transport: Rp {{ number_format($selectedOffer->biaya_transportasi, 0, ',', '.') }}</div>
                            </div>

                            <div style="text-align:right;">
                                <div style="font-size:1.35rem; font-weight:800; color:#16a34a;">
                                    Rp {{ number_format($selectedOffer->total_estimasi, 0, ',', '.') }}
                                </div>
                            </div>

                            <form action="{{ route('admin.booking.tugaskan', $booking->id_request) }}" method="POST" onsubmit="return confirm('Verifikasi penawaran ini dan tugaskan {{ $selectedOffer->name }}?');">
                                @csrf
                                <button type="submit" class="btn-modern-success">
                                    <i class="fa-solid fa-user-check"></i>
                                    Verifikasi &amp; Tugaskan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            @elseif(in_array($booking->status, ['dikonfirmasi', 'dikerjakan'], true))
                <div class="info-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
                        <div>
                            <div style="font-size:0.82rem; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:0.04em;">Status Pengerjaan Saat Ini</div>
                            <div style="font-size:1.15rem; font-weight:800; color:#0f172a; margin-top:4px; display:flex; align-items:center; gap:8px;">
                                <i class="fa-solid fa-spinner fa-spin" style="color:#007bff;"></i>
                                {{ $booking->status === 'dikonfirmasi' ? 'Tukang Ditugaskan (Menunggu Mulai)' : 'Sedang Dalam Proses Pengerjaan' }}
                            </div>
                        </div>

                        @if($booking->status === 'dikonfirmasi' && (int) $booking->bayar_sekarang !== 1)
                            <div style="padding:12px 16px; border:1px solid #fcd34d; background:#fffbeb; color:#92400e; border-radius:12px; font-size:.88rem; font-weight:700;">
                                <i class="fa-solid fa-hourglass-half"></i>
                                Menunggu pemesan menyelesaikan pembayaran. Pekerjaan belum dapat dimulai.
                            </div>
                        @else
                            <form action="{{ route('booking.status', $booking->id_request) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="{{ $booking->status === 'dikonfirmasi' ? 'dikerjakan' : 'selesai' }}">
                                <button type="submit" class="btn-modern-success">
                                    <i class="fa-solid {{ $booking->status === 'dikonfirmasi' ? 'fa-play' : 'fa-check-double' }}"></i>
                                    {{ $booking->status === 'dikonfirmasi' ? 'Mulai Pengerjaan' : 'Tandai Pekerjaan Selesai' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Rekomendasi Tukang (Algoritma Haversine + Scoring) -->
            @include('admin.booking._rekomendasi')

        </div>
    </div>

    <!-- Modal Profil Admin Standar -->
    @include('admin._profile_modal')

    <!-- Modal Konfirmasi Keluar Tunggal -->
    @include('admin._logout_modal')

    <div class="photo-viewer" id="photoViewer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Pratinjau foto kerusakan">
        <div class="photo-viewer-content">
            <img class="photo-viewer-image" id="photoViewerImage" src="" alt="Foto kerusakan ukuran penuh">
            <div class="photo-viewer-actions">
                <a class="photo-viewer-link" id="photoViewerOpen" href="" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka foto asli
                </a>
                <button class="photo-viewer-close" id="photoViewerClose" type="button">
                    <i class="fa-solid fa-xmark"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        (function () {
            const viewer = document.getElementById('photoViewer');
            const image = document.getElementById('photoViewerImage');
            const openLink = document.getElementById('photoViewerOpen');
            const closeButton = document.getElementById('photoViewerClose');

            function closeViewer() {
                viewer.classList.remove('open');
                viewer.setAttribute('aria-hidden', 'true');
                image.src = '';
            }

            document.querySelectorAll('.photo-link').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const photoUrl = link.dataset.photoUrl;
                    image.src = photoUrl;
                    openLink.href = photoUrl;
                    viewer.classList.add('open');
                    viewer.setAttribute('aria-hidden', 'false');
                    closeButton.focus();
                });
            });

            closeButton.addEventListener('click', closeViewer);
            viewer.addEventListener('click', function (event) {
                if (event.target === viewer) closeViewer();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && viewer.classList.contains('open')) closeViewer();
            });
        })();
    </script>
</body>
</html>
