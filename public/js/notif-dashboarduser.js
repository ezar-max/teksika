(function(){
  "use strict";

  const bell = document.getElementById('notifBell');
  const dropdown = document.getElementById('notifDropdown');
  const list = document.getElementById('notifList');
  const closeBtn = document.getElementById('notifCloseX');

  if (!bell || !dropdown || !list) return;

  function fmtTanggal(iso){
    try {
      return new Date(iso).toLocaleString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
      });
    } catch(e) { return ''; }
  }

  function fmtRupiah(num){
    try {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
      }).format(Number(num || 0));
    } catch(e) { return 'Rp0'; }
  }

  function renderNotifications() {
    const requests = window.notifRequests || [];
    if (!requests || requests.length === 0) {
      list.innerHTML = '<div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 0.88rem;">Tidak ada notifikasi baru</div>';
      return;
    }

    const rows = [];
    requests.forEach(function(b){
      if (b.status_admin === 'diterima' && b.metode_pembayaran !== 'COD' && b.jumlah_pembayaran && Number(b.jumlah_pembayaran) > 0 && Number(b.bayar_sekarang) !== 1) {
        rows.push(`
          <div class="notif-item">
            <span class="notif-icon"><i class="fa-solid fa-credit-card"></i></span>
            <div>
              <div>Konfirmasi pembayaran untuk <b>${b.layanan || 'Layanan'}</b>. Total: <b>${fmtRupiah(b.jumlah_pembayaran)}</b></div>
              <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
              <div style="margin-top:6px;">
                <a href="/pembayaran?booking=${encodeURIComponent(b.kode_booking || '')}" class="btn-detail" style="text-decoration:none; font-size:12px; padding:4px 10px; background:#eaf4ff; color:#007bff; border-radius:6px; font-weight:600; display:inline-block;">Bayar Sekarang</a>
              </div>
            </div>
          </div>
        `);
      } else if (b.status_admin === 'diterima') {
        rows.push(`
          <div class="notif-item">
            <span class="notif-icon"><i class="fa-solid fa-circle-check"></i></span>
            <div>
              <div>Booking <b>${b.layanan || 'Layanan'}</b> diterima. Teknisi siap menuju lokasi.</div>
              <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
            </div>
          </div>
        `);
      } else if (b.status_admin === 'ditolak') {
        rows.push(`
          <div class="notif-item">
            <span class="notif-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div>
              <div>Booking <b>${b.layanan || 'Layanan'}</b> ditolak oleh sistem/admin.</div>
              <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
            </div>
          </div>
        `);
      } else {
        rows.push(`
          <div class="notif-item">
            <span class="notif-icon"><i class="fa-solid fa-clock"></i></span>
            <div>
              <div>Permintaan <b>${b.layanan || 'Layanan'}</b> sedang diproses.</div>
              <div class="notif-time">${fmtTanggal(b.created_at)}</div>
            </div>
          </div>
        `);
      }
    });

    list.innerHTML = rows.join('') || '<div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 0.88rem;">Tidak ada notifikasi baru</div>';
  }

  renderNotifications();

  bell.addEventListener('click', function(e){
    e.stopPropagation();
    dropdown.classList.toggle('open');
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', function(e){
      e.stopPropagation();
      dropdown.classList.remove('open');
    });
  }

  document.addEventListener('click', function(e){
    if (!dropdown.contains(e.target) && e.target !== bell) {
      dropdown.classList.remove('open');
    }
  });
})();

