@extends('templates.template')

@section('page_title', 'Fasilitas Ruangan')
@section('page_subtitle', 'Kelola fasilitas tiap ruangan dengan mudah')

@section('content')
<style>
    .facilities-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .facilities-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: var(--space-3);
        margin-bottom: var(--space-5);
    }
    .facilities-search {
        position: relative;
        width: 260px;
        max-width: 100%;
    }
    .facilities-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.95rem;
    }
    .facilities-search input {
        width: 100%;
        height: 40px;
        border-radius: var(--radius-pill);
        border: 1px solid var(--border-color-light);
        background: var(--bg-card);
        padding: 0 var(--space-3) 0 36px;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        outline: none;
        transition: border-color var(--transition-fast);
    }
    .facilities-search input:focus {
        border-color: var(--brand-orange);
    }
    .facilities-filter {
        height: 40px;
        border-radius: var(--radius-pill);
        border: 1px solid var(--border-color-light);
        background: var(--bg-card);
        padding: 0 var(--space-4);
        font-size: var(--font-size-sm);
        font-weight: 500;
        color: var(--text-secondary);
        cursor: pointer;
    }
    .facility-room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: var(--space-4);
    }
    .facility-room-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: 1px solid var(--border-color-light);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all var(--transition-fast);
    }
    .facility-room-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        border-color: var(--brand-orange);
    }
    .facility-room-thumb {
        width: 100%;
        height: 150px;
        background: var(--bg-input);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .facility-room-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .facility-room-thumb i { font-size: 2.2rem; color: var(--text-muted); }
    .facility-room-body {
        padding: var(--space-4);
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
        flex: 1;
    }
    .facility-room-name {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        margin: 0;
    }
    .facility-room-meta {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }
    .facility-room-meta i { color: var(--text-muted); }
    .facility-room-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 2px;
    }
    .facility-count-badge {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .facility-count-badge i { color: var(--text-muted); }
    .facility-status-badge {
        font-size: var(--font-size-xs);
        font-weight: 600;
        padding: 2px 10px;
        border-radius: var(--radius-pill);
        white-space: nowrap;
    }
    .facility-status-badge.available {
        background: rgba(16, 185, 129, 0.12);
        color: var(--status-available);
    }
    .facility-status-badge.inactive {
        background: rgba(239, 68, 68, 0.12);
        color: var(--status-rejected);
    }
    .btn-view-facilities {
        margin-top: var(--space-2);
        width: 100%;
        height: 38px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--brand-orange);
        background: transparent;
        color: var(--brand-orange);
        font-weight: 600;
        font-size: var(--font-size-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        transition: all var(--transition-fast);
    }
    .btn-view-facilities:hover {
        background: var(--brand-orange);
        color: #fff;
    }
    .empty-rooms {
        text-align: center;
        padding: var(--space-9) 0;
        color: var(--text-muted);
    }
    .empty-rooms i { font-size: 2.5rem; display: block; margin-bottom: var(--space-3); }
    @media (max-width:991.98px) {
        .facilities-content { padding: var(--space-3); }
    }
</style>

<div class="facilities-content">

    <div class="greeting-section" style="margin-bottom: var(--space-4);">
        <h1 class="greeting-title">Fasilitas Ruangan</h1>
        <div class="greeting-sub">
            <span>Pilih ruangan untuk melihat dan mengelola fasilitas yang tersedia.</span>
        </div>
    </div>

    <form method="GET" action="{{ route('settings.facilities.index') }}" class="facilities-toolbar">
        <div style="display:flex; align-items:center; gap:var(--space-2); flex-wrap:wrap;">
            <a href="{{ route('settings.facilities.master') }}" style="font-size:var(--font-size-sm); color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                <i class="bi bi-box-seam"></i> Kelola Data Master Fasilitas
            </a>
        </div>
        <div style="display:flex; align-items:center; gap:var(--space-2); flex-wrap:wrap;">
            <div class="facilities-search">
                <i class="bi bi-search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ruangan...">
            </div>
        </div>
    </form>

    @if($rooms->count())
        <div class="facility-room-grid">
            @foreach($rooms as $room)
                <div class="facility-room-card">
                    <div class="facility-room-thumb">
                        @if($room->photos->count())
                            <img src="{{ $room->photos->first()->photo_url }}" alt="Foto {{ $room->name }}">
                        @else
                            <i class="bi bi-image"></i>
                        @endif
                    </div>
                    <div class="facility-room-body">
                        <h3 class="facility-room-name">{{ $room->name }}</h3>
                        <p class="facility-room-meta"><i class="bi bi-geo-alt"></i> {{ $room->location }}</p>
                        <p class="facility-room-meta"><i class="bi bi-people"></i> Kapasitas {{ $room->capacity }} orang</p>

                        <div class="facility-room-footer">
                            <span class="facility-count-badge">
                                <i class="bi bi-tools"></i> {{ $room->facilities->count() }} fasilitas
                            </span>
                        </div>

                        <a href="{{ route('settings.facilities.room', $room->id) }}" class="btn-view-facilities">
                            Lihat Fasilitas <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-rooms">
            <i class="bi bi-building"></i>
            <p>Belum ada ruangan yang terdaftar.</p>
        </div>
    @endif
</div>
@endsection