<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <img src="{{ asset('images/logo-putih.png') }}" alt="Logo" class="sidebar-logo">

        <a href="{{ route('admin.home') }}" class="menu-item">
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
        <a href="{{ route('admin.pricing.index') }}" class="menu-item">
            <i class="fa-solid fa-sliders" style="font-size:20px; width:28px; text-align:center;"></i>
            <span class="menu-text">Aturan Tarif</span>
        </a>
        <a href="{{ route('chat') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon" weight="50" height="50">
            <span class="menu-text">Chat</span>
        </a>

        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </div>

    <!-- Modal Logout -->
    <div id="logoutModal" class="modal-logout">
      <div class="modal-logout-content">
        <span class="modal-logout-close" onclick="closeLogoutModal()">&times;</span>
        <div class="modal-logout-header">
          <i class="fa-solid fa-right-from-bracket icon-logout"></i>
          <h2>Konfirmasi Log out</h2>
          <p>Yakin Ingin Keluar Dari Akun?</p>
        </div>
        <div class="modal-logout-user">
          <div class="user-info">
            <i class="fa-solid fa-user-circle"></i>
            <div>
                            <strong>ADMIN</strong><br>
                            <span>{{ $admin->name ?? '-' }} | {{ $admin->email ?? '-' }}</span>
                        </div>
                        <span class="role">Admin</span>
          </div>
        </div>
        <div class="modal-logout-warning">
          <i class="fa-solid fa-circle-exclamation"></i>
          <p>Anda akan keluar dari akun ini dan perlu login kembali untuk mengakses admin panel</p>
        </div>
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
            <input type="text" id="searchServices" placeholder="Cari layanan...">
            <div class="user-info" style="position:relative;">
                @php
                    $pendingBookingCount = \App\Models\Request::where('status_admin', 'belum_diterima')->count();
                    $pendingPaymentsCount = \App\Models\Pembayaran::where('status', 'pending')->count();
                    $notifTotalCount = $pendingBookingCount + $pendingPaymentsCount;
                    $notifBookings = \App\Models\Request::where('status_admin', 'belum_diterima')->orderBy('created_at', 'desc')->take(5)->get();
                    $notifPayments = \App\Models\Pembayaran::where('status', 'pending')->orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                <div id="notifBell" class="notif-bell" style="position:relative; margin-right:12px; cursor:pointer;">
                    <i class="fa-solid fa-bell"></i>
                    @if($notifTotalCount > 0)
                        <span class="notif-badge" style="position:absolute; top:-6px; right:-6px; background:#ef4444; color:#fff; font-size:10px; padding:2px 6px; border-radius:999px; line-height:1;">{{ $notifTotalCount }}</span>
                    @endif
                </div>
                <div id="notifDropdown" class="notif-dropdown" style="display:none; position:absolute; right:0; top:48px; width:360px; max-height:70vh; overflow:auto; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.12); z-index:10000;">
                    <div style="padding:12px 14px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between;">
                        <strong>Notifikasi</strong>
                        <small style="color:#666;">Booking: {{ $pendingBookingCount }} · Pembayaran: {{ $pendingPaymentsCount }}</small>
                    </div>
                    <div style="padding:10px 14px;">
                        <div style="font-weight:600; margin-bottom:6px;">Booking Masuk</div>
                        @forelse($notifBookings as $b)
                            <a href="{{ route('booking') }}" style="display:block; padding:8px 10px; border-radius:8px; text-decoration:none; color:#111; background:#f9fafb; margin-bottom:6px;">
                                <div style="font-size:0.95rem; font-weight:600;">{{ $b->kode_booking ?? 'BK-' . str_pad($b->id_request,3,'0',STR_PAD_LEFT) }}</div>
                                <div style="font-size:0.86rem; color:#555;">{{ $b->layanan ?? 'Layanan' }} · {{ $b->lokasi ?? '-' }}</div>
                                <div style="font-size:0.8rem; color:#777;">{{ $b->created_at?->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div style="font-size:0.9rem; color:#666; padding:6px 0;">Tidak ada booking baru.</div>
                        @endforelse
                    </div>
                    <div style="padding:10px 14px; border-top:1px solid #f1f5f9;">
                        <div style="font-weight:600; margin-bottom:6px;">Pembayaran Masuk</div>
                        @forelse($notifPayments as $p)
                            <a href="{{ route('transaksi') }}" style="display:block; padding:8px 10px; border-radius:8px; text-decoration:none; color:#111; background:#f9fafb; margin-bottom:6px;">
                                <div style="font-size:0.95rem; font-weight:600;">{{ $p->kode_pembayaran }}</div>
                                <div style="font-size:0.86rem; color:#555;">Jumlah: Rp {{ number_format($p->jumlah,0,',','.') }} · {{ $p->metode_pembayaran }}</div>
                                <div style="font-size:0.8rem; color:#777;">{{ $p->created_at?->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div style="font-size:0.9rem; color:#666; padding:6px 0;">Tidak ada pembayaran baru.</div>
                        @endforelse
                    </div>
                    <div style="padding:10px 14px; border-top:1px solid #eee; display:flex; gap:8px; justify-content:flex-end;">
                        <a href="{{ route('booking') }}" style="font-size:0.9rem; color:#2563eb; text-decoration:none;">Lihat Booking</a>
                        <a href="{{ route('transaksi') }}" style="font-size:0.9rem; color:#2563eb; text-decoration:none;">Lihat Pembayaran</a>
                    </div>
                </div>
                <div class="profile" onclick="openAdminProfileModal()" style="cursor:pointer;">
                    <i class="fa-solid fa-user"></i>
                    <span>{{ $admin->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Admin Profile Modal -->
        <div id="adminProfileModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; padding:24px 20px; border-radius:14px; width:100%; max-width:420px; box-shadow:0 10px 30px rgba(0,0,0,0.15); position:relative;">
                <span onclick="closeAdminProfileModal()" style="position:absolute; top:10px; right:14px; font-size:1.4rem; cursor:pointer;">&times;</span>
                <h2 style="margin:0 0 6px;">Profil Admin</h2>
                <p style="margin:0 0 16px; color:#666;">Informasi akun admin yang sedang login</p>
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <i class="fa-solid fa-user-circle" style="font-size:48px; color:#555;"></i>
                    <div>
                        <div style="font-weight:700; font-size:1.05rem;">{{ $admin->name ?? '-' }}</div>
                        <div style="color:#555;">{{ $admin->email ?? '-' }}</div>
                        <div style="margin-top:4px; font-size:.9rem; color:#1856a7; font-weight:600;">Role: Admin</div>
                        @if(isset($admin) && isset($admin->created_at))
                            <div style="margin-top:4px; font-size:.9rem; color:#666;">Member sejak: {{ $admin->created_at->format('M Y') }}</div>
                        @endif
                    </div>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:10px;">
                    <button onclick="closeAdminProfileModal()" style="padding:8px 14px; border:1px solid #ddd; background:#fff; border-radius:8px; cursor:pointer;">Tutup</button>
                    <button onclick="openLogoutModal()" style="padding:8px 14px; border:none; background:#ef4444; color:#fff; border-radius:8px; cursor:pointer;">Keluar</button>
                </div>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="welcome">
            <h1>HOME</h1>
            <p>Selamat datang kembali!, berikut ringkasan hari ini</p>
        </div>

        <!-- Stats -->
        <div class="stats">
            <div class="card blue">
              <div>
                <p class="label">Booking</p>
                <h2>{{ $totalBooking ?? 0 }}</h2>
                <span class="growth green">&nbsp;</span>
              </div>
              <div class="icon">
                <i class="fa-solid fa-calendar-days"></i>
              </div>
            </div>

            <div class="card purple">
              <div>
                <p class="label">Tukang Active</p>
                <h2>{{ $activeWorkers ?? 0 }}</h2>
                <span class="growth green">&nbsp;</span>
              </div>
              <div class="icon">
                <i class="fa-solid fa-users"></i>
              </div>
            </div>
        </div>

        @php($services = isset($services) ? $services : collect())

<!-- Layanan -->
<div class="d-flex-between" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:2rem;margin-bottom:1rem;">
    <h3 class="section-title" style="font-size:1.5rem;font-weight:600;">Layanan Tersedia</h3>
    <button class="btn-add" onclick="openCreatePopup()" style="background:#2563eb;color:#fff;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:8px;">
        <i class="fa-solid fa-plus"></i> Tambah Layanan
    </button>
</div>


<div class="services-grid">
    @forelse($services->chunk(2) as $row)
        <div class="services-row">
            @foreach($row as $service)
                <div class="service-card" data-id="{{ $service->id }}">
                    <div class="service-card-row">
                        <div class="service-icon">
                            @if(!empty($service->gambar))
                                <img src="{{ asset('uploads/services/'.$service->gambar) }}" alt="{{ $service->nama }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
                            @elseif(strtolower($service->nama) === 'listrik')
                                <img src="{{ asset('images/listrik.png') }}" alt="icon listrik" style="width:48px;height:48px;object-fit:contain;">
                            @else
                                <i class="fa-solid fa-gear" style="font-size:44px;"></i>
                            @endif
                        </div>
                        <div class="service-info">
                            <h4 style="margin:0;font-size:1.1rem;font-weight:600;">{{ $service->nama }}</h4>
                            <p style="margin:4px 0;font-size:.9rem;color:#555;">{{ $service->deskripsi }}</p>
                            <span style="display:block;font-size:.95rem;color:#1856a7;font-weight:500;">
                                Rp {{ number_format($service->harga_min,0,',','.') }} - Rp {{ number_format($service->harga_max,0,',','.') }}
                            </span>
                            <span class="service-status" style="font-size:.8rem;padding:4px 8px;border-radius:6px;
                                background:{{ $service->status == 'aktif' ? '#dcfce7' : '#fee2e2' }};
                                color:{{ $service->status == 'aktif' ? '#166534' : '#991b1b' }};">
                                {{ ucfirst($service->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="service-actions" style="display:flex;justify-content:flex-end;gap:12px;margin-top:8px;">
                        </button>
                        <button class="edit-btn" data-id="{{ $service->id }}" title="Edit" style="background:none;border:none;cursor:pointer;color:#9333ea;">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="delete-btn" data-id="{{ $service->id }}" title="Hapus" style="background:none;border:none;cursor:pointer;color:#dc2626;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
            @if($row->count() < 2)
                <div class="service-card service-card-empty"></div>
            @endif
        </div>
    @empty
        <p style="padding:12px;border:1px dashed #ddd;border-radius:8px;text-align:center;">Belum ada layanan. Klik <b>Tambah Layanan</b> untuk membuat.</p>
    @endforelse
</div>

    <!-- Popup Create -->
    <div class="popup-overlay" id="createPopup" style="display:none;">
      <div class="popup-content">
          <h2>+Tambah Layanan</h2>
          @if ($errors->any())
              <div class="alert alert-danger" style="background:#fee2e2;color:#991b1b;padding:10px 16px;border-radius:8px;margin-bottom:12px;">
                  <ul style="margin:0;padding-left:18px;">
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
          @if (session('success'))
              <div class="alert alert-success" style="background:#dcfce7;color:#166534;padding:10px 16px;border-radius:8px;margin-bottom:12px;">
                  {{ session('success') }}
              </div>
          @endif
          <form id="createForm" action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <label>Nama Layanan</label>
              <input type="text" name="nama" required>
              <label>Deskripsi</label>
              <textarea name="deskripsi"></textarea>
              <label>Gambar Layanan</label>
              <input type="file" name="gambar" accept="image/*">
              <label>Harga Minimum</label>
              <input type="number" name="harga_min" required>
              <label>Harga Maksimum</label>
              <input type="number" name="harga_max" required>
              <label>Status Aktif</label>
              <select name="status">
                  <option value="aktif">Aktif</option>
                  <option value="nonaktif">Nonaktif</option>
              </select>
              <div class="popup-actions">
                  <button type="button" onclick="closeCreatePopup()">Batal</button>
                  <button type="submit">Tambah</button>
              </div>
          </form>
      </div>
    </div>

    <!-- Popup Edit -->
    <div class="popup-overlay" id="popup" style="display:none;">
      <div class="popup-content">
          <h2>✏️ Edit Layanan</h2>
          <form id="editForm" enctype="multipart/form-data">
              <input type="hidden" id="editId" name="id">
              <input type="hidden" name="_method" value="PUT">
              <label>Nama Layanan</label>
              <input type="text" id="namaLayanan" name="nama">
              <label>Deskripsi</label>
              <textarea id="deskripsi" name="deskripsi"></textarea>
              <label>Harga</label>
              <div style="display:flex;gap:10px;">
                  <input type="number" id="editHargaMin" name="harga_min" placeholder="Min (contoh: 100000)" min="0" required style="flex:1;">
                  <span style="align-self:center;"><h2>-</h2></span>
                  <input type="number" id="editHargaMax" name="harga_max" placeholder="Max (contoh: 200000)" min="0" required style="flex:1;">
              </div>
              <label>Status Aktif</label>
              <select id="status" name="status">
                  <option value="aktif">Aktif</option>
                  <option value="nonaktif">Tidak Aktif</option>
              </select>
              <label>Ganti Gambar (opsional)</label>
              <input type="file" id="editGambar" name="gambar" accept="image/*">
              <div class="popup-actions">
                  <button type="button" class="btn-cancel" onclick="closeEditPopup()">Batal</button>
                  <button type="submit" class="btn-save">Simpan</button>
              </div>
          </form>
      </div>
    </div>

    <script>
    // === util ===
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    // === CREATE ===
    function openCreatePopup(){ document.getElementById("createPopup").style.display="flex"; }
    function closeCreatePopup(){ document.getElementById("createPopup").style.display="none"; }

    // === EDIT ===
    function closeEditPopup(){ document.getElementById("popup").style.display="none"; }

    document.querySelectorAll(".edit-btn").forEach(btn=>{
        btn.addEventListener("click", ()=>{
            const card = btn.closest(".service-card");
            document.getElementById("editId").value = btn.dataset.id;
            document.getElementById("namaLayanan").value = card.querySelector("h4").innerText.trim();
            document.getElementById("deskripsi").value = card.querySelector("p").innerText.trim();
            // Ambil harga min dan max dari span harga
            const hargaText = card.querySelector("span").innerText.trim();
            const hargaArr = hargaText.replace(/[^\d\-]/g, '').split('-');
            document.getElementById("editHargaMin").value = hargaArr[0] ? hargaArr[0].trim() : '';
            document.getElementById("editHargaMax").value = hargaArr[1] ? hargaArr[1].trim() : '';
            const statusText = card.querySelector(".service-status").innerText.trim().toLowerCase();
            document.getElementById("status").value = statusText === "aktif" ? "aktif" : "nonaktif";
            // Kosongkan input file gambar
            document.getElementById("editGambar").value = "";
            document.getElementById("popup").style.display="flex";
        });
    });

    document.getElementById("editForm").addEventListener("submit", e=>{
        e.preventDefault();
        const id = document.getElementById("editId").value;
        const formData = new FormData(document.getElementById("editForm"));
        // Tambahkan file gambar jika ada
        const gambar = document.getElementById("editGambar").files[0];
        if(gambar) formData.set('gambar', gambar);
        fetch(`/services/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
                "Accept": "application/json"
            },
            body: formData
        }).then(r=>r.json()).then(d=>{
            if(d.success){ location.reload(); }
        });
    });

    // === DELETE ===
    document.querySelectorAll(".delete-btn").forEach(btn=>{
        btn.addEventListener("click", ()=>{
            if(confirm("Yakin hapus layanan ini?")){
                fetch(`/services/${btn.dataset.id}`, {
                    method:"DELETE",
                    headers:{ "X-CSRF-TOKEN":token, "Accept":"application/json" }
                }).then(r=>r.json()).then(d=>{
                    if(d.success){ location.reload(); }
                });
            }
        });
    });
    </script>

    <script>
    function openAdminProfileModal(){
        var el = document.getElementById('adminProfileModal');
        if(el){ el.style.display = 'flex'; }
    }
    function closeAdminProfileModal(){
        var el = document.getElementById('adminProfileModal');
        if(el){ el.style.display = 'none'; }
    }
    </script>
    <script>
    // Pencarian layanan (client-side)
    (function(){
        const input = document.getElementById('searchServices');
        const cards = Array.from(document.querySelectorAll('.service-card'));
        if(!input || cards.length===0) return;
        input.addEventListener('input', function(){
            const q = this.value.trim().toLowerCase();
            cards.forEach(card=>{
                const name = card.querySelector('h4')?.innerText.trim().toLowerCase() || '';
                card.style.display = name.includes(q) ? '' : 'none';
            });
        });
    })();
    </script>
    <script>
    // Toggle dropdown notifikasi
    (function(){
        const bell = document.getElementById('notifBell');
        const dd = document.getElementById('notifDropdown');
        if(!bell || !dd) return;
        function closeDD(){
            dd.style.display = 'none';
            document.removeEventListener('click', onOutsideClick);
            document.removeEventListener('keydown', onEsc);
        }
        function onOutsideClick(e){
            if(!dd.contains(e.target) && !bell.contains(e.target)){
                closeDD();
            }
        }
        function onEsc(e){ if(e.key === 'Escape'){ closeDD(); } }
        bell.addEventListener('click', function(e){
            e.stopPropagation();
            const show = dd.style.display !== 'block';
            if(show){
                dd.style.display = 'block';
                setTimeout(()=>{
                    document.addEventListener('click', onOutsideClick);
                    document.addEventListener('keydown', onEsc);
                },0);
            } else {
                closeDD();
            }
        });
    })();
    </script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>
