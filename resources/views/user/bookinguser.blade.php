<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Booking Saya — TEKSIKA</title>
<!-- Google Fonts: Plus Jakarta Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Fonts & Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('css/dashboard_user.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile-modal.css') }}">
<style>
:root{
    --blue:#007bff;         /* header & aksen utama */
    --blue-dark:#005ecf;    /* shadow/hover */
    --sidebar:#0a84ff;      /* sidebar */
    --bg:#f8fafc;           /* latar konten */
    --card:#ffffff;
    --text:#0f172a;
    --muted:#64748b;
    --line:#e2e8f0;
    --green:#10b981;
    --green-deep:#059669;
    --blackpill:#0f172a;
    --orange:#f59e0b;
    --pill:#f1f5f9;
    --radius:18px;
    --shadow:0 4px 20px -2px rgba(15, 23, 42, 0.05);
  }
  
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
    color:var(--text);
    background:var(--bg);
    overflow-x:hidden;
  }
  
  .header input {
    flex: none;
    width: 250px;
    padding: 10px 15px;
    border-radius: 20px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transition: all 0.3s ease;
  }
  
  .header input::placeholder {
    color: rgba(255, 255, 255, 0.7);
  }
  
  .header input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.3);
    width: 300px;
  }
  
  .user-info {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .user-info .fa-bell {
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
  }
  
  .user-info .fa-bell:hover {
    background: rgba(255, 255, 255, 0.1);
  }
  
  /* CONTENT - PERBAIKAN TAMPILAN SAJA */
  .content {
    padding: 30px;
  }
  
  .header-booking {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }
  
  .header-booking h2 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text);
    margin: 0;
    position: relative;
  }
  
  .header-booking h2::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: var(--blue);
    border-radius: 2px;
  }
  
  .btn-request {
    background-color: #0a45ff;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(10, 69, 255, 0.3);
  }
  
  .btn-request:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(10, 69, 255, 0.4);
  }
  
  .subtitle {
    margin-top: 15px;
    color: var(--muted);
    font-size: 16px;
    margin-bottom: 25px;
  }
  
  /* BOARD - PERBAIKAN TAMPILAN SAJA */
  .board {
    background: white;
    border-radius: 16px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    padding: 25px;
    margin-top: 10px;
    border: 1px solid var(--line);
  }
  
  /* BOOKING CARD - PERBAIKAN TAMPILAN SAJA */
  .booking-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    border: 1px solid var(--line);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  
  .booking-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--blue);
    border-radius: 2px 0 0 2px;
  }
  
  .booking-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  }
  
  .booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .booking-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
  }
  
  .booking-code {
    font-size: 14px;
    color: var(--blue);
    font-weight: 600;
    background: rgba(10, 124, 255, 0.1);
    padding: 4px 10px;
    border-radius: 20px;
  }
  
  .booking-card-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
  }
  
  .booking-date {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--muted);
    font-size: 14px;
  }
  
  .booking-date i {
    color: var(--blue);
  }
  
  .booking-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  
  .booking-status.status-progress {
    background: rgba(32, 196, 99, 0.1);
    color: var(--green);
  }
  
  .booking-status.status-waiting {
    background: rgba(255, 152, 0, 0.1);
    color: #ff9800;
  }
  
  .booking-status.status-rejected {
    background: rgba(244, 67, 54, 0.1);
    color: #f44336;
  }
  
  .booking-status.status-cancelled {
    background: rgba(158, 158, 158, 0.1);
    color: #9e9e9e;
  }
  
  .booking-worker {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--muted);
    font-size: 14px;
    margin-top: 5px;
  }
  
  .booking-worker i {
    color: var(--blue);
  }
  
  .booking-card-footer {
    display: flex;
    gap: 10px;
    margin-top: 5px;
    align-items: center;
    flex-wrap: wrap;
  }
  
  .btn-detail {
    background: rgba(10, 124, 255, 0.1);
    color: var(--blue);
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  
  .btn-detail:hover {
    background: var(--blue);
    color: white;
  }
  
  .btn-cancel {
    background: rgba(244, 67, 54, 0.1);
    color: #f44336;
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  
  .btn-cancel:hover {
    background: #f44336;
    color: white;
  }
  
  /* DROPDOWN NOTIF - PERBAIKAN TAMPILAN SAJA */
  .notif-dropdown {
    position: absolute;
    top: 60px;
    right: 100px;
    width: 280px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 99;
    font-family: 'Inter', sans-serif;
    animation: slideDown 0.3s ease;
  }
  
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .notif-dropdown.open {
    display: flex;
  }
  
  .notif-header {
    font-weight: 600;
    padding: 12px 15px;
    border-bottom: 1px solid var(--line);
    background: var(--bg);
    font-size: 15px;
  }
  
  .notif-item {
    padding: 12px 15px;
    font-size: 14px;
    color: var(--text);
    border-bottom: 1px solid var(--line);
    cursor: pointer;
    display: flex;
    gap: 10px;
    align-items: flex-start;
    transition: all 0.2s ease;
  }
  
  .notif-item:last-child {
    border-bottom: none;
  }
  
  .notif-item:hover {
    background: var(--bg);
  }
  
  .notif-icon {
    font-size: 16px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  
  .notif-icon .fa-credit-card {
    color: #1976d2;
    background: rgba(25, 118, 210, 0.1);
  }
  
  .notif-icon .fa-circle-check {
    color: var(--green);
    background: rgba(32, 196, 99, 0.1);
  }
  
  .notif-icon .fa-triangle-exclamation {
    color: #f44336;
    background: rgba(244, 67, 54, 0.1);
  }
  
  .notif-icon .fa-ban {
    color: #9e9e9e;
    background: rgba(158, 158, 158, 0.1);
  }
  
  .notif-icon .fa-clock {
    color: #ff9800;
    background: rgba(255, 152, 0, 0.1);
  }
  
  .notif-time {
    font-size: 12px;
    color: var(--muted);
    margin-top: 4px;
  }
  
  /* MODAL DETAIL - PERBAIKAN TAMPILAN SAJA */
  .modal {
    position: fixed;
    inset: 0;
    display: none;
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
  }
  
  .modal.open {
    display: flex;
  }
  
  .backdrop {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 1;
    pointer-events: auto;
  }
  
  .dialog {
    position: relative;
    background: #ffffff;
    border-radius: 20px;
    width: 94%;
    max-width: 880px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45);
    padding: 26px 30px;
    z-index: 2;
    animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  
  @keyframes modalFadeIn {
    from {
      opacity: 0;
      transform: translateY(-20px) scale(0.98);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .modal-dialog-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 18px;
  }

  .modal-dialog-info {
    flex: 1;
    min-width: 0;
  }

  .detail-title {
    font-weight: 800;
    font-size: 1.35rem;
    color: #0f172a;
    margin: 0 0 8px 0;
    line-height: 1.3;
    word-break: break-word;
  }

  .detail-meta-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .detail-code-badge {
    font-size: 0.8rem;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
  }

  .modal-dialog-side {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-shrink: 0;
  }
  
  .price-badge {
    text-align: right;
  }
  
  .price-badge .big {
    color: #10b981;
    font-size: 1.45rem;
    font-weight: 800;
    line-height: 1.2;
    white-space: nowrap;
  }

  .price-badge .muter {
    font-size: 0.75rem;
    color: #64748b;
    margin: 2px 0 0 0;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.04em;
  }

  .close-x {
    position: static;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }
  
  .close-x:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #ef4444;
    transform: rotate(90deg);
  }
  
  .muter {
    color: #64748b;
    font-size: 13px;
    margin-bottom: 4px;
    font-weight: 600;
  }
  
  .status-mini {
    background: rgba(32, 196, 99, 0.1);
    color: var(--green);
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    display: inline-block;
  }
  
  .detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 14px;
  }

  .detail-modal-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
    gap: 18px;
    margin-top: 20px;
  }

  .detail-panel,
  .detail-price-panel {
    min-width: 0;
    padding: 16px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: #fff;
  }

  .detail-panel h3,
  .detail-price-panel h3 {
    margin: 0;
    font-size: 16px;
    color: var(--text);
  }

  .detail-price-panel {
    background: #f8fbff;
    border-color: #bfdbfe;
  }

  .detail-price-panel h3 i {
    color: var(--blue);
  }

  .detail-price-caption {
    margin: 8px 0 12px;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.5;
  }

  .detail-price-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid #e8eef5;
    color: var(--text);
    font-size: 13px;
  }

  .detail-price-row strong {
    text-align: right;
    white-space: nowrap;
  }

  .detail-price-total {
    margin-top: 4px;
    border-bottom: 0;
    font-size: 15px;
    font-weight: 800;
  }

  .detail-price-total strong {
    color: var(--green);
  }
  
  .detail-grid > div {
    margin-bottom: 15px;
  }
  
  .detail-grid > div > div:last-child {
    font-size: 15px;
    color: var(--text);
    font-weight: 500;
  }
  
  .modal-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 25px;
  }
  
  .modal-actions .btn {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background: var(--blue);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .modal-actions .btn:hover {
    background: var(--blue-dark);
  }
  
  /* EMPTY STATE - PERBAIKAN TAMPILAN SAJA */
  .empty-state {
    text-align: center;
    color: var(--muted);
    margin-top: 50px;
    padding: 30px;
  }
  
  .empty-state i {
    font-size: 50px;
    color: var(--line);
    margin-bottom: 15px;
  }
  
  .empty-state div {
    font-size: 18px;
    margin-bottom: 10px;
  }
  
  /* MODAL LOGOUT - PERBAIKAN TAMPILAN SAJA */
  .modal-logout {
    display: none;
    position: fixed;
    z-index: 3000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
  }
  
  .modal-logout-content {
    background: white;
    border-radius: 16px;
    width: 420px;
    max-width: 90%;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    animation: modalFadeIn 0.3s ease;
  }
  
  .modal-logout-header {
    background: #4BAAF4;
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
  }
  
  .modal-logout-header h2 {
    margin: 15px 0 5px;
    font-size: 20px;
    font-weight: 600;
  }
  
  .modal-logout-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
  }
  
  .modal-logout-close {
    position: absolute;
    right: 15px;
    top: 15px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
    color: white;
    transition: all 0.3s ease;
  }
  
  .modal-logout-close:hover {
    background: rgba(255, 255, 255, 0.3);
  }
  
  .modal-logout-user {
    margin: 18px auto;
    background: #f4f7fb;
    padding: 12px 15px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 15px;
  }
  
  .modal-logout-user .role {
    background: #e8f4ff;
    color: #1b82e2;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
  }
  
  .modal-logout-warning {
    margin: 10px 15px;
    background: #fff3cd;
    color: #856404;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 14px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.4;
  }
  
  .modal-logout-footer {
    display: flex;
    justify-content: space-between;
    padding: 15px;
    background: #f8f9fa;
  }
  
  .btn-cancel {
    background: #f1f1f1;
    border: none;
    color: #333;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.2s;
  }
  
  .btn-cancel:hover {
    background: #e0e0e0;
  }
  
  .btn-confirm {
    background: #4BAAF4;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.2s;
  }
  
  .btn-confirm:hover {
    background: #399de9;
  }
  
  /* PAYMENT NOTIFICATION POPUP - PERBAIKAN TAMPILAN SAJA */
  #payNotify {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 4000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
  }
  
  #payNotify > div {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    text-align: center;
    max-width: 360px;
    width: 90%;
    animation: modalFadeIn 0.3s ease;
  }
  
  #payNotify i {
    font-size: 36px;
    color: #ff9800;
    margin-bottom: 15px;
  }
  
  #payNotify h3 {
    margin: 0 0 10px;
    font-size: 20px;
    color: var(--text);
  }
  
  #payNotify p {
    margin: 0 0 20px;
    color: var(--muted);
    line-height: 1.5;
  }
  
  #payNotifyClose {
    background: var(--bg);
    color: var(--text);
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  #payNotifyClose:hover {
    background: var(--line);
  }
  
  #payNotifyLink {
    background: var(--blue);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
  }
  
  #payNotifyLink:hover {
    background: var(--blue-dark);
  }
  
  /* SUCCESS POPUP - PERBAIKAN TAMPILAN SAJA */
  #popupSuccess {
    position: fixed;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--green);
    color: white;
    padding: 15px 25px;
    border-radius: 10px;
    z-index: 5000;
    box-shadow: 0 10px 25px rgba(32, 196, 99, 0.3);
    font-weight: 500;
    animation: slideDown 0.3s ease;
  }
  
  /* RESPONSIVE - PERBAIKAN TAMPILAN SAJA */
  @media (max-width: 768px) {
    .sidebar {
      width: 0;
    }
    
    .sidebar.open {
      width: 200px;
    }
    
    .main {
      margin-left: 0;
    }
    
    .main.shift {
      margin-left: 0;
    }
    
    .header input {
      width: 180px;
    }
    
    .header input:focus {
      width: 200px;
    }
    
    .header-booking {
      flex-direction: column;
      align-items: flex-start;
      gap: 15px;
    }
    
    .detail-grid {
      grid-template-columns: 1fr;
    }
    .detail-modal-layout {
      grid-template-columns: 1fr;
    }
    
    .booking-card-meta {
      flex-direction: column;
      gap: 8px;
    }
    
    .booking-card-footer {
      flex-direction: column;
    }
    
    .modal-logout-footer {
      flex-direction: column;
      gap: 10px;
    }
    
    .modal-logout-footer button {
      width: 100%;
    }
    
    .notif-dropdown {
      right: 20px;
      width: calc(100% - 40px);
      max-width: 320px;
    }
  }

  /* ========================================================
     MODERN RATING & REVIEW SYSTEM (CLEAN MONOCHROME / UNCOLORED)
     ======================================================== */
  .btn-rating-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    color: #1e293b;
    border: 1.5px solid #cbd5e1;
    padding: 7px 16px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .btn-rating-action i {
    color: #f59e0b;
    font-size: 0.95rem;
    transition: transform 0.2s ease;
  }

  .btn-rating-action:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
    transform: translateY(-1.5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .btn-rating-action:hover i {
    transform: scale(1.15);
  }

  /* Badge Ulasan Terkirim (Ketika Sudah Dinilai) */
  .user-review-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    padding: 6px 14px;
    border-radius: 12px;
    font-size: 0.82rem;
  }

  .review-stars {
    display: inline-flex;
    gap: 2px;
    font-size: 0.85rem;
  }

  .review-stars .star-active {
    color: #f59e0b;
  }

  .review-stars .star-muted {
    color: #cbd5e1;
  }

  .review-score {
    font-weight: 800;
    color: #b45309;
    font-size: 0.85rem;
  }

  .review-status {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    font-weight: 700;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
  }

  .review-snippet {
    color: #64748b;
    font-style: italic;
    font-size: 0.8rem;
    max-width: 220px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Dialog Modal Rating - Compact & Clean */
  .dialog-rating {
    max-width: 520px !important;
    padding: 22px 24px !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.3) !important;
  }

  .rating-modal-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 14px;
    position: relative;
  }

  .rating-header-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #fffbeb;
    border: 1.5px solid #fef3c7;
    color: #f59e0b;
    font-size: 1.15rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .rating-header-text {
    flex: 1;
    padding-right: 28px;
  }

  .rating-modal-title {
    font-size: 1.18rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2px 0;
    line-height: 1.2;
  }

  .rating-modal-subtitle {
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.4;
    margin: 0;
  }

  .rating-close-cross {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #f1f5f9;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-size: 1.25rem;
    line-height: 1;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
  }

  .rating-close-cross:hover {
    background: #fee2e2;
    color: #ef4444;
  }

  /* Kartu Teknisi dalam Modal */
  .rating-worker-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 14px;
  }

  .rating-worker-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #fffbeb;
    border: 1px solid #fef3c7;
    color: #f59e0b;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .rating-worker-info {
    flex: 1;
    min-width: 0;
  }

  .rating-target-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    color: #64748b;
    display: block;
  }

  .rating-worker-name {
    font-size: 0.96rem;
    font-weight: 700;
    color: #1e293b;
    margin: 1px 0 2px 0;
  }

  .rating-service-meta {
    font-size: 0.78rem;
    color: #0284c7;
    font-weight: 600;
  }

  /* Box Bintang Interaktif - Tanpa Warna Kuning (Clean Neutral / Monochrome) */
  .star-rating-box {
    text-align: center;
    margin-bottom: 14px;
    padding: 14px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
  }

  .star-rating-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
  }

  .interactive-stars {
    display: flex;
    justify-content: center;
    gap: 10px;
  }

  .star-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 32px;
    color: #cbd5e1;
    padding: 3px;
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), color 0.15s ease, filter 0.15s ease;
    line-height: 1;
  }

  .star-btn:hover {
    transform: scale(1.18);
  }

  /* Bintang saat dirating (active & hovered): langsung berwarna kuning/emas */
  .star-btn.active {
    color: #f59e0b;
    filter: drop-shadow(0 2px 5px rgba(245, 158, 11, 0.3));
  }

  .star-btn.hovered {
    color: #fbbf24 !important;
    transform: scale(1.2);
    filter: drop-shadow(0 3px 8px rgba(245, 158, 11, 0.4));
  }

  .star-feedback-text {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
  }

  .feedback-badge {
    background: #fef3c7;
    border: 1px solid #fde68a;
    color: #b45309;
    font-size: 0.78rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 6px;
  }

  .feedback-desc {
    font-size: 0.84rem;
    font-weight: 700;
    color: #0f172a;
  }

  /* Quick Praise Chips - Tanpa Icon Lain */
  .rating-chips-box {
    margin-bottom: 14px;
  }

  .rating-chips-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
    display: block;
  }

  .rating-chips-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .chip-item {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    border-radius: 16px;
    font-size: 0.76rem;
    padding: 5px 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
  }

  .chip-item:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
  }

  /* Textarea */
  .rating-textarea-box {
    margin-bottom: 16px;
  }

  .rating-textarea-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 5px;
    display: block;
  }

  .rating-textarea {
    width: 100%;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 12px;
    font-family: inherit;
    font-size: 0.84rem;
    resize: vertical;
    min-height: 65px;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
    background: #ffffff;
    color: #1e293b;
  }

  .rating-textarea:focus {
    outline: none;
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
  }

  .textarea-counter {
    font-size: 0.72rem;
    color: #94a3b8;
    text-align: right;
    margin-top: 4px;
  }

  /* Actions */
  .rating-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
  }

  .btn-rating-cancel {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 8px 18px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    font-family: inherit;
  }

  .btn-rating-cancel:hover {
    background: #e2e8f0;
    color: #1e293b;
  }

  .btn-rating-submit {
    background: #0284c7;
    border: none;
    color: #ffffff;
    padding: 8px 22px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
    font-family: inherit;
  }

  .btn-rating-submit:hover {
    background: #0369a1;
    transform: translateY(-1px);
  }
</style>
</head>
<body>
   <!-- Sidebar - TANPA PERUBAHAN STRUKTUR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo-wrap">
            <img src="{{ asset('images/logo-putih.png') }}" alt="Logo" class="sidebar-logo">
        </div>
        <a href="{{ route('dashboard_user') }}" class="menu-item">
            <img src="{{ asset('images/home.png') }}" alt="logo" class="icon">
            <span class="menu-text">Beranda</span>
        </a>
        <a href="{{ route('bookinguser') }}" class="menu-item active">
            <img src="{{ asset('images/booking.png') }}" alt="logo" class="icon">
            <span class="menu-text">Booking</span>
        </a>
        <a href="{{ route('request') }}" class="menu-item">
            <img src="{{ asset('images/request.png') }}" alt="logo" class="icon">
            <span class="menu-text">Request</span>
        </a>
        <a href="{{ route('chatuser') }}" class="menu-item">
            <img src="{{ asset('images/chat.png') }}" alt="logo" class="icon">
            <span class="menu-text">Chat</span>
        </a>
        <a href="{{ route('transaksi_user') }}" class="menu-item">
            <img src="{{ asset('images/pembayaran.png') }}" alt="logo" class="icon">
            <span class="menu-text">Transaksi</span>
        </a>
        <div class="logout">
            <a href="javascript:void(0)" onclick="openLogoutModal()" class="menu-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="menu-text">Keluar</span>
            </a>
        </div>
    </div>
    
    <!-- Modal Logout -->
    <div id="logoutModal" class="modal-logout" style="display:none;" onclick="if(event.target===this) closeLogoutModal();">
      <div class="modal-logout-content">
        <span class="modal-logout-close" onclick="closeLogoutModal()">&times;</span>
        <div class="modal-logout-header">
          <i class="fa-solid fa-right-from-bracket icon-logout"></i>
          <h2>Konfirmasi Keluar</h2>
          <p>Yakin ingin keluar dari akun TEKSIKA?</p>
        </div>
        <div class="modal-logout-user">
          <div class="user-info">
            <i class="fa-solid fa-user-circle"></i>
            <div>
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong><br>
              <span>{{ auth()->user()->email ?? '-' }}</span>
            </div>
          </div>
        </div>
        <div class="modal-logout-warning">
          <i class="fa-solid fa-circle-exclamation"></i>
          <p>Anda akan keluar dari akun TEKSIKA ini dan perlu login kembali untuk mengakses pesanan.</p>
        </div>
        <div class="modal-logout-footer">
          <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
          <button type="button" class="btn-confirm" onclick="confirmLogout()">Ya, Logout</button>
        </div>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="main" id="main">
        <!-- Header -->
        <div class="header">
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>
            <input type="text" id="searchBookingUser" placeholder="Cari booking...">
            <div class="user-info">
                <div class="header-notif-btn" id="notifBell" title="Notifikasi" style="cursor:pointer;">
                    <i class="fa-solid fa-bell"></i>
                </div>
                @php
                    $avatarUrlHeader = !empty(auth()->user()->avatar) ? asset('storage/'.auth()->user()->avatar) : asset('images/userabu.png');
                @endphp
                <div class="header-profile-pill" onclick="openProfileModal()" title="Profil Akun">
                    <img id="avatarHeader" src="{{ $avatarUrlHeader }}" alt="{{ auth()->user()->name ?? 'User' }}">
                    <span>{{ auth()->user()->name ?? 'Akun Saya' }}</span>
                    <i class="fa-solid fa-chevron-down profile-chevron"></i>
                </div>
            </div>
        </div>
        
        <!-- CONTENT - PERBAIKAN TAMPILAN SAJA -->
        <main class="content">
            <div class="header-booking">
                <h2>BOOKING SAYA</h2>
                <a href="{{ route('request') }}"><button class="btn-request">+ Mulai Request</button></a>
            </div>
            <p class="subtitle">Kelola Semua Booking dan Jadwal Anda</p>
            
            <!-- Dropdown Notifikasi - PERBAIKAN TAMPILAN SAJA -->
            <div class="notif-dropdown" id="notifDropdown">
              <span id="notifCloseX" style="position:absolute; top:8px; right:12px; font-size:18px; color:#888; cursor:pointer; z-index:100;">&times;</span>
              <div class="notif-header">Notifikasi</div>
              <div id="notifList" style="max-height:320px; overflow-y:auto;"></div>
            </div>

            @if(session('success'))
              <div style="margin-bottom:14px; padding:12px 16px; border:1px solid #86efac; background:#f0fdf4; color:#166534; border-radius:8px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
              <div style="margin-bottom:14px; padding:12px 16px; border:1px solid #fecaca; background:#fef2f2; color:#991b1b; border-radius:8px;">{{ session('error') }}</div>
            @endif
            
            <section class="board">
                @if($requests->count())
                    @foreach($requests as $request)
                @php
                  $statusLabels = [
                    'menunggu' => 'Menunggu pemeriksaan admin',
                    'menunggu_persetujuan' => 'Penawaran menunggu persetujuan Anda',
                    'persetujuan_dikirim' => 'Persetujuan terkirim - menunggu verifikasi admin',
                    'dikonfirmasi' => 'Tukang ditugaskan',
                    'dikerjakan' => 'Sedang dikerjakan',
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak',
                    'dibatalkan' => 'Dibatalkan',
                  ];
                  $statusLabel = $statusLabels[$request->status] ?? 'Menunggu pemeriksaan admin';
                  $detailOffer = $request->selectedOffer
                    ?? ($request->estimasi_total !== null ? null : $request->offers->sortBy('total_estimasi')->first());
                  $detailOfferWorker = $request->selectedOffer?->worker
                    ?? $request->worker
                    ?? $request->estimateWorker
                    ?? $detailOffer?->worker;
                  $detailWorkerLabel = $detailOfferWorker?->name
                    ?? ($request->preferredWorker
                      ? 'Preferensi form: ' . $request->preferredWorker->name . ' (belum dikonfirmasi)'
                      : ($request->estimasi_total !== null ? 'Belum dipilih; memakai tarif awal layanan' : 'Belum dipilih'));
                  $detailOfferIsSelected = $request->selectedOffer !== null || $request->worker !== null;
                  $detailBasePrice = $detailOffer?->harga_dasar_snapshot ?? $request->harga_dasar_estimasi ?? $request->harga_layanan;
                  if ($detailOffer && $detailBasePrice === null && (float) $detailOffer->faktor_harga_snapshot > 0) {
                    $detailBasePrice = round((float) $detailOffer->biaya_jasa_snapshot / (float) $detailOffer->faktor_harga_snapshot);
                  }
                  $detailUrgencyCharge = $detailOffer?->biaya_urgensi_snapshot ?? $request->biaya_urgensi_estimasi;
                  $detailDamageCharge = $detailOffer?->biaya_kerusakan_snapshot ?? $request->biaya_kerusakan_estimasi;
                  $detailQualificationCharge = $detailOffer?->biaya_kualifikasi_snapshot ?? $request->biaya_kualifikasi_estimasi;
                  $detailRatingCharge = $detailOffer?->biaya_rating_snapshot ?? $request->biaya_rating_estimasi;
                  $detailExperienceCharge = $detailOffer?->biaya_pengalaman_snapshot ?? $request->biaya_pengalaman_estimasi;
                  $detailExperiencePercent = $detailOffer?->pengalaman_persen_snapshot ?? $request->pengalaman_persen_estimasi;
                  $detailWorkerFee = $detailOffer
                    ? (float) ($detailOffer->biaya_kualifikasi_snapshot ?? ((float) ($detailRatingCharge ?? 0) + (float) ($detailExperienceCharge ?? 0)))
                    : (float) ($detailQualificationCharge ?? 0);
                  $detailRating = $detailOffer?->rating_snapshot ?? $request->rating_estimasi ?? $detailOfferWorker?->rating;
                  $detailExperience = $detailOffer?->pengalaman_snapshot ?? $request->pengalaman_estimasi ?? $detailOfferWorker?->pengalaman;
                  $detailAdditionalCharge = $detailOffer?->biaya_additional_snapshot ?? $request->biaya_additional_estimasi;
                  $detailServiceCharge = $detailOffer?->biaya_jasa_snapshot ?? ($request->harga_layanan !== null ? (float) $request->harga_layanan + (float) ($request->biaya_urgensi_estimasi ?? 0) + (float) ($request->biaya_kerusakan_estimasi ?? 0) : null);
                  $detailMaterialCharge = $detailOffer?->biaya_material_snapshot ?? 0;
                  $detailTransportCharge = $detailOffer?->biaya_transportasi ?? $request->biaya_jarak;
                  $detailPriceTotal = $detailOffer?->total_estimasi ?? $request->estimasi_total;
                  $detailPriceStatus = $detailOffer
                    ? ($detailOfferIsSelected ? 'Penawaran tukang pilihan' : 'Penawaran awal berdasarkan kandidat tukang')
                    : ($request->estimasi_total !== null
                      ? ($request->estimateWorker
                        ? 'Estimasi langsung dari tarif ' . $request->estimateWorker->name . ' · rating ' . number_format((float) ($request->rating_estimasi ?? $request->estimateWorker->rating ?? 5), 1) . ' · pengalaman ' . (int) ($request->pengalaman_estimasi ?? $request->estimateWorker->pengalaman ?? 0) . ' th'
                        : 'Estimasi awal memakai harga layanan karena belum ada tukang yang cocok')
                      : 'Menunggu estimasi');
                @endphp
                    <div class="booking-card js-booking"
                        data-id="{{ $request->id_request }}"
                        data-judul="{{ $request->layanan }}"
                        data-kode="{{ $request->kode_booking }}"
                        data-tanggal="{{ \Carbon\Carbon::parse($request->created_at)->translatedFormat('l, d F Y H:i') }}"
                        data-status="{{ strtoupper($statusLabel) }}"
                        data-lokasi="{{ $request->lokasi ?? '-' }}"
                        data-desk="{{ $request->detail ?? '-' }}"
                        data-pembayaran="{{ $request->metode_pembayaran ?? '-' }}"
                        data-estimasi="{{ $detailPriceTotal !== null ? 'Rp ' . number_format($detailPriceTotal, 0, ',', '.') : '-' }}"
                        data-urgensi="{{ $request->urgensi ?? '-' }}"
                        data-kerusakan="{{ $request->tingkat_kerusakan ?? '-' }}"
                        data-kompleksitas="{{ $request->kompleksitas ?? '-' }}"
                        data-tukang="{{ $detailWorkerLabel }}"
                        data-rating="{{ $detailOffer?->rating_snapshot ?? $request->rating_estimasi ?? $detailOfferWorker?->rating ?? '' }}"
                        data-pengalaman="{{ $detailOffer?->pengalaman_snapshot ?? $request->pengalaman_estimasi ?? $detailOfferWorker?->pengalaman ?? '' }}"
                        data-jarak="{{ $detailOffer?->jarak_km ?? $request->jarak_km ?? '' }}"
                        data-jarak-label="{{ $detailOffer ? 'Jarak rute berkendara penawaran' : 'Jarak rute berkendara' }}"
                        data-durasi-layanan="{{ $detailOffer?->estimasi_durasi_menit ?? $request->estimasi_durasi_menit ?? '' }}"
                        data-harga="{{ $detailPriceTotal ?? '' }}"
                        data-harga-dasar="{{ $detailBasePrice ?? '' }}"
                        data-biaya-kualifikasi="{{ $detailQualificationCharge ?? '' }}"
                        data-biaya-rating="{{ $detailRatingCharge ?? '' }}"
                        data-biaya-pengalaman="{{ $detailExperienceCharge ?? '' }}"
                        data-pengalaman-persen="{{ $detailExperiencePercent ?? '' }}"
                        data-skor-rating="{{ $detailRating !== null ? number_format((float) $detailRating * 0.25, 2, '.', '') : '' }}"
                        data-skor-pengalaman="{{ $detailExperience !== null ? number_format((float) $detailExperience * 0.25, 2, '.', '') : '' }}"
                        data-biaya-tukang="{{ $detailWorkerFee }}"
                        data-biaya-additional="{{ $detailAdditionalCharge ?? '' }}"
                        data-biaya-urgensi="{{ $detailUrgencyCharge ?? '' }}"
                        data-biaya-kerusakan="{{ $detailDamageCharge ?? '' }}"
                        data-biaya-jasa="{{ $detailServiceCharge ?? '' }}"
                        data-biaya-material="{{ $detailMaterialCharge ?? '' }}"
                        data-biaya-transportasi="{{ $detailTransportCharge ?? '' }}"
                        data-total-penawaran="{{ $detailPriceTotal ?? '' }}"
                        data-status-harga="{{ $detailPriceStatus }}">
                        <div class="booking-card-content">
                            <div class="booking-card-header">
                                <div class="booking-title">{{ $request->layanan }}</div>
                                <div class="booking-code">Kode: <b>{{ $request->kode_booking ?? '-' }}</b></div>
                            </div>
                            <div class="booking-card-meta">
                                <span class="booking-date">
                                    <i class="far fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($request->created_at)->translatedFormat('d M Y H:i') }}
                                </span>
                                <span class="booking-status {{ in_array($request->status, ['dikonfirmasi', 'dikerjakan', 'selesai'], true) ? 'status-progress' : (in_array($request->status, ['ditolak', 'dibatalkan'], true) ? ($request->status === 'dibatalkan' ? 'status-cancelled' : 'status-rejected') : 'status-waiting') }}">
                                    <i class="fas fa-circle"></i>
                                  {{ $statusLabel }}
                                </span>
                            </div>
                            <div style="margin: 6px 0 2px; font-weight: 700; color: #007bff; font-size: 0.95rem;">
                                @if($request->offers->count())
                                  {{ $request->offers->count() }} penawaran tukang tersedia
                                @elseif($request->status_admin === 'diterima')
                                  Total disetujui: Rp {{ number_format($request->estimasi_total ?? $request->jumlah_pembayaran ?? 0, 0, ',', '.') }}
                                @elseif($request->estimasi_total !== null)
                                  Estimasi awal: Rp {{ number_format($request->estimasi_total, 0, ',', '.') }}
                                @else
                                  <span style="color:#64748b; font-weight:500;">Harga menunggu pemeriksaan admin</span>
                                @endif
                            </div>
                              @if($request->offers->count())
                                <div style="margin-top:10px; padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; color:#334155; font-size:0.88rem;">
                                  <strong>Bandingkan penawaran</strong>
                                  <div style="display:grid; gap:9px; margin-top:9px;">
                                    @foreach($request->offers as $offer)
                                      <article style="padding:10px; border:1px solid {{ (int) $request->id_penawaran_terpilih === (int) $offer->id ? '#22c55e' : '#dbe2ea' }}; border-radius:7px; background:#fff;">
                                        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                          @if($offer->worker?->foto)<img src="{{ asset('storage/' . $offer->worker->foto) }}" alt="{{ $offer->worker->name }}" onerror="this.onerror=null;this.src='{{ asset('images/userabu.png') }}';" style="width:42px;height:42px;object-fit:cover;border-radius:50%;">@endif
                                          <div style="flex:1; min-width:150px;"><strong>{{ $offer->worker?->name ?? 'Mitra' }}</strong><div style="color:#64748b;font-size:.8rem;">{{ $offer->keahlian_snapshot }} · {{ $offer->pengalaman_snapshot }} thn · ★ {{ number_format($offer->rating_snapshot, 1) }} ({{ $offer->jumlah_ulasan_snapshot }} ulasan)</div></div>
                                          <div style="font-size:.82rem;">
                                            Tarif layanan <b>Rp {{ number_format($offer->harga_dasar_snapshot ?? 0, 0, ',', '.') }}</b><br>
                                            @if($offer->biaya_rating_snapshot !== null || $offer->biaya_pengalaman_snapshot !== null)
                                              Skor rating <b>{{ number_format((float) $offer->rating_snapshot * 0.25, 2) }}</b> · skor pengalaman <b>{{ number_format((float) $offer->pengalaman_snapshot * 0.25, 2) }}</b><br>
                                              Biaya tukang <b>Rp {{ number_format($offer->biaya_kualifikasi_snapshot ?? 0, 0, ',', '.') }}</b><br>
                                            @else
                                              Penyesuaian rating & pengalaman <b>+Rp {{ number_format($offer->biaya_kualifikasi_snapshot ?? 0, 0, ',', '.') }}</b><br>
                                            @endif
                                            Urgensi <b>+Rp {{ number_format($offer->biaya_urgensi_snapshot ?? 0, 0, ',', '.') }}</b> · kerusakan <b>+Rp {{ number_format($offer->biaya_kerusakan_snapshot ?? 0, 0, ',', '.') }}</b><br>
                                            Jasa <b>Rp {{ number_format($offer->biaya_jasa_snapshot ?? 0, 0, ',', '.') }}</b> · material <b>Rp {{ number_format($offer->biaya_material_snapshot ?? 0, 0, ',', '.') }}</b><br>
                                            @if((float) ($offer->biaya_additional_snapshot ?? 0) > 0)
                                              Biaya tambahan (data booking lama) <b>+Rp {{ number_format($offer->biaya_additional_snapshot, 0, ',', '.') }}</b><br>
                                            @endif
                                            Biaya jarak <b>Rp {{ number_format($offer->biaya_transportasi, 0, ',', '.') }}</b>
                                          </div>
                                          <strong style="font-size:.95rem;">Rp {{ number_format($offer->total_estimasi, 0, ',', '.') }}</strong>
                                          @if($offer->status === 'dipilih')<span style="color:#166534;font-weight:700;">Pilihan Anda</span>@endif
                                          @if($offer->status === 'ditugaskan')<span style="color:#166534;font-weight:700;">Tukang ditugaskan</span>@endif
                                          @if($request->status === 'menunggu_persetujuan' && $offer->status === 'ditawarkan')
                                            <form action="{{ route('bookinguser.pilih-penawaran', $request->id_request) }}" method="POST" onsubmit="return confirm('Pilih {{ $offer->worker?->name }} dengan total Rp {{ number_format($offer->total_estimasi, 0, ',', '.') }}?');">
                                              @csrf
                                              <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                              <button type="submit" class="btn-detail">Pilih Tukang</button>
                                            </form>
                                          @endif
                                        </div>
                                        <div style="margin-top:5px;color:#64748b;font-size:.78rem;">Biaya jarak dihitung dari kantor pusat, {{ $offer->jarak_km }} km · durasi layanan sesuai booking {{ $offer->estimasi_durasi_menit }} menit</div>
                                        @if($offer->worker && $offer->worker->reviews->isNotEmpty())
                                          <div style="display:grid;gap:3px;margin-top:6px;color:#475569;font-size:.78rem;">
                                            @foreach($offer->worker->reviews->whereNotNull('ulasan')->take(2) as $review)
                                              <span>“{{ \Illuminate\Support\Str::limit($review->ulasan, 140) }}” · {{ $review->rating }}/5</span>
                                            @endforeach
                                          </div>
                                        @endif
                                        @if($offer->worker)
                                          <a href="{{ route('tukang.ulasan', $offer->worker->id) }}" style="display:inline-block; margin-top:7px; color:#2563eb; font-size:.8rem; font-weight:700;">Lihat semua ulasan</a>
                                        @endif
                                      </article>
                                    @endforeach
                                  </div>
                                  <div style="margin-top:8px;color:#64748b;">Kerusakan {{ ucfirst($request->tingkat_kerusakan ?? '-') }}, kompleksitas {{ ucfirst($request->kompleksitas ?? '-') }}, urgensi {{ ucfirst($request->urgensi ?? '-') }}.@if($request->penawaran_catatan) Catatan admin: {{ $request->penawaran_catatan }}@endif</div>
                                </div>
                              @elseif($request->penawaran_dikirim_at && $request->estimasi_total)
                                <div style="margin-top:10px;padding:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:.86rem;">Penawaran lama: Rp {{ number_format($request->estimasi_total, 0, ',', '.') }}. Pilihan tukang tidak tersedia untuk penawaran lama.</div>
                              @endif
                              @if($request->status_admin == 'diterima' && $request->worker)
                                <div class="booking-worker">
                                    <i class="fa fa-user" style="color:#1e88e5"></i>
                                    <span>Tukang: <b>{{ $request->worker->name }}</b></span>
                                </div>
                            @endif
                            <div class="booking-card-footer">
                                <button class="btn-detail" title="Lihat Detail"><i class="fa fa-eye"></i> Detail</button>
                                @if($request->status_admin === 'diterima' && $request->status === 'dikonfirmasi' && (int) $request->bayar_sekarang !== 1 && (float) $request->jumlah_pembayaran > 0)
                                    <a href="{{ route('pembayaran.create', ['booking' => $request->kode_booking]) }}" class="btn-detail" style="text-decoration:none; background:#0a84ff; color:#fff; border-color:#0a84ff;">
                                        <i class="fa-solid fa-credit-card"></i> Bayar Rp {{ number_format($request->jumlah_pembayaran, 0, ',', '.') }}
                                    </a>
                                @elseif($request->status_admin === 'diterima' && (int) $request->bayar_sekarang === 1)
                                    <span style="align-self:center; color:#15803d; font-size:0.88rem; font-weight:700;">
                                        <i class="fa-solid fa-circle-check"></i> Pembayaran lunas
                                    </span>
                                @endif
                                @if($request->status === 'persetujuan_dikirim')
                                  <span style="align-self:center; color:#166534; font-size:0.88rem;">Pilihan Anda menunggu verifikasi admin.</span>
                                @endif
                                @if(in_array($request->status, ['menunggu', 'menunggu_persetujuan'], true) && $request->status_admin === 'belum_diterima')
                                    <form action="{{ route('bookinguser.batal', $request->id_request) }}" method="POST" class="no-pointer" onsubmit="return confirm('Batalkan booking ini?');">
                                        @csrf
                                    <button type="submit" class="btn-cancel" title="Batalkan">Batalkan Pesanan</button>
                                    </form>
                                @endif
                                @if($request->status === 'selesai' && !$request->review)
                                    <button type="button" class="btn-rating-action" onclick="openRatingModal('{{ $request->id_request }}', '{{ addslashes($request->worker ? $request->worker->name : 'Teknisi TEKSIKA') }}', '{{ addslashes($request->layanan) }}', '{{ $request->kode_booking }}')">
                                        <i class="fa-solid fa-star"></i>
                                        <span>Beri Rating &amp; Ulasan</span>
                                    </button>
                                @elseif($request->review)
                                    <div class="user-review-badge">
                                        <div class="review-stars">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i class="fa-solid fa-star {{ $s <= $request->review->rating ? 'star-active' : 'star-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="review-score">{{ $request->review->rating }}.0</span>
                                        <span class="review-status">Ulasan Terkirim</span>
                                        @if($request->review->ulasan)
                                            <span class="review-snippet" title="{{ $request->review->ulasan }}">“{{ \Illuminate\Support\Str::limit($request->review->ulasan, 40) }}”</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        <div>Belum ada booking. Silakan request layanan terlebih dahulu.</div>
                    </div>
                @endif
            </section>
        </main>
    </div>
    
    <!-- ====== MODAL DETAIL BOOKING - PERBAIKAN TAMPILAN SAJA ====== -->
    <div class="modal" id="modalDetail" aria-hidden="true">
        <div class="backdrop" data-close="detail"></div>
        <div class="dialog">
            <!-- Header Modal Modern & Terpisah Rapi -->
            <div class="modal-dialog-header">
                <div class="modal-dialog-info">
                    <h2 class="detail-title" id="dJudul"></h2>
                    <div class="detail-meta-row">
                        <span class="detail-code-badge" id="dKode"></span>
                        <span class="status-mini" id="dStatus"></span>
                    </div>
                </div>
                <div class="modal-dialog-side">
                    <div class="price-badge">
                        <div class="big" id="dHarga"></div>
                        <div class="muter" id="dPembayaran"></div>
                    </div>
                    <button class="close-x" data-close="detail" aria-label="Tutup" title="Tutup Detail">&times;</button>
                </div>
            </div>
            <div class="detail-modal-layout">
                <div class="detail-panel">
                    <h3>Detail pesanan</h3>
                    <div class="detail-grid">
                        <div>
                            <div class="muter">Jadwal</div>
                            <div id="dJadwal"></div>
                        </div>
                        <div>
                            <div class="muter">Tukang</div>
                            <div id="dTukang"></div>
                        </div>
                        <div>
                            <div class="muter">Lokasi</div>
                            <div id="dLokasi"></div>
                        </div>
                        <div>
                            <div class="muter">Keterangan</div>
                            <div id="dDesk" style="white-space: pre-wrap; word-break: break-word;"></div>
                        </div>
                        <div>
                            <div class="muter">Tingkat Urgensi</div>
                            <div id="dUrgensi"></div>
                        </div>
                        <div>
                            <div class="muter">Tingkat Kerusakan</div>
                            <div id="dKerusakan"></div>
                        </div>
                        <div>
                            <div class="muter">Kompleksitas</div>
                            <div id="dKompleksitas"></div>
                        </div>
                        <div>
                            <div class="muter">Jarak dan durasi layanan</div>
                            <div id="dJarak"></div>
                        </div>
                        <div>
                            <div class="muter">Metode Pembayaran</div>
                            <div id="dPembayaranDetail"></div>
                        </div>
                    </div>
                </div>
                <div class="detail-price-panel">
                    <h3><i class="fa-solid fa-receipt"></i> Transparansi harga</h3>
                    <p class="detail-price-caption" id="dHargaStatus"></p>
                    <div class="detail-price-row"><span>Tarif layanan</span><strong id="dHargaDasar"></strong></div>
                    <div class="detail-price-row" id="dRowLegacyQualification" style="display:none;"><span>Penyesuaian kualifikasi (data lama)</span><strong id="dBiayaKualifikasi"></strong></div>
                    <div class="detail-price-row" id="dRowRating"><span id="dRatingLabel">Skor rating</span><strong id="dBiayaRating"></strong></div>
                    <div class="detail-price-row" id="dRowExperience"><span id="dExperienceLabel">Skor pengalaman</span><strong id="dBiayaPengalaman"></strong></div>
                    <div class="detail-price-row" id="dRowWorkerFee"><span id="dWorkerFeeLabel">Biaya tukang</span><strong id="dBiayaTukang"></strong></div>
                    <div class="detail-price-row" id="dRowAdditional" style="display:none;"><span>Biaya tambahan (data booking lama)</span><strong id="dBiayaAdditional"></strong></div>
                    <div class="detail-price-row"><span>Tambahan urgensi</span><strong id="dBiayaUrgensi"></strong></div>
                    <div class="detail-price-row"><span>Tambahan tingkat kerusakan</span><strong id="dBiayaKerusakan"></strong></div>
                    <div class="detail-price-row"><span>Subtotal jasa</span><strong id="dBiayaJasa"></strong></div>
                    <div class="detail-price-row"><span>Material / suku cadang</span><strong id="dBiayaMaterial"></strong></div>
                    <div class="detail-price-row"><span>Transportasi</span><strong id="dBiayaTransportasi"></strong></div>
                    <div class="detail-price-row detail-price-total"><span>Total penawaran</span><strong id="dHargaTotal"></strong></div>
                    <p class="detail-price-caption" id="dHargaNote"></p>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn" data-close="detail">Tutup</button>
            </div>
        </div>
    </div>
    
    <!-- ====== MODAL RATING & ULASAN TEKNISI ====== -->
    <div class="modal" id="modalRating" aria-hidden="true">
        <div class="backdrop" data-close="rating"></div>
        <div class="dialog dialog-rating">
            <div class="rating-modal-header">
                <div class="rating-header-icon">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="rating-header-text">
                    <h2 class="rating-modal-title">Beri Rating &amp; Ulasan</h2>
                    <p class="rating-modal-subtitle">Bagikan penilaian performa kerja teknisi untuk menjaga kualitas layanan TEKSIKA.</p>
                </div>
                <button type="button" class="rating-close-cross" data-close="rating" aria-label="Tutup">&times;</button>
            </div>

            <form id="ratingForm" method="POST" action="">
                @csrf
                <!-- Kartu Ringkasan Teknisi -->
                <div class="rating-worker-card">
                    <div class="rating-worker-avatar">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="rating-worker-info">
                        <span class="rating-target-label">Teknisi Bertugas</span>
                        <h4 class="rating-worker-name" id="ratingWorkerName">Nama Teknisi</h4>
                        <div class="rating-service-meta">
                            <span id="ratingServiceTitle">Layanan</span> &bull; <span id="ratingBookingCode">BK-000</span>
                        </div>
                    </div>
                </div>

                <!-- Bintang Interaktif (Tanpa Warna Kuning, Hanya Ikon Bintang) -->
                <div class="star-rating-box">
                    <label class="star-rating-label">Bagaimana kepuasan hasil pengerjaan?</label>
                    <div class="interactive-stars" id="interactiveStars">
                        <input type="hidden" name="rating" id="ratingValue" value="5" required>
                        <button type="button" class="star-btn active" data-rating="1" title="1 Bintang"><i class="fa-solid fa-star"></i></button>
                        <button type="button" class="star-btn active" data-rating="2" title="2 Bintang"><i class="fa-solid fa-star"></i></button>
                        <button type="button" class="star-btn active" data-rating="3" title="3 Bintang"><i class="fa-solid fa-star"></i></button>
                        <button type="button" class="star-btn active" data-rating="4" title="4 Bintang"><i class="fa-solid fa-star"></i></button>
                        <button type="button" class="star-btn active" data-rating="5" title="5 Bintang"><i class="fa-solid fa-star"></i></button>
                    </div>
                    <div class="star-feedback-text">
                        <span class="feedback-badge" id="feedbackBadge">5.0 / 5.0</span>
                        <span class="feedback-desc" id="feedbackDesc">Sangat Puas</span>
                    </div>
                </div>

                <!-- Pilihan Pujian Cepat (Tanpa Ikon Lain) -->
                <div class="rating-chips-box">
                    <label class="rating-chips-label">Pujian cepat (klik untuk menambahkan):</label>
                    <div class="rating-chips-list">
                        <button type="button" class="chip-item" onclick="appendReviewTag('Pekerjaan sangat rapi &amp; teliti')">Pekerjaan Rapi &amp; Teliti</button>
                        <button type="button" class="chip-item" onclick="appendReviewTag('Datang tepat waktu')">Datang Tepat Waktu</button>
                        <button type="button" class="chip-item" onclick="appendReviewTag('Teknisi ramah dan komunikatif')">Ramah &amp; Komunikatif</button>
                        <button type="button" class="chip-item" onclick="appendReviewTag('Pengerjaan cepat &amp; solutif')">Pengerjaan Cepat</button>
                        <button type="button" class="chip-item" onclick="appendReviewTag('Sangat profesional')">Sangat Profesional</button>
                    </div>
                </div>

                <!-- Kolom Ulasan Teks -->
                <div class="rating-textarea-box">
                    <label class="rating-textarea-label" for="ratingComment">Ulasan Anda (Opsional):</label>
                    <textarea name="ulasan" id="ratingComment" class="rating-textarea" rows="3" maxlength="2000" placeholder="Ceritakan pengalaman Anda mengenai hasil perbaikan, keahlian teknisi, atau saran lainnya..." oninput="updateReviewCharCount()"></textarea>
                    <div class="textarea-counter"><span id="reviewCharCount">0</span>/2000 karakter</div>
                </div>

                <!-- Tombol Aksi (Tanpa Ikon Lain) -->
                <div class="rating-modal-actions">
                    <button type="button" class="btn-rating-cancel" data-close="rating">Batal</button>
                    <button type="submit" class="btn-rating-submit">Kirim Ulasan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Popup Notifikasi Pembayaran - PERBAIKAN TAMPILAN SAJA -->
    <div id="payNotify" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:16px; padding:24px 22px; box-shadow:0 8px 32px rgba(0,0,0,0.12); text-align:center; max-width:360px; margin:auto;">
        <i class="fa-solid fa-bell" style="font-size:2.2rem; color:#ff9800; margin-bottom:12px;"></i>
        <h3 style="margin:0 0 8px;">Konfirmasi Admin</h3>
        <p id="payNotifyText" style="margin:0 0 14px; color:#333"></p>
        <div style="display:flex; gap:8px; justify-content:center;">
          <button id="payNotifyClose" style="background:#eee; color:#333; border:none; border-radius:8px; padding:8px 14px; font-weight:600;">Nanti</button>
          <a id="payNotifyLink" href="#" style="background:#1856a7; color:#fff; border:none; border-radius:8px; padding:8px 14px; font-weight:600; text-decoration:none;">Bayar Sekarang</a>
        </div>
      </div>
    </div>
    
    <script>
    // Toggle Sidebar - TANPA PERUBAHAN FUNGSI
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('main').classList.toggle('shift');
    });
    
    // Modal Logout - TANPA PERUBAHAN FUNGSI
    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }
    
    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
    
    function confirmLogout() {
        window.location.href = "/logout";
    }
    
    // Notifikasi - PERBAIKAN FUNGSI
    (function(){
        const bell = document.getElementById('notifBell');
        const dropdown = document.getElementById('notifDropdown');
        const list = document.getElementById('notifList');
        const bookings = @json($requests);
        function fmtTanggal(iso){
            try{ return new Date(iso).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }); }catch(e){ return ''; }
        }
        function fmtRupiah(num){ try{ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(Number(num||0)); }catch(e){ return 'Rp0'; } }
        const rows = [];
        bookings.forEach(b => {
            // Semua metode pembayaran, termasuk COD, harus lunas sebelum pekerjaan dimulai.
            if (b.status_admin === 'diterima' && b.status === 'dikonfirmasi' && b.jumlah_pembayaran && Number(b.jumlah_pembayaran) > 0 && Number(b.bayar_sekarang) !== 1) {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-credit-card"></i></span>
                  <div>
                    <div>Booking <b>${b.layanan}</b> dikonfirmasi. Selesaikan pembayaran${b.metode_pembayaran === 'COD' ? ' tunai' : ''} sebelum admin memulai pekerjaan. Total: <b>${fmtRupiah(b.jumlah_pembayaran)}</b></div>
                    <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
                    <div style="margin-top:8px;"><a href="{{ url('/pembayaran') }}?booking=${encodeURIComponent(b.kode_booking)}" class="btn-detail" style="text-decoration:none; font-size:13px; padding:4px 10px;">Bayar Sekarang</a></div>
                  </div>
                </div>`);
            }
            // Ditolak
            if (b.status_admin === 'ditolak') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                  <div>
                    <div>Booking <b>${b.layanan}</b> ditolak.</div>
                    <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
                  </div>
                </div>`);
            }
            // Dibatalkan (oleh user)
            if (b.status_admin === 'dibatalkan') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-ban"></i></span>
                  <div>
                    <div>Booking <b>${b.layanan}</b> dibatalkan.</div>
                    <div class="notif-time">${fmtTanggal(b.updated_at || b.created_at)}</div>
                  </div>
                </div>`);
            }
            if (b.status === 'menunggu_persetujuan') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                  <div>
                    <div>Penawaran untuk <b>${b.layanan}</b> sudah tersedia. Silakan periksa rincian dan berikan keputusan.</div>
                    <div class="notif-time">${fmtTanggal(b.penawaran_dikirim_at || b.updated_at)}</div>
                  </div>
                </div>`);
            } else if (b.status === 'persetujuan_dikirim') {
                rows.push(`
                <div class="notif-item">
                  <span class="notif-icon"><i class="fa-solid fa-hourglass-half"></i></span>
                  <div>
                    <div>Persetujuan penawaran <b>${b.layanan}</b> sedang diverifikasi admin.</div>
                    <div class="notif-time">${fmtTanggal(b.persetujuan_user_at || b.updated_at)}</div>
                  </div>
                </div>`);
            } else if (b.status === 'menunggu') {
                rows.push(`
              <div class="notif-item">
                <span class="notif-icon"><i class="fa-regular fa-clock"></i></span>
                <div>
                  <div>Request <b>${b.layanan}</b> menunggu pemeriksaan admin.</div>
                  <div class="notif-time">${fmtTanggal(b.created_at)}</div>
                </div>
              </div>`);
            }
        });
        list.innerHTML = rows.length ? rows.join('') : '<div class="notif-item">Tidak ada notifikasi.</div>';
        if (bell && dropdown) {
            bell.addEventListener('click', function(e){
                e.stopPropagation();
                dropdown.classList.toggle('open');
            });
            document.addEventListener('click', function(e){
                if (!dropdown.contains(e.target) && e.target !== bell) {
                    dropdown.classList.remove('open');
                }
            });
            document.getElementById('notifCloseX') && document.getElementById('notifCloseX').addEventListener('click', function(){
                dropdown.classList.remove('open');
            });
        }
    })();
    
    // search filter
    document.getElementById('searchBookingUser').addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.booking-card.js-booking').forEach(function(card) {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
    
    // modal detail
    document.querySelectorAll('.btn-detail').forEach(btn => {
      if (btn.closest('form')) return;
        btn.addEventListener('click', function() {
            const card = this.closest('.booking-card');
            document.getElementById('dJudul').textContent = card.dataset.judul;
            document.getElementById('dKode').textContent = "Kode: " + card.dataset.kode;
            document.getElementById('dStatus').textContent = card.dataset.status;
            const formatMoney = value => value !== '' && value !== null && Number.isFinite(Number(value))
                ? `Rp ${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(Number(value)))}`
                : (card.dataset.statusHarga === 'Menunggu estimasi' ? 'Menunggu estimasi' : 'Tidak tercatat');
            document.getElementById('dHarga').textContent = formatMoney(card.dataset.harga);
            document.getElementById('dHargaTotal').textContent = formatMoney(card.dataset.totalPenawaran);
            document.getElementById('dPembayaran').textContent = card.dataset.pembayaran;
            document.getElementById('dPembayaranDetail').textContent = card.dataset.pembayaran;
            document.getElementById('dJadwal').textContent = card.dataset.tanggal;
            document.getElementById('dTukang').textContent = card.dataset.tukang;
            document.getElementById('dLokasi').textContent = card.dataset.lokasi;
            document.getElementById('dDesk').textContent = card.dataset.desk;
            document.getElementById('dUrgensi').textContent = card.dataset.urgensi || '-';
            document.getElementById('dKerusakan').textContent = card.dataset.kerusakan || '-';
            document.getElementById('dKompleksitas').textContent = card.dataset.kompleksitas || '-';
            const distance = card.dataset.jarak;
            const serviceDuration = card.dataset.durasiLayanan;
            const distanceLabel = distance
                ? `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(Number(distance))} km (${card.dataset.jarakLabel})`
                : 'Dihitung saat admin mengirim penawaran';
            document.getElementById('dJarak').textContent = serviceDuration
                ? `${distanceLabel} · durasi layanan ${serviceDuration} menit`
                : distanceLabel;
            document.getElementById('dHargaStatus').textContent = card.dataset.statusHarga;
            document.getElementById('dHargaDasar').textContent = formatMoney(card.dataset.hargaDasar);
            const hasDetailedQualification = card.dataset.biayaRating !== '' || card.dataset.biayaPengalaman !== '';
            document.getElementById('dRowLegacyQualification').style.display = hasDetailedQualification ? 'none' : '';
            document.getElementById('dRowRating').style.display = hasDetailedQualification ? '' : 'none';
            document.getElementById('dRowExperience').style.display = hasDetailedQualification ? '' : 'none';
            document.getElementById('dRowWorkerFee').style.display = hasDetailedQualification ? '' : 'none';
            document.getElementById('dBiayaKualifikasi').textContent = formatMoney(card.dataset.biayaKualifikasi);
            document.getElementById('dBiayaRating').textContent = card.dataset.skorRating || '-';
            document.getElementById('dBiayaPengalaman').textContent = card.dataset.skorPengalaman || '-';
            document.getElementById('dBiayaTukang').textContent = formatMoney(card.dataset.biayaTukang);
            document.getElementById('dRatingLabel').textContent = `Skor rating (${card.dataset.rating || '-'} bintang × 0,25)`;
            document.getElementById('dExperienceLabel').textContent = `Skor pengalaman (${card.dataset.pengalaman || '-'} thn × 0,25)`;
            document.getElementById('dWorkerFeeLabel').textContent = `Biaya tukang (${Number(card.dataset.skorRating || 0) + Number(card.dataset.skorPengalaman || 0)} × ${formatMoney(card.dataset.hargaDasar)})`;
            document.getElementById('dBiayaAdditional').textContent = formatMoney(card.dataset.biayaAdditional);
            document.getElementById('dRowAdditional').style.display = Number(card.dataset.biayaAdditional || 0) > 0 ? '' : 'none';
            document.getElementById('dBiayaUrgensi').textContent = formatMoney(card.dataset.biayaUrgensi);
            document.getElementById('dBiayaKerusakan').textContent = formatMoney(card.dataset.biayaKerusakan);
            document.getElementById('dBiayaJasa').textContent = formatMoney(card.dataset.biayaJasa);
            document.getElementById('dBiayaMaterial').textContent = formatMoney(card.dataset.biayaMaterial);
            document.getElementById('dBiayaTransportasi').textContent = formatMoney(card.dataset.biayaTransportasi);
            document.getElementById('dHargaNote').textContent = card.dataset.statusHarga === 'Menunggu estimasi'
                ? 'Estimasi harga akan muncul setelah rincian booking tersedia.'
                : (card.dataset.statusHarga.startsWith('Estimasi awal')
                    ? 'Estimasi langsung dari isian form. Biaya material/suku cadang belum termasuk; admin dapat mengirim harga final setelah pemeriksaan.'
                    : 'Rincian mengikuti penawaran tukang. Material dan transportasi ditampilkan terpisah.');
            // Buka Modal Detail
            const modal = document.getElementById('modalDetail');
            if (modal) modal.classList.add('open');
        });
    });
    
    // Tutup Modal Detail via Tombol Tutup / Cross
    document.querySelectorAll('[data-close="detail"]').forEach(el => {
        el.addEventListener('click', () => {
            const modal = document.getElementById('modalDetail');
            if (modal) modal.classList.remove('open');
        });
    });
    
    // Tutup Modal Detail jika klik area luar (Backdrop)
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalDetail');
        if (modal && e.target.classList.contains('backdrop')) {
            modal.classList.remove('open');
        }
    });
    
    // Notifikasi pembayaran untuk semua booking yang sudah dikonfirmasi admin.
    (function(){
        try {
            const bookings = @json($requests);
            const b = bookings.find(x => x.status_admin === 'diterima' && x.status === 'dikonfirmasi' && x.jumlah_pembayaran && Number(x.jumlah_pembayaran) > 0 && Number(x.bayar_sekarang) !== 1);
            if (b) {
                const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(b.jumlah_pembayaran);
                const paymentMethodNote = b.metode_pembayaran === 'COD' ? ' Pembayaran tunai juga harus diselesaikan sebelum pekerjaan dimulai.' : '';
                const text = `Request Anda (${b.layanan}) telah dikonfirmasi admin. Total yang harus dibayar: ${rupiah}.${paymentMethodNote}`;
                document.getElementById('payNotifyText').textContent = text;
                // arahkan ke halaman pembayaran detail atau form create dengan kode booking
                const link = document.getElementById('payNotifyLink');
                // Arahkan ke halaman create pembayaran dengan kode booking
                link.href = `{{ url('/pembayaran') }}?booking=${encodeURIComponent(b.kode_booking)}`;
                const modal = document.getElementById('payNotify');
                modal.style.display = 'flex';
                document.getElementById('payNotifyClose').onclick = function(){ modal.style.display = 'none'; };
            }
        } catch(e) { /* ignore */ }
    })();

    // ==========================================
    // LOGIKA MODAL RATING & ULASAN TEKNISI
    // ==========================================
    const starDescriptions = {
        1: { badge: '1.0 / 5.0', desc: 'Sangat Kecewa' },
        2: { badge: '2.0 / 5.0', desc: 'Kurang Puas' },
        3: { badge: '3.0 / 5.0', desc: 'Cukup Baik' },
        4: { badge: '4.0 / 5.0', desc: 'Puas' },
        5: { badge: '5.0 / 5.0', desc: 'Sangat Puas' }
    };

    function setStarRating(rating) {
        const r = Math.max(1, Math.min(5, parseInt(rating, 10) || 5));
        const ratingInput = document.getElementById('ratingValue');
        if (ratingInput) ratingInput.value = r;

        const starBtns = document.querySelectorAll('#interactiveStars .star-btn');
        starBtns.forEach(btn => {
            const btnVal = parseInt(btn.dataset.rating, 10);
            if (btnVal <= r) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        const info = starDescriptions[r] || starDescriptions[5];
        const badgeEl = document.getElementById('feedbackBadge');
        const descEl = document.getElementById('feedbackDesc');
        if (badgeEl) badgeEl.textContent = info.badge;
        if (descEl) descEl.textContent = info.desc;
    }

    function openRatingModal(requestId, workerName, serviceTitle, bookingCode) {
        const modal = document.getElementById('modalRating');
        const form = document.getElementById('ratingForm');
        if (!modal || !form) return;

        form.action = `/booking-user/${requestId}/ulasan`;
        document.getElementById('ratingWorkerName').textContent = workerName || 'Teknisi TEKSIKA';
        document.getElementById('ratingServiceTitle').textContent = serviceTitle || 'Layanan';
        document.getElementById('ratingBookingCode').textContent = bookingCode || '';

        // Reset star to 5 by default
        setStarRating(5);
        const commentEl = document.getElementById('ratingComment');
        if (commentEl) commentEl.value = '';
        updateReviewCharCount();

        modal.classList.add('open');
    }

    function closeRatingModal() {
        const modal = document.getElementById('modalRating');
        if (modal) modal.classList.remove('open');
    }

    document.querySelectorAll('[data-close="rating"]').forEach(el => {
        el.addEventListener('click', closeRatingModal);
    });

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalRating');
        if (modal && e.target === modal) {
            modal.classList.remove('open');
        }
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modalRating = document.getElementById('modalRating');
            if (modalRating && modalRating.classList.contains('open')) {
                modalRating.classList.remove('open');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const starBtns = document.querySelectorAll('#interactiveStars .star-btn');
        starBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                setStarRating(this.dataset.rating);
            });

            btn.addEventListener('mouseenter', function() {
                const hoverVal = parseInt(this.dataset.rating, 10);
                starBtns.forEach(b => {
                    if (parseInt(b.dataset.rating, 10) <= hoverVal) {
                        b.classList.add('hovered');
                    } else {
                        b.classList.remove('hovered');
                    }
                });
                const info = starDescriptions[hoverVal];
                if (info) {
                    const badgeEl = document.getElementById('feedbackBadge');
                    const descEl = document.getElementById('feedbackDesc');
                    if (badgeEl) badgeEl.textContent = info.badge;
                    if (descEl) descEl.textContent = info.desc;
                }
            });

            btn.addEventListener('mouseleave', function() {
                starBtns.forEach(b => b.classList.remove('hovered'));
                const currentRating = document.getElementById('ratingValue') ? document.getElementById('ratingValue').value : 5;
                setStarRating(currentRating);
            });
        });
    });

    function appendReviewTag(tagText) {
        const commentEl = document.getElementById('ratingComment');
        if (!commentEl) return;
        const currentText = commentEl.value.trim();
        if (currentText.includes(tagText)) return;
        if (currentText === '') {
            commentEl.value = tagText + '. ';
        } else {
            commentEl.value = currentText + ', ' + tagText.toLowerCase() + '. ';
        }
        updateReviewCharCount();
        commentEl.focus();
    }

    function updateReviewCharCount() {
        const commentEl = document.getElementById('ratingComment');
        const counterEl = document.getElementById('reviewCharCount');
        if (commentEl && counterEl) {
            counterEl.textContent = commentEl.value.length;
        }
    }
    </script>
    
    @if(session('success'))
    <div id="popupSuccess" style="position:fixed;top:30px;left:50%;transform:translateX(-50%);background:#4caf50;color:#fff;padding:16px 32px;border-radius:8px;z-index:9999;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
        {{ session('success') }}
    </div>
    <script>
    setTimeout(function() {
        document.getElementById('popupSuccess').style.display = 'none';
    }, 2500);
    </script>
    @endif

    @include('user._profile_modal')
</body>
</html>