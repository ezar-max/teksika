<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Booking — TEKSIKA Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS & Icons -->
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/worker-dropdown.css') }}">
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
        <a href="{{ route('booking') }}" class="menu-item active">
            <img src="{{ asset('images/booking.png') }}" alt="logo" class="icon">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('transaksi') }}" class="menu-item">
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

        @if(($approvalWaiting ?? 0) > 0)
            <div style="margin:16px 32px 0; padding:12px 18px; border:1px solid #fde68a; background:#fffbeb; color:#92400e; border-radius:14px; font-weight:700; display:flex; align-items:center; gap:10px;">
                <i class="fa-solid fa-bell" style="color:#d97706;"></i>
                <span>{{ $approvalWaiting }} persetujuan penawaran dari pelanggan menunggu verifikasi admin.</span>
            </div>
        @endif

        <!-- Booking Title -->
        <div class="booking-title-container">
            <div>
                <h2><i class="fa-solid fa-calendar-check" style="color:#007bff; margin-right:8px;"></i> Manajemen Booking</h2>
                <p>Kelola semua permintaan layanan, penugasan teknisi, dan jadwal perbaikan.</p>
            </div>
            <div>
                <form action="{{ route('booking.export.pdf') }}" method="GET" target="_blank" style="margin:0;">
                    <button type="submit" class="btn-export-pdf">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Export Laporan PDF</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card summary-blue">
                <div>
                    <div class="summary-title">Total Permintaan</div>
                    <div class="summary-value">{{ $totalBooking }}</div>
                </div>
                <div class="summary-icon">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
            <div class="summary-card summary-orange">
                <div>
                    <div class="summary-title">Menunggu Konfirmasi</div>
                    <div class="summary-value">{{ $waitingBooking }}</div>
                </div>
                <div class="summary-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="filter-search-container">
            <div class="filter-search-row">
                <input type="text" id="searchBookingAdmin" placeholder="Cari pemesan, kode booking, atau layanan...">
                <div class="filter-buttons" role="group" aria-label="Filter status booking">
                    <button onclick="filterBooking('all')" id="btn-all" class="active">Semua</button>
                    <button onclick="filterBooking('waiting')" id="btn-waiting">Menunggu</button>
                    <button onclick="filterBooking('confirmed')" id="btn-confirmed">Ditugaskan</button>
                    <button onclick="filterBooking('ongoing')" id="btn-ongoing">Berlangsung</button>
                    <button onclick="filterBooking('done')" id="btn-done">Selesai</button>
                </div>
            </div>
        </div>

        <!-- Booking List -->
        <div class="booking-list">
            @forelse($requests as $req)
            @php
                $workflowLabels = [
                    'menunggu' => 'Menunggu Pemeriksaan',
                    'menunggu_persetujuan' => 'Menunggu Persetujuan User',
                    'persetujuan_dikirim' => 'Persetujuan User: Perlu Verifikasi',
                    'dikonfirmasi' => 'Tukang Ditugaskan',
                    'dikerjakan' => 'Sedang Dikerjakan',
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak',
                    'dibatalkan' => 'Dibatalkan',
                ];
                $userAvatar = !empty($req->user?->avatar) ? asset('storage/'.$req->user->avatar) : asset('images/userabu.png');
            @endphp
            <div class="booking-card js-booking" id="booking-card-{{ $req->id_request }}" data-workflow-status="{{ $req->status }}">
                <div class="booking-card-left">
                    <img src="{{ $userAvatar }}" class="booking-avatar" alt="avatar {{ $req->user?->name ?? 'User' }}" onerror="this.onerror=null;this.src='{{ asset('images/userabu.png') }}';">
                    <div>
                        <div class="booking-user">
                            {{ $req->user?->name ?? 'Pengguna' }}
                            @if($req->worker)
                                <br><small><i class="fa-solid fa-user-gear"></i> Mitra: {{ $req->worker->name }}</small>
                            @endif
                        </div>
                        <div class="booking-meta">
                            <span class="booking-service">{{ $req->layanan }}</span> &bull;
                            <span class="booking-code">{{ $req->kode_booking ?? '-' }}</span>
                        </div>
                        <div class="booking-date">
                            <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($req->created_at)->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                </div>

                <div class="booking-card-right">
                    @if($req->offers->count())
                        <div class="booking-price">Mulai Rp {{ number_format($req->offers->min('total_estimasi'),0,',','.') }}</div>
                    @elseif($req->status_admin === 'diterima')
                        <div class="booking-price">Rp {{ number_format($req->estimasi_total ?? $req->jumlah_pembayaran ?? 0,0,',','.') }}</div>
                    @else
                        <div class="booking-price" style="color:#64748b; font-size:0.9rem;">Belum ada penawaran</div>
                    @endif

                    <div class="booking-status {{
                        in_array($req->status, ['dikonfirmasi', 'dikerjakan', 'selesai'], true) ? 'status-confirmed' : (
                            in_array($req->status, ['ditolak', 'dibatalkan'], true) ? 'status-rejected' : 'status-waiting'
                        )
                    }}">
                        {{ $workflowLabels[$req->status] ?? 'Menunggu Pemeriksaan' }}
                    </div>

                    <div class="action-btns">
                        @if(in_array($req->status, ['menunggu', 'menunggu_persetujuan'], true) && $req->status_admin === 'belum_diterima')
                            <form action="{{ route('booking.tolak', $req->id_request) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menolak pesanan ini?');">
                                @csrf
                                <button type="submit" class="btn-reject">Tolak</button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.booking.show', $req->id_request) }}" class="btn-detail">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Penawaran & Detail</span>
                        </a>
                    </div>
                </div>
            </div>
            @empty
                <div style="padding:48px 24px; background:#fff; border:1.5px dashed #cbd5e1; border-radius:18px; text-align:center; color:#64748b;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size:3rem; color:#94a3b8; margin-bottom:12px;"></i>
                    <p style="font-weight:700; font-size:1.1rem; color:#0f172a; margin:0 0 6px 0;">Belum Ada Permintaan Booking</p>
                    <p style="font-size:0.88rem; margin:0;">Permintaan layanan baru dari pelanggan akan muncul secara otomatis di halaman ini.</p>
                </div>
            @endforelse
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

    // Booking Filters
    let activeBookingFilter = 'all';

    function applyBookingFilters() {
        const keyword = (document.getElementById('searchBookingAdmin')?.value || '').trim().toLowerCase();
        document.querySelectorAll('.booking-card').forEach(card => {
            const status = card.dataset.workflowStatus;
            const matchesStatus = activeBookingFilter === 'all'
                || (activeBookingFilter === 'waiting' && ['menunggu', 'menunggu_persetujuan', 'persetujuan_dikirim'].includes(status))
                || (activeBookingFilter === 'confirmed' && status === 'dikonfirmasi')
                || (activeBookingFilter === 'ongoing' && status === 'dikerjakan')
                || (activeBookingFilter === 'done' && status === 'selesai');
            const text = card.innerText.toLowerCase();
            card.style.display = matchesStatus && text.includes(keyword) ? '' : 'none';
        });
    }

    const searchBookingAdmin = document.getElementById('searchBookingAdmin');
    if (searchBookingAdmin) {
        searchBookingAdmin.addEventListener('input', applyBookingFilters);
    }

    function filterBooking(status) {
        activeBookingFilter = status;
        applyBookingFilters();
        
        document.querySelectorAll('.filter-buttons button').forEach(btn => btn.classList.remove('active'));
        const btn = document.getElementById('btn-' + status);
        if (btn) btn.classList.add('active');
    }
    </script>
</body>
</html>
