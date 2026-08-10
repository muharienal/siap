@extends('templates.template')

@section('title', $room->name)
@section('page_title', 'Ruang Meeting')

@section('content')
<style>
    /* ============================================
       PAGE HEADER / TOOLBAR (identik dengan halaman
       Manajemen Ruang Rapat)
    ============================================ */
    .db-toolbar {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: var(--space-4); margin-bottom: var(--space-5);
    }
    .btn-back, .btn-primary-sm {
        height: 44px; padding: 0 var(--space-5);
        border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; gap: 8px;
        text-decoration: none; cursor: pointer; transition: all 0.2s;
        white-space: nowrap; border: none;
    }
    .btn-back {
        background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }
    .btn-primary-sm {
        background: var(--brand-gradient); color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25); color: white; }

    /* ============================================
       ROOM HERO CARD
    ============================================ */
    .room-hero-card {
        display: flex; align-items: flex-start; justify-content: space-between; gap: var(--space-4);
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        padding: 1.25rem 1.5rem; margin-bottom: var(--space-6);
        position: relative; overflow: hidden; flex-wrap: wrap;
    }
    .room-hero-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: var(--brand-gradient); }
    .room-hero-text { min-width: 0; flex: 1; }
    .room-title-row { display: flex; align-items: center; gap: var(--space-3); flex-wrap: wrap; }
    .room-title { font-size: var(--font-size-xl); font-weight: 700; margin: 0; letter-spacing: -0.02em; color: var(--text-primary); line-height: 1.25; }
    .room-hero-description { margin: 6px 0 0 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500; line-height: 1.6; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700;
    }
    .status-pill.available { background: rgba(16, 185, 129, 0.12); color: var(--brand-green); }
    .status-pill.busy { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* ============================================
       DETAIL GRID 2 KOLOM
    ============================================ */
    .detail-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: var(--space-6); align-items: start; }
    @media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }

    .section-card {
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        padding: 1rem 1.5rem 1.5rem; margin-bottom: var(--space-6);
    }
    .section-card:last-child { margin-bottom: 0; }
    .section-title {
        font-size: 1.05rem; font-weight: 700; margin: -0.5rem 0 1rem 0; padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color-light);
        display: flex; align-items: center; justify-content: space-between; color: var(--text-primary);
        text-transform: none !important;
    }
    .section-title span { display: flex; align-items: center; gap: 8px; text-transform: none !important; }
    .section-title i {
        width: 28px; height: 28px; border-radius: 8px; background: rgba(249, 115, 22, 0.1);
        color: var(--brand-orange); display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem;
    }

    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; }
    .gallery-item { position: relative; aspect-ratio: 1; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color-light); }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
    .no-photos {
        grid-column: 1/-1; text-align: center; padding: 2rem; color: var(--text-muted);
        background: var(--bg-input); border-radius: var(--radius-sm); border: 2px dashed var(--border-color);
    }
    .no-photos i { color: var(--text-muted); }

    .room-desc { font-size: var(--font-size-sm); color: var(--text-secondary); line-height: 1.6; margin: 0; }

    .info-grid { display: flex; flex-direction: column; gap: 10px; }
    .info-item { display: flex; align-items: center; gap: 14px; padding: 12px 14px; background: var(--bg-input); border-radius: var(--radius-sm); }
    .info-item-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0; }
    .info-item-icon.icon-blue { background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); }
    .info-item-icon.icon-orange { background: rgba(249, 115, 22, 0.1); color: var(--brand-orange); }
    .info-item-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .info-item-label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.4px; }
    .info-item-value { font-weight: 600; color: var(--text-primary); font-size: 0.9rem; }

    .facility-list-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: var(--bg-input); border-radius: var(--radius-sm); margin-bottom: 8px; }
    .facility-list-item:last-child { margin-bottom: 0; }
    .facility-info { display: flex; align-items: center; gap: 12px; }
    .facility-icon { width: 36px; height: 36px; background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .facility-name { font-weight: 600; color: var(--text-primary); font-size: 0.9rem; }
    .facility-qty { font-size: 0.78rem; color: var(--text-muted); }

    /* Jadwal Hari Ini */
    .schedule-date { font-weight: 400; color: var(--text-muted); font-size: 0.85rem; }
    .schedule-list { display: flex; flex-direction: column; gap: var(--space-2); max-height: 480px; overflow-y: auto; }
    .schedule-row { display: flex; align-items: center; gap: var(--space-3); font-size: var(--font-size-xs); }
    .schedule-row .time { width: 44px; flex-shrink: 0; color: var(--text-muted); font-weight: 600; }
    .schedule-row .slot { flex: 1; padding: var(--space-2) var(--space-3); border-radius: var(--radius-xs); background: var(--bg-input); color: var(--text-muted); }
    .schedule-row .slot.busy { background: rgba(239, 68, 68, 0.10); color: #dc2626; font-weight: 600; }

    @media (max-width: 767.98px) {
        .db-toolbar .btn-primary-sm { width: 100%; justify-content: center; }
    }
</style>

<div class="db-toolbar">
    <a href="{{ route('rooms.list') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <a href="{{ route('bookings.create') }}?room={{ $room->id }}" class="btn-primary-sm">
        <i class="bi bi-plus-lg"></i> Booking
    </a>
</div>

{{-- ROOM HERO CARD --}}
<div class="room-hero-card">
    <div class="room-hero-text">
        <div class="room-title-row">
            <h1 class="room-title">{{ $room->name }}</h1>
            @if($isAvailableNow)
                <span class="status-pill available"><span class="dot"></span> Tersedia</span>
            @else
                <span class="status-pill busy"><span class="dot"></span> Terisi</span>
            @endif
        </div>
        <p class="room-hero-description">{{ $room->location }} &middot; Kapasitas {{ $room->capacity }} orang</p>
    </div>
</div>

<div class="detail-grid">
    {{-- Kolom Kiri: Galeri -> Deskripsi -> Informasi Dasar --}}
    <div>
        <div class="section-card">
            <div class="section-title">
                <span><i class="bi bi-images"></i> Galeri Foto</span>
            </div>
            <div class="gallery-grid">
                @php
                    $validPhotos = $room->photos->filter(function($p) { return $p->photo_url !== null; });
                @endphp
                @if($validPhotos->count() > 0)
                    @foreach($validPhotos as $photo)
                        <div class="gallery-item">
                            <img src="{{ $photo->photo_url }}" alt="Foto {{ $room->name }}">
                        </div>
                    @endforeach
                @else
                    <div class="no-photos">
                        <i class="bi bi-image d-block fs-4 mb-2"></i>
                        Belum ada foto untuk ruangan ini.
                    </div>
                @endif
            </div>
        </div>

        <div class="section-card">
            <div class="section-title">
                <span><i class="bi bi-card-text"></i> Deskripsi</span>
            </div>
            <p class="room-desc">{{ $room->description ?: 'Belum ada deskripsi untuk ruangan ini.' }}</p>
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
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Fasilitas -> Jadwal Hari Ini --}}
    <div>
        <div class="section-card">
            <div class="section-title">
                <span><i class="bi bi-tools"></i> Fasilitas Ruangan</span>
            </div>
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

        <div class="section-card">
            <div class="section-title">
                <span><i class="bi bi-calendar3"></i> Jadwal Hari Ini</span>
                <span class="schedule-date">{{ $today->translatedFormat('d M Y') }}</span>
            </div>
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
@endsection