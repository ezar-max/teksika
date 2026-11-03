<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Worker Management Dashboard with Toggle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="{{ asset('css/worker.css') }}">
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
        <div class="worker-content">
            <!-- Title & Subtitle -->
            <div class="worker-header-row">
                <div>
                    <h1 class="worker-title">Worker management</h1>
                    <div class="worker-subtitle">Kelola semua tukang dan performa mereka</div>
                </div>
                <button class="btn-tambah-pekerja" onclick="openAddWorkerPopup()"><i class="fa-solid fa-plus"></i> Tambah Pekerja</button>
            </div>

            <!-- Summary Cards -->
            <div class="worker-summary-cards">
                <div class="worker-summary-card total">
                    <div class="icon-bg green">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div class="summary-label">Total Tukang</div>
                        <div class="summary-value">{{ $workers->count() }}</div>
                    </div>
                </div>
                <div class="worker-summary-card active">
                    <div class="icon-bg purple">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <div class="summary-label">Sedang bekerja</div>
                        <div class="summary-value">{{ ($workers ?? collect())->where('status', 'lagi bekerja')->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Worker List -->
            <div class="worker-list-container">
                <h2 class="worker-list-title">Daftar tukang</h2>
                <div class="worker-list" id="workerList">
                    @foreach($workers ?? [] as $worker)
                    <div class="worker-card">
                        <div class="worker-card-header">
                            <div class="worker-avatar">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div>
                                <div class="worker-name">{{ $worker->name }}</div>
                                <div class="worker-role">{{ $worker->role }}</div>
                                @php($st = strtolower($worker->status))
                                <span class="worker-status-badge" style="display:inline-block;margin-top:6px;padding:4px 8px;border-radius:999px;font-size:.8rem;font-weight:600;
                                  background: {{ $st==='aktif' ? '#dcfce7' : ($st==='lagi bekerja' ? '#fee9c3' : '#fee2e2') }};
                                  color: {{ $st==='aktif' ? '#166534' : ($st==='lagi bekerja' ? '#92400e' : '#991b1b') }};">
                                    {{ ucfirst($worker->status) }}
                                </span>
                                <div class="worker-id">ID: {{ $worker->id }}</div>
                            </div>
                            <div class="worker-card-actions">
                                <i class="fa-solid fa-eye"></i>
                                <!-- Tombol edit pakai data-* -->
                                <i class="fa-solid fa-pen" style="cursor:pointer"
                                   onclick="openEditWorkerPopup(this)"
                                   data-id="{{ $worker->id }}"
                                   data-name="{{ $worker->name }}"
                                   data-role="{{ $worker->role }}"
                                   data-alamat="{{ $worker->alamat }}"
                                   data-phone="{{ $worker->phone }}"
                                   data-skills="{{ $worker->skills }}"
                                   data-pengalaman="{{ $worker->pengalaman }}"
                                   data-status="{{ $worker->status }}"></i>
                                <!-- Tombol delete -->
                                <form action="{{ url('/worker/' . $worker->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $worker->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <i class="fa-solid fa-trash" style="cursor:pointer;color:red"
                                       onclick="confirmDelete({{ $worker->id }})"></i>
                                </form>
                            </div>
                        </div>
                        <hr>
                        <div class="worker-card-body">
                            <div class="worker-info-row">
                                <div>Umur:<br><b>{{ $worker->umur }} tahun</b></div>
                                <div>Pengalaman:<br><b>{{ $worker->pengalaman }} tahun</b></div>
                            </div>
                            <div>Alamat:<br><b>{{ $worker->alamat }}</b></div>
                            <div>No Telepon:<br><b>{{ $worker->phone }}</b></div>
                            <div>Keahlian:</div>
                            <div class="worker-skill-list">
                                @foreach(explode(',', $worker->skills) as $skill)
                                    <span class="worker-skill">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
</worker-content>

<!-- Popup Tambah Tukang -->
<div id="addWorkerPopup" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:32px 24px;border-radius:16px;max-width:420px;width:100%;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
        <h2 style="margin-bottom:18px;"><i class="fa-solid fa-pen"></i> Tambah Tukang</h2>
        <form id="formAddWorker" action="{{ route('worker.store') }}" method="POST">
            @csrf
            <div style="display:flex;gap:12px;">
                <div style="flex:1;">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" required style="width:100%;margin-bottom:10px;">
                </div>
                <div style="flex:1;">
                    <label for="role">Kategori</label>
                    <input type="text" id="role" name="role" required style="width:100%;margin-bottom:10px;">
                </div>
            </div>
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" required style="width:100%;margin-bottom:10px;">
            <label for="phone">No Telepon</label>
            <input type="text" id="phone" name="phone" required style="width:100%;margin-bottom:10px;">
            <label for="skills">Keahlian</label>
            <input type="text" id="skills" name="skills" required style="width:100%;margin-bottom:10px;">
            <label for="pengalaman">Pengalaman</label>
            <input type="number" id="pengalaman" name="pengalaman" required style="width:100%;margin-bottom:10px;">
            <label for="status">status</label>
            <select id="status" name="status" style="width:100%;margin-bottom:18px;">
                <option value="aktif">Aktif</option>
                <option value="lagi bekerja">Lagi bekerja</option>
                <option value="tidak aktif">Tidak Aktif</option>
            </select>
            <div style="display:flex;justify-content:space-between;gap:10px;">
                <button type="button" onclick="closeAddWorkerPopup()" style="padding:8px 18px;border-radius:8px;border:1px solid #ccc;background:#fff;">Batal</button>
                <button type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#222;color:#fff;">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- Popup Edit Tukang -->
<div id="editWorkerPopup" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:32px 24px;border-radius:16px;max-width:420px;width:100%;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
        <h2 style="margin-bottom:18px;"><i class="fa-solid fa-pen"></i> Edit Tukang</h2>
        <form id="formEditWorker" action="" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id">
            <div style="display:flex;gap:12px;">
                <div style="flex:1;">
                    <label for="edit_name">Nama</label>
                    <input type="text" id="edit_name" name="name" required style="width:100%;margin-bottom:10px;">
                </div>
                <div style="flex:1;">
                    <label for="edit_role">Kategori</label>
                    <input type="text" id="edit_role" name="role" required style="width:100%;margin-bottom:10px;">
                </div>
            </div>
            <label for="edit_alamat">Alamat</label>
            <input type="text" id="edit_alamat" name="alamat" required style="width:100%;margin-bottom:10px;">
            <label for="edit_phone">No Telepon</label>
            <input type="text" id="edit_phone" name="phone" required style="width:100%;margin-bottom:10px;">
            <label for="edit_skills">Keahlian</label>
            <input type="text" id="edit_skills" name="skills" required style="width:100%;margin-bottom:10px;">
            <label for="edit_pengalaman">Pengalaman</label>
            <input type="number" id="edit_pengalaman" name="pengalaman" required style="width:100%;margin-bottom:10px;">
            <label for="edit_status">status</label>
            <select id="edit_status" name="status" style="width:100%;margin-bottom:18px;">
                <option value="aktif">Aktif</option>
                <option value="lagi bekerja">Lagi bekerja</option>
                <option value="tidak aktif">Tidak Aktif</option>
            </select>
            <div style="display:flex;justify-content:space-between;gap:10px;">
                <button type="button" onclick="closeEditWorkerPopup()" style="padding:8px 18px;border-radius:8px;border:1px solid #ccc;background:#fff;">Batal</button>
                <button type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#222;color:#fff;">Simpan</button>
            </div>
        </form>
    </div>
</div>

        </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Popup Tambah
    function openAddWorkerPopup(){
        document.getElementById('addWorkerPopup').style.display = 'flex';
    }
    function closeAddWorkerPopup(){
        document.getElementById('addWorkerPopup').style.display = 'none';
    }

    // Popup Edit
    function openEditWorkerPopup(el){
        const id = el.getAttribute("data-id");
        const name = el.getAttribute("data-name");
        const role = el.getAttribute("data-role");
        const alamat = el.getAttribute("data-alamat");
        const phone = el.getAttribute("data-phone");
        const skills = el.getAttribute("data-skills");
        const pengalaman = el.getAttribute("data-pengalaman");
        const status = el.getAttribute("data-status");

        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_role").value = role;
        document.getElementById("edit_alamat").value = alamat;
        document.getElementById("edit_phone").value = phone;
        document.getElementById("edit_skills").value = skills;
        document.getElementById("edit_pengalaman").value = pengalaman;
        document.getElementById("edit_status").value = status;

        // update action url form
        document.getElementById("formEditWorker").action = "/worker/" + id;

        document.getElementById('editWorkerPopup').style.display = 'flex';
    }
    function closeEditWorkerPopup(){
        document.getElementById('editWorkerPopup').style.display = 'none';
    }

    // Konfirmasi Delete
    function confirmDelete(id){
        if(confirm("Apakah kamu yakin ingin menghapus pekerja ini?")){
            document.getElementById('deleteForm' + id).submit();
        }
    }
    </script>
</body>
</html>
