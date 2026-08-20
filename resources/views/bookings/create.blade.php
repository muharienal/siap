@extends('templates.template')

@section('title', 'Ajukan Peminjaman Ruangan')
@section('page_title', 'Peminjaman Ruangan')

@php
    // FIX: sebelumnya closure PHP multi-statement dipasang LANGSUNG di
    // dalam @json(...) — parser directive Blade ternyata tidak bisa
    // menangani ekspresi sekompleks itu dengan benar (memotong argumen
    // di tengah jalan), hasilnya file PHP terkompilasi jadi rusak
    // sintaksnya (ParseError "Unclosed '['"). Dihitung dulu di sini
    // sebagai variabel biasa, baru di-@json()-kan sebagai satu variabel
    // tunggal — jauh lebih aman untuk Blade.
    $roomsLookupData = $rooms->mapWithKeys(function ($r) {
        $photos = $r->photos ?? collect();
        return [$r->id => [
            'name' => $r->name,
            'location' => $r->location,
            'capacity' => $r->capacity,
            'photo' => $photos->count() > 0 ? $photos->first()->photo_url : null,
        ]];
    });
@endphp

@section('content')
<style>
    /* ============================================
       LAYOUT
       Padding kustom dihapus total. Container layout
       (templates.template) sudah menyediakan padding-nya
       sendiri — menambahkan padding lagi di sini membuat
       konten "bergeser" masuk dobel dibanding halaman
       Manajemen Ruang Rapat (referensi), baik secara
       vertikal maupun horizontal.

       CATATAN: Jika header masih terlihat turun setelah
       perubahan ini, penyebabnya ada di file layout
       (templates.template), bukan di file ini — karena file
       ini sudah 0 (nol) padding tambahan di titik manapun
       sebelum konten pertama (.db-toolbar).
    ============================================ */
    .form-content {
        width: 100%;
    }

    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
        gap: var(--space-4);
        margin-bottom: var(--space-4);
    }

    .btn-back {
        height: 44px; padding: 0 var(--space-5);
        border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; gap: 8px;
        text-decoration: none; cursor: pointer; transition: all 0.2s;
        white-space: nowrap;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    /* ============================================
       FORM CARD — 1 kartu besar saja (tidak dipecah),
       dipisahkan antar bagian dengan garis 1px seperti versi awal.
    ============================================ */
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1px;
        background-color: var(--border-color-light);
    }
    .form-section { background: var(--bg-card); padding: var(--space-5); }
    .full-width { grid-column: 1 / -1; }

    .section-title {
        font-size: 1.05rem; font-weight: 700;
        color: var(--text-primary); margin-bottom: var(--space-4);
        display: flex; align-items: center; gap: 8px;
        padding-bottom: var(--space-3);
        border-bottom: 1px solid var(--border-color-light);
        text-transform: none !important;
    }
    .section-title i { color: var(--brand-orange); font-size: 1.2rem; }

    .form-group { margin-bottom: var(--space-4); }
    .form-group:last-child { margin-bottom: 0; }
    .form-label {
        font-weight: 700; font-size: 0.8rem;
        color: var(--text-secondary); margin-bottom: 6px;
        display: block; text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .form-label .required { color: #ef4444; }

    .modern-input, .form-select {
        width: 100%; height: 44px; padding: 0 14px;
        border: 1px solid var(--border-color); border-radius: var(--radius-sm);
        background: var(--bg-input); font-size: 0.9rem; font-weight: 400; font-family: inherit;
        color: var(--text-primary);
        transition: all 0.2s; outline: none;
    }
    .modern-input:focus, .form-select:focus {
        border-color: var(--brand-orange); background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }
    textarea.modern-input { height: auto; padding: 10px 14px; font-weight: 400; }

    /* ============================================
       SELECT — dibuat custom (bukan panah bawaan browser)
       supaya konsisten & jelas terlihat sebagai dropdown,
       serta font-nya ikut font aplikasi (bukan font sistem
       default milik browser).
    ============================================ */
    .select-wrapper { position: relative; }
    .select-wrapper .form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 40px;
        cursor: pointer;
    }
    .select-wrapper::after {
        content: '';
        position: absolute;
        top: 50%; right: 16px;
        width: 10px; height: 10px;
        border-right: 2px solid var(--text-muted);
        border-bottom: 2px solid var(--text-muted);
        transform: translateY(-70%) rotate(45deg);
        pointer-events: none;
        transition: border-color 0.2s;
    }
    .select-wrapper:focus-within::after { border-color: var(--brand-orange); }
    .select-wrapper .form-select option { font-weight: 400; color: var(--text-primary); }

    .input-readonly {
        display: flex; align-items: center; gap: 8px;
        background: var(--bg-body); cursor: not-allowed;
        color: var(--text-secondary); font-weight: 400;
    }

    /* ============================================
       ROOM CARD — diperkecil, disamakan dengan
       ukuran kartu di halaman Manajemen Ruang Rapat
    ============================================ */
    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: var(--space-4);
    }
    .room-card {
        background: var(--bg-card); border: 2px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        cursor: pointer;
        transition: all var(--transition-fast); display: flex; flex-direction: column;
        text-decoration: none; color: inherit; position: relative;
    }
    .room-card:hover {
        transform: translateY(-3px); box-shadow: var(--shadow-hover);
        border-color: var(--brand-orange);
    }
    .room-card.selected {
        border-color: var(--brand-green);
        background: rgba(34, 197, 94, 0.03);
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1), var(--shadow-hover);
    }
    .room-card input[type="radio"] {
        position: absolute; opacity: 0; pointer-events: none;
    }

    /* ============================================================
       BADGE CENTANG — muncul di pojok kanan-atas foto ruangan saat
       kartu dipilih. Lingkaran hijau solid dengan ring putih tipis
       (kontras jelas di atas foto apa pun), plus overlay gradasi
       halus di foto supaya badge-nya tidak "mengambang" tanpa
       konteks. Animasi pop-in singkat memberi umpan balik yang jelas
       tanpa terasa berlebihan.
       ============================================================ */
    .room-card-check {
        position: absolute; top: 10px; right: 10px; z-index: 3;
        width: 30px; height: 30px; border-radius: 50%;
        background: var(--brand-green); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem; box-shadow: 0 0 0 3px rgba(255,255,255,0.9), 0 4px 10px rgba(0,0,0,0.2);
        opacity: 0; transform: scale(0.4);
        transition: opacity 0.22s ease, transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }
    .room-card.selected .room-card-check { opacity: 1; transform: scale(1); }

    .room-card-image::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.28) 0%, transparent 35%);
        opacity: 0; transition: opacity 0.22s ease; pointer-events: none;
    }
    .room-card.selected .room-card-image::after { opacity: 1; }
    .room-card-image {
        width: 100%; height: 150px; background: var(--bg-input);
        position: relative; overflow: hidden;
        border-top-left-radius: calc(var(--radius-card) - 2px);
        border-top-right-radius: calc(var(--radius-card) - 2px);
    }
    .room-card-image img {
        width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;
    }
    .room-card:hover .room-card-image img { transform: scale(1.05); }
    .room-card-image-placeholder {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); font-size: 2.2rem; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    }

    .room-card-body {
        padding: var(--space-3) var(--space-4); display: flex; flex-direction: column; gap: 6px; flex: 1;
    }
    .room-card-title { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.3; }
    .room-card-meta {
        display: flex; align-items: center; gap: var(--space-3);
        font-size: 0.78rem; color: var(--text-secondary); flex-wrap: wrap;
    }
    .room-card-meta i { color: var(--text-muted); margin-right: 4px; }

    /* Facility Badge with Tooltip */
    .facility-badge-wrapper {
        position: relative;
        display: inline-block;
        margin-top: 4px;
        align-self: flex-start;
    }
    .facility-badge {
        padding: 4px 10px; border-radius: var(--radius-pill);
        font-size: 11px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
        cursor: default;
    }
    .facility-badge.has-facilities {
        background: rgba(59, 130, 246, 0.08); color: var(--brand-blue);
    }
    .facility-badge.no-facilities {
        background: rgba(148, 163, 184, 0.1); color: var(--text-muted);
    }

    .facility-tooltip {
        position: absolute;
        bottom: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%) translateY(5px);
        background: #1e293b;
        color: white;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 500;
        min-width: 180px;
        max-width: 250px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s ease;
        z-index: 1000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        pointer-events: none;
        text-align: left;
    }
    .facility-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1e293b;
    }
    .facility-tooltip ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .facility-tooltip li {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: normal;
        line-height: 1.3;
    }
    .facility-tooltip li::before {
        content: "•";
        color: var(--brand-orange);
        font-weight: bold;
    }
    .facility-badge-wrapper:hover .facility-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    /* Availability Status */
    .availability-status {
        display: none; padding: var(--space-3); border-radius: var(--radius-sm);
        margin-top: var(--space-2); font-size: 0.85rem; font-weight: 400;
        align-items: center; gap: 8px;
    }
    .availability-status.available { display: flex; background: rgba(16,185,129,0.1); color: #047857; }
    .availability-status.unavailable { display: flex; background: rgba(239,68,68,0.1); color: #b91c1c; }
    .availability-status.checking { display: flex; background: rgba(59,130,246,0.1); color: #1d4ed8; }
    .availability-status.idle { display: flex; background: rgba(148,163,184,0.12); color: var(--text-muted); }
    .availability-status.priority-notice { display: flex; background: rgba(249,115,22,0.1); color: var(--brand-orange-dark); }

    /* Additional Facilities (Custom Options) — sengaja dibuat POLOS
       (tanpa border/background/padding kotak) supaya tidak terlihat
       seperti "kartu di dalam kartu"; cukup pemisah jarak antar opsi. */
    .additional-option {
        display: flex; flex-direction: column; gap: 10px;
        margin-bottom: var(--space-4);
    }
    .additional-option:last-child { margin-bottom: 0; }
    .form-check-custom {
        display: flex; align-items: center; gap: 10px;
        font-weight: 600; color: var(--text-primary); font-size: 0.9rem;
        cursor: pointer;
    }
    .form-check-custom input { width: 18px; height: 18px; accent-color: var(--brand-orange); cursor: pointer; }
    .additional-input { width: 100%; height: 40px; padding: 0 12px; border: 1px solid var(--border-color); border-radius: var(--radius-xs); background: var(--bg-input); font-size: 0.85rem; font-family: inherit; outline: none; transition: all 0.2s; }
    .additional-input:focus { border-color: var(--brand-orange); background: var(--bg-card); box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
    .additional-input:disabled { background: var(--bg-body); }
    textarea.additional-input { height: auto; padding: 8px 12px; resize: vertical; }

    .form-actions { background: var(--bg-body); display: flex; gap: var(--space-3); justify-content: flex-end; padding: var(--space-5); }
    /* Tombol aksi utama disamakan dengan palet warna btn-modal (hijau =
       aksi konfirmasi/positif) yang sudah didefinisikan di halaman
       Detail Peminjaman — bukan gradasi oranye-hijau seperti sebelumnya,
       supaya makna warnanya konsisten di seluruh aplikasi. */
    .btn-submit { height: 44px; padding: 0 var(--space-6); background: var(--brand-green); border: none; border-radius: var(--radius-sm); font-weight: 600; font-size: 0.9rem; color: white; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(34,197,94,0.2); }
    .btn-submit:hover:not(:disabled) { background: var(--brand-green-dark); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(34,197,94,0.25); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .btn-cancel { height: 44px; padding: 0 var(--space-5); background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600; font-size: 0.9rem; color: var(--text-secondary); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
    .btn-cancel:hover { border-color: var(--text-muted); color: var(--text-primary); }

    /* MATERIAL TIME PICKER STYLES (tetap seperti punya Anda, tidak diubah) */
    .time-trigger {
        height: 44px; padding: 0 var(--space-4);
        display: inline-flex; align-items: center; gap: var(--space-2);
        background: var(--bg-input); border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); font-size: 0.9rem;
        font-weight: 400; font-family: inherit; color: var(--text-primary); cursor: pointer;
        white-space: nowrap; transition: all 0.2s; width: 100%;
    }
    .time-trigger:hover { border-color: var(--brand-orange); background: var(--bg-card); }
    .time-trigger i.bi-clock { color: var(--brand-orange); }
    .time-trigger .chevron { color: var(--text-muted); font-size: 0.65rem; margin-left: 2px; }
    .time-trigger .time-placeholder { color: var(--text-muted); font-weight: 500; }

    .mtp-overlay { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.4); animation: mtpFadeIn 0.18s ease; padding: 16px; }
    .mtp-overlay.open { display: flex; }
    .mtp-dialog { background: var(--bg-card); border-radius: 20px; box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28); width: 420px; max-width: 100%; max-height: 92vh; overflow-y: auto; animation: mtpSlideUp 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes mtpFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes mtpSlideUp { from { opacity: 0; transform: translateY(24px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .mtp-header { padding: 24px 28px 8px; }
    .mtp-title { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-muted); }
    .mtp-subtitle { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
    .mtp-time-display { display: flex; justify-content: center; gap: 28px; padding: 16px 28px 12px; }
    .mtp-field { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; padding: 10px 16px; border-radius: 14px; transition: background 0.2s; }
    .mtp-field:hover { background: var(--bg-hover); }
    .mtp-field.active .mtp-digital { color: var(--brand-orange); }
    .mtp-field.active .mtp-field-label { color: var(--brand-orange-dark); }
    .mtp-field-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
    .mtp-digital { font-size: 44px; font-weight: 400; color: var(--text-primary); line-height: 1; display: flex; align-items: baseline; font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }
    .mtp-digital .mtp-colon { opacity: 0.45; padding: 0 2px; }
    .mtp-digital .mtp-unit { cursor: pointer; padding: 0 4px; border-radius: 8px; transition: background 0.2s; }
    .mtp-digital .mtp-unit:hover { background: rgba(249, 115, 22, 0.08); }
    .mtp-digital .mtp-unit.mode-active { background: rgba(249, 115, 22, 0.14); color: var(--brand-orange); }
    .mtp-error { display: none; align-items: center; gap: 6px; font-size: 12px; color: #b91c1c; background: rgba(239, 68, 68, 0.08); border-radius: var(--radius-xs); padding: 8px 14px; margin: 4px 28px 0; }
    .mtp-error.show { display: flex; }
    .mtp-clock-wrap { display: flex; flex-direction: column; align-items: center; padding: 20px 28px 12px; }
    .mtp-clock { position: relative; width: 280px; height: 280px; max-width: 80vw; max-height: 80vw; border-radius: 50%; background: var(--bg-body); user-select: none; flex-shrink: 0; margin: 0 auto; }
    .mtp-num { position: absolute; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 16px; font-weight: 500; color: var(--text-primary); cursor: pointer; transform: translate(-50%, -50%); transition: background 0.2s, color 0.2s, transform 0.2s; background: transparent; z-index: 2; }
    .mtp-num:hover:not(.disabled) { background: rgba(249, 115, 22, 0.12); }
    .mtp-num:active:not(.disabled) { transform: translate(-50%, -50%) scale(0.95); }
    .mtp-num.selected { background: var(--brand-orange); color: #fff; font-weight: 700; transform: translate(-50%, -50%) scale(1.05); }
    .mtp-num.disabled { color: var(--text-muted); opacity: 0.3; cursor: not-allowed; pointer-events: none; }
    .mtp-hand-svg { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
    .mtp-hand { stroke: var(--brand-orange); stroke-width: 2; stroke-linecap: round; transition: x2 0.2s ease, y2 0.2s ease; vector-effect: non-scaling-stroke; }
    .mtp-center-dot { position: absolute; left: 50%; top: 50%; width: 12px; height: 12px; background: var(--brand-orange); border-radius: 50%; transform: translate(-50%, -50%); pointer-events: none; z-index: 3; }
    .mtp-footer { display: flex; justify-content: flex-end; align-items: center; padding: 16px 20px 20px; }
    .mtp-actions { display: flex; gap: 8px; }
    .mtp-btn { height: 40px; padding: 0 20px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
    .mtp-btn-cancel { background: transparent; color: var(--text-secondary); }
    .mtp-btn-cancel:hover { background: var(--bg-hover); }
    .mtp-btn-ok { background: var(--brand-orange); color: #fff; }
    .mtp-btn-ok:hover { background: var(--brand-orange-dark); }

    /* TOAST NOTIFICATION */
    .toast-container { position: fixed; top: 20px; right: 20px; z-index: 10000; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
    .toast-notification { min-width: 320px; max-width: 400px; background: var(--bg-card); border-radius: var(--radius-sm); box-shadow: 0 10px 30px rgba(0,0,0,0.15); padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px; pointer-events: auto; animation: toastSlideIn 0.3s ease forwards; border-left: 4px solid; }
    .toast-notification.success { border-left-color: var(--brand-green); }
    .toast-notification.error { border-left-color: #ef4444; }
    .toast-notification.info { border-left-color: var(--brand-blue); }
    .toast-icon { font-size: 1.5rem; flex-shrink: 0; margin-top: 2px; }
    .toast-notification.success .toast-icon { color: var(--brand-green); }
    .toast-notification.error .toast-icon { color: #ef4444; }
    .toast-notification.info .toast-icon { color: var(--brand-blue); }
    .toast-content { flex: 1; }
    .toast-title { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; text-transform: none !important; }
    .toast-message { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4; }
    .toast-close { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1rem; padding: 0; margin-top: 2px; transition: color 0.2s; }
    .toast-close:hover { color: var(--text-primary); }
    @keyframes toastSlideIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
    @keyframes toastSlideOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }

    @media (max-width: 991.98px) {
        .form-card { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column-reverse; }
        .form-actions button, .form-actions a { width: 100%; justify-content: center; }
    }
    @media (max-width: 575.98px) {
        .mtp-dialog { width: calc(100vw - 32px); }
        .mtp-header { padding: 20px 20px 4px; }
        .mtp-time-display { gap: 16px; padding: 8px 20px 4px; }
        .mtp-digital { font-size: 36px; }
        .mtp-field { padding: 8px 12px; }
        .mtp-clock { width: 240px; height: 240px; }
        .mtp-footer { padding: 12px 16px 16px; }
    }    /* ============================================================
       MODAL KONFIRMASI — ringkasan pengajuan sebelum benar-benar
       dikirim. Desain clean: header gradasi brand, tiap detail
       ditampilkan sebagai baris ikon+label+nilai (mudah dipindai
       cepat), fasilitas tambahan sebagai chip kecil, dan blok
       keperluan/agenda ditonjolkan sebagai kutipan tersendiri.
       ============================================================ */
    .confirm-modal .modal-content {
        border: none; border-radius: 22px; overflow: hidden;
        box-shadow: 0 30px 70px rgba(16,24,43,0.22);
    }
    .confirm-modal .modal-header {
        background: var(--bg-card); color: var(--text-primary); border-bottom: 1px solid var(--border-color-light);
        padding: 1.25rem 1.75rem; position: relative;
    }
    .confirm-modal .modal-header .modal-title {
        font-weight: 700; font-size: 1.15rem; color: var(--text-primary); display: flex; align-items: center; gap: 10px;
    }
    .confirm-modal .modal-header .modal-title i { color: var(--brand-orange); }
    .confirm-modal .modal-header .btn-close {
        opacity: 0.6; transition: opacity 0.2s;
    }
    .confirm-modal .modal-header .btn-close:hover { opacity: 1; }
    .confirm-modal .modal-body { padding: 1.75rem; max-height: 65vh; overflow-y: auto; }
    .confirm-modal .modal-footer { padding: 1.1rem 1.75rem; border-top: 1px solid var(--border-color-light); gap: 10px; }

    .confirm-room-card {
        display: flex; align-items: center; gap: 14px;
        background: var(--bg-body); border-radius: 16px; padding: 12px;
        margin-bottom: 1.25rem; border: 1px solid var(--border-color-light);
    }
    .confirm-room-card img {
        width: 64px; height: 64px; border-radius: 12px; object-fit: cover; flex-shrink: 0;
    }
    .confirm-room-card-placeholder {
        width: 64px; height: 64px; border-radius: 12px; flex-shrink: 0;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 1.4rem;
    }
    .confirm-room-name { font-weight: 700; font-size: 1.02rem; color: var(--text-primary); line-height: 1.3; }
    .confirm-room-meta { font-size: 0.8rem; color: var(--text-secondary); margin-top: 3px; display: flex; gap: 10px; flex-wrap: wrap; }

    .confirm-row {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 11px 0; border-bottom: 1px solid var(--border-color-light);
    }
    .confirm-row:last-child { border-bottom: none; }
    .confirm-row-icon {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        background: rgba(255,145,66,0.1); color: var(--brand-orange-dark);
        display: flex; align-items: center; justify-content: center; font-size: 0.95rem;
    }
    .confirm-row-label { font-size: 0.74rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted); }
    .confirm-row-value { font-size: 0.92rem; font-weight: 500; color: var(--text-primary); margin-top: 2px; line-height: 1.4; word-break: break-word; }

    .confirm-purpose-quote {
        background: var(--bg-body); border-left: 3px solid var(--brand-orange);
        border-radius: 10px; padding: 12px 14px; font-size: 0.88rem; color: var(--text-primary);
        line-height: 1.55; font-style: italic; margin-top: 2px;
    }

    .confirm-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
    .confirm-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(34,197,94,0.1); color: var(--brand-green-dark);
        font-size: 0.78rem; font-weight: 600; padding: 5px 12px; border-radius: 999px;
    }
    .confirm-empty-note { font-size: 0.85rem; color: var(--text-muted); font-style: italic; }

    .confirm-priority-flag {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e;
        font-size: 0.78rem; font-weight: 700; padding: 5px 12px; border-radius: 999px; margin-top: 2px;
    }
</style>

<div class="form-content">
    <div class="db-toolbar">
        <a href="{{ route('bookings.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FIX: form ini sebelumnya tidak menampilkan error validasi maupun
         pesan gagal-simpan sama sekali — user tidak tahu kenapa submit-nya
         gagal. Sekarang ditampilkan pakai komponen alert acuan. --}}
    @if(session('error'))
        <div class="alert-modern alert-error">
            <i class="bi bi-exclamation-circle-fill alert-modern-icon"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="alert-modern alert-error">
            <i class="bi bi-exclamation-circle-fill alert-modern-icon"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
        @csrf

        <div class="form-card">

            <div class="form-section full-width">
                <div class="section-title">
                    <i class="bi bi-door-open"></i> Pilih Ruangan
                </div>
                <div class="room-grid">
                    @foreach($rooms as $room)
                        @php
                            $photos = $room->photos ?? collect();
                            $hasPhoto = $photos->count() > 0;
                            $firstPhoto = $hasPhoto ? $photos->first()->photo_url : null;
                            $isSelected = old('room_id') == $room->id || ($prefill['room_id'] ?? '') == $room->id;
                        @endphp
                        <label class="room-card {{ $isSelected ? 'selected' : '' }}">
                            <input type="radio" name="room_id" value="{{ $room->id }}" class="room-radio" {{ $isSelected ? 'checked' : '' }} required>
                            <div class="room-card-image">
                                @if($hasPhoto)
                                    <img src="{{ $firstPhoto }}" alt="{{ $room->name }}" loading="lazy">
                                @else
                                    <div class="room-card-image-placeholder"><i class="bi bi-image"></i></div>
                                @endif
                                <span class="room-card-check"><i class="bi bi-check-lg"></i></span>
                            </div>
                            <div class="room-card-body">
                                <h3 class="room-card-title">{{ $room->name }}</h3>
                                <div class="room-card-meta">
                                    <span><i class="bi bi-geo-alt"></i>{{ $room->location }}</span>
                                    <span><i class="bi bi-people"></i>{{ $room->capacity }} orang</span>
                                </div>

                                <div class="facility-badge-wrapper">
                                    @if($room->facilities->count() > 0)
                                        <span class="facility-badge has-facilities">
                                            <i class="bi bi-tools"></i> Ada Fasilitas
                                        </span>
                                        <div class="facility-tooltip">
                                            <ul>
                                                @foreach($room->facilities as $f)
                                                    <li>{{ $f->name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="facility-badge no-facilities">
                                            <i class="bi bi-dash-circle"></i> Tanpa Fasilitas
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- KOLOM 1: WAKTU PEMINJAMAN --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-clock"></i> Waktu Peminjaman
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal <span class="required">*</span></label>
                    <input type="date" name="start_date" id="bookingDate" class="modern-input"
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('start_date', $prefill['date'] ?? date('Y-m-d')) }}" required onchange="onDateOrTimeChanged()">
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Mulai & Selesai <span class="required">*</span></label>
                    <button type="button" class="time-trigger" onclick="openBookingTimePicker()">
                        <span id="bookingTimeLabel" class="{{ (old('start_time', $prefill['start_time'] ?? '')) ? '' : 'time-placeholder' }}">
                            {{ old('start_time', $prefill['start_time'] ?? '') ? old('start_time', $prefill['start_time'] ?? '') . ' – ' . old('end_time', $prefill['end_time'] ?? '') : 'Pilih jam mulai & selesai' }}
                        </span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>
                    <input type="hidden" name="start_time" id="bookingStartTime" value="{{ old('start_time', $prefill['start_time'] ?? '') }}">
                    <input type="hidden" name="end_time" id="bookingEndTime" value="{{ old('end_time', $prefill['end_time'] ?? '') }}">
                </div>

                @php
                    // 1 = Admin (Bisa pilih), 2 = Karyawan (Fix Reguler), 3 = Sesdir (Fix Priority)
                    // PENTING: field ini sengaja TIDAK ditampilkan ke Karyawan/Sesdir
                    // sama sekali — hanya Admin yang boleh tahu ada perbedaan
                    // jenis peminjaman, supaya tidak menimbulkan kecemburuan
                    // antar pengguna. Nilainya tetap dikirim via hidden input.
                    $userRole = Auth::user()->role;
                    $isFixed = in_array($userRole, [2, 3]);
                    $fixedType = $userRole == 3 ? 'Priority' : 'Regular';
                    $currentType = old('booking_type', $isFixed ? $fixedType : 'Regular');
                @endphp
                @if($isFixed)
                    <input type="hidden" name="booking_type" value="{{ $fixedType }}">
                @else
                <div class="form-group">
                    <label class="form-label">Jenis Peminjaman</label>
                    <div class="select-wrapper">
                        <select name="booking_type" class="form-select" required onchange="checkAvailability()">
                            <option value="Regular" {{ $currentType == 'Regular' ? 'selected' : '' }}>Reguler</option>
                            <option value="Priority" {{ $currentType == 'Priority' ? 'selected' : '' }}>Priority</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="availability-status idle" id="availabilityStatus">
                    <i class="bi bi-info-circle"></i>
                    <span id="availabilityMessage">Pilih ruangan, tanggal, dan jam untuk cek ketersediaan</span>
                </div>
            </div>

            {{-- KOLOM 2: DETAIL PEMINJAMAN --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-card-text"></i> Detail Peminjaman
                </div>

                <div class="form-group">
                    <label class="form-label">Keperluan / Agenda <span class="required">*</span></label>
                    <textarea name="purpose" class="modern-input" rows="8" placeholder="Jelaskan agenda rapat atau keperluan peminjaman..." required>{{ old('purpose') }}</textarea>
                    <small class="text-muted">Minimal 10 karakter</small>
                </div>
            </div>

            {{-- KOLOM 3: FASILITAS TAMBAHAN --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-tools"></i> Fasilitas Tambahan
                </div>

                <div class="additional-option">
                    <label class="form-check-custom">
                        <input type="checkbox" name="needs_consumption" id="needsConsumption" value="1" onchange="toggleConsumption(this)" {{ old('needs_consumption') ? 'checked' : '' }}>
                        Konsumsi
                    </label>
                    <input type="number" name="consumption_qty" class="additional-input" placeholder="Jumlah Peserta" min="1" id="consumptionQty" value="{{ old('consumption_qty') }}" disabled>
                </div>

                <div class="additional-option">
                    <label class="form-check-custom">
                        <input type="checkbox" name="needs_other" id="needsOther" value="1" onchange="toggleOther(this)" {{ old('needs_other') ? 'checked' : '' }}>
                        Lain-lain (Catatan)
                    </label>
                    <textarea name="other_notes" class="additional-input" rows="4" placeholder="Tuliskan kebutuhan lain..." id="otherNotes" disabled>{{ old('other_notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions full-width">
                <button type="button" class="btn-submit" id="submitBtn" onclick="handleSubmitClick()">
                    <i class="bi bi-send"></i> Ajukan Peminjaman
                </button>
            </div>

        </div>
    </form>
</div>

<!-- MODAL KONFIRMASI PENGAJUAN -->
<div class="modal fade confirm-modal" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel"><i class="bi bi-clipboard-check"></i> Konfirmasi Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="confirm-room-card" id="confirmRoomCard">
                    <!-- diisi JS -->
                </div>

                <div class="confirm-row">
                    <div class="confirm-row-icon"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <div class="confirm-row-label">Tanggal</div>
                        <div class="confirm-row-value" id="confirmDate">-</div>
                    </div>
                </div>
                <div class="confirm-row">
                    <div class="confirm-row-icon"><i class="bi bi-clock"></i></div>
                    <div>
                        <div class="confirm-row-label">Jam</div>
                        <div class="confirm-row-value" id="confirmTime">-</div>
                    </div>
                </div>
                <div class="confirm-row" id="confirmTypeRow" style="display:none;">
                    <div class="confirm-row-icon"><i class="bi bi-tag"></i></div>
                    <div>
                        <div class="confirm-row-label">Jenis Peminjaman</div>
                        <div class="confirm-row-value" id="confirmType">-</div>
                    </div>
                </div>
                <div class="confirm-row">
                    <div class="confirm-row-icon"><i class="bi bi-card-text"></i></div>
                    <div style="min-width:0; flex:1;">
                        <div class="confirm-row-label">Keperluan / Agenda</div>
                        <div class="confirm-purpose-quote" id="confirmPurpose">-</div>
                    </div>
                </div>
                <div class="confirm-row">
                    <div class="confirm-row-icon"><i class="bi bi-tools"></i></div>
                    <div>
                        <div class="confirm-row-label">Fasilitas Tambahan</div>
                        <div class="confirm-badges" id="confirmFacilities">
                            <span class="confirm-empty-note">Tidak ada permintaan tambahan</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-submit" id="confirmSubmitBtn">
                    <i class="bi bi-send"></i> Ya, Ajukan Peminjaman
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MATERIAL TIME PICKER MODAL (tidak diubah dari punya Anda) --}}
<div class="mtp-overlay" id="mtpOverlay" onclick="if(event.target===this)closeTimePicker(true)">
    <div class="mtp-dialog" role="dialog" aria-modal="true" aria-labelledby="mtpTitle">
        <div class="mtp-header">
            <div class="mtp-title" id="mtpTitle">Pilih Jam Kerja</div>
            <div class="mtp-subtitle">Rentang 07:00 - 16:00</div>
        </div>
        <div class="mtp-time-display">
            <div class="mtp-field active" data-field="start" onclick="setActiveField('start')">
                <div class="mtp-field-label">Mulai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="mtpStartHour" onclick="event.stopPropagation();selectUnit('start', 'hour')">07</span><span class="mtp-colon">:</span><span class="mtp-unit" id="mtpStartMin" onclick="event.stopPropagation();selectUnit('start', 'minute')">00</span>
                </div>
            </div>
            <div class="mtp-field" data-field="end" onclick="setActiveField('end')">
                <div class="mtp-field-label">Selesai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="mtpEndHour" onclick="event.stopPropagation();selectUnit('end', 'hour')">08</span><span class="mtp-colon">:</span><span class="mtp-unit" id="mtpEndMin" onclick="event.stopPropagation();selectUnit('end', 'minute')">00</span>
                </div>
            </div>
        </div>
        <div class="mtp-error" id="mtpError"><i class="bi bi-exclamation-triangle"></i> <span></span></div>
        <div class="mtp-clock-wrap">
            <div class="mtp-clock" id="mtpClock">
                <svg class="mtp-hand-svg" viewBox="0 0 100 100" aria-hidden="true">
                    <line class="mtp-hand" id="mtpHand" x1="50" y1="50" x2="50" y2="15" vector-effect="non-scaling-stroke" />
                </svg>
                <div class="mtp-center-dot"></div>
            </div>
        </div>
        <div class="mtp-footer">
            <div class="mtp-actions">
                <button type="button" class="mtp-btn mtp-btn-cancel" onclick="closeTimePicker(true)">Batal</button>
                <button type="button" class="mtp-btn mtp-btn-ok" onclick="applyTimePicker()">OK</button>
            </div>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toastContainer"></div>

<script>
    // ============================================
    // TOAST NOTIFICATION SYSTEM
    // ============================================
    function showToast(type, title, message, duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;

        const iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            info: 'bi-info-circle-fill'
        };

        toast.innerHTML = `
            <i class="bi ${iconMap[type]} toast-icon"></i>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button type="button" class="toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        `;

        container.appendChild(toast);

        if (duration > 0) {
            setTimeout(() => { dismissToast(toast); }, duration);
        }
    }

    function dismissToast(toast) {
        if (!toast) return;
        toast.style.animation = 'toastSlideOut 0.3s ease forwards';
        setTimeout(() => {
            if (toast.parentElement) toast.parentElement.removeChild(toast);
        }, 300);
    }

    // ============================================
    // ROOM SELECTION
    // Memilih ruangan TIDAK langsung memicu cek ketersediaan.
    // Karena field jam masih kosong di awal, guard di
    // checkAvailability() otomatis mencegah pengecekan sampai
    // tanggal & jam benar-benar sudah dipilih.
    // ============================================
    document.querySelectorAll('.room-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.room-card').forEach(opt => opt.classList.remove('selected'));
            this.closest('.room-card').classList.add('selected');
            checkAvailability();
        });
    });

    // ============================================
    // ADDITIONAL FACILITIES TOGGLE
    // ============================================
    function toggleConsumption(checkbox) {
        document.getElementById('consumptionQty').disabled = !checkbox.checked;
        if (!checkbox.checked) document.getElementById('consumptionQty').value = '';
    }
    function toggleOther(checkbox) {
        document.getElementById('otherNotes').disabled = !checkbox.checked;
        if (!checkbox.checked) document.getElementById('otherNotes').value = '';
    }
    // Init state on load
    document.addEventListener('DOMContentLoaded', function() {
        toggleConsumption(document.getElementById('needsConsumption'));
        toggleOther(document.getElementById('needsOther'));
    });

    // ============================================
    // AVAILABILITY CHECK
    // ============================================
    let availabilityTimer;
    function checkAvailability() {
        clearTimeout(availabilityTimer);

        const roomId = document.querySelector('input[name="room_id"]:checked')?.value;
        const date = document.getElementById('bookingDate')?.value;
        const startTime = document.getElementById('bookingStartTime')?.value;
        const endTime = document.getElementById('bookingEndTime')?.value;

        const statusEl = document.getElementById('availabilityStatus');
        const msgEl = document.getElementById('availabilityMessage');

        if (!roomId || !date || !startTime || !endTime) {
            statusEl.className = 'availability-status idle';
            statusEl.querySelector('i').className = 'bi bi-info-circle';
            msgEl.textContent = 'Pilih ruangan, tanggal, dan jam untuk cek ketersediaan';
            return;
        }

        statusEl.className = 'availability-status checking';
        statusEl.querySelector('i').className = 'bi bi-arrow-clockwise';
        msgEl.textContent = 'Memeriksa ketersediaan...';

        availabilityTimer = setTimeout(() => {
            const startDateTime = `${date} ${startTime}:00`;
            const endDateTime = `${date} ${endTime}:00`;

            fetch('{{ route("bookings.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    room_id: roomId,
                    start_time: startDateTime,
                    end_time: endDateTime,
                    booking_type: document.querySelector('[name="booking_type"]')?.value || 'Regular'
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.priority_override) {
                    statusEl.className = 'availability-status priority-notice';
                    statusEl.querySelector('i').className = 'bi bi-star-fill';
                    msgEl.textContent = data.message;
                } else if (data.available) {
                    statusEl.className = 'availability-status available';
                    statusEl.querySelector('i').className = 'bi bi-check-circle-fill';
                    msgEl.textContent = data.message;
                } else {
                    statusEl.className = 'availability-status unavailable';
                    statusEl.querySelector('i').className = 'bi bi-x-circle-fill';
                    msgEl.textContent = data.message;
                }
            })
            .catch(() => {
                statusEl.className = 'availability-status unavailable';
                statusEl.querySelector('i').className = 'bi bi-exclamation-triangle';
                msgEl.textContent = 'Gagal memeriksa ketersediaan';
            });
        }, 500);
    }

    function onDateOrTimeChanged() {
        checkAvailability();
    }

    // ============================================
    // MATERIAL TIME PICKER LOGIC (tidak diubah dari punya Anda)
    // ============================================
    var MTP_WORK_START = 7;
    var MTP_WORK_END = 16;
    var mtpState = {
        open: false,
        activeField: 'start',
        mode: 'hour',
        start: { h: 7, m: 0 },
        end: { h: 8, m: 0 }
    };

    function pad2(n) { return String(n).padStart(2, '0'); }
    function minsOf(t) { return t.h * 60 + t.m; }
    function isValidOperational(t) { var m = minsOf(t); return m >= MTP_WORK_START * 60 && m <= MTP_WORK_END * 60; }
    function parseLabel(str) { var p = String(str).trim().split(':'); return { h: parseInt(p[0], 10) || 7, m: parseInt((p[1] || '0').split(/\s/)[0], 10) || 0 }; }

    function openBookingTimePicker() {
        var startVal = document.getElementById('bookingStartTime').value || '07:00';
        var endVal = document.getElementById('bookingEndTime').value || '08:00';

        mtpState.start = parseLabel(startVal);
        mtpState.end   = parseLabel(endVal);

        if (!isValidOperational(mtpState.start)) mtpState.start = { h: MTP_WORK_START, m: 0 };
        if (!isValidOperational(mtpState.end))   mtpState.end   = { h: MTP_WORK_END, m: 0 };

        mtpState.activeField = 'start';
        mtpState.mode = 'hour';
        mtpState.open = true;
        document.getElementById('mtpOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        renderClock();
        updateDigitalDisplay();
        hideMtpError();
    }

    function closeTimePicker(revert) {
        mtpState.open = false;
        document.getElementById('mtpOverlay').classList.remove('open');
        document.body.style.overflow = '';
        hideMtpError();
    }

    function setActiveField(field) {
        mtpState.activeField = field;
        mtpState.mode = 'hour';
        renderClock();
        updateDigitalDisplay();
        hideMtpError();
    }

    function selectUnit(field, mode) {
        mtpState.activeField = field;
        mtpState.mode = mode;
        renderClock();
        updateDigitalDisplay();
        hideMtpError();
    }

    function isDisabled(h, m) {
        if (mtpState.activeField === 'end') {
            var startMins = minsOf(mtpState.start);
            var currentMins = h * 60 + m;
            return currentMins <= startMins;
        }
        return false;
    }

    function selectHour(h24) {
        if (isDisabled(h24, 0) && isDisabled(h24, 30)) return;
        var t = mtpState[mtpState.activeField];
        t.h = h24;
        hideMtpError();
        mtpState.mode = 'minute';
        renderClock();
        updateDigitalDisplay();
    }

    function selectMinute(m) {
        if (isDisabled(mtpState[mtpState.activeField].h, m)) return;
        var t = mtpState[mtpState.activeField];
        t.m = m;
        hideMtpError();
        updateDigitalDisplay();

        if (mtpState.activeField === 'start') {
            mtpState.activeField = 'end';
            mtpState.mode = 'hour';

            if (minsOf(mtpState.end) <= minsOf(mtpState.start)) {
                var newEndMins = minsOf(mtpState.start) + 30;
                if (newEndMins > MTP_WORK_END * 60) newEndMins = MTP_WORK_END * 60;
                mtpState.end = { h: Math.floor(newEndMins / 60), m: newEndMins % 60 };
            }
        }

        renderClock();
        updateDigitalDisplay();
    }

    function renderClock() {
        var clock = document.getElementById('mtpClock');
        clock.querySelectorAll('.mtp-num').forEach(function (n) { n.remove(); });

        var t = mtpState[mtpState.activeField];
        var R = 38.5;
        var C = 50;

        if (mtpState.mode === 'hour') {
            var hours = [];
            for (var h = MTP_WORK_START; h <= MTP_WORK_END; h++) hours.push(h);
            var totalHours = hours.length;
            var angleStep = 360 / totalHours;
            var offset = hours.indexOf(12);

            hours.forEach(function(h, i) {
                var angle = ((i - offset) * angleStep - 90) * Math.PI / 180;
                var x = C + R * Math.cos(angle);
                var y = C + R * Math.sin(angle);
                var el = document.createElement('div');
                el.className = 'mtp-num';
                el.textContent = pad2(h);
                el.style.left = x + '%';
                el.style.top = y + '%';

                var h00Disabled = isDisabled(h, 0);
                var h30Disabled = isDisabled(h, 30);

                if (h00Disabled && h30Disabled) {
                    el.classList.add('disabled');
                } else {
                    el.addEventListener('click', function() { selectHour(h); });
                    if (t.h === h) el.classList.add('selected');
                }
                clock.appendChild(el);
            });

            var selIdx = hours.indexOf(t.h);
            if (selIdx !== -1) {
                var handAngle = ((selIdx - offset) * angleStep - 90) * Math.PI / 180;
                var handX = C + 35 * Math.cos(handAngle);
                var handY = C + 35 * Math.sin(handAngle);
                setHand(handX, handY);
            }
        } else {
            var minutes = [0, 30];
            minutes.forEach(function(m, i) {
                var angle = (i * 180 - 90) * Math.PI / 180;
                var x = C + R * Math.cos(angle);
                var y = C + R * Math.sin(angle);
                var el = document.createElement('div');
                el.className = 'mtp-num';
                el.textContent = pad2(m);
                el.style.left = x + '%';
                el.style.top = y + '%';

                if (isDisabled(t.h, m)) {
                    el.classList.add('disabled');
                } else {
                    el.addEventListener('click', function() { selectMinute(m); });
                    if (t.m === m) el.classList.add('selected');
                }
                clock.appendChild(el);
            });

            var selIdxM = minutes.indexOf(t.m);
            if (selIdxM !== -1) {
                var handAngleM = (selIdxM * 180 - 90) * Math.PI / 180;
                var handXM = C + 35 * Math.cos(handAngleM);
                var handYM = C + 35 * Math.sin(handAngleM);
                setHand(handXM, handYM);
            }
        }
    }

    function setHand(x, y) {
        var hand = document.getElementById('mtpHand');
        hand.setAttribute('x2', x);
        hand.setAttribute('y2', y);
    }

    function updateDigitalDisplay() {
        var s = mtpState.start, e = mtpState.end;
        document.getElementById('mtpStartHour').textContent = pad2(s.h);
        document.getElementById('mtpStartMin').textContent = pad2(s.m);
        document.getElementById('mtpEndHour').textContent = pad2(e.h);
        document.getElementById('mtpEndMin').textContent = pad2(e.m);

        document.querySelectorAll('.mtp-field').forEach(function (f) {
            f.classList.toggle('active', f.dataset.field === mtpState.activeField);
        });

        var prefix = mtpState.activeField === 'start' ? 'mtpStart' : 'mtpEnd';
        var hourEl = document.getElementById(prefix + 'Hour');
        var minEl = document.getElementById(prefix + 'Min');
        document.querySelectorAll('.mtp-digital .mtp-unit').forEach(function (u) { u.classList.remove('mode-active'); });
        if (mtpState.mode === 'hour' && hourEl) hourEl.classList.add('mode-active');
        if (mtpState.mode === 'minute' && minEl) minEl.classList.add('mode-active');
    }

    function showMtpError(msg) {
        var err = document.getElementById('mtpError');
        err.querySelector('span').textContent = msg;
        err.classList.add('show');
    }
    function hideMtpError() {
        document.getElementById('mtpError').classList.remove('show');
    }

    function applyTimePicker() {
        if (!isValidOperational(mtpState.start) || !isValidOperational(mtpState.end)) {
            showMtpError('Waktu harus dalam rentang 07:00–16:00.');
            return;
        }
        if (minsOf(mtpState.start) >= minsOf(mtpState.end)) {
            showMtpError('Jam mulai harus lebih awal dari jam selesai.');
            return;
        }

        // VALIDASI WAKTU LEWAT (PAST DATE/TIME) — live, dicek langsung terhadap waktu saat ini
        var selectedDateStr = document.getElementById('bookingDate').value;
        if (selectedDateStr) {
            var now = new Date();
            var selectedStartDateTime = new Date(selectedDateStr + 'T' + pad2(mtpState.start.h) + ':' + pad2(mtpState.start.m) + ':00');

            if (selectedStartDateTime < now) {
                showMtpError('Tidak dapat memilih tanggal dan jam yang sudah lewat.');
                return;
            }
        }

        var startLabel = pad2(mtpState.start.h) + ':' + pad2(mtpState.start.m);
        var endLabel = pad2(mtpState.end.h) + ':' + pad2(mtpState.end.m);

        document.getElementById('bookingStartTime').value = startLabel;
        document.getElementById('bookingEndTime').value = endLabel;
        var labelEl = document.getElementById('bookingTimeLabel');
        labelEl.textContent = startLabel + ' – ' + endLabel;
        labelEl.classList.remove('time-placeholder');

        closeTimePicker(false);
        checkAvailability(); // Cek ketersediaan baru dijalankan setelah jam benar-benar dipilih
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mtpState.open) closeTimePicker(true);
    });

    // ============================================================
    // FORM SUBMIT (AJAX) — sekarang dipicu dari tombol "Ya, Ajukan
    // Peminjaman" di modal konfirmasi, bukan langsung dari submit form.
    // ============================================================
    function submitBookingForm() {
        var form = document.getElementById('bookingForm');

        const btn = document.getElementById('submitBtn');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast('success', 'Berhasil!', data.message || 'Peminjaman berhasil diajukan.', 3000);
                // Overlay SENGAJA ditunda muncul (bukan langsung bersamaan
                // toast) — supaya toast-nya sempat kelihatan dulu sebelum
                // layar meredup, bukan langsung ketiban overlay.
                setTimeout(() => {
                    if (typeof showBlockingOverlay === 'function') {
                        showBlockingOverlay('Mengalihkan ke halaman detail...');
                    }
                }, 500);
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("bookings.index") }}';
                }, 1500);
            } else {
                showToast('error', 'Gagal', data.message || 'Terjadi kesalahan saat mengajukan peminjaman.', 5000);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            if (error && error.errors) {
                let errorMessages = Object.values(error.errors).flat();
                showToast('error', 'Validasi Gagal', errorMessages.join('<br>'), 6000);
            } else {
                showToast('error', 'Error', 'Terjadi kesalahan jaringan atau server.', 5000);
            }
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // ============================================================
    // MODAL KONFIRMASI — dipicu saat tombol "Ajukan Peminjaman" utama
    // diklik. Validasi native browser dijalankan dulu (reportValidity),
    // baru kalau semua field wajib sudah terisi, ringkasan ditampilkan
    // untuk diperiksa sebelum benar-benar dikirim ke server.
    // ============================================================
    var roomsLookup = @json($roomsLookupData);

    function handleSubmitClick() {
        var form = document.getElementById('bookingForm');
        if (!form.reportValidity()) return;

        var dateVal = document.getElementById('bookingDate').value;
        var startVal = document.getElementById('bookingStartTime').value;
        if (dateVal && startVal) {
            var selectedStart = new Date(dateVal + 'T' + startVal + ':00');
            if (selectedStart < new Date()) {
                showToast('error', 'Waktu Tidak Valid', 'Tanggal dan jam yang dipilih sudah lewat. Silakan pilih waktu lain.', 5000);
                return;
            }
        }

        populateConfirmModal();
        var modalEl = document.getElementById('confirmModal');
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    function populateConfirmModal() {
        // Ruangan
        var roomRadio = document.querySelector('input[name="room_id"]:checked');
        var roomCard = document.getElementById('confirmRoomCard');
        if (roomRadio && roomsLookup[roomRadio.value]) {
            var r = roomsLookup[roomRadio.value];
            roomCard.innerHTML = (r.photo
                ? '<img src="' + r.photo + '" alt="' + r.name + '">'
                : '<div class="confirm-room-card-placeholder"><i class="bi bi-image"></i></div>'
            ) + '<div><div class="confirm-room-name">' + r.name + '</div>' +
                '<div class="confirm-room-meta"><span><i class="bi bi-geo-alt"></i> ' + r.location + '</span>' +
                '<span><i class="bi bi-people"></i> ' + r.capacity + ' orang</span></div></div>';
        }

        // Tanggal & jam
        var dateVal = document.getElementById('bookingDate').value;
        if (dateVal) {
            var d = new Date(dateVal + 'T00:00:00');
            document.getElementById('confirmDate').textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
        var startVal = document.getElementById('bookingStartTime').value;
        var endVal = document.getElementById('bookingEndTime').value;
        document.getElementById('confirmTime').textContent = startVal + ' – ' + endVal + ' WIB';

        // Jenis peminjaman — cuma ditampilkan kalau field-nya memang
        // terlihat di form (khusus Admin). Untuk Karyawan/Sesdir, field
        // ini disembunyikan dari form sehingga tidak perlu muncul juga
        // di ringkasan konfirmasi (konsisten dengan alasan awal field
        // ini disembunyikan — menghindari kecemburuan antar pengguna).
        var typeSelect = document.querySelector('select[name="booking_type"]');
        var typeRow = document.getElementById('confirmTypeRow');
        if (typeSelect) {
            typeRow.style.display = 'flex';
            var typeVal = typeSelect.value;
            document.getElementById('confirmType').innerHTML = typeVal === 'Priority'
                ? '<span class="confirm-priority-flag"><i class="bi bi-star-fill"></i> Priority</span>'
                : 'Reguler';
        } else {
            typeRow.style.display = 'none';
        }

        // Keperluan
        var purposeVal = document.querySelector('textarea[name="purpose"]').value.trim();
        document.getElementById('confirmPurpose').textContent = purposeVal || '-';

        // Fasilitas tambahan
        var badges = [];
        var needsConsumption = document.getElementById('needsConsumption').checked;
        var consumptionQty = document.getElementById('consumptionQty').value;
        if (needsConsumption) {
            badges.push('<span class="confirm-badge"><i class="bi bi-cup-hot"></i> Konsumsi' + (consumptionQty ? ' &middot; ' + consumptionQty + ' orang' : '') + '</span>');
        }
        var needsOther = document.getElementById('needsOther').checked;
        var otherNotes = document.getElementById('otherNotes').value.trim();
        if (needsOther) {
            badges.push('<span class="confirm-badge"><i class="bi bi-sticky"></i> ' + (otherNotes || 'Lain-lain') + '</span>');
        }
        document.getElementById('confirmFacilities').innerHTML = badges.length
            ? badges.join('')
            : '<span class="confirm-empty-note">Tidak ada permintaan tambahan</span>';
    }

    document.getElementById('confirmSubmitBtn').addEventListener('click', function () {
        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
        submitBookingForm();
    });
</script>
@endsection