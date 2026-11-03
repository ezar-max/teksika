<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home User</title>
<link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
<link rel="stylesheet" href="{{ asset('css/transaksiuser.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
<link rel="stylesheet" href="{{ asset('css/notif-dashboarduser.css') }}">
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
<span>{{ Auth::user()->name ?? 'Pengguna' }} | {{ Auth::user()->email ?? '' }}</span>
<span class="role">{{ Auth::user()->role ?? 'Customer' }}</span>
</div>
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
<input id="serviceSearch" type="text" placeholder="Cari layanan..." aria-label="Cari layanan">
<div class="user-info">
    <i class="fa-solid fa-bell" id="notifBell" style="cursor:pointer;"></i>
    @php($avatarUrlHeader = !empty($user->avatar) ? asset('storage/'.$user->avatar) : asset('images/avatar.png'))
    <div class="profile" onclick="openProfileModal()" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
        <img id="avatarHeader" src="{{ $avatarUrlHeader }}" alt="Avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
        <span>{{ $user->name }}</span>
    </div>
</div>
<!-- Dropdown Notifikasi Booking User -->
<div class="notif-dropdown" id="notifDropdown">
    <span id="notifCloseX" style="position:absolute; top:8px; right:12px; font-size:18px; color:#888; cursor:pointer; z-index:100;">&times;</span>
    <div class="notif-header">Notifikasi</div>
    <div id="notifList" style="max-height:320px; overflow-y:auto;"></div>
</div>
</div>
<!-- Modal Profil -->
<div id="profileModal" class="modal-profile">
<div class="modal-profile-content">
<span class="modal-profile-close" onclick="closeProfileModal()">&times;</span>
<h2>Profil Saya</h2>
<p class="subtitle">Kelola informasi akun anda</p>
<div class="profile-info">
@php($avatarUrlProfile = !empty($user->avatar) ? asset('storage/'.$user->avatar) : asset('images/avatar.png'))
<img src="{{ $avatarUrlProfile }}" alt="Avatar" class="profile-icon" style="width:96px;height:96px;border-radius:50%;object-fit:cover;margin-bottom:10px;">
<h3>{{ $user->name }}</h3>
<p>{{ $user->email }}</p>
@if(isset($user->phone))
<p>{{ $user->phone }}</p>
@endif
</div>
<div class="profile-stats">
<div class="stat">
<h3>{{ $totalBooking }}</h3>
<p>Total Booking</p>
</div>
<div class="stat">
<h3>{{ $totalTransaksi ?? 0 }}</h3>
<p>Total Transaksi</p>
</div>
<div class="stat">
<h3>{{ $user->created_at->format('M Y') }}</h3>
<p>Member Sejak</p>
</div>
</div>
<p class="member-info">
Member TEKSIKA - platform jasa tukang terpercaya dengan layanan admin profesional
</p>
<button class="btn-logout" onclick="openLogoutModal()">Keluar</button>
<button class="btn-edit" onclick="openEditProfileModal()">Edit Profil</button>
</div>
</div>
<!-- Modal Edit Profil -->
<div id="editProfileModal" class="modal-profile" style="display:none;">
<div class="edit-card">
<span class="modal-profile-close" onclick="closeEditProfileModal()" aria-label="Tutup">&times;</span>
<div class="edit-header">
<div class="avatar"><i class="fa fa-user"></i></div>
<div>
<h3 class="edit-title">Edit Profil</h3>
<p class="edit-sub">Perbarui informasi akun Anda</p>
</div>
</div>
<form action="{{ route('user.updateProfile') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="edit-grid">
<div class="edit-field" style="grid-column:1/-1;">
<label class="edit-label" for="avatar">Foto Profil</label>
<div style="display:flex; align-items:center; gap:12px;">
<div>
@php($avatarUrl = !empty($user->avatar) ? asset('storage/'.$user->avatar) : asset('images/avatar.png'))
<img id="avatarPreview" src="{{ $avatarUrl }}" alt="Avatar" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:1px solid #e5e7eb;">
</div>
<div>
<input id="avatar" type="file" name="avatar" accept="image/*">
<div style="font-size:12px;color:#6b7280;">Format: JPG/PNG, maks 5MB</div>
</div>
</div>
</div>
<div class="edit-field">
<label class="edit-label" for="name">Nama</label>
<div class="input-wrap">
<i class="fa fa-user icon"></i>
<input id="name" type="text" class="edit-input" name="name" value="{{ $user->name }}" required>
</div>
</div>
<div class="edit-field">
<label class="edit-label" for="email">Email</label>
<div class="input-wrap">
<i class="fa fa-envelope icon"></i>
<input id="email" type="email" class="edit-input" name="email" value="{{ $user->email }}" required>
</div>
</div>
@if(isset($user->phone))
<div class="edit-field" style="grid-column:1/-1;">
<label class="edit-label" for="phone">Nomor Telepon</label>
<div class="input-wrap">
<i class="fa fa-phone icon"></i>
<input id="phone" type="text" class="edit-input" name="phone" value="{{ $user->phone }}" placeholder="Opsional">
</div>
</div>
@endif
</div>
<div class="edit-actions">
<button type="button" class="btn-outline" onclick="closeEditProfileModal()">Batal</button>
<button type="submit" class="btn-primary">Simpan</button>
</div>
</form>
<script>
(function(){
const input = document.getElementById('avatar');
const img = document.getElementById('avatarPreview');
if (input && img) {
input.addEventListener('change', function(){
const file = this.files && this.files[0];
if (!file) return;
const reader = new FileReader();
reader.onload = function(e){
  img.src = e.target.result;
  var hdr = document.getElementById('avatarHeader');
  if (hdr) hdr.src = e.target.result;
};
reader.readAsDataURL(file);
});
}
})();
</script>
</div>
</div>
<!-- Welcome Section -->
<div class="welcome">
<h1>SELAMAT DATANG DI TEKSIKA!</h1>
<p>Platform jasa tukang terpercaya yang akan mencarikan tukang terbaik untuk Anda</p>
</div>
<!-- Cara Kerja Card -->
<div class="info-card">
<div class="info-text">
<i class="fa-solid fa-circle-info"></i>
<strong>Cara Kerja Teksika</strong>
<span>Proses mudah dan cepat - hanya 3 langkah!</span>
</div>
<button id="lihatDetailBtn">Lihat detail</button>
</div>
<!-- POPUP -->
<div id="popupDetail" class="popup">
<div class="popup-content custom-steps-popup">
<span class="close-btn" id="closePopup">&times;</span>
<h2 class="popup-title">
<i class="fa-solid fa-wrench" style="color:#6c6c6c"></i> CARA KERJA TEKSIKA
</h2>
<div class="steps-container">
<div class="step">
<div class="step-icon icon-green"><i class="fa-solid fa-comments"></i></div>
<h3>1. Request Layanan</h3>
<p>
Pilih jenis layanan yang dibutuhkan dari kategori yang tersedia, atau chat langsung dengan admin kami untuk konsultasi.
</p>
</div>
<div class="step">
<div class="step-icon icon-blue"><i class="fa-solid fa-user"></i></div>
<h3>2. Admin Carikan Tukang</h3>
<p>
Admin profesional kami akan mencarikan tukang terbaik dan berpengalaman sesuai kebutuhan dan lokasi Anda.
</p>
</div>
<div class="step">

<div class="step-icon icon-purple"><i class="fa-solid fa-check"></i></div>
<h3>3. Tukang Datang</h3>
<p>
Tukang terpilih akan datang sesuai jadwal yang disepakati dan menyelesaikan pekerjaan dengan profesional.
</p>
</div>
</div>

<div class="popup-buttons">
<a href="{{route ('request') }}"><button class="main-btn">+ Mulai Request</button></a>
<a href="{{ route('chatuser') }}" class="main-btn">Chat Dengan Admin</a>
</div>
</div>
</div>
<!-- Image Carousel Section -->
<div class="carousel-section">
<div class="carousel-container">
<div class="carousel-track">
<div class="carousel-slide active">
<img src="{{ asset('images/tukanginn.jpg') }}" alt="Tukanginn">
<div class="carousel-caption">
<h3>Layanan Tukang Profesional</h3>
<p>Layanan bangun rumah dan renovasi dengan hasil berkualitas Aman, Cepat , Berkualitas, Terpercaya</p>
</div>
</div>
<div class="carousel-slide">
<img src="{{ asset('images/benerinlistrikk.jpeg') }}" alt="Tukang Listrik">
<div class="carousel-caption">
<h3>Instalasi Listrik</h3>
<p>Percayakan instalasi listrik pada ahlinya dijamin aman, hemat, dan terpercaya</p>
</div>
</div>
<div class="carousel-slide">
<img src="{{ asset('images/ac2.jpg') }}" alt="Tukang AC">
<div class="carousel-caption">
<h3>Perawatan AC Berkualitas</h3>
<p>Nikmati udara sejuk tanpa khawatir – perawatan AC dijamin hemat energi & tahan lama</p>
</div>
</div>
<div class="carousel-slide">
<img src="{{ asset('images/benerinpipaa.jpg') }}" alt="Tukang pipa">
<div class="carousel-caption">
<h3>Solusi Pipa Terpercaya</h3>
<p>Instalasi dan perbaikan pipa oleh ahli plumbing berpengalaman</p>
</div>
</div>
<div class="carousel-slide">
<img src="{{ asset('images/tukangcatt.jpg') }}" alt="Tukang Cat">
<div class="carousel-caption">
<h3>Pengecatan </h3>
<p>Percayakan pengecatan pada ahlinya dijamin rapi, bersih, dan hasil maksimal</p>
</div>
</div>
</div>

<!-- Tombol Navigasi -->
<button class="carousel-btn prev-btn" id="prevBtn">
<i class="fas fa-chevron-left"></i>
</button>
<button class="carousel-btn next-btn" id="nextBtn">
<i class="fas fa-chevron-right"></i>
</button>

<!-- Indikator -->
<div class="carousel-indicators">
<span class="indicator active" data-slide="0"></span>
<span class="indicator" data-slide="1"></span>
<span class="indicator" data-slide="2"></span>
<span class="indicator" data-slide="3"></span>
<span class="indicator" data-slide="4"></span>
</div>
</div>
</div>
<!-- Services -->
<h2 class="section-title">Layanan Tersedia</h2>
<div class="services">
@php($services = isset($services) ? $services->where('status','aktif') : collect())
@forelse($services as $service)
<div class="service-card">
  @if(!empty($service->gambar))
    <div style="display:flex;align-items:center;justify-content:center;width:60px;height:60px;border-radius:10px;overflow:hidden;margin-bottom:10px;background:none;">
      <img src="{{ asset('uploads/services/'.$service->gambar) }}" alt="{{ $service->nama }}" style="width:60px;height:60px;object-fit:cover;border-radius:10px;display:block;">
    </div>
  @else
    <div class="icon orange" style="display:flex;align-items:center;justify-content:center;width:60px;height:60px;border-radius:10px;overflow:hidden;">
      <i class="fa-solid fa-bolt"></i>
    </div>
  @endif
  <h3 style="margin-bottom:8px;">{{ $service->nama }}</h3>
  @if(!empty($service->deskripsi))
  <div style="color:#444;font-size:.97rem;margin-bottom:10px;line-height:1.5;">
  {{ $service->deskripsi }}
  </div>
  @endif
  @if(isset($service->harga_min) && isset($service->harga_max))
  <div style="font-size:1rem;color:#1856a7;font-weight:600;margin-bottom:12px;">
  Rp {{ number_format($service->harga_min,0,',','.') }} - Rp {{ number_format($service->harga_max,0,',','.') }}
  </div>
  @endif
  <a href="{{ route('request', ['layanan' => $service->nama]) }}">
  <button class="request-btn">+ Mulai Request</button>
  </a>
</div>
@empty
<p>Belum ada layanan tersedia.</p>
@endforelse
</div>
</div>
<script src="{{ asset('js/sidebar.js') }}"></script>
<script src="{{ asset('js/popup.js') }}"></script>
<script>
window.notifRequests = @json($requests ?? []);
</script>
<script src="{{ asset('js/notif-dashboarduser.js') }}"></script>
<script>
function setHeaderBlur(on) {
  var h = document.querySelector('.header');
  if (h) h.style.filter = on ? 'blur(4px)' : '';
}
function openEditProfileModal() {
  document.getElementById('editProfileModal').style.display = 'block';
  setHeaderBlur(true);
}
function closeEditProfileModal() {
  document.getElementById('editProfileModal').style.display = 'none';
  setHeaderBlur(false);
}
function openProfileModal() {
  var el = document.getElementById('profileModal');
  if (el) el.style.display = 'flex';
  setHeaderBlur(true);
}
function closeProfileModal() {
  var el = document.getElementById('profileModal');
  if (el) el.style.display = 'none';
  setHeaderBlur(false);
}
// Fungsi untuk carousel
document.addEventListener('DOMContentLoaded', function() {
// Carousel functionality
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.indicator');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
let currentSlide = 0;

// Function to show a specific slide
function showSlide(index) {
// Hide all slides
slides.forEach(slide => {
slide.classList.remove('active');
});

// Remove active class from all indicators
indicators.forEach(indicator => {
indicator.classList.remove('active');
});

// Show the selected slide
slides[index].classList.add('active');
indicators[index].classList.add('active');

// Update current slide index
currentSlide = index;
}

// Function to show the next slide
function nextSlide() {
let next = (currentSlide + 1) % slides.length;
showSlide(next);
}

// Function to show the previous slide
function prevSlide() {
let prev = (currentSlide - 1 + slides.length) % slides.length;
showSlide(prev);
}

// Add click event to indicators
indicators.forEach((indicator, index) => {
indicator.addEventListener('click', () => {
showSlide(index);
});
});

// Add click event to navigation buttons
if (prevBtn) {
prevBtn.addEventListener('click', prevSlide);
}

if (nextBtn) {
nextBtn.addEventListener('click', nextSlide);
}

// Auto-advance slides every 5 seconds
setInterval(nextSlide, 5000);

// Search layanan (filter kartu layanan) — tampilkan banner hasil, jarak dari header, dan pesan kosong yang rapi
const searchInput = document.getElementById('serviceSearch');
if (searchInput) {
  searchInput.addEventListener('input', function(){
    const qRaw = this.value.trim();
    const q = qRaw.toLowerCase();
    const servicesSection = document.querySelector('.services');
    const sectionsToToggle = ['.welcome', '.info-card', '.carousel-section', '.popup'];
    const servicesTitle = document.querySelector('.section-title');
    const cards = document.querySelectorAll('.services .service-card');

    let matched = 0;
    cards.forEach(card => {
      const name = (card.querySelector('h3')?.textContent || '').toLowerCase();
      const desc = Array.from(card.querySelectorAll('div')).map(n => n.textContent).join(' ').toLowerCase();
      const show = !q || name.includes(q) || desc.includes(q);
      card.style.display = show ? '' : 'none';
      if (show) matched++;
    });

    // Banner hasil pencarian (hiasan) di atas daftar layanan
    let banner = document.getElementById('searchBanner');
    if (!banner && servicesSection) {
      banner = document.createElement('div');
      banner.id = 'searchBanner';
      banner.style.cssText = 'display:none;margin:18px 0 10px;padding:12px 14px;border-radius:12px;background:linear-gradient(135deg,#eef5ff,#f4f8ff);border:1px solid #dbe7ff;box-shadow:0 6px 18px rgba(24,86,167,0.08);color:#1856a7;font-weight:600;display:flex;align-items:center;justify-content:space-between;gap:10px;';
      banner.innerHTML = '<div style="display:flex;align-items:center;gap:10px;"><i class="fa fa-search" style="color:#1856a7;"></i><span id="sbText"></span></div><button id="sbClear" style="background:#1856a7;color:#fff;border:none;border-radius:8px;padding:6px 10px;font-weight:600;cursor:pointer;">Bersihkan</button>';
      servicesSection.parentNode.insertBefore(banner, servicesSection);
      banner.querySelector('#sbClear').addEventListener('click', function(){
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
      });
    }

    // Elemen "tidak ada hasil" yang rapi dan terpusat
    let emptyEl = document.getElementById('noServiceResult');
    if (!emptyEl && servicesSection) {
      emptyEl = document.createElement('div');
      emptyEl.id = 'noServiceResult';
      emptyEl.style.cssText = 'display:none;align-items:center;justify-content:center;min-height:160px;margin:18px auto;max-width:600px;padding:18px;border-radius:12px;border:1px dashed #c7d2fe;background:#f8fafc;color:#475569;font-weight:600;box-shadow:0 10px 30px rgba(23,37,84,0.04);text-align:center;';
      emptyEl.innerHTML = '<div style="display:flex;align-items:center;gap:10px;"><i class="fa fa-circle-info" style="color:#64748b;"></i><span>Tidak ada layanan yang cocok.</span></div>';
      servicesSection.appendChild(emptyEl);
    }

    if (q) {
      // Sembunyikan semua section selain layanan
      sectionsToToggle.forEach(sel => { const el = document.querySelector(sel); if (el) el.style.display = 'none'; });
      if (servicesTitle) servicesTitle.style.display = 'none';

      // Tampilkan daftar layanan + banner, tambah jarak dari header
      if (servicesSection) {
        servicesSection.style.display = '';
        servicesSection.style.marginTop = '22px';
      }
      if (banner) {
        const sbText = banner.querySelector('#sbText');
        if (sbText) sbText.textContent = matched + ' layanan untuk "' + qRaw + '"';
        banner.style.display = 'flex';
      }

      // Atur pesan kosong
      if (matched === 0 && emptyEl) emptyEl.style.display = 'flex'; else if (emptyEl) emptyEl.style.display = 'none';

      // scroll ke layanan agar fokus
      try { servicesSection && servicesSection.scrollIntoView({behavior:'smooth', block:'start'}); } catch(e) {}
    } else {
      // Tampilkan kembali section non-layanan saat input kosong
      sectionsToToggle.forEach(sel => { const el = document.querySelector(sel); if (el) el.style.display = ''; });
      if (servicesTitle) servicesTitle.style.display = '';
      if (servicesSection) { servicesSection.style.display = ''; servicesSection.style.marginTop = ''; }
      if (emptyEl) emptyEl.style.display = 'none';
      if (banner) banner.style.display = 'none';
      // Reset: tampilkan semua kartu
      cards.forEach(card => { card.style.display = ''; });
    }
  });
}

// Blur header saat popup cara kerja dibuka/ditutup
const lihatDetailBtn = document.getElementById('lihatDetailBtn');
const closePopup = document.getElementById('closePopup');
if (lihatDetailBtn) {
  lihatDetailBtn.addEventListener('click', function(){ setHeaderBlur(true); });
}
if (closePopup) {
  closePopup.addEventListener('click', function(){ setHeaderBlur(false); });
}
});
</script>
</body>
<script>
(function(){
    const bell = document.getElementById('notifBell');
    const dropdown = document.getElementById('notifDropdown');
    const list = document.getElementById('notifList');
    const bookings = window.notifRequests || [];
    function fmtTanggal(iso){
        try{ return new Date(iso).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }); }catch(e){ return ''; }
    }
    function fmtRupiah(num){ try{ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(Number(num||0)); }catch(e){ return 'Rp0'; }
    }
    const rows = [];
    bookings.forEach(b => {
        // Konfirmasi pembayaran (non-COD)
        if (b.status_admin === 'diterima' && b.metode_pembayaran !== 'COD' && b.jumlah_pembayaran && Number(b.jumlah_pembayaran) > 0 && Number(b.bayar_sekarang) !== 1) {
            rows.push(`
            <div class=\"notif-item\" style=\"padding:12px 16px;border-bottom:1px solid #eee;display:flex;gap:12px;\">
              <span class=\"notif-icon\"><i class=\"fa-solid fa-credit-card\"></i></span>
              <div>
                <div>Konfirmasi pembayaran untuk <b>${b.layanan}</b>. Total: <b>${fmtRupiah(b.jumlah_pembayaran)}</b></div>
                <div class=\"notif-time\" style=\"font-size:12px;color:#888;\">${fmtTanggal(b.updated_at || b.created_at)}</div>
                <div style=\"margin-top:8px;\"><a href=\"/pembayaran?booking=${encodeURIComponent(b.kode_booking)}\" class=\"btn-detail\" style=\"text-decoration:none; font-size:13px; padding:4px 10px;\">Bayar Sekarang</a></div>
              </div>
            </div>`);
        }
        // Diterima (COD)
        if (b.status_admin === 'diterima' && b.metode_pembayaran === 'COD') {
            rows.push(`
            <div class=\"notif-item\" style=\"padding:12px 16px;border-bottom:1px solid #eee;display:flex;gap:12px;\">
              <span class=\"notif-icon\"><i class=\"fa-solid fa-circle-check\"></i></span>
              <div>
                <div>Booking <b>${b.layanan}</b> dikonfirmasi (COD). Siapkan pembayaran tunai.</div>
                <div class=\"notif-time\" style=\"font-size:12px;color:#888;\">${fmtTanggal(b.updated_at || b.created_at)}</div>
              </div>
            </div>`);
        }
        // Ditolak
        if (b.status_admin === 'ditolak') {
            rows.push(`
            <div class=\"notif-item\" style=\"padding:12px 16px;border-bottom:1px solid #eee;display:flex;gap:12px;\">
              <span class=\"notif-icon\"><i class=\"fa-solid fa-triangle-exclamation\"></i></span>
              <div>
                <div>Booking <b>${b.layanan}</b> ditolak.</div>
                <div class=\"notif-time\" style=\"font-size:12px;color:#888;\">${fmtTanggal(b.updated_at || b.created_at)}</div>
              </div>
            </div>`);
        }
        // Dibatalkan (oleh user)
        if (b.status_admin === 'dibatalkan') {
            rows.push(`
            <div class=\"notif-item\" style=\"padding:12px 16px;border-bottom:1px solid #eee;display:flex;gap:12px;\">
              <span class=\"notif-icon\"><i class=\"fa-solid fa-ban\"></i></span>
              <div>
                <div>Booking <b>${b.layanan}</b> dibatalkan.</div>
                <div class=\"notif-time\" style=\"font-size:12px;color:#888;\">${fmtTanggal(b.updated_at || b.created_at)}</div>
              </div>
            </div>`);
        }
        // Menunggu (opsional ditampilkan)
        rows.push(`
          <div class=\"notif-item\" style=\"padding:12px 16px;border-bottom:1px solid #eee;display:flex;gap:12px;\">
            <span class=\"notif-icon\"><i class=\"fa-regular fa-clock\"></i></span>
            <div>
              <div>Booking <b>${b.layanan}</b> sedang menunggu konfirmasi admin.</div>
              <div class=\"notif-time\" style=\"font-size:12px;color:#888;\">${fmtTanggal(b.created_at)}</div>
            </div>
          </div>`);
    });
    list.innerHTML = rows.length ? rows.join('') : '<div class="notif-item">Tidak ada notifikasi.</div>';
    if (bell && dropdown) {
        bell.addEventListener('click', function(e){
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', function(e){
            if (!dropdown.contains(e.target) && e.target !== bell) {
                dropdown.classList.remove('open');
            }
        });
        document.getElementById('notifCloseX') && document.getElementById('notifCloseX').addEventListener('click', function(){
            dropdown.classList.remove('open');
        });
    }
})();
</script>
</html>