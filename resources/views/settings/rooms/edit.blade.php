@extends('templates.template')

@section('page_title', 'Edit Ruangan')
@section('page_subtitle', 'Ubah data ruangan rapat')

@section('content')
<style>
    .form-content { padding: var(--space-5) var(--space-6); max-width:800px; margin:0 auto; }
    .form-card { background:var(--bg-card); border-radius:var(--radius-card); border:none; box-shadow:var(--shadow-card); overflow:hidden; }
    .form-card .card-header { padding:var(--space-4) var(--space-5); border-bottom:1px solid var(--border-color-light); }
    .form-card .card-body { padding:var(--space-5); }
    .form-group { margin-bottom:var(--space-4); }
    .form-group .form-label { font-weight:600; font-size:var(--font-size-sm); color:var(--text-secondary); display:block; margin-bottom:var(--space-1); }
    .form-control, .form-select { height:42px; padding:0 var(--space-3); font-size:var(--font-size-sm); background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--radius-sm); width:100%; }
    .form-control:focus, .form-select:focus { border-color:var(--brand-orange); box-shadow:0 0 0 3px rgba(249,115,22,0.06); outline:none; }
    .btn-submit { height:42px; padding:0 var(--space-5); background:var(--brand-gradient); border:none; border-radius:var(--radius-sm); font-weight:600; font-size:var(--font-size-sm); color:#fff; transition:all var(--transition-fast); cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
    .btn-submit:hover { background:var(--brand-gradient-hover); transform:translateY(-1px); }
    .btn-cancel { height:42px; padding:0 var(--space-5); background:transparent; border:1px solid var(--border-color); border-radius:var(--radius-sm); font-weight:600; font-size:var(--font-size-sm); color:var(--text-secondary); transition:all var(--transition-fast); cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
    .btn-cancel:hover { border-color:var(--brand-orange); color:var(--brand-orange-dark); }
    .alert-danger { background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.10); color:#dc2626; padding:var(--space-3) var(--space-4); border-radius:var(--radius-sm); margin-bottom:var(--space-4); }
    .alert-danger ul { margin:0; padding-left:var(--space-4); }
    .existing-photos { display:flex; gap:8px; flex-wrap:wrap; margin:var(--space-2) 0; }
    .existing-photos .photo-item { position:relative; width:80px; height:80px; border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border-color); background:var(--bg-body); }
    .existing-photos .photo-item img { width:100%; height:100%; object-fit:cover; }
    .existing-photos .photo-item .delete-photo { position:absolute; top:2px; right:2px; background:rgba(239,68,68,0.85); color:#fff; border:none; border-radius:50%; width:18px; height:18px; font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all var(--transition-fast); }
    .existing-photos .photo-item .delete-photo:hover { background:#ef4444; transform:scale(1.1); }
    .drop-zone { border:2px dashed var(--border-color); border-radius:var(--radius-sm); padding:var(--space-4); text-align:center; cursor:pointer; transition:all var(--transition-fast); background:var(--bg-body); min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center; position:relative; }
    .drop-zone:hover { border-color:var(--brand-orange); background:rgba(249,115,22,0.02); }
    .drop-zone .drop-icon { font-size:2rem; color:var(--text-muted); margin-bottom:var(--space-1); }
    .drop-zone .drop-text { font-size:var(--font-size-sm); color:var(--text-secondary); }
    .drop-zone .drop-sub { font-size:var(--font-size-xs); color:var(--text-muted); }
    .drop-zone input[type="file"] { position:absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer; }
    .photo-preview-grid { display:flex; flex-wrap:wrap; gap:8px; margin-top:var(--space-2); }
    .photo-preview-grid .preview-item { position:relative; width:80px; height:80px; border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border-color); background:var(--bg-body); }
    .photo-preview-grid .preview-item img { width:100%; height:100%; object-fit:cover; }
    .photo-preview-grid .preview-item .remove-preview { position:absolute; top:2px; right:2px; background:rgba(239,68,68,0.85); color:#fff; border:none; border-radius:50%; width:18px; height:18px; font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .photo-preview-grid .preview-item .remove-preview:hover { background:#ef4444; }
    @media (max-width:991.98px) { .form-content { padding:var(--space-3); } .form-card .card-body { padding:var(--space-4); } }
    @media (max-width:575.98px) { .form-content { padding:var(--space-2); } .form-card .card-body { padding:var(--space-3); } }
</style>

<div class="form-content">

    <div class="greeting-section" style="margin-bottom:var(--space-5);">
        <h1 class="greeting-title">Edit Ruangan</h1>
        <div class="greeting-sub"><span><i class="bi bi-door-open me-1"></i> Ubah data ruangan rapat</span></div>
    </div>

    <div class="form-card">
        <div class="card-header"><h5 class="card-title" style="font-weight:700; margin:0;"><i class="bi bi-pencil-square me-2"></i>Form Ruangan</h5></div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert-danger">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('settings.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data" id="roomForm">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $room->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Kapasitas (orang) <span class="text-danger">*</span></label>
                    <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $room->capacity) }}" min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $room->location) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $room->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Ruangan</label>
                    <!-- Existing Photos -->
                    <div class="existing-photos" id="existingPhotos">
                        @if($room->photos && $room->photos->count() > 0)
                            @foreach($room->photos as $photo)
                                <div class="photo-item" data-id="{{ $photo->id }}">
                                    <img src="{{ $photo->photo_url }}" alt="Foto Ruangan">
                                    <button type="button" class="delete-photo" onclick="toggleDeletePhoto({{ $photo->id }}, this)">×</button>
                                </div>
                            @endforeach
                        @else
                            <span style="font-size:var(--font-size-xs); color:var(--text-muted); padding:var(--space-1) 0;">Belum ada foto</span>
                        @endif
                    </div>
                    <input type="hidden" name="delete_photos" id="deletePhotos" value="">

                    <!-- Upload new photos -->
                    <div class="drop-zone" id="dropZone">
                        <div class="drop-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="drop-text">Klik atau drag & drop untuk tambah foto</div>
                        <div class="drop-sub">Format: JPG, PNG, GIF, SVG. Maks 2MB per file</div>
                        <input type="file" name="photos[]" accept="image/*" multiple id="photoInput">
                    </div>
                    <div class="photo-preview-grid" id="photoPreviewGrid"></div>
                    <small class="form-text text-muted" style="font-size:var(--font-size-xs);">Tambahkan foto baru (bisa pilih beberapa).</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (old('status', $room->status) == 1) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ (old('status', $room->status) == 0) ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('settings.rooms.index') }}" class="btn-cancel"><i class="bi bi-x-lg"></i> Batal</a>
                    <button type="submit" class="btn-submit"><i class="bi bi-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let deletedPhotoIds = [];

    function toggleDeletePhoto(photoId, button) {
        const item = button.closest('.photo-item');
        const id = parseInt(photoId);

        const idx = deletedPhotoIds.indexOf(id);
        if (idx > -1) {
            // Undo delete
            deletedPhotoIds.splice(idx, 1);
            item.style.opacity = '1';
            item.style.pointerEvents = 'auto';
            button.innerHTML = '×';
            button.style.background = 'rgba(239,68,68,0.85)';
        } else {
            // Mark for delete
            deletedPhotoIds.push(id);
            item.style.opacity = '0.3';
            item.style.pointerEvents = 'none';
            button.innerHTML = '✓';
            button.style.background = 'rgba(16,185,129,0.85)';
        }
        document.getElementById('deletePhotos').value = deletedPhotoIds.join(',');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('photoInput');
        const previewGrid = document.getElementById('photoPreviewGrid');
        const dropZone = document.getElementById('dropZone');

        function updatePreview(files) {
            previewGrid.innerHTML = '';
            if (!files || files.length === 0) return;

            Array.from(files).forEach(function(file, index) {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                const item = document.createElement('div');
                item.className = 'preview-item';
                reader.onload = function(e) {
                    item.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <button type="button" class="remove-preview" data-index="${index}">×</button>
                    `;
                    previewGrid.appendChild(item);

                    item.querySelector('.remove-preview').addEventListener('click', function() {
                        const dt = new DataTransfer();
                        const remaining = Array.from(fileInput.files).filter((_, i) => i !== index);
                        remaining.forEach(f => dt.items.add(f));
                        fileInput.files = dt.files;
                        updatePreview(fileInput.files);
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        fileInput.addEventListener('change', function() {
            updatePreview(this.files);
        });

        if (dropZone) {
            dropZone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
            dropZone.addEventListener('dragleave', function(e) { e.preventDefault(); this.classList.remove('dragover'); });
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const dt = new DataTransfer();
                    Array.from(files).forEach(f => dt.items.add(f));
                    fileInput.files = dt.files;
                    updatePreview(fileInput.files);
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
        }

        document.getElementById('roomForm').addEventListener('submit', function(e) {
            // Pastikan delete_photos terisi
            document.getElementById('deletePhotos').value = deletedPhotoIds.join(',');
        });
    });
</script>
@endsection