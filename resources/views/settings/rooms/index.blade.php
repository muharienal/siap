@extends('templates.template')

@section('page_title', 'Manajemen Ruangan')
@section('page_subtitle', 'Kelola data ruangan rapat')

@section('content')
<style>
    .settings-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .table-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .table-card .card-header {
        padding: var(--space-3) var(--space-5);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
        min-height: 48px;
    }
    .table-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .table-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
    .table-card .card-body {
        padding: 0;
        overflow-x: auto;
    }
    .table-booking {
        width: 100%;
        border-collapse: collapse;
        font-size: var(--font-size-sm);
        min-width: 700px;
    }
    .table-booking thead th {
        background: var(--bg-card);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: var(--font-size-xs);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: var(--space-2) var(--space-3);
        border-bottom: 1px solid var(--border-color-light);
        text-align: left;
        height: 38px;
    }
    .table-booking tbody td {
        padding: var(--space-2) var(--space-3);
        border-bottom: 1px solid var(--border-color-light);
        height: 44px;
        background: var(--bg-card);
        transition: background var(--transition-fast);
    }
    .table-booking tbody tr:nth-child(even) td { background: #fafbfc; }
    .table-booking tbody tr:hover td { background: rgba(249,115,22,0.03); }
    .btn-action-group { display:flex; gap:4px; flex-wrap:wrap; }
    .btn-action {
        padding: 4px 10px;
        border-radius: var(--radius-sm);
        font-size: var(--font-size-xs);
        border: none;
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        font-weight: 500;
    }
    .btn-action.edit {
        background: rgba(245,158,11,0.08);
        color: #d97706;
    }
    .btn-action.edit:hover {
        background: rgba(245,158,11,0.16);
    }
    .btn-action.delete {
        background: rgba(239,68,68,0.06);
        color: #dc2626;
    }
    .btn-action.delete:hover {
        background: rgba(239,68,68,0.12);
    }
    .btn-today {
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
        white-space: nowrap;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(249,115,22,0.15);
    }
    .btn-today:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16,185,129,0.20);
        color: var(--text-inverse);
    }
    .badge-status {
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        display: inline-block;
    }
    .badge-status.active {
        background: rgba(16,185,129,0.12);
        color: var(--brand-green-dark);
    }
    .badge-status.inactive {
        background: rgba(239,68,68,0.08);
        color: #dc2626;
    }
    .no-results {
        padding: var(--space-6);
        text-align: center;
        color: var(--text-muted);
    }
    .no-results i {
        font-size: 2rem;
        display: block;
        margin-bottom: var(--space-3);
        color: var(--border-color);
    }
    @media (max-width:991.98px) {
        .settings-content { padding: var(--space-3); }
    }
    @media (max-width:575.98px) {
        .settings-content { padding: var(--space-2); }
        .table-booking { font-size: var(--font-size-xs); min-width: 480px; }
    }

    .btn-preview {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 500;
        background: rgba(59,130,246,0.08);
        color: var(--brand-blue-dark);
        border: 1px solid rgba(59,130,246,0.15);
        cursor: pointer;
        transition: all var(--transition-fast);
        text-decoration: none;
    }
    .btn-preview:hover {
        background: rgba(59,130,246,0.16);
        transform: translateY(-1px);
    }
    .btn-preview.no-photo {
        background: var(--bg-body);
        color: var(--text-muted);
        border-color: var(--border-color);
        cursor: default;
    }
    .btn-preview.no-photo:hover {
        transform: none;
        background: var(--bg-body);
    }

    /* Lightbox */
    .gallery-lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.92);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease;
    }
    .gallery-lightbox.active {
        display: flex;
    }
    .gallery-lightbox img {
        max-width: 85%;
        max-height: 85%;
        object-fit: contain;
        border-radius: var(--radius-sm);
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .gallery-lightbox .close-lightbox {
        position: absolute;
        top: 24px;
        right: 32px;
        font-size: 2.5rem;
        color: #fff;
        background: none;
        border: none;
        cursor: pointer;
        transition: transform var(--transition-fast);
        line-height: 1;
    }
    .gallery-lightbox .close-lightbox:hover {
        transform: scale(1.2);
    }
    .gallery-lightbox .nav-lightbox {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        color: #fff;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        cursor: pointer;
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gallery-lightbox .nav-lightbox:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-50%) scale(1.05);
    }
    .gallery-lightbox .nav-lightbox.prev { left: 24px; }
    .gallery-lightbox .nav-lightbox.next { right: 24px; }
    .gallery-lightbox .lightbox-caption {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        font-size: var(--font-size-sm);
        background: rgba(0,0,0,0.5);
        padding: var(--space-2) var(--space-4);
        border-radius: var(--radius-pill);
        max-width: 80%;
        text-align: center;
    }
    .gallery-lightbox .lightbox-counter {
        position: absolute;
        top: 24px;
        left: 50%;
        transform: translateX(-50%);
        color: rgba(255,255,255,0.6);
        font-size: var(--font-size-sm);
        background: rgba(0,0,0,0.3);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-pill);
    }
    .gallery-lightbox .lightbox-thumbnails {
        position: absolute;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        max-width: 80%;
        overflow-x: auto;
        padding: 8px;
        background: rgba(0,0,0,0.3);
        border-radius: var(--radius-sm);
    }
    .gallery-lightbox .lightbox-thumbnails img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: var(--radius-xs);
        cursor: pointer;
        border: 2px solid transparent;
        transition: all var(--transition-fast);
    }
    .gallery-lightbox .lightbox-thumbnails img:hover {
        border-color: rgba(255,255,255,0.5);
    }
    .gallery-lightbox .lightbox-thumbnails img.active {
        border-color: var(--brand-orange);
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<div class="settings-content">

    <div class="greeting-section" style="margin-bottom: var(--space-5);">
        <h1 class="greeting-title">Manajemen Ruangan</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-door-open me-1"></i> Kelola semua ruangan rapat</span>
        </div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <div class="title">
                <i class="bi bi-table"></i> Daftar Ruangan
                <span style="font-weight:400; font-size:var(--font-size-sm); color:var(--text-muted); margin-left:var(--space-1);">
                    {{ $rooms->count() }} total
                </span>
            </div>
            <a href="{{ route('settings.rooms.create') }}" class="btn-today">
                <i class="bi bi-plus-lg"></i> Tambah Ruangan
            </a>
        </div>
        <div class="card-body">
            @if($rooms->count() > 0)
                <table class="table-booking">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="min-width:160px;">Nama Ruangan</th>
                            <th style="min-width:90px;">Kapasitas</th>
                            <th style="min-width:130px;">Lokasi</th>
                            <th style="min-width:120px;">Foto</th>
                            <th style="min-width:90px;">Status</th>
                            <th style="min-width:180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $item)
                            @php
                                // Gunakan optional untuk menghindari error jika photos null
                                $photos = $item->photos ?? collect();
                                $hasPhotos = $photos->count() > 0;
                                $photoCount = $photos->count();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->capacity }} orang</td>
                                <td>{{ $item->location }}</td>
                                <td>
                                    @if($hasPhotos)
                                        <button class="btn-preview" onclick="openGallery({{ $loop->index }})">
                                            <i class="bi bi-images"></i> {{ $photoCount }} foto
                                        </button>
                                    @else
                                        <span class="btn-preview no-photo">
                                            <i class="bi bi-image"></i> Tidak ada
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 1)
                                        <span class="badge-status active">Aktif</span>
                                    @else
                                        <span class="badge-status inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-action-group">
                                        <a href="{{ route('settings.rooms.edit', $item->id) }}" class="btn-action edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('settings.rooms.destroy', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus ruangan ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-results">
                    <i class="bi bi-door-open"></i>
                    <p>Belum ada ruangan</p>
                    <a href="{{ route('settings.rooms.create') }}" style="color:var(--brand-orange); text-decoration:none; font-weight:600;">Tambah ruangan pertama</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Lightbox -->
<div class="gallery-lightbox" id="lightbox">
    <button class="close-lightbox" onclick="closeLightbox()" aria-label="Tutup">&times;</button>
    <button class="nav-lightbox prev" onclick="navigateLightbox(-1)" aria-label="Sebelumnya">&#10094;</button>
    <button class="nav-lightbox next" onclick="navigateLightbox(1)" aria-label="Selanjutnya">&#10095;</button>
    <img id="lightboxImage" src="" alt="Foto Ruangan">
    <div class="lightbox-counter" id="lightboxCounter"></div>
    <div class="lightbox-caption" id="lightboxCaption"></div>
    <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
</div>

<script>
    // Data ruangan dari server
    var roomsData = @json($roomsData);

    var currentRoomIndex = 0;
    var currentPhotoIndex = 0;
    var currentPhotos = [];

    function openGallery(index) {
        var room = roomsData[index];
        if (!room || !room.photos || room.photos.length === 0) {
            return;
        }

        currentRoomIndex = index;
        currentPhotos = room.photos;
        currentPhotoIndex = 0;

        updateLightbox();
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function updateLightbox() {
        var img = document.getElementById('lightboxImage');
        var caption = document.getElementById('lightboxCaption');
        var counter = document.getElementById('lightboxCounter');
        var thumbs = document.getElementById('lightboxThumbnails');

        var room = roomsData[currentRoomIndex];
        var photo = currentPhotos[currentPhotoIndex];

        if (photo) {
            img.src = photo;
            img.alt = room.name;
        }

        caption.textContent = room.name + ' - ' + (currentPhotoIndex + 1) + '/' + currentPhotos.length;
        counter.textContent = (currentPhotoIndex + 1) + ' dari ' + currentPhotos.length;

        // Thumbnails
        thumbs.innerHTML = '';
        currentPhotos.forEach(function(p, idx) {
            var thumb = document.createElement('img');
            thumb.src = p;
            thumb.alt = 'Thumbnail ' + (idx + 1);
            thumb.className = idx === currentPhotoIndex ? 'active' : '';
            thumb.onclick = function() {
                currentPhotoIndex = idx;
                updateLightbox();
            };
            thumbs.appendChild(thumb);
        });

        var activeThumb = thumbs.querySelector('.active');
        if (activeThumb) {
            activeThumb.scrollIntoView({ block: 'nearest', inline: 'center' });
        }
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    }

    function navigateLightbox(direction) {
        var newIndex = currentPhotoIndex + direction;
        if (newIndex < 0 || newIndex >= currentPhotos.length) {
            return;
        }
        currentPhotoIndex = newIndex;
        updateLightbox();
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('lightbox').classList.contains('active')) {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                navigateLightbox(-1);
            } else if (e.key === 'ArrowRight') {
                navigateLightbox(1);
            }
        }
    });

    // Tutup lightbox saat klik di luar gambar
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
</script>
@endsection