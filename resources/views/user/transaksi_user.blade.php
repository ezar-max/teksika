<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Transaksi - TEKSIKA</title>
  <!-- Font & Icon -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
  <link rel="stylesheet" href="{{ asset('css/transaksiuser.css') }}">
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
            <!-- Search dihapus sesuai permintaan -->
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
    <!-- CONTENT -->
    <div class="content">
  <h2>Riwayat Transaksi</h2>
  <p>Semua transaksi dan pembayaran Anda</p>
  
  @if($payments->count() > 0)
    @foreach($payments as $payment)
      <div class="transaction">
        <div class="left">
          <i class="fa-solid fa-credit-card"></i>
          <div>
            <h4>{{ $payment->kode_pembayaran ?? '-' }}</h4>
            <p>Jumlah: Rp {{ number_format($payment->jumlah,0,',','.') }}</p>
            <small>{{ $payment->created_at->format('d-m-Y H:i') }}</small>
          </div>
        </div>
        <div class="right">
          <div class="status {{ strtolower($payment->status) }}">{{ ucfirst($payment->status) }}</div>
          <h4>{{ $payment->metode_pembayaran }}</h4>
          @php
            $reqQuery = \App\Models\Request::where('id_user', auth()->id());
            if (!empty($payment->jumlah)) {
                $reqQuery->where('jumlah_pembayaran', $payment->jumlah);
            }
            if (!empty($payment->metode_pembayaran)) {
                $reqQuery->where('metode_pembayaran', $payment->metode_pembayaran);
            }
            $req = $reqQuery->orderByDesc('created_at')->first();
            if (!$req) {
                // Fallback: ambil request terbaru milik user
                $req = \App\Models\Request::where('id_user', auth()->id())->orderByDesc('created_at')->first();
            }
            // Tentukan tanggal layanan dari kolom request.tanggal jika ada, jika tidak gunakan created_at
            $tanggalReq = null;
            if ($req) {
                if (!empty($req->tanggal)) {
                    try { $tanggalReq = \Carbon\Carbon::parse($req->tanggal); } catch (\Exception $e) { $tanggalReq = $req->created_at; }
                } else {
                    $tanggalReq = $req->created_at;
                }
            }
            $payload = [
                'status' => $payment->status,
                'kode_pembayaran' => $payment->kode_pembayaran,
                'metode_pembayaran' => $payment->metode_pembayaran,
                'created_at' => optional($payment->created_at)->toIso8601String(),
                'jumlah' => $payment->jumlah,
                // Data dari tabel request
                'layanan' => $req->layanan ?? null,
                'lokasi' => $req->lokasi ?? null,
                'tanggal_layanan' => optional($tanggalReq)->toIso8601String(),
                'foto' => $req->foto ?? null,
                'tukang' => optional(optional($req)->worker)->name ?? null,
            ];
          @endphp
          <button type="button" class="btn-detail" title="Lihat Detail" data-payment='{{ htmlspecialchars(json_encode($payload), ENT_QUOTES, 'UTF-8') }}'>
            Lihat Detail
          </button>
        </div>
      </div>
    @endforeach
  @else
    <div class="empty-state">
      <i class="fa-solid fa-receipt"></i>
      <h3>Belum Ada Transaksi</h3>
      <p>Anda belum memiliki riwayat transaksi. Lakukan pemesanan layanan untuk melihat riwayat transaksi Anda di sini.</p>
    </div>
  @endif
</div>
  </div>
  <!-- MODAL -->
  <div class="modal" id="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3><i class="fa-solid fa-file-invoice"></i> Bukti Transaksi</h3>
    <div class="paid" id="modal-status">Pembayaran Berhasil</div>
    <div class="detail-section">
      <h4><i class="fa-solid fa-credit-card"></i> Detail Pembayaran</h4>
      <p><b>Kode Pembayaran:</b> <span id="modal-kode"></span></p>
      <p><b>Metode Pembayaran:</b> <span id="modal-metode"></span></p>
      <p><b>Waktu Pembayaran:</b> <span id="modal-waktu"></span></p>
      <p><b>Total Pembayaran:</b> <b id="modal-total"></b></p>
    </div>
    <center><p class="thanks-note">Terimakasih telah menggunakan layanan TEKSIKA!</p></center>
  </div>
</div>
   <script src="{{ asset('js/sidebar.js') }}"></script>
   <script src="{{ asset('js/popup.js') }}"></script>
   <script src="{{ asset('js/transaksiuser.js') }}"></script>
</body>
</html>