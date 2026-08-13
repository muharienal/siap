@extends('templates.template')

@section('title', 'Ruang Rapat')
@section('page_title', 'Ruang Rapat')

@section('content')
<style>
    /* ============================================
       TOOLBAR (identik dengan halaman Manajemen
       Ruang Rapat)
    ============================================ */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-4);
        margin-bottom: var(--space-5);
    }
    .db-toolbar .count { font-size: var(--font-size-sm); color: var(--text-muted); }

    /* ============================================
       ROOM GRID & CARD (disamakan persis dengan
       settings/rooms/index.blade.php)
    ============================================ */
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

    .room-card-body {
        padding: var(--space-4); display: flex; flex-direction: column; gap: var(--space-2); flex: 1;
    }
    .room-card-title { font-size: var(--font-size-md); font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.3; }

    /* Facility Badge with Tooltip */
    .facility-badge-wrapper { position: relative; display: inline-block; margin-top: var(--space-2); align-self: flex-start; }
    .facility-badge {
        padding: 4px 10px; border-radius: var(--radius-pill); font-size: 11px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px; cursor: default;
    }
    .facility-badge.has-facilities { background: rgba(59, 130, 246, 0.08); color: var(--brand-blue); }
    .facility-badge.no-facilities { background: rgba(148, 163, 184, 0.1); color: var(--text-muted); }
    .facility-tooltip {
        position: absolute; bottom: calc(100% + 10px); left: 50%;
        transform: translateX(-50%) translateY(5px);
        background: #1e293b; color: white; padding: 12px 16px; border-radius: 10px;
        font-size: 0.8rem; font-weight: 500; min-width: 180px; max-width: 250px;
        opacity: 0; visibility: hidden; transition: all 0.25s ease; z-index: 1000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); pointer-events: none; text-align: left;
    }
    .facility-tooltip::after {
        content: ''; position: absolute; top: 100%; left: 50%; transform: translateX(-50%);
        border: 6px solid transparent; border-top-color: #1e293b;
    }
    .facility-tooltip ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; }
    .facility-tooltip li { display: flex; align-items: center; gap: 6px; white-space: normal; line-height: 1.3; }
    .facility-tooltip li::before { content: "•"; color: var(--brand-orange); font-weight: bold; }
    .facility-badge-wrapper:hover .facility-tooltip { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); }

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

    .empty-state {
        text-align: center; padding: var(--space-9) var(--space-4);
        background: var(--bg-card); border-radius: var(--radius-card); border: 2px dashed var(--border-color);
    }
    .empty-state i { font-size: 3rem; color: var(--border-color); margin-bottom: var(--space-4); }
    .empty-state h4 { font-weight: 700; color: var(--text-primary); margin-bottom: var(--space-2); }
    .empty-state p { color: var(--text-muted); margin-bottom: 0; }
</style>

<div class="db-toolbar">
    <div></div>
</div>

@if($rooms->count() > 0)
    <div class="room-grid">
        @foreach($rooms as $room)
            <a href="{{ route('rooms.show', $room->id) }}" class="room-card">
                <div class="room-card-image">
                    @php
                        $validPhoto = $room->photos->first(function($p) { return $p->photo_url !== null; });
                    @endphp
                    @if($validPhoto)
                        <img src="{{ $validPhoto->photo_url }}" alt="{{ $room->name }}">
                    @else
                        <div class="room-card-image-placeholder"><i class="bi bi-image"></i></div>
                    @endif
                </div>
                <div class="room-card-body">
                    <h3 class="room-card-title">{{ $room->name }}</h3>

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
        <i class="bi bi-building"></i>
        <h4>Belum ada ruangan</h4>
        <p>Belum ada ruangan yang terdaftar.</p>
    </div>
@endif
@endsection