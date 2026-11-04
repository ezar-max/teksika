<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Mitra Tukang — TEKSIKA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS & Icons -->
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/worker.css') }}">
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
        <a href="{{ route('worker') }}" class="menu-item active">
            <img src="{{ asset('images/worker.png') }}" alt="logo" class="icon">
            <span class="menu-text">Worker</span>
        </a>
        <a href="{{ route('admin.pricing.index') }}" class="menu-item">
            <i class="fa-solid fa-sliders"></i>
            <span class="menu-text">Aturan Tarif</span>
        </a>
        <a href="{{ route('chat') }}" class="menu-item">
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
        <div class="worker-content">
            <!-- Title & Subtitle -->
            <div class="worker-header-row">
                <div>
                    <h1 class="worker-title"><i class="fa-solid fa-users-gear" style="color:#007bff; margin-right:8px;"></i> Manajemen Mitra Teknisi</h1>
                    <div class="worker-subtitle">Kelola profil tukang, ketersediaan jadwal, tarif dasar, dan performa ulasan</div>
                </div>
                <button type="button" class="btn-tambah-pekerja" onclick="openAddWorkerPopup()">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Tambah Mitra Teknisi</span>
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="worker-summary-cards">
                <div class="worker-summary-card">
                    <div class="icon-bg green">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div class="summary-label">Total Mitra Terdaftar</div>
                        <div class="summary-value">{{ $workers->count() }}</div>
                    </div>
                </div>
                <div class="worker-summary-card">
                    <div class="icon-bg purple">
                        <i class="fa-solid fa-business-time"></i>
                    </div>
                    <div>
                        <div class="summary-label">Sedang Menjalankan Pekerjaan</div>
                        <div class="summary-value">{{ ($workers ?? collect())->where('display_availability', 'Sedang Bekerja')->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Worker List -->
            <div class="worker-list-container">
                <h2 class="worker-list-title">Daftar Mitra Teknisi</h2>
                <div class="worker-list" id="workerList">
                    @forelse($workers ?? [] as $worker)
                    @php
                        $st = strtolower($worker->display_availability ?? '');
                        $statusBg = $st === 'siap bekerja' ? '#dcfce7' : ($st === 'sedang bekerja' ? '#fef3c7' : '#fee2e2');
                        $statusColor = $st === 'siap bekerja' ? '#166534' : ($st === 'sedang bekerja' ? '#92400e' : '#991b1b');
                    @endphp
                    <div class="worker-card">
                        <div>
                            <div class="worker-card-header">
                                <div class="worker-avatar">
                                    @if($worker->foto)
                                        <img src="{{ asset('storage/' . $worker->foto) }}" alt="Foto {{ $worker->name }}" onerror="this.onerror=null;this.src='{{ asset('images/userabu.png') }}';">
                                    @else
                                        <div style="width:100%; height:100%; display:grid; place-items:center; background:#e2e8f0; color:#64748b; font-size:1.4rem;">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div style="min-width:0; flex:1;">
                                    <div class="worker-name">{{ $worker->name }}</div>
                                    <div class="worker-role">{{ $worker->role }} &bull; ID #{{ $worker->id }}</div>
                                    <span style="display:inline-block; margin-top:4px; padding:3px 8px; border-radius:999px; font-size:.74rem; font-weight:700; background:{{ $statusBg }}; color:{{ $statusColor }};">
                                        {{ $worker->display_availability }}
                                    </span>
                                </div>
                                <div class="worker-card-actions">
                                    <i class="fa-solid fa-pen" title="Edit Data Mitra"
                                       onclick="openEditWorkerPopup(this)"
                                       data-id="{{ $worker->id }}"
                                       data-name="{{ $worker->name }}"
                                       data-role="{{ $worker->role }}"
                                       data-phone="{{ $worker->phone }}"
                                       data-skills="{{ json_encode(array_map('trim', explode(',', $worker->skills ?? ''))) }}"
                                       data-tarif="{{ $worker->tarif }}"
                                       data-pengalaman="{{ $worker->pengalaman }}"
                                       data-rating="{{ $worker->rating ?? 5.0 }}"
                                       data-status="{{ $worker->status }}"
                                       data-status-ketersediaan="{{ $worker->status_ketersediaan ?? 'siap' }}"
                                       data-foto="{{ $worker->foto ? asset('storage/' . $worker->foto) : '' }}"></i>
                                    <form action="{{ url('/worker/' . $worker->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $worker->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <i class="fa-solid fa-trash" title="Hapus Mitra"
                                           onclick="confirmDelete({{ $worker->id }})"></i>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="worker-card-body">
                                <div class="worker-info-row">
                                    <div>Umur:<br><b>{{ $worker->umur ?? '-' }} thn</b></div>
                                    <div>Pengalaman:<br><b>{{ $worker->pengalaman }} thn</b></div>
                                    <div>Rating:<br><b><i class="fa-solid fa-star" style="color:#eab308;"></i> {{ number_format($worker->rating ?? 5.0, 1) }}</b></div>
                                </div>
                                <div style="margin-bottom:4px;">
                                    <i class="fa-solid fa-location-dot" style="color:#ef4444; margin-right:4px;"></i>
                                    <strong>Pangkalan:</strong> Kantor Pusat TEKSIKA - SMKN 1 Gunung Putri
                                </div>
                                <div style="margin-bottom:4px;">
                                    <i class="fa-solid fa-phone" style="color:#007bff; margin-right:4px;"></i>
                                    <strong>Kontak:</strong> {{ $worker->phone }}
                                </div>
                                <div style="margin-bottom:8px;">
                                    <i class="fa-solid fa-tag" style="color:#10b981; margin-right:4px;"></i>
                                    <strong>Tarif Dasar:</strong> {{ $worker->tarif !== null ? 'Rp ' . number_format($worker->tarif, 0, ',', '.') : 'Sesuai Layanan' }}
                                </div>
                                <div style="font-weight:700; font-size:0.8rem; color:#64748b; margin-top:8px;">Keahlian:</div>
                                <div class="worker-skill-list">
                                    @foreach(array_filter(array_map('trim', explode(',', $worker->skills ?? ''))) as $skill)
                                        <span class="worker-skill">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div style="grid-column: 1 / -1; padding:48px 24px; background:#fff; border:1.5px dashed #cbd5e1; border-radius:18px; text-align:center; color:#64748b;">
                            <i class="fa-solid fa-users-slash" style="font-size:3rem; color:#94a3b8; margin-bottom:12px;"></i>
                            <p style="font-weight:700; font-size:1.1rem; color:#0f172a; margin:0 0 6px 0;">Belum Ada Mitra Teknisi Terdaftar</p>
                            <p style="font-size:0.88rem; margin:0 0 16px 0;">Daftarkan mitra teknisi baru untuk melayani permintaan booking.</p>
                            <button type="button" class="btn-tambah-pekerja" onclick="openAddWorkerPopup()">
                                <i class="fa-solid fa-plus"></i> Tambah Mitra Pertama
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Tambah Tukang (Spacious & Modern) -->
    <div id="addWorkerPopup" class="popup-overlay" style="display:none;" onclick="if(event.target===this) closeAddWorkerPopup();">
        <div class="popup-content popup-large">
            <!-- Modern Header -->
            <div class="popup-header-modern">
                <div class="popup-header-info">
                    <div class="popup-icon-bubble">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 class="popup-header-title">Tambah Mitra Teknisi Baru</h3>
                        <p class="popup-header-subtitle">Daftarkan tenaga ahli teknisi dengan spesialisasi, tarif dasar, dan pengalaman kerja.</p>
                    </div>
                </div>
                <button type="button" class="popup-close-btn" onclick="closeAddWorkerPopup()" title="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formAddWorker" action="{{ route('worker.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-grid-2col">
                    <!-- Kolom Kiri: Identitas & Kontak -->
                    <div>
                        <div style="margin-bottom:14px;">
                            <label for="name"><i class="fa-solid fa-user" style="color:#007bff; margin-right:4px;"></i> Nama Lengkap</label>
                            <input type="text" id="name" name="name" placeholder="Contoh: Budi Santoso" required>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="role"><i class="fa-solid fa-briefcase" style="color:#007bff; margin-right:4px;"></i> Kategori / Spesialisasi</label>
                            <input type="text" id="role" name="role" placeholder="Contoh: Teknisi Listrik & Bangunan" required>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="phone"><i class="fa-solid fa-phone" style="color:#10b981; margin-right:4px;"></i> Nomor Telepon / WhatsApp</label>
                            <input type="text" id="phone" name="phone" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="tarif"><i class="fa-solid fa-money-bill-wave" style="color:#10b981; margin-right:4px;"></i> Tarif Jasa Dasar (Rp)</label>
                            <input type="number" id="tarif" name="tarif" placeholder="Contoh: 150000" min="0" step="1000" required>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label for="pengalaman"><i class="fa-solid fa-award" style="color:#f59e0b; margin-right:4px;"></i> Pengalaman</label>
                                <input type="number" id="pengalaman" name="pengalaman" placeholder="Tahun (cth: 3)" min="0" required>
                            </div>
                            <div>
                                <label for="rating"><i class="fa-solid fa-star" style="color:#f59e0b; margin-right:4px;"></i> Rating Awal</label>
                                <input type="number" step="0.1" id="rating" name="rating" min="0" max="5" value="5.0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Foto Profil & Keahlian & Status -->
                    <div>
                        <!-- Foto Profil Mitra dengan Live Preview Card -->
                        <div style="margin-bottom:14px;">
                            <label for="foto"><i class="fa-solid fa-camera" style="color:#007bff; margin-right:4px;"></i> Foto Profil Mitra (Opsional)</label>
                            <div class="file-upload-card">
                                <div class="file-preview-avatar" id="addWorkerPreviewWrap">
                                    <img id="addWorkerPreviewImg" src="" alt="Preview" style="display:none;">
                                    <i id="addWorkerDefaultIcon" class="fa-solid fa-user"></i>
                                </div>
                                <div class="file-upload-details">
                                    <input type="file" id="foto" name="foto" accept="image/*" onchange="previewWorkerPhoto(this, 'addWorkerPreviewImg', 'addWorkerDefaultIcon')">
                                    <div style="font-size:0.76rem; color:#64748b; margin-top:5px;">
                                        Format JPG, PNG, WEBP (Maksimal 2MB).
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="skills"><i class="fa-solid fa-wrench" style="color:#007bff; margin-right:4px;"></i> Keahlian (Pilih satu atau lebih)</label>
                            <select id="skills" name="skills[]" multiple required style="min-height:95px;">
                                @foreach($services as $service)
                                    <option value="{{ $service->nama }}">{{ $service->nama }}{{ $service->status !== 'aktif' ? ' (Nonaktif)' : '' }}</option>
                                @endforeach
                            </select>
                            <small style="font-size:0.75rem; color:#64748b; display:block; margin-top:3px;">Gunakan Ctrl (Windows) / Cmd (Mac) untuk memilih multi-keahlian.</small>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label for="status"><i class="fa-solid fa-toggle-on" style="color:#007bff; margin-right:4px;"></i> Status Akun</label>
                                <select id="status" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak aktif">Tidak Aktif</option>
                                </select>
                            </div>
                            <div>
                                <label for="status_ketersediaan"><i class="fa-solid fa-clock" style="color:#007bff; margin-right:4px;"></i> Ketersediaan</label>
                                <select id="status_ketersediaan" name="status_ketersediaan">
                                    <option value="siap">Siap Bekerja</option>
                                    <option value="tidak_tersedia">Tidak Tersedia</option>
                                    <option value="libur">Libur</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="popup-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddWorkerPopup()">Batal</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-plus"></i> Tambah Mitra Teknisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup Edit Tukang (Spacious & Modern) -->
    <div id="editWorkerPopup" class="popup-overlay" style="display:none;" onclick="if(event.target===this) closeEditWorkerPopup();">
        <div class="popup-content popup-large">
            <!-- Modern Header -->
            <div class="popup-header-modern">
                <div class="popup-header-info">
                    <div class="popup-icon-bubble" style="background:#faf5ff; color:#9333ea; border-color:#e9d5ff;">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="popup-header-title">Edit Data Mitra Teknisi</h3>
                        <p class="popup-header-subtitle">Perbarui data spesialisasi, tarif dasar, keahlian, dan ketersediaan mitra.</p>
                    </div>
                </div>
                <button type="button" class="popup-close-btn" onclick="closeEditWorkerPopup()" title="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formEditWorker" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                
                <div class="form-grid-2col">
                    <!-- Kolom Kiri -->
                    <div>
                        <div style="margin-bottom:14px;">
                            <label for="edit_name"><i class="fa-solid fa-user" style="color:#007bff; margin-right:4px;"></i> Nama Lengkap</label>
                            <input type="text" id="edit_name" name="name" required>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="edit_role"><i class="fa-solid fa-briefcase" style="color:#007bff; margin-right:4px;"></i> Kategori / Spesialisasi</label>
                            <input type="text" id="edit_role" name="role" required>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="edit_phone"><i class="fa-solid fa-phone" style="color:#10b981; margin-right:4px;"></i> No Telepon / WhatsApp</label>
                            <input type="text" id="edit_phone" name="phone" required>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="edit_tarif"><i class="fa-solid fa-money-bill-wave" style="color:#10b981; margin-right:4px;"></i> Tarif Jasa Dasar (Rp)</label>
                            <input type="number" id="edit_tarif" name="tarif" required min="0" step="1000">
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label for="edit_pengalaman"><i class="fa-solid fa-award" style="color:#f59e0b; margin-right:4px;"></i> Pengalaman (Thn)</label>
                                <input type="number" id="edit_pengalaman" name="pengalaman" required min="0">
                            </div>
                            <div>
                                <label for="edit_rating"><i class="fa-solid fa-star" style="color:#f59e0b; margin-right:4px;"></i> Rating</label>
                                <input type="number" step="0.1" id="edit_rating" name="rating" min="0" max="5">
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div>
                        <!-- Foto Profil dengan Live Preview Card -->
                        <div style="margin-bottom:14px;">
                            <label for="edit_foto"><i class="fa-solid fa-camera" style="color:#007bff; margin-right:4px;"></i> Ganti Foto (Kosongkan jika tetap)</label>
                            <div class="file-upload-card">
                                <div class="file-preview-avatar" id="editWorkerPreviewWrap">
                                    <img id="editWorkerPreviewImg" src="" alt="Foto Mitra" style="display:none;">
                                    <i id="editWorkerDefaultIcon" class="fa-solid fa-user"></i>
                                </div>
                                <div class="file-upload-details">
                                    <input type="file" id="edit_foto" name="foto" accept="image/*" onchange="previewWorkerPhoto(this, 'editWorkerPreviewImg', 'editWorkerDefaultIcon')">
                                    <div style="font-size:0.76rem; color:#64748b; margin-top:5px;">
                                        Pilih foto baru jika ingin mengganti foto saat ini.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom:14px;">
                            <label for="edit_skills"><i class="fa-solid fa-wrench" style="color:#007bff; margin-right:4px;"></i> Keahlian (Pilih satu atau lebih)</label>
                            <select id="edit_skills" name="skills[]" multiple required style="min-height:95px;">
                                @foreach($services as $service)
                                    <option value="{{ $service->nama }}">{{ $service->nama }}{{ $service->status !== 'aktif' ? ' (Nonaktif)' : '' }}</option>
                                @endforeach
                            </select>
                            <small style="font-size:0.75rem; color:#64748b; display:block; margin-top:3px;">Gunakan Ctrl (Windows) / Cmd (Mac) untuk memilih multi-keahlian.</small>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label for="edit_status"><i class="fa-solid fa-toggle-on" style="color:#007bff; margin-right:4px;"></i> Status Akun</label>
                                <select id="edit_status" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak aktif">Tidak Aktif</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_status_ketersediaan"><i class="fa-solid fa-clock" style="color:#007bff; margin-right:4px;"></i> Ketersediaan</label>
                                <select id="edit_status_ketersediaan" name="status_ketersediaan">
                                    <option value="siap">Siap Bekerja</option>
                                    <option value="tidak_tersedia">Tidak Tersedia</option>
                                    <option value="libur">Libur</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="popup-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditWorkerPopup()">Batal</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Profile Modal -->
    @include('admin._profile_modal')

    <!-- Modal Logout Tunggal Admin -->
    @include('admin._logout_modal')

    <!-- Scripts -->
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

    // Live Preview Foto Upload
    function previewWorkerPhoto(input, imgId, iconId){
        if(input.files && input.files[0]){
            const reader = new FileReader();
            reader.onload = function(e){
                const img = document.getElementById(imgId);
                const icon = document.getElementById(iconId);
                if(img){
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                if(icon){
                    icon.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Popup Tambah
    function openAddWorkerPopup(){
        document.getElementById('formAddWorker').reset();
        const addImg = document.getElementById('addWorkerPreviewImg');
        const addIcon = document.getElementById('addWorkerDefaultIcon');
        if(addImg){ addImg.src = ''; addImg.style.display = 'none'; }
        if(addIcon){ addIcon.style.display = 'block'; }
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
        const phone = el.getAttribute("data-phone");
        const skills = JSON.parse(el.getAttribute("data-skills") || "[]");
        const tarif = el.getAttribute("data-tarif");
        const pengalaman = el.getAttribute("data-pengalaman");
        const rating = el.getAttribute("data-rating") || "5.0";
        const status = el.getAttribute("data-status");
        const statusKetersediaan = el.getAttribute("data-status-ketersediaan") || "siap";
        const foto = el.getAttribute("data-foto");

        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_role").value = role;
        document.getElementById("edit_phone").value = phone;
        Array.from(document.getElementById("edit_skills").options).forEach(option => {
            option.selected = skills.includes(option.value);
        });
        document.getElementById("edit_tarif").value = tarif;
        document.getElementById("edit_pengalaman").value = pengalaman;
        document.getElementById("edit_rating").value = rating;
        document.getElementById("edit_status").value = status;
        document.getElementById("edit_status_ketersediaan").value = statusKetersediaan;

        // Preview foto saat ini
        const editImg = document.getElementById("editWorkerPreviewImg");
        const editIcon = document.getElementById("editWorkerDefaultIcon");
        document.getElementById("edit_foto").value = "";
        if(foto && foto.trim() !== ""){
            editImg.src = foto;
            editImg.style.display = 'block';
            editIcon.style.display = 'none';
        } else {
            editImg.src = "";
            editImg.style.display = 'none';
            editIcon.style.display = 'block';
        }

        document.getElementById("formEditWorker").action = "{{ url('admin/worker') }}/" + id;
        document.getElementById('editWorkerPopup').style.display = 'flex';
    }
    function closeEditWorkerPopup(){
        document.getElementById('editWorkerPopup').style.display = 'none';
    }

    // Konfirmasi Delete
    function confirmDelete(id){
        if(confirm("Apakah Anda yakin ingin menghapus data teknisi ini?")){
            document.getElementById('deleteForm' + id).submit();
        }
    }
    </script>
</body>
</html>
