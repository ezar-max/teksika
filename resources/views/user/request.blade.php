<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/request.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>
      <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <img src="{{ asset('images/logo-putih.png') }}" alt="Logo" class="sidebar-logo">

        <a href="{{ route('dashboard_user') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('bookinguser') }}" class="menu-item">
            <img src="{{ asset('images/booking.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('request') }}" class="menu-item">
            <img src="{{ asset('images/request.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Request</span>
        </a>
        <a href="{{ route('chatuser') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Chat</span>
        </a>
        <a href="{{ route('transaksi_user') }}" class="menu-item">
            <img src="{{ asset('images/pembayaran.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Transaksi</span>
        </a>

        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </div>
    </div>
<!-- Modal Logout -->
<div id="logoutModal" class="modal-logout">
  <div class="modal-logout-content">
    <!-- Tombol Close (X) -->
    <span class="modal-logout-close" onclick="closeLogoutModal()">&times;</span>

    <!-- Header -->
    <div class="modal-logout-header">
      <i class="fa-solid fa-right-from-bracket icon-logout"></i>
      <h2>Konfirmasi Log out</h2>
      <p>Yakin Ingin Keluar Dari Akun?</p>
    </div>

    <!-- Info User -->
    <div class="modal-logout-user">
      <div class="user-info">
        <i class="fa-solid fa-user-circle"></i>
        <div>
          <strong>CUSTOMER</strong><br>
          <span>Fredi Wahyu Ezar | Gmail.com</span>
        </div>
        <span class="role">Admin</span>
      </div>
    </div>

    <!-- Peringatan -->
    <div class="modal-logout-warning">
      <i class="fa-solid fa-circle-exclamation"></i>
      <p>Anda akan keluar dari akun ini dan perlu login kembali untuk mengakses admin panel</p>
    </div>

    <!-- Tombol -->
    <div class="modal-logout-footer">
      <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
      <button class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
    </div>
  </div>
</div>

    <!-- Main Content -->
    <div class="main">
        <!-- Header -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <input type="text" placeholder="Search">
            <div class="user-info">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>

<!-- Modal Profil -->
<div id="profileModal" class="modal-profile">
  <div class="modal-profile-content">
    <!-- Tombol Close -->
    <span class="modal-profile-close" onclick="closeProfileModal()">&times;</span>
    
    <h2>Profil Saya</h2>
    <p class="subtitle">Kelola informasi akun anda</p>

    <div class="profile-info">
      <i class="fa-solid fa-user-circle profile-icon"></i>
      <h3>Fredi Wahyu Ezar</h3>
      <p>frediwahyuezar@gmail.com</p>
    </div>

    <div class="profile-stats">
      <div class="stat">
        <h3>2</h3>
        <p>Total Booking</p>
      </div>
      <div class="stat">
        <h3>4.9</h3>
        <p>Rating</p>
      </div>
      <div class="stat">
        <h3>24/7</h3>
        <p>Support</p>
      </div>
    </div>

    <p class="member-info">
      Member ARSIKA - platform jasa tukang terpercaya dengan layanan admin profesional
    </p>

    <button class="btn-logout" onclick="openLogoutModal()">Keluar</button>
  </div>
</div>
<div class="request-container" style="max-height: 90vh; overflow-y: auto;">
    <div class="content">
      <div class="card">
        <h2>Detail Request</h2>
        <form method="POST" action="{{ route('request.preview') }}" enctype="multipart/form-data" id="requestForm">
            @csrf

            <label>Jenis Layanan</label>
            @php($selected = isset($services) ? $services->firstWhere('nama', request('layanan')) : null)
            @if($selected)
              <div class="selected-service" style="margin-bottom:10px;">
                <span style="display:inline-block; padding:8px 12px; border-radius:8px; background:#eef5ff; color:#1856a7; font-weight:700;">
                  {{ $selected->nama }}
                </span>
              </div>
              <input type="hidden" id="layananInput" name="layanan" value="{{ $selected->nama }}" />
              <!-- Tempat menampilkan harga -->
              <div id="hargaLayanan" style="margin:8px 0 18px; font-weight:600; color:#1856a7;"></div>
            @else
              <div class="alert" style="padding:12px; border:1px solid #f5c6cb; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:18px;">
                Silakan pilih layanan dari halaman Beranda terlebih dahulu.
              </div>
              <a href="{{ route('dashboard_user') }}" class="btn" style="display:inline-block; padding:10px 14px; background:#1856a7; color:#fff; border-radius:8px; text-decoration:none; margin-bottom:18px;">Pilih Layanan</a>
              <input type="hidden" id="layananInput" name="layanan" value="">
              <div id="hargaLayanan" style="margin:8px 0 18px; font-weight:600; color:#1856a7;"></div>
            @endif

            <label>Lokasi</label>
            <input type="text" id="lokasiInput" name="lokasi" placeholder="Masukkan lokasi..." required style="background:#f5f5f5; margin-bottom:14px;" />

            <label>Deskripsi Masalah</label>
            <textarea name="detail" placeholder="Jelaskan detail masalah pekerjaan yang di butuhkan..." required style="background:#f5f5f5; margin-bottom:14px;"></textarea>

            <label>Foto Kerusakan</label>
            <div class="upload-box" id="uploadBox" onclick="triggerUpload()" style="background:#fff; border:1.5px solid #cfcfcf; border-radius:12px; min-height:180px; display:flex; align-items:center; justify-content:center; flex-direction:column; margin-bottom:18px;">
              <div id="placeholder" class="placeholder" style="text-align:center;">
                <i class="fa fa-camera" style="font-size:2.5rem; color:#888;"></i>
                <p style="font-weight:600; margin:8px 0 0 0;">Klik untuk Upload Foto</p>
                <small style="color:#888;">Maksimal 5 foto, Masing-masing max 5mb</small>
              </div>
              <input type="file" id="uploadInput" name="foto[]" accept="image/*" multiple style="display: none;" onchange="previewImages(event)">
              <div id="previewContainer" class="preview-container"></div>
            </div>

            <label>Tingkat urgensi</label>
            <div class="urgency" style="display:flex; gap:16px; margin-bottom:18px;">
              <label style="flex:1; border:1.5px solid #cfcfcf; border-radius:10px; padding:12px;">
                <input type="radio" name="urgensi" value="rendah" required onclick="setUrgensi(this)"> Normal
                <div style="font-size:0.95em; color:#888;">
                  Respon admin: <b>1 jam - 1.30 jam</b><br>
                  Estimasi pengerjaan: <b>20 - 30 hari</b><br>
                  <span style="color:#43d17a;">Tarif standar</span>
                </div>
              </label>
              <label style="flex:1; border:1.5px solid #cfcfcf; border-radius:10px; padding:12px;">
                <input type="radio" name="urgensi" value="sedang" onclick="setUrgensi(this)"> Mendesak
                <div style="font-size:0.95em; color:#888;">
                  Respon admin: <b>30 menit - 1 jam</b><br>
                  Estimasi pengerjaan: <b>10 - 20 hari</b><br>
                  <span style="color:#f9a825;">+30% dari tarif normal</span>
                </div>
              </label>
              <label style="flex:1; border:1.5px solid #cfcfcf; border-radius:10px; padding:12px;">
                <input type="radio" name="urgensi" value="tinggi" onclick="setUrgensi(this)"> Darurat
                <div style="font-size:0.95em; color:#888;">
                  Respon admin: <b>0 - 30 menit</b><br>
                  Estimasi pengerjaan: <b>5 - 10 hari</b><br>
                  <span style="color:#ff5252;">+50% dari tarif normal</span>
                </div>
              </label>
            </div>

            <h2>Opsi pembayaran</h2>
<label>Metode pembayaran</label>
<div style="display:flex; gap:18px; margin-bottom:18px;">
  <label class="pay-method" style="flex:1; border:1.5px solid #cfcfcf; border-radius:10px; padding:18px; display:flex; align-items:center; cursor:pointer;">
    <input type="radio" name="metode_pembayaran" value="E-wallet" required style="margin-right:12px;" />
    <span style="font-size:2rem; color:#00c853; margin-right:12px;"><i class="fa-solid fa-wallet"></i></span>
    <div>
      <div style="font-weight:600;">Qris</div>
      <div style="font-size:0.95em; color:#888;">Gopay, Ovo, Dana, ShopeePay</div>
    </div>
  </label>
  <label class="pay-method" style="flex:1; border:1.5px solid #cfcfcf; border-radius:10px; padding:18px; display:flex; align-items:center; cursor:pointer;">
    <input type="radio" name="metode_pembayaran" value="Bank" style="margin-right:12px;" />
    <span style="font-size:2rem; color:#2196f3; margin-right:12px;"><i class="fa-solid fa-building-columns"></i></span>
    <div>
      <div style="font-weight:600;">Bank</div>
      <div style="font-size:0.95em; color:#888;">BCA, Mandiri, BRI, BNI</div>
    </div>
  </label>
  <label class="pay-method" style="flex:1; border:1.5px solid #cfcfcf; border-radius:10px; padding:18px; display:flex; align-items:center; cursor:pointer;">
    <input type="radio" name="metode_pembayaran" value="COD" style="margin-right:12px;" />
    <span style="font-size:2rem; color:#ff9800; margin-right:12px;"><i class="fa-solid fa-money-bill-wave"></i></span>
    <div>
      <div style="font-weight:600;">COD</div>
      <div style="font-size:0.95em; color:#888;">Bayar tunai saat selesai</div>
    </div>
  </label>
</div>
            <button type="submit" class="btn-submit" style="width:100%; background:#1856a7; color:#fff; font-size:1.1rem; font-weight:600; border-radius:8px; padding:14px 0; margin-top:18px;">Kirim ke Admin <i class="fa-solid fa-paper-plane"></i></button>
        </form>
      </div>
    </div>
</div>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/request.js') }}"></script>
    <style>
      .option-btn.selected{
        background:#1856a7 !important;
        color:#fff !important;
        border-color:#1856a7 !important;
      }
    </style>

    <!-- Popup Request Berhasil -->
<div id="successPopup" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:16px; padding:32px 28px; box-shadow:0 8px 32px rgba(0,0,0,0.12); text-align:center; max-width:350px; margin:auto;">
    <i class="fa-solid fa-circle-check" style="font-size:3rem; color:#43d17a; margin-bottom:18px;"></i>
    <h2 style="margin-bottom:10px;">Request Berhasil!</h2>
    <p style="color:#333; margin-bottom:18px;">Permintaan Anda telah dikirim.<br>Silakan menunggu admin untuk mengkonfirmasi request Anda.</p>
    <button onclick="closeSuccessPopup()" style="background:#1856a7; color:#fff; border:none; border-radius:8px; padding:10px 24px; font-size:1rem; font-weight:600;">Tutup</button>
  </div>
</div>

<script>
let hargaMin = {{ isset($selected) && $selected ? $selected->harga_min : 'null' }};
let hargaMax = {{ isset($selected) && $selected ? $selected->harga_max : 'null' }};
let urgensi = null;

// Inisialisasi tampilan harga saat halaman dimuat
document.addEventListener('DOMContentLoaded', function(){
    updateHargaDanPembayaran();
});

function setUrgensi(radio) {
    urgensi = radio.value;
    updateHargaDanPembayaran();
}

function updateHargaDanPembayaran() {
    let hargaText = '';
    let hargaNormal = hargaMin;
    let hargaAkhir = hargaMin;

    if (hargaMin !== null && hargaMax !== null) {
        if (hargaMin === hargaMax) {
            hargaText = 'Harga: Rp ' + formatRupiah(hargaMin);
        } else {
            hargaText = 'Harga: Rp ' + formatRupiah(hargaMin) + ' - Rp ' + formatRupiah(hargaMax);
        }
    }

    // Hitung harga sesuai urgensi
    if (hargaMin !== null && urgensi) {
        if (urgensi === 'sedang') {
            hargaAkhir = Math.round(hargaNormal * 1.3);
            hargaText += ' (Mendesak: Rp ' + formatRupiah(hargaAkhir) + ')';
        } else if (urgensi === 'tinggi') {
            hargaAkhir = Math.round(hargaNormal * 1.5);
            hargaText += ' (Darurat: Rp ' + formatRupiah(hargaAkhir) + ')';
        } else {
            hargaAkhir = hargaNormal;
        }
        // harga akhir hanya ditampilkan (admin akan mengisi harga final saat konfirmasi)
    }

    document.getElementById('hargaLayanan').innerText = hargaText;
}

function formatRupiah(angka) {
    return parseInt(angka).toLocaleString('id-ID');
}

// Tidak perlu restore selected layanan, karena layanan diambil dari parameter URL

// === Submit form: selalu kirim ke admin tanpa alur pembayaran awal ===
document.querySelector('form').addEventListener('submit', function(e) {
    this.action = "{{ route('request.store') }}";
    this.method = "POST"; // Pastikan method POST
});
</script>
</body>
</html>