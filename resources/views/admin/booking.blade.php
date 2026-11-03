<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/worker-dropdown.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <!-- Search dihapus sesuai permintaan, search booking tetap -->
            <div class="user-info">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>

        <!-- Booking Management Title -->
        <div class="booking-title-container">
            <h2>Booking Management</h2>
            <p>Kelola semua booking dan jadwal tukang</p>
        </div>

        <!-- Booking Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card summary-blue">
                <div>
                    <div class="summary-title">Total booking</div>
                    <div class="summary-value">{{ $totalBooking }}</div>
                </div>
                <div class="summary-icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
            <div class="summary-card summary-orange">
                <div>
                    <div class="summary-title">Menunggu konfirmasi</div>
                    <div class="summary-value">{{ $waitingBooking }}</div>
                </div>
                <div class="summary-icon">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
            </div>
        </div>

        <!-- Booking Filter & Search -->
        <div class="filter-search-container">
            <div class="filter-search-row">
                <input type="text" id="searchBookingAdmin" placeholder="Cari booking...">
                <div class="filter-buttons">
                    <button onclick="filterBooking('all')" id="btn-all" class="active">Semua</button>
                    <button onclick="filterBooking('waiting')" id="btn-waiting" class="active">Menunggu</button>
                    <button onclick="filterBooking('confirmed')" id="btn-confirmed" class="active">Dikonfirmasi</button>
                    <button onclick="filterBooking('ongoing')" id="btn-ongoing" class="active">Berlangsung</button>
                    <button onclick="filterBooking('done')" id="btn-done" class="active">Selesai</button>
                </div>
                <div>
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </div>
            </div>
            <!-- Tambahkan tombol export di sini -->
            <div style="margin-top: 16px;">
                <form action="{{ route('booking.export.pdf') }}" method="GET" target="_blank">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </button>
                </form>
            </div>
        </div>

        <!-- Booking List -->
        <div class="booking-list">
            @foreach($requests as $req)
            <div class="booking-card js-booking" id="booking-card-{{ $req->id_request }}">
                <div class="booking-card-left">
                    @php($userAvatar = !empty($req->user->avatar) ? asset('storage/'.$req->user->avatar) : asset('images/avatar.png'))
                    <img src="{{ $userAvatar }}" class="booking-avatar" alt="avatar">
                    <div>
                        <div class="booking-user">
                            {{ $req->user->name ?? '-' }}
                            @if($req->worker)
                                <br><small>Tukang: {{ $req->worker->name }}</small>
                            @endif
                        </div>
                        <div class="booking-meta">
                            <span class="booking-service">{{ $req->layanan }}</span> &nbsp;|&nbsp;
                            <span class="booking-code">Kode: <b>{{ $req->kode_booking ?? '-' }}</b></span>
                        </div>
                        <div class="booking-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d-m-Y') }}</div>
                    </div>
                </div>
                <div class="booking-card-right">
                    <div class="booking-price">Rp{{ number_format($req->jumlah_pembayaran,0,',','.') }}</div>
                    <div class="booking-status {{
                        $req->status_admin === 'diterima' ? 'status-confirmed' : (
                            $req->status_admin === 'ditolak' ? 'status-rejected' : (
                                $req->status_admin === 'dibatalkan' ? 'status-cancelled' : 'status-waiting'
                            )
                        )
                    }}">
                        {{
                            $req->status_admin === 'diterima' ? 'Dikonfirmasi' : (
                                $req->status_admin === 'ditolak' ? 'Ditolak' : (
                                    $req->status_admin === 'dibatalkan' ? 'Dibatalkan' : 'Menunggu'
                                )
                            )
                        }}
                    </div>
                    <div class="action-btns">
                        @if($req->status_admin == 'belum_diterima')
                            <form action="{{ route('booking.konfirmasi', $req->id_request) }}" method="POST" style="display:inline;">
                                @csrf
                                <select name="id_worker" class="worker-dropdown" required>
                                    <option value="">Pilih Worker</option>
                                    @foreach($workers as $worker)
                                        <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                    @endforeach
                                </select>
                                @if(($req->metode_pembayaran ?? '') !== 'COD')
                                    <input type="number" name="jumlah_pembayaran" min="0" step="1000" placeholder="Masukkan jumlah (Rp)" value="{{ old('jumlah_pembayaran', $req->jumlah_pembayaran) }}" style="margin-left:8px; padding:6px 10px; border:1px solid #ddd; border-radius:6px;" required>
                                @else
                                    <small style="margin:6px 8px; color:#777;">Metode: COD (tanpa input harga)</small>
                                @endif
                                <button type="submit" class="btn-accept">Konfirmasi</button>
                            </form>
                            <form action="{{ route('booking.tolak', $req->id_request) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-reject">Tolak</button>
                            </form>
                        @endif
                        <a href="javascript:void(0)" 
                           class="btn-detail" 
                           onclick="openBookingDetailModal({{ $req->id_request }})">Detail</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Modal Detail Booking -->
        <!-- Modal Detail Booking -->
<div id="bookingDetailModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:16px; padding:32px 28px; box-shadow:0 8px 32px rgba(0,0,0,0.12); text-align:left; max-width:400px; margin:auto; position:relative; max-height:80vh; overflow-y:auto;">
    <span style="position:absolute; top:12px; right:18px; font-size:1.5rem; cursor:pointer;" onclick="closeBookingDetailModal()">&times;</span>
    <h2>Detail Booking</h2>
    <div id="bookingDetailContent"></div>
    <button onclick="closeBookingDetailModal()" style="margin-top: 16px; padding: 8px 16px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">Tutup</button>
  </div>
</div>

<script>
// Fungsi untuk menutup modal
function closeBookingDetailModal() {
    document.getElementById('bookingDetailModal').style.display = 'none';
}

// Fungsi untuk membuka modal detail booking
function openBookingDetailModal(id) {
    // Ambil data booking dari variabel JSON yang sudah di-set dari Blade
    const bookings = @json($requests);
    const booking = bookings.find(b => b.id_request === id);
    const content = document.getElementById('bookingDetailContent');
    
    if (booking) {
        // Format tanggal ke format Indonesia
        const tanggal = new Date(booking.created_at).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        // Format jumlah pembayaran dengan format Rupiah
        const jumlahPembayaran = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(booking.jumlah_pembayaran);
        
        // Siapkan foto kerusakan (tampilkan langsung sesuai yang dikirim user)
        const storageBase = "{{ asset('storage') }}";
        const fotos = ((booking.foto || '') + '').split(',').map(s => s.trim()).filter(Boolean);
        let fotoHtml = '';
        if (fotos.length) {
            fotoHtml = fotos.map(raw => {
                // Normalisasi path (gantikan backslash ke slash dan hapus leading slash)
                let p = (raw || '').replace(/\\\\/g, '/').replace(/^\/+/, '');
                // Jika sudah URL penuh, gunakan langsung; jika tidak, prefix dengan storage
                const url = (/^https?:\/\//i).test(p) ? p : `${storageBase}/${p}`;
                return `<div style="margin:10px 0;"><img src="${url}" alt="Foto kerusakan" style="width:100%;height:auto;border-radius:8px;border:1px solid #eee;"/></div>`;
            }).join('');
        } else {
            fotoHtml = '<div style="color:#777;">Tidak ada foto</div>';
        }
        
        // Buat HTML untuk detail booking
        const detail = `
            <div style="margin-bottom:16px;">
                <p><strong>Nama Pemesan:</strong> ${booking.user ? booking.user.name : '-'}</p>
                <p><strong>Tukang:</strong> ${booking.worker ? booking.worker.name : 'Belum ditentukan'}</p>
                <p><strong>Layanan:</strong> ${booking.layanan}</p>
                <p><strong>Kode Booking:</strong> ${booking.kode_booking}</p>
                <p><strong>Tanggal:</strong> ${tanggal}</p>
                <p><strong>Jumlah Pembayaran:</strong> ${jumlahPembayaran}</p>
                <p><strong>Status:</strong> ${
                    booking.status_admin === 'diterima' ? 'Dikonfirmasi' : (
                        booking.status_admin === 'ditolak' ? 'Ditolak' : (
                            booking.status_admin === 'dibatalkan' ? 'Dibatalkan' : 'Menunggu'
                        )
                    )
                }</p>
                <p><strong>Metode Pembayaran:</strong> ${booking.metode_pembayaran || '-'}</p>
                <p><strong>Lokasi:</strong> ${booking.lokasi || '-'}</p>
                <div><strong>Detail:</strong></div>
                <div style="white-space:pre-wrap; word-break:break-word;">${booking.detail || '-'}</div>
                <p><strong>Foto Kerusakan:</strong></p>
                ${fotoHtml}
            </div>
        `;
        content.innerHTML = detail;
    } else {
        content.innerHTML = '<p>Detail booking tidak ditemukan.</p>';
    }
    
    // Tampilkan modal
    document.getElementById('bookingDetailModal').style.display = 'flex';
}

// Hapus fungsi showBookingDetail yang tidak digunakan
// Hapus fungsi closeBookingDetailModal yang duplikat

// Event listener untuk search booking admin
const searchBookingAdmin = document.getElementById('searchBookingAdmin');
if (searchBookingAdmin) {
    searchBookingAdmin.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.booking-card').forEach(function(card) {
            const nama = card.querySelector('.booking-user')?.textContent.toLowerCase() || '';
            const layanan = card.querySelector('.booking-service')?.textContent.toLowerCase() || '';
            const tanggal = card.querySelector('.booking-date')?.textContent.toLowerCase() || '';
            if (
                nama.includes(keyword) ||
                layanan.includes(keyword) ||
                tanggal.includes(keyword)
            ) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Fungsi filter booking
function filterBooking(status) {
    const cards = document.querySelectorAll('.booking-card');
    cards.forEach(card => {
        if (status === 'all') {
            card.style.display = '';
        } else {
            // Cek status berdasarkan class yang ada di card
            const statusClass = card.querySelector('.booking-status').classList.contains('status-confirmed') ? 'confirmed' : 'waiting';
            if (statusClass === status || 
                (status === 'waiting' && statusClass === 'waiting') ||
                (status === 'confirmed' && statusClass === 'confirmed')) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        }
    });
    
    // Highlight tombol aktif
    document.querySelectorAll('.filter-buttons button').forEach(btn => btn.classList.remove('active'));
    document.getElementById('btn-' + status).classList.add('active');
}

// Event listener untuk menu toggle
const menuToggle = document.getElementById('menuToggle');
if (menuToggle) {
    menuToggle.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar.style.display === 'none' || sidebar.style.display === '') {
            sidebar.style.display = 'block';
        } else {
            sidebar.style.display = 'none';
        }
    });
}

// Hapus event listener duplikat dan yang tidak diperlukan
</script>