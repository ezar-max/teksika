<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Transaksi — TEKSIKA</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Icons & Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
  <link rel="stylesheet" href="{{ asset('css/transaksiuser.css') }}">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
      background: #f8fafc !important;
    }
    .content-transaksi {
      padding: 28px;
      max-width: 1040px;
      margin: 0 auto;
    }
    .transaksi-header-title {
      margin-bottom: 24px;
    }
    .transaksi-header-title h2 {
      font-size: 1.6rem;
      font-weight: 800;
      color: #0f172a;
      margin: 0 0 6px;
      letter-spacing: -0.02em;
    }
    .transaksi-header-title p {
      color: #64748b;
      font-size: 0.95rem;
      margin: 0;
    }
    .transaction {
      background: #ffffff !important;
      border: 1px solid #e2e8f0 !important;
      border-radius: 18px !important;
      padding: 20px 24px !important;
      margin-bottom: 16px !important;
      box-shadow: 0 4px 18px -4px rgba(15, 23, 42, 0.05) !important;
      transition: all 0.25s ease !important;
    }
    .transaction:hover {
      transform: translateY(-3px) !important;
      border-color: #bde0ff !important;
      box-shadow: 0 12px 28px -6px rgba(0, 123, 255, 0.12) !important;
    }
    .status.success {
      background: #dcfce7 !important;
      color: #166534 !important;
      font-weight: 700;
      border: 1px solid #bbf7d0;
    }
    .status.pending {
      background: #fef3c7 !important;
      color: #92400e !important;
      font-weight: 700;
      border: 1px solid #fde68a;
    }
    .status.failed {
      background: #fee2e2 !important;
      color: #991b1b !important;
      font-weight: 700;
      border: 1px solid #fecaca;
    }
    .empty-state {
      background: #ffffff;
      border: 1px dashed #cbd5e1;
      border-radius: 20px;
      padding: 48px 24px;
      text-align: center;
      color: #64748b;
    }
    .empty-state i {
      font-size: 3rem;
      color: #94a3b8;
      margin-bottom: 14px;
    }
    .empty-state h3 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 6px;
    }
  </style>
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
        <a href="{{ route('request') }}" class="menu-item">
            <img src="{{ asset('images/request.png') }}" alt="logo" class="icon">
            <span class="menu-text">Request</span>
        </a>
        <a href="{{ route('chatuser') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon">
            <span class="menu-text">Chat</span>
        </a>
        <a href="{{ route('transaksi_user') }}" class="menu-item active">
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
          <p>Anda akan keluar dari akun TEKSIKA ini dan perlu login kembali untuk mengakses transaksi.</p>
        </div>
        <div class="modal-logout-footer">
          <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
          <button type="button" class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <!-- Header -->
        <header class="header">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <input type="text" placeholder="Cari transaksi..." aria-label="Cari transaksi">
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
        </header>

        <!-- CONTENT -->
        <main class="content-transaksi">
          <div class="transaksi-header-title">
            <h2>Riwayat Transaksi</h2>
            <p>Daftar invoice dan status pembayaran riwayat layanan Anda</p>
          </div>
          
          @if($payments->count() > 0)
            @foreach($payments as $payment)
              <div class="transaction">
                <div class="left">
                  <div style="width:48px; height:48px; border-radius:14px; background:#eaf4ff; color:#007bff; display:flex; align-items:center; justify-content:center; font-size:1.4rem; border:1px solid #bde0ff; flex-shrink:0;">
                    <i class="fa-solid fa-receipt"></i>
                  </div>
                  <div>
                    <h4 style="font-size:1.05rem; font-weight:700; color:#0f172a; margin:0 0 4px;">{{ $payment->kode_pembayaran ?? '-' }}</h4>
                    <p style="font-size:0.92rem; color:#007bff; font-weight:700; margin:0 0 2px;">Jumlah: Rp {{ number_format($payment->jumlah,0,',','.') }}</p>
                    <small style="color:#64748b;">{{ $payment->created_at->format('d-m-Y H:i') }}</small>
                  </div>
                </div>
                <div class="right" style="text-align:right;">
                  <span class="status {{ strtolower($payment->status) }}" style="padding:4px 12px; border-radius:9999px; font-size:0.8rem; display:inline-block; margin-bottom:6px;">
                    {{ strtoupper($payment->status) }}
                  </span>
                  <div style="font-size:0.88rem; font-weight:600; color:#475569; margin-bottom:8px;">
                    Metode: {{ ucfirst($payment->metode_pembayaran) }}
                  </div>
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
                        $req = \App\Models\Request::where('id_user', auth()->id())->orderByDesc('created_at')->first();
                    }
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
                        'layanan' => $req->layanan ?? null,
                        'lokasi' => $req->lokasi ?? null,
                        'tanggal_layanan' => optional($tanggalReq)->toIso8601String(),
                        'foto' => $req->foto ?? null,
                        'tukang' => optional(optional($req)->worker)->name ?? null,
                    ];
                  @endphp
                  <button type="button" class="btn-detail" title="Lihat Detail" data-payment='{{ htmlspecialchars(json_encode($payload), ENT_QUOTES, 'UTF-8') }}' style="background:linear-gradient(135deg, #007bff 0%, #005ecf 100%); color:#fff; border:none; border-radius:10px; padding:7px 16px; font-weight:700; cursor:pointer; font-size:0.85rem; font-family:inherit;">
                    <i class="fa-solid fa-file-lines" style="margin-right:4px;"></i> Lihat Bukti
                  </button>
                </div>
              </div>
            @endforeach
          @else
            <div class="empty-state">
              <i class="fa-solid fa-receipt"></i>
              <h3>Belum Ada Transaksi</h3>
              <p>Anda belum memiliki riwayat transaksi. Lakukan pemesanan layanan untuk melihat bukti dan invoice pembayaran di sini.</p>
              <a href="{{ route('request') }}" style="display:inline-block; margin-top:14px; background:#007bff; color:#fff; padding:10px 22px; border-radius:9999px; text-decoration:none; font-weight:700; font-size:0.9rem;">Pesan Teknisi Sekarang</a>
            </div>
          @endif
        </main>
    </div>

    <!-- MODAL BUKTI TRANSAKSI -->
    <div class="modal" id="modal" style="display:none;" onclick="if(event.target===this) closeModal();">
      <div class="modal-content" style="border-radius:24px; border:1px solid #e2e8f0; font-family:'Plus Jakarta Sans',sans-serif; box-shadow:0 25px 60px -15px rgba(0,123,255,0.25);">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 style="font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-file-invoice" style="color:#007bff;"></i> Bukti Transaksi Resmi</h3>
        <div class="paid" id="modal-status" style="border-radius:12px; font-weight:700;">Pembayaran Berhasil</div>
        <div class="detail-section" style="border-radius:14px; background:#f8fafc; border:1px solid #e2e8f0; padding:16px;">
          <h4 style="font-weight:700; color:#0f172a; margin-bottom:12px;"><i class="fa-solid fa-credit-card" style="color:#007bff;"></i> Rincian Pembayaran</h4>
          <p style="margin-bottom:8px;"><b>Kode Pembayaran:</b> <span id="modal-kode"></span></p>
          <p style="margin-bottom:8px;"><b>Metode Pembayaran:</b> <span id="modal-metode"></span></p>
          <p style="margin-bottom:8px;"><b>Waktu Pembayaran:</b> <span id="modal-waktu"></span></p>
          <p style="margin-bottom:8px;"><b>Total Pembayaran:</b> <b id="modal-total" style="color:#007bff; font-size:1.05rem;"></b></p>
        </div>
        <center><p class="thanks-note" style="color:#64748b; font-size:0.85rem; margin-top:16px;">Terima kasih telah mempercayai layanan resmi TEKSIKA!</p></center>
      </div>
    </div>

    @include('user._profile_modal')

    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/transaksiuser.js') }}"></script>
</body>
</html>
