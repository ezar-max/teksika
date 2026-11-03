<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Tarif & Parameter Algoritma — TEKSIKA</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; }
        .main-container { padding: 24px 32px; max-width: 1100px; }
        .card-box { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .card-title { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px; }
        .card-desc { font-size: 0.85rem; color: #64748b; margin: 0 0 18px 0; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
        .field-group { display: flex; flex-direction: column; gap: 6px; }
        .field-label { font-size: 0.85rem; font-weight: 600; color: #334155; }
        .field-input { padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; font-family: inherit; }
        .field-input:focus { outline: none; border-color: #007bff; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }
        .field-hint { font-size: 0.75rem; color: #94a3b8; }
        .weight-badge { padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; }
        .weight-badge.valid { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .weight-badge.invalid { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
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
        <a href="{{ route('booking') }}" class="menu-item">
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
        <a href="{{ route('admin.pricing.index') }}" class="menu-item active">
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

    <!-- Main Content -->
    <div class="main">
        <div class="header" style="background:#fff; padding:16px 32px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="menu-toggle" id="menuToggle" style="cursor:pointer; font-size:1.2rem;">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <h2 style="margin:0; font-size:1.25rem; font-weight:700;">Pengaturan Tarif &amp; Parameter Algoritma</h2>
            </div>
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

            <form action="{{ route('admin.pricing.update') }}" method="POST">
                @csrf

                <!-- Section 1: Jarak & Tarif Transportasi (Haversine & Pricing) -->
                <div class="card-box">
                    <h3 class="card-title">
                        <i class="fa-solid fa-route" style="color:#007bff;"></i>
                        Tarif Jarak &amp; Transportasi
                    </h3>
                    <p class="card-desc">Aturan biaya jarak dan radius pengerjaan yang dihitung dengan rumus Haversine.</p>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label" for="jarak_gratis_km">Batas Jarak Gratis (km)</label>
                            <input type="number" step="0.1" min="0" class="field-input" id="jarak_gratis_km" name="jarak_gratis_km" value="{{ $rules['jarak_gratis_km']->value ?? '5.0' }}" required>
                            <span class="field-hint">Perjalanan di bawah jarak ini bebas biaya transportasi.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="tarif_per_km">Tarif Tambahan Per KM (Rp)</label>
                            <input type="number" min="0" step="100" class="field-input" id="tarif_per_km" name="tarif_per_km" value="{{ $rules['tarif_per_km']->value ?? '2500' }}" required>
                            <span class="field-hint">Dikenakan untuk setiap km setelah batas gratis.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="kecepatan_kmh">Estimasi Kecepatan Tempuh (km/jam)</label>
                            <input type="number" min="10" max="100" class="field-input" id="kecepatan_kmh" name="kecepatan_kmh" value="{{ $rules['kecepatan_kmh']->value ?? '30' }}" required>
                            <span class="field-hint">Standar kota = 30 km/jam (2 menit per km).</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="radius_maksimal_km">Radius Maksimal Tukang (km)</label>
                            <input type="number" min="1" max="100" step="0.5" class="field-input" id="radius_maksimal_km" name="radius_maksimal_km" value="{{ $rules['radius_maksimal_km']->value ?? '15.0' }}" required>
                            <span class="field-hint">Tukang di luar radius ini tidak akan direkomendasikan.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Bobot Algoritma Weighted Scoring -->
                <div class="card-box">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:14px;">
                        <div>
                            <h3 class="card-title" style="margin:0;">
                                <i class="fa-solid fa-scale-balanced" style="color:#007bff;"></i>
                                Bobot Penilaian Tukang (Weighted Scoring)
                            </h3>
                            <p class="card-desc" style="margin:4px 0 0 0;">Total penjumlahan keempat bobot kriteria <b>harus bernilai tepat 1,00 (100%)</b>.</p>
                        </div>
                        <div id="weightBadge" class="weight-badge valid">
                            <i class="fa-solid fa-check"></i> Total Bobot: <span id="weightTotalText">1.00</span> / 1.00
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label" for="bobot_jarak">Bobot Jarak (0 - 1)</label>
                            <input type="number" step="0.01" min="0" max="1" class="field-input weight-input" id="bobot_jarak" name="bobot_jarak" value="{{ $rules['bobot_jarak']->value ?? '0.25' }}" oninput="updateTotalWeight()" required>
                            <span class="field-hint">Prioritas kedekatan lokasi tukang ke pengguna.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="bobot_rating">Bobot Rating (0 - 1)</label>
                            <input type="number" step="0.01" min="0" max="1" class="field-input weight-input" id="bobot_rating" name="bobot_rating" value="{{ $rules['bobot_rating']->value ?? '0.15' }}" oninput="updateTotalWeight()" required>
                            <span class="field-hint">Prioritas kepuasan pelanggan dari tukang.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="bobot_pengalaman">Bobot Pengalaman (0 - 1)</label>
                            <input type="number" step="0.01" min="0" max="1" class="field-input weight-input" id="bobot_pengalaman" name="bobot_pengalaman" value="{{ $rules['bobot_pengalaman']->value ?? '0.15' }}" oninput="updateTotalWeight()" required>
                            <span class="field-hint">Prioritas jam terbang / tahun kerja tukang.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="bobot_beban_kerja">Bobot Beban Kerja (0 - 1)</label>
                            <input type="number" step="0.01" min="0" max="1" class="field-input weight-input" id="bobot_beban_kerja" name="bobot_beban_kerja" value="{{ $rules['bobot_beban_kerja']->value ?? '0.45' }}" oninput="updateTotalWeight()" required>
                            <span class="field-hint">Prioritas tukang dengan tugas aktif lebih sedikit.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Faktor Urgensi & Durasi Kerja -->
                <div class="card-box">
                    <h3 class="card-title">
                        <i class="fa-solid fa-clock-rotate-left" style="color:#007bff;"></i>
                        Faktor Pengali Urgensi &amp; Durasi
                    </h3>
                    <p class="card-desc">Pengali harga berdasarkan urgensi dan pengali estimasi durasi kerja berdasarkan tingkat kesulitan.</p>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label" for="faktor_urgensi_rendah">Urgensi Normal (Pengali)</label>
                            <input type="number" step="0.05" min="1" class="field-input" id="faktor_urgensi_rendah" name="faktor_urgensi_rendah" value="{{ $rules['faktor_urgensi_rendah']->value ?? '1.00' }}" required>
                            <span class="field-hint">Tarif normal standar (1.00).</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="faktor_urgensi_sedang">Urgensi Mendesak (Pengali)</label>
                            <input type="number" step="0.05" min="1" class="field-input" id="faktor_urgensi_sedang" name="faktor_urgensi_sedang" value="{{ $rules['faktor_urgensi_sedang']->value ?? '1.25' }}" required>
                            <span class="field-hint">+25% tarif untuk permintaan mendesak (1.25).</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="faktor_urgensi_tinggi">Urgensi Darurat (Pengali)</label>
                            <input type="number" step="0.05" min="1" class="field-input" id="faktor_urgensi_tinggi" name="faktor_urgensi_tinggi" value="{{ $rules['faktor_urgensi_tinggi']->value ?? '1.50' }}" required>
                            <span class="field-hint">+50% tarif untuk situasi darurat (1.50).</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="pengali_durasi_sedang">Pengali Durasi Sedang</label>
                            <input type="number" step="0.1" min="1" class="field-input" id="pengali_durasi_sedang" name="pengali_durasi_sedang" value="{{ $rules['pengali_durasi_sedang']->value ?? '1.50' }}" required>
                            <span class="field-hint">Pekerjaan sedang (60 menit x 1.5 = 90 menit).</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="pengali_durasi_tinggi">Pengali Durasi Berat</label>
                            <input type="number" step="0.1" min="1" class="field-input" id="pengali_durasi_tinggi" name="pengali_durasi_tinggi" value="{{ $rules['pengali_durasi_tinggi']->value ?? '2.00' }}" required>
                            <span class="field-hint">Pekerjaan berat (60 menit x 2.0 = 120 menit).</span>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
                    <button type="submit" id="btnSubmitPricing" style="background:#007bff; color:#fff; border:none; padding:12px 28px; border-radius:10px; font-weight:700; font-size:1rem; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Aturan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
    function updateTotalWeight() {
        const j = parseFloat(document.getElementById('bobot_jarak').value) || 0;
        const r = parseFloat(document.getElementById('bobot_rating').value) || 0;
        const p = parseFloat(document.getElementById('bobot_pengalaman').value) || 0;
        const b = parseFloat(document.getElementById('bobot_beban_kerja').value) || 0;

        const total = Math.round((j + r + p + b) * 100) / 100;
        const badge = document.getElementById('weightBadge');
        const text = document.getElementById('weightTotalText');
        const btn = document.getElementById('btnSubmitPricing');

        text.innerText = total.toFixed(2);

        if (Math.abs(total - 1.00) < 0.001) {
            badge.className = 'weight-badge valid';
            badge.innerHTML = '<i class="fa-solid fa-check"></i> Total Bobot: ' + total.toFixed(2) + ' / 1.00 (Valid)';
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        } else {
            badge.className = 'weight-badge invalid';
            badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Total Bobot: ' + total.toFixed(2) + ' / 1.00 (Harus tepat 1.00)';
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
        }
    }
    document.addEventListener('DOMContentLoaded', updateTotalWeight);
    </script>
</body>
</html>

