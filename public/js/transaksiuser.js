(function () {
  'use strict';

  function byId(id) {
    return document.getElementById(id);
  }

  function setText(id, value) {
    var el = byId(id);
    if (el) el.textContent = value;
  }

  function formatRupiah(n) {
    var num = Number(n) || 0;
    return 'Rp ' + num.toLocaleString('id-ID');
  }

  function safeDateStr(dtStr) {
    if (!dtStr) return '-';
    try {
      var d = new Date(dtStr);
      if (!isNaN(d.getTime())) {
        return d.toLocaleString('id-ID', {
          year: 'numeric', month: '2-digit', day: '2-digit',
          hour: '2-digit', minute: '2-digit'
        });
      }
    } catch (e) {}
    return dtStr || '-';
  }

  function renderFotos(payment) {
    var container = byId('modal-foto');
    if (!container) return;

    var html = '';
    if (payment && payment.foto) {
      var list = Array.isArray(payment.foto) ? payment.foto : String(payment.foto).split(',');
      html = list.filter(Boolean).map(function (src) {
        var url = String(src).trim();
        if (!/^https?:\/\//i.test(url) && url[0] !== '/') url = '/' + url;
        return '<img src="' + url + '" alt="Bukti">';
      }).join('');
    } else if (payment && payment.foto_bukti) {
      var u = String(payment.foto_bukti).trim();
      if (!/^https?:\/\//i.test(u) && u[0] !== '/') u = '/' + u;
      html = '<img src="' + u + '" alt="Bukti">';
    }
    container.innerHTML = html;
  }

  function setHeaderBlur(on) {
    var h = document.querySelector('.header');
    if (h) h.style.filter = on ? 'blur(4px)' : '';
  }

  function showDetail(payment) {
    // Status
    var statusText = (payment && payment.status) ? (payment.status.charAt(0).toUpperCase() + payment.status.slice(1)) : '-';
    setText('modal-status', statusText);

    // Info layanan (opsional)
    setText('modal-layanan', (payment && payment.layanan) || '-');
    setText('modal-tukang', (payment && payment.tukang) || '-');
    setText('modal-tanggal', (payment && payment.tanggal_layanan) || '-');
    setText('modal-lokasi', (payment && payment.lokasi) || '-');

    // Detail pembayaran
    setText('modal-kode', (payment && payment.kode_pembayaran) || '-');
    setText('modal-metode', (payment && payment.metode_pembayaran) || '-');
    setText('modal-waktu', (payment && payment.created_at) ? safeDateStr(payment.created_at) : '-');
    setText('modal-total', formatRupiah(payment && payment.jumlah));

    // Foto bukti
    renderFotos(payment);

    // Tampilkan modal
    var modalEl = byId('modal');
    if (modalEl) modalEl.style.display = 'flex';
    setHeaderBlur(true);
  }

  function closeModal() {
    var modalEl = byId('modal');
    if (modalEl) modalEl.style.display = 'none';
    setHeaderBlur(false);
  }

  function bindCloseHandlers() {
    var modal = byId('modal');
    if (!modal) return;

    // Klik di luar konten menutup modal
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal();
    });

    // Tombol ESC menutup modal
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var m = byId('modal');
        if (m && m.style.display !== 'none') closeModal();
      }
    });
  }

  function bindButtons() {
    // Tangani klik tombol detail (mode capturing agar mencegah inline onclick error)
    document.addEventListener('click', function (e) {
      var target = e.target;
      if (!target) return;
      var btn = target.closest && target.closest('.btn-detail');
      if (!btn) return;
      e.preventDefault();
      // Cegah handler lain (termasuk inline) agar tidak menimbulkan error
      if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
      if (typeof e.stopPropagation === 'function') e.stopPropagation();

      var obj = null;
      var payload = btn.getAttribute('data-payment');
      if (payload) {
        try { obj = JSON.parse(payload); } catch (err) { obj = null; }
      }

      if (!obj) {
        // Fallback: ambil dari DOM transaksi terdekat
        var trx = btn.closest('.transaction');
        if (trx) {
          var kode = trx.querySelector('.left h4');
          var jumlah = trx.querySelector('.left p');
          var waktu = trx.querySelector('.left small');
          var metode = trx.querySelector('.right h4');
          var statusEl = trx.querySelector('.right .status');
          obj = {
            kode_pembayaran: kode ? kode.textContent.trim() : '-',
            jumlah: jumlah ? (function(){
              var t = jumlah.textContent.replace(/[^0-9]/g, '');
              return Number(t || 0);
            })() : 0,
            created_at: waktu ? waktu.textContent.trim() : '-',
            metode_pembayaran: metode ? metode.textContent.trim() : '-',
            status: statusEl ? statusEl.textContent.trim().toLowerCase() : '-'
          };
        } else {
          obj = {};
        }
      }

      showDetail(obj || {});
    }, true);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function(){
      bindCloseHandlers();
      bindButtons();
    });
  } else {
    bindCloseHandlers();
    bindButtons();
  }

  // Ekspor global agar bisa dipanggil dari atribut onclick pada Blade
  window.showDetail = showDetail;
  window.closeModal = closeModal;
})();
