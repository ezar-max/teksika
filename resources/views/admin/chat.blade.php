<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat & Komunikasi — TEKSIKA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS & Icons -->
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatadmin.css') }}">
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
        <a href="{{ route('chat') }}" class="menu-item active">
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

        <!-- Content -->
        <div class="chat-content">
            <h1 class="chat-title"><i class="fa-solid fa-comments" style="color:#007bff; margin-right:8px;"></i> Pesan & Komunikasi</h1>
            <p class="chat-subtitle">Pusat interaksi langsung dengan customer untuk konfirmasi perbaikan dan konsultasi teknis</p>
            
            <div class="chat-stats">
                <div class="stats">
                    <div class="card blue">
                        <div>
                            <p class="label">Total Pesan</p>
                            <h2>{{ $totalMessages ?? 0 }}</h2>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-message"></i>
                        </div>
                    </div>

                    <div class="card purple">
                        <div>
                            <p class="label">Pesan Belum Dibaca</p>
                            <h2>{{ $unreadCount ?? 0 }}</h2>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                    </div>

                    <div class="card green">
                        <div>
                            <p class="label">Total Pelanggan</p>
                            <h2>{{ $totalCustomers ?? 0 }}</h2>
                        </div>
                        <div class="icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chat-main">
                <!-- Chat Sidebar (Daftar Pengguna) -->
                <div class="chat-sidebar">
                    <div class="chat-sidebar-header">
                        <span>Percakapan</span>
                        <div class="chat-filter">
                            <button type="button" class="active">Semua</button>
                            <button type="button">Belum Dibaca</button>
                        </div>
                    </div>
                    
                    <div class="chat-list">
                        @forelse($users as $user)
                        <form method="GET" action="{{ route('chat') }}">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="chat-user{{ isset($selectedUser) && $selectedUser->id == $user->id ? ' active' : '' }}">
                                <i class="fa-solid fa-user"></i>
                                <span class="chat-username">{{ $user->name }}</span>
                                <span class="chat-role {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                                @if(isset($unreadUserIds) && in_array($user->id, $unreadUserIds))
                                    <span style="display:inline-block; width:10px; height:10px; background:#ef4444; border-radius:50%; margin-left:8px; flex-shrink:0;"></span>
                                @endif
                            </button>
                        </form>
                        @empty
                            <div style="padding:20px; text-align:center; color:#94a3b8; font-size:0.86rem;">Belum ada kontak percakapan.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Window (Area Percakapan) -->
                <div class="chat-window">
                    <div class="chat-window-header">
                        <div class="chat-user-info">
                            <i class="fa-solid fa-user"></i>
                            <div>
                                <span class="chat-username" style="font-size:1.05rem;">
                                    {{ $selectedUser->name ?? 'Pilih Percakapan' }}
                                </span>
                                @if(isset($selectedUser))
                                    <div class="chat-status">
                                        <i class="fa-solid fa-circle" style="font-size:0.5rem; vertical-align:middle; margin-right:3px;"></i> Online &bull; {{ ucfirst($selectedUser->role) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if(isset($selectedUser) && !empty($selectedUser->phone))
                            <a href="tel:{{ $selectedUser->phone }}" style="color:#007bff; font-size:1.1rem; text-decoration:none;" title="Hubungi Customer">
                                <i class="fa-solid fa-phone"></i>
                            </a>
                        @endif
                    </div>

                    <div class="chat-container" data-own-sender="admin" data-last-id="{{ $chats->last()->id ?? 0 }}" @if(isset($selectedUser)) data-messages-url="{{ route('chat.messages', ['user_id' => $selectedUser->id]) }}" @endif>
                        @if(isset($chats) && count($chats) > 0)
                            @foreach($chats as $chat)
                            <div class="bubble-row {{ $chat->sender === 'admin' ? 'user' : 'admin' }}">
                                <div class="chat-bubble">{{ $chat->message }}</div>
                                <div class="muter">
                                    {{ \Carbon\Carbon::parse($chat->created_at)->timezone('Asia/Jakarta')->format('H:i') }}
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="chat-empty">
                                <i class="fa-regular fa-comments" style="font-size:2.5rem; display:block; margin-bottom:8px;"></i>
                                {{ isset($selectedUser) ? 'Belum ada percakapan dengan pengguna ini. Mulai dengan mengirimkan pesan di bawah.' : 'Pilih percakapan dari daftar di sebelah kiri untuk melihat pesan.' }}
                            </div>
                        @endif
                    </div>

                    @if(isset($selectedUser))
                    <div class="chat-input">
                        <form action="{{ route('chat.send') }}" method="POST" class="chat-input-form">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                            <input type="text" name="message" placeholder="Tuliskan balasan untuk customer..." required autocomplete="off">
                            <button type="submit" title="Kirim Balasan">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Profile Modal -->
    @include('admin._profile_modal')

    <!-- Modal Logout Tunggal Admin -->
    @include('admin._logout_modal')

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('js/chatadmin.js') }}"></script>
    <script src="{{ asset('js/chat.js') }}"></script>
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

    // Filter Unread/All
    (function(){
        var filter = document.querySelector('.chat-filter');
        var list = document.querySelector('.chat-list');
        if (!filter || !list) return;
        var buttons = filter.querySelectorAll('button');
        if (buttons.length < 2) return;
        var btnAll = buttons[0];
        var btnUnread = buttons[1];
        var unreadIds = @json($unreadUserIds ?? []);

        function setActive(btn){
            buttons.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
        }
        function showAll(){
            list.querySelectorAll('form').forEach(function(f){ f.style.display = ''; });
            setActive(btnAll);
        }
        function showUnread(){
            list.querySelectorAll('form').forEach(function(f){
                var input = f.querySelector('input[name="user_id"]');
                var uid = input ? input.value : null;
                var show = false;
                if (uid !== null) {
                    show = unreadIds.includes(Number(uid)) || unreadIds.includes(uid);
                }
                f.style.display = show ? '' : 'none';
            });
            setActive(btnUnread);
        }

        btnAll.addEventListener('click', function(e){ e.preventDefault(); showAll(); });
        btnUnread.addEventListener('click', function(e){ e.preventDefault(); showUnread(); });
    })();

    // Auto scroll ke bawah saat load
    (function(){
        var box = document.querySelector('.chat-container');
        if (box) { box.scrollTop = box.scrollHeight; }
    })();
    </script>
</body>
</html>
