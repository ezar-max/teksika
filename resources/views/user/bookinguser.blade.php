<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Booking Saya — TEKSIKA</title>
<!-- Google Fonts: Plus Jakarta Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Fonts & Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
<style>
:root{
    --blue:#007bff;         /* header & aksen utama */
    --blue-dark:#005ecf;    /* shadow/hover */
    --sidebar:#0a84ff;      /* sidebar */
    --bg:#f8fafc;           /* latar konten */
    --card:#ffffff;
    --text:#0f172a;
    --muted:#64748b;
    --line:#e2e8f0;
    --green:#10b981;
    --green-deep:#059669;
    --blackpill:#0f172a;
    --orange:#f59e0b;
    --pill:#f1f5f9;
    --radius:18px;
    --shadow:0 4px 20px -2px rgba(15, 23, 42, 0.05);
  }
  
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
    color:var(--text);
    background:var(--bg);
    overflow-x:hidden;
  }
  
  .header input {
    flex: none;
    width: 250px;
    padding: 10px 15px;
    border-radius: 20px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transition: all 0.3s ease;
  }
  
  .header input::placeholder {
    color: rgba(255, 255, 255, 0.7);
  }
  
  .header input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.3);
    width: 300px;
  }
  
  .user-info {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .user-info .fa-bell {
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
  }
  
  .user-info .fa-bell:hover {
    background: rgba(255, 255, 255, 0.1);
  }
  
  /* CONTENT - PERBAIKAN TAMPILAN SAJA */
  .content {
    padding: 30px;
  }
  
  .header-booking {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }
  
  .header-booking h2 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text);
    margin: 0;
    position: relative;
  }
  
  .header-booking h2::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: var(--blue);
    border-radius: 2px;
  }
  
  .btn-request {
    background-color: #0a45ff;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(10, 69, 255, 0.3);
  }
  
  .btn-request:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(10, 69, 255, 0.4);
  }
  
  .subtitle {
    margin-top: 15px;
    color: var(--muted);
    font-size: 16px;
    margin-bottom: 25px;
  }
  
  /* BOARD - PERBAIKAN TAMPILAN SAJA */
  .board {
    background: white;
    border-radius: 16px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    padding: 25px;
    margin-top: 10px;
    border: 1px solid var(--line);
  }
  
  /* BOOKING CARD - PERBAIKAN TAMPILAN SAJA */
  .booking-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    border: 1px solid var(--line);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .booking-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--blue);
    border-radius: 2px 0 0 2px;
  }
  
  .booking-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  }
  
  .booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .booking-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
  }
  
  .booking-code {
    font-size: 14px;
    color: var(--blue);
    font-weight: 600;
    background: rgba(10, 124, 255, 0.1);
    padding: 4px 10px;
    border-radius: 20px;
  }
  
  .booking-card-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
  }
  
  .booking-date {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--muted);
    font-size: 14px;
  }
  
  .booking-date i {
    color: var(--blue);
  }
  
  .booking-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  
  .booking-status.status-progress {
    background: rgba(32, 196, 99, 0.1);
    color: var(--green);
  }
  
  .booking-status.status-waiting {
    background: rgba(255, 152, 0, 0.1);
    color: #ff9800;
  }
  
  .booking-status.status-rejected {
    background: rgba(244, 67, 54, 0.1);
    color: #f44336;
  }
  
  .booking-status.status-cancelled {
    background: rgba(158, 158, 158, 0.1);
    color: #9e9e9e;
  }
  
  .booking-worker {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--muted);
    font-size: 14px;
    margin-top: 5px;
  }
  
  .booking-worker i {
    color: var(--blue);
  }
  
  .booking-card-footer {
    display: flex;
    gap: 10px;
    margin-top: 5px;
  }
  
  .btn-detail {
    background: rgba(10, 124, 255, 0.1);
    color: var(--blue);
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  
  .btn-detail:hover {
    background: var(--blue);
    color: white;
  }
  
  .btn-cancel {
    background: rgba(244, 67, 54, 0.1);
    color: #f44336;
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  
  .btn-cancel:hover {
    background: #f44336;
    color: white;
  }
  
  /* DROPDOWN NOTIF - PERBAIKAN TAMPILAN SAJA */
  .notif-dropdown {
    position: absolute;
    top: 60px;
    right: 100px;
    width: 280px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 99;
    font-family: 'Inter', sans-serif;
    animation: slideDown 0.3s ease;
  }
  
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .notif-dropdown.open {
    display: flex;
  }
  
  .notif-header {
    font-weight: 600;
    padding: 12px 15px;
    border-bottom: 1px solid var(--line);
    background: var(--bg);
    font-size: 15px;
  }
  
  .notif-item {
    padding: 12px 15px;
    font-size: 14px;
    color: var(--text);
    border-bottom: 1px solid var(--line);
    cursor: pointer;
    display: flex;
    gap: 10px;
    align-items: flex-start;
    transition: all 0.2s ease;
  }
  
  .notif-item:last-child {
    border-bottom: none;
  }
  
  .notif-item:hover {
    background: var(--bg);
  }
  
  .notif-icon {
    font-size: 16px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  
  .notif-icon .fa-credit-card {
    color: #1976d2;
    background: rgba(25, 118, 210, 0.1);
  }
  
  .notif-icon .fa-circle-check {
    color: var(--green);
    background: rgba(32, 196, 99, 0.1);
  }
  
  .notif-icon .fa-triangle-exclamation {
    color: #f44336;
    background: rgba(244, 67, 54, 0.1);
  }
  
  .notif-icon .fa-ban {
    color: #9e9e9e;
    background: rgba(158, 158, 158, 0.1);
  }
  
  .notif-icon .fa-clock {
    color: #ff9800;
    background: rgba(255, 152, 0, 0.1);
  }
  
  .notif-time {
    font-size: 12px;
    color: var(--muted);
    margin-top: 4px;
  }
  
  /* MODAL DETAIL - PERBAIKAN TAMPILAN SAJA */
  .modal {
    position: fixed;
    inset: 0;
    display: none;
    z-index: 2000;
    align-items: center;
    justify-content: center;
  }
  
  .modal.open {
    display: flex;
  }
  /* Efek blur/dim pada konten utama saat modal detail dibuka */
  #main.modal-dim {
    filter: blur(4px) brightness(0.85);
    transition: filter 0.2s ease;
  }
  
  .backdrop {
    position: absolute;
    inset: 0;
    background: transparent; /* hilangkan overlay hitam */
    pointer-events: auto;
  }
  
  .dialog {
    position: relative;
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 920px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    padding: 25px;
    animation: modalFadeIn 0.3s ease;
  }
  
  @keyframes modalFadeIn {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .close-x {
    position: absolute;
    right: 15px;
    top: 15px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--bg);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
    color: var(--muted);
    transition: all 0.3s ease;
  }
  
  .close-x:hover {
    background: var(--line);
    color: var(--text);
  }
  
  .detail-title {
    font-weight: 700;
    font-size: 22px;
    margin: 0 0 5px;
    color: var(--text);
  }
  
  .muter {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 10px;
  }
  
  .status-mini {
    background: rgba(32, 196, 99, 0.1);
    color: var(--green);
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    display: inline-block;
  }
  
  .price-badge {
    margin-left: auto;
    text-align: right;
  }
  
  .price-badge .big {
    color: var(--green);
    font-size: 22px;
    font-weight: 700;
  }
  
  .detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 14px;
  }

  .detail-modal-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
    gap: 18px;
    margin-top: 20px;
  }

  .detail-panel,
  .detail-price-panel {
    min-width: 0;
    padding: 16px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: #fff;
  }

  .detail-panel h3,
  .detail-price-panel h3 {
    margin: 0;
    font-size: 16px;
    color: var(--text);
  }

  .detail-price-panel {
    background: #f8fbff;
    border-color: #bfdbfe;
  }

  .detail-price-panel h3 i {
    color: var(--blue);
  }

  .detail-price-caption {
    margin: 8px 0 12px;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.5;
  }

  .detail-price-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid #e8eef5;
    color: var(--text);
    font-size: 13px;
  }

  .detail-price-row strong {
    text-align: right;
    white-space: nowrap;
  }

  .detail-price-total {
    margin-top: 4px;
    border-bottom: 0;
    font-size: 15px;
    font-weight: 800;
  }

  .detail-price-total strong {
    color: var(--green);
  }
  
  .detail-grid > div {
    margin-bottom: 15px;
  }
  
  .detail-grid > div > div:last-child {
    font-size: 15px;
    color: var(--text);
    font-weight: 500;
  }
  
  .modal-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 25px;
  }
  
  .modal-actions .btn {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background: var(--blue);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .modal-actions .btn:hover {
    background: var(--blue-dark);
  }
  
  /* EMPTY STATE - PERBAIKAN TAMPILAN SAJA */
  .empty-state {
    text-align: center;
    color: var(--muted);
    margin-top: 50px;
    padding: 30px;
  }
  
  .empty-state i {
    font-size: 50px;
    color: var(--line);
    margin-bottom: 15px;
  }
  
  .empty-state div {
    font-size: 18px;
    margin-bottom: 10px;
  }
  
  /* MODAL LOGOUT - PERBAIKAN TAMPILAN SAJA */
  .modal-logout {
    display: none;
    position: fixed;
    z-index: 3000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
  }
  
  .modal-logout-content {
    background: white;
    border-radius: 16px;
    width: 420px;
    max-width: 90%;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    animation: modalFadeIn 0.3s ease;
  }
  
  .modal-logout-header {
    background: #4BAAF4;
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
  }
  
  .modal-logout-header h2 {
    margin: 15px 0 5px;
    font-size: 20px;
    font-weight: 600;
  }
  
  .modal-logout-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
  }
  
  .modal-logout-close {
    position: absolute;
    right: 15px;
    top: 15px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
    color: white;
    transition: all 0.3s ease;
  }
  
  .modal-logout-close:hover {
    background: rgba(255, 255, 255, 0.3);
  }
  
  .modal-logout-user {
    margin: 18px auto;
    background: #f4f7fb;
    padding: 12px 15px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 15px;
  }
  
  .modal-logout-user .role {
    background: #e8f4ff;
    color: #1b82e2;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
  }
  
  .modal-logout-warning {
    margin: 10px 15px;
    background: #fff3cd;
    color: #856404;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 14px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.4;
  }
  
  .modal-logout-footer {
    display: flex;
    justify-content: space-between;
    padding: 15px;
    background: #f8f9fa;
  }
  
  .btn-cancel {
    background: #f1f1f1;
    border: none;
    color: #333;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.2s;
  }
  
  .btn-cancel:hover {
    background: #e0e0e0;
  }
  
  .btn-confirm {
    background: #4BAAF4;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.2s;
  }
  
  .btn-confirm:hover {
    background: #399de9;
  }
  
  /* PAYMENT NOTIFICATION POPUP - PERBAIKAN TAMPILAN SAJA */
  #payNotify {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 4000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
  }
  
  #payNotify > div {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    text-align: center;
    max-width: 360px;
    width: 90%;
    animation: modalFadeIn 0.3s ease;
  }
  
  #payNotify i {
    font-size: 36px;
    color: #ff9800;
    margin-bottom: 15px;
  }
  
  #payNotify h3 {
    margin: 0 0 10px;
    font-size: 20px;
    color: var(--text);
  }
  
  #payNotify p {
    margin: 0 0 20px;
    color: var(--muted);
    line-height: 1.5;
  }
  
  #payNotifyClose {
    background: var(--bg);
    color: var(--text);
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  #payNotifyClose:hover {
    background: var(--line);
  }
  
  #payNotifyLink {
    background: var(--blue);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
  }
  
  #payNotifyLink:hover {
    background: var(--blue-dark);
  }
  
  /* SUCCESS POPUP - PERBAIKAN TAMPILAN SAJA */
  #popupSuccess {
    position: fixed;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--green);
    color: white;
    padding: 15px 25px;
    border-radius: 10px;
    z-index: 5000;
    box-shadow: 0 10px 25px rgba(32, 196, 99, 0.3);
    font-weight: 500;
    animation: slideDown 0.3s ease;
  }
  
  /* RESPONSIVE - PERBAIKAN TAMPILAN SAJA */
  @media (max-width: 768px) {
    .sidebar {
      width: 0;
    }
    
    .sidebar.open {
      width: 200px;
    }
    
    .main {
      margin-left: 0;
    }
    
    .main.shift {
      margin-left: 0;
    }
    
    .header input {
      width: 180px;
    }
    
    .header input:focus {
      width: 200px;
    }
    
    .header-booking {
      flex-direction: column;
      align-items: flex-start;
      gap: 15px;
    }
    
    .detail-grid {
      grid-template-columns: 1fr;
    }
    .detail-modal-layout {
      grid-template-columns: 1fr;
    }
    
    .booking-card-meta {
      flex-direction: column;
      gap: 8px;
    }
    
    .booking-card-footer {
      flex-direction: column;
    }
    
    .modal-logout-footer {
      flex-direction: column;
      gap: 10px;
    }
    
    .modal-logout-footer button {
      width: 100%;
    }
    
    .notif-dropdown {
      right: 20px;
      width: calc(100% - 40px);
      max-width: 320px;
    }
  }
</style>
</head>
<body>
   <!-- Sidebar - TANPA PERUBAHAN STRUKTUR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo-wrap">
            <img src="{{ asset('images/logo-putih.png') }}" alt="Logo" class="sidebar-logo">
        </div>
        <a href="{{ route('dashboard_user') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="logo" class="icon">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('bookinguser') }}" class="menu-item active">
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
        <a href="{{ route('transaksi_user') }}" class="menu-item">
            <img src="{{ asset('images/pembayaran.png') }}" alt="logo" class="icon">
            <span class="menu-text">Transaksi</span>
        </a>
        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </div>
    
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
          <p>Anda akan keluar dari akun TEKSIKA ini dan perlu login kembali untuk mengakses pesanan.</p>
        </div>
        <div class="modal-logout-footer">
          <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
          <button type="button" class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
        </div>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="main" id="main">
        <!-- Header -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <input type="text" id="searchBookingUser" placeholder="Cari booking...">
            <div class="user-info">
                <div class="header-notif-btn" id="notifBell" title="Notifikasi" style="cursor:pointer;">
                    <i class="fa-solid fa-bell"></i>
                </div>
                @php
                    $avatarUrlHeader = !empty(auth()->user()->avatar) ? asset('storage/'.auth()->user()->avatar) : asset('images/userabu.png');
                @endphp
                <div class="header-profile-pill" onclick="openProfileModal()" title="Profil Akun">
                    <img id="avatarHeader" src="{{ $avatarUrlHeader }}" alt="{{ auth()->user()->name ?? 'User' }}">
                    <span>{{ auth()->user()->name ?? 'Akun Saya' }}</span>
                    <i class="fa-solid fa-chevron-down profile-chevron"></i>
                </div>
            </div>
        </div>
        
        <!-- CONTENT - PERBAIKAN TAMPILAN SAJA -->
        <main class="content">
            <div class="header-booking">
                <h2>BOOKING SAYA</h2>
                <a href="{{ route('request') }}"><button class="btn-request">+ Mulai Request</button></a>
            </div>
            <p class="subtitle">Kelola Semua Booking dan Jadwal Anda</p>
            
            <!-- Dropdown Notifikasi - PERBAIKAN TAMPILAN SAJA -->
            <div class="notif-dropdown" id="notifDropdown">
              <span id="notifCloseX" style="position:absolute; top:8px; right:12px; font-size:18px; color:#888; cursor:pointer; z-index:100;">&times;</span>
              <div class="notif-header">Notifikasi</div>
              <div id="notifList" style="max-height:320px; overflow-y:auto;"></div>
            </div>

            @if(session('success'))
              <div style="margin-bottom:14px; padding:12px 16px; border:1px solid #86efac; background:#f0fdf4; color:#166534; border-radius:8px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
              <div style="margin-bottom:14px; padding:12px 16px; border:1px solid #fecaca; background:#fef2f2; color:#991b1b; border-radius:8px;">{{ session('error') }}</div>
            @endif
            
            <section class="board">
                @if($requests->count())
                    @foreach($requests as $request)
                @php
                  $statusLabels = [
                    'menunggu' => 'Menunggu pemeriksaan admin',
                    'menunggu_persetujuan' => 'Penawaran menunggu persetujuan Anda',
                    'persetujuan_dikirim' => 'Persetujuan terkirim - menunggu verifikasi admin',
                    'dikonfirmasi' => 'Tukang ditugaskan',
                    'dikerjakan' => 'Sedang dikerjakan',
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak',
                    'dibatalkan' => 'Dibatalkan',
                  ];
                  $statusLabel = $statusLabels[$request->status] ?? 'Menunggu pemeriksaan admin';
                  $detailOffer = $request->selectedOffer
                    ?? ($request->estimasi_total !== null ? null : $request->offers->sortBy('total_estimasi')->first());
                  $detailOfferWorker = $request->selectedOffer?->worker
                    ?? $request->worker
                    ?? $request->estimateWorker
                    ?? $detailOffer?->worker;
                  $detailWorkerLabel = $detailOfferWorker?->name
                    ?? ($request->preferredWorker
                      ? 'Preferensi form: ' . $request->preferredWorker->name . ' (belum dikonfirmasi)'
                      : ($request->estimasi_total !== null ? 'Belum dipilih; memakai tarif awal layanan' : 'Belum dipilih'));
                  $detailOfferIsSelected = $request->selectedOffer !== null || $request->worker !== null;
                  $detailBasePrice = $detailOffer?->harga_dasar_snapshot ?? $request->harga_dasar_estimasi ?? $request->harga_layanan;
                  if ($detailOffer && $detailBasePrice === null && (float) $detailOffer->faktor_harga_snapshot > 0) {
                    $detailBasePrice = round((float) $detailOffer->biaya_jasa_snapshot / (float) $detailOffer->faktor_harga_snapshot);
                  }
                  $detailUrgencyCharge = $detailOffer?->biaya_urgensi_snapshot ?? $request->biaya_urgensi_estimasi;
                  $detailDamageCharge = $detailOffer?->biaya_kerusakan_snapshot ?? $request->biaya_kerusakan_estimasi;
                  $detailQualificationCharge = $detailOffer?->biaya_kualifikasi_snapshot ?? $request->biaya_kualifikasi_estimasi;
                  $detailServiceCharge = $detailOffer?->biaya_jasa_snapshot ?? ($request->harga_layanan !== null ? (float) $request->harga_layanan + (float) ($request->biaya_urgensi_estimasi ?? 0) + (float) ($request->biaya_kerusakan_estimasi ?? 0) : null);
                  $detailMaterialCharge = $detailOffer?->biaya_material_snapshot ?? 0;
                  $detailTransportCharge = $detailOffer?->biaya_transportasi ?? $request->biaya_jarak;
                  $detailPriceTotal = $detailOffer?->total_estimasi ?? $request->estimasi_total;
                  $detailPriceStatus = $detailOffer
                    ? ($detailOfferIsSelected ? 'Penawaran tukang pilihan' : 'Penawaran awal berdasarkan kandidat tukang')
                    : ($request->estimasi_total !== null
                      ? ($request->estimateWorker
                        ? 'Estimasi langsung dari tarif ' . $request->estimateWorker->name . ' · rating ' . number_format((float) ($request->rating_estimasi ?? $request->estimateWorker->rating ?? 5), 1) . ' · pengalaman ' . (int) ($request->pengalaman_estimasi ?? $request->estimateWorker->pengalaman ?? 0) . ' th'
                        : 'Estimasi awal memakai harga layanan karena belum ada tukang yang cocok')
                      : 'Menunggu estimasi');
                @endphp
                    <div class="booking-card js-booking"
                        data-id="{{ $request->id_request }}"
                        data-judul="{{ $request->layanan }}"
                        data-kode="{{ $request->kode_booking }}"
                        data-tanggal="{{ \Carbon\Carbon::parse($request->created_at)->translatedFormat('l, d F Y H:i') }}"
                        data-status="{{ strtoupper($statusLabel) }}"
                        data-lokasi="{{ $request->lokasi ?? '-' }}"
                        data-desk="{{ $request->detail ?? '-' }}"
                        data-pembayaran="{{ $request->metode_pembayaran ?? '-' }}"
                        data-estimasi="{{ $detailPriceTotal !== null ? 'Rp ' . number_format($detailPriceTotal, 0, ',', '.') : '-' }}"
                        data-urgensi="{{ $request->urgensi ?? '-' }}"
                        data-kerusakan="{{ $request->tingkat_kerusakan ?? '-' }}"
                        data-kompleksitas="{{ $request->kompleksitas ?? '-' }}"
                        data-tukang="{{ $detailWorkerLabel }}"
                        data-jarak="{{ $detailOffer?->jarak_km ?? $request->jarak_km ?? '' }}"
                        data-jarak-label="{{ $detailOffer ? 'Jarak perjalanan penawaran' : ($request->estimasi_total !== null ? 'Estimasi jarak garis lurus' : 'Jarak') }}"
                        data-jarak-waktu="{{ $detailOffer ? '' : ($request->durasi_perjalanan_menit ?? '') }}"
                        data-harga="{{ $detailPriceTotal ?? '' }}"
                        data-harga-dasar="{{ $detailBasePrice ?? '' }}"
                        data-biaya-kualifikasi="{{ $detailQualificationCharge ?? '' }}"
                        data-biaya-urgensi="{{ $detailUrgencyCharge ?? '' }}"
                        data-biaya-kerusakan="{{ $detailDamageCharge ?? '' }}"
                        data-biaya-jasa="{{ $detailServiceCharge ?? '' }}"
                        data-biaya-material="{{ $detailMaterialCharge ?? '' }}"
                        data-biaya-transportasi="{{ $detailTransportCharge ?? '' }}"
                        data-total-penawaran="{{ $detailPriceTotal ?? '' }}"
                        data-status-harga="{{ $detailPriceStatus }}">
                        <div class="booking-card-content">
                            <div class="booking-card-header">
                                <div class="booking-title">{{ $request->layanan }}</div>
                                <div class="booking-code">Kode: <b>{{ $request->kode_booking ?? '-' }}</b></div>
                            </div>
                            <div class="booking-card-meta">
                                <span class="booking-date">
                                    <i class="far fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($request->created_at)->translatedFormat('d M Y H:i') }}
                                </span>
                                <span class="booking-status {{ in_array($request->status, ['dikonfirmasi', 'dikerjakan', 'selesai'], true) ? 'status-progress' : (in_array($request->status, ['ditolak', 'dibatalkan'], true) ? ($request->status === 'dibatalkan' ? 'status-cancelled' : 'status-rejected') : 'status-waiting') }}">
                                    <i class="fas fa-circle"></i>
                                  {{ $statusLabel }}
                                </span>
                            </div>
                            <div style="margin: 6px 0 2px; font-weight: 700; color: #007bff; font-size: 0.95rem;">
                                @if($request->offers->count())
                                  {{ $request->offers->count() }} penawaran tukang tersedia
                                @elseif($request->status_admin === 'diterima')
                                  Total disetujui: Rp {{ number_format($request->estimasi_total ?? $request->jumlah_pembayaran ?? 0, 0, ',', '.') }}
                                @elseif($request->estimasi_total !== null)
                                  Estimasi awal: Rp {{ number_format($request->estimasi_total, 0, ',', '.') }}
                                @else
                                  <span style="color:#64748b; font-weight:500;">Harga menunggu pemeriksaan admin</span>
                                @endif
                            </div>
                              @if($request->offers->count())
                                <div style="margin-top:10px; padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; color:#334155; font-size:0.88rem;">
                                  <strong>Bandingkan penawaran</strong>
                                  <div style="display:grid; gap:9px; margin-top:9px;">
                                    @foreach($request->offers as $offer)
                                      <article style="padding:10px; border:1px solid {{ (int) $request->id_penawaran_terpilih === (int) $offer->id ? '#22c55e' : '#dbe2ea' }}; border-radius:7px; background:#fff;">
                                        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                          @if($offer->worker?->foto)<img src="{{ asset('storage/' . $offer->worker->foto) }}" alt="{{ $offer->worker->name }}" onerror="this.onerror=null;this.src='{{ asset('images/userabu.png') }}';" style="width:42px;height:42px;object-fit:cover;border-radius:50%;">@endif
                                          <div style="flex:1; min-width:150px;"><strong>{{ $offer->worker?->name ?? 'Mitra' }}</strong><div style="color:#64748b;font-size:.8rem;">{{ $offer->keahlian_snapshot }} · {{ $offer->pengalaman_snapshot }} thn · ★ {{ number_format($offer->rating_snapshot, 1) }} ({{ $offer->jumlah_ulasan_snapshot }} ulasan)</div></div>
                                          <div style="font-size:.82rem;">Jasa + material <b>Rp {{ number_format($offer->biaya_jasa_material, 0, ',', '.') }}</b><br>Biaya jarak <b>Rp {{ number_format($offer->biaya_transportasi, 0, ',', '.') }}</b></div>
                                          <strong style="font-size:.95rem;">Rp {{ number_format($offer->total_estimasi, 0, ',', '.') }}</strong>
                                          @if($offer->status === 'dipilih')<span style="color:#166534;font-weight:700;">Pilihan Anda</span>@endif
                                          @if($offer->status === 'ditugaskan')<span style="color:#166534;font-weight:700;">Tukang ditugaskan</span>@endif
                                          @if($request->status === 'menunggu_persetujuan' && $offer->status === 'ditawarkan')
                                            <form action="{{ route('bookinguser.pilih-penawaran', $request->id_request) }}" method="POST" onsubmit="return confirm('Pilih {{ $offer->worker?->name }} dengan total Rp {{ number_format($offer->total_estimasi, 0, ',', '.') }}?');">
                                              @csrf
                                              <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                              <button type="submit" class="btn-detail">Pilih Tukang</button>
                                            </form>
                                          @endif
                                        </div>
                                        <div style="margin-top:5px;color:#64748b;font-size:.78rem;">Biaya jarak dihitung dari kantor pusat, {{ $offer->jarak_km }} km · estimasi {{ $offer->estimasi_durasi_menit }} menit</div>
                                        @if($offer->worker && $offer->worker->reviews->isNotEmpty())
                                          <div style="display:grid;gap:3px;margin-top:6px;color:#475569;font-size:.78rem;">
                                            @foreach($offer->worker->reviews->whereNotNull('ulasan')->take(2) as $review)
                                              <span>“{{ \Illuminate\Support\Str::limit($review->ulasan, 140) }}” · {{ $review->rating }}/5</span>
                                            @endforeach
                                          </div>
                                        @endif
                                      </article>
                                    @endforeach
                                  </div>
                                  <div style="margin-top:8px;color:#64748b;">Kerusakan {{ ucfirst($request->tingkat_kerusakan ?? '-') }}, kompleksitas {{ ucfirst($request->kompleksitas ?? '-') }}, urgensi {{ ucfirst($request->urgensi ?? '-') }}.@if($request->penawaran_catatan) Catatan admin: {{ $request->penawaran_catatan }}@endif</div>
                                </div>
                              @elseif($request->penawaran_dikirim_at && $request->estimasi_total)
                                <div style="margin-top:10px;padding:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:.86rem;">Penawaran lama: Rp {{ number_format($request->estimasi_total, 0, ',', '.') }}. Pilihan tukang tidak tersedia untuk penawaran lama.</div>
                              @endif
                              @if($request->status_admin == 'diterima' && $request->worker)
                                <div class="booking-worker">
                                    <i class="fa fa-user" style="color:#1e88e5"></i>
                                    <span>Tukang: <b>{{ $request->worker->name }}</b></span>
                                </div>
                            @endif
                            <div class="booking-card-footer">
                                <button class="btn-detail" title="Lihat Detail"><i class="fa fa-eye"></i> Detail</button>
                                @if($request->status === 'persetujuan_dikirim')
                                  <span style="align-self:center; color:#166534; font-size:0.88rem;">Pilihan Anda menunggu verifikasi admin.</span>
                                @endif
                                @if(in_array($request->status, ['menunggu', 'menunggu_persetujuan'], true) && $request->status_admin === 'belum_diterima')
                                    <form action="{{ route('bookinguser.batal', $request->id_request) }}" method="POST" class="no-pointer" onsubmit="return confirm('Batalkan booking ini?');">
                                        @csrf
                                    <button type="submit" class="btn-cancel" title="Batalkan">Batalkan Pesanan</button>
                                    </form>
                                @endif
                                @if($request->status === 'selesai' && !$request->review)
                                  <form action="{{ route('bookinguser.ulasan', $request->id_request) }}" method="POST" style="display:flex;gap:6px;align-items:center;">
                                    @csrf
                                    <select name="rating" required aria-label="Rating worker"><option value="">Rating</option>@for($score = 5; $score >= 1; $score--)<option value="{{ $score }}">{{ $score }} bintang</option>@endfor</select>
                                    <input type="text" name="ulasan" maxlength="2000" placeholder="Ulasan (opsional)" aria-label="Ulasan worker">
                                    <button type="submit" class="btn-detail">Kirim Ulasan</button>
                                  </form>
                                @elseif($request->review)
                                  <span style="align-self:center;color:#166534;">Rating Anda: {{ $request->review->rating }}/5</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        <div>Belum ada booking. Silakan request layanan terlebih dahulu.</div>
                    </div>
                @endif
            </section>
        </main>
    </div>
    
    <!-- ====== MODAL DETAIL BOOKING - PERBAIKAN TAMPILAN SAJA ====== -->
    <div class="modal" id="modalDetail" aria-hidden="true">
        <div class="backdrop" data-close="detail"></div>
        <div class="dialog">
            <button class="close-x" data-close="detail" aria-label="Tutup">&times;</button>
            <div style="display:flex;align-items:start;gap:15px">
                <div>
                    <div class="detail-title" id="dJudul"></div>
                    <div class="muter" id="dKode"></div>
                    <div class="status-mini" id="dStatus"></div>
                </div>
                <div class="price-badge">
                    <div class="big" id="dHarga"></div>
                    <div class="muter" id="dPembayaran"></div>
                </div>
            </div>
            <div class="detail-modal-layout">
                <div class="detail-panel">
                    <h3>Detail pesanan</h3>
                    <div class="detail-grid">
                        <div>
                            <div class="muter">Jadwal</div>
                            <div id="dJadwal"></div>
                        </div>
                        <div>
                            <div class="muter">Tukang</div>
                            <div id="dTukang"></div>
                        </div>
                        <div>
                            <div class="muter">Lokasi</div>
                            <div id="dLokasi"></div>
                        </div>
                        <div>
                            <div class="muter">Keterangan</div>
                            <div id="dDesk" style="white-space: pre-wrap; word-break: break-word;"></div>
                        </div>
                        <div>
                            <div class="muter">Tingkat Urgensi</div>
                            <div id="dUrgensi"></div>
                        </div>
                        <div>
                            <div class="muter">Tingkat Kerusakan</div>
                            <div id="dKerusakan"></div>
                        </div>
                        <div>
                            <div class="muter">Kompleksitas</div>
                            <div id="dKompleksitas"></div>
                        </div>
                        <div>
                            <div class="muter">Jarak & estimasi durasi total</div>
                            <div id="dJarak"></div>
                        </div>
                        <div>
                            <div class="muter">Metode Pembayaran</div>
                            <div id="dPembayaranDetail"></div>
                        </div>
                    </div>
                </div>
                <div class="detail-price-panel">
                    <h3><i class="fa-solid fa-receipt"></i> Transparansi harga</h3>
                    <p class="detail-price-caption" id="dHargaStatus"></p>
                    <div class="detail-price-row"><span>Harga awal jasa tukang</span><strong id="dHargaDasar"></strong></div>
                    <div class="detail-price-row"><span>Penyesuaian rating & pengalaman</span><strong id="dBiayaKualifikasi"></strong></div>
                    <div class="detail-price-row"><span>Tambahan urgensi</span><strong id="dBiayaUrgensi"></strong></div>
                    <div class="detail-price-row"><span>Tambahan tingkat kerusakan</span><strong id="dBiayaKerusakan"></strong></div>
                    <div class="detail-price-row"><span>Subtotal jasa</span><strong id="dBiayaJasa"></strong></div>
                    <div class="detail-price-row"><span>Material / suku cadang</span><strong id="dBiayaMaterial"></strong></div>
                    <div class="detail-price-row"><span>Transportasi</span><strong id="dBiayaTransportasi"></strong></div>
                    <div class="detail-price-row detail-price-total"><span>Total penawaran</span><strong id="dHargaTotal"></strong></div>
                    <p class="detail-price-caption" id="dHargaNote"></p>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn" data-close="detail">Tutup</button>
            </div>
        </div>
    </div>
    
    <!-- Popup Notifikasi Pembayaran - PERBAIKAN TAMPILAN SAJA -->
    <div id="payNotify" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:16px; padding:24px 22px; box-shadow:0 8px 32px rgba(0,0,0,0.12); text-align:center; max-width:360px; margin:auto;">
        <i class="fa-solid fa-bell" style="font-size:2.2rem; color:#ff9800; margin-bottom:12px;"></i>
        <h3 style="margin:0 0 8px;">Konfirmasi Admin</h3>
        <p id="payNotifyText" style="margin:0 0 14px; color:#333"></p>
        <div style="display:flex; gap:8px; justify-content:center;">
          <button id="payNotifyClose" style="background:#eee; color:#333; border:none; border-radius:8px; padding:8px 14px; font-weight:600;">Nanti</button>
          <a id="payNotifyLink" href="#" style="background:#1856a7; color:#fff; border:none; border-radius:8px; padding:8px 14px; font-weight:600; text-decoration:none;">Bayar Sekarang</a>
        </div>
      </div>
    </div>
    
    <script>
    // Toggle Sidebar - TANPA PERUBAHAN FUNGSI
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('main').classList.toggle('shift');
    });
    
    // Modal Logout - TANPA PERUBAHAN FUNGSI
    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }
    
    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
    
    function confirmLogout() {
        window.location.href = "/logout";
    }
    
    // Notifikasi - PERBAIKAN FUNGSI
    (function(){
        const bell = document.getElementById('notifBell');
        const dropdown = document.getElementById('notifDropdown');
        const list = document.getElementById('notifList');
        const bookings = @json($requests);
        function fmtTanggal(iso){
            try{ return new Date(iso).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }); }catch(e){ return ''; }
        }
        function fmtRupiah(num){ try{ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(Number(num||0)); }catch(e){ return 'Rp0'; } }
        const rows = [];
        bookings.forEach(b => {
            // Konfirmasi pembayaran (non-COD)
            if (b.status_admin === 'diterima' && b.metode_pembayaran !== 'COD' && b.jumlah_pembayaran && Number(b.jumlah_pembayaran) > 0 && Number(b.bayar_sekarang) !== 1) {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-credit-card"></i></span>
                  <div>
                    <div>Konfirmasi pembayaran untuk <b>${b.layanan}</b>. Total: <b>${fmtRupiah(b.jumlah_pembayaran)}</b></div>
                    <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
                    <div style="margin-top:8px;"><a href="{{ url('/pembayaran') }}?booking=${encodeURIComponent(b.kode_booking)}" class="btn-detail" style="text-decoration:none; font-size:13px; padding:4px 10px;">Bayar Sekarang</a></div>
                  </div>
                </div>`);
            }
            // Diterima (COD)
            if (b.status_admin === 'diterima' && b.metode_pembayaran === 'COD') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-circle-check"></i></span>
                  <div>
                    <div>Booking <b>${b.layanan}</b> dikonfirmasi (COD). Siapkan pembayaran tunai.</div>
                    <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
                  </div>
                </div>`);
            }
            // Ditolak
            if (b.status_admin === 'ditolak') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                  <div>
                    <div>Booking <b>${b.layanan}</b> ditolak.</div>
                    <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
                  </div>
                </div>`);
            }
            // Dibatalkan (oleh user)
            if (b.status_admin === 'dibatalkan') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-ban"></i></span>
                  <div>
                    <div>Booking <b>${b.layanan}</b> dibatalkan.</div>
                    <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
                  </div>
                </div>`);
            }
            if (b.status === 'menunggu_persetujuan') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                  <div>
                    <div>Penawaran untuk <b>${b.layanan}</b> sudah tersedia. Silakan periksa rincian dan berikan keputusan.</div>
                    <div class="notif-time">${fmtTanggal(b.penawaran_dikirim_at || b.updated_at)}</div>
                  </div>
                </div>`);
            } else if (b.status === 'persetujuan_dikirim') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-hourglass-half"></i></span>
                  <div>
                    <div>Persetujuan penawaran <b>${b.layanan}</b> sedang diverifikasi admin.</div>
                    <div class="notif-time">${fmtTanggal(b.persetujuan_user_at || b.updated_at)}</div>
                  </div>
                </div>`);
            } else if (b.status === 'menunggu') {
                rows.push(`
              <div class="notif-item">
                <span class="notif-icon"><i class="fa-regular fa-clock"></i></span>
                <div>
                  <div>Request <b>${b.layanan}</b> menunggu pemeriksaan admin.</div>
                  <div class="notif-time">${fmtTanggal(b.created_at)}</div>
                </div>
              </div>`);
            }
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
    
    // search filter
    document.getElementById('searchBookingUser').addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.booking-card.js-booking').forEach(function(card) {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
    
    // modal detail
    document.querySelectorAll('.btn-detail').forEach(btn => {
      if (btn.closest('form')) return;
        btn.addEventListener('click', function() {
            const card = this.closest('.booking-card');
            document.getElementById('dJudul').textContent = card.dataset.judul;
            document.getElementById('dKode').textContent = "Kode: " + card.dataset.kode;
            document.getElementById('dStatus').textContent = card.dataset.status;
            const formatMoney = value => value !== '' && value !== null && Number.isFinite(Number(value))
                ? `Rp ${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(Number(value)))}`
                : (card.dataset.statusHarga === 'Menunggu estimasi' ? 'Menunggu estimasi' : 'Tidak tercatat');
            document.getElementById('dHarga').textContent = formatMoney(card.dataset.harga);
            document.getElementById('dHargaTotal').textContent = formatMoney(card.dataset.totalPenawaran);
            document.getElementById('dPembayaran').textContent = card.dataset.pembayaran;
            document.getElementById('dPembayaranDetail').textContent = card.dataset.pembayaran;
            document.getElementById('dJadwal').textContent = card.dataset.tanggal;
            document.getElementById('dTukang').textContent = card.dataset.tukang;
            document.getElementById('dLokasi').textContent = card.dataset.lokasi;
            document.getElementById('dDesk').textContent = card.dataset.desk;
            document.getElementById('dUrgensi').textContent = card.dataset.urgensi;
            document.getElementById('dKerusakan').textContent = card.dataset.kerusakan || '-';
            document.getElementById('dKompleksitas').textContent = card.dataset.kompleksitas || '-';
            const distance = card.dataset.jarak;
            const travelTime = card.dataset.jarakWaktu;
            document.getElementById('dJarak').textContent = distance
                ? `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(Number(distance))} km (${card.dataset.jarakLabel})${travelTime ? ` · total sekitar ${travelTime} menit` : ''}`
                : 'Dihitung saat admin mengirim penawaran';
            document.getElementById('dHargaStatus').textContent = card.dataset.statusHarga;
            document.getElementById('dHargaDasar').textContent = formatMoney(card.dataset.hargaDasar);
            document.getElementById('dBiayaKualifikasi').textContent = formatMoney(card.dataset.biayaKualifikasi);
            document.getElementById('dBiayaUrgensi').textContent = formatMoney(card.dataset.biayaUrgensi);
            document.getElementById('dBiayaKerusakan').textContent = formatMoney(card.dataset.biayaKerusakan);
            document.getElementById('dBiayaJasa').textContent = formatMoney(card.dataset.biayaJasa);
            document.getElementById('dBiayaMaterial').textContent = formatMoney(card.dataset.biayaMaterial);
            document.getElementById('dBiayaTransportasi').textContent = formatMoney(card.dataset.biayaTransportasi);
            document.getElementById('dHargaNote').textContent = card.dataset.statusHarga === 'Menunggu estimasi'
                ? 'Estimasi harga akan muncul setelah rincian booking tersedia.'
                : (card.dataset.statusHarga.startsWith('Estimasi awal')
                    ? 'Estimasi langsung dari isian form. Biaya material/suku cadang belum termasuk; admin dapat mengirim harga final setelah pemeriksaan.'
                    : 'Rincian mengikuti penawaran tukang. Material dan transportasi ditampilkan terpisah.');
            document.getElementById('modalDetail').classList.add('open');
            (function(){ const m = document.getElementById('main'); if (m) m.classList.add('modal-dim'); })();
        });
    });
    
    document.querySelectorAll('[data-close="detail"]').forEach(el => {
        el.addEventListener('click', () => {
            document.getElementById('modalDetail').classList.remove('open');
            (function(){ const m = document.getElementById('main'); if (m) m.classList.remove('modal-dim'); })();
        });
    });
    
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalDetail');
        if (e.target.classList.contains('backdrop')) {
            modal.classList.remove('open');
            const m = document.getElementById('main'); if (m) m.classList.remove('modal-dim');
        }
    });
    
    // Notifikasi bayar jika ada booking dikonfirmasi & metode non-COD
    (function(){
        try {
            const bookings = @json($requests);
            const b = bookings.find(x => x.status_admin === 'diterima' && x.metode_pembayaran !== 'COD' && x.jumlah_pembayaran && Number(x.jumlah_pembayaran) > 0 && Number(x.bayar_sekarang) !== 1);
            if (b) {
                const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(b.jumlah_pembayaran);
                const text = `Request Anda (${b.layanan}) telah dikonfirmasi admin. Total yang harus dibayar: ${rupiah}.`;
                document.getElementById('payNotifyText').textContent = text;
                // arahkan ke halaman pembayaran detail atau form create dengan kode booking
                const link = document.getElementById('payNotifyLink');
                // Arahkan ke halaman create pembayaran dengan kode booking
                link.href = `{{ url('/pembayaran') }}?booking=${encodeURIComponent(b.kode_booking)}`;
                const modal = document.getElementById('payNotify');
                modal.style.display = 'flex';
                document.getElementById('payNotifyClose').onclick = function(){ modal.style.display = 'none'; };
            }
        } catch(e) { /* ignore */ }
    })();
    </script>
    
    @if(session('success'))
    <div id="popupSuccess" style="position:fixed;top:30px;left:50%;transform:translateX(-50%);background:#4caf50;color:#fff;padding:16px 32px;border-radius:8px;z-index:9999;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
        {{ session('success') }}
    </div>
    <script>
    setTimeout(function() {
        document.getElementById('popupSuccess').style.display = 'none';
    }, 2500);
    </script>
    @endif

    @include('user._profile_modal')
</body>
</html>