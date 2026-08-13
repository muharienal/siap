@extends('templates.template')

@section('title', 'Manajemen Ruang Rapat')
@section('page_title', 'Manajemen Ruang Rapat')

@section('content')
<style>
    /* Toolbar (Sama persis dengan Dashboard/Booking) */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: var(--space-4);
        margin-bottom: var(--space-6);
    }
    .db-toolbar h1 {
        font-size: var(--font-size-2xl);
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.02em;
    }
    .db-toolbar .db-meta {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-2) 0 0;
        display: flex;
        align-items: center;
        gap: var(--space-3);
        flex-wrap: wrap;
    }
    .db-toolbar .actions {
        display: flex;
        gap: var(--space-3);
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .btn-primary-sm {
        height: 44px; padding: 0 var(--space-5);
        background: var(--brand-gradient); border: none;
        border-radius: var(--radius-sm); font-weight: 600;
        font-size: var(--font-size-sm); color: var(--text-inverse);
        transition: all var(--transition-fast); cursor: pointer;
        display: inline-flex; align-items: center; gap: var(--space-2);
        text-decoration: none; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover {
        background: var(--brand-gradient-hover); transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25); color: var(--text-inverse);
    }

    .btn-outline-sm {
        height: 44px; padding: 0 var(--space-5);
        background: var(--bg-card); border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); font-weight: 600;
        font-size: var(--font-size-sm); color: var(--text-secondary);
        transition: all var(--transition-fast); cursor: pointer;
        display: inline-flex; align-items: center; gap: var(--space-2);
        text-decoration: none;
    }
    .btn-outline-sm:hover {
        border-color: var(--brand-orange); color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.04);
    }

    /* Room Grid */
    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: var(--space-5);
    }
    .room-card {
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        cursor: pointer;
        transition: all var(--transition-fast); display: flex; flex-direction: column;
        text-decoration: none; color: inherit;
    }
    .room-card:hover {
        transform: translateY(-4px); box-shadow: var(--shadow-hover);
        border-color: var(--brand-orange);
    }
    .room-card-image {
        width: 100%; height: 180px; background: var(--bg-input);
        position: relative; overflow: hidden;
        border-top-left-radius: var(--radius-card);
        border-top-right-radius: var(--radius-card);
    }
    .room-card-image img {
        width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;
    }
    .room-card:hover .room-card-image img { transform: scale(1.05); }
    .room-card-image-placeholder {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); font-size: 3rem; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    }
    .room-card-status {
        position: absolute; top: 12px; right: 12px; padding: 4px 10px;
        border-radius: var(--radius-pill); font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.3px; backdrop-filter: blur(8px);
    }
    .status-active { background: rgba(16, 185, 129, 0.9); color: white; }
    .status-inactive { background: rgba(239, 68, 68, 0.9); color: white; }
    
    .room-card-body {
        padding: var(--space-4); display: flex; flex-direction: column; gap: var(--space-2); flex: 1;
    }
    .room-card-title { font-size: var(--font-size-md); font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.3; }
    .room-card-meta {
        display: flex; align-items: center; gap: var(--space-3);
        font-size: var(--font-size-sm); color: var(--text-secondary); flex-wrap: wrap;
    }
    .room-card-meta i { color: var(--text-muted); margin-right: 4px; }

    /* Facility Badge with Tooltip */
    .facility-badge-wrapper { 
        position: relative; 
        display: inline-block; 
        margin-top: var(--space-2); 
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

    /* Custom Tooltip - Modern Clean */
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

    .room-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: auto; padding-top: var(--space-3); border-top: 1px solid var(--border-color-light);
    }
    .room-card-stats { font-size: var(--font-size-xs); color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
    .btn-view-detail {
        padding: 6px 14px; border-radius: var(--radius-sm); font-size: var(--font-size-xs);
        font-weight: 600; background: rgba(249, 115, 22, 0.08); color: var(--brand-orange-dark);
        border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all var(--transition-fast);
    }
    .room-card:hover .btn-view-detail { background: var(--brand-orange); color: white; }

    /* Empty State */
    .empty-state {
        text-align: center; padding: var(--space-9) var(--space-4);
        background: var(--bg-card); border-radius: var(--radius-card); border: 2px dashed var(--border-color);
    }
    .empty-state i { font-size: 3rem; color: var(--border-color); margin-bottom: var(--space-4); }
    .empty-state h4 { font-weight: 700; color: var(--text-primary); margin-bottom: var(--space-2); }
    .empty-state p { color: var(--text-muted); margin-bottom: var(--space-4); }

    /* MODAL MODERN CLEAN (STANDAR) */
    .modern-modal .modal-dialog { max-width: 800px; }
    .modern-modal .modal-content {
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .modern-modal .modal-header {
        background: var(--bg-card);
        border-bottom: 1px solid var(--border-color-light);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modern-modal .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modern-modal .modal-title i {
        color: var(--brand-orange);
        font-size: 1.5rem;
    }
    .modern-modal .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-muted);
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .modern-modal .btn-close:hover { opacity: 1; }
    
    .modern-modal .modal-body { padding: 1.5rem; max-height: 80vh; overflow-y: auto; }
    
    .modern-modal .modal-footer {
        border: none;
        padding: 0 1.5rem 1.5rem 1.5rem;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .modern-input {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--bg-input);
        font-size: 0.9rem;
        color: var(--text-primary);
        transition: all 0.2s;
        outline: none;
    }
    .modern-input:focus {
        border-color: var(--brand-orange);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }
    textarea.modern-input { height: auto; padding: 10px 14px; }
    .form-label-modern {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    
    .btn-modal-cancel {
        height: 42px;
        padding: 0 20px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-secondary);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-modal-cancel:hover { background: var(--bg-hover); }
    
    .btn-modal-submit {
        height: 42px;
        padding: 0 24px;
        border-radius: var(--radius-sm);
        border: none;
        background: var(--brand-gradient);
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(249,115,22,0.2);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-modal-submit:hover {
        box-shadow: 0 6px 16px rgba(16,185,129,0.25);
        transform: translateY(-1px);
    }
    .btn-modal-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* FORM GRID 2 KOLOM (NO SCROLL) */
    .form-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 1.5rem;
    }
    .form-left-col {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .form-right-col {
        display: flex;
        flex-direction: column;
    }

    /* MODERN DRAG & DROP UPLOAD */
    .upload-zone {
        border: 2px dashed var(--border-color);
        background: var(--bg-input);
        border-radius: var(--radius-sm);
        padding: 1.5rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: var(--brand-orange);
        background: rgba(249, 115, 22, 0.04);
    }
    .upload-icon {
        font-size: 2.5rem;
        color: var(--brand-orange);
        margin-bottom: 0.5rem;
        display: block;
    }
    .upload-text {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }
    .upload-subtext {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    
    .uploaded-files-list {
        margin-top: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 150px;
        overflow-y: auto;
    }
    .uploaded-file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-xs);
        animation: slideIn 0.3s ease;
    }
    .file-info {
        display: flex;
        align-items: center;
        gap: 10px;
        overflow: hidden;
    }
    .file-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .file-details h6 {
        margin: 0;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .file-details small {
        font-size: 0.7rem;
        color: var(--text-muted);
    }
    .btn-remove-file {
        background: transparent;
        border: none;
        color: #dc2626;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0 4px;
        transition: transform 0.2s;
        flex-shrink: 0;
    }
    .btn-remove-file:hover { transform: scale(1.2); }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* FACILITY SELECT GRID (CHECKBOX + QTY) */
    .facility-select-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
        max-height: 200px;
        overflow-y: auto;
        padding: 10px;
        background: var(--bg-input);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-sm);
    }
    .fac-select-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 12px;
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-xs);
    }
    .fac-check-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        color: var(--text-primary);
    }
    .fac-qty-input {
        width: 60px !important;
        height: 34px !important;
        text-align: center;
        padding: 0 !important;
        font-size: 0.8rem !important;
    }
    .fac-qty-input:disabled {
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
        cursor: not-allowed;
    }

    /* MODAL DATA FASILITAS (INLINE CRUD) */
    .facility-modal .modal-dialog { max-width: 700px; }
    .facility-modal .modal-body { padding: 0; display: flex; min-height: 350px; }
    .fac-list-panel {
        flex: 1.2;
        border-right: 1px solid var(--border-color-light);
        padding: 1.25rem;
        background: var(--bg-body);
        overflow-y: auto;
        max-height: 500px;
    }
    .fac-list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .fac-list-title { font-size: 0.875rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-add-new { background: var(--brand-orange); color: white; border: none; height: 32px; width: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: all 0.2s; }
    .btn-add-new:hover { background: var(--brand-orange-dark); transform: scale(1.05); }
    .fac-item { background: var(--bg-card); padding: 12px 14px; border-radius: var(--radius-sm); margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; border: 1px solid transparent; transition: all 0.2s; cursor: pointer; }
    .fac-item:hover { border-color: var(--border-color); transform: translateX(2px); }
    .fac-item.active { border-color: var(--brand-orange); background: rgba(249, 115, 22, 0.04); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .fac-item-name { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
    .fac-item-name i { color: var(--brand-blue); font-size: 1rem; }
    .fac-actions-group { display: flex; gap: 4px; }
    .fac-btn { width: 30px; height: 30px; border-radius: 6px; border: none; background: transparent; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .fac-btn:hover { background: rgba(0,0,0,0.05); color: var(--text-primary); }
    .fac-btn.delete:hover { background: rgba(239,68,68,0.1); color: #dc2626; }

    .fac-form-panel { flex: 1; padding: 1.5rem; background: var(--bg-card); display: flex; flex-direction: column; }
    .fac-form-title { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px; }
    .fac-form-actions { margin-top: auto; display: flex; gap: 10px; padding-top: 1.5rem; }

    /* TOAST NOTIFICATION (MODERN ALERT) */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }
    .toast-notification {
        min-width: 320px;
        max-width: 400px;
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        padding: 16px 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        pointer-events: auto;
        animation: toastSlideIn 0.3s ease forwards;
        border-left: 4px solid;
    }
    .toast-notification.success { border-left-color: var(--brand-green); }
    .toast-notification.error { border-left-color: #ef4444; }
    .toast-notification.info { border-left-color: var(--brand-blue); }
    .toast-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .toast-notification.success .toast-icon { color: var(--brand-green); }
    .toast-notification.error .toast-icon { color: #ef4444; }
    .toast-notification.info .toast-icon { color: var(--brand-blue); }
    .toast-content { flex: 1; }
    .toast-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    .toast-message {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }
    .toast-close {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 1rem;
        padding: 0;
        margin-top: 2px;
        transition: color 0.2s;
    }
    .toast-close:hover { color: var(--text-primary); }
    
    @keyframes toastSlideIn {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes toastSlideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }

    @media (max-width: 991.98px) {
        .db-toolbar { flex-direction: column; align-items: stretch; }
        .db-toolbar .actions { display: flex; }
        .db-toolbar .actions .btn-primary-sm, .db-toolbar .actions .btn-outline-sm { flex: 1; justify-content: center; }
        
        .form-grid { grid-template-columns: 1fr; }
        .upload-zone { min-height: 150px; }

        .facility-modal .modal-body { flex-direction: column; }
        .fac-list-panel { border-right: none; border-bottom: 1px solid var(--border-color-light); max-height: 250px; }
    }
</style>

{{-- FIX: aksi hapus ruangan (form submit biasa, bukan AJAX) redirect ke
     sini dengan session('success')/session('error'), tapi sebelumnya
     tidak pernah ditampilkan. --}}
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

<div class="db-toolbar">
    <div>
        <button type="button" class="btn-outline-sm" data-bs-toggle="modal" data-bs-target="#masterFacilityModal">
            <i class="bi bi-box-seam"></i> Data Fasilitas
        </button>
    </div>
    <div class="actions">
        <button type="button" class="btn-primary-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="bi bi-plus-lg"></i> Tambah Ruangan
        </button>
    </div>
</div>

<div id="roomGridContainer">
    @if($rooms->count() > 0)
        <div class="room-grid" id="roomGrid">
            @foreach($rooms as $room)
                <a href="{{ route('settings.rooms.show', $room->id) }}" class="room-card" style="text-decoration: none; color: inherit;">
                    <div class="room-card-image">
                        @php
                            $validPhoto = $room->photos->first(function($p) { return $p->photo_url !== null; });
                        @endphp
                        @if($validPhoto)
                            <img src="{{ $validPhoto->photo_url }}" alt="{{ $room->name }}">
                        @else
                            <div class="room-card-image-placeholder"><i class="bi bi-image"></i></div>
                        @endif
                        <span class="room-card-status {{ $room->status == 1 ? 'status-active' : 'status-inactive' }}">
                            {{ $room->status == 1 ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="room-card-body">
                        <h3 class="room-card-title">{{ $room->name }}</h3>
                        <div class="room-card-meta">
                            <span><i class="bi bi-geo-alt"></i>{{ $room->location }}</span>
                            <span><i class="bi bi-people"></i>{{ $room->capacity }} orang</span>
                        </div>
                        
                        {{-- FACILITY BADGE WITH TOOLTIP --}}
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

                        <div class="room-card-footer">
                            <div class="room-card-stats">
                                <i class="bi bi-images"></i> {{ $room->photos->filter(function($p) { return $p->photo_url !== null; })->count() }} foto
                            </div>
                            <span class="btn-view-detail">Lihat Detail <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-door-open"></i>
            <h4>Belum ada ruangan</h4>
            <p>Mulai tambahkan ruangan rapat pertama Anda</p>
            <button type="button" class="btn-primary-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal" style="display: inline-flex;">
                <i class="bi bi-plus-lg"></i> Tambah Ruangan
            </button>
        </div>
    @endif
</div>

{{-- ========================================== --}}
{{-- MODAL: TAMBAH RUANGAN BARU (2 KOLOM + FASILITAS) --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-square"></i> Tambah Ruangan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <form action="{{ route('settings.rooms.store') }}" method="POST" enctype="multipart/form-data" id="addRoomForm">
                @csrf
                <div class="modal-body">
                    <div class="form-grid">
                        {{-- Kolom Kiri: Form Input --}}
                        <div class="form-left-col">
                            <div>
                                <label class="form-label-modern">Nama Ruangan</label>
                                <input type="text" name="name" class="modern-input" placeholder="Contoh: Ruang Rapat Argopuro" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-7">
                                    <label class="form-label-modern">Lokasi</label>
                                    <input type="text" name="location" class="modern-input" placeholder="Lantai 1" required>
                                </div>
                                <div class="col-5">
                                    <label class="form-label-modern">Kapasitas</label>
                                    <input type="number" name="capacity" class="modern-input" placeholder="10" min="1" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label-modern">Status</label>
                                <select name="status" class="modern-input" required>
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                            
                            {{-- FIELD FASILITAS --}}
                            <div>
                                <label class="form-label-modern">Fasilitas Ruangan</label>
                                <div class="facility-select-grid">
                                    @if($facilities->count() > 0)
                                        @foreach($facilities as $facility)
                                            <div class="fac-select-item">
                                                <label class="fac-check-label">
                                                    <input type="checkbox" class="form-check-input fac-checkbox" name="facilities[{{ $facility->id }}][active]" value="1" data-fac-id="{{ $facility->id }}">
                                                    {{ $facility->name }}
                                                </label>
                                                <input type="number" name="facilities[{{ $facility->id }}][quantity]" class="modern-input fac-qty-input" placeholder="Qty" min="1" value="1" disabled>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted text-center" style="grid-column: 1/-1;">Belum ada data fasilitas. Silakan tambah via tombol Data Fasilitas.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Upload Foto --}}
                        <div class="form-right-col">
                            <label class="form-label-modern">Upload Foto Ruangan</label>
                            <div class="upload-zone" id="dropzone" onclick="document.getElementById('fileInput').click()">
                                <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                <div class="upload-text">Click To Upload</div>
                                <div class="upload-subtext">atau drag & drop file kesini</div>
                                <div class="upload-subtext" style="margin-top: 5px; font-size: 0.7rem;">JPG, PNG, Max 10MB</div>
                                <input type="file" name="photos[]" id="fileInput" multiple accept="image/*" style="display: none;">
                            </div>
                            
                            <div class="uploaded-files-list" id="uploadedFilesList"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal-submit" id="submitRoomBtn">
                        <i class="bi bi-save"></i> Simpan Ruangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL: DATA FASILITAS (MODERN INLINE)    --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal facility-modal" id="masterFacilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-seam"></i> Data Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="fac-list-panel">
                    <div class="fac-list-header">
                        <span class="fac-list-title">Daftar Fasilitas</span>
                    </div>
                    <div id="facilityListContainer"></div>
                </div>

                <div class="fac-form-panel">
                    <h5 class="fac-form-title" id="formTitle"><i class="bi bi-plus-circle"></i> Tambah Fasilitas Baru</h5>
                    <form id="facilityForm" class="d-flex flex-column h-100">
                        @csrf
                        <input type="hidden" id="facility_id" name="id">
                        <div class="mb-3">
                            <label class="form-label-modern">Nama Fasilitas</label>
                            <input type="text" class="modern-input" id="facility_name" name="name" placeholder="Contoh: Microphone" required>
                        </div>
                        
                        <div class="fac-form-actions">
                            <button type="button" class="btn-modal-cancel" onclick="resetFacilityForm()">Batal</button>
                            <button type="submit" class="btn-modal-submit" id="submitBtn">Simpan Fasilitas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toastContainer"></div>

<script>
    // ============================================
    // TOAST NOTIFICATION SYSTEM (MODERN ALERT)
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
    // SCRIPT ENABLE/DISABLE QTY FASILITAS
    // ============================================
    document.querySelectorAll('.fac-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const qtyInput = document.querySelector(`input[name="facilities[${this.dataset.facId}][quantity]"]`);
            if (qtyInput) {
                qtyInput.disabled = !this.checked;
                if (!this.checked) qtyInput.value = 1;
            }
        });
    });

    // ============================================
    // SCRIPT MULTIPLE FILE UPLOAD MODERN
    // ============================================
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const uploadedFilesList = document.getElementById('uploadedFilesList');
    let uploadedFiles = [];

    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
        fileInput.value = '';
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
    });

    dropzone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }, false);

    function handleFiles(files) {
        [...files].forEach(file => {
            if (!file.type.match('image.*')) {
                showToast('error', 'Format Tidak Didukung', `${file.name} bukan file gambar.`);
                return;
            }
            if (uploadedFiles.some(f => f.name === file.name && f.size === file.size)) {
                showToast('info', 'File Duplikat', `${file.name} sudah ditambahkan.`);
                return;
            }
            uploadedFiles.push(file);
            renderFileItem(file);
        });
    }

    function renderFileItem(file) {
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
            <button type="button" class="btn-remove-file" onclick="removeFile(this, '${file.name}', ${file.size})">
                <i class="bi bi-x"></i>
            </button>
        `;
        uploadedFilesList.appendChild(fileItem);
    }

    function removeFile(btn, name, size) {
        uploadedFiles = uploadedFiles.filter(f => !(f.name === name && f.size === size));
        btn.closest('.uploaded-file-item').remove();
    }

    // ============================================
    // SCRIPT SUBMIT FORM (AJAX DENGAN TOAST)
    // ============================================
    document.getElementById('addRoomForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        formData.delete('photos[]');
        uploadedFiles.forEach(file => {
            formData.append('photos[]', file);
        });

        const submitBtn = document.getElementById('submitRoomBtn');
        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

        fetch(this.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                showToast('success', 'Berhasil!', data.message || 'Ruangan berhasil ditambahkan.', 3000);
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showToast('error', 'Gagal', data.message || 'Gagal menyimpan ruangan.', 5000);
            }
        })
        .catch(error => {
            if (error && error.errors) {
                let errorMessages = [];
                const errorList = Object.values(error.errors).flat();
                errorList.forEach(msg => errorMessages.push(msg));
                showToast('error', 'Validasi Gagal', errorMessages.join('<br>'), 6000);
            } else if (error && error.message) {
                showToast('error', 'Error', error.message, 5000);
            } else {
                showToast('error', 'Error', 'Terjadi kesalahan jaringan atau server.', 5000);
                console.error('Fetch Error:', error);
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });

    // ============================================
    // SCRIPT UNTUK DATA FASILITAS (AJAX)
    // ============================================
    var facilitiesData = @json($facilities);
    var routeStore = "{{ route('settings.facilities.store') }}";
    var routeUpdate = "{{ route('settings.facilities.update', ':id') }}".replace(':id', '');
    var routeDelete = "{{ route('settings.facilities.destroy', ':id') }}".replace(':id', '');

    function renderFacilityList() {
        var container = document.getElementById('facilityListContainer');
        if (facilitiesData.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-inbox d-block fs-4 mb-2"></i> Belum ada data</div>';
            return;
        }

        var html = '';
        facilitiesData.forEach(function(f) {
            html += `
                <div class="fac-item" id="item-${f.id}">
                    <div class="fac-item-name">
                        <i class="bi bi-box"></i> ${f.name}
                    </div>
                    <div class="fac-actions-group">
                        <button class="fac-btn" onclick="editFacility(${f.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="fac-btn delete" onclick="deleteFacility(${f.id})" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function resetFacilityForm() {
        document.getElementById('facilityForm').reset();
        document.getElementById('facility_id').value = '';
        document.getElementById('formTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Tambah Fasilitas Baru';
        document.getElementById('submitBtn').textContent = 'Simpan Fasilitas';
        
        document.querySelectorAll('.fac-item').forEach(function(el) {
            el.classList.remove('active');
        });
    }

    function editFacility(id) {
        var f = facilitiesData.find(x => x.id === id);
        if (!f) return;

        document.getElementById('facility_id').value = f.id;
        document.getElementById('facility_name').value = f.name;
        
        document.getElementById('formTitle').innerHTML = '<i class="bi bi-pencil-circle"></i> Edit Fasilitas';
        document.getElementById('submitBtn').textContent = 'Update Fasilitas';
        
        document.querySelectorAll('.fac-item').forEach(function(el) {
            el.classList.remove('active');
        });
        document.getElementById('item-' + id).classList.add('active');
    }

    function deleteFacility(id) {
        if (!confirm('Yakin ingin menghapus fasilitas ini?')) return;

        fetch(routeDelete + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'DELETE',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                facilitiesData = facilitiesData.filter(x => x.id !== id);
                renderFacilityList();
                resetFacilityForm();
                showToast('success', 'Berhasil!', 'Fasilitas berhasil dihapus.', 3000);
            } else {
                showToast('error', 'Gagal', data.message || 'Gagal menghapus fasilitas.', 5000);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000);
        });
    }

    document.getElementById('facilityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('facility_id').value;
        var url = id ? routeUpdate + id : routeStore;
        var method = id ? 'PUT' : 'POST';

        var formData = new FormData(this);
        var data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => {
            if (!r.ok) {
                return r.json().then(err => { throw err; });
            }
            return r.json();
        })
        .then(res => {
            if (res.success) {
                if (id) {
                    var index = facilitiesData.findIndex(x => x.id === parseInt(id));
                    if (index !== -1) facilitiesData[index] = res.data;
                    showToast('success', 'Berhasil!', 'Fasilitas berhasil diperbarui.', 3000);
                } else {
                    facilitiesData.push(res.data);
                    showToast('success', 'Berhasil!', 'Fasilitas berhasil ditambahkan.', 3000);
                }
                renderFacilityList();
                resetFacilityForm();
            } else {
                showToast('error', 'Gagal', res.message || 'Gagal menyimpan fasilitas.', 5000);
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

    // Init
    renderFacilityList();
</script>
@endsection