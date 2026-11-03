<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TEKSIKA — Solusi Teknisi & Perbaikan Rumah Profesional</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="{{ asset('css/landingpageuser.css') }}">
</head>
<body>

  <!-- Top Scroll Progress Indicator -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- ==================== NAVBAR ==================== -->
  <header class="site-header" id="siteHeader">
    <div class="container header-inner">
      <a href="{{ route('landingpageuser') }}" class="brand-logo" aria-label="Beranda TEKSIKA">
        <img src="{{ asset('images/logo-biru.png') }}" alt="Logo TEKSIKA">
        <span class="brand-wordmark">TEKSIKA</span>
      </a>

      <nav class="main-nav" id="navMenu" aria-label="Navigasi Utama">
        <a href="#beranda" class="nav-item active" data-section="beranda">Beranda</a>
        <a href="#layanan" class="nav-item" data-section="layanan">Layanan</a>
        <a href="#standar-kerja" class="nav-item" data-section="standar-kerja">Cara Kerja</a>
        <a href="#portofolio" class="nav-item" data-section="portofolio">Portofolio</a>
        <a href="#testimoni" class="nav-item" data-section="testimoni">Ulasan</a>
      </nav>

      <div class="header-cta">
        <a href="{{ route('menu_login') }}" class="btn-header">
          <span>Masuk / Pesan</span>
          <svg width="15" height="15" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3.5 8H12.5M12.5 8L8.5 4M12.5 8L8.5 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>

      <button class="mobile-menu-btn" id="navToggle" aria-label="Buka Menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </header>

  <main>
    <!-- ==================== HERO SECTION (2-COLUMN SIDE-BY-SIDE) ==================== -->
    <section class="hero" id="beranda">
      <div class="hero-bg-glow"></div>
      <div class="container hero-grid">
        
        <!-- Left Column: Copy, Actions & Quick Stats -->
        <div class="hero-content" data-scroll>
          <h1 class="hero-title">
            Solusi Perbaikan &amp; Instalasi Rumah, <span class="text-brand">Cepat &amp; Bergaransi.</span>
          </h1>

          <p class="hero-desc">
            Panggil teknisi ahli untuk perbaikan listrik, perawatan AC, instalasi pipa air, hingga pengecatan rumah. Harga disepakati di awal tanpa biaya tersembunyi.
          </p>

          <div class="hero-cta-row">
            <a href="{{ route('menu_login') }}" class="btn-primary-lg">
              <span>Pesan Teknisi Sekarang</span>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3.5 8H12.5M12.5 8L8.5 4M12.5 8L8.5 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
            <a href="#layanan" class="btn-outline-lg">Jelajahi Layanan</a>
          </div>
        </div>

        <!-- Right Column: Sharp Visual Bento Mosaic -->
        <div class="hero-visual" data-scroll data-delay="120">
          <div class="mosaic-grid">
            <!-- Main Tall Photo -->
            <div class="mosaic-card mosaic-tall">
              <img src="{{ asset('images/ac2.jpg') }}" alt="Teknisi AC Profesional TEKSIKA" class="parallax-img" data-speed="0.03">
              <div class="mosaic-overlay">
                <span class="mosaic-tag">Tata Udara</span>
                <p class="mosaic-label">Servis &amp; Perawatan AC</p>
              </div>
            </div>

            <!-- Top Right Photo -->
            <div class="mosaic-card mosaic-top-right">
              <img src="{{ asset('images/benerinlistrikk.jpeg') }}" alt="Teknisi Listrik TEKSIKA">
              <div class="mosaic-overlay compact">
                <span class="mosaic-tag">Kelistrikan</span>
                <p class="mosaic-label">Instalasi &amp; Panel</p>
              </div>
            </div>

            <!-- Bottom Right Photo -->
            <div class="mosaic-card mosaic-bottom-right">
              <img src="{{ asset('images/benerinpipaa.jpg') }}" alt="Teknisi Plumbing TEKSIKA">
              <div class="mosaic-overlay compact">
                <span class="mosaic-tag">Plumbing</span>
                <p class="mosaic-label">Pipa &amp; Sanitari</p>
              </div>
            </div>
          </div>

          <!-- Floating Guarantee Card -->
          <div class="hero-floating-card">
            <div class="fc-badge">SNI</div>
            <div class="fc-text">
              <strong>Mitra Teknisi Tersertifikasi</strong>
              <span>Kunjungan tepat waktu &amp; peralatan lengkap</span>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ==================== SERVICES (4 VISUAL CARDS) ==================== -->
    <section class="section-services" id="layanan">
      <div class="container">
        <div class="section-header-row" data-scroll>
          <div>
            <span class="section-tag">LAYANAN UTAMA</span>
            <h2 class="section-title">Kebutuhan Teknis Rumah dalam Satu Platform</h2>
          </div>
          <p class="section-sub">
            Ditangani langsung oleh spesialis di bidangnya masing-masing dengan standar keamanan tinggi dan estimasi biaya transparan.
          </p>
        </div>

        <div class="services-cards-grid">
          <!-- Card 1: Listrik -->
          <article class="service-card" data-scroll>
            <div class="sc-img-wrap">
              <img src="{{ asset('images/benerinlistrikk.jpeg') }}" alt="Instalasi & Perbaikan Listrik">
              <span class="sc-badge">01 • Kelistrikan</span>
            </div>
            <div class="sc-body">
              <h3 class="sc-title">Instalasi &amp; Perbaikan Listrik</h3>
              <p class="sc-desc">
                Penanganan korsleting, penggantian MCB, penambahan titik stop kontak &amp; saklar, serta perapian kabel rumah.
              </p>
              <div class="sc-footer">
                <div class="sc-price">
                  <span>Mulai dari</span>
                  <strong>Rp 150.000</strong>
                </div>
                <a href="{{ route('menu_login') }}" class="sc-btn">Pesan ↗</a>
              </div>
            </div>
          </article>

          <!-- Card 2: AC -->
          <article class="service-card" data-scroll data-delay="80">
            <div class="sc-img-wrap">
              <img src="{{ asset('images/ac2.jpg') }}" alt="Servis & Cuci AC">
              <span class="sc-badge">02 • Pendingin</span>
            </div>
            <div class="sc-body">
              <h3 class="sc-title">Servis &amp; Instalasi AC</h3>
              <p class="sc-desc">
                Cuci AC berkala, pengisian freon, perbaikan AC bocor atau kurang dingin, hingga bongkar pasang unit baru.
              </p>
              <div class="sc-footer">
                <div class="sc-price">
                  <span>Mulai dari</span>
                  <strong>Rp 150.000</strong>
                </div>
                <a href="{{ route('menu_login') }}" class="sc-btn">Pesan ↗</a>
              </div>
            </div>
          </article>

          <!-- Card 3: Plumbing -->
          <article class="service-card" data-scroll data-delay="160">
            <div class="sc-img-wrap">
              <img src="{{ asset('images/benerinpipaa.jpg') }}" alt="Plumbing & Saluran Air">
              <span class="sc-badge">03 • Plumbing</span>
            </div>
            <div class="sc-body">
              <h3 class="sc-title">Plumbing &amp; Saluran Air</h3>
              <p class="sc-desc">
                Perbaikan pipa bocor, pelancaran saluran air mampet, pemasangan pompa pendorong, kuras toren, &amp; sanitari.
              </p>
              <div class="sc-footer">
                <div class="sc-price">
                  <span>Mulai dari</span>
                  <strong>Rp 150.000</strong>
                </div>
                <a href="{{ route('menu_login') }}" class="sc-btn">Pesan ↗</a>
              </div>
            </div>
          </article>

          <!-- Card 4: Cat & Renovasi -->
          <article class="service-card" data-scroll data-delay="240">
            <div class="sc-img-wrap">
              <img src="{{ asset('images/tukangcatt.jpg') }}" alt="Pengecatan & Renovasi">
              <span class="sc-badge">04 • Interior &amp; Sipil</span>
            </div>
            <div class="sc-body">
              <h3 class="sc-title">Pengecatan &amp; Perbaikan</h3>
              <p class="sc-desc">
                Pengecatan ulang dinding interior maupun eksterior, pelapisan anti-bocor (waterproofing), dan perbaikan plafon.
              </p>
              <div class="sc-footer">
                <div class="sc-price">
                  <span>Mulai dari</span>
                  <strong>Rp 150.000</strong>
                </div>
                <a href="{{ route('menu_login') }}" class="sc-btn">Pesan ↗</a>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ==================== DARK CONTRAST SECTION: HOW IT WORKS ==================== -->
    <section class="section-dark-process" id="standar-kerja">
      <div class="container">
        <div class="process-grid">
          <!-- Left Info -->
          <div class="process-intro" data-scroll>
            <span class="section-tag tag-light">CARA KERJA TEKSIKA</span>
            <h2 class="process-heading">Tiga langkah praktis tanpa repot &amp; tanpa biaya tersembunyi.</h2>
            <p class="process-sub">
              Kami merancang alur pemesanan yang transparan agar Anda mengetahui persis tindakan perbaikan serta biayanya sebelum teknisi mulai bekerja.
            </p>

            <div class="trust-pills-list">
              <div class="t-pill">
                <span class="t-num">98%</span>
                <span class="t-text">Kunjungan Tepat Waktu</span>
              </div>
              <div class="t-pill">
                <span class="t-num">120+</span>
                <span class="t-text">Teknisi Terverifikasi</span>
              </div>
            </div>

            <a href="{{ route('menu_login') }}" class="btn-primary-lg">
              <span>Jadwalkan Kunjungan Sekarang</span>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3.5 8H12.5M12.5 8L8.5 4M12.5 8L8.5 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          </div>

          <!-- Right Steps -->
          <div class="process-cards-col">
            <div class="p-step-card" data-scroll>
              <div class="p-step-number">01</div>
              <div class="p-step-body">
                <div class="p-step-top">
                  <h3>Pilih Layanan &amp; Waktu Kunjungan</h3>
                  <span class="p-badge">± 2 Menit</span>
                </div>
                <p>Pilih kategori masalah teknis rumah Anda, tulis keluhan singkat, dan tentukan jadwal kedatangan teknisi sesuai waktu luang Anda.</p>
              </div>
            </div>

            <div class="p-step-card" data-scroll data-delay="100">
              <div class="p-step-number">02</div>
              <div class="p-step-body">
                <div class="p-step-top">
                  <h3>Inspeksi &amp; Persetujuan Estimasi Biaya</h3>
                  <span class="p-badge">Transparan</span>
                </div>
                <p>Teknisi datang memeriksa titik kerusakan dan menjelaskan rincian biaya secara transparan sebelum pekerjaan dieksekusi.</p>
              </div>
            </div>

            <div class="p-step-card" data-scroll data-delay="200">
              <div class="p-step-number">03</div>
              <div class="p-step-body">
                <div class="p-step-top">
                  <h3>Pengerjaan Rapi &amp; Garansi Aktif</h3>
                  <span class="p-badge">Garansi 30 Hari</span>
                </div>
                <p>Perbaikan diselesaikan dengan rapi dan bersih. Pembayaran dilakukan dengan aman disertai perlindungan garansi 30 hari.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== PORTFOLIO SECTION ==================== -->
    <section class="section-portfolio" id="portofolio">
      <div class="container">
        <div class="section-header-row" data-scroll>
          <div>
            <span class="section-tag">HASIL KERJA NYATA</span>
            <h2 class="section-title">Dokumentasi Proyek Lapangan</h2>
          </div>
          <p class="section-sub">
            Rekam jejak pengerjaan instalasi dan perbaikan yang telah diselesaikan oleh tim teknisi kami di berbagai lokasi pelanggan.
          </p>
        </div>

        <div class="portfolio-grid">
          <!-- Case 1: Listrik -->
          <article class="portfolio-card" data-scroll>
            <div class="pf-img-box">
              <img src="{{ asset('images/Benerin listrik.jpeg') }}" alt="Renovasi Listrik Rumah">
              <span class="pf-category">Kelistrikan</span>
              <span class="pf-duration">1 Hari Pengerjaan</span>
            </div>
            <div class="pf-content">
              <div class="pf-location">Sudirman, Jakarta Pusat</div>
              <h3 class="pf-title">Renovasi Listrik &amp; Panel MCB Rumah</h3>
              <p class="pf-desc">
                Penambahan titik stop kontak, penggantian MCB pembagi daya, serta perapian jalur kabel utama sesuai standar SNI.
              </p>
              <div class="pf-footer">
                <div class="pf-price-box">
                  <span>Total Biaya</span>
                  <strong>Rp 150.000</strong>
                </div>
                <a href="#" class="btn-secondary pf-detail-btn" data-target="popup-listrik">Lihat Detail</a>
              </div>
            </div>
          </article>

          <!-- Case 2: AC -->
          <article class="portfolio-card" data-scroll data-delay="100">
            <div class="pf-img-box">
              <img src="{{ asset('images/ac2.jpg') }}" alt="Servis & Perawatan AC">
              <span class="pf-category">Tata Udara</span>
              <span class="pf-duration">2 Jam Pengerjaan</span>
            </div>
            <div class="pf-content">
              <div class="pf-location">Tebet, Jakarta Selatan</div>
              <h3 class="pf-title">Perawatan Menyeluruh &amp; Cuci Unit AC</h3>
              <p class="pf-desc">
                Pembersihan evaporator, blower indoor, kondensor outdoor, serta pengecekan tekanan freon agar AC dingin optimal.
              </p>
              <div class="pf-footer">
                <div class="pf-price-box">
                  <span>Total Biaya</span>
                  <strong>Rp 150.000</strong>
                </div>
                <a href="#" class="btn-secondary pf-detail-btn" data-target="popup-ac">Lihat Detail</a>
              </div>
            </div>
          </article>

          <!-- Case 3: Plumbing -->
          <article class="portfolio-card" data-scroll data-delay="200">
            <div class="pf-img-box">
              <img src="{{ asset('images/benerinpipaa.jpg') }}" alt="Perbaikan Instalasi Plumbing">
              <span class="pf-category">Plumbing</span>
              <span class="pf-duration">1 Hari Pengerjaan</span>
            </div>
            <div class="pf-content">
              <div class="pf-location">Depok, Jawa Barat</div>
              <h3 class="pf-title">Perbaikan Pipa Bocor &amp; Saluran Air</h3>
              <p class="pf-desc">
                Penanganan kebocoran sambungan pipa bertekanan, pelancaran saluran mampet, dan uji distribusi air bersih.
              </p>
              <div class="pf-footer">
                <div class="pf-price-box">
                  <span>Total Biaya</span>
                  <strong>Rp 150.000</strong>
                </div>
                <a href="#" class="btn-secondary pf-detail-btn" data-target="popup-plumbing">Lihat Detail</a>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ==================== TESTIMONIALS SECTION ==================== -->
    <section class="section-testimonials" id="testimoni">
      <div class="container">
        <div class="section-header-row" data-scroll>
          <div>
            <span class="section-tag">ULASAN PELANGGAN</span>
            <h2 class="section-title">Dipercaya oleh Pemilik Hunian</h2>
          </div>
          <p class="section-sub">
            Pengalaman langsung dari pelanggan yang telah menggunakan layanan teknisi TEKSIKA untuk kebutuhan rumah mereka.
          </p>
        </div>

        <div class="testimonials-grid">
          <div class="testi-card" data-scroll>
            <div class="testi-header">
              <span class="testi-badge">Layanan Kelistrikan</span>
              <div class="testi-stars" aria-label="Rating 5 dari 5">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="#F59E0B"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>5.0</span>
              </div>
            </div>
            <p class="testi-quote">
              “Teknisi datang tepat waktu membawa alat ukur lengkap. Jalur kabel di plafon yang tadinya berantakan dirapikan ulang dan pembagian beban MCB dijelaskan dengan sangat transparan.”
            </p>
            <div class="testi-user">
              <img src="{{ asset('images/user1.png') }}" alt="Andi Pratama">
              <div>
                <strong>Andi Pratama</strong>
                <span>Sudirman, Jakarta Pusat</span>
              </div>
            </div>
          </div>

          <div class="testi-card" data-scroll data-delay="100">
            <div class="testi-header">
              <span class="testi-badge">Layanan Plumbing</span>
              <div class="testi-stars" aria-label="Rating 4.9 dari 5">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="#F59E0B"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>4.9</span>
              </div>
            </div>
            <p class="testi-quote">
              “Teknisi ramah dan profesional. Sebelum mulai membongkar pipa yang bocor, rincian biaya disepakati dulu di awal sehingga tidak ada biaya dadakan sama sekali.”
            </p>
            <div class="testi-user">
              <img src="{{ asset('images/user2.png') }}" alt="Sinta Rahmawati">
              <div>
                <strong>Sinta Rahmawati</strong>
                <span>Tebet, Jakarta Selatan</span>
              </div>
            </div>
          </div>

          <div class="testi-card" data-scroll data-delay="200">
            <div class="testi-header">
              <span class="testi-badge">Servis &amp; Cuci AC</span>
              <div class="testi-stars" aria-label="Rating 5 dari 5">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="#F59E0B"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>5.0</span>
              </div>
            </div>
            <p class="testi-quote">
              “Pengerjaan sangat bersih dan rapi. Setelah diservis oleh tim TEKSIKA, AC kamar langsung dingin maksimal dan tidak ada lagi masalah air menetes.”
            </p>
            <div class="testi-user">
              <img src="{{ asset('images/user3.jpg') }}" alt="Budi Santoso">
              <div>
                <strong>Budi Santoso</strong>
                <span>Depok, Jawa Barat</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== CTA BANNER ==================== -->
    <section class="section-cta-banner">
      <div class="container">
        <div class="cta-box" data-scroll>
          <div class="cta-box-text">
            <h2>Butuh Teknisi untuk Perbaikan Rumah Hari Ini?</h2>
            <p>Buat permintaan layanan dalam hitungan menit dan biarkan teknisi tersertifikasi kami menanganinya untuk Anda.</p>
          </div>
          <div class="cta-box-action">
            <a href="{{ route('menu_login') }}" class="btn-white-cta">
              <span>Mulai Request Sekarang</span>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3.5 8H12.5M12.5 8L8.5 4M12.5 8L8.5 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ==================== POPUP MODAL LISTRIK ==================== -->
  <div id="popup-listrik" class="popup" role="dialog" aria-modal="true" aria-labelledby="title-listrik">
    <div class="popup-content">
      <div class="popup-top-bar">
        <span class="popup-code">DETAIL PROYEK • KELISTRIKAN</span>
        <button class="close" aria-label="Tutup rincian">&times;</button>
      </div>

      <h2 id="title-listrik" class="popup-heading">Renovasi Listrik &amp; Panel MCB Rumah</h2>

      <div class="popup-gallery">
        <img src="{{ asset('images/Benerin listrik.jpeg') }}" alt="Dokumentasi Listrik 1">
        <img src="{{ asset('images/benerinlistrikk.jpeg') }}" alt="Dokumentasi Listrik 2">
      </div>

      <div class="popup-body">
        <div class="popup-main-text">
          <h3>Deskripsi Pekerjaan</h3>
          <p>
            Proyek ini mencakup pemasangan titik stop kontak tambahan, penggantian MCB pembagi yang sudah melemah, serta penataan ulang jalur kabel listrik agar aman sesuai standar SNI.
          </p>

          <h3>Rincian Tindakan</h3>
          <ul class="spec-list">
            <li>Penambahan titik stop kontak dan saklar berstandar SNI</li>
            <li>Penggantian unit MCB baru &amp; penyeimbangan beban daya</li>
            <li>Perapian jalur kabel utama dengan pelindung pipa konduit</li>
          </ul>
        </div>

        <aside class="popup-sidebar">
          <div class="sidebar-row">
            <span class="sidebar-key">Lokasi</span>
            <span class="sidebar-val">Sudirman, Jakarta Pusat</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Durasi</span>
            <span class="sidebar-val">1 Hari Kerja</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Tanggal</span>
            <span class="sidebar-val">10 Januari 2025</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Teknisi</span>
            <span class="sidebar-val">Andi — Tim Listrik Pro</span>
          </div>
          <div class="sidebar-total">
            <span>Total Biaya</span>
            <strong>Rp 150.000</strong>
          </div>
          <a href="{{ route('menu_login') }}" class="btn-primary-lg full-width">Pesan Layanan Serupa</a>
        </aside>
      </div>
    </div>
  </div>

  <!-- ==================== POPUP MODAL AC ==================== -->
  <div id="popup-ac" class="popup" role="dialog" aria-modal="true" aria-labelledby="title-ac">
    <div class="popup-content">
      <div class="popup-top-bar">
        <span class="popup-code">DETAIL PROYEK • TATA UDARA</span>
        <button class="close" aria-label="Tutup rincian">&times;</button>
      </div>

      <h2 id="title-ac" class="popup-heading">Perawatan Menyeluruh &amp; Cuci Unit AC</h2>

      <div class="popup-gallery">
        <img src="{{ asset('images/ac2.jpg') }}" alt="Dokumentasi Servis AC 1">
        <img src="{{ asset('images/benerinac.jpg') }}" alt="Dokumentasi Servis AC 2">
      </div>

      <div class="popup-body">
        <div class="popup-main-text">
          <h3>Deskripsi Pekerjaan</h3>
          <p>
            Perawatan rutin untuk menjaga performa pendinginan AC tetap optimal dan hemat listrik, meliputi pembersihan filter, evaporator, kondensor, serta pengecekan tekanan freon.
          </p>

          <h3>Rincian Tindakan</h3>
          <ul class="spec-list">
            <li>Pembersihan filter udara, sirip evaporator, dan blower indoor</li>
            <li>Pembersihan jalur pembuangan air untuk mencegah kebocoran</li>
            <li>Pengecekan tekanan freon dan arus kompresor</li>
          </ul>
        </div>

        <aside class="popup-sidebar">
          <div class="sidebar-row">
            <span class="sidebar-key">Lokasi</span>
            <span class="sidebar-val">Tebet, Jakarta Selatan</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Durasi</span>
            <span class="sidebar-val">2 Jam</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Tanggal</span>
            <span class="sidebar-val">15 Januari 2025</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Teknisi</span>
            <span class="sidebar-val">Budi — Spesialis AC</span>
          </div>
          <div class="sidebar-total">
            <span>Total Biaya</span>
            <strong>Rp 150.000</strong>
          </div>
          <a href="{{ route('menu_login') }}" class="btn-primary-lg full-width">Pesan Layanan Serupa</a>
        </aside>
      </div>
    </div>
  </div>

  <!-- ==================== POPUP MODAL PLUMBING ==================== -->
  <div id="popup-plumbing" class="popup" role="dialog" aria-modal="true" aria-labelledby="title-plumbing">
    <div class="popup-content">
      <div class="popup-top-bar">
        <span class="popup-code">DETAIL PROYEK • PLUMBING</span>
        <button class="close" aria-label="Tutup rincian">&times;</button>
      </div>

      <h2 id="title-plumbing" class="popup-heading">Perbaikan Pipa Bocor &amp; Saluran Air</h2>

      <div class="popup-gallery">
        <img src="{{ asset('images/benerinpipaa.jpg') }}" alt="Dokumentasi Plumbing 1">
        <img src="{{ asset('images/pipa.jpeg') }}" alt="Dokumentasi Plumbing 2">
      </div>

      <div class="popup-body">
        <div class="popup-main-text">
          <h3>Deskripsi Pekerjaan</h3>
          <p>
            Perbaikan saluran air yang tersumbat, penggantian sambungan pipa rusak, serta pemeriksaan titik kebocoran untuk memastikan aliran air bersih kembali normal.
          </p>

          <h3>Rincian Tindakan</h3>
          <ul class="spec-list">
            <li>Pembersihan saluran pembuangan air yang tersumbat</li>
            <li>Penggantian pipa dan sambungan keran yang bocor</li>
            <li>Pemeriksaan menyeluruh tekanan distribusi air</li>
          </ul>
        </div>

        <aside class="popup-sidebar">
          <div class="sidebar-row">
            <span class="sidebar-key">Lokasi</span>
            <span class="sidebar-val">Depok, Jawa Barat</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Durasi</span>
            <span class="sidebar-val">1 Hari Kerja</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Tanggal</span>
            <span class="sidebar-val">20 Januari 2025</span>
          </div>
          <div class="sidebar-row">
            <span class="sidebar-key">Teknisi</span>
            <span class="sidebar-val">Suryo — Ahli Plumbing</span>
          </div>
          <div class="sidebar-total">
            <span>Total Biaya</span>
            <strong>Rp 150.000</strong>
          </div>
          <a href="{{ route('menu_login') }}" class="btn-primary-lg full-width">Pesan Layanan Serupa</a>
        </aside>
      </div>
    </div>
  </div>

  <!-- ==================== FOOTER ==================== -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-main-grid">
        <div class="footer-col-brand">
          <div class="footer-logo-row">
            <img src="{{ asset('images/logo-putih.png') }}" alt="Logo TEKSIKA" class="footer-logo">
            <span class="footer-wordmark">TEKSIKA</span>
          </div>
          <p class="footer-desc">
            Platform pemesanan teknisi profesional untuk perbaikan listrik, perawatan AC, plumbing, dan renovasi rumah dengan jaminan garansi resmi.
          </p>
          <p class="footer-address">
            Wanaherang, Kec. Gunungputri, Kab. Bogor, Jawa Barat
          </p>
        </div>

        <div class="footer-col-links">
          <span class="footer-col-label">Navigasi</span>
          <a href="#beranda">Beranda</a>
          <a href="#layanan">Layanan Kami</a>
          <a href="#standar-kerja">Cara Kerja</a>
          <a href="#portofolio">Portofolio</a>
          <a href="#testimoni">Ulasan Pelanggan</a>
        </div>

        <div class="footer-col-links">
          <span class="footer-col-label">Layanan Teknis</span>
          <a href="{{ route('menu_login') }}">Instalasi &amp; Perbaikan Listrik</a>
          <a href="{{ route('menu_login') }}">Servis &amp; Cuci AC</a>
          <a href="{{ route('menu_login') }}">Plumbing &amp; Pipa Air</a>
          <a href="{{ route('menu_login') }}">Pengecatan &amp; Renovasi</a>
        </div>

        <div class="footer-col-links">
          <span class="footer-col-label">Hubungi Kami</span>
          <a href="https://wa.me/62895373441563" target="_blank" rel="noopener">WhatsApp: +62 895-3734-41563</a>
          <a href="https://www.instagram.com/teksika.id/" target="_blank" rel="noopener">Instagram: @teksika.id</a>
          <a href="https://www.facebook.com/share/16VxbAQdAY/" target="_blank" rel="noopener">Facebook Resmi TEKSIKA</a>
        </div>
      </div>

      <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} TEKSIKA. Semua Hak Dilindungi.</span>
        <span>Layanan Teknisi Rumah &amp; Gedung Terpercaya</span>
      </div>
    </div>
  </footer>

  <script src="{{ asset('js/popuplanding.js') }}"></script>
</body>
</html>
