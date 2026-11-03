(function () {
  'use strict';

  function openTrxDetail(btn) {
    try {
      const card = btn.closest('.transaction-card');
      const d = card.dataset;
      const body = document.getElementById('trxDetailBody');
      const jumlah = d.jumlah ? 'Rp ' + d.jumlah : 'Rp 0';
      body.innerHTML = `
        <div><strong>Kode Pembayaran:</strong> ${d.kode}</div>
        <div><strong>Nama Pembayar:</strong> ${d.nama}</div>
        <div><strong>Email:</strong> ${d.email}</div>
        <div><strong>Metode:</strong> ${d.metode}</div>
        <div><strong>Status:</strong> ${d.status}</div>
        <div><strong>Jumlah:</strong> ${jumlah}</div>
      `;
      const modal = document.getElementById('trxDetailModal');
      modal.style.display = 'flex';
    } catch (e) {}
  }

  function closeTrxDetail() {
    const modal = document.getElementById('trxDetailModal');
    if (modal) modal.style.display = 'none';
  }

  function bindEvents() {
    // Bind tombol detail
    document.querySelectorAll('.btn-detail-trx').forEach(btn => {
      btn.addEventListener('click', () => openTrxDetail(btn));
    });

    // Bind close button
    const closeBtn = document.querySelector('.trx-detail-close');
    if (closeBtn) closeBtn.addEventListener('click', closeTrxDetail);

    // Bind tutup button
    const btnClose = document.querySelector('.trx-detail-btn-close');
    if (btnClose) btnClose.addEventListener('click', closeTrxDetail);

    // Bind backdrop click
    const modal = document.getElementById('trxDetailModal');
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeTrxDetail();
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindEvents);
  } else {
    bindEvents();
  }
})();
