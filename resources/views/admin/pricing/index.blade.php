<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aturan Tarif & Parameter Algoritma — TEKSIKA Admin</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
        }

        .pricing-content {
            padding: 24px 32px 60px;
            max-width: 1280px;
        }

        /* Top Header Row */
        .pricing-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .pricing-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--admin-text-main, #0f172a);
            margin: 0 0 4px 0;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pricing-subtitle {
            color: var(--admin-text-muted, #64748b);
            font-size: 0.9rem;
            margin: 0;
        }

        .pricing-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Mode Status Badge */
        .mode-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .badge-locked {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .badge-unlocked {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            animation: pulseGlow 1.8s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.2); }
            50% { box-shadow: 0 0 0 6px rgba(0, 123, 255, 0.1); }
        }

        /* Bento Summary Cards (Matching Worker & Booking) */
        .pricing-summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .pricing-summary-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .pricing-summary-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.08);
        }

        .pricing-summary-card .icon-bg {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .icon-bg.blue { background: #eff6ff; color: #007bff; border: 1px solid #bfdbfe; }
        .icon-bg.green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .icon-bg.yellow { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .icon-bg.purple { background: #faf5ff; color: #9333ea; border: 1px solid #e9d5ff; }

        .pricing-summary-card .stat-info {
            flex: 1;
            min-width: 0;
        }

        .pricing-summary-card .stat-info .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
            letter-spacing: -0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pricing-summary-card .stat-info .stat-label {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Bento Card Box */
        .card-box {
            background: #ffffff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            padding: 28px 30px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }

        .card-box:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 24px -2px rgba(15, 23, 42, 0.07);
        }

        .card-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-title-group {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .card-icon-bubble {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .bubble-blue { background: #eff6ff; color: #007bff; border: 1.5px solid #bfdbfe; }
        .bubble-purple { background: #faf5ff; color: #9333ea; border: 1.5px solid #e9d5ff; }
        .bubble-amber { background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a; }

        .card-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .card-desc {
            font-size: 0.86rem;
            color: #64748b;
            margin: 3px 0 0 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-label {
            font-size: 0.86rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Field Input: Readonly Mode vs Edit Mode */
        .field-input {
            padding: 12px 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.96rem;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .field-input {
            background: #ffffff;
            border-color: #93c5fd;
            font-weight: 600;
            cursor: text;
        }

        .field-input:focus {
            outline: none;
            background: #ffffff;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        .field-hint {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.4;
        }

        /* Weight total badge */
        .weight-badge {
            padding: 8px 18px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }

        .weight-badge.valid {
            background: #dcfce7;
            color: #15803d;
            border: 1.5px solid #86efac;
        }

        .weight-badge.invalid {
            background: #fee2e2;
            color: #b91c1c;
            border: 1.5px solid #fca5a5;
        }

        .alert-box {
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .pricing-editing-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 14px;
            margin-top: 24px;
            margin-bottom: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .pricing-content {
                padding: 18px;
            }
            .pricing-summary-cards {
                grid-template-columns: 1fr;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
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
        <a href="{{ route('booking') }}" class="menu-item">
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
        <a href="{{ route('admin.pricing.index') }}" class="menu-item active">
            <i class="fa-solid fa-sliders icon" aria-hidden="true"></i>
            <span class="menu-text">Aturan Tarif</span>
        </a>
        <a href="{{ route('chat') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="Chat" class="icon">
            <span class="menu-text">Chat</span>
        </a>

        <!-- Log Out Tunggal Admin -->
        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item logout-link" title="Keluar">
                <i class="fa-solid fa-right-from-bracket icon-logout"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main">
        <!-- Header Atas (Persis Selaras dengan Worker, Booking, & Transaksi) -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle" title="Toggle Sidebar">
                <i class="fa-solid fa-bars"></i>
            </div>
            
            <div class="user-info">
                @php
                    $currentAdminUser = Auth::guard('admin')->user() ?? auth()->user();
                @endphp
                
                <div class="header-profile-pill" onclick="openAdminProfileModal()" title="Profil Akun Administrator">
                    <div class="admin-avatar-icon"><i class="fa-solid fa-user-shield"></i></div>
                    <span>{{ $currentAdminUser->name ?? 'Admin' }}</span>
                    <i class="fa-solid fa-chevron-down profile-chevron"></i>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="pricing-content">

            <!-- Title & Action Bar (Persis Selaras dengan Halaman Admin Lainnya) -->
            <div class="pricing-header-row">
                <div>
                    <h1 class="pricing-title">
                        <i class="fa-solid fa-sliders" style="color:#007bff;"></i>
                        Aturan Tarif &amp; Biaya
                    </h1>
                    <div class="pricing-subtitle">
                        Pengaturan tarif layanan, poin, pengalaman, urgensi, kerusakan, dan jangkauan.
                    </div>
                </div>

                <div class="pricing-header-actions">
                    <button type="button" class="btn-primary" onclick="submitPricingForm()">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </div>

            <!-- Bento Summary Cards (4 Kartu Ringkasan Cerdas) -->
            <div class="pricing-summary-cards">
                <!-- Card 1: Radius Maksimal -->
                <div class="pricing-summary-card">
                    <div class="icon-bg blue">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><span id="summaryRadius">{{ number_format((float)($rules['radius_maksimal_km']->value ?? 15.0), 1, '.', '') }}</span> KM</div>
                        <div class="stat-label">Radius Jangkauan Maksimal</div>
                    </div>
                </div>

                <!-- Card 2: Tarif Tambahan -->
                <div class="pricing-summary-card">
                    <div class="icon-bg green">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">Rp <span id="summaryTarif">{{ number_format((float)($rules['tarif_per_km']->value ?? 2500), 0, ',', '.') }}</span></div>
                        <div class="stat-label">Tarif Tambahan Per KM</div>
                    </div>
                </div>

                <!-- Card 3: Pengali Urgensi -->
                <div class="pricing-summary-card">
                    <div class="icon-bg yellow">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><span id="summaryUrgensiRendah">{{ number_format((float)($rules['faktor_urgensi_rendah']->value ?? 1.00), 2, '.', '') }}</span>x - <span id="summaryUrgensiTinggi">{{ number_format((float)($rules['faktor_urgensi_tinggi']->value ?? 1.40), 2, '.', '') }}</span>x</div>
                        <div class="stat-label">Rentang Pengali Urgensi</div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check" style="font-size:1.2rem;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation" style="font-size:1.2rem;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Form Parameter Tarif & Algoritma -->
            <form id="pricingForm" action="{{ route('admin.pricing.update') }}" method="POST">
                @csrf

                <!-- Section 1: Biaya Jarak & Radius -->
                <div class="card-box">
                    <div class="card-header-row">
                        <div class="card-title-group">
                            <div class="card-icon-bubble bubble-blue">
                                <i class="fa-solid fa-route"></i>
                            </div>
                            <div>
                                <h3 class="card-title">Biaya Jarak &amp; Radius Layanan</h3>
                                <p class="card-desc">Aturan biaya jarak kantor pusat TEKSIKA ke lokasi pemesan dan batas jangkauan order.</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label" for="jarak_gratis_km">
                                <i class="fa-solid fa-gift" style="color:#007bff;"></i>
                                Batas Jarak Gratis (km)
                            </label>
                            <input type="number" step="0.1" min="0" class="field-input" id="jarak_gratis_km" name="jarak_gratis_km" value="{{ $rules['jarak_gratis_km']->value ?? '5.0' }}" required>
                            <span class="field-hint">Jarak di bawah batas ini dibebaskan dari biaya transportasi tukang.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="tarif_per_km">
                                <i class="fa-solid fa-money-bill-wave" style="color:#10b981;"></i>
                                Tarif Per KM Tambahan (Rp)
                            </label>
                            <input type="number" min="0" step="100" class="field-input" id="tarif_per_km" name="tarif_per_km" value="{{ $rules['tarif_per_km']->value ?? '2500' }}" oninput="updateSummaryCards()" required>
                            <span class="field-hint">Dikenakan untuk setiap km setelah melewati batas gratis.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="radius_maksimal_km">
                                <i class="fa-solid fa-circle-notch" style="color:#ef4444;"></i>
                                Radius Maksimal Layanan (km)
                            </label>
                            <input type="number" min="1" max="100" step="0.5" class="field-input" id="radius_maksimal_km" name="radius_maksimal_km" value="{{ $rules['radius_maksimal_km']->value ?? '15.0' }}" oninput="updateSummaryCards()" required>
                            <span class="field-hint">Lokasi di luar radius ini akan ditolak otomatis oleh sistem.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Penyesuaian Tarif Tukang -->
                <div class="card-box">
                    <div class="card-header-row">
                        <div class="card-title-group">
                            <div class="card-icon-bubble bubble-purple">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>
                            <div>
<<<<<<< HEAD
                                <h3 class="card-title">Bobot Algoritma Rekomendasi (Weighted Scoring)</h3>
                                <p class="card-desc">Total penjumlahan kedua bobot kriteria harus bernilai tepat <b>1.00 (100%)</b>.</p>
=======
                                <h3 class="card-title">Penyesuaian Tarif Tukang</h3>
                                <p class="card-desc">Atur tambahan harga berdasarkan poin dan pengalaman. Poin dibatasi 0–100 dan semua tambahan dihitung dari tarif dasar.</p>
>>>>>>> 40abfce1492bc7d6974163b6b4b29023d1a61d3b
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
<<<<<<< HEAD
                            <label class="field-label" for="bobot_rating">
                                <i class="fa-solid fa-star" style="color:#f59e0b;"></i>
                                Bobot Rating User (0 - 1)
                            </label>
                            <input type="number" step="0.01" min="0" max="1" class="field-input weight-input" id="bobot_rating" name="bobot_rating" value="{{ $rules['bobot_rating']->value ?? '0.50' }}" oninput="updateTotalWeight()" required>
                            <span class="field-hint">Prioritas kepuasan pelanggan dan skor ulasan tukang.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="bobot_pengalaman">
                                <i class="fa-solid fa-award" style="color:#10b981;"></i>
                                Bobot Pengalaman Kerja (0 - 1)
                            </label>
                            <input type="number" step="0.01" min="0" max="1" class="field-input weight-input" id="bobot_pengalaman" name="bobot_pengalaman" value="{{ $rules['bobot_pengalaman']->value ?? '0.50' }}" oninput="updateTotalWeight()" required>
                            <span class="field-hint">Prioritas jam terbang dan tahun kerja aktif teknisi.</span>
=======
                            <label class="field-label" for="poin_tarif_persen">Tambahan Poin (% tarif dasar)</label>
                            <input type="number" step="0.1" min="0" max="100" class="field-input" id="poin_tarif_persen" name="poin_tarif_persen" value="{{ $rules['poin_tarif_persen']->value ?? '2.00' }}" required>
                            <span class="field-hint">Rumus: tarif dasar × persentase ini × (poin/100). Poin di atas 100 tetap dihitung sebagai 100.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="pengalaman_per_tahun_persen">Tambahan Pengalaman (% per tahun)</label>
                            <input type="number" step="0.1" min="0" max="100" class="field-input" id="pengalaman_per_tahun_persen" name="pengalaman_per_tahun_persen" value="{{ $rules['pengalaman_per_tahun_persen']->value ?? '1.00' }}" required>
                            <span class="field-hint">Persentase tarif dasar untuk setiap tahun pengalaman tukang.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="pengalaman_batas_persen">Batas Tambahan Pengalaman (%)</label>
                            <input type="number" step="0.1" min="0" max="100" class="field-input" id="pengalaman_batas_persen" name="pengalaman_batas_persen" value="{{ $rules['pengalaman_batas_persen']->value ?? '25.00' }}" required>
                            <span class="field-hint">Membatasi total persentase tambahan harga dari pengalaman.</span>
>>>>>>> 40abfce1492bc7d6974163b6b4b29023d1a61d3b
                        </div>
                    </div>
                </div>

                <!-- Section 4: Faktor Urgensi & Durasi Kerja -->
                <div class="card-box">
                    <div class="card-header-row">
                        <div class="card-title-group">
                            <div class="card-icon-bubble bubble-amber">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h3 class="card-title">Faktor Urgensi &amp; Tingkat Kerusakan</h3>
                                <p class="card-desc">Pengali harga berdasarkan tingkat kedaruratan dan kerusakan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label" for="faktor_urgensi_rendah">Urgensi Normal (Pengali)</label>
                            <input type="number" step="0.05" min="1" class="field-input" id="faktor_urgensi_rendah" name="faktor_urgensi_rendah" value="{{ $rules['faktor_urgensi_rendah']->value ?? '1.00' }}" oninput="updateSummaryCards()" required>
                            <span class="field-hint">Tarif normal standar (1.00).</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="faktor_urgensi_sedang">Urgensi Mendesak (Pengali)</label>
                            <input type="number" step="0.05" min="1" class="field-input" id="faktor_urgensi_sedang" name="faktor_urgensi_sedang" value="{{ $rules['faktor_urgensi_sedang']->value ?? '1.20' }}" required>
                            <span class="field-hint">+20% prioritas pengerjaan tukang cepat.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="faktor_urgensi_tinggi">Urgensi Darurat (Pengali)</label>
                            <input type="number" step="0.05" min="1" class="field-input" id="faktor_urgensi_tinggi" name="faktor_urgensi_tinggi" value="{{ $rules['faktor_urgensi_tinggi']->value ?? '1.40' }}" oninput="updateSummaryCards()" required>
                            <span class="field-hint">+40% dispatch segera dalam antrian prioritas.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="faktor_kerusakan_ringan">Kerusakan Ringan (Pengali)</label>
                            <input type="number" step="0.01" min="1" class="field-input" id="faktor_kerusakan_ringan" name="faktor_kerusakan_ringan" value="{{ $rules['faktor_kerusakan_ringan']->value ?? '1.00' }}" required>
                            <span class="field-hint">Tarif dasar jasa standar (1.00).</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="faktor_kerusakan_sedang">Kerusakan Sedang (Pengali)</label>
                            <input type="number" step="0.01" min="1" class="field-input" id="faktor_kerusakan_sedang" name="faktor_kerusakan_sedang" value="{{ $rules['faktor_kerusakan_sedang']->value ?? '1.05' }}" required>
                            <span class="field-hint">+5% penyesuaian dari tarif dasar.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="faktor_kerusakan_berat">Kerusakan Berat (Pengali)</label>
                            <input type="number" step="0.01" min="1" class="field-input" id="faktor_kerusakan_berat" name="faktor_kerusakan_berat" value="{{ $rules['faktor_kerusakan_berat']->value ?? '1.10' }}" required>
                            <span class="field-hint">+10% penyesuaian pekerjaan berat.</span>
                        </div>

                    </div>
                </div>

                <div class="pricing-editing-bar">
                    <button type="submit" id="btnSubmitPricing" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Seluruh Perubahan Aturan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Modal Profil Admin Standar (Tanpa Tombol Keluar Ganda) -->
    @include('admin._profile_modal')

    <!-- Modal Konfirmasi Keluar Tunggal -->
    @include('admin._logout_modal')

    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
    // Modal Profil Admin
    function openAdminProfileModal(){
        var el = document.getElementById('adminProfileModal');
        if(el){ el.style.display = 'flex'; }
    }
    function closeAdminProfileModal(){
        var el = document.getElementById('adminProfileModal');
        if(el){ el.style.display = 'none'; }
    }

    // Modal Logout Tunggal Admin
    function openLogoutModal(){
        var el = document.getElementById('logoutModal');
        if(el){ el.style.display = 'flex'; }
    }
    function closeLogoutModal(){
        var el = document.getElementById('logoutModal');
        if(el){ el.style.display = 'none'; }
    }
    function confirmLogout(){
        window.location.href = "{{ route('logout') }}";
    }

    function submitPricingForm() {
        document.getElementById('pricingForm').requestSubmit();
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // ==========================================
    // UPDATE REAL-TIME SUMMARY CARDS ATAS
    // ==========================================
    function updateSummaryCards() {
        // 1. Radius Maksimal (KM)
        const radiusEl = document.getElementById('radius_maksimal_km');
        const summaryRadius = document.getElementById('summaryRadius');
        if (radiusEl && summaryRadius) {
            const val = parseFloat(radiusEl.value);
            summaryRadius.innerText = !isNaN(val) ? val.toFixed(1) : '15.0';
        }

        // 2. Tarif Tambahan Per KM (Rp)
        const tarifEl = document.getElementById('tarif_per_km');
        const summaryTarif = document.getElementById('summaryTarif');
        if (tarifEl && summaryTarif) {
            const val = parseInt(tarifEl.value, 10);
            summaryTarif.innerText = !isNaN(val) ? formatRupiah(val) : '2.500';
        }

        // 3. Rentang Pengali Urgensi (x - x)
        const urgensiRendahEl = document.getElementById('faktor_urgensi_rendah');
        const urgensiTinggiEl = document.getElementById('faktor_urgensi_tinggi');
        const summaryUrgensiRendah = document.getElementById('summaryUrgensiRendah');
        const summaryUrgensiTinggi = document.getElementById('summaryUrgensiTinggi');
        if (urgensiRendahEl && summaryUrgensiRendah) {
            const val = parseFloat(urgensiRendahEl.value);
            summaryUrgensiRendah.innerText = !isNaN(val) ? val.toFixed(2) : '1.00';
        }
        if (urgensiTinggiEl && summaryUrgensiTinggi) {
            const val = parseFloat(urgensiTinggiEl.value);
            summaryUrgensiTinggi.innerText = !isNaN(val) ? val.toFixed(2) : '1.40';
        }
    }

<<<<<<< HEAD
    // ==========================================
    // VALIDASI TOTAL BOBOT (HARUS 1.00)
    // ==========================================
    function updateTotalWeight() {
        const r = parseFloat(document.getElementById('bobot_rating')?.value) || 0;
        const p = parseFloat(document.getElementById('bobot_pengalaman')?.value) || 0;

        const total = Math.round((r + p) * 100) / 100;
        const badge = document.getElementById('weightBadge');
        const text = document.getElementById('weightTotalText');
        const summaryText = document.getElementById('summaryTotalBobot');
        const btn = document.getElementById('btnSubmitPricing');

        if (text) text.innerText = total.toFixed(2);
        if (summaryText) summaryText.innerText = total.toFixed(2);

        if (Math.abs(total - 1.00) < 0.001) {
            if (badge) {
                badge.className = 'weight-badge valid';
                badge.innerHTML = '<i class="fa-solid fa-check"></i> Total Bobot: ' + total.toFixed(2) + ' / 1.00 (Valid)';
            }
            if (btn) btn.disabled = false;
        } else {
            if (badge) {
                badge.className = 'weight-badge invalid';
                badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Total Bobot: ' + total.toFixed(2) + ' / 1.00 (Harus tepat 1.00)';
            }
            if (btn) btn.disabled = true;
        }
    }

=======
>>>>>>> 40abfce1492bc7d6974163b6b4b29023d1a61d3b
    document.addEventListener('DOMContentLoaded', function() {
        updateSummaryCards();
    });
    </script>
</body>
</html>
