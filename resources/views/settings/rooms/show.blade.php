@extends('templates.template')

@section('title', 'Detail Ruangan: ' . $room->name)
@section('page_title', 'Manajemen Ruang Rapat')

@section('content')
<style>
    /* ============================================
       PAGE HEADER / TOOLBAR
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
    .btn-danger-outline {
        height: 44px; padding: 0 var(--space-5);
        border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; gap: 8px;
        text-decoration: none; cursor: pointer; transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-back {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .btn-primary-sm {
        background: var(--brand-gradient); border: none; color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25); }

    .btn-outline-sm {
        background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);
    }
    .btn-outline-sm:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .btn-danger-outline {
        background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); color: #dc2626;
    }
    .btn-danger-outline:hover { background: rgba(239, 68, 68, 0.1); }

    .btn-add-modal {
        padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-sm);
        background: var(--brand-orange); color: white; border: none; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s; height: 32px;
    }
    .btn-add-modal:hover { background: var(--brand-orange-dark); }

    /* ============================================
       ROOM HERO CARD
    ============================================ */
    .room-hero-card {
        display: flex;
        align-items: flex-start;
        gap: var(--space-4);
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: 1.25rem 1.5rem;
        margin-bottom: var(--space-6);
        position: relative;
        overflow: hidden;
    }
    .room-hero-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 5px;
        background: var(--brand-gradient);
    }
    .room-hero-icon {
        width: 56px; height: 56px; flex-shrink: 0;
        border-radius: 14px;
        background: var(--brand-gradient);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 6px 16px rgba(249, 115, 22, 0.25);
        margin-top: 2px;
    }
    .room-hero-text { min-width: 0; flex: 1; }
    .room-title {
        font-size: var(--font-size-xl);
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.02em;
        color: var(--text-primary);
        line-height: 1.25;
        text-transform: none;
    }
    .room-hero-description {
        margin: 6px 0 0 0;
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
        line-height: 1.6;
    }

    /* ============================================
       DETAIL GRID 2 KOLOM
    ============================================ */
    .detail-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: var(--space-6);
        align-items: start;
    }

    .section-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: 1rem 1.5rem 1.5rem;
        margin-bottom: var(--space-6);
    }
    .section-card:last-child { margin-bottom: 0; }

    /*
      FIX (capitalization):
      This section previously rendered in ALL CAPS ("GALERI FOTO",
      "INFORMASI DASAR", "FASILITAS RUANGAN") even though the markup below
      contains normal Title Case text ("Galeri Foto", "Informasi Dasar",
      "Fasilitas Ruangan"). That means a *global* stylesheet (most likely
      inside templates/template.blade.php, a shared app.css, or a Bootstrap
      utility class bleeding onto h5/.card-title-like selectors) is applying
      `text-transform: uppercase` with higher/equal specificity and this
      partial view has no local rule to counter it.

      Root-causing that external stylesheet requires the layout/app CSS
      file, which isn't part of this view. The safe, deterministic fix that
      does not require touching a file we don't have visibility into is to
      explicitly pin text-transform locally with !important, so this
      component is immune to whatever global rule is upstream.
    */
    .section-title {
        font-size: 1.05rem; font-weight: 700;
        margin: -0.5rem 0 1rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color-light);
        display: flex; align-items: center; justify-content: space-between;
        color: var(--text-primary);
        text-transform: none !important;
    }
    .section-title span {
        display: flex; align-items: center; gap: 8px;
        text-transform: none !important;
    }
    .section-title i {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(249, 115, 22, 0.1);
        color: var(--brand-orange);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
    }

    .gallery-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
    .gallery-item {
        position: relative; aspect-ratio: 1; border-radius: var(--radius-sm);
        overflow: hidden; border: 1px solid var(--border-color-light);
    }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-item .delete-photo-btn {
        position: absolute; top: 4px; right: 4px; width: 28px; height: 28px;
        background: rgba(239, 68, 68, 0.9); color: white; border: none;
        border-radius: 50%; cursor: pointer; display: flex; align-items: center;
        justify-content: center; font-size: 0.8rem; opacity: 0; transition: opacity 0.2s;
    }
    .gallery-item:hover .delete-photo-btn { opacity: 1; }
    .gallery-item .delete-photo-btn:disabled { opacity: 1; cursor: wait; }
    .no-photos {
        grid-column: 1/-1; text-align: center; padding: 2rem; color: var(--text-muted);
        background: var(--bg-input); border-radius: var(--radius-sm); border: 2px dashed var(--border-color);
    }
    .no-photos i { color: var(--text-muted); }

    /* ============================================
       INFORMASI DASAR
    ============================================ */
    .info-grid { display: flex; flex-direction: column; gap: 10px; }
    .info-item {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 14px;
        background: var(--bg-input);
        border-radius: var(--radius-sm);
    }
    .info-item-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem; flex-shrink: 0;
    }
    .info-item-icon.icon-blue { background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); }
    .info-item-icon.icon-orange { background: rgba(249, 115, 22, 0.1); color: var(--brand-orange); }
    .info-item-icon.icon-green { background: rgba(16, 185, 129, 0.1); color: var(--brand-green); }
    .info-item-icon.icon-red { background: rgba(239, 68, 68, 0.1); color: #dc2626; }

    .info-item-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .info-item-label {
        font-size: 0.7rem; font-weight: 700; color: var(--text-muted);
        letter-spacing: 0.4px;
    }
    .info-item-value {
        font-weight: 600; color: var(--text-primary); font-size: 0.9rem;
    }

    .status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 999px;
        font-size: 0.8rem; font-weight: 700;
    }
    .status-pill.active { background: rgba(16, 185, 129, 0.12); color: var(--brand-green); }
    .status-pill.inactive { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .facility-list-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; background: var(--bg-input); border-radius: var(--radius-sm);
        margin-bottom: 8px;
    }
    .facility-list-item:last-child { margin-bottom: 0; }
    .facility-info { display: flex; align-items: center; gap: 12px; }
    .facility-icon { width: 36px; height: 36px; background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .facility-name { font-weight: 600; color: var(--text-primary); font-size: 0.9rem; }
    .facility-qty { font-size: 0.78rem; color: var(--text-muted); }

    /* MODAL & FORMS */
    .modern-modal .modal-dialog { max-width: 700px; }
    .modern-modal .modal-content { border: 1px solid var(--border-color-light); border-radius: var(--radius-card); overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
    .modern-modal .modal-header { background: var(--bg-card); border-bottom: 1px solid var(--border-color-light); padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
    .modern-modal .modal-title { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 10px; text-transform: none !important; }
    .modern-modal .modal-title i { color: var(--brand-orange); font-size: 1.5rem; }
    .modern-modal .btn-close { background: none; border: none; font-size: 1.5rem; color: var(--text-muted); opacity: 0.7; transition: opacity 0.2s; }
    .modern-modal .btn-close:hover { opacity: 1; }
    .modern-modal .modal-body { padding: 1.5rem; }
    .modern-modal .modal-footer { border-top: 1px solid var(--border-color-light); padding: 1.25rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; }

    .modern-input { width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-input); font-size: 0.9rem; color: var(--text-primary); transition: all 0.2s; outline: none; }
    .modern-input:focus { border-color: var(--brand-orange); background: var(--bg-card); box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12); }
    textarea.modern-input { height: auto; padding: 10px 14px; }
    .form-label-modern { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }

    .btn-modal-cancel { height: 42px; padding: 0 20px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-secondary); font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-modal-cancel:hover { background: var(--bg-hover); }
    .btn-modal-submit { height: 42px; padding: 0 24px; border-radius: var(--radius-sm); border: none; background: var(--brand-gradient); color: white; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(249,115,22,0.2); display: inline-flex; align-items: center; gap: 6px; }
    .btn-modal-submit:hover { box-shadow: 0 6px 16px rgba(16,185,129,0.25); transform: translateY(-1px); }
    .btn-modal-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .upload-zone { border: 2px dashed var(--border-color); background: var(--bg-input); border-radius: var(--radius-sm); padding: 2rem 1rem; text-align: center; cursor: pointer; transition: all 0.3s ease; position: relative; }
    .upload-zone:hover, .upload-zone.dragover { border-color: var(--brand-orange); background: rgba(249, 115, 22, 0.04); }
    .upload-icon { font-size: 2.5rem; color: var(--brand-orange); margin-bottom: 0.5rem; display: block; }
    .upload-text { font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.9rem; }
    .upload-subtext { font-size: 0.75rem; color: var(--text-muted); }
    .uploaded-files-list { margin-top: 1rem; display: flex; flex-direction: column; gap: 8px; }
    .uploaded-file-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: var(--bg-card); border: 1px solid var(--border-color-light); border-radius: var(--radius-xs); }
    .file-info { display: flex; align-items: center; gap: 10px; overflow: hidden; }
    .file-icon { width: 32px; height: 32px; border-radius: 6px; background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .file-details h6 { margin: 0; font-size: 0.8rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; max-width: 250px; overflow: hidden; text-overflow: ellipsis; }
    .file-details small { font-size: 0.7rem; color: var(--text-muted); }
    .btn-remove-file { background: transparent; border: none; color: #dc2626; font-size: 1.1rem; cursor: pointer; padding: 0 4px; transition: transform 0.2s; flex-shrink: 0; }
    .btn-remove-file:hover { transform: scale(1.2); }

    .manage-fac-list { max-height: 300px; overflow-y: auto; padding-right: 5px; }
    .manage-fac-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: white; border: 1px solid var(--border-color-light); border-radius: var(--radius-sm); margin-bottom: 8px; }
    .manage-fac-qty-input { width: 60px; height: 34px; text-align: center; padding: 0 !important; font-size: 0.85rem !important; }

    /* TOAST */
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

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 991.98px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 767.98px) {
        .db-toolbar { flex-wrap: wrap; }
        .db-toolbar .actions { width: 100%; }
        .db-toolbar .actions .btn-outline-sm,
        .db-toolbar .actions .btn-danger-outline,
        .db-toolbar .actions form { flex: 1; }
        .db-toolbar .actions .btn-danger-outline { width: 100%; justify-content: center; }
        .room-hero-card { flex-wrap: wrap; }
        .room-hero-icon { width: 48px; height: 48px; font-size: 1.3rem; }
        .room-title { font-size: 1.25rem; }
        .room-hero-description { font-size: 0.85rem; }
        .section-title { font-size: 1rem; }
        .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
        .modern-modal .modal-dialog { margin: 1rem; }
    }
    @media (max-width: 575.98px) {
        .db-toolbar .actions { flex-direction: column; gap: 8px; }
        .db-toolbar .actions .btn-outline-sm,
        .db-toolbar .actions .btn-danger-outline { width: 100%; justify-content: center; }
        .info-item { flex-direction: column; align-items: flex-start; gap: 6px; }
        .info-item-icon { width: 36px; height: 36px; }
        .info-item-value { font-size: 0.85rem; }
        .manage-fac-item { flex-direction: column; align-items: stretch; gap: 10px; }
        .manage-fac-qty-input { width: 100%; }
        .d-flex.gap-2 { flex-direction: column; }
        .d-flex.gap-2 > * { width: 100%; }
    }
</style>

<div class="db-toolbar">
    <a href="{{ route('settings.rooms.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div class="actions">
        <button type="button" class="btn-outline-sm" data-bs-toggle="modal" data-bs-target="#editRoomModal">
            <i class="bi bi-pencil-square"></i> Edit Informasi
        </button>
        <form action="{{ route('settings.rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Yakin hapus ruangan ini secara permanen?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger-outline">
                <i class="bi bi-trash"></i> Hapus Ruangan
            </button>
        </form>
    </div>
</div>

{{-- ROOM HERO CARD --}}
<div class="room-hero-card">
    <div class="room-hero-text">
        <h1 class="room-title">{{ $room->name }}</h1>
        <p class="room-hero-description">{{ $room->location }} &middot; Kapasitas {{ $room->capacity }} orang</p>
    </div>
</div>

<div class="detail-grid">
    {{-- Kolom Kiri: Galeri Foto -> Informasi Dasar --}}
    <div>
        <div class="section-card">
            <div class="section-title">
                <span><i class="bi bi-images"></i> Galeri Foto</span>
                <button class="btn-add-modal" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                    Tambah Foto
                </button>
            </div>
            <div class="gallery-grid" id="galleryGrid">
                @php
                    $validPhotos = $room->photos->filter(function($p) { return $p->photo_url !== null; });
                @endphp
                @if($validPhotos->count() > 0)
                    @foreach($validPhotos as $photo)
                        {{-- FIX: this used to be a real <form method="POST"> that the browser
                             submitted natively. Since nothing intercepted its submit event,
                             a successful delete navigated the whole tab to the JSON endpoint
                             (you literally saw the raw {"success":true,...} response).
                             It is now a plain button wired to deletePhoto() via fetch(),
                             consistent with every other AJAX action in this page. --}}
                        <div class="gallery-item" data-photo-id="{{ $photo->id }}">
                            <img src="{{ $photo->photo_url }}" alt="Foto {{ $room->name }}">
                            <button type="button" class="delete-photo-btn" title="Hapus Foto" onclick="deletePhoto({{ $photo->id }}, this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="no-photos">
                        <i class="bi bi-image d-block fs-4 mb-2"></i>
                        Belum ada foto. Klik "Tambah Foto" untuk mengunggah.
                    </div>
                @endif
            </div>
        </div>

        <div class="section-card">
            <div class="section-title">
                <span><i class="bi bi-info-circle"></i> Informasi Dasar</span>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item-icon icon-blue"><i class="bi bi-people-fill"></i></div>
                    <div class="info-item-text">
                        <span class="info-item-label">Kapasitas</span>
                        <span class="info-item-value">{{ $room->capacity }} Orang</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon icon-orange"><i class="bi bi-geo-alt-fill"></i></div>
                    <div class="info-item-text">
                        <span class="info-item-label">Lokasi</span>
                        <span class="info-item-value">{{ $room->location }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon {{ $room->status == 1 ? 'icon-green' : 'icon-red' }}">
                        <i class="bi {{ $room->status == 1 ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                    </div>
                    <div class="info-item-text">
                        <span class="info-item-label">Status</span>
                        @if($room->status == 1)
                            <span class="status-pill active"><span class="dot"></span> Aktif</span>
                        @else
                            <span class="status-pill inactive"><span class="dot"></span> Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Fasilitas Ruangan --}}
    <div>
        <div class="section-card">
            <div class="section-title">
                <span><i class="bi bi-tools"></i> Fasilitas Ruangan</span>
                <button class="btn-add-modal" data-bs-toggle="modal" data-bs-target="#manageFacilityModal">
                    Kelola
                </button>
            </div>
            <div id="roomFacilitiesList">
                @if($room->facilities->count() > 0)
                    @foreach($room->facilities as $f)
                        <div class="facility-list-item">
                            <div class="facility-info">
                                <div class="facility-icon"><i class="bi bi-box"></i></div>
                                <div>
                                    <div class="facility-name">{{ $f->name }}</div>
                                    <div class="facility-qty">Jumlah: {{ $f->pivot->quantity }} unit</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-photos" style="padding: 1.5rem;">
                        <i class="bi bi-inbox d-block fs-4 mb-2"></i>
                        Belum ada fasilitas terpasang.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL: EDIT INFORMASI RUANGAN              --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="editRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Informasi Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <form action="{{ route('settings.rooms.update', $room->id) }}" method="POST" id="editRoomForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label-modern">Nama Ruangan</label>
                            <input type="text" name="name" class="modern-input" value="{{ $room->name }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label-modern">Kapasitas</label>
                            <input type="number" name="capacity" class="modern-input" value="{{ $room->capacity }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-modern">Lokasi</label>
                        <input type="text" name="location" class="modern-input" value="{{ $room->location }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-modern">Status</label>
                        <select name="status" class="modern-input">
                            <option value="1" {{ $room->status == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $room->status == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal-submit"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL: UPLOAD FOTO RUANGAN (AJAX)          --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cloud-arrow-up"></i> Upload Foto Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="upload-zone" id="dropzoneUpload" onclick="document.getElementById('fileInputUpload').click()">
                    <i class="bi bi-cloud-arrow-up upload-icon"></i>
                    <div class="upload-text">Click To Upload</div>
                    <div class="upload-subtext">atau drag & drop file kesini (JPG, PNG, Max 2MB)</div>
                    <input type="file" name="photos[]" id="fileInputUpload" multiple accept="image/*" style="display: none;">
                </div>
                <div class="uploaded-files-list" id="uploadedFilesListUpload"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-modal-submit" onclick="submitPhotos()"><i class="bi bi-upload me-1"></i> Upload Foto</button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL: KELOLA FASILITAS RUANGAN (AJAX)     --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="manageFacilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-tools"></i> Kelola Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                    <label class="form-label-modern" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tambah Fasilitas</label>
                    <div class="d-flex gap-2">
                        <select id="addFacilitySelect" class="modern-input" style="flex: 2;">
                            <option value="">Pilih Fasilitas...</option>
                            @foreach($availableFacilities as $af)
                                <option value="{{ $af->id }}">{{ $af->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" id="addFacilityQty" class="modern-input" placeholder="Qty" min="1" value="1" style="flex: 0.5;">
                        <button type="button" class="btn-modal-submit" onclick="addFacilityTemp()" style="width: 44px; padding: 0; justify-content: center;">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <label class="form-label-modern" style="font-size: 0.8rem; letter-spacing: 0.5px;">Fasilitas Terpasang</label>
                <div class="manage-fac-list" id="manageFacilityList">
                    {{-- Diisi via JS --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-modal-submit" onclick="saveFacilityChanges()" id="saveFacilityBtn">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
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
            setTimeout(() => {
                dismissToast(toast);
            }, duration);
        }
    }

    function dismissToast(toast) {
        if (!toast) return;
        toast.style.animation = 'toastSlideOut 0.3s ease forwards';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 300);
    }

    // ============================================
    // SCRIPT EDIT INFO (AJAX)
    // ============================================
    document.getElementById('editRoomForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => {
            if (!r.ok) return r.json().then(err => { throw err; });
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showToast('success', 'Berhasil!', data.message || 'Informasi ruangan diperbarui.', 3000);
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showToast('error', 'Gagal', data.message || 'Gagal memperbarui informasi.', 5000);
            }
        })
        .catch(err => {
            if (err && err.errors) {
                let errorMessages = Object.values(err.errors).flat();
                showToast('error', 'Validasi Gagal', errorMessages.join('<br>'), 6000);
            } else {
                showToast('error', 'Error', 'Terjadi kesalahan.', 5000);
            }
        });
    });

    // ============================================
    // SCRIPT HAPUS FOTO (AJAX)
    // ------------------------------------------------
    // FIX: previously a real <form method="POST"> with @method('DELETE')
    // and no JS listener. A "real" form submit is a full browser
    // navigation — nothing prevented the default action, so on success
    // the tab navigated straight to the JSON endpoint and rendered the
    // raw {"success":true,...} payload (exactly what your screenshot
    // showed). Fixed by using fetch() with a real DELETE verb (safe,
    // since we're not submitting an HTML <form> — fetch supports DELETE
    // natively, no method-spoofing hidden input needed) and removing
    // just that gallery tile from the DOM instead of reloading/navigating.
    // ============================================
    const roomId = {{ $room->id }};
    const routeDeletePhoto = "{{ route('settings.rooms.delete-photo', $room->id) }}";

    function deletePhoto(photoId, btnEl) {
        if (!confirm('Hapus foto ini?')) return;

        btnEl.disabled = true;

        fetch(routeDeletePhoto, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ photo_id: photoId })
        })
        .then(r => {
            if (!r.ok) return r.json().then(err => { throw err; });
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showToast('success', 'Berhasil!', data.message || 'Foto berhasil dihapus.', 3000);
                const item = btnEl.closest('.gallery-item');
                if (item) item.remove();

                const grid = document.getElementById('galleryGrid');
                if (grid && grid.querySelectorAll('.gallery-item').length === 0) {
                    grid.innerHTML = `
                        <div class="no-photos">
                            <i class="bi bi-image d-block fs-4 mb-2"></i>
                            Belum ada foto. Klik "Tambah Foto" untuk mengunggah.
                        </div>`;
                }
            } else {
                showToast('error', 'Gagal', data.message || 'Gagal menghapus foto.', 5000);
                btnEl.disabled = false;
            }
        })
        .catch(err => {
            showToast('error', 'Error', (err && err.message) ? err.message : 'Terjadi kesalahan jaringan atau server.', 5000);
            btnEl.disabled = false;
        });
    }

    // ============================================
    // SCRIPT UPLOAD FOTO (AJAX)
    // ============================================
    const dropzoneUpload = document.getElementById('dropzoneUpload');
    const fileInputUpload = document.getElementById('fileInputUpload');
    const uploadedFilesListUpload = document.getElementById('uploadedFilesListUpload');
    let filesToUpload = [];

    fileInputUpload.addEventListener('change', function(e) {
        handleFilesUpload(e.target.files);
        fileInputUpload.value = '';
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzoneUpload.addEventListener(eventName, preventDefaults, false);
    });
    function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneUpload.addEventListener(eventName, () => dropzoneUpload.classList.add('dragover'), false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneUpload.addEventListener(eventName, () => dropzoneUpload.classList.remove('dragover'), false);
    });
    dropzoneUpload.addEventListener('drop', function(e) {
        handleFilesUpload(e.dataTransfer.files);
    }, false);

    function handleFilesUpload(files) {
        [...files].forEach(file => {
            if (!file.type.match('image.*')) { showToast('error', 'Format Salah', `${file.name} bukan gambar.`); return; }
            if (filesToUpload.some(f => f.name === file.name && f.size === file.size)) { showToast('info', 'Duplikat', `${file.name} sudah ada.`); return; }
            filesToUpload.push(file);
            renderFileItemUpload(file);
        });
    }

    function renderFileItemUpload(file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileItem = document.createElement('div');
        fileItem.className = 'uploaded-file-item';
        fileItem.innerHTML = `
            <div class="file-info">
                <div class="file-icon"><i class="bi bi-file-earmark-image"></i></div>
                <div class="file-details">
                    <h6>${file.name}</h6>
                    <small>${fileSize} MB</small>
                </div>
            </div>
            <button type="button" class="btn-remove-file" onclick="removeFileUpload(this, '${file.name}', ${file.size})">
                <i class="bi bi-x"></i>
            </button>
        `;
        uploadedFilesListUpload.appendChild(fileItem);
    }

    function removeFileUpload(btn, name, size) {
        filesToUpload = filesToUpload.filter(f => !(f.name === name && f.size === size));
        btn.closest('.uploaded-file-item').remove();
    }

    function submitPhotos() {
        if (filesToUpload.length === 0) { showToast('info', 'Info', 'Belum ada file dipilih.'); return; }

        let formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('name', '{{ $room->name }}');
        formData.append('capacity', '{{ $room->capacity }}');
        formData.append('location', '{{ $room->location }}');
        formData.append('status', '{{ $room->status }}');

        filesToUpload.forEach(file => {
            formData.append('photos[]', file);
        });

        fetch("{{ route('settings.rooms.update', $room->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                showToast('success', 'Berhasil!', 'Foto berhasil diupload.', 3000);
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showToast('error', 'Gagal', data.message || 'Gagal upload foto.', 5000);
            }
        })
        .catch(error => {
            if (error && error.errors) {
                let errorMessages = Object.values(error.errors).flat();
                showToast('error', 'Validasi Gagal', errorMessages.join('<br>'), 6000);
            } else {
                showToast('error', 'Error', 'Terjadi kesalahan jaringan atau server.', 5000);
                console.error('Error:', error);
            }
        });
    }

    // ============================================
    // SCRIPT KELOLA FASILITAS (BATCH UPDATE AJAX)
    // ------------------------------------------------
    // ROOT CAUSE OF THE 405 (evidenced by DevTools):
    //   PUT http://localhost:8000/settings/facilities/room/8  -> 405 Method Not Allowed
    //
    // The old code built these URLs like this:
    //   route('settings.facilities.room.update', ['room' => ':room', 'facility' => ':fac'])
    // then did `.replace(':room', roomId).replace(':fac', facId)` on the
    // resulting string.
    //
    // The problem: Laravel's route() helper rawurlencode()'s every route
    // parameter VALUE it substitutes. The colon character ':' is NOT in
    // RFC 3986's "unreserved" set, so rawurlencode(':room') actually
    // produces '%3Aroom' (and ':fac' -> '%3Afac', ':id' -> '%3Aid') in the
    // rendered HTML/JS. The client-side `.replace(':room', ...)` then
    // searches for the literal substring ':room', which no longer exists
    // (it was already turned into '%3Aroom' server-side) — so the
    // replacement silently does nothing, and the browser is left calling
    // a malformed/incomplete URL. That's why BOTH "add" (attach) and
    // "update" quantity failed — every one of these calls was built with
    // the same broken placeholder trick.
    //
    // FIX: never round-trip a fake placeholder value through Laravel's
    // route() — it's designed to encode arbitrary values, not preserve
    // literal tokens. Instead:
    //   1. Resolve the room-scoped base URL ONCE using the real room ID
    //      (which IS known at Blade compile time, so route() encodes it
    //      correctly and there's nothing to "replace" afterwards).
    //   2. For facility-scoped operations (update/detach), append the
    //      facility ID as a plain JS template literal segment — no
    //      Laravel involvement, so nothing gets encoded away.
    // ============================================
    let tempFacilities = [];
    const originalFacilities = @json($room->facilities->map(function($f) { return ['id' => $f->id, 'name' => $f->name, 'quantity' => $f->pivot->quantity]; }));

    // Real room ID substituted directly — no fake placeholder, nothing to encode/replace.
    const facilityRoomBaseUrl = "{{ route('settings.facilities.room.attach', $room->id) }}";

    function facilityDetachUrl(facilityId) {
        return `${facilityRoomBaseUrl}/${facilityId}`;
    }
    function facilityUpdateUrl(facilityId) {
        return `${facilityRoomBaseUrl}/${facilityId}`;
    }

    // Inisialisasi state sementara saat modal dibuka
    document.getElementById('manageFacilityModal').addEventListener('shown.bs.modal', function () {
        tempFacilities = JSON.parse(JSON.stringify(originalFacilities));
        renderManageFacilityList();
    });

    function renderManageFacilityList() {
        var container = document.getElementById('manageFacilityList');
        if (tempFacilities.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-inbox d-block fs-4 mb-2"></i>Belum ada fasilitas terpasang.</div>';
            return;
        }

        var html = '';
        tempFacilities.forEach(function(f) {
            html += `
                <div class="manage-fac-item" data-fac-id="${f.id}">
                    <div class="facility-info">
                        <div class="facility-icon"><i class="bi bi-box"></i></div>
                        <div>
                            <div class="facility-name">${f.name}</div>
                            <div class="facility-qty">Jumlah unit</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" value="${f.quantity}" min="1" class="modern-input manage-fac-qty-input" onchange="updateQtyTemp(${f.id}, this.value)">
                        <button class="btn-danger-outline" style="height: 34px; padding: 0 10px; border-radius: 6px;" onclick="detachFacilityTemp(${f.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    // Tambah fasilitas ke state sementara
    function addFacilityTemp() {
        var select = document.getElementById('addFacilitySelect');
        var facId = parseInt(select.value);
        var qty = parseInt(document.getElementById('addFacilityQty').value);
        var facName = select.options[select.selectedIndex].text;

        if (!facId) { showToast('info', 'Info', 'Pilih fasilitas dahulu!'); return; }
        if (!qty || qty < 1) { showToast('info', 'Info', 'Jumlah tidak valid.'); return; }

        if (tempFacilities.some(f => f.id === facId)) {
            showToast('info', 'Info', 'Fasilitas sudah ada di daftar.');
            return;
        }

        tempFacilities.push({ id: facId, name: facName, quantity: qty });
        renderManageFacilityList();

        select.value = '';
        document.getElementById('addFacilityQty').value = '1';
    }

    function updateQtyTemp(facId, qty) {
        var facility = tempFacilities.find(f => f.id === facId);
        if (facility) {
            facility.quantity = parseInt(qty);
        }
    }

    function detachFacilityTemp(facId) {
        tempFacilities = tempFacilities.filter(f => f.id !== facId);
        renderManageFacilityList();
    }

    // Fungsi Simpan Perubahan: Bandingkan temp dengan original, lalu kirim ke server
    async function saveFacilityChanges() {
        const saveBtn = document.getElementById('saveFacilityBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        let hasError = false;
        let errorDetails = [];

        try {
            // 1. Cari yang dihapus (ada di original, tidak ada di temp)
            for (const orig of originalFacilities) {
                if (!tempFacilities.some(t => t.id === orig.id)) {
                    let res = await fetch(facilityDetachUrl(orig.id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) {
                        hasError = true;
                        errorDetails.push(`Hapus "${orig.name}": HTTP ${res.status}`);
                    }
                }
            }

            // 2. Cari yang ditambahkan (tidak ada di original, ada di temp)
            // Dan yang qty-nya berubah
            for (const temp of tempFacilities) {
                const orig = originalFacilities.find(o => o.id === temp.id);

                if (!orig) {
                    // Attach baru — facility_id dikirim di body, roomId sudah ada di URL
                    let res = await fetch(facilityRoomBaseUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ facility_id: temp.id, quantity: temp.quantity })
                    });
                    if (!res.ok) {
                        hasError = true;
                        errorDetails.push(`Tambah "${temp.name}": HTTP ${res.status}`);
                    }
                } else if (orig.quantity !== temp.quantity) {
                    // Update qty
                    let res = await fetch(facilityUpdateUrl(temp.id), {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ quantity: temp.quantity })
                    });
                    if (!res.ok) {
                        hasError = true;
                        errorDetails.push(`Update "${temp.name}": HTTP ${res.status}`);
                    }
                }
            }

            if (!hasError) {
                showToast('success', 'Berhasil!', 'Perubahan fasilitas berhasil disimpan.', 3000);
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showToast('error', 'Gagal', 'Sebagian perubahan gagal disimpan.<br>' + errorDetails.join('<br>'), 7000);
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Perubahan';
            }
        } catch (err) {
            console.error(err);
            showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000);
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Perubahan';
        }
    }
</script>
@endsection