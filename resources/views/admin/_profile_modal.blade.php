@php
    $currentAdmin = Auth::guard('admin')->user() ?? auth()->user();
    $adminName = $currentAdmin?->name ?? 'Administrator';
    $adminEmail = $currentAdmin?->email ?? 'admin@teksika.com';
    
    try {
        $totalLayananStat = \App\Models\Service::count();
    } catch (\Throwable $e) {
        $totalLayananStat = 0;
    }
    
    try {
        $totalBookingStat = \App\Models\Request::count();
    } catch (\Throwable $e) {
        $totalBookingStat = 0;
    }
    
    try {
        $totalWorkerStat = \App\Models\Worker::count();
    } catch (\Throwable $e) {
        $totalWorkerStat = 0;
    }
@endphp

<!-- Modal Profil Admin (Theme-Matched with Plus Jakarta Sans) -->
<div id="adminProfileModal" class="modal-profile" style="display:none;" onclick="if(event.target===this) closeAdminProfileModal();">
  <div class="modal-profile-content">
    <div class="modal-profile-banner" style="background: linear-gradient(135deg, #007bff 0%, #005ecf 100%);">
      <button type="button" class="modal-profile-close" onclick="closeAdminProfileModal()" aria-label="Tutup">&times;</button>
    </div>

    <div class="modal-profile-body">
      <div class="profile-avatar-wrap">
        <div style="width:96px; height:96px; border-radius:50%; background:#e0f2fe; color:#0284c7; display:grid; place-items:center; font-size:2.4rem; border:4px solid #fff; box-shadow:0 4px 14px rgba(0,0,0,0.12);">
          <i class="fa-solid fa-user-shield"></i>
        </div>
        <span class="profile-online-badge" title="Sesi Admin Aktif" style="background:#10b981;"></span>
      </div>

      <h3 class="profile-name">{{ $adminName }}</h3>
      <div>
        <span class="profile-role-tag" style="background:rgba(0,123,255,0.1); color:#007bff; border:1px solid rgba(0,123,255,0.2);">
          <i class="fa-solid fa-circle-check"></i> Administrator Sistem
        </span>
      </div>

      <div class="profile-contacts">
        <div class="profile-contact-pill">
          <i class="fa-regular fa-envelope"></i>
          <span>{{ $adminEmail }}</span>
        </div>
        <div class="profile-contact-pill">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Hak Akses Penuh</span>
        </div>
      </div>

      <div class="profile-stats">
        <div class="stat">
          <h3>{{ $totalLayananStat }}</h3>
          <p>Layanan</p>
        </div>
        <div class="stat">
          <h3>{{ $totalBookingStat }}</h3>
          <p>Booking</p>
        </div>
        <div class="stat">
          <h3>{{ $totalWorkerStat }}</h3>
          <p>Mitra Tukang</p>
        </div>
      </div>

      <div class="member-info">
        <i class="fa-solid fa-shield-check"></i>
        <span>Panel administrasi resmi TEKSIKA. Akses verifikasi pesanan, penawaran harga, manajemen mitra, dan sistem tarif.</span>
      </div>

      <!-- Action: Hanya tombol Tutup (Logout tunggal hanya di Sidebar) -->
      <div class="profile-actions" style="margin-top:20px;">
        <button type="button" class="btn-profile-edit" onclick="closeAdminProfileModal();" style="width:100%; justify-content:center;">
          <i class="fa-solid fa-check"></i> Tutup Informasi
        </button>
      </div>
    </div>
  </div>
</div>

