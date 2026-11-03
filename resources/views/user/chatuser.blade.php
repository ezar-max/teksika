<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat User & Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatuser.css') }}">
      <link rel="stylesheet" href="{{ asset('css/transaksiuser.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <!-- Main Content -->
    <div class="main">
        <!-- ======= HEADER ATAS ======= -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <input type="text" placeholder="Search">
            <div class="user-info">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>

        <!-- ======= CHAT AREA ======= -->
        <div class="chat-area">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-header-info">
                    <i class="fa-solid fa-user-tie admin-icon"></i>
                    <span class="admin-name">Admin</span>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-container" id="chatContainer">
                @if(isset($chats) && count($chats) > 0)
                    @foreach($chats as $chat)
                        <div class="bubble-row {{ $chat->sender === 'user' ? 'user' : 'admin' }}">
                            <div class="chat-bubble">
                                {{ $chat->message }}
                            </div>
                            <div class="muter" style="font-size:12px;margin-top:4px;">
                                {{ \Carbon\Carbon::parse($chat->created_at)->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="chat-empty">Belum ada pesan.</div>
                @endif
            </div>

            <!-- Chat Input -->
            <div class="chat-input">
                <form action="{{ route('chatuser.send') }}" method="POST" class="chat-input-form">
                    @csrf
                    <input type="hidden" name="admin_id" value="1"> <!-- Ganti 1 dengan ID admin sebenarnya jika dinamis -->
                    <input type="text" name="message" id="chatMessage" placeholder="Ketik pesan..." required>
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/chatuser.js') }}"></script>
    <script>
      // Auto scroll ke bawah saat halaman dimuat
      (function(){
        var box = document.getElementById('chatContainer');
        if (box) { box.scrollTop = box.scrollHeight; }
      })();
    </script>
</body>
</html>
