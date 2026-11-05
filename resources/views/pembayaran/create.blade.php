<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Layanan — TEKSIKA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/create.css') }}?v={{ time() }}">
</head>
<body>

@php
    $hasBooking = isset($requestData) && isset($requestData['kode_booking']);
    $rawMetode = trim($requestData['metode_pembayaran'] ?? 'Bank');
    $lowered = strtolower($rawMetode);

    if (in_array($lowered, ['e-wallet', 'qris', 'ewallet'])) {
        $metodeType = 'qris';
        $defaultMetodeInput = 'QRIS';
        $defaultSummaryLabel = 'QRIS / E-Wallet';
    } elseif (in_array($lowered, ['cod', 'tunai', 'cash'])) {
        $metodeType = 'cash';
        $defaultMetodeInput = 'Tunai';
        $defaultSummaryLabel = 'Bayar Tunai di Tempat (COD)';
    } else {
        $metodeType = 'bank';
        $defaultMetodeInput = 'BCA';
        $defaultSummaryLabel = 'Virtual Account BCA';
    }
    
    // Virtual Account deterministik
    $uid = auth()->id() ?? 0;
    $suffix = str_pad((string)$uid, 6, '0', STR_PAD_LEFT);
    $bookDigits = isset($requestData['kode_booking']) ? preg_replace('/\D/','', $requestData['kode_booking']) : '';
    $bookDigits = substr(str_pad($bookDigits, 4, '0', STR_PAD_LEFT), -4);
    $vaBCA = '3901' . $suffix . $bookDigits;
    $vaBRI = '8881' . $suffix . $bookDigits;
    $vaMandiri = '8967' . $suffix . $bookDigits;
    $vaBNI = '8277' . $suffix . $bookDigits;

    $rawJumlah = (float)($jumlah ?? ($requestData['jumlah_pembayaran'] ?? 0));
    $formattedTotal = 'Rp ' . number_format($rawJumlah, 0, ',', '.');
@endphp

<div class="payment-page-wrapper">
    <!-- Navbar / Header Atas -->
    <header class="payment-navbar">
        <a href="{{ route('dashboard_user') }}" class="payment-brand">
            <img src="{{ asset('images/logo-biru.png') }}" alt="Logo TEKSIKA">
            <span class="payment-brand-title">TEKSIKA Pay</span>
        </a>
        <div style="display:flex; align-items:center; gap:12px;">
            <div class="payment-secure-badge">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Enkripsi 256-Bit Aman</span>
            </div>
            <a href="{{ route('bookinguser') }}" class="payment-nav-back" title="Kembali ke Daftar Booking">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </header>

    @if(session('error'))
        <div style="max-width:1180px; margin:16px auto; padding:12px 16px; border:1px solid #fecaca; background:#fef2f2; color:#991b1b; border-radius:10px;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pembayaran.store') }}" method="POST" id="paymentForm">
        @csrf

        {{-- Hidden Fields untuk Alur Booking & Fallback --}}
        @if($hasBooking)
            <input type="hidden" name="booking_kode" value="{{ $requestData['kode_booking'] }}">
        @else
            <input type="hidden" name="layanan" value="{{ $requestData['layanan'] ?? '' }}">
            <input type="hidden" name="lokasi" value="{{ $requestData['lokasi'] ?? '' }}">
            <input type="hidden" name="urgensi" value="{{ $requestData['urgensi'] ?? '' }}">
            <input type="hidden" name="detail" value="{{ $requestData['detail'] ?? '' }}">
            <input type="hidden" name="jumlah_pembayaran" value="{{ $rawJumlah }}">
            <input type="hidden" name="bayar_sekarang" value="{{ $requestData['bayar_sekarang'] ?? 1 }}">
            @if(!empty($fotoPaths))
                @foreach($fotoPaths as $foto)
                    <input type="hidden" name="fotoPaths[]" value="{{ $foto }}">
                @endforeach
            @endif
        @endif

        {{-- Nilai Jumlah Pembayaran Riil --}}
        <input type="hidden" name="jumlah" value="{{ $rawJumlah }}">
        {{-- Nilai Metode Pembayaran yang dikirim ke Controller --}}
        <input type="hidden" name="metode_pembayaran" id="finalMetodeInput" value="{{ $defaultMetodeInput }}">

        <div class="payment-grid">
            <!-- Kolom Kiri: Detail Metode Sesuai Pilihan Booking -->
            <div class="payment-card">
                @if($metodeType === 'bank')
                    <!-- ==============================================
                         METODE: TRANSFER BANK (VIRTUAL ACCOUNT)
                         ============================================== -->
                    <div class="selected-method-banner">
                        <div class="method-banner-left">
                            <div class="method-banner-icon icon-bank">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <span class="method-banner-badge">Metode Dipilih Saat Pemesanan</span>
                                <h3 class="method-banner-title">Transfer Bank (Virtual Account)</h3>
                                <p class="method-banner-sub">Pilih salah satu bank di bawah ini untuk mendapatkan nomor Virtual Account Anda</p>
                            </div>
                        </div>
                    </div>

                    <div style="font-size:0.83rem; font-weight:700; color:#475569; margin-bottom:10px;">
                        Pilih Bank Tujuan Transfer:
                    </div>
                    <div class="bank-cards-grid">
                        <!-- BCA -->
                        <div class="bank-card-option selected" onclick="selectBank('BCA')">
                            <input type="radio" name="bank_option" value="BCA" checked>
                            <div class="bank-card-info">
                                <div class="bank-badge bca">BCA</div>
                                <div class="bank-title-wrap">
                                    <span class="bank-name">Bank BCA</span>
                                    <span class="bank-type">Virtual Account</span>
                                </div>
                            </div>
                            <div class="bank-check-indicator"><i class="fa-solid fa-check"></i></div>
                        </div>

                        <!-- BRI -->
                        <div class="bank-card-option" onclick="selectBank('BRI')">
                            <input type="radio" name="bank_option" value="BRI">
                            <div class="bank-card-info">
                                <div class="bank-badge bri">BRI</div>
                                <div class="bank-title-wrap">
                                    <span class="bank-name">Bank BRI</span>
                                    <span class="bank-type">Virtual Account</span>
                                </div>
                            </div>
                            <div class="bank-check-indicator"><i class="fa-solid fa-check"></i></div>
                        </div>

                        <!-- Mandiri -->
                        <div class="bank-card-option" onclick="selectBank('Mandiri')">
                            <input type="radio" name="bank_option" value="Mandiri">
                            <div class="bank-card-info">
                                <div class="bank-badge mandiri">MANDIRI</div>
                                <div class="bank-title-wrap">
                                    <span class="bank-name">Bank Mandiri</span>
                                    <span class="bank-type">Virtual Account</span>
                                </div>
                            </div>
                            <div class="bank-check-indicator"><i class="fa-solid fa-check"></i></div>
                        </div>

                        <!-- BNI -->
                        <div class="bank-card-option" onclick="selectBank('BNI')">
                            <input type="radio" name="bank_option" value="BNI">
                            <div class="bank-card-info">
                                <div class="bank-badge bni">BNI</div>
                                <div class="bank-title-wrap">
                                    <span class="bank-name">Bank BNI</span>
                                    <span class="bank-type">Virtual Account</span>
                                </div>
                            </div>
                            <div class="bank-check-indicator"><i class="fa-solid fa-check"></i></div>
                        </div>
                    </div>

                    <!-- Virtual Account Display Box -->
                    <div class="va-display-box">
                        <div class="va-header">
                            <span class="va-bank-tag" id="vaBankNameDisplay">Bank BCA Virtual Account</span>
                            <span class="va-auto-verify"><i class="fa-solid fa-bolt"></i> Verifikasi Otomatis</span>
                        </div>
                        <div class="va-number-row">
                            <span class="va-number-text" id="vaNumberDisplay">{{ $vaBCA }}</span>
                            <button type="button" class="btn-copy-va" id="btnCopyVA" onclick="copyVirtualAccount()">
                                <i class="fa-regular fa-copy"></i>
                                <span>Salin</span>
                            </button>
                        </div>
                        <div class="va-account-info">
                            <span>Atas Nama: <strong>TEKSIKA - {{ auth()->user()->name ?? 'Customer' }}</strong></span>
                            <span>Batas Bayar: <strong>24 Jam</strong></span>
                        </div>
                    </div>

                    <!-- Petunjuk Transfer Accordion -->
                    <div class="payment-guide-box">
                        <button type="button" class="guide-toggle-btn" onclick="toggleGuide()">
                            <span><i class="fa-solid fa-circle-question" style="color:#007bff; margin-right:6px;"></i> Cara Pembayaran via Mobile Banking & ATM</span>
                            <i class="fa-solid fa-chevron-down" id="guideArrow"></i>
                        </button>
                        <div class="guide-content-body" id="guideBody">
                            <strong style="color:#0f172a;">1. Mobile Banking:</strong>
                            <ol>
                                <li>Buka aplikasi m-Banking bank Anda lalu pilih menu <strong>Transfer / Bayar</strong>.</li>
                                <li>Pilih <strong>Virtual Account / Tagihan</strong>.</li>
                                <li>Salin dan tempel nomor Virtual Account di atas.</li>
                                <li>Pastikan nama penerima <strong>TEKSIKA</strong> dan nominal tagihan sesuai.</li>
                                <li>Masukkan PIN Anda untuk menyelesaikan pembayaran.</li>
                            </ol>
                            <strong style="color:#0f172a; display:block; margin-top:10px;">2. Mesin ATM:</strong>
                            <ol>
                                <li>Masukkan kartu ATM dan 6 digit PIN Anda.</li>
                                <li>Pilih menu <strong>Transaksi Lainnya</strong> > <strong>Transfer</strong> > <strong>Ke Rekening Virtual Account</strong>.</li>
                                <li>Ketikkan nomor VA di atas, tekan <strong>Benar</strong>, dan simpan struk transaksi Anda.</li>
                            </ol>
                        </div>
                    </div>

                @elseif($metodeType === 'qris')
                    <!-- ==============================================
                         METODE: QRIS / E-WALLET
                         ============================================== -->
                    <div class="selected-method-banner">
                        <div class="method-banner-left">
                            <div class="method-banner-icon icon-qris">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div>
                                <span class="method-banner-badge">Metode Dipilih Saat Pemesanan</span>
                                <h3 class="method-banner-title">QRIS / E-Wallet</h3>
                                <p class="method-banner-sub">Scan QR Code di bawah menggunakan aplikasi e-wallet atau mobile banking Anda</p>
                            </div>
                        </div>
                    </div>

                    <div class="qris-container">
                        <div class="qris-card">
                            <div class="qris-brand-logo">
                                <span class="qris-badge-tag">QRIS</span>
                                <span style="font-size:0.75rem; font-weight:700; color:#475569;">Standar Pembayaran Nasional</span>
                            </div>
                            <div class="qris-img-wrap">
                                <img src="{{ asset('images/qr.jpg') }}" alt="QRIS TEKSIKA">
                            </div>
                            <div class="qris-caption">
                                Arahkan kamera aplikasi <strong>DANA, GoPay, OVO, ShopeePay</strong>, atau <strong>Mobile Banking</strong> untuk scan QR di atas.
                            </div>
                            <div class="qris-supported-logos">
                                <span class="qris-supported-badge">DANA</span>
                                <span class="qris-supported-badge">GoPay</span>
                                <span class="qris-supported-badge">OVO</span>
                                <span class="qris-supported-badge">ShopeePay</span>
                                <span class="qris-supported-badge">BCA Mobile</span>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- ==============================================
                         METODE: TUNAI / COD
                         ============================================== -->
                    <div class="selected-method-banner">
                        <div class="method-banner-left">
                            <div class="method-banner-icon icon-cash">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <div>
                                <span class="method-banner-badge">Metode Dipilih Saat Pemesanan</span>
                                <h3 class="method-banner-title">Bayar Tunai di Tempat (COD)</h3>
                                <p class="method-banner-sub">Pembayaran tunai wajib diselesaikan sebelum admin memulai pengerjaan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="cash-container">
                        <div class="cash-info-title">
                            <i class="fa-solid fa-shield-heart"></i>
                            <span>Konfirmasi Pembayaran Tunai</span>
                        </div>
                        <div class="cash-info-desc">
                            Serahkan pembayaran tunai sesuai tagihan kepada teknisi/admin sebelum pekerjaan dimulai. Konfirmasi pembayaran pada halaman ini agar admin dapat melanjutkan proses pengerjaan.<br>
                            <em>Pekerjaan tidak akan dimulai sebelum pembayaran dikonfirmasi.</em>
                        </div>
                    </div>
                @endif

                <!-- Form Data Pembayar -->
                <div style="margin-top:20px; padding-top:18px; border-top:1px solid #e2e8f0;">
                    <h3 style="font-size:0.95rem; font-weight:800; color:#0f172a; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-user-shield" style="color:#007bff;"></i>
                        <span>Informasi Pembayar</span>
                    </h3>

                    <div class="form-group-wrap">
                        <label for="nama_pembayar" class="form-label-modern">Nama Pembayar</label>
                        <input type="text" class="form-input-modern" id="nama_pembayar" name="nama_pembayar" value="{{ auth()->user()->name ?? '' }}" required placeholder="Contoh: Budi Santoso">
                    </div>

                    <div class="form-group-wrap">
                        <label for="email_pembayar" class="form-label-modern">Email Konfirmasi (Opsional)</label>
                        <input type="email" class="form-input-modern" id="email_pembayar" name="email_pembayar" value="{{ auth()->user()->email ?? '' }}" placeholder="email@domain.com">
                    </div>

                    <div class="form-group-wrap">
                        <label for="keterangan" class="form-label-modern">Catatan Pembayaran (Opsional)</label>
                        <input type="text" class="form-input-modern" id="keterangan" name="keterangan" placeholder="Contoh: Pembayaran servis booking {{ $requestData['kode_booking'] ?? '' }}">
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Tagihan & Tombol Konfirmasi -->
            <div>
                <div class="order-summary-card">
                    <span class="summary-header-badge">
                        <i class="fa-solid fa-file-invoice"></i> Ringkasan Pesanan
                    </span>

                    <div class="summary-booking-id">
                        <span>{{ $requestData['kode_booking'] ?? 'Pemesanan Servis' }}</span>
                        <span class="summary-service-pill">{{ $requestData['layanan'] ?? 'Servis Teknisi' }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Status Pembayaran</span>
                        <span style="font-weight:700; color:#f59e0b; background:#fffbeb; padding:2px 8px; border-radius:6px; font-size:0.75rem;">
                            Menunggu Pembayaran
                        </span>
                    </div>

                    <div class="summary-row">
                        <span>Metode Terpilih</span>
                        <strong id="summarySelectedMethod" style="color:#0f172a;">{{ $defaultSummaryLabel }}</strong>
                    </div>

                    @if(!empty($requestData['lokasi']))
                    <div class="summary-row" style="align-items:flex-start;">
                        <span style="min-width:70px;">Alamat</span>
                        <span style="text-align:right; font-size:0.78rem; color:#475569; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $requestData['lokasi'] }}">
                            {{ $requestData['lokasi'] }}
                        </span>
                    </div>
                    @endif

                    <div class="summary-divider"></div>

                    <div class="summary-row">
                        <span>Subtotal Jasa & Biaya</span>
                        <span>{{ $formattedTotal }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Biaya Layanan & Admin</span>
                        <span style="color:#10b981; font-weight:700;">Gratis (Rp 0)</span>
                    </div>

                    <div class="summary-row total-row">
                        <span class="total-label">Total Pembayaran</span>
                        <span class="total-amount-display">{{ $formattedTotal }}</span>
                    </div>

                    <button type="submit" class="btn-submit-payment" id="btnSubmitPay">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Konfirmasi & Bayar Sekarang</span>
                    </button>

                    <div class="summary-guarantee-note">
                        <i class="fa-solid fa-lock"></i>
                        <span>Transaksi resmi & bergaransi TEKSIKA</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(function(){
    // Data mapping Virtual Account jika metode bank
    const vaMap = {
        'BCA': { name: 'Bank BCA Virtual Account', number: '{{ $vaBCA }}', label: 'Virtual Account BCA' },
        'BRI': { name: 'Bank BRI Virtual Account', number: '{{ $vaBRI }}', label: 'Virtual Account BRI' },
        'Mandiri': { name: 'Bank Mandiri Virtual Account', number: '{{ $vaMandiri }}', label: 'Virtual Account Mandiri' },
        'BNI': { name: 'Bank BNI Virtual Account', number: '{{ $vaBNI }}', label: 'Virtual Account BNI' }
    };

    const finalMetodeInput = document.getElementById('finalMetodeInput');
    const summarySelectedMethod = document.getElementById('summarySelectedMethod');
    const vaBankNameDisplay = document.getElementById('vaBankNameDisplay');
    const vaNumberDisplay = document.getElementById('vaNumberDisplay');
    const btnCopyVA = document.getElementById('btnCopyVA');

    // Select Bank Option (Hanya jika metode Transfer Bank)
    window.selectBank = function(bank) {
        document.querySelectorAll('.bank-card-option').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            const isTarget = radio && radio.value === bank;
            card.classList.toggle('selected', isTarget);
            if (radio) radio.checked = isTarget;
        });

        if (vaMap[bank]) {
            if (vaBankNameDisplay) vaBankNameDisplay.textContent = vaMap[bank].name;
            if (vaNumberDisplay) vaNumberDisplay.textContent = vaMap[bank].number;
            if (finalMetodeInput) finalMetodeInput.value = bank;
            if (summarySelectedMethod) summarySelectedMethod.textContent = vaMap[bank].label;
        }
    };

    // Copy VA to Clipboard
    window.copyVirtualAccount = function() {
        if (!vaNumberDisplay) return;
        const textToCopy = (vaNumberDisplay.textContent || '').trim();
        if (!textToCopy) return;

        navigator.clipboard.writeText(textToCopy).then(() => {
            if (btnCopyVA) {
                btnCopyVA.classList.add('copied');
                btnCopyVA.innerHTML = '<i class="fa-solid fa-check"></i> <span>Tersalin!</span>';
                setTimeout(() => {
                    btnCopyVA.classList.remove('copied');
                    btnCopyVA.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Salin</span>';
                }, 1800);
            }
        });
    };

    // Accordion Panduan
    window.toggleGuide = function() {
        const body = document.getElementById('guideBody');
        const btn = document.querySelector('.guide-toggle-btn');
        if (!body || !btn) return;
        if (body.classList.contains('open')) {
            body.classList.remove('open');
            btn.classList.remove('open');
        } else {
            body.classList.add('open');
            btn.classList.add('open');
        }
    };

    // Proteksi Double Submit pada form
    const form = document.getElementById('paymentForm');
    const submitBtn = document.getElementById('btnSubmitPay');
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Memproses Pembayaran...</span>';
        });
    }
})();
</script>

</body>
</html>
