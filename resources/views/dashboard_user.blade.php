<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pengguna — TEKSIKA</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Icons & Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notif-dashboarduser.css') }}">
</head>
<body>

  <!-- ==================== SIDEBAR ==================== -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo-wrap">
      <img src="{{ asset('images/logo-putih.png') }}" alt="TEKSIKA Logo" class="sidebar-logo">
    </div>

    <a href="{{ route('dashboard_user') }}" class="menu-item active">
      <img src="{{ asset('images/home.png') }}" alt="Home" class="icon">
      <span class="menu-text">Beranda</span>
    </a>
    <a href="{{ route('bookinguser') }}" class="menu-item">
      <img src="{{ asset('images/booking.png') }}" alt="Booking" class="icon">
      <span class="menu-text">Booking</span>
    </a>
    <a href="{{ route('request') }}" class="menu-item">
      <img src="{{ asset('images/request.png') }}" alt="Request" class="icon">
      <span class="menu-text">Request</span>
    </a>
    <a href="{{ route('chatuser') }}" class="menu-item">
      <img src="{{ asset('images/chat.png') }}" alt="Chat" class="icon">
      <span class="menu-text">Chat</span>
    </a>
    <a href="{{ route('transaksi_user') }}" class="menu-item">
      <img src="{{ asset('images/pembayaran.png') }}" alt="Transaksi" class="icon">
      <span class="menu-text">Transaksi</span>
    </a>

    <div class="logout">
      <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item">
        <i class="fa-solid fa-right-from-bracket" style="font-size: 1.1rem;"></i>
        <span class="menu-text">Keluar</span>
      </a>
    </div>
  </aside>

  <!-- ==================== MAIN CONTENT ==================== -->
  <div class="main">
    
    <!-- Top Header Bar -->
    <header class="header">
      <div class="menu-toggle" id="menuToggle" aria-label="Buka / Tutup Sidebar">
        <i class="fa-solid fa-bars"></i>
      </div>

      <input id="serviceSearch" type="text" placeholder="Cari layanan teknisi..." aria-label="Cari layanan">

      <div class="user-info">
        <div class="header-notif-btn" id="notifBell" title="Notifikasi">
          <i class="fa-solid fa-bell"></i>
        </div>

        @php
          $avatarUrlHeader = !empty($user->avatar) ? asset('storage/'.$user->avatar) : asset('images/userabu.png');
        @endphp
        <div class="header-profile-pill" onclick="openProfileModal()" title="Profil Akun">
          <img id="avatarHeader" src="{{ $avatarUrlHeader }}" alt="{{ $user->name ?? 'User' }}">
          <span>{{ $user->name ?? 'Akun Saya' }}</span>
          <i class="fa-solid fa-chevron-down profile-chevron"></i>
        </div>
      </div>

      <!-- Dropdown Notifikasi -->
      <div class="notif-dropdown" id="notifDropdown">
        <span id="notifCloseX" style="position:absolute; top:10px; right:14px; font-size:18px; color:#888; cursor:pointer; z-index:100;">&times;</span>
        <div class="notif-header">
          <i class="fa-solid fa-bell" style="color:#007bff; margin-right:6px;"></i> Notifikasi Anda
        </div>
        <div id="notifList" style="max-height:340px; overflow-y:auto;"></div>
      </div>
    </header>

    <!-- Welcome Section -->
    <section class="welcome">
      <h1>Selamat Datang di <span class="text-brand">TEKSIKA</span>, {{ explode(' ', $user->name ?? 'Pelanggan')[0] }}!</h1>
      <p>Platform terpadu teknisi &amp; perbaikan rumah profesional. Pengerjaan cepat, transparan, dan bergaransi.</p>
    </section>

    <!-- Cara Kerja Card -->
    <section class="info-card">
      <div class="info-text">
        <div class="info-icon-badge">
          <i class="fa-solid fa-wand-magic-sparkles"></i>
        </div>
        <div class="info-copy">
          <strong>Cara Mudah Booking Teknisi di TEKSIKA</strong>
          <span>Cukup 3 langkah mudah: request layanan, konfirmasi mitra teknisi, pekerjaan selesai bergaransi.</span>
        </div>
      </div>
      <button type="button" id="lihatDetailBtn">
        <span>Lihat Detail</span>
        <i class="fa-solid fa-arrow-right"></i>
      </button>
    </section>

    <!-- ==================== CAROUSEL SECTION (ENHANCED CINEMATIC BENTO BANNER) ==================== -->
    <section class="carousel-section">
      <div class="carousel-container">
        
        <!-- Floating Guarantee Card (Alike Landing Page Hero Badge) -->
        <div class="carousel-floating-badge">
          <div class="cfb-icon">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <div class="cfb-text">
            <strong>Garansi Resmi TEKSIKA</strong>
            <span>Teknisi Tersertifikasi &amp; Siap Datang</span>
          </div>
        </div>

        <div class="carousel-track">
          <!-- Slide 1: Tata Udara (AC) -->
          <div class="carousel-slide active">
            <img src="{{ asset('images/ac2.jpg') }}" alt="Perawatan AC Berkualitas">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
              <span class="slide-tag"><i class="fa-solid fa-snowflake"></i> Tata Udara</span>
              <h3>Servis &amp; Perawatan AC Berkualitas</h3>
              <p>Nikmati udara sejuk maksimal hemat listrik. Layanan cuci AC, isi freon, perbaikan kompresor hingga bongkar pasang unit bergaransi.</p>
              <div class="slide-cta-row">
                <a href="{{ route('request', ['layanan' => 'Servis AC']) }}" class="slide-cta-btn">
                  <span>Pesan Sekarang</span>
                  <i class="fa-solid fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Slide 2: Kelistrikan -->
          <div class="carousel-slide">
            <img src="{{ asset('images/benerinlistrikk.jpeg') }}" alt="Instalasi Listrik">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
              <span class="slide-tag"><i class="fa-solid fa-bolt"></i> Kelistrikan</span>
              <h3>Instalasi &amp; Penanganan Listrik Ahli</h3>
              <p>Atasi korsleting listrik, pasang instalasi baru, peremajaan panel MCB dan perkabelan dengan standar keselamatan SNI terbaik.</p>
              <div class="slide-cta-row">
                <a href="{{ route('request', ['layanan' => 'Instalasi Listrik']) }}" class="slide-cta-btn">
                  <span>Pesan Sekarang</span>
                  <i class="fa-solid fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Slide 3: Renovasi & Bangunan -->
          <div class="carousel-slide">
            <img src="{{ asset('images/tukanginn.jpg') }}" alt="Renovasi Bangunan">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
              <span class="slide-tag"><i class="fa-solid fa-helmet-safety"></i> Konstruksi &amp; Renovasi</span>
              <h3>Layanan Tukang Bangunan Profesional</h3>
              <p>Solusi perbaikan atap bocor, renovasi lantai, pasang keramik, hingga perombakan ruangan dengan hasil presisi dan rapi.</p>
              <div class="slide-cta-row">
                <a href="{{ route('request') }}" class="slide-cta-btn">
                  <span>Pesan Sekarang</span>
                  <i class="fa-solid fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Slide 4: Plumbing -->
          <div class="carousel-slide">
            <img src="{{ asset('images/benerinpipaa.jpg') }}" alt="Plumbing & Pipa">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
              <span class="slide-tag"><i class="fa-solid fa-faucet-drip"></i> Sanitari &amp; Plumbing</span>
              <h3>Solusi Saluran &amp; Pipa Air Terpercaya</h3>
              <p>Perbaikan pipa bocor, instalasi tandon &amp; pompa pendorong, serta penanganan saluran pembuangan mampet tanpa bongkar total.</p>
              <div class="slide-cta-row">
                <a href="{{ route('request', ['layanan' => 'Perbaikan Pipa']) }}" class="slide-cta-btn">
                  <span>Pesan Sekarang</span>
                  <i class="fa-solid fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Slide 5: Pengecatan -->
          <div class="carousel-slide">
            <img src="{{ asset('images/tukangcatt.jpg') }}" alt="Pengecatan Rumah">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
              <span class="slide-tag"><i class="fa-solid fa-paint-roller"></i> Finishing &amp; Cat</span>
              <h3>Pengecatan Dinding Interior &amp; Eksterior</h3>
              <p>Percayakan pengecatan dinding dan plafon pada ahlinya. Dijamin rata, bersih, tahan cuaca, dengan hasil warna maksimal.</p>
              <div class="slide-cta-row">
                <a href="{{ route('request', ['layanan' => 'Pengecatan Rumah']) }}" class="slide-cta-btn">
                  <span>Pesan Sekarang</span>
                  <i class="fa-solid fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Tombol Navigasi Carousel -->
        <button class="carousel-btn prev-btn" id="prevBtn" aria-label="Slide Sebelumnya">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class="carousel-btn next-btn" id="nextBtn" aria-label="Slide Selanjutnya">
          <i class="fa-solid fa-chevron-right"></i>
        </button>

        <!-- Indikator Slide -->
        <div class="carousel-indicators">
          <span class="indicator active" data-slide="0"></span>
          <span class="indicator" data-slide="1"></span>
          <span class="indicator" data-slide="2"></span>
          <span class="indicator" data-slide="3"></span>
          <span class="indicator" data-slide="4"></span>
        </div>
      </div>
    </section>

    <!-- ==================== POPUP CARA KERJA ==================== -->
    <div id="popupDetail" class="popup">
      <div class="popup-content custom-steps-popup">
        <span class="close-btn" id="closePopup" aria-label="Tutup">&times;</span>
        <h2 class="popup-title">
          <i class="fa-solid fa-wrench"></i> Cara Kerja Layanan TEKSIKA
        </h2>
        
        <div class="steps-container">
          <div class="step">
            <div class="step-icon icon-green"><i class="fa-solid fa-list-check"></i></div>
            <h3>1. Request Layanan</h3>
            <p>Pilih kategori perbaikan, tentukan titik lokasi rumah pada peta, dan tentukan detail keluhan kerusakan.</p>
          </div>
          <div class="step">
            <div class="step-icon icon-blue"><i class="fa-solid fa-user-check"></i></div>
            <h3>2. Penugasan Mitra</h3>
            <p>Sistem dan admin kami mencarikan mitra teknisi terdekat dengan keahlian dan rating terbaik untuk Anda.</p>
          </div>
          <div class="step">
            <div class="step-icon icon-purple"><i class="fa-solid fa-circle-check"></i></div>
            <h3>3. Pengerjaan &amp; Garansi</h3>
            <p>Teknisi hadir tepat waktu, menyelesaikan perbaikan secara rapi, didukung sistem pembayaran aman dan garansi resmi.</p>
          </div>
        </div>

        <div class="popup-buttons">
          <a href="{{ route('request') }}" class="main-btn">
            <i class="fa-solid fa-plus"></i> Mulai Request Sekarang
          </a>
          <a href="{{ route('chatuser') }}" class="main-btn" style="background:#f1f5f9; color:#0f172a; box-shadow:none; border:1px solid #cbd5e1;">
            <i class="fa-solid fa-comment-dots"></i> Konsultasi Admin
          </a>
        </div>
      </div>
    </div>

    <!-- ==================== SERVICES SECTION ==================== -->
    <div class="section-header-wrap">
      <h2 class="section-title">Kategori Layanan Tersedia</h2>
    </div>

    <div class="services">
      @php($services = isset($services) ? $services->where('status','aktif') : collect())
      @forelse($services as $service)
      <div class="service-card">
        @if(!empty($service->gambar))
          <div class="service-icon-box">
            <img src="{{ asset('uploads/services/'.$service->gambar) }}" alt="{{ $service->nama }}">
          </div>
        @else
          <div class="service-icon-box fallback">
            <i class="fa-solid fa-screwdriver-wrench"></i>
          </div>
        @endif

        <h3>{{ $service->nama }}</h3>
        
        @if(!empty($service->deskripsi))
          <div class="service-desc">
            {{ Str::limit($service->deskripsi, 95) }}
          </div>
        @endif

        @if(isset($service->harga_min) && isset($service->harga_max))
          <div class="service-price-pill">
            <i class="fa-solid fa-tag"></i>
            Rp {{ number_format($service->harga_min,0,',','.') }} - Rp {{ number_format($service->harga_max,0,',','.') }}
          </div>
        @endif

        <a href="{{ route('request', ['layanan' => $service->nama]) }}" style="text-decoration:none; margin-top:auto;">
          <button class="request-btn" type="button">
            <span>Mulai Request</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </a>
      </div>
      @empty
        <div style="grid-column: 1/-1; text-align:center; padding:40px; background:#fff; border-radius:18px; border:1px dashed #cbd5e1; color:#64748b;">
          <i class="fa-solid fa-toolbox" style="font-size:2rem; margin-bottom:10px; color:#94a3b8; display:block;"></i>
          Belum ada layanan aktif saat ini.
        </div>
      @endforelse
    </div>

  </div> <!-- End of .main -->

  <!-- ==================== UNIFIED MODALS ==================== -->
  @include('user._profile_modal')

  <!-- Modal Konfirmasi Logout -->
  <div id="logoutModal" class="modal-logout" style="display:none;" onclick="if(event.target===this) closeLogoutModal();">
    <div class="modal-logout-content">
      <span class="modal-logout-close" onclick="closeLogoutModal()">&times;</span>
      
      <div class="modal-logout-header">
        <i class="fa-solid fa-right-from-bracket icon-logout"></i>
        <h2>Konfirmasi Keluar</h2>
        <p>Apakah Anda yakin ingin keluar dari akun TEKSIKA?</p>
      </div>

      <div class="modal-logout-user">
        <div class="user-info">
          <i class="fa-solid fa-user-circle"></i>
          <div>
            <strong>{{ $user->name ?? 'Pengguna' }}</strong>
            <span>{{ $user->email ?? '' }}</span>
          </div>
        </div>
      </div>

      <div class="modal-logout-warning">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div>Sesi aktif Anda akan diakhiri. Anda perlu memasukkan email &amp; kata sandi untuk masuk kembali.</div>
      </div>

      <div class="modal-logout-footer">
        <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
        <button type="button" class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('js/sidebar.js') }}"></script>
  <script src="{{ asset('js/popup.js') }}"></script>
  <script>
    window.notifRequests = @json($requests ?? []);
  </script>
  <script src="{{ asset('js/notif-dashboarduser.js') }}"></script>

  <script>
  // Carousel functionality
  document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let currentSlide = 0;
    let autoTimer = null;

    function showSlide(index) {
      if (!slides.length) return;
      slides.forEach(slide => slide.classList.remove('active'));
      indicators.forEach(indicator => indicator.classList.remove('active'));

      currentSlide = (index + slides.length) % slides.length;
      slides[currentSlide].classList.add('active');
      if (indicators[currentSlide]) indicators[currentSlide].classList.add('active');
    }

    function nextSlide() { showSlide(currentSlide + 1); }
    function prevSlide() { showSlide(currentSlide - 1); }

    function startAutoSlide() {
      stopAutoSlide();
      autoTimer = setInterval(nextSlide, 5000);
    }
    function stopAutoSlide() {
      if (autoTimer) clearInterval(autoTimer);
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', function() {
        prevSlide();
        startAutoSlide();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        nextSlide();
        startAutoSlide();
      });
    }

    indicators.forEach((indicator, index) => {
      indicator.addEventListener('click', function() {
        showSlide(index);
        startAutoSlide();
      });
    });

    const carousel = document.querySelector('.carousel-container');
    if (carousel) {
      carousel.addEventListener('mouseenter', stopAutoSlide);
      carousel.addEventListener('mouseleave', startAutoSlide);
    }

    startAutoSlide();

    // Service Search
    const searchInput = document.getElementById('serviceSearch');
    if (searchInput) {
      searchInput.addEventListener('input', function(){
        const q = this.value.trim().toLowerCase();
        const cards = document.querySelectorAll('.services .service-card');
        const sectionsToToggle = ['.welcome', '.info-card', '.carousel-section'];
        
        cards.forEach(card => {
          const name = (card.querySelector('h3')?.textContent || '').toLowerCase();
          const desc = (card.querySelector('.service-desc')?.textContent || '').toLowerCase();
          const show = !q || name.includes(q) || desc.includes(q);
          card.style.display = show ? '' : 'none';
        });

        if (q) {
          sectionsToToggle.forEach(sel => {
            const el = document.querySelector(sel);
            if (el) el.style.display = 'none';
          });
        } else {
          sectionsToToggle.forEach(sel => {
            const el = document.querySelector(sel);
            if (el) el.style.display = '';
          });
        }
      });
    }
  });
  </script>
</body>
</html>
