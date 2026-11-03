
  // Toggle Sidebar
const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const main = document.querySelector(".main");

menuToggle.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    main.classList.toggle("shift");
});

// Active Menu Highlight
const menuItems = document.querySelectorAll(".menu-item");
menuItems.forEach(item => {
    item.addEventListener("click", () => {
        menuItems.forEach(i => i.classList.remove("active"));
        item.classList.add("active");
    });
});
// Ambil elemen modal & tombol
const modal = document.getElementById("logoutModal");
const closeBtn = document.querySelector(".modal-logout-close");
const cancelBtn = document.querySelector(".btn-cancel");
const confirmBtn = document.querySelector(".btn-confirm");
const logoutLink = document.querySelector(".logout a"); // tombol logout

// Fungsi untuk membuka modal
function openLogoutModal() {
    modal.style.display = "flex";
}

// Fungsi untuk menutup modal
function closeLogoutModal() {
    modal.style.display = "none";
}

// Event tombol ❌ dan Batal
closeBtn.addEventListener("click", closeLogoutModal);
cancelBtn.addEventListener("click", closeLogoutModal);

// Event klik di luar modal → tutup modal
window.addEventListener("click", (e) => {
    if (e.target === modal) {
        closeLogoutModal();
    }
});

// Event klik tombol Logout → buka modal
logoutLink.addEventListener("click", (e) => {
    e.preventDefault();
    openLogoutModal();
});

// Event klik tombol "Ya, Logout"
confirmBtn.addEventListener("click", () => {
    window.location.href = "/login/menu_login"; // arahkan ke menu_login.blade.php
});

document.addEventListener("DOMContentLoaded", () => {
    const popup = document.getElementById("popupDetail");
    const btn = document.getElementById("lihatDetailBtn");
    const closeBtn = document.getElementById("closePopup");

    btn.addEventListener("click", () => {
        popup.classList.add("show");
        popup.classList.remove("hide");
        popup.style.display = "flex";
        setTimeout(() => {
            popup.style.opacity = "1";
        }, 10);
    });

    function hidePopup() {
        popup.classList.add("hide");
        popup.classList.remove("show");
        popup.style.opacity = "0";
        setTimeout(() => {
            popup.style.display = "none";
        }, 400); // waktu animasi fade out
    }

    closeBtn.addEventListener("click", hidePopup);

    window.addEventListener("click", (event) => {
        if (event.target === popup) {
            hidePopup();
        }
    });
});
function openProfileModal() {
  let modal = document.getElementById("profileModal");
  modal.style.display = "flex";
  setTimeout(() => modal.classList.add("show"), 10); // kasih delay biar transisi jalan
}

function closeProfileModal() {
  let modal = document.getElementById("profileModal");
  modal.classList.remove("show");
  setTimeout(() => modal.style.display = "none", 250); // tunggu animasi selesai
}

  // Klik di luar modal -> tutup
  window.onclick = function(event) {
    let modal = document.getElementById("profileModal");
    if (event.target === modal) {
      modal.style.display = "none";
    }
  }
  // Helpers
  const qs = (s, el=document) => el.querySelector(s);
  const qsa = (s, el=document) => [...el.querySelectorAll(s)];

  // Open/Close modal utils
  function openModal(id){ qs('#'+id).classList.add('open'); }
  function closeModal(id){ qs('#'+id).classList.remove('open'); }

  // Hanya tombol di dalam .no-pointer yang aktif; card tidak clickable
  qsa('.no-pointer').forEach(el=>{
    el.addEventListener('click', e => e.stopPropagation());
  });

  // Booking card actions (tidak pasang listener di card)
  qsa('.js-booking').forEach(card=>{
    // Eye button -> buka detail (hanya ada kalau status Selesai)
    const eyeBtn = card.querySelector('.eye');
    if(eyeBtn){
      eyeBtn.addEventListener('click', (e)=>{
        e.stopPropagation();
        fillDetail(card);
        openModal('modalDetail');
      });
    }

    // Pencil -> Edit
    const editBtn = card.querySelector('.js-edit');
    if(editBtn){
      editBtn.addEventListener('click', (e)=>{
        e.stopPropagation();
        fillEdit(card);
        openModal('modalEdit');
      });
    }
  });

  // Fill EDIT modal dengan data dari card
  function fillEdit(card){
    const d = card.dataset;
    qs('#eTanggal').value = ''; // kosong (contoh UI kamu)
    qs('#eLokasi').value = d.lokasi || '';
    qs('#eDesk').value = d.desk || '';
    qs('#eUrgensi').value = d.urgensi || 'Normal';
    qs('#eBayar').value = d.pembayaran || 'Transfer';
    qs('#btnSimpan').dataset.ref = d.id || '';
  }

  // Fill DETAIL modal
  function fillDetail(card){
    const d = card.dataset;
    qs('#dJudul').textContent = d.judul || '-';
    qs('#dKode').textContent  = d.id || '-';
    qs('#dStatus').textContent = (d.status || '').toUpperCase();

    const hargaNum = Number(d.harga||0);
    qs('#dHarga').textContent = hargaNum>0 ? toIDR(hargaNum) : '—';
    qs('#dPembayaran').textContent = d.pembayaran || '—';

    qs('#dJadwal').textContent  = d.jadwalHari || d.jadwal || '-';
    qs('#dTukang').textContent   = d.tukang || '-';
    qs('#dLokasi').textContent   = d.lokasi || '-';
    qs('#dDesk').textContent     = d.desk || '-';
    qs('#dEstimasi').textContent = d.estimasi || '-';
    qs('#dUrgensi').textContent  = d.urgensi || '-';
  }

  function toIDR(n){ return 'Rp' + n.toLocaleString('id-ID'); }

  // Close handlers
  qsa('[data-close]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const which = btn.getAttribute('data-close');
      closeModal(which==='edit' ? 'modalEdit' : 'modalDetail');
    });
  });

  // ESC to close
  window.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape'){
      closeModal('modalEdit'); closeModal('modalDetail');
    }
  });

  // Simpan (dummy)
  qs('#btnSimpan').addEventListener('click', ()=>{
    closeModal('modalEdit');
    alert('Perubahan disimpan (dummy).');
  });

  // Klik "Lihat ARSIKA" (dummy)
  qs('.arsika').addEventListener('click', ()=>{
    alert('ARSIKA dibuka (dummy).');
  });

  const bell = document.querySelector('.bell');
const notifDropdown = document.getElementById('notifDropdown');

bell.addEventListener('click', () => {
  notifDropdown.classList.toggle('open');
});

document.addEventListener('click', (e) => {
  if (!bell.contains(e.target) && !notifDropdown.contains(e.target)) {
    notifDropdown.classList.remove('open');
  }
});
