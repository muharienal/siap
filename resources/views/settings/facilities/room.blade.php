@extends('templates.template')

@section('page_title', 'Fasilitas ' . $room->name)
@section('page_subtitle', 'Kelola fasilitas untuk ruangan ini')

@section('content')
<style>
    .room-facility-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1100px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .back-link {
        font-size: var(--font-size-sm);
        color: var(--text-muted);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-bottom: var(--space-4);
        transition: color var(--transition-fast);
    }
    .back-link:hover { color: var(--brand-orange); }

    /* Header ruangan */
    .room-hero {
        position: relative;
        border-radius: var(--radius-card);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        margin-bottom: var(--space-5);
        min-height: 160px;
        display: flex;
        align-items: flex-end;
        background: var(--bg-input);
    }
    .room-hero img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .room-hero .room-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15,23,42,0) 30%, rgba(15,23,42,0.78) 100%);
    }
    .room-hero .room-hero-icon {
        font-size: 2.5rem;
        color: var(--text-muted);
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .room-hero-body {
        position: relative;
        z-index: 1;
        padding: var(--space-4) var(--space-5);
        width: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: var(--space-2);
    }
    .room-hero-body h2 {
        font-size: var(--font-size-xl);
        font-weight: 700;
        color: #fff;
        margin: 0 0 4px 0;
    }
    .room-hero-meta {
        display: flex;
        gap: var(--space-4);
        flex-wrap: wrap;
        font-size: var(--font-size-sm);
        color: rgba(255,255,255,0.88);
    }
    .room-hero-meta span { display: flex; align-items: center; gap: 5px; }
    .room-hero-count {
        background: rgba(255,255,255,0.16);
        backdrop-filter: blur(6px);
        color: #fff;
        font-weight: 600;
        font-size: var(--font-size-sm);
        padding: 6px 14px;
        border-radius: var(--radius-pill);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .alert-success {
        background: rgba(16,185,129,0.10);
        color: #059669;
        padding: var(--space-3) var(--space-4);
        border-radius: var(--radius-sm);
        margin-bottom: var(--space-4);
        font-size: var(--font-size-sm);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Panel tambah fasilitas */
    .add-panel {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: 1px dashed var(--border-color-light);
        padding: var(--space-4) var(--space-5);
        margin-bottom: var(--space-5);
    }
    .add-panel-title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: var(--space-3);
    }
    .add-panel-title i { color: var(--brand-orange); font-size: 1.2rem; }
    .add-facility-row {
        display: flex;
        gap: var(--space-2);
        flex-wrap: wrap;
        align-items: center;
    }
    .add-facility-row > select {
        height: 44px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color-light);
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        color: var(--text-primary);
        outline: none;
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
    }
    .add-facility-row > select:focus {
        border-color: var(--brand-orange);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.12);
    }
    .add-facility-row > select { flex: 1; min-width: 220px; }

    .new-facility-field {
        position: relative;
        min-width: 200px;
        flex: 1;
    }
    .new-facility-field i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--brand-orange);
        font-size: 0.95rem;
        pointer-events: none;
    }
    .new-facility-field input[type="text"] {
        width: 100%;
        height: 44px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color-light);
        background: var(--bg-input);
        padding: 0 var(--space-3) 0 40px;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        outline: none;
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
    }
    .new-facility-field input[type="text"]::placeholder { color: var(--text-muted); }
    .new-facility-field input[type="text"]:focus {
        border-color: var(--brand-orange);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.12);
    }
    .btn-add-submit {
        height: 44px;
        padding: 0 var(--space-5);
        background: var(--brand-gradient);
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-inverse);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all var(--transition-fast);
        box-shadow: 0 2px 8px rgba(249,115,22,0.15);
    }
    .btn-add-submit:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16,185,129,0.20);
    }
    .add-panel-empty {
        margin: 0;
        font-size: var(--font-size-sm);
        color: var(--text-muted);
    }
    .add-panel-empty a { color: var(--brand-orange); font-weight: 600; text-decoration: none; }

    /* Daftar fasilitas: kartu bukan tabel */
    .section-title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        margin-bottom: var(--space-3);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title i { color: var(--brand-orange); }

    .facility-table-header {
        --facility-cols: 2fr 2fr 110px 100px;
        display: grid;
        grid-template-columns: var(--facility-cols);
        gap: var(--space-3);
        padding: 0 var(--space-4) var(--space-2) var(--space-4);
        font-size: var(--font-size-xs);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
    }
    .facility-table-header .col-qty,
    .facility-table-header .col-actions {
        text-align: center;
    }
    .facility-item-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }
    .facility-item {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: var(--space-3) var(--space-4);
        display: grid;
        grid-template-columns: 2fr 2fr 110px 100px;
        gap: var(--space-3);
        align-items: center;
        transition: all var(--transition-fast);
    }
    .facility-item:hover {
        border-color: var(--brand-orange);
        box-shadow: var(--shadow-hover);
    }
    .facility-col-name {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        min-width: 0;
    }
    .facility-col-desc {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        min-width: 0;
        overflow-wrap: break-word;
    }
    .facility-col-qty { flex-wrap: nowrap; }
    .facility-col-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-2);
    }
    .facility-item-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-sm);
        background: rgba(249,115,22,0.08);
        color: var(--brand-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .facility-item-info { flex: 1; min-width: 140px; }
    .facility-item-name {
        font-weight: 700;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
    }
    .facility-item-location {
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }
    .facility-col-qty-display {
        font-size: var(--font-size-sm);
        font-weight: 400;
        color: var(--text-primary);
        text-align: center;
    }
    .qty-stepper {
        display: inline-flex;
        align-items: stretch;
        box-sizing: border-box;
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-pill);
        overflow: hidden;
        height: 36px;
        background: var(--bg-input);
    }
    .qty-stepper.qty-stepper-lg { height: 44px; }
    .qty-stepper.qty-stepper-lg button { width: 38px; font-size: 1.1rem; }
    .qty-stepper.qty-stepper-lg input { width: 50px; }
    .qty-stepper button {
        box-sizing: border-box;
        width: 32px;
        flex: 0 0 auto;
        border: none;
        margin: 0;
        padding: 0;
        background: var(--bg-input);
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background var(--transition-fast), color var(--transition-fast);
    }
    .qty-stepper button:hover { background: rgba(249,115,22,0.10); color: var(--brand-orange); }
    .qty-stepper input {
        box-sizing: border-box;
        width: 44px;
        flex: 0 0 auto;
        height: auto;
        align-self: stretch;
        border: none;
        border-left: 1px solid var(--border-color-light);
        border-right: 1px solid var(--border-color-light);
        margin: 0;
        padding: 0;
        background: var(--bg-card);
        text-align: center;
        font-size: var(--font-size-sm);
        font-weight: 600;
        font-family: inherit;
        line-height: 1;
        color: var(--text-primary);
        outline: none;
        -moz-appearance: textfield;
        appearance: textfield;
    }
    .qty-stepper input::-webkit-inner-spin-button,
    .qty-stepper input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .qty-stepper input:focus { background: #fff; }
    .btn-icon-save {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: rgba(16,185,129,0.10);
        color: #059669;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        transition: all var(--transition-fast);
    }
    .btn-icon-save:hover { background: #059669; color: #fff; }
    .btn-icon-edit {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: rgba(249,115,22,0.08);
        color: var(--brand-orange);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all var(--transition-fast);
    }
    .btn-icon-edit:hover { background: var(--brand-orange); color: #fff; }
    .btn-icon-delete {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: rgba(239,68,68,0.06);
        color: #dc2626;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        transition: all var(--transition-fast);
    }
    .btn-icon-delete:hover { background: #dc2626; color: #fff; }
    .qty-form, .delete-form { display: inline-flex; align-items: center; gap: var(--space-2); }

    .no-results {
        padding: var(--space-8);
        text-align: center;
        color: var(--text-muted);
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: 1px dashed var(--border-color-light);
    }
    .no-results i { font-size: 2rem; display: block; margin-bottom: var(--space-3); color: var(--border-color); }

    @media (max-width: 767.98px) {
        .facility-table-header { display: none; }
        .facility-item { grid-template-columns: 1fr; }
        .facility-col-actions { justify-content: flex-end; }
    }
    @media (max-width: 575.98px) {
        .room-facility-content { padding: var(--space-3); }
        .facility-item-info { text-align: left; }
    }
</style>

<div class="room-facility-content">
    <a href="{{ route('settings.facilities.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke Fasilitas Ruangan
    </a>

    <div class="room-hero">
        @if($room->photos->count())
            <img src="{{ $room->photos->first()->photo_url }}" alt="Foto {{ $room->name }}">
            <div class="room-hero-overlay"></div>
        @else
            <div class="room-hero-icon"><i class="bi bi-image"></i></div>
            <div class="room-hero-overlay"></div>
        @endif
        <div class="room-hero-body">
            <div>
                <h2>{{ $room->name }}</h2>
                <div class="room-hero-meta">
                    <span><i class="bi bi-geo-alt"></i> {{ $room->location }}</span>
                    <span><i class="bi bi-people"></i> Kapasitas {{ $room->capacity }} orang</span>
                </div>
            </div>
            <span class="room-hero-count"><i class="bi bi-tools"></i> {{ $room->facilities->count() }} fasilitas</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="add-panel">
        <div class="add-panel-title"><i class="bi bi-plus-circle"></i> Tambah Fasilitas ke Ruangan</div>
        <form method="POST" action="{{ route('settings.facilities.room.attach', $room->id) }}" class="add-facility-row" id="addFacilityForm">
            @csrf
            <select name="facility_id" id="facilitySelect" required onchange="toggleNewFacilityFields()">
                <option value="">Pilih fasilitas...</option>
                @foreach($availableFacilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                @endforeach
                <option value="new">+ Fasilitas baru...</option>
            </select>

            <div class="new-facility-field" id="newFacilityNameWrap" style="display:none;">
                <i class="bi bi-tag"></i>
                <input type="text" name="new_facility_name" id="newFacilityName" placeholder="Nama fasilitas baru">
            </div>
            <div class="new-facility-field" id="newFacilityLocationWrap" style="display:none;">
                <i class="bi bi-geo-alt"></i>
                <input type="text" name="new_facility_location" id="newFacilityLocation" placeholder="Lokasi penyimpanan">
            </div>

            <div class="qty-stepper qty-stepper-lg">
                <button type="button" onclick="stepQty(this, -1)">&minus;</button>
                <input type="number" name="quantity" id="addQuantityInput" value="1" min="1">
                <button type="button" onclick="stepQty(this, 1)">&plus;</button>
            </div>
            <button type="submit" class="btn-add-submit"><i class="bi bi-plus-lg"></i> Tambahkan</button>
        </form>
        @if(!$availableFacilities->count())
            <p class="add-panel-empty" style="margin-top:var(--space-2);">
                Semua fasilitas yang ada sudah ditambahkan ke ruangan ini — pilih <strong>"+ Fasilitas baru..."</strong> di atas untuk membuat jenis baru.
            </p>
        @endif
    </div>

    <script>
        function toggleNewFacilityFields() {
            const isNew = document.getElementById('facilitySelect').value === 'new';
            const nameWrap = document.getElementById('newFacilityNameWrap');
            const locationWrap = document.getElementById('newFacilityLocationWrap');
            nameWrap.style.display = isNew ? 'block' : 'none';
            locationWrap.style.display = isNew ? 'block' : 'none';
            document.getElementById('newFacilityName').required = isNew;
            document.getElementById('newFacilityLocation').required = isNew;
        }
    </script>

    <div class="section-title"><i class="bi bi-list-check"></i> Fasilitas di {{ $room->name }}</div>

    @if($room->facilities->count())
        <div class="facility-table-header">
            <div>Nama Fasilitas</div>
            <div>Deskripsi</div>
            <div class="col-qty">Jumlah</div>
            <div class="col-actions">Aksi</div>
        </div>
        <div class="facility-item-list">
            @foreach($room->facilities as $facility)
                <div class="facility-item">
                    <div class="facility-col-name">
                        <div class="facility-item-icon"><i class="bi bi-tools"></i></div>
                        <div class="facility-item-info">
                            <div class="facility-item-name">{{ $facility->name }}</div>
                            <div class="facility-item-location"><i class="bi bi-geo-alt"></i> {{ $facility->storage_location }}</div>
                        </div>
                    </div>

                    <div class="facility-col-desc">
                        {{ $facility->description ?: '—' }}
                    </div>

                    <div class="facility-col-qty-display">{{ $facility->pivot->quantity }}</div>

                    <div class="facility-col-actions">
                        <a href="{{ route('settings.facilities.edit', $facility->id) }}?room_id={{ $room->id }}" class="btn-icon-edit" title="Edit fasilitas">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form method="POST" action="{{ route('settings.facilities.room.detach', [$room->id, $facility->id]) }}" class="delete-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon-delete" title="Hapus" onclick="return confirm('Hapus fasilitas ini dari ruangan?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-results">
            <i class="bi bi-tools"></i>
            <p>Belum ada fasilitas di ruangan ini.</p>
        </div>
    @endif
</div>

<script>
    function stepQty(button, delta) {
        const input = button.parentElement.querySelector('input[name="quantity"]');
        const newVal = Math.max(1, parseInt(input.value || '1', 10) + delta);
        input.value = newVal;
    }
</script>
@endsection