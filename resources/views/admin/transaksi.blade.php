<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaction Management Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <img src="{{ asset('images/logo-putih.png') }}" alt="Logo" class="sidebar-logo">

        <a href="{{ route('dashboard_admin') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('booking') }}" class="menu-item">
            <img src="{{ asset('images/booking.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('transaksi') }}" class="menu-item">
            <img src="{{ asset('images/pembayaran.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Pembayaran</span>
        </a>
        <a href="{{ route('worker') }}" class="menu-item">
            <img src="{{ asset('images/worker.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Worker</span>
        </a>
        <a href="{{ route('chat') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Chat</span>
        </a>

        <div class="logout">
            <a href="{{ route('menu_login') }}" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
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
 
        <!-- conten -->
        <!-- Transaction Management Title -->
        <div class="transaction-title-container">
            <h1 class="transaction-title">Transaction Management</h1>
            <div class="transaction-subtitle">Monitor semua transaksi dan pembayaran</div>
        </div>

        <!-- Transaction Summary Cards -->
        @php
            $completedCount = ($transaksis ?? collect())->where('status', 'success')->count();
            $processingCount = ($transaksis ?? collect())->where('status', 'pending')->count();
        @endphp
        <div class="transaction-summary-cards">
            <div class="transaction-summary-card completed">
                <div class="summary-icon-bg purple">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="summary-label">Completed</div>
                    <div class="summary-value">{{ $completedCount }}</div>
                </div>
            </div>
            <div class="transaction-summary-card processing">
                <div class="summary-icon-bg orange">
                    <i class="bi bi-clock"></i>
                </div>
                <div>
                    <div class="summary-label">Processing</div>
                    <div class="summary-value">{{ $processingCount }}</div>
                </div>
            </div>
        </div>

        <!-- Transaction Search -->
        <div class="transaction-search-container">
            <input type="text" class="transaction-search-input" placeholder="Search">
        </div>

        <!-- Transaction List -->
        <div class="transaction-list">
            @foreach($transaksis as $trx)
            <div class="transaction-card" 
                 data-kode="{{ $trx->kode_pembayaran ?? '-' }}"
                 data-nama="{{ $trx->nama_pembayar ?? '-' }}"
                 data-email="{{ $trx->email_pembayar ?? '-' }}"
                 data-metode="{{ $trx->metode_pembayaran ?? '-' }}"
                 data-status="{{ $trx->status ?? '-' }}"
                 data-jumlah="{{ number_format($trx->jumlah ?? 0,0,',','.') }}">
                <div class="transaction-info">
                    <div class="transaction-code">{{ $trx->kode_pembayaran ?? '-' }}</div>
                    <div>
                        {{ $trx->nama_pembayar ?? '-' }} • {{ $trx->email_pembayar ?? '-' }}
                    </div>
                    <div>Payment: {{ $trx->metode_pembayaran ?? '-' }}</div>
                    <div>Status: {{ $trx->status ?? '-' }}</div>
                                    </div>
                <div class="transaction-status">
                    <div class="transaction-price">Rp {{ number_format($trx->jumlah ?? 0,0,',','.') }}</div>
                    <div class="transaction-actions">
                        <button class="transaction-menu-btn"><i class="bi bi-three-dots"></i></button>
                        <button class="btn-detail-trx">Detail</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Simple search filter JS -->
       <script src="{{ asset('js/sidebar.js') }}"></script>
       <script src="{{ asset('js/popup.js') }}"></script>
       <script src="{{ asset('js/transaksi.js') }}"></script>
<!-- Modal Detail Transaksi -->
<div id="trxDetailModal" class="trx-detail-modal">
  <div class="trx-detail-content">
    <span class="trx-detail-close">&times;</span>
    <h3>Detail Transaksi</h3>
    <div id="trxDetailBody"></div>
    <div class="trx-detail-footer">
      <button class="trx-detail-btn-close">Tutup</button>
    </div>
  </div>
</div>
</body>
</html>
