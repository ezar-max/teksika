@php
    $currentUser = $user ?? auth()->user();
    $avatarUrl = !empty($currentUser?->avatar) ? asset('storage/'.$currentUser->avatar) : asset('images/userabu.png');
    
    if (isset($totalBooking)) {
        $statBooking = $totalBooking;
    } elseif (isset($requests)) {
        $statBooking = $requests->count();
    } else {
        try {
            $statBooking = \App\Models\Request::where('id_user', $currentUser?->id ?? 0)->count();
        } catch (\Throwable $e) {
            $statBooking = 0;
        }
    }

    if (isset($totalTransaksi)) {
        $statTransaksi = $totalTransaksi;
    } elseif (isset($payments)) {
        $statTransaksi = $payments->count();
    } else {
        try {
            $statTransaksi = \App\Models\Pembayaran::where('user_id', $currentUser?->id ?? 0)->count();
        } catch (\Throwable $e) {
            $statTransaksi = 0;
        }
    }

    $memberSince = $currentUser?->created_at ? $currentUser->created_at->format('M Y') : 'Member';
@endphp

<!-- Modal Profil Pengguna -->
<div id="profileModal" class="modal-profile" style="display:none;" onclick="if(event.target===this) closeProfileModal();">
  <div class="modal-profile-content">
    <div class="modal-profile-banner">
      <button type="button" class="modal-profile-close" onclick="closeProfileModal()" aria-label="Tutup">&times;</button>
    </div>

    <div class="modal-profile-body">
      <div class="profile-avatar-wrap">
        <img id="avatarModalPreview" src="{{ $avatarUrl }}" alt="{{ $currentUser->name ?? 'User' }}" class="profile-icon">
        <span class="profile-online-badge" title="Akun Aktif"></span>
      </div>

      <h3 class="profile-name">{{ $currentUser->name ?? 'Pengguna TEKSIKA' }}</h3>
      <div>
        <span class="profile-role-tag">
          <i class="fa-solid fa-circle-check"></i> Customer Terverifikasi
        </span>
      </div>

      <div class="profile-contacts">
        <div class="profile-contact-pill">
          <i class="fa-regular fa-envelope"></i>
          <span>{{ $currentUser->email ?? '-' }}</span>
        </div>
        @if(!empty($currentUser->phone))
        <div class="profile-contact-pill">
          <i class="fa-solid fa-phone"></i>
          <span>{{ $currentUser->phone }}</span>
        </div>
        @endif
      </div>

      <div class="profile-stats">
        <div class="stat">
          <h3>{{ $statBooking }}</h3>
          <p>Booking</p>
        </div>
        <div class="stat">
          <h3>{{ $statTransaksi }}</h3>
          <p>Transaksi</p>
        </div>
        <div class="stat">
          <h3>{{ $memberSince }}</h3>
          <p>Member</p>
        </div>
      </div>

      <div class="member-info">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Pengguna aktif layanan resmi TEKSIKA dengan jaminan garansi & teknisi bersertifikasi.</span>
      </div>

      <div class="profile-actions">
        <button type="button" class="btn-profile-logout" onclick="closeProfileModal(); openLogoutModal();">
          <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
        </button>
        <button type="button" class="btn-profile-edit" onclick="closeProfileModal(); openEditProfileModal();">
          <i class="fa-solid fa-pen-to-square"></i> Edit Profil
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Profil Pengguna -->
<div id="editProfileModal" class="modal-profile" style="display:none;" onclick="if(event.target===this) closeEditProfileModal();">
  <div class="edit-card">
    <button type="button" class="modal-profile-close" onclick="closeEditProfileModal()" style="color:#64748b; background:#f1f5f9; border-color:#e2e8f0;" aria-label="Tutup">&times;</button>
    
    <div class="edit-header">
      <div class="avatar"><i class="fa-solid fa-user-pen"></i></div>
      <div>
        <h3 class="edit-title">Edit Profil Akun</h3>
        <p class="edit-sub">Perbarui informasi kontak dan foto profil Anda</p>
      </div>
    </div>

    <form action="{{ route('user.updateProfile') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="edit-grid">
        <div class="edit-field">
          <label class="edit-label">Foto Profil</label>
          <div class="avatar-upload-box">
            <div class="avatar-preview-wrap">
              <img id="avatarEditPreview" src="{{ $avatarUrl }}" alt="Avatar Preview">
            </div>
            <div class="avatar-upload-cta">
              <input id="avatarFileInput" type="file" name="avatar" accept="image/*">
              <span class="avatar-upload-hint">Pilih JPG, PNG, atau WEBP (maksimal 5MB).</span>
            </div>
          </div>
        </div>

        <div class="edit-field">
          <label class="edit-label" for="editUserName">Nama Lengkap</label>
          <div class="input-wrap">
            <i class="fa-solid fa-user icon"></i>
            <input id="editUserName" type="text" class="edit-input" name="name" value="{{ $currentUser->name ?? '' }}" required placeholder="Nama Anda">
          </div>
        </div>

        <div class="edit-field">
          <label class="edit-label" for="editUserEmail">Email</label>
          <div class="input-wrap">
            <i class="fa-solid fa-envelope icon"></i>
            <input id="editUserEmail" type="email" class="edit-input" name="email" value="{{ $currentUser->email ?? '' }}" required placeholder="email@domain.com">
          </div>
        </div>

        @if(isset($currentUser->phone))
        <div class="edit-field">
          <label class="edit-label" for="editUserPhone">Nomor Telepon / WhatsApp</label>
          <div class="input-wrap">
            <i class="fa-solid fa-phone icon"></i>
            <input id="editUserPhone" type="text" class="edit-input" name="phone" value="{{ $currentUser->phone ?? '' }}" placeholder="08xxxxxxxxxx">
          </div>
        </div>
        @endif
      </div>

      <div class="edit-actions">
        <button type="button" class="btn-outline" onclick="closeEditProfileModal()">Batal</button>
        <button type="submit" class="btn-primary">
          <i class="fa-solid fa-check"></i> Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function setHeaderBlur(on) {
  var h = document.querySelector('.header');
  if (h) h.style.filter = on ? 'blur(3px)' : '';
}

function openProfileModal() {
  var el = document.getElementById('profileModal');
  if (el) el.style.display = 'flex';
  setHeaderBlur(true);
}

function closeProfileModal() {
  var el = document.getElementById('profileModal');
  if (el) el.style.display = 'none';
  setHeaderBlur(false);
}

function openEditProfileModal() {
  var el = document.getElementById('editProfileModal');
  if (el) el.style.display = 'flex';
  setHeaderBlur(true);
}

function closeEditProfileModal() {
  var el = document.getElementById('editProfileModal');
  if (el) el.style.display = 'none';
  setHeaderBlur(false);
}

// Live avatar preview on select
document.addEventListener('DOMContentLoaded', function(){
  var avatarInput = document.getElementById('avatarFileInput');
  var editImg = document.getElementById('avatarEditPreview');
  var modalImg = document.getElementById('avatarModalPreview');
  var headerImg = document.getElementById('avatarHeader');

  if (avatarInput) {
    avatarInput.addEventListener('change', function(){
      var file = this.files && this.files[0];
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function(e){
        if (editImg) editImg.src = e.target.result;
        if (modalImg) modalImg.src = e.target.result;
        if (headerImg) headerImg.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }
});
</script>
