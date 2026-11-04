<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konsultasi &amp; Chat — TEKSIKA</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatuser.css') }}">
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
        <a href="{{ route('chatuser') }}" class="menu-item active">
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
          <p>Anda akan keluar dari akun TEKSIKA dan sesi chat akan disimpan.</p>
        </div>
        <div class="modal-logout-footer">
          <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
          <button type="button" class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <!-- ======= HEADER ATAS ======= -->
        <header class="header">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <input type="text" placeholder="Cari pesan atau topik..." aria-label="Cari pesan">
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

        <!-- ======= CHAT AREA ======= -->
        <div class="chat-area">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="admin-avatar-wrap">
                        <i class="fa-solid fa-headset"></i>
                        <span class="admin-online-badge" title="Online"></span>
                    </div>
                    <div class="chat-header-text">
                        <span class="admin-name">Admin Layanan TEKSIKA</span>
                        <span class="admin-status"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> Online • Siap Membantu</span>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-container" id="chatContainer" data-own-sender="user" data-last-id="{{ $chats->last()->id ?? 0 }}" data-messages-url="{{ route('chatuser.messages') }}">
                @if(isset($chats) && count($chats) > 0)
                    @foreach($chats as $chat)
                        <div class="bubble-row {{ $chat->sender === 'user' ? 'user' : 'admin' }}">
                            <div class="chat-bubble">
                                {{ $chat->message }}
                            </div>
                            <div class="bubble-time">
                                {{ \Carbon\Carbon::parse($chat->created_at)->timezone('Asia/Jakarta')->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="chat-empty">
                        <i class="fa-regular fa-comments"></i>
                        <div>Belum ada pesan. Mulai obrolan untuk konsultasi dengan admin TEKSIKA!</div>
                    </div>
                @endif
            </div>

            <!-- Chat Input -->
            <div class="chat-input">
                <form action="{{ route('chatuser.send') }}" method="POST" class="chat-input-form">
                    @csrf
                    <input type="hidden" name="admin_id" value="1">
                    <input type="text" name="message" id="chatMessage" placeholder="Tulis pesan atau pertanyaan Anda..." required autocomplete="off">
                    <button type="submit" aria-label="Kirim Pesan">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @include('user._profile_modal')

    <!-- JS -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/chat.js') }}"></script>
    <script>
      (function(){
        var box = document.getElementById('chatContainer');
        if (box) { box.scrollTop = box.scrollHeight; }
      })();
    </script>
</body>
</html>
