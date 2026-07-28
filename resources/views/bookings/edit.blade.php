@extends('templates.template')

@section('page_title', 'Edit Peminjaman')
@section('page_subtitle', 'Ubah detail peminjaman ruangan')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .form-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1200px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .greeting-section {
        margin-bottom: var(--space-5);
    }
    .greeting-section .greeting-title {
        font-size: var(--font-size-xl);
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.01em;
    }
    .greeting-section .greeting-sub {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-1) 0 0 0;
        display: flex;
        align-items: center;
        gap: var(--space-2);
        flex-wrap: wrap;
    }
    .form-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .form-card .card-header {
        padding: var(--space-4) var(--space-6);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
    }
    .form-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .form-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
    .form-card .card-header .badge-status {
        padding: 4px 14px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-status.pending {
        background: rgba(245, 158, 11, 0.10);
        color: #d97706;
    }
    .badge-status.approved {
        background: rgba(16, 185, 129, 0.10);
        color: var(--brand-green-dark);
    }
    .badge-status.rejected {
        background: rgba(239, 68, 68, 0.06);
        color: #dc2626;
    }
    .form-card .card-body {
        padding: var(--space-6);
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-6);
    }
    @media (max-width: 991.98px) {
        .form-grid { grid-template-columns: 1fr; gap: var(--space-4); }
        .form-content { padding: var(--space-3); }
        .form-card .card-body { padding: var(--space-4); }
        .form-card .card-header { padding: var(--space-3) var(--space-4); }
    }
    @media (max-width: 575.98px) {
        .form-content { padding: var(--space-2); }
        .form-card .card-body { padding: var(--space-3); }
        .form-card .card-header { padding: var(--space-2) var(--space-3); flex-direction: column; align-items: flex-start; }
        .datetime-wrapper { flex-wrap: wrap; }
        .datetime-wrapper .date-input { flex: 1 1 100%; }
        .datetime-wrapper .time-input { flex: 1 1 40%; }
    }
    .form-group {
        margin-bottom: var(--space-4);
    }
    .form-group .form-label {
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: block;
        margin-bottom: var(--space-1);
    }
    .form-group .form-label .text-danger { color: #ef4444; }
    .form-group .form-label .text-muted { font-weight: 400; color: var(--text-muted); font-size: var(--font-size-xs); }
    .form-control, .form-select {
        height: 44px;
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        width: 100%;
        transition: border var(--transition-fast), box-shadow var(--transition-fast), background var(--transition-fast);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--brand-orange);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.06);
        outline: none;
    }
    .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; }
    textarea.form-control { height: auto; min-height: 100px; resize: vertical; padding: var(--space-2) var(--space-3); }
    .invalid-feedback { font-size: var(--font-size-xs); color: #ef4444; margin-top: var(--space-1); }
    .datetime-wrapper { display: flex; gap: var(--space-2); align-items: center; flex-wrap: wrap; }
    .datetime-wrapper .date-input { flex: 2; position: relative; min-width: 150px; }
    .datetime-wrapper .time-input { flex: 1; position: relative; min-width: 100px; }
    .datetime-wrapper .form-control { padding-left: 36px; height: 44px; }
    .datetime-wrapper .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem; pointer-events: none; z-index: 2; }
    .datetime-wrapper .time-separator { color: var(--text-muted); font-weight: 500; font-size: var(--font-size-sm); }
    .availability-status { padding: var(--space-2) var(--space-4); border-radius: var(--radius-sm); font-size: var(--font-size-sm); display: none; align-items: center; gap: var(--space-2); margin-top: var(--space-2); animation: fadeUp 0.3s ease forwards; }
    .availability-status.visible { display: flex; }
    .availability-status.loading { background: rgba(59, 130, 246, 0.06); color: var(--brand-blue-dark); border: 1px solid rgba(59, 130, 246, 0.10); }
    .availability-status.available { background: rgba(16, 185, 129, 0.08); color: var(--brand-green-dark); border: 1px solid rgba(16, 185, 129, 0.15); }
    .availability-status.unavailable { background: rgba(239, 68, 68, 0.06); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.10); }
    .availability-status .spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(59, 130, 246, 0.15); border-top-color: var(--brand-blue-dark); border-radius: 50%; animation: spin 0.8s linear infinite; flex-shrink: 0; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .select2-container--bootstrap-5 .select2-selection--multiple { min-height: 44px; border-color: var(--border-color); border-radius: var(--radius-sm); background: var(--bg-input); padding: 2px 6px; }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice { background: rgba(249, 115, 22, 0.08); border: 1px solid rgba(249, 115, 22, 0.15); color: var(--brand-orange-dark); border-radius: var(--radius-pill); padding: 2px 12px; font-size: var(--font-size-xs); font-weight: 500; display: inline-flex; align-items: center; gap: 4px; }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove { color: var(--text-muted); font-size: 0.9rem; margin-right: 2px; cursor: pointer; transition: color var(--transition-fast); }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover { color: #ef4444; }
    .select2-container--bootstrap-5 .select2-dropdown { border-color: var(--border-color); border-radius: var(--radius-sm); box-shadow: var(--shadow-dropdown); }
    .btn-submit { height: 44px; padding: 0 var(--space-6); background: var(--brand-gradient); border: none; border-radius: var(--radius-sm); font-weight: 600; font-size: var(--font-size-sm); color: var(--text-inverse); transition: all var(--transition-fast); cursor: pointer; display: inline-flex; align-items: center; gap: var(--space-2); box-shadow: 0 2px 8px rgba(249, 115, 22, 0.15); }
    .btn-submit:hover { background: var(--brand-gradient-hover); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(16, 185, 129, 0.20); color: var(--text-inverse); }
    .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    .btn-cancel { height: 44px; padding: 0 var(--space-5); background: transparent; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600; font-size: var(--font-size-sm); color: var(--text-secondary); transition: all var(--transition-fast); cursor: pointer; display: inline-flex; align-items: center; gap: var(--space-2); text-decoration: none; }
    .btn-cancel:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); background: rgba(249, 115, 22, 0.04); }
    .facility-quantity { display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-1); }
    .facility-quantity label { font-weight: 500; font-size: var(--font-size-sm); color: var(--text-secondary); min-width: 100px; }
    .facility-quantity input { width: 80px; height: 36px; text-align: center; padding: 0 var(--space-2); border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-input); font-size: var(--font-size-sm); }
    .facility-quantity input:focus { border-color: var(--brand-orange); outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.06); }
    .recommended-slots { margin-top: var(--space-2); display: flex; flex-wrap: wrap; gap: 6px; }
    .recommended-slots .slot-btn { padding: 4px 14px; border-radius: var(--radius-pill); font-size: var(--font-size-xs); font-weight: 500; background: rgba(59, 130, 246, 0.06); color: var(--brand-blue-dark); border: 1px solid rgba(59, 130, 246, 0.10); cursor: pointer; transition: all var(--transition-fast); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .recommended-slots .slot-btn:hover { background: rgba(59, 130, 246, 0.12); transform: translateY(-1px); }
    .recommended-slots .slot-btn i { font-size: 0.7rem; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .form-card { animation: fadeUp 0.4s ease forwards; }

    .room-select-wrapper, .facility-select-wrapper { position: relative; }
    .room-select-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-2) var(--space-3);
        min-height: 60px;
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        cursor: pointer;
        text-align: left;
    }
    .room-select-trigger.is-invalid { border-color: var(--bs-danger, #dc3545); }
    .room-select-thumb {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-xs);
        overflow: hidden;
        flex-shrink: 0;
        background: var(--surface-1, #f1f1f1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .room-select-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .room-select-text { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .room-select-name { font-weight: 600; font-size: var(--font-size-sm); color: var(--text-primary); }
    .room-select-capacity { font-size: var(--font-size-xs); color: var(--text-muted); }
    .room-select-chevron { color: var(--text-muted); flex-shrink: 0; }
    .room-select-panel, .facility-select-panel {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 40;
        max-height: 280px;
        overflow-y: auto;
        padding: var(--space-2);
    }
    .room-select-panel.open, .facility-select-panel.open { display: block; }
    .room-select-option {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-2);
        border-radius: var(--radius-xs);
        cursor: pointer;
    }
    .room-select-option:hover { background: var(--bg-hover, #f8f9fa); }
    .facility-checkbox-row {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-2);
        border-radius: var(--radius-xs);
        cursor: pointer;
        font-size: var(--font-size-sm);
        margin: 0;
    }
    .facility-checkbox-row:hover { background: var(--bg-hover, #f8f9fa); }
    .facility-checkbox { width: 16px; height: 16px; cursor: pointer; }
</style>

<div class="form-content">

    <div class="greeting-section">
        <h1 class="greeting-title">Edit Peminjaman</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-calendar3 me-1"></i> Ubah detail peminjaman ruangan</span>
        </div>
    </div>

    <div class="form-card">
        <div class="card-header">
            <div class="title">
                <i class="bi bi-pencil-square"></i> Form Peminjaman
            </div>
            <div>
                @if($booking->status == 0)
                    <span class="badge-status pending"><i class="bi bi-clock"></i> Pending</span>
                @elseif($booking->status == 1)
                    <span class="badge-status approved"><i class="bi bi-check-circle"></i> Disetujui</span>
                @else
                    <span class="badge-status rejected"><i class="bi bi-x-circle"></i> Ditolak</span>
                @endif
            </div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger" style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.10); color:#dc2626; padding:var(--space-3) var(--space-4); border-radius:var(--radius-sm); margin-bottom:var(--space-4);">
                    <ul style="margin:0; padding-left:var(--space-4);">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bookings.update', $booking->id) }}" method="POST" id="bookingForm">
                @csrf @method('PUT')

                <div class="form-grid">

                    <!-- LEFT COLUMN -->
                    <div class="form-left">

                        <div class="form-group">
                            <label for="room_id" class="form-label">
                                <i class="bi bi-door-open me-1"></i> Ruangan <span class="text-danger">*</span>
                            </label>

                            <select name="room_id" id="room_id" class="d-none @error('room_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ (old('room_id', $booking->room_id) == $room->id) ? 'selected' : '' }}
                                            data-capacity="{{ $room->capacity }}">
                                        {{ $room->name }} (Kapasitas: {{ $room->capacity }} org)
                                    </option>
                                @endforeach
                            </select>

                            <div class="room-select-wrapper">
                                <button type="button" id="roomSelectTrigger" class="room-select-trigger @error('room_id') is-invalid @enderror" onclick="toggleRoomPanel()">
                                    <span id="roomSelectThumb" class="room-select-thumb" style="display:none;"><img id="roomSelectImg" src="" alt=""></span>
                                    <span class="room-select-text">
                                        <span id="roomSelectName" class="room-select-name">-- Pilih Ruangan --</span>
                                        <span id="roomSelectCapacity" class="room-select-capacity" style="display:none;"></span>
                                    </span>
                                    <i class="bi bi-chevron-down room-select-chevron"></i>
                                </button>

                                <div id="roomSelectPanel" class="room-select-panel">
                                    @foreach($rooms as $room)
                                        <div class="room-select-option" onclick="pickRoom({{ $room->id }}, '{{ addslashes($room->name) }}', {{ $room->capacity }})">
                                            <span class="room-select-thumb">
                                                @if($room->photos->count())
                                                    <img src="{{ $room->photos->first()->photo_url }}" alt="{{ $room->name }}">
                                                @else
                                                    <i class="bi bi-door-open"></i>
                                                @endif
                                            </span>
                                            <span class="room-select-text">
                                                <span class="room-select-name">{{ $room->name }}</span>
                                                <span class="room-select-capacity">Kapasitas {{ $room->capacity }} orang</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div id="roomCapacityHint" class="form-text text-muted" style="font-size:var(--font-size-xs); margin-top:var(--space-1); display:none;">
                                <i class="bi bi-people"></i> Kapasitas: <span id="capacityDisplay">0</span> orang
                            </div>
                            @error('room_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var preId = document.getElementById('room_id').value;
                                var roomsMap = @json($rooms->keyBy('id'));
                                if (preId && roomsMap[preId]) {
                                    var r = roomsMap[preId];
                                    document.getElementById('roomSelectName').textContent = r.name;
                                    document.getElementById('roomSelectCapacity').textContent = 'Kapasitas ' + r.capacity + ' orang';
                                    document.getElementById('roomSelectCapacity').style.display = '';
                                    if (r.photos && r.photos.length) {
                                        document.getElementById('roomSelectImg').src = r.photos[0].photo_url;
                                        document.getElementById('roomSelectThumb').style.display = '';
                                    }
                                }
                            });
                        </script>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-calendar3 me-1"></i> Tanggal & Waktu <span class="text-danger">*</span>
                            </label>
                            <div class="datetime-wrapper">
                                <div class="date-input">
                                    <i class="bi bi-calendar input-icon"></i>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_date', \Carbon\Carbon::parse($booking->start_time)->format('Y-m-d')) }}"
                                           min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                           required>
                                </div>
                                <div class="time-input">
                                    <i class="bi bi-clock input-icon"></i>
                                    <input type="text" name="start_time" id="start_time"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_time', \Carbon\Carbon::parse($booking->start_time)->format('H:i')) }}"
                                           placeholder="--:--" readonly style="cursor:pointer;background:var(--bg-input);"
                                           onclick="openTimePicker('start_time')"
                                           required>
                                </div>
                                <span class="time-separator">sampai</span>
                                <div class="time-input">
                                    <i class="bi bi-clock input-icon"></i>
                                    <input type="text" name="end_time" id="end_time"
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           value="{{ old('end_time', \Carbon\Carbon::parse($booking->end_time)->format('H:i')) }}"
                                           placeholder="--:--" readonly style="cursor:pointer;background:var(--bg-input);"
                                           onclick="openTimePicker('end_time')"
                                           required>
                                </div>
                            </div>
                            <small class="form-text text-muted" style="font-size:var(--font-size-xs);">
                                <i class="bi bi-info-circle me-1"></i> Jam kerja 07:00 – 16:00 WIB
                            </small>

                            <div id="timePickerOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:1000;align-items:center;justify-content:center;">
                                <div style="background:#fff;border-radius:16px;padding:1.5rem;width:280px;box-shadow:0 10px 40px rgba(0,0,0,0.2);">
                                    <p style="font-size:14px;color:#6b7280;margin:0 0 1rem;">Masukkan waktu</p>
                                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:1.25rem;">
                                        <div style="text-align:center;">
                                            <input id="tpHour" type="text" maxlength="2" inputmode="numeric"
                                                   class="tp-box"
                                                   style="width:72px;height:64px;font-size:32px;text-align:center;border-radius:10px;background:#f3f4f6;color:#111827;border:2px solid transparent;font-weight:600;">
                                            <p style="font-size:12px;color:#9ca3af;margin:6px 0 0;">Jam</p>
                                        </div>
                                        <span style="font-size:32px;color:#9ca3af;padding-bottom:20px;">:</span>
                                        <div style="text-align:center;">
                                            <input id="tpMinute" type="text" maxlength="2" inputmode="numeric"
                                                   class="tp-box"
                                                   style="width:72px;height:64px;font-size:32px;text-align:center;border-radius:10px;background:#f3f4f6;color:#111827;border:2px solid transparent;font-weight:600;">
                                            <p style="font-size:12px;color:#9ca3af;margin:6px 0 0;">Menit</p>
                                        </div>
                                    </div>
                                    <p id="tpError" style="font-size:12px;color:#dc2626;text-align:center;margin:0 0 0.75rem;display:none;"></p>
                                    <div style="display:flex;justify-content:flex-end;gap:8px;">
                                        <button type="button" onclick="closeTimePicker()" style="height:38px;padding:0 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;font-size:14px;cursor:pointer;">Batal</button>
                                        <button type="button" onclick="confirmTimePicker()" style="height:38px;padding:0 16px;border-radius:8px;border:none;background:#f97316;color:#fff;font-size:14px;font-weight:500;cursor:pointer;">OK</button>
                                    </div>
                                </div>
                            </div>
                            <style>
                                .tp-box:focus { background:#fff7ed !important; color:#c2410c !important; border-color:#f97316 !important; outline:none; }
                            </style>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div id="availabilityStatus" class="availability-status">
                                <span class="spinner" id="availabilitySpinner"></span>
                                <span id="availabilityMessage">Mengecek ketersediaan...</span>
                            </div>
                        </div>

                        <div id="recommendedSlotsContainer" style="display:none; margin-top:var(--space-2);">
                            <label class="form-label" style="font-size:var(--font-size-xs); color:var(--text-muted);">
                                <i class="bi bi-clock-history me-1"></i> Slot Tersedia Lainnya
                            </label>
                            <div class="recommended-slots" id="recommendedSlots"></div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="form-right">

                        <div class="form-group">
                            <label for="purpose" class="form-label">
                                <i class="bi bi-clipboard me-1"></i> Tujuan Peminjaman <span class="text-danger">*</span>
                            </label>
                            <textarea name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror"
                                      rows="4" placeholder="Jelaskan tujuan peminjaman ruangan..." required>{{ old('purpose', $booking->purpose) }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size:var(--font-size-xs);">
                                Minimal 10 karakter, maksimal 500 karakter
                            </small>
                            <div id="purposeCounter" style="font-size:var(--font-size-xs); color:var(--text-muted); margin-top:4px; text-align:right;">
                                <span id="purposeCount">0</span> / 500
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="facilities" class="form-label">
                                <i class="bi bi-tools me-1"></i> Fasilitas Tambahan <span class="text-muted">(Opsional)</span>
                            </label>
                            <select name="facilities[]" id="facilities" class="d-none @error('facilities') is-invalid @enderror" multiple="multiple">
                                @foreach($facilities as $facility)
                                    @php
                                        $selected = false;
                                        if (old('facilities')) {
                                            $selected = in_array($facility->id, old('facilities'));
                                        } else {
                                            $selected = $bookingFacilities->contains('facility_id', $facility->id);
                                        }
                                    @endphp
                                    <option value="{{ $facility->id }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $facility->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="facility-select-wrapper">
                                <button type="button" id="facilitySelectTrigger" class="room-select-trigger" onclick="toggleFacilityPanel()">
                                    <span class="room-select-text">
                                        <span id="facilitySelectLabel" class="room-select-name">Pilih fasilitas tambahan</span>
                                    </span>
                                    <i class="bi bi-chevron-down room-select-chevron"></i>
                                </button>

                                <div id="facilitySelectPanel" class="facility-select-panel">
                                    @foreach($facilities as $facility)
                                        @php
                                            $selected = false;
                                            if (old('facilities')) {
                                                $selected = in_array($facility->id, old('facilities'));
                                            } else {
                                                $selected = $bookingFacilities->contains('facility_id', $facility->id);
                                            }
                                        @endphp
                                        <label class="facility-checkbox-row">
                                            <input type="checkbox" class="facility-checkbox" value="{{ $facility->id }}"
                                                   {{ $selected ? 'checked' : '' }}
                                                   onchange="toggleFacility({{ $facility->id }}, this.checked)">
                                            <span>{{ $facility->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <small class="form-text text-muted" style="font-size:var(--font-size-xs);">
                                <i class="bi bi-info-circle me-1"></i> Pilih satu atau lebih fasilitas tambahan yang diperlukan
                            </small>
                            @error('facilities')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="facilitiesQuantities" class="form-group {{ $bookingFacilities->isEmpty() ? 'd-none' : '' }}">
                            <label class="form-label" style="font-size:var(--font-size-xs); color:var(--text-secondary);">
                                <i class="bi bi-hashtag me-1"></i> Jumlah Fasilitas
                            </label>
                            <div id="quantityInputs"></div>
                        </div>

                    </div>

                </div>

                <div style="margin-top: var(--space-5); border-top: 1px solid var(--border-color-light); padding-top: var(--space-4);">
                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                        <a href="{{ route('bookings.index') }}" class="btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn-submit" id="submitBtn" disabled>
                            <i class="bi bi-save"></i> Update Peminjaman
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {

        // ============================================================
        // CUSTOM TIME PICKER (bebas, tidak harus kelipatan 30 menit)
        // ============================================================
        let tpTargetField = null;

        window.openTimePicker = function(fieldId) {
            tpTargetField = fieldId;
            const current = $('#' + fieldId).val() || '07:00';
            const parts = current.split(':');
            $('#tpHour').val(parts[0] || '07');
            $('#tpMinute').val(parts[1] || '00');
            $('#tpError').hide();
            $('#timePickerOverlay').css('display', 'flex');
            setTimeout(() => $('#tpHour').select(), 50);
        };

        window.closeTimePicker = function() {
            $('#timePickerOverlay').hide();
            tpTargetField = null;
        };

        window.confirmTimePicker = function() {
            let h = parseInt($('#tpHour').val(), 10);
            let m = parseInt($('#tpMinute').val(), 10);

            if (isNaN(h) || isNaN(m) || h < 0 || h > 23 || m < 0 || m > 59) {
                $('#tpError').text('Masukkan jam (00-23) dan menit (00-59) yang valid').show();
                return;
            }

            const hStr = String(h).padStart(2, '0');
            const mStr = String(m).padStart(2, '0');
            const value = hStr + ':' + mStr;

            $('#' + tpTargetField).val(value).trigger('change');
            $('#timePickerOverlay').hide();
            tpTargetField = null;
        };

        $('#tpHour, #tpMinute').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);
        });

        $('#tpHour').on('input', function() {
            if (this.value.length === 2) {
                $('#tpMinute').select();
            }
        });

        $('#timePickerOverlay').on('click', function(e) {
            if (e.target.id === 'timePickerOverlay') {
                closeTimePicker();
            }
        });

        // ============================================================
        // CUSTOM ROOM SELECT
        // ============================================================
        window.toggleRoomPanel = function() {
            $('#roomSelectPanel').toggleClass('open');
            $('#facilitySelectPanel').removeClass('open');
        };

        window.pickRoom = function(id, name, capacity) {
            $('#room_id').val(id).trigger('change');
            $('#roomSelectName').text(name);
            $('#roomSelectCapacity').text('Kapasitas ' + capacity + ' orang').show();

            const roomsData = @json($rooms->keyBy('id'));
            const r = roomsData[id];
            if (r && r.photos && r.photos.length) {
                $('#roomSelectImg').attr('src', r.photos[0].photo_url);
                $('#roomSelectThumb').show();
            } else {
                $('#roomSelectThumb').hide();
            }
            $('#roomSelectPanel').removeClass('open');
        };

        window.toggleFacilityPanel = function() {
            $('#facilitySelectPanel').toggleClass('open');
            $('#roomSelectPanel').removeClass('open');
        };

        window.toggleFacility = function(id, checked) {
            const opt = $('#facilities option[value="' + id + '"]');
            opt.prop('selected', checked);
            $('#facilities').trigger('change');

            const selectedNames = [];
            $('#facilities option:selected').each(function() {
                selectedNames.push($(this).text().trim());
            });
            $('#facilitySelectLabel').text(selectedNames.length ? selectedNames.join(', ') : 'Pilih fasilitas tambahan');
        };

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.room-select-wrapper').length) {
                $('#roomSelectPanel').removeClass('open');
            }
            if (!$(e.target).closest('.facility-select-wrapper').length) {
                $('#facilitySelectPanel').removeClass('open');
            }
        });

        const preselectedFacilityNames = [];
        $('.facility-checkbox:checked').each(function() {
            preselectedFacilityNames.push($(this).next('span').text().trim());
        });
        if (preselectedFacilityNames.length) {
            $('#facilitySelectLabel').text(preselectedFacilityNames.join(', '));
        }

        const facilitiesData = @json($facilities->keyBy('id'));
        const existingQuantities = @json($bookingFacilities->pluck('quantity', 'facility_id'));

        $('#facilities').on('change', function() {
            const selected = $(this).val();
            const container = $('#facilitiesQuantities');
            const qContainer = $('#quantityInputs');

            if (selected && selected.length > 0) {
                container.removeClass('d-none');
                let html = '';
                selected.forEach(function(id, idx) {
                    const fac = facilitiesData[id];
                    let qty = 1;
                    const oldQ = @json(old('quantities', []));
                    if (oldQ[idx]) qty = oldQ[idx];
                    else if (existingQuantities[id]) qty = existingQuantities[id];
                    html += `
                        <div class="facility-quantity">
                            <label>${fac.name}</label>
                            <input type="number" name="quantities[]" value="${qty}" min="1" max="100">
                        </div>
                    `;
                });
                qContainer.html(html);
            } else {
                container.addClass('d-none');
                qContainer.html('');
            }
        });

        @if(old('facilities') || $bookingFacilities->count() > 0)
            $('#facilities').trigger('change');
        @endif

        // Capacity hint
        $('#room_id').on('change', function() {
            const selected = $(this).find('option:selected');
            const capacity = selected.data('capacity');
            const hint = $('#roomCapacityHint');
            if (capacity) {
                hint.show();
                $('#capacityDisplay').text(capacity);
            } else {
                hint.hide();
            }
            checkAvailability();
        });
        $('#room_id').trigger('change');

        // Purpose counter
        $('#purpose').on('input', function() {
            const count = $(this).val().length;
            $('#purposeCount').text(count);
            if (count > 500) $('#purposeCount').css('color', '#ef4444');
            else $('#purposeCount').css('color', 'var(--text-muted)');
        });
        $('#purpose').trigger('input');

        // Availability check
        let checkTimeout;

        function checkAvailability() {
            const roomId = $('#room_id').val();
            const date = $('#start_date').val();
            const start = $('#start_time').val();
            const end = $('#end_time').val();

            const status = $('#availabilityStatus');
            const message = $('#availabilityMessage');
            const spinner = $('#availabilitySpinner');

            status.removeClass('visible available unavailable loading');

            if (!roomId || !date || !start || !end) {
                $('#submitBtn').prop('disabled', true);
                return;
            }

            const startDateTime = date + 'T' + start + ':00';
            const endDateTime = date + 'T' + end + ':00';

            if (new Date(endDateTime) <= new Date(startDateTime)) {
                status.addClass('visible unavailable');
                message.html('<i class="bi bi-exclamation-triangle"></i> Waktu selesai harus setelah waktu mulai');
                $('#submitBtn').prop('disabled', true);
                return;
            }

            const startHour = parseInt(start.split(':')[0]);
            const endHour = parseInt(end.split(':')[0]);
            if (startHour < 7 || endHour > 16 || startHour > 16 || endHour < 7) {
                status.addClass('visible unavailable');
                message.html('<i class="bi bi-exclamation-triangle"></i> Peminjaman hanya dapat dilakukan pada jam kerja 07:00 – 16:00 WIB');
                $('#submitBtn').prop('disabled', true);
                return;
            }

            status.addClass('visible loading');
            spinner.show();
            message.html('Mengecek ketersediaan...');
            $('#submitBtn').prop('disabled', true);

            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('bookings.check-availability') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        room_id: roomId,
                        start_time: startDateTime,
                        end_time: endDateTime,
                        exclude_booking_id: {{ $booking->id }}
                    },
                    success: function(res) {
                        status.removeClass('loading');
                        spinner.hide();
                        if (res.available) {
                            status.addClass('visible available');
                            message.html('<i class="bi bi-check-circle"></i> ' + res.message);
                            $('#submitBtn').prop('disabled', false);
                            showRecommendedSlots(roomId, date);
                        } else {
                            status.addClass('visible unavailable');
                            message.html('<i class="bi bi-exclamation-triangle"></i> ' + res.message);
                            $('#submitBtn').prop('disabled', true);
                            $('#recommendedSlotsContainer').hide();
                        }
                    },
                    error: function(xhr) {
                        status.removeClass('loading');
                        spinner.hide();
                        status.addClass('visible unavailable');
                        let errorMsg = 'Gagal mengecek ketersediaan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        message.html('<i class="bi bi-exclamation-triangle"></i> ' + errorMsg);
                        $('#submitBtn').prop('disabled', true);
                    }
                });
            }, 400);
        }

        function showRecommendedSlots(roomId, date) {
            const container = $('#recommendedSlotsContainer');
            const slotsContainer = $('#recommendedSlots');

            $.ajax({
                url: "{{ route('bookings.check-availability') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    room_id: roomId,
                    date_only: date,
                    get_slots: true
                },
                success: function(res) {
                    if (res.available_slots && res.available_slots.length > 0) {
                        container.show();
                        slotsContainer.html('');
                        res.available_slots.slice(0, 6).forEach(function(slot) {
                            const isSelected = slot === $('#start_time').val();
                            slotsContainer.append(`
                                <button type="button" class="slot-btn ${isSelected ? 'selected' : ''}"
                                        onclick="selectSlot('${slot}')"
                                        style="${isSelected ? 'background:rgba(16,185,129,0.12); border-color:var(--brand-green); color:var(--brand-green-dark);' : ''}">
                                    ${isSelected ? '<i class="bi bi-check-circle"></i>' : ''} ${slot}
                                </button>
                            `);
                        });
                    } else {
                        container.hide();
                    }
                },
                error: function() {
                    container.hide();
                }
            });
        }

        window.selectSlot = function(time) {
            $('#start_time').val(time);
            const parts = time.split(':');
            let hour = parseInt(parts[0]) + 1;
            const min = parts[1];
            if (hour >= 24) hour = 23;
            const endHour = String(hour).padStart(2, '0');
            let endMin = min;
            if (parseInt(min) < 30) {
                endMin = '30';
            } else {
                endMin = '00';
                hour = parseInt(hour) + 1;
                const endHour2 = String(hour).padStart(2, '0');
                $('#end_time').val(endHour2 + ':' + endMin);
                checkAvailability();
                return;
            }
            $('#end_time').val(endHour + ':' + endMin);
            checkAvailability();
        };

        $('#room_id, #start_date, #start_time, #end_time').on('change', checkAvailability);

        $('#start_time, #end_time').on('change', function() {
            const start = $('#start_time').val();
            const end = $('#end_time').val();
            if (start && end) {
                const startMinutes = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
                const endMinutes = parseInt(end.split(':')[0]) * 60 + parseInt(end.split(':')[1]);
                if (endMinutes <= startMinutes) {
                    $('#end_time')[0].setCustomValidity('Waktu selesai harus setelah waktu mulai');
                } else {
                    $('#end_time')[0].setCustomValidity('');
                }
            }
        });

        $('#bookingForm').on('submit', function(e) {
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();
            if (!startDate || !startTime || !endTime) {
                e.preventDefault();
                alert('Silakan isi semua field waktu');
                return false;
            }
            const startDateTime = new Date(startDate + 'T' + startTime + ':00');
            const endDateTime = new Date(startDate + 'T' + endTime + ':00');
            if (endDateTime <= startDateTime) {
                e.preventDefault();
                alert('Waktu selesai harus setelah waktu mulai');
                return false;
            }
        });

        setTimeout(checkAvailability, 500);

    });
</script>
@endsection