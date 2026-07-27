@extends('templates.template')

@section('page_title', 'Ruang Meeting')
@section('page_subtitle', 'Daftar seluruh ruang meeting yang tersedia')

@section('content')
<style>
    .room-list-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .room-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: var(--space-4);
    }
    .room-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: 1px solid var(--border-color-light);
        box-shadow: var(--shadow-card);
        padding: var(--space-4);
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
        transition: all var(--transition-fast);
    }
    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        border-color: var(--brand-orange);
    }
    .room-card-thumb {
        width: 100%;
        height: 130px;
        border-radius: var(--radius-sm);
        background: var(--bg-input);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .room-card-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .room-card-thumb i { font-size: 2rem; color: var(--text-muted); }
    .room-card-name {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
    }
    .room-card-meta {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: var(--space-1);
    }
    .room-card-status {
        align-self: flex-start;
        font-size: var(--font-size-xs);
        font-weight: 600;
        padding: 2px 10px;
        border-radius: var(--radius-pill);
    }
    .room-card-status.available {
        background: rgba(16, 185, 129, 0.12);
        color: var(--status-available);
    }
    .room-card-status.busy {
        background: rgba(239, 68, 68, 0.12);
        color: var(--status-rejected);
    }
    .empty-rooms {
        text-align: center;
        padding: var(--space-9) 0;
        color: var(--text-muted);
    }
    .empty-rooms i { font-size: 2.5rem; display: block; margin-bottom: var(--space-3); }
</style>

<div class="room-list-content">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-5); flex-wrap:wrap; gap:var(--space-2);">
        <h1 style="font-size:var(--font-size-xl); font-weight:700; color:var(--text-primary); margin:0;">Ruang Meeting</h1>
        <span style="font-size:var(--font-size-sm); color:var(--text-muted);">{{ $rooms->count() }} ruangan</span>
    </div>

    @if($rooms->count())
        <div class="room-card-grid">
            @foreach($rooms as $room)
                <a href="{{ route('rooms.show', $room->id) }}" class="room-card">
                    <div class="room-card-thumb">
                        @if($room->photos->count())
                            <img src="{{ $room->photos->first()->photo_url }}" alt="Foto {{ $room->name }}">
                        @else
                            <i class="bi bi-image"></i>
                        @endif
                    </div>
                    <div class="room-card-name">{{ $room->name }}</div>
                    <div class="room-card-meta"><i class="bi bi-people"></i> {{ $room->capacity }} orang</div>
                    <span class="room-card-status {{ $roomStatus[$room->id] }}">
                        {{ $roomStatus[$room->id] === 'busy' ? 'Terisi' : 'Tersedia' }}
                    </span>
                </a>
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
