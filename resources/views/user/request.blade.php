<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Request Layanan — TEKSIKA</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Icons -->
    <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/request.css') }}">
    <style>
        .worker-choice-card[hidden] {
            display: none !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Leaflet OpenStreetMap Fallback -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @if(config('services.google_maps.api_key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ urlencode(config('services.google_maps.api_key')) }}&libraries=geometry"></script>
    @endif
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo-wrap">
            <img src="{{ asset('images/logo-putih.png') }}" alt="Logo" class="sidebar-logo">
        </div>

        <a href="{{ route('dashboard_user') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="logo" class="icon">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('bookinguser') }}" class="menu-item">
            <img src="{{ asset('images/booking.png') }}" alt="logo" class="icon">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('request') }}" class="menu-item active">
            <img src="{{ asset('images/request.png') }}" alt="logo" class="icon">
            <span class="menu-text">Request</span>
        </a>
        <a href="{{ route('chatuser') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon">
            <span class="menu-text">Chat</span>
        </a>
        <a href="{{ route('transaksi_user') }}" class="menu-item">
            <img src="{{ asset('images/pembayaran.png') }}" alt="logo" class="icon">
            <span class="menu-text">Transaksi</span>
        </a>

        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Modal Logout -->
    <div id="logoutModal" class="modal-logout" style="display:none;" onclick="if(event.target===this) closeLogoutModal();">
      <div class="modal-logout-content">
        <span class="modal-logout-close" onclick="closeLogoutModal()">&times;</span>
        <div class="modal-logout-header">
          <i class="fa-solid fa-right-from-bracket icon-logout"></i>
          <h2>Konfirmasi Keluar</h2>
          <p>Yakin ingin keluar dari akun TEKSIKA?</p>
        </div>
        <div class="modal-logout-user">
          <div class="user-info">
            <i class="fa-solid fa-user-circle"></i>
            <div>
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong><br>
              <span>{{ auth()->user()->email ?? '-' }}</span>
            </div>
          </div>
        </div>
        <div class="modal-logout-warning">
          <i class="fa-solid fa-circle-exclamation"></i>
          <p>Anda akan keluar dari akun ini dan perlu login kembali untuk mengakses request layanan.</p>
        </div>
        <div class="modal-logout-footer">
          <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
          <button type="button" class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main" id="main">
        <!-- Header -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <input type="text" placeholder="Cari layanan..." aria-label="Cari layanan">
            <div class="user-info">
                @php
                    $avatarUrlHeader = !empty(auth()->user()->avatar) ? asset('storage/'.auth()->user()->avatar) : asset('images/userabu.png');
                @endphp
                <div class="header-profile-pill" onclick="openProfileModal()" title="Profil Akun">
                    <img id="avatarHeader" src="{{ $avatarUrlHeader }}" alt="{{ auth()->user()->name ?? 'User' }}">
                    <span>{{ auth()->user()->name ?? 'Akun Saya' }}</span>
                    <i class="fa-solid fa-chevron-down profile-chevron"></i>
                </div>
            </div>
        </div>

        <!-- Full Width Request Container -->
        <div class="request-page-wrapper">
            <!-- Header Banner -->
            <div class="request-header-banner">
                <div class="request-header-left">
                    <h1><i class="fa-solid fa-file-circle-plus"></i> Form Permintaan Layanan</h1>
                    <p>Lengkapi kebutuhan perbaikan Anda. Mitra teknisi terbaik TEKSIKA siap membantu dengan cepat dan transparan.</p>
                </div>
                <div class="request-header-badge">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>Teknisi Berpengalaman & Bergaransi</span>
                </div>
            </div>

            @if(session('error'))
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 18px; border-radius:12px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('user.request.store') }}" enctype="multipart/form-data" id="requestForm">
                @csrf

                <!-- 2-Column Responsive Layout Grid -->
                <div class="request-grid">
                    <!-- ==============================================
                         LEFT COLUMN: Layanan, Mitra, Detail, Urgensi, Bayar
                         ============================================== -->
                    <div class="request-col-left">
                        <!-- Card 1: Jenis Layanan & Mitra Tukang -->
                        <div class="request-card">
                            <div class="request-card-header">
                                <div class="request-card-icon">
                                    <i class="fa-solid fa-wrench"></i>
                                </div>
                                <div class="request-card-title-group">
                                    <h2>Layanan & Rekomendasi Teknisi</h2>
                                    <p>Tentukan kategori pekerjaan dan pilih mitra yang paling sesuai</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jenis Layanan <span class="required">*</span></label>
                                @php
                                  $selected = $selected ?? ($services->first() ?? null);
                                  $activeLayanan = old('layanan', $selected ? $selected->nama : ($services->first()->nama ?? ''));
                                @endphp

                                <select name="layanan" id="layananInput" class="form-control" onchange="updateWorkerChoices(); if(typeof triggerEstimasi === 'function') triggerEstimasi();" required>
                                    @if(isset($services))
                                      @foreach($services as $s)
                                          <option value="{{ $s->nama }}" 
                                                  data-service-id="{{ $s->id }}"
                                                  data-service-name="{{ $s->nama }}"
                                                  data-harga-min="{{ $s->harga_min }}" 
                                                  data-harga-max="{{ $s->harga_max }}"
                                                  {{ $activeLayanan === $s->nama ? 'selected' : '' }}>
                                              {{ $s->nama }} (Estimasi: Rp {{ number_format($s->harga_min, 0, ',', '.') }} - Rp {{ number_format($s->harga_max, 0, ',', '.') }})
                                          </option>
                                      @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Mitra Tukang Section -->
                            <div class="form-group" style="margin-top: 14px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                                    <span>Pilih Mitra Teknisi (Opsional)</span>
                                    <small style="font-weight:500; color:#64748b;">Admin dapat memilihkan jika dikosongkan</small>
                                </label>
                                
                                <div id="workerChoices" class="workers-grid">
                                    @forelse($workers as $worker)
                                      @php
                                        $availabilityBg = $worker->display_availability === 'Siap Bekerja' ? '#dcfce7' : ($worker->display_availability === 'Sedang Bekerja' ? '#fef3c7' : '#fee2e2');
                                        $availabilityText = $worker->display_availability === 'Siap Bekerja' ? '#166534' : ($worker->display_availability === 'Sedang Bekerja' ? '#92400e' : '#991b1b');
                                      @endphp
                                      <article class="worker-choice-card {{ old('id_worker_pilihan') == $worker->id ? 'is-selected' : '' }}" 
                                               data-skills="{{ json_encode(array_map('trim', explode(',', $worker->skills ?? ''))) }}" 
                                               data-available="{{ $worker->display_availability === 'Siap Bekerja' ? '1' : '0' }}" 
                                               data-tarif="{{ $worker->tarif ?? '' }}"
                                               data-rating="{{ $worker->display_rating }}"
                                               data-experience="{{ $worker->pengalaman ?? 0 }}"
                                               data-points="{{ min(100, max(0, (int) ($worker->poin ?? 0))) }}"
                                               data-worker-name="{{ $worker->name }}"
                                               onclick="const r = this.querySelector('input[type=radio]'); if(!r.disabled){ r.checked = true; selectWorker(r); }">
                                        @if($worker->foto)
                                          <img src="{{ asset('storage/' . $worker->foto) }}" alt="Foto {{ $worker->name }}" class="worker-avatar" onerror="this.onerror=null;this.src='{{ asset('images/userabu.png') }}';">
                                        @else
                                          <div class="worker-avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                                        @endif
                                        <div class="worker-details">
                                          <div class="worker-name">{{ $worker->name }}</div>
                                          <div class="worker-tag-row">
                                            <span class="worker-status-badge" style="background:{{ $availabilityBg }}; color:{{ $availabilityText }};">
                                                {{ $worker->display_availability }}
                                            </span>
                                            <div class="worker-rating">
                                                <i class="fa-solid fa-star"></i> {{ number_format($worker->display_rating, 1) }} ({{ $worker->jumlah_ulasan }})
                                            </div>
                                          </div>
                                          <div class="worker-meta-info">Keahlian: {{ $worker->skills ?: $worker->role }} &bull; {{ $worker->pengalaman }} thn &bull; {{ min(100, max(0, (int) ($worker->poin ?? 0))) }} poin</div>
                                          <div class="worker-choice-price">Tarif jasa akan disesuaikan</div>
                                          <a href="{{ route('tukang.ulasan', $worker->id) }}" onclick="event.stopPropagation();" style="display:inline-block; margin-top:6px; color:#2563eb; font-size:.8rem; font-weight:700;">Lihat semua ulasan</a>
                                        </div>
                                        <input type="radio" name="id_worker_pilihan" value="{{ $worker->id }}" class="worker-radio-custom" {{ old('id_worker_pilihan') == $worker->id ? 'checked' : '' }} {{ $worker->display_availability !== 'Siap Bekerja' ? 'disabled' : '' }} onchange="selectWorker(this)">
                                      </article>
                                    @empty
                                      <div style="color:#64748b; font-size:0.88rem; padding:10px 0;">Data mitra belum tersedia.</div>
                                    @endforelse
                                </div>
                                <div id="workerChoicesEmpty" style="display:none; color:#0284c7; background:#e0f2fe; padding:10px 14px; border-radius:10px; font-size:0.84rem; margin-top:10px;">
                                    <i class="fa-solid fa-circle-info" style="margin-right:6px;"></i> Belum ada mitra khusus yang siap bekerja saat ini. Anda tetap dapat mengirimkan request, admin TEKSIKA akan segera mencarikan mitra teknisi handal untuk Anda.
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Detail Kerusakan & Foto -->
                        <div class="request-card">
                            <div class="request-card-header">
                                <div class="request-card-icon">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                </div>
                                <div class="request-card-title-group">
                                    <h2>Detail Kerusakan & Dokumentasi</h2>
                                    <p>Tuliskan penjelasan kendala dan unggah foto kerusakan untuk verifikasi admin</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Deskripsi Masalah <span class="required">*</span></label>
                                <textarea name="detail" class="form-control" placeholder="Contoh: Saluran pipa wastafel dapur bocor dan merembes ke dinding bawah, butuh penanganan segera..." required>{{ old('detail') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Foto Kerusakan <span class="required">*</span></label>
                                <div class="upload-dropzone" id="uploadBox" tabindex="-1" onclick="triggerUpload()">
                                    <i class="fa-solid fa-cloud-arrow-up upload-dropzone-icon"></i>
                                    <div class="upload-dropzone-text" id="placeholder">Klik untuk Pilih Minimal 1 Foto Kerusakan</div>
                                    <div class="upload-dropzone-hint">Wajib untuk verifikasi tingkat kerusakan oleh admin. Maksimal 5 foto (JPG, JPEG, PNG), ukuran per foto maksimal 5MB.</div>
                                    <input type="file" id="uploadInput" name="foto[]" accept="image/jpeg,image/png" multiple aria-required="true" style="display: none;" onchange="previewImages(event)">
                                </div>
                                <p id="uploadRequiredError" class="upload-required-error" role="alert" hidden>Unggah minimal 1 foto kerusakan agar admin dapat memverifikasi kondisi.</p>
                                <div id="previewContainer" class="preview-container"></div>
                            </div>
                        </div>

                        <!-- Card 3: Urgensi & Opsi Pembayaran -->
                        <div class="request-card">
                            <div class="request-card-header">
                                <div class="request-card-icon">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div class="request-card-title-group">
                                    <h2>Tingkat Urgensi & Pembayaran</h2>
                                    <p>Prioritas pengerjaan dan metode transaksi yang diinginkan</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tingkat Urgensi Pengerjaan <span class="required">*</span></label>
                                <div class="urgency-grid">
                                    <label class="urgency-card urgensi-rendah {{ old('urgensi', 'rendah') === 'rendah' ? 'selected' : '' }}">
                                        <div class="urgency-card-title">
                                            <span>Normal</span>
                                            <input type="radio" name="urgensi" value="rendah" {{ old('urgensi', 'rendah') === 'rendah' ? 'checked' : '' }} required onchange="selectUrgency(this)">
                                        </div>
                                        <div class="urgency-card-desc">Tarif standar tanpa biaya tambahan.</div>
                                    </label>
                                    <label class="urgency-card urgensi-sedang {{ old('urgensi') === 'sedang' ? 'selected' : '' }}">
                                        <div class="urgency-card-title">
                                            <span>Mendesak</span>
                                            <input type="radio" name="urgensi" value="sedang" {{ old('urgensi') === 'sedang' ? 'checked' : '' }} onchange="selectUrgency(this)">
                                        </div>
                                        <div class="urgency-card-desc"><strong style="color:#d97706;">+{{ number_format(((float) ($pricingRules['faktor_urgensi_sedang'] ?? 1.20) - 1) * 100, 0) }}%</strong>; Prioritas pengerjaan lebih tinggi.</div>
                                    </label>
                                    <label class="urgency-card urgensi-tinggi {{ old('urgensi') === 'tinggi' ? 'selected' : '' }}">
                                        <div class="urgency-card-title">
                                            <span>Darurat</span>
                                            <input type="radio" name="urgensi" value="tinggi" {{ old('urgensi') === 'tinggi' ? 'checked' : '' }} onchange="selectUrgency(this)">
                                        </div>
                                        <div class="urgency-card-desc"><strong style="color:#ef4444;">+{{ number_format(((float) ($pricingRules['faktor_urgensi_tinggi'] ?? 1.40) - 1) * 100, 0) }}%</strong>; Prioritas pengerjaan tertinggi.</div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tingkat Kerusakan <span class="required">*</span></label>
                                <div class="damage-level-grid">
                                    @foreach([
                                        'ringan' => ['Ringan', 'faktor_kerusakan_ringan', 1.00],
                                        'sedang' => ['Sedang', 'faktor_kerusakan_sedang', 1.05],
                                        'berat' => ['Berat', 'faktor_kerusakan_berat', 1.10],
                                    ] as $value => [$label, $rule, $defaultFactor])
                                        @php $damagePercent = number_format(((float) ($pricingRules[$rule] ?? $defaultFactor) - 1) * 100, 0); @endphp
                                        <label class="damage-level-card {{ old('tingkat_kerusakan', 'ringan') === $value ? 'selected' : '' }}">
                                            <span class="damage-level-name">{{ $label }}</span>
                                            <strong>{{ $damagePercent > 0 ? '+' : '' }}{{ $damagePercent }}%</strong>
                                            <input type="radio" name="tingkat_kerusakan" value="{{ $value }}" {{ old('tingkat_kerusakan', 'ringan') === $value ? 'checked' : '' }} required onchange="selectDamageLevel(this)">
                                            <span class="damage-level-description">
                                                @switch($value)
                                                    @case('ringan') Gangguan kecil; fungsi utama masih berjalan. @break
                                                    @case('sedang') Fungsi terganggu atau sebagian tidak berjalan. @break
                                                    @case('berat') Kerusakan parah, tidak berfungsi, atau berisiko. @break
                                                @endswitch
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="damage-level-note">
                                    <i class="fa-solid fa-circle-info"></i>
                                    Pilih berdasarkan kondisi yang Anda lihat. Tingkat kerusakan ini merupakan perkiraan awal dan akan diverifikasi admin sebelum penawaran final; admin dapat menyesuaikannya setelah pemeriksaan.
                                </p>
                            </div>

                            <div class="price-breakdown" id="priceBreakdown" aria-live="polite">
                                <h3><i class="fa-solid fa-receipt"></i> Transparansi Estimasi Biaya</h3>
                                <p class="price-breakdown-worker" id="priceBreakdownWorker">Pilih layanan untuk melihat estimasi dari tarif teknisi.</p>
                                <div class="price-breakdown-row">
                                    <span id="priceBaseLabel">Harga awal jasa</span>
                                    <strong id="priceBase">-</strong>
                                </div>
                                <div class="price-breakdown-row">
                                    <span id="priceRatingLabel">Penyesuaian rating</span>
                                    <strong id="priceRating">-</strong>
                                </div>
                                <div class="price-breakdown-row">
                                    <span id="priceExperienceLabel">Penyesuaian pengalaman</span>
                                    <strong id="priceExperience">-</strong>
                                </div>
                                <div class="price-breakdown-row">
                                    <span id="pricePointsLabel">Tambahan poin</span>
                                    <strong id="pricePoints">-</strong>
                                </div>
                                <div class="price-breakdown-row">
                                    <span id="priceUrgencyLabel">Tambahan urgensi</span>
                                    <strong id="priceUrgency">-</strong>
                                </div>
                                <div class="price-breakdown-row">
                                    <span id="priceDamageLabel">Tambahan tingkat kerusakan</span>
                                    <strong id="priceDamage">-</strong>
                                </div>
                                <div class="price-breakdown-row">
                                    <span>Subtotal jasa</span>
                                    <strong id="priceLaborTotal">-</strong>
                                </div>
                                <div class="price-breakdown-row">
                                    <span>Biaya transportasi</span>
                                    <strong id="priceTransport">Menghitung jarak...</strong>
                                </div>
                                <div class="price-breakdown-row price-breakdown-total">
                                    <span>Estimasi total</span>
                                    <strong id="priceTotal">-</strong>
                                </div>
                                <p class="price-breakdown-note">Tambahan poin = tarif dasar × persentase poin × poin/100; poin maksimum 100. Pengalaman dan rating dihitung terpisah dari tarif dasar. Material/suku cadang belum termasuk; harga final dikonfirmasi admin setelah pemeriksaan.</p>
                            </div>

                            <div class="form-group" style="margin-top: 16px;">
                                <label class="form-label">Metode Pembayaran <span class="required">*</span></label>
                                <div class="payment-grid">
                                    <label class="payment-card selected">
                                        <div class="payment-card-icon icon-wallet">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </div>
                                        <div class="payment-card-title">QRIS / E-Wallet</div>
                                        <div class="payment-card-sub">GoPay, OVO, Dana, Shopee</div>
                                        <input type="radio" name="metode_pembayaran" value="E-wallet" checked required onchange="selectPayment(this)">
                                    </label>
                                    <label class="payment-card">
                                        <div class="payment-card-icon icon-bank">
                                            <i class="fa-solid fa-building-columns"></i>
                                        </div>
                                        <div class="payment-card-title">Transfer Bank</div>
                                        <div class="payment-card-sub">BCA, Mandiri, BRI, BNI</div>
                                        <input type="radio" name="metode_pembayaran" value="Bank" onchange="selectPayment(this)">
                                    </label>
                                    <label class="payment-card">
                                        <div class="payment-card-icon icon-cod">
                                            <i class="fa-solid fa-hand-holding-dollar"></i>
                                        </div>
                                        <div class="payment-card-title">COD</div>
                                        <div class="payment-card-sub">Bayar tunai di tempat</div>
                                        <input type="radio" name="metode_pembayaran" value="COD" onchange="selectPayment(this)">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==============================================
                         RIGHT COLUMN: Peta, Koordinat, Form Alamat & Submit (Sticky)
                         ============================================== -->
                    <div class="request-col-right sticky-column">
                        <div class="request-card">
                            <div class="request-card-header">
                                <div class="request-card-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="request-card-title-group">
                                    <h2>Lokasi & Alamat Lengkap</h2>
                                    <p>Isi alamat lengkap untuk menampilkan titik lokasi di peta</p>
                                </div>
                            </div>

                            <!-- Partial Estimasi Algoritma Cerdas dan Google Maps -->
                            <input type="hidden" id="lokasiInput" name="lokasi" value="{{ old('lokasi') }}">
                            @include('user.request._estimasi')

                            <!-- Input Alamat Lengkap -->
                            <div class="form-group" style="margin-top: 14px;">
                                <label class="form-label" for="alamatLengkap">Alamat Lengkap</label>
                                <textarea id="alamatLengkap" oninput="syncLocationAddress()" onchange="syncLocationAddress()" onblur="syncLocationAddress()" placeholder="Kabupaten Bogor, Kecamatan Gunung Putri, Jalan Pancasila, RT 004/RW 001" maxlength="255" rows="2" class="form-control">{{ old('lokasi') }}</textarea>
                                <div id="addressStatus" style="font-size:0.78rem; color:#64748b; margin-top:5px;">
                                    <i class="fa-solid fa-circle-check" style="color:#10b981;"></i> Titik peta dicari otomatis dari alamat. Sertakan kabupaten, kecamatan, jalan, dan RT/RW bila tersedia.
                                </div>
                            </div>

                            <!-- Submit CTA Button -->
                            <button type="submit" class="btn-submit-request" id="btnSubmitRequest">
                                <span>Kirim Permintaan ke Admin</span>
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>

                            <div class="trust-guarantee-note">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Permintaan diverifikasi langsung oleh Admin TEKSIKA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/request.js') }}"></script>

    <script>
    // Visual handlers for Urgency, Payment, and Worker cards
    function selectUrgency(radio) {
        document.querySelectorAll('.urgency-card').forEach(c => c.classList.remove('selected'));
        if (radio && radio.closest('.urgency-card')) {
            radio.closest('.urgency-card').classList.add('selected');
        }
        updatePricingBreakdown();
        if (typeof triggerEstimasi === 'function') triggerEstimasi();
    }

    function selectDamageLevel(radio) {
        document.querySelectorAll('.damage-level-card').forEach(c => c.classList.remove('selected'));
        if (radio && radio.closest('.damage-level-card')) {
            radio.closest('.damage-level-card').classList.add('selected');
        }
        updatePricingBreakdown();
    }

    function selectPayment(radio) {
        document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
        if (radio && radio.closest('.payment-card')) {
            radio.closest('.payment-card').classList.add('selected');
        }
    }

    function selectWorker(radio) {
        document.querySelectorAll('.worker-choice-card').forEach(c => c.classList.remove('is-selected'));
        if (radio && radio.checked && radio.closest('.worker-choice-card')) {
            radio.closest('.worker-choice-card').classList.add('is-selected');
        }
        updatePricingBreakdown();
    }

    // Form submission ensures address is synced
    document.getElementById('requestForm').addEventListener('submit', function(event) {
        const uploadInput = document.getElementById('uploadInput');
        const uploadError = document.getElementById('uploadRequiredError');
        if (!uploadInput.files.length) {
            event.preventDefault();
            uploadError.hidden = false;
            document.getElementById('uploadBox').focus();
            return;
        }
        uploadError.hidden = true;
        if (typeof syncLocationAddress === 'function') syncLocationAddress();
        this.action = "{{ route('request.store') }}";
        this.method = "POST";
    });

    // Dynamic Worker Filter by selected service
    function updateWorkerChoices() {
        const serviceInput = document.getElementById('layananInput');
        if (!serviceInput) return;

        const selectedOption = serviceInput.tagName === 'SELECT' ? serviceInput.selectedOptions[0] : document.getElementById('id_service');
        const serviceName = serviceInput.tagName === 'SELECT'
            ? (serviceInput.value || '')
            : (selectedOption?.dataset.serviceName || '');
        const hargaMin = Number(selectedOption?.dataset.hargaMin || 0);
        const hargaMax = Number(selectedOption?.dataset.hargaMax || 0);
        const cards = Array.from(document.querySelectorAll('.worker-choice-card'));
        let visibleCards = 0;

        cards.forEach(function (card) {
            const skills = JSON.parse(card.dataset.skills || '[]').map(skill => skill.trim().toLowerCase());
            const matches = Boolean(serviceName && card.dataset.available === '1' && skills.includes(serviceName.trim().toLowerCase()));
            const radio = card.querySelector('input[name="id_worker_pilihan"]');

            if (matches) {
                card.hidden = false;
                card.style.display = 'flex';
                if (radio) {
                    radio.disabled = false;
                    radio.required = false;
                }
                visibleCards++;
                const range = hargaMin === hargaMax
                    ? `Rp ${new Intl.NumberFormat('id-ID').format(hargaMin)}`
                    : `Rp ${new Intl.NumberFormat('id-ID').format(hargaMin)} - Rp ${new Intl.NumberFormat('id-ID').format(hargaMax)}`;
                const priceEl = card.querySelector('.worker-choice-price');
                if (priceEl) {
                    priceEl.textContent = `Tarif dasar: ${range}`;
                }
            } else {
                card.hidden = true;
                card.style.display = 'none';
                if (radio) {
                    radio.disabled = true;
                    radio.required = false;
                    if (radio.checked) {
                        radio.checked = false;
                    }
                }
                card.classList.remove('is-selected');
            }
        });

        const emptyMessage = document.getElementById('workerChoicesEmpty');
        if (emptyMessage) {
            emptyMessage.style.display = serviceName && visibleCards === 0 ? 'block' : 'none';
        }
        updatePricingBreakdown();
    }

    const pricingRules = @json($pricingRules);
    const rupiahFormatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });

    function formatRupiah(amount) {
        return `Rp ${rupiahFormatter.format(Math.round(amount))}`;
    }

    function calculateWorkerEstimate(workerCard, serviceBasePrice) {
        const workerBaseRate = Number(workerCard?.dataset?.tarif || 0) > 0
            ? Number(workerCard.dataset.tarif)
            : serviceBasePrice;
        if (!(workerBaseRate > 0)) return null;

        const rating = Number(workerCard?.dataset?.rating || 3);
        const experience = Number(workerCard?.dataset?.experience || 0);
        const points = Math.max(0, Math.min(100, Number(workerCard?.dataset?.points || 0)));
        const ratingPremium = Math.max(0, rating - 3) * 0.02;
        const experienceRate = Math.min(100, Math.max(0, Number(pricingRules.pengalaman_per_tahun_persen ?? 1)));
        const experienceCap = Math.min(100, Math.max(0, Number(pricingRules.pengalaman_batas_persen ?? 25)));
        const experiencePremium = Math.min(Math.max(0, experience) * experienceRate, experienceCap) / 100;
        const ratingFee = Math.round(workerBaseRate * ratingPremium);
        const experienceFee = Math.round(workerBaseRate * experiencePremium);
        const adjustedRate = Math.round(workerBaseRate) + ratingFee + experienceFee;
        const pointsRate = Math.min(100, Math.max(0, Number(pricingRules.poin_tarif_persen ?? 2)));
        const pointsFee = Math.round(workerBaseRate * pointsRate / 100 * points / 100);

        const urgency = document.querySelector('input[name="urgensi"]:checked')?.value || 'rendah';
        const damage = document.querySelector('input[name="tingkat_kerusakan"]:checked')?.value || 'ringan';
        const urgencyFactor = Number(pricingRules[`faktor_urgensi_${urgency}`] || (urgency === 'tinggi' ? 1.40 : (urgency === 'sedang' ? 1.20 : 1.00)));
        const damageFactor = Number(pricingRules[`faktor_kerusakan_${damage}`] || (damage === 'berat' ? 1.10 : (damage === 'sedang' ? 1.05 : 1.00)));

        const urgencyFee = Math.round(workerBaseRate * (urgencyFactor - 1));
        const damageFee = Math.round(workerBaseRate * (damageFactor - 1));
        const laborTotal = adjustedRate + urgencyFee + damageFee;

        // Gunakan jarak rute berkendara Google Maps (fallback garis lurus)
        const distance = (typeof window.currentRouteDistanceKm === 'number' && !window.currentRouteUnavailable)
            ? window.currentRouteDistanceKm
            : getEstimatedDistanceKm();
        const jarakGratisKm = Number(pricingRules.jarak_gratis_km || 5);
        const tarifPerKm = Number(pricingRules.tarif_per_km || 2500);
        const transportFee = distance > jarakGratisKm
            ? Math.round((distance - jarakGratisKm) * tarifPerKm)
            : 0;

        return {
            baseRate: Math.round(workerBaseRate),
            rating: rating,
            experience: experience,
            points: points,
            pointsRate: pointsRate,
            ratingFee: ratingFee,
            experienceFee: experienceFee,
            experiencePercent: experiencePremium * 100,
            adjustedRate: adjustedRate,
            pointsFee: pointsFee,
            urgency: urgency,
            urgencyFactor: urgencyFactor,
            urgencyFee: urgencyFee,
            damage: damage,
            damageFactor: damageFactor,
            damageFee: damageFee,
            laborTotal: laborTotal,
            distance: distance,
            jarakGratisKm: jarakGratisKm,
            transportFee: transportFee,
            total: laborTotal + transportFee + pointsFee
        };
    }

    function getEstimatedDistanceKm() {
        const latitude = Number(document.getElementById('latInput')?.value);
        const longitude = Number(document.getElementById('lonInput')?.value);
        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return 0;

        const officeLatitude = -6.4239;
        const officeLongitude = 106.9401;
        const toRadians = degree => degree * Math.PI / 180;
        const latitudeDelta = toRadians(latitude - officeLatitude);
        const longitudeDelta = toRadians(longitude - officeLongitude);
        const haversine = Math.sin(latitudeDelta / 2) ** 2
            + Math.cos(toRadians(officeLatitude)) * Math.cos(toRadians(latitude))
            * Math.sin(longitudeDelta / 2) ** 2;
        return Math.round(6371 * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine)) * 100) / 100;
    }

    function updatePricingBreakdown() {
        const breakdown = document.getElementById('priceBreakdown');
        if (!breakdown) return;

        const selectedWorker = document.querySelector('.worker-choice-card input[name="id_worker_pilihan"]:checked');
        const serviceInput = document.getElementById('layananInput');
        const selectedOption = serviceInput && serviceInput.tagName === 'SELECT'
            ? serviceInput.selectedOptions[0]
            : document.getElementById('id_service');
        const serviceName = serviceInput && serviceInput.value ? serviceInput.value : (selectedOption?.dataset?.serviceName || '');
        const serviceBasePrice = selectedOption ? Number(selectedOption.dataset.hargaMin || 0) : 0;

        const matchingWorkers = Array.from(document.querySelectorAll('.worker-choice-card')).filter(function (card) {
            const skills = JSON.parse(card.dataset.skills || '[]').map(skill => skill.trim().toLowerCase());
            return card.dataset.available === '1' && serviceName && skills.includes(serviceName.trim().toLowerCase());
        });
        const chosenCard = selectedWorker ? selectedWorker.closest('.worker-choice-card') : null;

        const estimatedWorkers = matchingWorkers.map(card => ({
            card: card,
            estimate: calculateWorkerEstimate(card, serviceBasePrice)
        })).filter(item => item.estimate !== null)
            .sort((left, right) => left.estimate.total - right.estimate.total);

        estimatedWorkers.forEach(function (item) {
            const priceEl = item.card.querySelector('.worker-choice-price');
            if (priceEl) {
                priceEl.textContent = `Estimasi total ${formatRupiah(item.estimate.total)} · rating +${formatRupiah(item.estimate.ratingFee)} · pengalaman +${formatRupiah(item.estimate.experienceFee)} · poin +${formatRupiah(item.estimate.pointsFee)}`;
            }
        });

        const estimateCard = chosenCard || estimatedWorkers[0]?.card || null;
        const estimate = estimateCard
            ? calculateWorkerEstimate(estimateCard, serviceBasePrice)
            : (serviceBasePrice > 0
                ? calculateWorkerEstimate({ dataset: { tarif: String(serviceBasePrice), rating: '3', experience: '0' } }, serviceBasePrice)
                : null);

        let priceSource = '';
        if (estimateCard) {
            priceSource = chosenCard
                ? `${estimateCard.dataset.workerName} (Tukang pilihan)`
                : `${estimateCard.dataset.workerName} (Rekomendasi tarif terendah)`;
        } else if (serviceBasePrice > 0) {
            priceSource = `${serviceName} (Tarif standar layanan)`;
        }

        const urgency = document.querySelector('input[name="urgensi"]:checked')?.value || 'rendah';
        const damage = document.querySelector('input[name="tingkat_kerusakan"]:checked')?.value || 'ringan';
        const urgencyFactor = Number(pricingRules[`faktor_urgensi_${urgency}`] || (urgency === 'tinggi' ? 1.40 : (urgency === 'sedang' ? 1.20 : 1.00)));
        const damageFactor = Number(pricingRules[`faktor_kerusakan_${damage}`] || (damage === 'berat' ? 1.10 : (damage === 'sedang' ? 1.05 : 1.00)));
        const ratingPercent = estimate && estimate.baseRate > 0
            ? Math.round(estimate.ratingFee / estimate.baseRate * 100)
            : 0;
        const urgencyPercent = Math.round((urgencyFactor - 1) * 100);
        const damagePercent = Math.round((damageFactor - 1) * 100);

        const workerDescEl = document.getElementById('priceBreakdownWorker');
        if (workerDescEl) {
            if (estimateCard) {
                workerDescEl.textContent = `Estimasi dihitung menggunakan ${priceSource}. Poin dibatasi 0–100; pengalaman dikenakan ${pricingRules.pengalaman_per_tahun_persen ?? 1}% per tahun hingga batas ${pricingRules.pengalaman_batas_persen ?? 25}%.`;
            } else if (serviceName) {
                workerDescEl.textContent = `Belum ada mitra khusus siap bekerja untuk kategori ini. Estimasi memakai tarif dasar layanan ${serviceName}.`;
            } else {
                workerDescEl.textContent = 'Pilih kategori layanan di atas untuk melihat rincian estimasi tarif.';
            }
        }

        const priceBaseLabel = document.getElementById('priceBaseLabel');
        if (priceBaseLabel) {
            if (estimateCard && chosenCard) {
                priceBaseLabel.textContent = `Tarif dasar tukang (${estimateCard.dataset.workerName})`;
            } else if (estimateCard) {
                priceBaseLabel.textContent = `Tarif dasar tukang (${estimateCard.dataset.workerName} - Rekomendasi)`;
            } else if (serviceName) {
                priceBaseLabel.textContent = `Tarif dasar layanan (${serviceName})`;
            } else {
                priceBaseLabel.textContent = 'Tarif dasar layanan';
            }
        }
        document.getElementById('priceBase').textContent = estimate ? formatRupiah(estimate.baseRate) : '-';
        document.getElementById('priceRatingLabel').textContent = `Penyesuaian rating (+${ratingPercent}%)`;
        document.getElementById('priceRating').textContent = estimate ? `+ ${formatRupiah(estimate.ratingFee)}` : '-';
        document.getElementById('priceExperienceLabel').textContent = estimate ? `Pengalaman ${estimate.experience} thn (+${estimate.experiencePercent}%)` : 'Penyesuaian pengalaman';
        document.getElementById('priceExperience').textContent = estimate ? `+ ${formatRupiah(estimate.experienceFee)}` : '-';
        document.getElementById('pricePointsLabel').textContent = estimate
            ? `Poin ${estimate.points}/100 × ${estimate.pointsRate}% tarif dasar`
            : 'Tambahan poin';
        document.getElementById('pricePoints').textContent = estimate ? `+ ${formatRupiah(estimate.pointsFee)}` : '-';
        document.getElementById('priceUrgencyLabel').textContent = `Tambahan urgensi (+${urgencyPercent}%)`;
        document.getElementById('priceUrgency').textContent = estimate ? `+ ${formatRupiah(estimate.urgencyFee)}` : '-';
        document.getElementById('priceDamageLabel').textContent = `Kerusakan tambahan (+${damagePercent}%)`;
        document.getElementById('priceDamage').textContent = estimate ? `+ ${formatRupiah(estimate.damageFee)}` : '-';
        document.getElementById('priceLaborTotal').textContent = estimate ? formatRupiah(estimate.laborTotal) : '-';

        const priceTransportEl = document.getElementById('priceTransport');
        if (priceTransportEl) {
            if (estimate) {
                priceTransportEl.textContent = estimate.transportFee > 0
                    ? formatRupiah(estimate.transportFee)
                    : `Rp 0 (Bebas biaya \u2264 ${estimate.jarakGratisKm} km)`;
            } else {
                priceTransportEl.textContent = '-';
            }
        }
        document.getElementById('priceTotal').textContent = estimate ? formatRupiah(estimate.total) : '-';
    }

    const layananEl = document.getElementById('layananInput');
    if (layananEl) {
        layananEl.addEventListener('change', updateWorkerChoices);
    }
    updateWorkerChoices();
    </script>

    <!-- Modal Profil Modular -->
    @include('user._profile_modal')
</body>
</html>
