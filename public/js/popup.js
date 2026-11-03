(function () {
  "use strict";

  function setText(id, value) {
    var el = document.getElementById(id);
    if (el) el.textContent = value;
  }

  function openBookingDetail(card) {
    if (!card) return;
    var modal = document.getElementById('modalDetail');
    if (!modal) return;

    var d = card.dataset || {};

    setText('dJudul', d.judul || '-');
    setText('dKode', 'Kode: ' + (d.kode || '-'));
    setText('dStatus', d.status || '-');

    var harga = (d.harga && d.harga !== '-') ? d.harga : '0';
    setText('dHarga', 'Rp ' + harga);
    if (document.getElementById('dHargaTotal')) {
      setText('dHargaTotal', 'Rp ' + harga);
    }

    setText('dPembayaran', d.pembayaran || '-');
    setText('dJadwal', d.tanggal || '-');
    setText('dTukang', d.tukang || '-');
    setText('dLokasi', d.lokasi || '-');
    setText('dDesk', d.desk || '-');
    setText('dUrgensi', d.urgensi || '-');

    modal.style.display = 'flex';
  }

  function bindBookingModal() {
    var modal = document.getElementById('modalDetail');
    if (!modal) return; // Hanya aktif di halaman yang punya modal detail booking

    // Buka modal saat tombol detail dalam kartu booking ditekan
    var btns = document.querySelectorAll('.booking-card .btn-detail');
    btns.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        // Pastikan hanya berlaku untuk kartu booking
        var card = this.closest('.booking-card');
        if (!card) return;
        openBookingDetail(card);
      });
    });

    // Tutup modal jika klik tombol close atau elemen bertanda data-close="detail"
    var closers = document.querySelectorAll('[data-close="detail"]');
    closers.forEach(function (el) {
      el.addEventListener('click', function () {
        modal.style.display = 'none';
      });
    });

    // Tutup saat klik backdrop
    modal.addEventListener('click', function (e) {
      if (e.target && e.target.classList && e.target.classList.contains('backdrop')) {
        modal.style.display = 'none';
      }
    });

    // Tutup saat tekan ESC
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        modal.style.display = 'none';
      }
    });
  }

  function bindInfoPopup() {
    var popup = document.getElementById('popupDetail');
    var openBtn = document.getElementById('lihatDetailBtn');
    var closeBtn = document.getElementById('closePopup');

    if (!popup || !openBtn) return;

    function setHeaderBlur(on) {
      var h = document.querySelector('.header');
      if (h) {
        h.style.filter = on ? 'blur(4px) brightness(0.6)' : '';
        h.style.transition = 'filter .2s ease';
      }
    }

    openBtn.addEventListener('click', function () {
      popup.style.display = 'flex';
      setHeaderBlur(true);
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        popup.style.display = 'none';
        setHeaderBlur(false);
      });
    }

    popup.addEventListener('click', function (e) {
      if (e.target === popup) {
        popup.style.display = 'none';
        setHeaderBlur(false);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        popup.style.display = 'none';
        setHeaderBlur(false);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      bindBookingModal();
      bindInfoPopup();
    });
  } else {
    bindBookingModal();
    bindInfoPopup();
  }
})();
