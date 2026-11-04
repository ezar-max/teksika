<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Transaksi — TEKSIKA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS & Icons -->
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo-wrap">
            <img src="{{ asset('images/logo-putih.png') }}" alt="Logo TEKSIKA" class="sidebar-logo">
        </div>

        <a href="{{ route('admin.home') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="logo" class="icon">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('booking') }}" class="menu-item">
            <img src="{{ asset('images/booking.png') }}" alt="logo" class="icon">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('transaksi') }}" class="menu-item active">
            <img src="{{ asset('images/pembayaran.png') }}" alt="logo" class="icon">
            <span class="menu-text">Pembayaran</span>
        </a>
        <a href="{{ route('worker') }}" class="menu-item">
            <img src="{{ asset('images/worker.png') }}" alt="logo" class="icon">
            <span class="menu-text">Worker</span>
        </a>
        <a href="{{ route('admin.pricing.index') }}" class="menu-item">
            <i class="fa-solid fa-sliders"></i>
            <span class="menu-text">Aturan Tarif</span>
        </a>
        <a href="{{ route('chat') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon">
            <span class="menu-text">Chat</span>
        </a>

        <!-- Logout Tunggal Admin -->
        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main" id="main">
        <!-- Header -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle">
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
 
        <!-- Title & Subtitle -->
        <div class="transaction-title-container">
            <h1 class="transaction-title"><i class="fa-solid fa-money-check-dollar" style="color:#007bff; margin-right:8px;"></i> Manajemen Transaksi</h1>
            <div class="transaction-subtitle">Monitor dan verifikasi seluruh pembayaran invoice dari customer</div>
        </div>

        <!-- Transaction Summary Cards -->
        @php
            $completedCount = ($transaksis ?? collect())->where('status', 'success')->count();
            $processingCount = ($transaksis ?? collect())->where('status', 'pending')->count();
        @endphp
        <div class="transaction-summary-cards">
            <div class="transaction-summary-card">
                <div class="summary-icon-bg purple">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="summary-label">Pembayaran Berhasil</div>
                    <div class="summary-value">{{ $completedCount }}</div>
                </div>
            </div>
            <div class="transaction-summary-card">
                <div class="summary-icon-bg orange">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <div class="summary-label">Menunggu Verifikasi</div>
                    <div class="summary-value">{{ $processingCount }}</div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="transaction-search-container">
            <input type="text" class="transaction-search-input" id="searchTransactionInput" placeholder="Cari kode invoice, nama pelanggan, atau metode pembayaran...">
        </div>

        <!-- Transaction List -->
        <div class="transaction-list">
            @forelse($transaksis as $trx)
            @php
                $st = strtolower($trx->status ?? '');
                $statusPillClass = $st === 'success' || $st === 'berhasil' ? 'status-success' : ($st === 'pending' ? 'status-pending' : 'status-failed');
                $statusPillLabel = $st === 'success' || $st === 'berhasil' ? 'Lunas / Berhasil' : ($st === 'pending' ? 'Menunggu Konfirmasi' : 'Dibatalkan / Gagal');
            @endphp
            <div class="transaction-card js-trx-card" 
                 data-kode="{{ $trx->kode_pembayaran ?? '-' }}"
                 data-nama="{{ $trx->nama_pembayar ?? '-' }}"
                 data-email="{{ $trx->email_pembayar ?? '-' }}"
                 data-metode="{{ $trx->metode_pembayaran ?? '-' }}"
                 data-status="{{ $trx->status ?? '-' }}"
                 data-jumlah="{{ number_format($trx->jumlah ?? 0,0,',','.') }}">
                <div class="transaction-info">
                    <div class="transaction-code">
                        <i class="fa-solid fa-receipt"></i>
                        <span>{{ $trx->kode_pembayaran ?? '-' }}</span>
                    </div>
                    <div class="transaction-payer">
                        {{ $trx->nama_pembayar ?? '-' }}
                        <span style="font-weight:400; color:#64748b; font-size:0.85rem;">&bull; {{ $trx->email_pembayar ?? '-' }}</span>
                    </div>
                    <div style="margin-top:4px;">
                        <span class="transaction-meta-pill"><i class="fa-solid fa-wallet"></i> {{ $trx->metode_pembayaran ?? 'Manual' }}</span>
                        <span class="status-pill {{ $statusPillClass }}">{{ $statusPillLabel }}</span>
                    </div>
                </div>
                <div class="transaction-status">
                    <div class="transaction-price">Rp {{ number_format($trx->jumlah ?? 0,0,',','.') }}</div>
                    <div class="transaction-actions">
                        <button type="button" class="btn-detail-trx" onclick="viewTrxDetail(this.closest('.transaction-card'))">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Detail Transaksi</span>
                        </button>
                    </div>
                </div>
            </div>
            @empty
                <div style="padding:48px 24px; background:#fff; border:1.5px dashed #cbd5e1; border-radius:18px; text-align:center; color:#64748b;">
                    <i class="fa-solid fa-receipt" style="font-size:3rem; color:#94a3b8; margin-bottom:12px;"></i>
                    <p style="font-weight:700; font-size:1.1rem; color:#0f172a; margin:0 0 6px 0;">Belum Ada Riwayat Transaksi</p>
                    <p style="font-size:0.88rem; margin:0;">Semua riwayat pembayaran dan bukti transfer dari customer akan tersimpan rapi di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div id="trxDetailModal" class="trx-detail-modal" onclick="if(event.target===this) closeTrxModal();">
      <div class="trx-detail-content">
        <span class="trx-detail-close" onclick="closeTrxModal()">&times;</span>
        <h3><i class="fa-solid fa-file-invoice-dollar" style="color:#007bff;"></i> Rincian Pembayaran</h3>
        <div id="trxDetailBody"></div>
        <div class="trx-detail-footer">
          <button type="button" class="trx-detail-btn-close" onclick="closeTrxModal()">Tutup</button>
        </div>
      </div>
    </div>

    <!-- Admin Profile Modal -->
    @include('admin._profile_modal')

    <!-- Modal Logout Tunggal Admin -->
    @include('admin._logout_modal')

    <!-- Scripts -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
    function openAdminProfileModal(){
        var el = document.getElementById('adminProfileModal');
        if(el){ el.style.display = 'flex'; }
    }
    function closeAdminProfileModal(){
        var el = document.getElementById('adminProfileModal');
        if(el){ el.style.display = 'none'; }
    }
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

    // Modal Transaksi Detail
    function viewTrxDetail(card) {
        if (!card) return;
        const d = card.dataset;
        const body = document.getElementById('trxDetailBody');
        body.innerHTML = `
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:14px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="color:#64748b; font-size:0.84rem;">Kode Invoice:</span>
                    <strong style="color:#007bff;">${d.kode}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="color:#64748b; font-size:0.84rem;">Nama Pembayar:</span>
                    <strong>${d.nama}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="color:#64748b; font-size:0.84rem;">Email Pembayar:</span>
                    <span>${d.email}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="color:#64748b; font-size:0.84rem;">Metode Bayar:</span>
                    <strong style="text-transform:uppercase;">${d.metode}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="color:#64748b; font-size:0.84rem;">Status Transaksi:</span>
                    <span style="font-weight:700; text-transform:uppercase;">${d.status}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-top:10px; border-top:1px dashed #cbd5e1; margin-top:10px;">
                    <span style="font-weight:700;">Total Dibayar:</span>
                    <strong style="font-size:1.15rem; color:#10b981;">Rp ${d.jumlah}</strong>
                </div>
            </div>
            <div style="font-size:0.8rem; color:#64748b; text-align:center;">
                <i class="fa-solid fa-shield-halved" style="color:#007bff;"></i> Transaksi terverifikasi dalam sistem pembayaran resmi TEKSIKA.
            </div>
        `;
        document.getElementById('trxDetailModal').style.display = 'flex';
    }

    function closeTrxModal() {
        document.getElementById('trxDetailModal').style.display = 'none';
    }

    // Client-side Transaction Search
    const searchInput = document.getElementById('searchTransactionInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('.js-trx-card').forEach(card => {
                const text = card.innerText.toLowerCase();
                card.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }
    </script>
</body>
</html>
