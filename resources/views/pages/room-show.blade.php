@extends('templates.template')

@section('page_title', $room->name)
@section('page_subtitle', 'Detail ruangan dan jadwal hari ini')

@section('content')
<style>
    .room-detail-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1200px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .room-detail-header {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        margin-bottom: var(--space-5);
        flex-wrap: wrap;
    }
    .back-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all var(--transition-fast);
        flex-shrink: 0;
    }
    .back-btn:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }
    .room-detail-header .room-title {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        flex-wrap: wrap;
    }
    .room-detail-header h1 {
        font-size: var(--font-size-xl);
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .room-detail-header p {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-1) 0 0 0;
    }
    .status-pill {
        font-size: var(--font-size-xs);
        font-weight: 600;
        padding: 2px 12px;
        border-radius: var(--radius-pill);
    }
    .status-pill.available {
        background: rgba(16, 185, 129, 0.12);
        color: var(--status-available);
    }
    .status-pill.busy {
        background: rgba(239, 68, 68, 0.12);
        color: var(--status-rejected);
    }
    .room-detail-header .actions {
        margin-left: auto;
    }
    .btn-primary-sm {
        height: 38px;
        padding: 0 var(--space-4);
        background: var(--brand-gradient);
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-inverse);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.12);
    }
    .btn-primary-sm:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.18);
    }
    .room-detail-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: var(--space-5);
        align-items: start;
    }
    @media (max-width: 900px) {
        .room-detail-grid { grid-template-columns: 1fr; }
    }
    .detail-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
        margin-bottom: var(--space-5);
    }
    .detail-card .card-header {
        padding: var(--space-4) var(--space-5);
        border-bottom: 1px solid var(--border-color-light);
        font-weight: 700;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .detail-card .card-body {
        padding: var(--space-5);
    }
    .gallery-main {
        width: 100%;
        height: 260px;
        border-radius: var(--radius-sm);
        background: var(--bg-input);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .gallery-main img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-main i { font-size: 2.5rem; color: var(--text-muted); }
    .gallery-thumbs {
        display: flex;
        gap: var(--space-2);
        margin-top: var(--space-3);
        flex-wrap: wrap;
    }
    .gallery-thumbs .thumb {
        width: 64px;
        height: 48px;
        border-radius: var(--radius-xs);
        overflow: hidden;
        background: var(--bg-input);
        cursor: pointer;
        border: 2px solid transparent;
    }
    .gallery-thumbs .thumb img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-thumbs .thumb.active { border-color: var(--brand-orange); }
    .room-desc {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-3) var(--space-4);
    }
    .info-item {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        font-size: var(--font-size-sm);
        color: var(--text-primary);
    }
    .info-item i { color: var(--brand-orange); font-size: 1rem; width: 18px; text-align: center; }
    .schedule-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }
    .schedule-row {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        font-size: var(--font-size-xs);
    }
    .schedule-row .time {
        width: 44px;
        flex-shrink: 0;
        color: var(--text-muted);
        font-weight: 600;
    }
    .schedule-row .slot {
        flex: 1;
        padding: var(--space-2) var(--space-3);
        border-radius: var(--radius-xs);
        background: var(--bg-input);
        color: var(--text-muted);
    }
    .schedule-row .slot.busy {
        background: rgba(239, 68, 68, 0.10);
        color: var(--status-rejected);
        font-weight: 600;
    }
</style>

<div class="room-detail-content">
    <div class="room-detail-header">
        <a href="{{ route('dashboard') }}" class="back-btn" aria-label="Kembali">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="room-title">
            <div>
                <div style="display:flex; align-items:center; gap: var(--space-2);">
                    <h1>{{ $room->name }}</h1>
                    @if($isAvailableNow)
                        <span class="status-pill available">Tersedia</span>
                    @else
                        <span class="status-pill busy">Terisi</span>
                    @endif
                </div>
                <p>{{ $room->location }} &middot; Kapasitas {{ $room->capacity }} orang</p>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('bookings.create') }}?room={{ $room->id }}" class="btn-primary-sm">
                <i class="bi bi-plus-lg"></i> Booking
            </a>
        </div>
    </div>

    <div class="room-detail-grid">
        <div>
            <div class="detail-card">
                <div class="card-header">Galeri</div>
                <div class="card-body">
                    @if($room->photos->count())
                        <div class="gallery-main" id="galleryMain">
                            <img src="{{ $room->photos->first()->photo_url }}" alt="Foto {{ $room->name }}" id="galleryMainImg">
                        </div>
                        @if($room->photos->count() > 1)
                            <div class="gallery-thumbs">
                                @foreach($room->photos as $index => $photo)
                                    <div class="thumb {{ $index === 0 ? 'active' : '' }}" onclick="setGalleryImage('{{ $photo->photo_url }}', this)">
                                        <img src="{{ $photo->photo_url }}" alt="Foto {{ $room->name }} {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="gallery-main">
                            <i class="bi bi-image"></i>
                        </div>
                    @endif
                </div>
            </div>

            <div class="detail-card">
                <div class="card-header">Deskripsi</div>
                <div class="card-body">
                    <p class="room-desc">{{ $room->description ?: 'Belum ada deskripsi untuk ruangan ini.' }}</p>
                </div>
            </div>

            <div class="detail-card">
                <div class="card-header">Informasi</div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item"><i class="bi bi-people"></i> {{ $room->capacity }} orang</div>
                        <div class="info-item"><i class="bi bi-geo-alt"></i> {{ $room->location }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; text-transform:none;">
                <span style="text-transform:uppercase; letter-spacing:0.03em;">Jadwal hari ini</span>
                <span style="font-weight:400; color: var(--text-muted);">{{ $today->translatedFormat('d M Y') }}</span>
            </div>
            <div class="card-body">
                <div class="schedule-list">
                    @foreach($schedule as $time => $booking)
                        <div class="schedule-row">
                            <span class="time">{{ $time }}</span>
                            @if($booking)
                                <div class="slot busy">{{ $booking->purpose }}</div>
                            @else
                                <div class="slot">Kosong</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setGalleryImage(url, el) {
        document.getElementById('galleryMainImg').src = url;
        document.querySelectorAll('.gallery-thumbs .thumb').forEach(function(t) { t.classList.remove('active'); });
        el.classList.add('active');
    }
</script>
@endsection
