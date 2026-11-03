<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="{{ asset('css/chatadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatuser.css') }}">
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
            <!-- Search dihapus sesuai permintaan -->
            <div class="user-info">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>
        <!-- content -->
        <div class="chat-content">
            <h2 class="chat-title">Messages & Communication</h2>
            <p class="chat-subtitle">Kelola komunikasi dengan customer dan tukang</p>
            <div class="chat-stats">
                <div class="stats">
            <div class="card blue">
              <div>
                <p class="label">Total Pesan</p>
                <h2>{{ $totalMessages ?? 0 }}</h2>
              </div>
              <div class="icon">
                <i class="fa-solid fa-calendar-days"></i>
              </div>
            </div>

            <div class="card purple">
              <div>
                <p class="label">Belum Dibaca</p>
                <h2>{{ $unreadCount ?? 0 }}</h2>
              </div>
              <div class="icon">
                <i class="fa-solid fa-envelope"></i>
              </div>
            </div>

            <div class="card ijo">
              <div>
                <p class="label">Customer</p>
                <h2>{{ $totalCustomers ?? 0 }}</h2>
              </div>
              <div class="icon">
                <i class="fa-solid fa-users"></i>
              </div>
            </div>

                    </div>
            </div>
            <div class="chat-main">
                <div class="chat-sidebar">
                    <div class="chat-sidebar-header">
                        <span>Conversations</span>
                        <div class="chat-filter">
                            <button class="active">All</button>
                            <button>Unread</button>
                        </div>
                    </div>
                    <div class="chat-list">
                        @foreach($users as $user)
                        <!-- Panggil dengan route('chat') -->
<form method="GET" action="{{ route('chat') }}">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="chat-user{{ isset($selectedUser) && $selectedUser->id == $user->id ? ' active' : '' }}">
                                <i class="fa-solid fa-user"></i>
                                <span class="chat-username">{{ $user->name }}</span>
                                <span class="chat-role {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                                @if(isset($unreadUserIds) && in_array($user->id, $unreadUserIds))
                                  <span style="display:inline-block;width:10px;height:10px;background:#ff3b30;border-radius:50%;margin-left:8px;"></span>
                                @endif
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                <div class="chat-window">
                <div class="chat-window-header">
                <div class="chat-user-info">
                <i class="fa-solid fa-user"></i>
                <span class="chat-username">
                {{ $selectedUser->name ?? 'Pilih User' }}
                </span>
                <span class="chat-status">
                @if(isset($selectedUser))
                Online &bull; {{ ucfirst($selectedUser->role) }}
                @endif
                </span>
                </div>
                <i class="fa-solid fa-phone"></i>
                </div>
                <div class="chat-container">
                @if(isset($chats) && count($chats) > 0)
                @foreach($chats as $chat)
                <div class="bubble-row {{ $chat->sender === 'admin' ? 'user' : 'admin' }}">
                <div class="chat-bubble">{{ $chat->message }}</div>
                <div class="muter" style="font-size:12px;margin-top:4px;">
                {{ \Carbon\Carbon::parse($chat->created_at)->format('H:i') }}
                </div>
                </div>
                @endforeach
                @else
                <div class="chat-empty">Belum ada pesan.</div>
                @endif
                </div>
                @if(isset($selectedUser))
                <div class="chat-input">
                <form action="{{ route('chat.send') }}" method="POST" class="chat-input-form">
                @csrf
                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                <input type="text" name="message" placeholder="Ketik pesan..." required>
                <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
                </div>
                @endif
                </div>
            </div>
        </div>
        <!-- end content -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="{{ asset('js/chatadmin.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
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
              // dukung string/number
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