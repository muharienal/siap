@extends('templates.template')

@section('title', $room->name)
@section('page_title', 'Ruang Rapat')

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
        background: var(--brand-orange); color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover { background: var(--brand-orange-dark); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(249, 115, 22, 0.25); color: white; }

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
    /* ============================================================
       LIGHTBOX FOTO — dipakai konsisten di semua halaman yang punya
       galeri foto ruangan (halaman ini, Manajemen Ruang Rapat, Dashboard)
       ============================================================ */
    .gallery-lightbox {
        display: none; position: fixed; inset: 0;
        background: rgba(10, 14, 25, 0.94); backdrop-filter: blur(6px);
        z-index: 9999; align-items: center; justify-content: center;
        flex-direction: column; padding: 24px;
        animation: lightboxFadeIn 0.25s ease;
    }
    .gallery-lightbox.active { display: flex; }
    @keyframes lightboxFadeIn { from { opacity: 0; } to { opacity: 1; } }
    .gallery-lightbox .lightbox-content {
        position: relative; width: 100%; max-width: 900px;
        display: flex; flex-direction: column; align-items: center;
        animation: lightboxScaleIn 0.3s cubic-bezier(0.16,1,0.3,1);
    }
    @keyframes lightboxScaleIn { from { opacity: 0; transform: scale(0.94); } to { opacity: 1; transform: scale(1); } }
    .gallery-lightbox .lightbox-image-wrap {
        width: 100%; border-radius: 20px; overflow: hidden;
        box-shadow: 0 30px 80px rgba(0,0,0,0.5); background: #0f172a;
        display: flex; align-items: center; justify-content: center; max-height: 72vh;
    }
    .gallery-lightbox .lightbox-image-wrap img { width: 100%; max-height: 72vh; object-fit: contain; display: block; }
    .gallery-lightbox .close-lightbox {
        position: absolute; top: -52px; right: 0; width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        color: #fff; font-size: 1.2rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center; transition: background 0.2s;
    }
    .gallery-lightbox .close-lightbox:hover { background: rgba(255,255,255,0.2); }
    .gallery-lightbox .nav-lightbox {
        position: absolute; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        color: #fff; font-size: 1.1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center; transition: all 0.2s;
    }
    .gallery-lightbox .nav-lightbox:hover { background: rgba(255,255,255,0.22); transform: translateY(-50%) scale(1.06); }
    .gallery-lightbox .nav-lightbox.prev { left: -20px; }
    .gallery-lightbox .nav-lightbox.next { right: -20px; }
    .gallery-lightbox .lightbox-footer { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 18px; }
    .gallery-lightbox .lightbox-caption {
        color: #fff; font-size: 0.9rem; font-weight: 600;
        background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
        padding: 7px 18px; border-radius: 999px;
    }
    .gallery-lightbox .lightbox-counter { color: rgba(255,255,255,0.5); font-size: 0.78rem; font-weight: 600; }
    .gallery-lightbox .lightbox-thumbnails { display: flex; gap: 8px; margin-top: 4px; max-width: 100%; overflow-x: auto; padding: 6px; scrollbar-width: none; }
    .gallery-lightbox .lightbox-thumbnails::-webkit-scrollbar { display: none; }
    .gallery-lightbox .lightbox-thumbnails img {
        width: 54px; height: 54px; object-fit: cover; border-radius: 10px; cursor: pointer;
        border: 2px solid transparent; opacity: 0.55; transition: all 0.2s; flex-shrink: 0;
    }
    .gallery-lightbox .lightbox-thumbnails img:hover { opacity: 0.85; }
    .gallery-lightbox .lightbox-thumbnails img.active { border-color: var(--brand-orange); opacity: 1; }
    @media (max-width: 767.98px) {
        .gallery-lightbox .nav-lightbox.prev { left: 4px; }
        .gallery-lightbox .nav-lightbox.next { right: 4px; }
        .gallery-lightbox .close-lightbox { top: 4px; right: 4px; background: rgba(0,0,0,0.45); }
    }

    .gallery-item { cursor: pointer; }
</style>

<div class="db-toolbar">
    <a href="{{ route('rooms.list') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <a href="{{ route('bookings.create') }}?room={{ $room->id }}" class="btn-primary-sm">
        <i class="bi bi-plus-lg"></i> Tambah Peminjaman
    </a>
</div>

{{-- ROOM HERO CARD --}}
<div class="room-hero-card">
    <div class="room-hero-text">
        <div class="room-title-row">
            <h1 class="room-title">{{ $room->name }}</h1>
        </div>
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
                        <div class="gallery-item" onclick="openLightbox({{ $loop->index }})">
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

        @include('pages.partials._room-today-schedule', ['schedule' => $schedule, 'today' => $today])
    </div>
</div>

{{-- LIGHTBOX FOTO --}}
<div class="gallery-lightbox" id="lightbox">
    <div class="lightbox-content">
        <button class="close-lightbox" onclick="closeLightbox()" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
        <button class="nav-lightbox prev" id="lightboxPrev" onclick="navigateLightbox(-1)" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
        <button class="nav-lightbox next" id="lightboxNext" onclick="navigateLightbox(1)" aria-label="Selanjutnya"><i class="bi bi-chevron-right"></i></button>
        <div class="lightbox-image-wrap">
            <img id="lightboxImage" src="" alt="Foto Ruangan">
        </div>
        <div class="lightbox-footer">
            <div class="lightbox-caption" id="lightboxCaption"></div>
            <div class="lightbox-counter" id="lightboxCounter"></div>
            <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
        </div>
    </div>
</div>

<script>
    (function () {
        var photos = @json($validPhotos->pluck('photo_url')->values());
        var roomName = @json($room->name);
        var idx = 0;

        window.openLightbox = function (startIndex) {
            if (!photos.length) return;
            idx = startIndex || 0;
            renderLightbox();
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        function renderLightbox() {
            document.getElementById('lightboxImage').src = photos[idx];
            document.getElementById('lightboxCaption').textContent = roomName;
            document.getElementById('lightboxCounter').textContent = (idx + 1) + ' dari ' + photos.length;

            var multi = photos.length > 1;
            document.getElementById('lightboxPrev').style.display = multi ? 'flex' : 'none';
            document.getElementById('lightboxNext').style.display = multi ? 'flex' : 'none';

            var thumbs = document.getElementById('lightboxThumbnails');
            thumbs.innerHTML = '';
            if (multi) {
                photos.forEach(function (p, i) {
                    var t = document.createElement('img');
                    t.src = p; t.className = i === idx ? 'active' : '';
                    t.onclick = function () { idx = i; renderLightbox(); };
                    thumbs.appendChild(t);
                });
            }
        }

        window.closeLightbox = function () {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        };
        window.navigateLightbox = function (dir) {
            var n = idx + dir;
            if (n < 0 || n >= photos.length) return;
            idx = n; renderLightbox();
        };
        document.addEventListener('keydown', function (e) {
            var lb = document.getElementById('lightbox');
            if (!lb || !lb.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') navigateLightbox(-1);
            else if (e.key === 'ArrowRight') navigateLightbox(1);
        });
        document.getElementById('lightbox').addEventListener('click', function (e) {
            if (e.target === this) closeLightbox();
        });
    })();
</script>

<script>
    // ============================================================
    // LIVE UPDATE — polling berkala (bukan WebSocket). Jadwal hari
    // ini di-refresh otomatis kalau ada booking baru disetujui/
    // berubah, supaya karyawan lihat ketersediaan yang selalu akurat
    // saat memutuskan mau booking jam berapa.
    // ============================================================
    (function () {
        var lastSignature = 'initial';

        createLivePoller({
            url: "{{ route('rooms.live-schedule', $room->id) }}",
            interval: 20000,
            onUpdate: function (data) {
                if (data.signature === lastSignature) return;
                lastSignature = data.signature;

                var el = document.getElementById('roomTodayScheduleCard');
                if (el) el.outerHTML = data.schedule;
            }
        });
    })();
    })();
</script>
@endsection