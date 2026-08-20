@extends('templates.template')

@section('title', 'Detail Peminjaman')
@section('page_title', 'Detail Peminjaman')

@section('content')
<style>
    /* ============================================
       PAGE HEADER / TOOLBAR
       (disamakan persis dengan settings/rooms/show.blade.php
       agar posisi tombol Kembali & aksi identik di semua
       halaman detail)
    ============================================ */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
        gap: var(--space-4);
        margin-bottom: var(--space-5);
    }
    .db-toolbar .actions {
        display: flex;
        gap: var(--space-3);
        flex-wrap: nowrap;
        flex-shrink: 0;
    }

    .btn-back,
    .btn-primary-sm,
    .btn-outline-sm,
    .btn-approve-sm,
    .btn-danger-outline {
        height: 44px; padding: 0 var(--space-5);
        border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; gap: 8px;
        text-decoration: none; cursor: pointer; transition: all 0.2s;
        white-space: nowrap; border: none;
    }

    .btn-back {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .btn-outline-sm {
        background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);
    }
    .btn-outline-sm:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .btn-approve-sm {
        background: var(--brand-green); color: white;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
    }
    .btn-approve-sm:hover { background: var(--brand-green-dark); }

    .btn-primary-sm {
        background: var(--brand-orange); color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover { background: var(--brand-orange-dark); }

    .btn-danger-outline {
        background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); color: #dc2626;
    }
    .btn-danger-outline:hover { background: rgba(239, 68, 68, 0.1); }

    /* ============================================
       MODAL SETUJUI BOOKING
       Layout 2 kolom: kiri "Informasi Peminjaman" (pola baris
       ikon+label+value, sama seperti Modal Konfirmasi Pengajuan di
       halaman Tambah Peminjaman), kanan "Catatan". Tinggi textarea
       di-fix (bukan ikut memanjang) supaya body modal tidak pernah
       perlu discroll berapa pun isinya.
    ============================================ */
    .approve-modal .modal-dialog { max-width: 720px; }
    .approve-modal .modal-content {
        border: none; border-radius: 20px; overflow: hidden;
        box-shadow: 0 30px 70px rgba(16,24,43,0.22);
    }
    .approve-modal .modal-header {
        border-bottom: none; padding: 1.5rem 1.75rem 0.75rem; align-items: flex-start;
    }
    .approve-modal .modal-title {
        display: flex; align-items: center; gap: 12px;
        font-weight: 700; font-size: 1.15rem; color: var(--text-primary);
    }
    .approve-modal .modal-title-icon {
        width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
        background: rgba(16, 185, 129, 0.12); color: var(--brand-green-dark);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
    }
    .approve-modal .btn-close { opacity: 0.5; transition: opacity 0.2s; }
    .approve-modal .btn-close:hover { opacity: 1; }
    .approve-modal .modal-body { padding: 0.25rem 1.75rem 1.5rem; }

    .approve-info-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    .approve-col-title {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.4px; color: var(--text-muted); margin-bottom: 10px;
    }
    .approve-col-title .optional-tag {
        text-transform: none; letter-spacing: 0; font-weight: 400;
        color: var(--text-muted); font-size: 11px;
    }

    /* Kolom 1: Informasi Peminjaman — disamakan lengkap dengan Modal
       Konfirmasi Pengajuan (card ruangan + baris ikon/label/value +
       kutipan keperluan + badge fasilitas tambahan). */
    .approve-room-card {
        display: flex; align-items: center; gap: 12px;
        background: var(--bg-body); border-radius: 14px; padding: 10px 12px;
        margin-bottom: 2px; border: 1px solid var(--border-color-light);
    }
    .approve-room-card img { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
    .approve-room-card-placeholder {
        width: 52px; height: 52px; border-radius: 10px; flex-shrink: 0;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 1.2rem;
    }
    .approve-room-name { font-weight: 700; font-size: 0.96rem; color: var(--text-primary); line-height: 1.3; }
    .approve-room-meta { font-size: 0.76rem; color: var(--text-secondary); margin-top: 3px; display: flex; gap: 10px; flex-wrap: wrap; }

    .approve-row {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 9px 0; border-bottom: 1px solid var(--border-color-light);
    }
    .approve-row:last-child { border-bottom: none; }
    .approve-row-icon {
        width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
        background: rgba(255, 145, 66, 0.1); color: var(--brand-orange-dark);
        display: flex; align-items: center; justify-content: center; font-size: 0.85rem;
    }
    .approve-row-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.4px; color: var(--text-muted);
    }
    /* Value baris SENGAJA tidak bold (font-weight 500) — hanya nama
       ruangan di kartu atas yang bold, sebagai judul kartu. */
    .approve-row-value {
        font-size: 0.9rem; font-weight: 500; color: var(--text-primary);
        margin-top: 2px; line-height: 1.4; word-break: break-word;
    }

    .approve-purpose-quote {
        background: var(--bg-body); border-left: 3px solid var(--brand-orange);
        border-radius: 10px; padding: 10px 12px; font-size: 0.86rem; color: var(--text-primary);
        line-height: 1.55; margin-top: 2px; white-space: pre-line; font-weight: 500;
    }

    .approve-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
    .approve-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(34,197,94,0.1); color: var(--brand-green-dark);
        font-size: 0.76rem; font-weight: 600; padding: 5px 12px; border-radius: 999px;
    }
    .approve-empty-note { font-size: 0.83rem; color: var(--text-muted); font-style: italic; }

    /* Kolom 2: Catatan — tinggi textarea di-fix (180px), tidak
       terlalu kecil (muat beberapa baris) dan tidak terlalu besar
       (tidak bikin modal jadi jangkung). */
    .approve-note-col textarea.form-control {
        height: 180px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);
        resize: none; font-size: var(--font-size-sm); padding: 0.75rem 0.85rem; line-height: 1.5;
    }
    .approve-note-col textarea.form-control:focus {
        border-color: var(--brand-green); box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
    }

    .approve-modal .modal-footer {
        padding: 1rem 1.75rem 1.5rem; border-top: 1px solid var(--border-color-light); gap: 10px;
    }

    /* ============================================================
       PALET WARNA TOMBOL MODAL
       Set lengkap supaya tombol di modal lain (Tolak, Alihkan, Hapus,
       dll) bisa diselaraskan ke sini nanti. Bentuk & ukuran semua
       SAMA (tinggi 44px, radius-sm, font-weight 600) — yang beda
       cuma warna sesuai makna aksinya. Pemakaian:
       <button class="btn-modal btn-modal-success">...</button>
       ------------------------------------------------------------
       btn-modal-success          -> aksi menyetujui / konfirmasi positif (hijau)
       btn-modal-danger           -> aksi menolak / hapus permanen, solid (merah)
       btn-modal-danger-outline   -> versi ringan tombol merah (mis. "Tolak" pemicu modal)
       btn-modal-warning          -> aksi butuh perhatian, belum tentu merusak (amber)
       btn-modal-info             -> aksi netral bersifat info/navigasi (biru)
       btn-modal-primary          -> aksi utama netral warna brand (oranye, mis. "Alihkan")
       btn-modal-secondary        -> aksi netral / Batal, selalu outline abu-abu
       ============================================================ */
    .btn-modal {
        height: 44px; padding: 0 1.5rem; border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        border: none; cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .btn-modal-success { background: var(--brand-green); color: #fff; box-shadow: 0 4px 12px rgba(34,197,94,0.2); }
    .btn-modal-success:hover { background: var(--brand-green-dark); }

    .btn-modal-danger { background: #ef4444; color: #fff; box-shadow: 0 4px 12px rgba(239,68,68,0.2); }
    .btn-modal-danger:hover { background: #dc2626; }

    .btn-modal-danger-outline { background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.2); color: #dc2626; }
    .btn-modal-danger-outline:hover { background: rgba(239,68,68,0.1); }

    .btn-modal-warning { background: #f59e0b; color: #fff; box-shadow: 0 4px 12px rgba(245,158,11,0.2); }
    .btn-modal-warning:hover { background: #d97706; }

    .btn-modal-info { background: var(--brand-blue); color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,0.2); }
    .btn-modal-info:hover { background: #2563eb; }

    .btn-modal-primary { background: var(--brand-orange); color: #fff; box-shadow: 0 4px 12px rgba(249,115,22,0.2); }
    .btn-modal-primary:hover { background: var(--brand-orange-dark); }

    .btn-modal-secondary { background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary); }
    .btn-modal-secondary:hover { border-color: var(--text-muted); color: var(--text-primary); }


    /* ============================================
       SATU CARD BESAR DETAIL PEMINJAMAN
    ============================================ */
    .detail-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .card-section {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    .card-section:last-child { border-bottom: none; }

    .section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: var(--space-4);
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: none !important;
    }
    .section-title i {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(249, 115, 22, 0.1);
        color: var(--brand-orange);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
    }

    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--space-4);
        flex-wrap: wrap;
    }
    .booking-room {
        font-size: var(--font-size-xl);
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }
    .booking-date {
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 500;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Foto ruangan di header, supaya halaman detail langsung terasa
       "hidup" tanpa menambah banyak tinggi (thumbnail, bukan banner
       besar) — sejalan dengan tujuan memadatkan halaman. */
    .booking-header-photo {
        width: 84px; height: 84px; border-radius: 16px; overflow: hidden; flex-shrink: 0;
    }
    .booking-header-photo img { width: 100%; height: 100%; object-fit: cover; }
    .booking-header-photo-placeholder {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: var(--text-muted); font-size: 1.6rem;
    }
    .booking-header-main { flex: 1; min-width: 180px; display: flex; align-items: center; gap: var(--space-4); }

    /* ============================================
       LAYOUT 2 KOLOM HALAMAN DETAIL
       Kolom utama (info inti) lebih lebar, kolom samping (QR &
       timeline) lebih ramping — supaya konten tidak melulu menumpuk
       vertikal dan halaman terasa lebih padat/rapi.
    ============================================ */
    .booking-page-grid {
        display: grid;
        grid-template-columns: 1.7fr 1fr;
        gap: var(--space-5);
        align-items: start;
    }
    .booking-side-col { display: flex; flex-direction: column; gap: var(--space-5); }

    .extra-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: var(--space-5);
    }

    @media (max-width: 1199.98px) {
        .booking-page-grid { grid-template-columns: 1fr; }
    }

    .status-badge {
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: var(--radius-pill);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .status-pending { background: rgba(245,158,11,0.12); color: #b45309; }
    .status-approved { background: rgba(16,185,129,0.12); color: #047857; }
    .status-rejected { background: rgba(239,68,68,0.12); color: #b91c1c; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--space-4);
    }
    .info-item {
        background: var(--bg-body);
        padding: var(--space-3) var(--space-4);
        border-radius: var(--radius-sm);
    }
    .info-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .info-value {
        font-size: var(--font-size-sm);
        font-weight: 600;
        color: var(--text-primary);
    }

    .purpose-box {
        background: var(--bg-body);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        line-height: 1.6;
    }

    .facility-list-detail {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }
    .facility-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-3);
        background: var(--bg-body);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
    }
    .facility-detail-name { font-weight: 600; color: var(--text-primary); }
    .facility-detail-qty {
        background: var(--brand-orange);
        color: white;
        padding: 2px 10px;
        border-radius: var(--radius-pill);
        font-size: 11px;
        font-weight: 700;
    }

    /* Rejection Alert */
    .rejection-alert {
        background: rgba(239,68,68,0.05);
        border: 1px solid rgba(239,68,68,0.2);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
    }
    .rejection-alert-title {
        font-weight: 700;
        color: #b91c1c;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: var(--space-2);
    }
    .rejection-alert-text {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }

    /* Priority Conflict Alert — pakai warna WARNING (amber) sesuai palet
       btn-modal-warning: konflik jadwal itu "butuh perhatian", bukan
       warna oranye netral biasa yang dipakai untuk ikon section biasa. */
    .conflict-alert {
        background: rgba(245,158,11,0.08);
        border: 1px solid rgba(245,158,11,0.3);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
    }
    .conflict-alert-title {
        font-weight: 700;
        color: #b45309;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: var(--space-2);
    }
    .conflict-alert-text {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        line-height: 1.6;
    }
    .conflict-alert-text a { color: #b45309; font-weight: 600; }
    .conflict-alert-actions { margin-top: var(--space-3); display: flex; gap: var(--space-2); flex-wrap: wrap; }

    .conflict-badge {
        font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-pill);
        background: rgba(245,158,11,0.15); color: #b45309;
        display: inline-flex; align-items: center; gap: 4px; text-transform: uppercase; letter-spacing: 0.3px;
    }

    /* Info: booking Priority ini menyebabkan booking lain bentrok */
    .displaces-info {
        background: rgba(59,130,246,0.05);
        border: 1px solid rgba(59,130,246,0.2);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
    }
    .displaces-info-title {
        font-weight: 700; color: var(--brand-blue);
        display: flex; align-items: center; gap: 6px; margin-bottom: var(--space-2);
    }
    .displaces-info-text { font-size: var(--font-size-sm); color: var(--text-secondary); margin-bottom: var(--space-2); }

    /* Riwayat Pengalihan Ruangan — perbandingan "sebelumnya" vs "sekarang"
       berdampingan, supaya peminjam langsung paham ruangan & jam berubah
       ke apa, tanpa harus membaca ulang catatan admin baris demi baris. */
    .reloc-history-grid {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .reloc-history-box {
        flex: 1; min-width: 140px;
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-sm); padding: 10px 12px;
    }
    .reloc-history-box.is-new { border-color: rgba(59,130,246,0.3); background: rgba(59,130,246,0.04); }
    .reloc-history-label {
        font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;
        color: var(--text-muted); margin-bottom: 4px;
    }
    .reloc-history-room { font-weight: 700; font-size: 0.88rem; color: var(--text-primary); }
    .reloc-history-time { font-size: 0.78rem; color: var(--text-secondary); margin-top: 3px; line-height: 1.4; }
    .reloc-history-arrow { color: var(--brand-blue); font-size: 1.1rem; flex-shrink: 0; }
    .badge-time-changed {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11.5px; font-weight: 600; padding: 3px 10px; border-radius: 999px;
        background: rgba(34,197,94,0.12); color: #15803d;
    }
    .badge-time-changed.changed { background: rgba(245,158,11,0.15); color: #b45309; }

    /* QR Code */
    .qr-section {
        text-align: center;
        padding: var(--space-4);
        background: var(--bg-body);
        border-radius: var(--radius-sm);
    }
    .qr-code {
        margin: var(--space-3) auto;
        width: 200px;
        height: 200px;
        background: white;
        padding: 10px;
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-card);
    }
    .qr-code img { width: 100%; height: 100%; }
    .qr-label {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: var(--space-2);
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 32px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border-color);
    }
    .timeline-item {
        position: relative;
        padding-bottom: var(--space-5);
    }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 3px solid var(--brand-orange);
    }
    .timeline-item.success::before { border-color: var(--brand-green); }
    .timeline-item.danger::before { border-color: #ef4444; }
    .timeline-item.info::before { border-color: var(--brand-blue); }
    .timeline-icon {
        position: absolute;
        left: -32px;
        top: -2px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
    }
    .timeline-icon.primary { background: var(--brand-orange); }
    .timeline-icon.success { background: var(--brand-green); }
    .timeline-icon.danger { background: #ef4444; }
    .timeline-icon.info { background: var(--brand-blue); }
    .timeline-title {
        font-weight: 700;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
    }
    .timeline-desc {
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
        margin-top: 2px;
    }
    .timeline-time {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    @media (max-width: 991.98px) {
        .db-toolbar { flex-wrap: wrap; }
        .db-toolbar .actions { width: 100%; }
        .db-toolbar .actions .btn-outline-sm,
        .db-toolbar .actions .btn-approve-sm,
        .db-toolbar .actions .btn-danger-outline,
        .db-toolbar .actions form { flex: 1; }
        .db-toolbar .actions .btn-outline-sm,
        .db-toolbar .actions .btn-approve-sm,
        .db-toolbar .actions .btn-danger-outline { width: 100%; justify-content: center; }
        .info-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 575.98px) {
        .db-toolbar .actions { flex-direction: column; gap: 8px; }
        .info-grid { grid-template-columns: 1fr; }
        .approve-info-grid { grid-template-columns: 1fr; gap: 1.25rem; }
        .approve-note-col textarea.form-control { height: 130px; }
        .booking-header-photo { width: 64px; height: 64px; border-radius: 12px; }
        .extra-info-grid { grid-template-columns: 1fr; }
    }

    /* ============================================================
       MODAL ALIHKAN KE RUANGAN LAIN
       Header pakai badge ikon oranye (primary), konsisten dengan
       modal Setujui (badge hijau) — beda warna sesuai makna aksinya,
       bentuk & pola tetap sama.
    ============================================================ */
    .relocate-modal .modal-dialog { max-width: 560px; }
    .relocate-modal .modal-content {
        border: none; border-radius: 20px; overflow: hidden;
        box-shadow: 0 30px 70px rgba(16,24,43,0.22);
    }
    .relocate-modal .modal-header {
        border-bottom: none; padding: 1.5rem 1.75rem 0.75rem; align-items: flex-start;
    }
    .relocate-modal .modal-title {
        display: flex; align-items: center; gap: 12px;
        font-weight: 700; font-size: 1.15rem; color: var(--text-primary);
    }
    .relocate-modal .modal-title-icon {
        width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
        background: rgba(249, 115, 22, 0.12); color: var(--brand-orange-dark);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }
    .relocate-modal .btn-close { opacity: 0.5; transition: opacity 0.2s; }
    .relocate-modal .btn-close:hover { opacity: 1; }
    .relocate-modal .modal-body { padding: 0.25rem 1.75rem 1.5rem; }
    .relocate-modal .modal-footer {
        padding: 1rem 1.75rem 1.5rem; border-top: 1px solid var(--border-color-light); gap: 10px;
    }
    .relocate-modal .reloc-intro {
        background: var(--bg-body); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-sm); padding: 0.85rem 1rem;
        font-size: var(--font-size-sm); color: var(--text-secondary); line-height: 1.6;
        margin-bottom: 1.25rem;
    }
    .relocate-modal .reloc-intro strong { color: var(--text-primary); }
    .relocate-modal .form-label {
        font-weight: 600; font-size: var(--font-size-sm); color: var(--text-primary); margin-bottom: 8px;
    }
    .relocate-modal .form-group { margin-bottom: 1.1rem; }
    .relocate-modal .form-group:last-child { margin-bottom: 0; }
    .relocate-modal .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .relocate-modal .modern-input,
    .relocate-modal .form-select {
        width: 100%; height: 44px; padding: 0 14px;
        border: 1px solid var(--border-color); border-radius: var(--radius-sm);
        background: var(--bg-input); font-size: 0.9rem; font-family: inherit;
        color: var(--text-primary); transition: all 0.2s; outline: none;
    }
    .relocate-modal .modern-input:focus,
    .relocate-modal .form-select:focus {
        border-color: var(--brand-orange); background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }
    .relocate-modal .form-select:disabled { color: var(--text-muted); cursor: not-allowed; }
    .select-wrapper { position: relative; }
    .select-wrapper .form-select {
        appearance: none; -webkit-appearance: none; -moz-appearance: none;
        padding-right: 40px; cursor: pointer;
    }
    .select-wrapper::after {
        content: ''; position: absolute; top: 50%; right: 16px;
        width: 10px; height: 10px;
        border-right: 2px solid var(--text-muted); border-bottom: 2px solid var(--text-muted);
        transform: translateY(-70%) rotate(45deg); pointer-events: none; transition: border-color 0.2s;
    }
    .select-wrapper:focus-within::after { border-color: var(--brand-orange); }
    .relocate-modal .room-select-hint {
        font-size: 12px; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 5px;
    }
    .relocate-modal .room-select-hint.empty { color: #b91c1c; }
    /* Catatan: TANPA placeholder (sesuai permintaan), tinggi di-fix —
       tidak kecil (muat beberapa baris) dan tidak besar. */
    .relocate-modal textarea.modern-input {
        height: 130px; padding: 10px 14px; resize: none; line-height: 1.5;
    }

    /* Trigger jam — dipakai ulang persis dari halaman Tambah Peminjaman,
       supaya pola pilih jam konsisten di seluruh aplikasi. */
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

    @media (max-width: 575.98px) {
        .relocate-modal .row-2 { grid-template-columns: 1fr; }
        .mtp-dialog { width: 95vw; }
        .mtp-header { padding: 16px 20px 4px; }
        .mtp-time-display { gap: 16px; padding: 8px 20px 4px; }
        .mtp-digital { font-size: 36px; }
        .mtp-field { padding: 8px 12px; }
        .mtp-clock { width: 240px; height: 240px; }
        .mtp-footer { padding: 12px 20px 20px; }
    }
</style>


{{-- FIX: aksi approve/tolak/hapus/alihkan bisa redirect balik ke halaman
     ini (back()), tapi pesan sukses/error-nya sebelumnya tidak pernah
     ditampilkan di sini. --}}
@if(session('success'))
    <div class="alert-modern alert-success">
        <i class="bi bi-check-circle-fill alert-modern-icon"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
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

@include('bookings.partials._top-status', ['booking' => $booking, 'isConflicted' => $isConflicted, 'awaitingConfirmation' => $awaitingConfirmation, 'isBookingOwner' => $isBookingOwner])

{{-- GRID 2 KOLOM: kiri = info inti peminjaman, kanan = QR presensi +
     timeline — supaya tidak melulu menumpuk ke bawah. --}}
<div class="booking-page-grid">

    {{-- KOLOM UTAMA --}}
    <div class="booking-main-col">
        <div class="detail-card">
            <div class="card-section">
                <div class="section-title">
                    <i class="bi bi-info-circle"></i> Informasi Peminjaman
                </div>
                <div class="info-grid" style="{{ Auth::user()->role != 1 ? 'grid-template-columns: repeat(3, 1fr);' : '' }}">
                    <div class="info-item">
                        <div class="info-label">Jam Mulai</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} WIB</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jam Selesai</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Peminjam</div>
                        <div class="info-value">{{ $booking->user->full_name ?? 'Unknown' }}</div>
                    </div>
                    @if(Auth::user()->role == 1)
                    <div class="info-item">
                        <div class="info-label">Tipe Booking</div>
                        <div class="info-value">{{ $booking->booking_type }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-section">
                <div class="section-title">
                    <i class="bi bi-card-text"></i> Keperluan
                </div>
                <div class="purpose-box">{{ $booking->purpose }}</div>
            </div>

            @if($booking->relocated_at)
            <div class="card-section">
                <div class="displaces-info">
                    <div class="displaces-info-title">
                        <i class="bi bi-arrow-left-right"></i> Riwayat Pengalihan Ruangan
                    </div>
                    @php
                        $fromRoomName = $booking->relocatedFromRoom->name ?? 'Ruangan sebelumnya';
                        $timeChanged = $booking->relocated_from_start_time && (
                            !$booking->relocated_from_start_time->eq($booking->start_time)
                            || !$booking->relocated_from_end_time->eq($booking->end_time)
                        );
                    @endphp
                    <div class="reloc-history-grid">
                        <div class="reloc-history-box">
                            <div class="reloc-history-label">Sebelumnya</div>
                            <div class="reloc-history-room">{{ $fromRoomName }}</div>
                            @if($booking->relocated_from_start_time)
                            <div class="reloc-history-time">
                                {{ $booking->relocated_from_start_time->locale('id')->isoFormat('D MMM Y') }}<br>
                                {{ $booking->relocated_from_start_time->format('H:i') }}&ndash;{{ $booking->relocated_from_end_time->format('H:i') }} WIB
                            </div>
                            @endif
                        </div>
                        <div class="reloc-history-arrow"><i class="bi bi-arrow-right"></i></div>
                        <div class="reloc-history-box is-new">
                            <div class="reloc-history-label">Sekarang</div>
                            <div class="reloc-history-room">{{ $booking->room->name }}</div>
                            <div class="reloc-history-time">
                                {{ $booking->start_time->locale('id')->isoFormat('D MMM Y') }}<br>
                                {{ $booking->start_time->format('H:i') }}&ndash;{{ $booking->end_time->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>
                    <div class="displaces-info-text mb-0" style="margin-top: 10px;">
                        <span class="badge-time-changed {{ $timeChanged ? 'changed' : '' }}">
                            <i class="bi bi-{{ $timeChanged ? 'clock-history' : 'check2' }}"></i>
                            {{ $timeChanged ? 'Jam rapat berubah' : 'Jam rapat tidak berubah, hanya ruangan' }}
                        </span>
                        @if($booking->relocation_note)
                            <br><br>Catatan admin: <em>"{{ $booking->relocation_note }}"</em>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Kebutuhan Tambahan & Fasilitas Diminta digabung berdampingan
                 (bukan tumpuk ke bawah) supaya lebih padat kalau dua-duanya ada. --}}
            @if(($booking->consumption_qty || $booking->other_notes) || $booking->bookingFacilities->count() > 0)
            <div class="card-section">
                <div class="extra-info-grid">
                    @if($booking->consumption_qty || $booking->other_notes)
                    <div>
                        <div class="section-title">
                            <i class="bi bi-tools"></i> Kebutuhan Tambahan
                        </div>
                        <div class="facility-list-detail">
                            @if($booking->consumption_qty)
                                <div class="facility-detail-item">
                                    <div class="facility-detail-name">Konsumsi</div>
                                    <span class="facility-detail-qty">{{ $booking->consumption_qty }} Peserta</span>
                                </div>
                            @endif
                            @if($booking->other_notes)
                                <div class="info-item">
                                    <div class="info-label">Lain-lain (Catatan)</div>
                                    <div class="info-value" style="font-weight: 500; white-space: pre-line;">{{ $booking->other_notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($booking->bookingFacilities->count() > 0)
                    <div>
                        <div class="section-title">
                            <i class="bi bi-box-seam"></i> Fasilitas Diminta
                        </div>
                        <div class="facility-list-detail">
                            @foreach($booking->bookingFacilities as $bf)
                                <div class="facility-detail-item">
                                    <div class="facility-detail-name">{{ $bf->facility->name }}</div>
                                    <span class="facility-detail-qty">Qty: {{ $bf->quantity }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    @include('bookings.partials._side-panel', ['booking' => $booking, 'timeline' => $timeline])
</div>

{{-- DAFTAR HADIR — full-width di bawah grid, karena daftarnya bisa
     panjang dan butuh ruang horizontal lebih untuk nyaman dibaca. --}}
@if($booking->attendances->count() > 0)
<div class="detail-card" style="margin-top: var(--space-5);">
    <div class="card-section">
        <div class="section-title" style="justify-content: space-between; margin-bottom: var(--space-4);">
            <span style="display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-people"></i> Daftar Hadir ({{ $booking->attendances->count() }})
            </span>
            <a href="{{ route('bookings.attendance.export', $booking->id) }}" class="btn-outline-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
        <div class="facility-list-detail">
            @foreach($booking->attendances as $att)
                <div class="facility-detail-item">
                    <div>
                        {{-- FIX: sebelumnya nama AKUN yang sedang login (user->full_name)
                             menimpa nama yang benar-benar diketik di form presensi
                             (guest_name) — jadi kalau admin scan QR sambil login lalu
                             mengisi nama tamu, yang tampil malah "Administrator Sistem",
                             bukan nama tamu yang diketik. guest_name selalu wajib diisi
                             di form, jadi itu yang seharusnya jadi sumber tampilan utama;
                             user_id cuma metadata pelacakan siapa yang login saat submit,
                             bukan pengganti nama yang diinput. --}}
                        <div class="facility-detail-name">{{ $att->guest_name ?: ($att->user->full_name ?? '-') }}</div>
                        <small class="text-muted">{{ $att->guest_institution ?? 'Internal' }}</small>
                    </div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}</small>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Print QR Modal — cetak QR tanpa pindah halaman --}}
@if($booking->status == 1 && $booking->absent_code)
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
    #printQrModal .qr-print-body { text-align: center; padding: 2rem 1.5rem; }
    #printQrModal .qr-print-room { font-size: 1.2rem; font-weight: 800; color: var(--text-primary); margin: 0; }
    #printQrModal .qr-print-meta { color: var(--text-muted); font-size: 0.85rem; margin-top: 4px; font-weight: 500; }
    #printQrModal .qr-print-code {
        width: 220px; height: 220px; margin: 1.5rem auto;
        background: #fff; padding: 12px; border-radius: 14px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08); border: 1px solid var(--border-color-light);
    }
    #printQrModal .qr-print-code img, #printQrModal .qr-print-code canvas { width: 100%; height: 100%; }
    #printQrModal .qr-print-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin-bottom: 1.5rem; }
    #printQrModal .qr-action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    #printQrModal .qr-action-btn {
        height: 44px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);
        background: var(--bg-card); color: var(--text-secondary); font-weight: 600; font-size: 0.85rem;
        display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s;
    }
    #printQrModal .qr-action-btn:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }
    #printQrModal .qr-action-btn.primary { background: var(--brand-gradient); border-color: transparent; color: #fff; grid-column: 1 / -1; }
    #printQrModal .qr-action-btn.primary:hover { color: #fff; filter: brightness(0.96); }

    /* ============================================================
       AREA CETAK KHUSUS — sengaja dipisah dari modal Bootstrap.
       Modal Bootstrap pakai flexbox untuk center vertikal isinya di
       layar, dan itu ikut kebawa saat print (menyebabkan konten
       "ngambang" jauh di bawah halaman kosong). Area ini disembunyikan
       total di layar (display:none) dan HANYA muncul saat print,
       mengalir normal dari atas halaman seperti dokumen biasa.
    ============================================================ */
    .qr-print-area { display: none; }
    @media print {
        body * { visibility: hidden; }
        .qr-print-area, .qr-print-area * { visibility: visible; }
        .qr-print-area {
            display: block !important;
            position: absolute; left: 0; top: 0; width: 100%;
        }
        .qr-print-page {
            max-width: 420px; margin: 50px auto 0; text-align: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .qr-print-page .brand { font-size: 12px; letter-spacing: 1px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 18px; }
        .qr-print-page h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .qr-print-page .meta { font-size: 13px; color: #475569; margin-bottom: 24px; }
        .qr-print-page .qr-box { width: 260px; height: 260px; margin: 0 auto 16px; padding: 14px; border: 2px solid #e2e8f0; border-radius: 16px; }
        .qr-print-page .qr-box img, .qr-print-page .qr-box canvas { width: 100%; height: 100%; }
        .qr-print-page .code-label { font-size: 12px; color: #64748b; font-weight: 700; margin-bottom: 20px; }
        .qr-print-page .instruction { font-size: 12px; color: #475569; line-height: 1.6; max-width: 320px; margin: 0 auto; }
    }
</style>
<div class="modal fade" id="printQrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-printer"></i> Cetak / Bagikan QR Presensi</h5>
                <button type="button" class="btn-close no-print" data-bs-dismiss="modal"></button>
            </div>
            <div class="qr-print-body">
                <h3 class="qr-print-room">{{ $booking->room->name }}</h3>
                <div class="qr-print-meta">
                    {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}<br>
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
                </div>
                <div class="qr-print-code" id="qrPrintModalContainer"></div>
                <div class="qr-print-label">Kode: {{ $booking->absent_code }}</div>

                <div class="qr-action-grid">
                    <button type="button" class="qr-action-btn" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                    <button type="button" class="qr-action-btn" id="qrDownloadBtn">
                        <i class="bi bi-download"></i> Download PNG
                    </button>
                    <button type="button" class="qr-action-btn primary" id="qrShareBtn">
                        <i class="bi bi-share-fill"></i> Bagikan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Konten khusus cetak, terpisah dari modal (lihat penjelasan di CSS) --}}
<div class="qr-print-area">
    <div class="qr-print-page">
        <div class="brand">SIAP &middot; Sistem Informasi Peminjaman Ruangan</div>
        <h1>{{ $booking->room->name }}</h1>
        <div class="meta">
            {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }} &middot;
            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
        </div>
        <div class="qr-box" id="qrPrintAreaContainer"></div>
        <div class="code-label">Kode: {{ $booking->absent_code }}</div>
        <div class="instruction">Pindai kode QR ini menggunakan kamera HP untuk mengisi presensi kehadiran rapat.</div>
    </div>
</div>

<script>
    (function () {
        var meetingUrl = "{{ url('/booking/meet/' . $booking->absent_code) }}";
        var shareText = "Presensi rapat {{ addslashes($booking->room->name) }} — " +
            "{{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y, H:i') }} WIB. " +
            "Isi kehadiran di sini: " + meetingUrl;
        var qrGenerated = false;

        function generateQrCodes() {
            if (qrGenerated) return;
            qrGenerated = true;
            new QRCode(document.getElementById('qrPrintModalContainer'), {
                text: meetingUrl, width: 196, height: 196,
                colorDark: "#000000", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.H
            });
            // QR kedua khusus untuk area cetak (dipisah dari modal, lihat CSS)
            new QRCode(document.getElementById('qrPrintAreaContainer'), {
                text: meetingUrl, width: 232, height: 232,
                colorDark: "#000000", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.H
            });
        }

        document.getElementById('printQrModal').addEventListener('shown.bs.modal', generateQrCodes);

        document.getElementById('qrDownloadBtn').addEventListener('click', function () {
            var canvas = document.querySelector('#qrPrintModalContainer canvas');
            if (!canvas) return;
            var link = document.createElement('a');
            link.download = 'QR-Presensi-{{ $booking->absent_code }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });

        document.getElementById('qrShareBtn').addEventListener('click', function () {
            var canvas = document.querySelector('#qrPrintModalContainer canvas');
            // Di HP: pakai native share sheet (bisa langsung kirim gambar QR ke WA/aplikasi lain).
            if (navigator.share && canvas) {
                canvas.toBlob(function (blob) {
                    var file = new File([blob], 'QR-Presensi-{{ $booking->absent_code }}.png', { type: 'image/png' });
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        navigator.share({ files: [file], title: 'QR Presensi {{ addslashes($booking->room->name) }}', text: shareText })
                            .catch(function () {});
                    } else {
                        navigator.share({ title: 'QR Presensi', text: shareText }).catch(function () {});
                    }
                });
            } else {
                // Fallback desktop: buka WhatsApp Web dengan teks siap kirim.
                window.open('https://wa.me/?text=' + encodeURIComponent(shareText), '_blank');
            }
        });
    })();
</script>
@endif

{{-- Approve Modal --}}
@if(Auth::user()->role == 1 && $booking->status == 0)
<div class="modal fade approve-modal" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bookings.approve', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="modal-title-icon"><i class="bi bi-check-lg"></i></span>
                        Setujui Peminjaman Ruangan?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="approve-info-grid">
                        {{-- KOLOM 1: Informasi Peminjaman --}}
                        <div class="approve-info-col">
                            <div class="approve-col-title">Informasi Peminjaman</div>

                            @php
                                $roomPhotos = $booking->room->photos ?? collect();
                                $roomFirstPhoto = $roomPhotos->count() > 0 ? $roomPhotos->first()->photo_url : null;
                                $hasExtra = $booking->consumption_qty || $booking->other_notes || $booking->bookingFacilities->count() > 0;
                            @endphp
                            <div class="approve-room-card">
                                @if($roomFirstPhoto)
                                    <img src="{{ $roomFirstPhoto }}" alt="{{ $booking->room->name }}">
                                @else
                                    <div class="approve-room-card-placeholder"><i class="bi bi-door-open"></i></div>
                                @endif
                                <div>
                                    <div class="approve-room-name">{{ $booking->room->name }}</div>
                                    <div class="approve-room-meta">
                                        <span><i class="bi bi-geo-alt"></i> {{ $booking->room->location }}</span>
                                        <span><i class="bi bi-people"></i> {{ $booking->room->capacity }} orang</span>
                                    </div>
                                </div>
                            </div>

                            <div class="approve-row">
                                <span class="approve-row-icon"><i class="bi bi-person"></i></span>
                                <div>
                                    <div class="approve-row-label">Peminjam</div>
                                    <div class="approve-row-value">{{ $booking->user->full_name ?? 'Unknown' }}</div>
                                </div>
                            </div>

                            <div class="approve-row">
                                <span class="approve-row-icon"><i class="bi bi-calendar3"></i></span>
                                <div>
                                    <div class="approve-row-label">Tanggal</div>
                                    <div class="approve-row-value">{{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
                                </div>
                            </div>

                            <div class="approve-row">
                                <span class="approve-row-icon"><i class="bi bi-clock"></i></span>
                                <div>
                                    <div class="approve-row-label">Jam</div>
                                    <div class="approve-row-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB</div>
                                </div>
                            </div>

                            <div class="approve-row">
                                <span class="approve-row-icon"><i class="bi bi-tag"></i></span>
                                <div>
                                    <div class="approve-row-label">Jenis Peminjaman</div>
                                    <div class="approve-row-value">{{ $booking->booking_type === 'Priority' ? 'Priority' : 'Reguler' }}</div>
                                </div>
                            </div>

                            <div class="approve-row">
                                <span class="approve-row-icon"><i class="bi bi-card-text"></i></span>
                                <div style="min-width:0; flex:1;">
                                    <div class="approve-row-label">Keperluan / Agenda</div>
                                    <div class="approve-purpose-quote">{{ $booking->purpose }}</div>
                                </div>
                            </div>

                            <div class="approve-row">
                                <span class="approve-row-icon"><i class="bi bi-tools"></i></span>
                                <div>
                                    <div class="approve-row-label">Fasilitas Tambahan</div>
                                    <div class="approve-badges">
                                        @if($booking->consumption_qty)
                                            <span class="approve-badge"><i class="bi bi-cup-hot"></i> Konsumsi &middot; {{ $booking->consumption_qty }} peserta</span>
                                        @endif
                                        @foreach($booking->bookingFacilities as $bf)
                                            <span class="approve-badge"><i class="bi bi-box-seam"></i> {{ $bf->facility->name }} &middot; {{ $bf->quantity }}</span>
                                        @endforeach
                                        @if($booking->other_notes)
                                            <span class="approve-badge"><i class="bi bi-sticky"></i> Lain-lain</span>
                                        @endif
                                        @if(!$hasExtra)
                                            <span class="approve-empty-note">Tidak ada permintaan tambahan</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM 2: Catatan --}}
                        <div class="approve-note-col">
                            <div class="approve-col-title">
                                Catatan <span class="optional-tag">(Opsional)</span>
                            </div>
                            <textarea
                                id="approvalNoteInput"
                                name="approval_note"
                                class="form-control"
                                maxlength="500"
                            ></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-modal-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal btn-modal-success">
                        <i class="bi bi-check-lg"></i> Ya, Setujui Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bookings.reject', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle text-danger"></i> Tolak Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Berikan alasan penolakan untuk peminjam:</p>
                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Minimal 10 karakter..." required minlength="10" maxlength="500"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg"></i> Tolak Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Confirm/Decline Relocation Modal — cuma dirender untuk pemilik booking
     saat sedang menunggu konfirmasinya sendiri. --}}
@if($awaitingConfirmation && $isBookingOwner)
<div class="modal fade approve-modal" id="confirmRelocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bookings.confirm-relocation', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="modal-title-icon"><i class="bi bi-check-lg"></i></span>
                        Setujui Pengalihan?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="approve-summary">
                        Anda akan memakai <strong>{{ $booking->room->name }}</strong> pada
                        {{ $booking->start_time->locale('id')->isoFormat('dddd, D MMMM Y') }},
                        {{ $booking->start_time->format('H:i') }}&ndash;{{ $booking->end_time->format('H:i') }} WIB
                        sebagai pengganti jadwal semula.
                        <span class="summary-hint">Kode presensi QR akan dibuat otomatis setelah Anda menyetujui.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-modal-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal btn-modal-success">
                        <i class="bi bi-check-lg"></i> Ya, Setujui Pengalihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade relocate-modal" id="declineRelocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bookings.decline-relocation', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="modal-title-icon" style="background: rgba(239,68,68,0.12); color: #dc2626;"><i class="bi bi-x-lg"></i></span>
                        Tolak Pengalihan?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="reloc-intro">
                        Booking ini akan otomatis berstatus <strong>Ditolak</strong> — jadwal semula sudah pasti terpakai
                        peminjaman lain sehingga tidak bisa dikembalikan. Anda perlu membuat pengajuan baru
                        kalau masih membutuhkan ruangan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-modal-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal btn-modal-danger">
                        <i class="bi bi-x-lg"></i> Ya, Tolak Pengalihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Relocate Modal (hanya render kalau booking sedang bentrok DAN yang
     melihat adalah admin — non-admin tidak boleh melihat markup ini sama
     sekali, bahkan lewat page-source, karena isinya menyebut "prioritas"). --}}
@if($booking->displaced_by_id && $booking->status != 2 && Auth::user()->role == 1)
<div class="modal fade relocate-modal" id="relocateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bookings.relocate', $booking->id) }}" method="POST" id="relocateForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="modal-title-icon"><i class="bi bi-arrow-left-right"></i></span>
                        Alihkan ke Ruangan Lain
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="reloc-intro">
                        Bentrok dengan peminjaman prioritas <strong>{{ $booking->displacedBy->user->full_name ?? 'Direksi' }}</strong>.
                        Pilih ruangan dan waktu pengganti.
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ruangan Pengganti</label>
                        <div class="select-wrapper">
                            <select name="room_id" class="form-select" id="relocRoomSelect" required disabled>
                                <option value="">Pilih tanggal &amp; jam dahulu</option>
                            </select>
                        </div>
                        <div class="room-select-hint" id="relocRoomHint">
                            <i class="bi bi-info-circle"></i> <span>Hanya menampilkan ruangan yang kosong pada waktu terpilih.</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="relocDate" class="modern-input"
                               min="{{ date('Y-m-d') }}"
                               value="{{ \Carbon\Carbon::parse($booking->start_time)->format('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jam Mulai &amp; Selesai</label>
                        <button type="button" class="time-trigger" onclick="openRelocTimePicker()">
                            <i class="bi bi-clock"></i>
                            <span id="relocTimeLabel">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                            <i class="bi bi-chevron-down chevron"></i>
                        </button>
                    </div>

                    {{-- Field asli yang benar-benar dikirim ke server (datetime penuh),
                         disinkronkan otomatis dari tanggal + jam yang dipilih di atas. --}}
                    <input type="hidden" name="start_time" id="relocStartTimeField" value="{{ \Carbon\Carbon::parse($booking->start_time)->format('Y-m-d H:i:00') }}">
                    <input type="hidden" name="end_time" id="relocEndTimeField" value="{{ \Carbon\Carbon::parse($booking->end_time)->format('Y-m-d H:i:00') }}">

                    <div class="form-group">
                        <label class="form-label">Catatan untuk Peminjam</label>
                        <textarea name="relocation_note" class="modern-input" required minlength="10" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-modal-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal btn-modal-primary" id="relocSubmitBtn">
                        <i class="bi bi-check-lg"></i> Alihkan &amp; Kirim Notifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Picker jam untuk modal Alihkan — komponen yang sama persis dengan
     halaman Tambah/Edit Peminjaman, namespace "reloc" supaya tidak
     bentrok ID kalau nanti ada picker lain di halaman yang sama. --}}
<div class="mtp-overlay" id="relocMtpOverlay" onclick="if(event.target===this)closeRelocTimePicker(true)">
    <div class="mtp-dialog" role="dialog" aria-modal="true" aria-labelledby="relocMtpTitle">
        <div class="mtp-header">
            <div class="mtp-title" id="relocMtpTitle">Pilih Jam Kerja</div>
            <div class="mtp-subtitle">Rentang 07:00 - 16:00</div>
        </div>
        <div class="mtp-time-display">
            <div class="mtp-field active" data-field="start" onclick="setRelocActiveField('start')">
                <div class="mtp-field-label">Mulai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="relocMtpStartHour" onclick="event.stopPropagation();selectRelocUnit('start', 'hour')">07</span><span class="mtp-colon">:</span><span class="mtp-unit" id="relocMtpStartMin" onclick="event.stopPropagation();selectRelocUnit('start', 'minute')">00</span>
                </div>
            </div>
            <div class="mtp-field" data-field="end" onclick="setRelocActiveField('end')">
                <div class="mtp-field-label">Selesai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="relocMtpEndHour" onclick="event.stopPropagation();selectRelocUnit('end', 'hour')">08</span><span class="mtp-colon">:</span><span class="mtp-unit" id="relocMtpEndMin" onclick="event.stopPropagation();selectRelocUnit('end', 'minute')">00</span>
                </div>
            </div>
        </div>
        <div class="mtp-error" id="relocMtpError"><i class="bi bi-exclamation-triangle"></i> <span></span></div>
        <div class="mtp-clock-wrap">
            <div class="mtp-clock" id="relocMtpClock">
                <svg class="mtp-hand-svg" viewBox="0 0 100 100" aria-hidden="true">
                    <line class="mtp-hand" id="relocMtpHand" x1="50" y1="50" x2="50" y2="15" vector-effect="non-scaling-stroke" />
                </svg>
                <div class="mtp-center-dot"></div>
            </div>
        </div>
        <div class="mtp-footer">
            <div class="mtp-actions">
                <button type="button" class="mtp-btn mtp-btn-cancel" onclick="closeRelocTimePicker(true)">Batal</button>
                <button type="button" class="mtp-btn mtp-btn-ok" onclick="applyRelocTimePicker()">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // ============================================================
    // PICKER JAM (khusus modal Alihkan) — logika identik dengan
    // picker di halaman Tambah/Edit Peminjaman, cuma di-namespace
    // "reloc" supaya ID-nya tidak pernah bentrok.
    // ============================================================
    var RELOC_WORK_START = 7;
    var RELOC_WORK_END = 16;
    var relocMtpState = {
        activeField: 'start',
        mode: 'hour',
        start: { h: 7, m: 0 },
        end: { h: 8, m: 0 }
    };

    function pad2(n) { return String(n).padStart(2, '0'); }
    function minsOf(t) { return t.h * 60 + t.m; }
    function isValidOperational(t) { var m = minsOf(t); return m >= RELOC_WORK_START * 60 && m <= RELOC_WORK_END * 60; }
    function parseLabel(str) { var p = String(str).trim().split(':'); return { h: parseInt(p[0], 10) || 7, m: parseInt((p[1] || '0').split(/\s/)[0], 10) || 0 }; }

    window.openRelocTimePicker = function () {
        var label = document.getElementById('relocTimeLabel').textContent.split(/[–-]/);
        relocMtpState.start = parseLabel(label[0] || '07:00');
        relocMtpState.end = parseLabel(label[1] || '08:00');

        if (!isValidOperational(relocMtpState.start)) relocMtpState.start = { h: RELOC_WORK_START, m: 0 };
        if (!isValidOperational(relocMtpState.end)) relocMtpState.end = { h: RELOC_WORK_END, m: 0 };

        relocMtpState.activeField = 'start';
        relocMtpState.mode = 'hour';
        document.getElementById('relocMtpOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        renderRelocClock();
        updateRelocDigitalDisplay();
        hideRelocMtpError();
    };

    window.closeRelocTimePicker = function () {
        document.getElementById('relocMtpOverlay').classList.remove('open');
        document.body.style.overflow = '';
        hideRelocMtpError();
    };

    window.setRelocActiveField = function (field) {
        relocMtpState.activeField = field;
        relocMtpState.mode = 'hour';
        renderRelocClock();
        updateRelocDigitalDisplay();
        hideRelocMtpError();
    };

    window.selectRelocUnit = function (field, mode) {
        relocMtpState.activeField = field;
        relocMtpState.mode = mode;
        renderRelocClock();
        updateRelocDigitalDisplay();
        hideRelocMtpError();
    };

    function isRelocDisabled(h, m) {
        if (relocMtpState.activeField === 'end') {
            var startMins = minsOf(relocMtpState.start);
            var currentMins = h * 60 + m;
            return currentMins <= startMins;
        }
        return false;
    }

    function selectRelocHour(h24) {
        if (isRelocDisabled(h24, 0) && isRelocDisabled(h24, 30)) return;
        var t = relocMtpState[relocMtpState.activeField];
        t.h = h24;
        hideRelocMtpError();
        relocMtpState.mode = 'minute';
        renderRelocClock();
        updateRelocDigitalDisplay();
    }

    function selectRelocMinute(m) {
        if (isRelocDisabled(relocMtpState[relocMtpState.activeField].h, m)) return;
        var t = relocMtpState[relocMtpState.activeField];
        t.m = m;
        hideRelocMtpError();
        updateRelocDigitalDisplay();

        if (relocMtpState.activeField === 'start') {
            relocMtpState.activeField = 'end';
            relocMtpState.mode = 'hour';
            if (minsOf(relocMtpState.end) <= minsOf(relocMtpState.start)) {
                var newEndMins = minsOf(relocMtpState.start) + 30;
                if (newEndMins > RELOC_WORK_END * 60) newEndMins = RELOC_WORK_END * 60;
                relocMtpState.end = { h: Math.floor(newEndMins / 60), m: newEndMins % 60 };
            }
        }

        renderRelocClock();
        updateRelocDigitalDisplay();
    }

    function renderRelocClock() {
        var clock = document.getElementById('relocMtpClock');
        clock.querySelectorAll('.mtp-num').forEach(function (n) { n.remove(); });

        var t = relocMtpState[relocMtpState.activeField];
        var R = 38.5;
        var C = 50;

        if (relocMtpState.mode === 'hour') {
            var hours = [];
            for (var h = RELOC_WORK_START; h <= RELOC_WORK_END; h++) hours.push(h);
            var totalHours = hours.length;
            var angleStep = 360 / totalHours;
            var offset = hours.indexOf(12);

            hours.forEach(function (h, i) {
                var angle = ((i - offset) * angleStep - 90) * Math.PI / 180;
                var x = C + R * Math.cos(angle);
                var y = C + R * Math.sin(angle);
                var el = document.createElement('div');
                el.className = 'mtp-num';
                el.textContent = pad2(h);
                el.style.left = x + '%';
                el.style.top = y + '%';

                var h00Disabled = isRelocDisabled(h, 0);
                var h30Disabled = isRelocDisabled(h, 30);

                if (h00Disabled && h30Disabled) {
                    el.classList.add('disabled');
                } else {
                    el.addEventListener('click', function () { selectRelocHour(h); });
                    if (t.h === h) el.classList.add('selected');
                }
                clock.appendChild(el);
            });

            var selIdx = hours.indexOf(t.h);
            if (selIdx !== -1) {
                var handAngle = ((selIdx - offset) * angleStep - 90) * Math.PI / 180;
                setRelocHand(C + 35 * Math.cos(handAngle), C + 35 * Math.sin(handAngle));
            }
        } else {
            var minutes = [0, 30];
            minutes.forEach(function (m, i) {
                var angle = (i * 180 - 90) * Math.PI / 180;
                var x = C + R * Math.cos(angle);
                var y = C + R * Math.sin(angle);
                var el = document.createElement('div');
                el.className = 'mtp-num';
                el.textContent = pad2(m);
                el.style.left = x + '%';
                el.style.top = y + '%';

                if (isRelocDisabled(t.h, m)) {
                    el.classList.add('disabled');
                } else {
                    el.addEventListener('click', function () { selectRelocMinute(m); });
                    if (t.m === m) el.classList.add('selected');
                }
                clock.appendChild(el);
            });

            var selIdxM = minutes.indexOf(t.m);
            if (selIdxM !== -1) {
                var handAngleM = (selIdxM * 180 - 90) * Math.PI / 180;
                setRelocHand(C + 35 * Math.cos(handAngleM), C + 35 * Math.sin(handAngleM));
            }
        }
    }

    function setRelocHand(x, y) {
        var hand = document.getElementById('relocMtpHand');
        hand.setAttribute('x2', x);
        hand.setAttribute('y2', y);
    }

    function updateRelocDigitalDisplay() {
        var s = relocMtpState.start, e = relocMtpState.end;
        document.getElementById('relocMtpStartHour').textContent = pad2(s.h);
        document.getElementById('relocMtpStartMin').textContent = pad2(s.m);
        document.getElementById('relocMtpEndHour').textContent = pad2(e.h);
        document.getElementById('relocMtpEndMin').textContent = pad2(e.m);

        document.querySelectorAll('#relocMtpOverlay .mtp-field').forEach(function (f) {
            f.classList.toggle('active', f.dataset.field === relocMtpState.activeField);
        });

        var prefix = relocMtpState.activeField === 'start' ? 'relocMtpStart' : 'relocMtpEnd';
        var hourEl = document.getElementById(prefix + 'Hour');
        var minEl = document.getElementById(prefix + 'Min');
        document.querySelectorAll('#relocMtpOverlay .mtp-digital .mtp-unit').forEach(function (u) { u.classList.remove('mode-active'); });
        if (relocMtpState.mode === 'hour' && hourEl) hourEl.classList.add('mode-active');
        if (relocMtpState.mode === 'minute' && minEl) minEl.classList.add('mode-active');
    }

    function showRelocMtpError(msg) {
        var err = document.getElementById('relocMtpError');
        err.querySelector('span').textContent = msg;
        err.classList.add('show');
    }
    function hideRelocMtpError() {
        document.getElementById('relocMtpError').classList.remove('show');
    }

    window.applyRelocTimePicker = function () {
        if (!isValidOperational(relocMtpState.start) || !isValidOperational(relocMtpState.end)) {
            showRelocMtpError('Waktu harus dalam rentang 07:00–16:00.');
            return;
        }
        if (minsOf(relocMtpState.start) >= minsOf(relocMtpState.end)) {
            showRelocMtpError('Jam mulai harus lebih awal dari jam selesai.');
            return;
        }

        var startLabel = pad2(relocMtpState.start.h) + ':' + pad2(relocMtpState.start.m);
        var endLabel = pad2(relocMtpState.end.h) + ':' + pad2(relocMtpState.end.m);

        document.getElementById('relocTimeLabel').textContent = startLabel + ' – ' + endLabel;
        closeRelocTimePicker();
        syncRelocDateTimeFields();
    };

    // ============================================================
    // SINKRON tanggal + jam terpilih -> field datetime penuh yang
    // benar-benar dikirim ke server, lalu refresh daftar ruangan
    // yang kosong pada rentang waktu tersebut.
    // ============================================================
    function syncRelocDateTimeFields() {
        var date = document.getElementById('relocDate').value;
        var label = document.getElementById('relocTimeLabel').textContent.split(/[–-]/);
        if (!date || label.length < 2) return;

        var startTime = label[0].trim();
        var endTime = label[1].trim();

        document.getElementById('relocStartTimeField').value = date + ' ' + startTime + ':00';
        document.getElementById('relocEndTimeField').value = date + ' ' + endTime + ':00';

        fetchAvailableRooms();
    }

    // ============================================================
    // AMBIL DAFTAR RUANGAN KOSONG — live, sesuai tanggal & jam yang
    // sedang dipilih. Ruangan yang sudah terpakai tidak ditampilkan
    // sama sekali (bukan cuma di-disable).
    // ============================================================
    var relocFetchToken = 0;
    function fetchAvailableRooms() {
        var select = document.getElementById('relocRoomSelect');
        var hint = document.getElementById('relocRoomHint');
        var startTime = document.getElementById('relocStartTimeField').value;
        var endTime = document.getElementById('relocEndTimeField').value;
        var currentToken = ++relocFetchToken;

        select.disabled = true;
        select.innerHTML = '<option value="">Memuat ruangan tersedia...</option>';
        hint.classList.remove('empty');
        hint.querySelector('span').textContent = 'Mencari ruangan yang kosong...';

        fetch('{{ route("bookings.available-rooms") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                start_time: startTime,
                end_time: endTime,
                exclude_booking_id: {{ $booking->id }}
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (currentToken !== relocFetchToken) return; // respons basi (sudah ganti waktu lagi), abaikan

            var rooms = data.rooms || [];
            select.innerHTML = '';

            if (rooms.length === 0) {
                select.innerHTML = '<option value="">Tidak ada ruangan kosong</option>';
                select.disabled = true;
                hint.classList.add('empty');
                hint.querySelector('span').textContent = 'Tidak ada ruangan kosong pada waktu ini. Coba jam atau tanggal lain.';
                return;
            }

            select.appendChild(new Option('Pilih ruangan', ''));
            rooms.forEach(function (r) {
                select.appendChild(new Option(r.name + ' · Kapasitas ' + r.capacity + ' orang', r.id));
            });
            select.disabled = false;
            hint.classList.remove('empty');
            hint.querySelector('span').textContent = 'Hanya menampilkan ruangan yang kosong pada waktu terpilih.';
        })
        .catch(function () {
            if (currentToken !== relocFetchToken) return;
            select.innerHTML = '<option value="">Gagal memuat ruangan</option>';
            select.disabled = true;
            hint.classList.add('empty');
            hint.querySelector('span').textContent = 'Gagal memeriksa ketersediaan ruangan. Coba lagi.';
        });
    }

    document.getElementById('relocDate').addEventListener('change', syncRelocDateTimeFields);

    // Saat modal dibuka, langsung sinkron & muat daftar ruangan sesuai
    // waktu default (waktu booking ini saat ini).
    document.getElementById('relocateModal').addEventListener('show.bs.modal', function () {
        syncRelocDateTimeFields();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('relocMtpOverlay').classList.contains('open')) {
            closeRelocTimePicker(true);
        }
    });
})();
</script>
@endif



@if($booking->status == 1 && $booking->absent_code)
<script>
    function renderMeetingQr(absentCode) {
        var container = document.getElementById("qrCodeContainer");
        if (!container || !absentCode) return;
        container.innerHTML = ''; // container selalu kosong tiap kali di-render ulang, aman dipanggil berkali-kali
        var url = "{{ url('/booking/meet') }}/" + absentCode;
        new QRCode(container, {
            text: url,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderMeetingQr("{{ $booking->absent_code }}");
    });
</script>
@else
<script>
    function renderMeetingQr(absentCode) {
        var container = document.getElementById("qrCodeContainer");
        if (!container || !absentCode) return;
        container.innerHTML = '';
        var url = "{{ url('/booking/meet') }}/" + absentCode;
        new QRCode(container, {
            text: url, width: 200, height: 200,
            colorDark: "#000000", colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }
</script>
@endif

{{-- ============================================================
     LIVE UPDATE — polling berkala, TANPA reload manual.
     Halaman ini tidak pakai WebSocket/broadcasting (di server belum
     ada infrastrukturnya — BROADCAST_DRIVER masih 'null'), jadi
     dipakai polling ringan lewat modul bersama public/js/live-poll.js
     (idle-aware: otomatis berhenti kalau halaman dibiarkan lama tanpa
     disentuh atau sesi sudah habis — supaya tidak membebani server
     sia-sia). Cukup untuk kebutuhan aplikasi internal seperti ini.
     ============================================================ --}}
<script>
(function () {
    var lastUpdatedAt = {{ optional($booking->updated_at)->timestamp ?? 'null' }};

    function showLiveToast() {
        var existing = document.getElementById('liveUpdateToast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.id = 'liveUpdateToast';
        toast.innerHTML = '<i class="bi bi-arrow-repeat"></i> Halaman diperbarui otomatis';
        toast.style.cssText = 'position:fixed; bottom:24px; left:50%; transform:translateX(-50%);' +
            'background:var(--text-primary); color:#fff; padding:10px 18px; border-radius:999px;' +
            'font-size:13px; font-weight:600; z-index:9999; box-shadow:0 8px 24px rgba(0,0,0,0.25);' +
            'display:flex; align-items:center; gap:8px; opacity:0; transition:opacity 0.25s;';
        document.body.appendChild(toast);
        requestAnimationFrame(function () { toast.style.opacity = '1'; });
        setTimeout(function () {
            toast.style.opacity = '0';
            setTimeout(function () { toast.remove(); }, 300);
        }, 2500);
    }

    createLivePoller({
        url: "{{ route('bookings.live', $booking->id) }}",
        interval: 15000,
        shouldPause: function () { return document.querySelector('.modal.show') !== null; },
        onUpdate: function (data) {
            // Tidak ada perubahan sejak polling terakhir — tidak perlu
            // menukar DOM sama sekali (hemat, dan hindari kedip visual).
            if (data.updated_at === lastUpdatedAt) return;
            lastUpdatedAt = data.updated_at;

            var topArea = document.getElementById('liveTopArea');
            var sideCol = document.getElementById('liveSideCol');
            if (topArea) topArea.outerHTML = data.top;
            if (sideCol) sideCol.outerHTML = data.side;

            if (data.has_qr) {
                renderMeetingQr(data.absent_code);
            }

            showLiveToast();
        }
    });
})();
</script>
@endsection