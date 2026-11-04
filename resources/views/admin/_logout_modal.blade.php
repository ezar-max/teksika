@php
    $currentAdmin = Auth::guard('admin')->user() ?? auth()->user();
    $adminName = $currentAdmin?->name ?? 'Administrator';
    $adminEmail = $currentAdmin?->email ?? 'admin@teksika.com';
@endphp

<!-- Modal Logout Admin (Theme-Matched, Single Unified Confirmation) -->
<div id="logoutModal" class="modal-logout" style="display:none;" onclick="if(event.target===this) closeLogoutModal();">
  <div class="modal-logout-content">
    <span class="modal-logout-close" onclick="closeLogoutModal()">&times;</span>
    <div class="modal-logout-header" style="background: linear-gradient(135deg, #007bff 0%, #005ecf 100%);">
      <i class="fa-solid fa-right-from-bracket icon-logout"></i>
      <h2>Konfirmasi Keluar</h2>
      <p>Yakin ingin keluar dari akun Admin TEKSIKA?</p>
    </div>
    <div class="modal-logout-user">
      <div class="user-info">
        <div style="width:44px; height:44px; border-radius:50%; background:#e0f2fe; color:#0284c7; display:grid; place-items:center; font-size:1.3rem; flex-shrink:0;">
          <i class="fa-solid fa-user-shield"></i>
        </div>
        <div>
          <strong style="color:#0f172a; font-size:0.95rem;">{{ $adminName }}</strong><br>
          <span style="color:#64748b; font-size:0.84rem;">{{ $adminEmail }}</span>
        </div>
        <span class="role" style="background:#e0f2fe; color:#0284c7; padding:4px 10px; border-radius:999px; font-size:0.75rem; font-weight:700; margin-left:auto;">Admin</span>
      </div>
    </div>
    <div class="modal-logout-warning">
      <i class="fa-solid fa-circle-exclamation"></i>
      <p>Anda akan keluar dari sesi administrator dan perlu login kembali untuk mengakses panel kontrol TEKSIKA.</p>
    </div>
    <div class="modal-logout-footer">
      <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
      <button type="button" class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
    </div>
  </div>
</div>

