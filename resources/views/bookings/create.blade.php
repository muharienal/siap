@extends('templates.template')

@section('page_title', 'Ajukan Peminjaman')
@section('page_subtitle', 'Isi formulir untuk mengajukan peminjaman ruangan')

@section('content')
<style>
    .form-content {
        padding: var(--space-6) var(--space-7);
        max-width: 1800px; 
        margin: 0 auto;
        width: 100%;
        padding-bottom: 100px;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-5);
        flex-wrap: wrap;
        gap: var(--space-3);
    }
    .form-header h1 { font-size: var(--font-size-2xl); font-weight: 800; color: var(--text-primary); margin: 0; }

    .btn-back {
        height: 42px; padding: 0 var(--space-4);
        background: var(--bg-card); border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); color: var(--text-secondary);
        font-weight: 600; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s;
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange); }

    .form-card {
        background: var(--border-color-light);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr; 
        gap: 1px; 
    }

    .form-section { background: var(--bg-card); padding: var(--space-5); }
    .full-width { grid-column: 1 / -1; }

    .section-title {
        font-size: var(--font-size-md); font-weight: 700;
        color: var(--text-primary); margin-bottom: var(--space-4);
        display: flex; align-items: center; gap: 8px;
        padding-bottom: var(--space-3);
        border-bottom: 1px solid var(--border-color-light);
    }
    .section-title i { color: var(--brand-orange); }

    .form-group { margin-bottom: var(--space-4); }
    .form-group:last-child { margin-bottom: 0; }

    .form-label {
        font-weight: 700; font-size: var(--font-size-sm);
        color: var(--text-primary); margin-bottom: var(--space-2);
        display: block;
    }
    .form-label .required { color: #ef4444; }

    .form-control, .form-select {
        height: 48px; border-radius: var(--radius-sm);
        border: 1px solid var(--border-color); font-size: var(--font-size-sm);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--brand-orange);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }

    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: var(--space-3);
    }
    .room-option {
        position: relative; border: 2px solid var(--border-color);
        border-radius: var(--radius-sm); padding: var(--space-3);
        cursor: pointer; transition: all 0.2s;
    }
    .room-option:hover { border-color: var(--brand-orange); background: rgba(249,115,22,0.02); }
    .room-option.selected { border-color: var(--brand-orange); background: rgba(249,115,22,0.05); }
    .room-option input[type="radio"] { position: absolute; opacity: 0; }
    .room-option .room-check {
        position: absolute; top: 8px; right: 8px;
        width: 20px; height: 20px; border-radius: 50%;
        background: var(--brand-orange); color: white;
        display: none; align-items: center; justify-content: center; font-size: 12px;
    }
    .room-option.selected .room-check { display: flex; }
    .room-option .room-img { width: 100%; height: 90px; object-fit: cover; border-radius: var(--radius-xs); margin-bottom: 8px; }
    .room-option .room-placeholder { width: 100%; height: 90px; background: var(--bg-body); border-radius: var(--radius-xs); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 2rem; margin-bottom: 8px; }
    .room-option .room-name { font-weight: 700; font-size: var(--font-size-sm); }
    .room-option .room-cap { font-size: 11px; color: var(--text-muted); }

    .room-detail-btn {
        position: absolute; top: 8px; left: 8px;
        display: inline-flex; align-items: center; gap: 4px;
        background: rgba(15,23,42,0.55);
        color: #fff; border: none;
        font-size: 10.5px; font-weight: 600;
        padding: 4px 8px; border-radius: 999px;
        cursor: pointer; z-index: 2;
        backdrop-filter: blur(2px);
        transition: background 0.2s;
    }
    .room-detail-btn:hover { background: var(--brand-orange); }

    /* Modal detail ruangan */
    .rd-overlay {
        display: none; position: fixed; inset: 0; z-index: 1055;
        background: rgba(15,23,42,0.55);
        align-items: center; justify-content: center;
        padding: var(--space-4);
    }
    .rd-overlay.open { display: flex; }
    .rd-modal {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        width: 100%; max-width: 560px;
        max-height: 88vh; overflow-y: auto;
        position: relative;
    }
    .rd-close {
        position: absolute; top: 12px; right: 12px;
        width: 32px; height: 32px; border-radius: 50%;
        border: none; background: rgba(15,23,42,0.06);
        color: var(--text-secondary); font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; z-index: 2;
    }
    .rd-close:hover { background: rgba(15,23,42,0.12); }
    .rd-photo-main {
        width: 100%; height: 220px; object-fit: cover;
        border-radius: var(--radius-card) var(--radius-card) 0 0;
        background: var(--bg-body);
    }
    .rd-photo-placeholder {
        width: 100%; height: 220px; background: var(--bg-body);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); font-size: 2.5rem;
        border-radius: var(--radius-card) var(--radius-card) 0 0;
    }
    .rd-thumbs { display: flex; gap: 6px; padding: var(--space-2) var(--space-4) 0; overflow-x: auto; }
    .rd-thumbs img {
        width: 56px; height: 42px; object-fit: cover; border-radius: var(--radius-xs);
        cursor: pointer; border: 2px solid transparent; flex-shrink: 0;
        opacity: 0.7; transition: all 0.15s;
    }
    .rd-thumbs img:hover, .rd-thumbs img.active { opacity: 1; border-color: var(--brand-orange); }
    .rd-body { padding: var(--space-4) var(--space-5) var(--space-5); }
    .rd-body h3 { font-size: var(--font-size-lg); font-weight: 800; color: var(--text-primary); margin: 0 0 6px; }
    .rd-meta { display: flex; gap: var(--space-4); flex-wrap: wrap; margin-bottom: var(--space-4); }
    .rd-meta span { font-size: var(--font-size-sm); color: var(--text-secondary); display: flex; align-items: center; gap: 5px; }
    .rd-meta i { color: var(--brand-orange); }
    .rd-desc { font-size: var(--font-size-sm); color: var(--text-secondary); line-height: 1.6; margin-bottom: var(--space-4); }
    .rd-desc.empty { color: var(--text-muted); font-style: italic; }
    .rd-section-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--text-muted); margin-bottom: var(--space-2); }
    .rd-facility-list { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: var(--space-5); }
    .rd-facility-chip {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--bg-body); border: 1px solid var(--border-color-light);
        border-radius: 999px; padding: 5px 12px; font-size: 12.5px; color: var(--text-primary); font-weight: 600;
    }
    .rd-facility-chip i { color: var(--brand-orange); font-size: 11px; }
    .rd-facility-empty { font-size: var(--font-size-sm); color: var(--text-muted); font-style: italic; margin-bottom: var(--space-5); }
    .rd-pick-btn {
        width: 100%; height: 46px; border: none; border-radius: var(--radius-sm);
        background: var(--brand-orange); color: #fff; font-weight: 700; font-size: var(--font-size-sm);
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background 0.2s;
    }
    .rd-pick-btn:hover { background: var(--brand-orange-dark, #ea580c); }

    .availability-status {
        display: none; padding: var(--space-3); border-radius: var(--radius-sm);
        margin-top: var(--space-2); font-size: var(--font-size-sm); font-weight: 600;
        align-items: center; gap: 8px;
    }
    .availability-status.available { display: flex; background: rgba(16,185,129,0.1); color: #047857; }
    .availability-status.unavailable { display: flex; background: rgba(239,68,68,0.1); color: #b91c1c; }
    .availability-status.checking { display: flex; background: rgba(59,130,246,0.1); color: #1d4ed8; }

    .facility-list {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: var(--space-2); max-height: none; overflow: visible; border: none;
    }
    .facility-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: var(--space-3); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-sm); transition: background 0.15s;
    }
    .facility-item:hover { background: var(--bg-hover); }
    .facility-item.selected { background: rgba(249,115,22,0.03); border-color: var(--brand-orange); }
    .facility-info { display: flex; align-items: center; gap: var(--space-3); flex: 1; }
    .facility-check { width: 24px; height: 24px; cursor: pointer; }
    .facility-name { font-weight: 600; font-size: var(--font-size-sm); }
    .facility-desc { font-size: 11px; color: var(--text-muted); }
    .facility-qty { width: 70px; height: 36px; text-align: center; border: 1px solid var(--border-color); border-radius: var(--radius-xs); font-size: var(--font-size-sm); }
    .facility-qty:disabled { background: var(--bg-body); opacity: 0.5; }

    .form-actions { background: var(--bg-body); display: flex; gap: var(--space-3); justify-content: flex-end; padding: var(--space-5); }
    .btn-submit { height: 48px; padding: 0 var(--space-6); background: var(--brand-gradient); border: none; border-radius: var(--radius-sm); font-weight: 600; font-size: var(--font-size-sm); color: white; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,185,129,0.2); }
    .btn-cancel { height: 48px; padding: 0 var(--space-5); background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600; font-size: var(--font-size-sm); color: var(--text-secondary); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
    .btn-cancel:hover { border-color: var(--text-muted); }

    /* MATERIAL TIME PICKER STYLES */
    .time-trigger {
        height: 48px; padding: 0 var(--space-4);
        display: inline-flex; align-items: center; gap: var(--space-2);
        background: var(--bg-input); border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); font-size: var(--font-size-sm);
        font-weight: 600; color: var(--text-primary); cursor: pointer;
        white-space: nowrap; transition: all 0.2s; width: 100%;
    }
    .time-trigger:hover { border-color: var(--brand-orange); background: var(--bg-card); }
    .time-trigger i.bi-clock { color: var(--brand-orange); }
    .time-trigger .chevron { color: var(--text-muted); font-size: 0.65rem; margin-left: 2px; }

    .mtp-overlay { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.32); animation: mtpFadeIn 0.18s ease; padding: 16px; }
    .mtp-overlay.open { display: flex; }
    /* DIPERBAIKI: Lebar modal dikembalikan ke 400px agar proporsional */
    .mtp-dialog { background: var(--bg-card); border-radius: 16px; box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28); width: 400px; max-width: 100%; max-height: 92vh; overflow-y: auto; animation: mtpSlideUp 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes mtpFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes mtpSlideUp { from { opacity: 0; transform: translateY(24px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .mtp-header { padding: 20px 24px 6px; }
    .mtp-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-muted); }
    .mtp-subtitle { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
    .mtp-time-display { display: flex; justify-content: center; gap: 24px; padding: 12px 24px 8px; }
    .mtp-field { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; padding: 8px 14px; border-radius: 12px; transition: background 0.2s; }
    .mtp-field:hover { background: var(--bg-hover); }
    .mtp-field.active .mtp-digital { color: var(--brand-orange); }
    .mtp-field.active .mtp-field-label { color: var(--brand-orange-dark); }
    .mtp-field-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
    /* DIPERBAIKI: Font digital dikembalikan ke 42px agar tidak kebesaran */
    .mtp-digital { font-size: 42px; font-weight: 400; color: var(--text-primary); line-height: 1; display: flex; align-items: baseline; font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }
    .mtp-digital .mtp-colon { opacity: 0.45; padding: 0 2px; }
    .mtp-digital .mtp-unit { cursor: pointer; padding: 0 3px; border-radius: 8px; transition: background 0.2s; }
    .mtp-digital .mtp-unit:hover { background: rgba(249, 115, 22, 0.08); }
    .mtp-digital .mtp-unit.mode-active { background: rgba(249, 115, 22, 0.14); color: var(--brand-orange); }
    .mtp-error { display: none; align-items: center; gap: 6px; font-size: 12px; color: #b91c1c; background: rgba(239, 68, 68, 0.08); border-radius: var(--radius-xs); padding: 7px 12px; margin: 4px 24px 0; }
    .mtp-error.show { display: flex; }
    .mtp-clock-wrap { display: flex; flex-direction: column; align-items: center; padding: 16px 24px 8px; }
    /* DIPERBAIKI: Ukuran jam dikembalikan ke 280px */
    .mtp-clock { position: relative; width: 280px; height: 280px; max-width: 80vw; max-height: 80vw; border-radius: 50%; background: var(--bg-body); user-select: none; flex-shrink: 0; margin: 0 auto; }
    /* DIPERBAIKI: Ukuran angka dikembalikan ke proporsi semula */
    .mtp-num { position: absolute; width: clamp(40px, 12vw, 44px); height: clamp(40px, 12vw, 44px); display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: clamp(14px, 4vw, 16px); font-weight: 500; color: var(--text-primary); cursor: pointer; transform: translate(-50%, -50%); transition: background 0.2s, color 0.2s, transform 0.2s; background: transparent; z-index: 2; }
    .mtp-num:hover:not(.disabled) { background: rgba(249, 115, 22, 0.12); }
    .mtp-num:active:not(.disabled) { transform: translate(-50%, -50%) scale(0.95); }
    .mtp-num.selected { background: var(--brand-orange); color: #fff; font-weight: 700; transform: translate(-50%, -50%) scale(1.05); }
    .mtp-num.disabled { color: var(--text-muted); opacity: 0.3; cursor: not-allowed; pointer-events: none; }
    .mtp-hand-svg { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
    .mtp-hand { stroke: var(--brand-orange); stroke-width: 2; stroke-linecap: round; transition: x2 0.2s ease, y2 0.2s ease; vector-effect: non-scaling-stroke; }
    .mtp-center-dot { position: absolute; left: 50%; top: 50%; width: 12px; height: 12px; background: var(--brand-orange); border-radius: 50%; transform: translate(-50%, -50%); pointer-events: none; z-index: 3; }
    .mtp-mode-hint { font-size: 12px; font-weight: 600; color: var(--brand-orange-dark); margin-top: 12px; height: 20px; text-align: center; background: rgba(249, 115, 22, 0.08); padding: 4px 12px; border-radius: 20px; }
    .mtp-footer { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px 16px; }
    .mtp-icon-btn { background: none; border: none; color: var(--text-muted); font-size: 18px; cursor: pointer; padding: 8px; border-radius: 8px; transition: all 0.2s; }
    .mtp-icon-btn:hover { background: var(--bg-hover); color: var(--brand-orange); }
    .mtp-actions { display: flex; gap: 8px; }
    .mtp-btn { height: 36px; padding: 0 18px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
    .mtp-btn-cancel { background: transparent; color: var(--text-secondary); }
    .mtp-btn-cancel:hover { background: var(--bg-hover); }
    .mtp-btn-ok { background: var(--brand-orange); color: #fff; }
    .mtp-btn-ok:hover { background: var(--brand-orange-dark); }

    @media (max-width: 991.98px) {
        .form-content { padding: var(--space-4); }
        .form-card { grid-template-columns: 1fr; }
        .facility-list { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column-reverse; }
        .form-actions button { width: 100%; justify-content: center; }
    }
    @media (max-width: 575.98px) {
        .mtp-dialog { width: 95vw; }
        .mtp-header { padding: 16px 20px 4px; }
        .mtp-time-display { gap: 16px; padding: 8px 20px 4px; }
        .mtp-digital { font-size: 36px; }
        .mtp-field { padding: 8px 12px; }
        .mtp-clock { width: 240px; height: 240px; }
        .mtp-footer { padding: 12px 20px 20px; }
    }
</style>

<div class="form-content">
    <div class="form-header">
        <div>
            <h1>Ajukan Peminjaman</h1>
            <p class="text-muted mb-0 mt-1">Lengkapi formulir di bawah untuk mengajukan peminjaman ruangan</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
        @csrf

        <div class="form-card">

            <div class="form-section full-width">
                <div class="section-title">
                    <i class="bi bi-door-open"></i> Pilih Ruangan
                </div>
                <div class="room-grid">
                    @foreach($rooms as $room)
                        @php
                            $photos = $room->photos ?? collect();
                            $hasPhoto = $photos->count() > 0;
                            $firstPhoto = $hasPhoto ? $photos->first()->photo_url : null;
                            $isSelected = old('room_id') == $room->id || ($prefill['room_id'] ?? '') == $room->id;
                        @endphp
                        <label class="room-option {{ $isSelected ? 'selected' : '' }}" onclick="selectRoom(this)">
                            <input type="radio" name="room_id" value="{{ $room->id }}" {{ $isSelected ? 'checked' : '' }} required onchange="checkAvailability()">
                            <button type="button" class="room-detail-btn"
                                onclick="event.preventDefault(); event.stopPropagation(); openRoomDetail(this)"
                                data-room-id="{{ $room->id }}"
                                data-name="{{ $room->name }}"
                                data-location="{{ $room->location }}"
                                data-capacity="{{ $room->capacity }}"
                                data-description="{{ $room->description ?? '' }}"
                                data-photos='@json($photos->pluck("photo_url")->values())'
                                data-facilities='@json($room->facilities->map(fn($f) => ["name" => $f->name, "qty" => $f->pivot->quantity])->values())'>
                                <i class="bi bi-info-circle"></i> Detail
                            </button>
                            <div class="room-check"><i class="bi bi-check"></i></div>
                            @if($hasPhoto)
                                <img src="{{ $firstPhoto }}" class="room-img" alt="{{ $room->name }}" loading="lazy">
                            @else
                                <div class="room-placeholder"><i class="bi bi-building"></i></div>
                            @endif
                            <div class="room-name">{{ $room->name }}</div>
                            <div class="room-cap"><i class="bi bi-people"></i> Kapasitas {{ $room->capacity }} orang</div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-clock"></i> Waktu Peminjaman
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $prefill['date'] ?? date('Y-m-d')) }}" required onchange="checkAvailability()">
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Mulai & Selesai <span class="required">*</span></label>
                    <button type="button" class="time-trigger" onclick="openBookingTimePicker()">
                        <i class="bi bi-clock"></i>
                        <span id="bookingTimeLabel">{{ old('start_time', $prefill['start_time'] ?? '07:00') }} – {{ old('end_time', $prefill['end_time'] ?? '08:00') }}</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>
                    <input type="hidden" name="start_time" id="bookingStartTime" value="{{ old('start_time', $prefill['start_time'] ?? '07:00') }}">
                    <input type="hidden" name="end_time" id="bookingEndTime" value="{{ old('end_time', $prefill['end_time'] ?? '08:00') }}">
                </div>

                <div class="availability-status" id="availabilityStatus">
                    <i class="bi bi-info-circle"></i>
                    <span id="availabilityMessage">Pilih ruangan dan waktu untuk cek ketersediaan</span>
                </div>

                <small class="text-muted d-block mt-2">
                    <i class="bi bi-info-circle"></i> Jam operasional: 07:00 - 16:00 WIB, interval 30 menit, Senin-Jumat
                </small>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <i class="bi bi-card-text"></i> Detail Peminjaman
                </div>

                <div class="form-group">
                    <label class="form-label">Keperluan / Agenda <span class="required">*</span></label>
                    <textarea name="purpose" class="form-control" rows="6" placeholder="Jelaskan agenda rapat atau keperluan peminjaman..." required>{{ old('purpose') }}</textarea>
                    <small class="text-muted">Minimal 10 karakter</small>
                </div>

                @if(Auth::user()->role == 1)
                    <div class="form-group">
                        <label class="form-label">Tipe Booking</label>
                        <select name="booking_type" class="form-select">
                            <option value="Regular" {{ old('booking_type') == 'Regular' ? 'selected' : '' }}>Regular</option>
                            <option value="Priority" {{ old('booking_type', 'Priority') == 'Priority' ? 'selected' : '' }}>Priority</option>
                        </select>
                        <small class="text-muted">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Priority otomatis menolak booking Regular lain di ruangan & jam yang sama — <strong>termasuk yang sudah disetujui admin</strong>.
                        </small>
                    </div>
                @endif
            </div>

            <div class="form-section full-width">
                <div class="section-title">
                    <i class="bi bi-box-seam"></i> Fasilitas Tambahan <small class="text-muted fw-normal">(Opsional)</small>
                </div>

                @if($facilities->count() > 0)
                    <div class="facility-list">
                        @foreach($facilities as $facility)
                            @php
                                $isChecked = is_array(old('facilities')) && in_array($facility->id, old('facilities'));
                                $qty = old('quantities.' . $facility->id, 1);
                            @endphp
                            <div class="facility-item {{ $isChecked ? 'selected' : '' }}">
                                <div class="facility-info">
                                    <input type="checkbox" name="facilities[]" value="{{ $facility->id }}" class="facility-check form-check-input" {{ $isChecked ? 'checked' : '' }} onchange="toggleFacility(this)">
                                    <div>
                                        <div class="facility-name">{{ $facility->name }}</div>
                                        <div class="facility-desc">{{ $facility->storage_location }}</div>
                                    </div>
                                </div>
                                <input type="number" name="quantities[]" class="facility-qty form-control" placeholder="Qty" min="1" value="{{ $qty }}" {{ !$isChecked ? 'disabled' : '' }}>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-4">Tidak ada fasilitas tersedia</p>
                @endif
            </div>

            <div class="form-actions full-width">
                <a href="{{ route('bookings.index') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-send"></i> Ajukan Peminjaman
                </button>
            </div>

        </div>
    </form>
</div>

{{-- MATERIAL TIME PICKER MODAL --}}
<div class="rd-overlay" id="rdOverlay" onclick="if(event.target===this)closeRoomDetail()">
    <div class="rd-modal">
        <button type="button" class="rd-close" onclick="closeRoomDetail()"><i class="bi bi-x-lg"></i></button>

        <img id="rdPhotoMain" class="rd-photo-main" src="" alt="" style="display:none;">
        <div id="rdPhotoPlaceholder" class="rd-photo-placeholder"><i class="bi bi-building"></i></div>
        <div id="rdThumbs" class="rd-thumbs"></div>

        <div class="rd-body">
            <h3 id="rdName"></h3>
            <div class="rd-meta">
                <span><i class="bi bi-geo-alt"></i> <span id="rdLocation"></span></span>
                <span><i class="bi bi-people"></i> Kapasitas <span id="rdCapacity"></span> orang</span>
            </div>

            <div class="rd-section-label">Deskripsi</div>
            <p id="rdDescription" class="rd-desc"></p>

            <div class="rd-section-label">Fasilitas Ruangan</div>
            <div id="rdFacilities" class="rd-facility-list"></div>

            <button type="button" class="rd-pick-btn" id="rdPickBtn">
                <i class="bi bi-check-circle"></i> Pilih Ruangan Ini
            </button>
        </div>
    </div>
</div>

<div class="mtp-overlay" id="mtpOverlay" onclick="if(event.target===this)closeTimePicker(true)">
    <div class="mtp-dialog" role="dialog" aria-modal="true" aria-labelledby="mtpTitle">
        <div class="mtp-header">
            <div class="mtp-title" id="mtpTitle">Pilih Jam Operasional</div>
            <div class="mtp-subtitle">Rentang 07:00–16:00, interval 30 menit</div>
        </div>
        <div class="mtp-time-display">
            <div class="mtp-field active" data-field="start" onclick="setActiveField('start')">
                <div class="mtp-field-label">Mulai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="mtpStartHour" onclick="event.stopPropagation();selectUnit('start', 'hour')">07</span><span class="mtp-colon">:</span><span class="mtp-unit" id="mtpStartMin" onclick="event.stopPropagation();selectUnit('start', 'minute')">00</span>
                </div>
            </div>
            <div class="mtp-field" data-field="end" onclick="setActiveField('end')">
                <div class="mtp-field-label">Selesai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="mtpEndHour" onclick="event.stopPropagation();selectUnit('end', 'hour')">08</span><span class="mtp-colon">:</span><span class="mtp-unit" id="mtpEndMin" onclick="event.stopPropagation();selectUnit('end', 'minute')">00</span>
                </div>
            </div>
        </div>
        <div class="mtp-error" id="mtpError"><i class="bi bi-exclamation-triangle"></i> <span></span></div>
        <div class="mtp-clock-wrap">
            <div class="mtp-clock" id="mtpClock">
                <svg class="mtp-hand-svg" viewBox="0 0 100 100" aria-hidden="true">
                    <line class="mtp-hand" id="mtpHand" x1="50" y1="50" x2="50" y2="15" vector-effect="non-scaling-stroke" />
                </svg>
                <div class="mtp-center-dot"></div>
            </div>
            <div class="mtp-mode-hint" id="mtpModeHint">Pilih jam mulai</div>
        </div>
        <div class="mtp-footer">
            <button type="button" class="mtp-icon-btn" onclick="toggleTimeMode()" title="Ganti mode jam/menit" aria-label="Ganti mode"><i class="bi bi-grid-3x3-gap"></i></button>
            <div class="mtp-actions">
                <button type="button" class="mtp-btn mtp-btn-cancel" onclick="closeTimePicker(true)">Batal</button>
                <button type="button" class="mtp-btn mtp-btn-ok" onclick="applyTimePicker()">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Facility toggle
    function toggleFacility(checkbox) {
        const item = checkbox.closest('.facility-item');
        const qtyInput = item.querySelector('.facility-qty');
        if (checkbox.checked) {
            item.classList.add('selected');
            qtyInput.disabled = false;
        } else {
            item.classList.remove('selected');
            qtyInput.disabled = true;
        }
    }

    // Availability check
    let availabilityTimer;
    function checkAvailability() {
        clearTimeout(availabilityTimer);

        const roomId = document.querySelector('input[name="room_id"]:checked')?.value;
        const date = document.querySelector('input[name="start_date"]')?.value;
        const startTime = document.getElementById('bookingStartTime')?.value;
        const endTime = document.getElementById('bookingEndTime')?.value;

        if (!roomId || !date || !startTime || !endTime) return;

        const statusEl = document.getElementById('availabilityStatus');
        const msgEl = document.getElementById('availabilityMessage');

        statusEl.className = 'availability-status checking';
        statusEl.querySelector('i').className = 'bi bi-arrow-clockwise';
        msgEl.textContent = 'Memeriksa ketersediaan...';

        availabilityTimer = setTimeout(() => {
            const startDateTime = `${date} ${startTime}:00`;
            const endDateTime = `${date} ${endTime}:00`;

            fetch('{{ route("bookings.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    room_id: roomId,
                    start_time: startDateTime,
                    end_time: endDateTime
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.available) {
                    statusEl.className = 'availability-status available';
                    statusEl.querySelector('i').className = 'bi bi-check-circle-fill';
                    msgEl.textContent = data.message;
                } else {
                    statusEl.className = 'availability-status unavailable';
                    statusEl.querySelector('i').className = 'bi bi-x-circle-fill';
                    msgEl.textContent = data.message;
                }
            })
            .catch(err => {
                statusEl.className = 'availability-status unavailable';
                statusEl.querySelector('i').className = 'bi bi-exclamation-triangle';
                msgEl.textContent = 'Gagal memeriksa ketersediaan';
            });
        }, 500);
    }

    // Highlight kartu ruangan yang dipilih
    function selectRoom(labelEl) {
        document.querySelectorAll('.room-option').forEach(function (el) {
            el.classList.remove('selected');
        });
        labelEl.classList.add('selected');
    }

    // ===== MODAL DETAIL RUANGAN =====
    var rdCurrentRoomId = null;

    function openRoomDetail(btn) {
        var name = btn.dataset.name;
        var location = btn.dataset.location;
        var capacity = btn.dataset.capacity;
        var description = btn.dataset.description || '';
        var photos = [];
        var facilities = [];
        try { photos = JSON.parse(btn.dataset.photos || '[]'); } catch (e) {}
        try { facilities = JSON.parse(btn.dataset.facilities || '[]'); } catch (e) {}

        rdCurrentRoomId = btn.dataset.roomId;

        document.getElementById('rdName').textContent = name;
        document.getElementById('rdLocation').textContent = location;
        document.getElementById('rdCapacity').textContent = capacity;

        var descEl = document.getElementById('rdDescription');
        if (description.trim()) {
            descEl.textContent = description;
            descEl.classList.remove('empty');
        } else {
            descEl.textContent = 'Belum ada deskripsi untuk ruangan ini.';
            descEl.classList.add('empty');
        }

        // Foto
        var mainImg = document.getElementById('rdPhotoMain');
        var placeholder = document.getElementById('rdPhotoPlaceholder');
        var thumbs = document.getElementById('rdThumbs');
        thumbs.innerHTML = '';

        if (photos.length > 0) {
            mainImg.src = photos[0];
            mainImg.style.display = 'block';
            placeholder.style.display = 'none';

            if (photos.length > 1) {
                photos.forEach(function (url, i) {
                    var img = document.createElement('img');
                    img.src = url;
                    if (i === 0) img.classList.add('active');
                    img.addEventListener('click', function () {
                        mainImg.src = url;
                        thumbs.querySelectorAll('img').forEach(function (t) { t.classList.remove('active'); });
                        img.classList.add('active');
                    });
                    thumbs.appendChild(img);
                });
            }
        } else {
            mainImg.style.display = 'none';
            placeholder.style.display = 'flex';
        }

        // Fasilitas
        var facWrap = document.getElementById('rdFacilities');
        facWrap.innerHTML = '';
        if (facilities.length > 0) {
            facilities.forEach(function (f) {
                var chip = document.createElement('span');
                chip.className = 'rd-facility-chip';
                chip.innerHTML = '<i class="bi bi-tools"></i> ' + f.name + ' (' + f.qty + ')';
                facWrap.appendChild(chip);
            });
        } else {
            var empty = document.createElement('p');
            empty.className = 'rd-facility-empty';
            empty.textContent = 'Belum ada fasilitas terdaftar untuk ruangan ini.';
            facWrap.appendChild(empty);
        }

        document.getElementById('rdOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeRoomDetail() {
        document.getElementById('rdOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    document.getElementById('rdPickBtn').addEventListener('click', function () {
        if (!rdCurrentRoomId) return;
        var radio = document.querySelector('input[name="room_id"][value="' + rdCurrentRoomId + '"]');
        if (radio) {
            radio.checked = true;
            selectRoom(radio.closest('.room-option'));
            checkAvailability();
        }
        closeRoomDetail();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('rdOverlay').classList.contains('open')) closeRoomDetail();
    });

    // MATERIAL TIME PICKER LOGIC
    var MTP_WORK_START = 7;
    var MTP_WORK_END = 16;
    var mtpState = {
        open: false,
        activeField: 'start',
        mode: 'hour',
        start: { h: 7, m: 0 },
        end: { h: 8, m: 0 }
    };

    function pad2(n) { return String(n).padStart(2, '0'); }
    function minsOf(t) { return t.h * 60 + t.m; }
    function isValidOperational(t) { var m = minsOf(t); return m >= MTP_WORK_START * 60 && m <= MTP_WORK_END * 60; }
    function parseLabel(str) { var p = String(str).trim().split(':'); return { h: parseInt(p[0], 10) || 7, m: parseInt((p[1] || '0').split(/\s/)[0], 10) || 0 }; }

    function getSelectedDateStr() {
        return document.querySelector('input[name="start_date"]')?.value || '';
    }

    function isSelectedDateToday() {
        var val = getSelectedDateStr();
        if (!val) return false;
        var today = new Date();
        var todayStr = today.getFullYear() + '-' + pad2(today.getMonth() + 1) + '-' + pad2(today.getDate());
        return val === todayStr;
    }

    function nowMinutes() {
        var now = new Date();
        return now.getHours() * 60 + now.getMinutes();
    }

    function nextValidStartToday() {
        var rounded = Math.ceil(nowMinutes() / 30) * 30;
        if (rounded < MTP_WORK_START * 60) rounded = MTP_WORK_START * 60;
        if (rounded > MTP_WORK_END * 60) rounded = MTP_WORK_END * 60;
        return { h: Math.floor(rounded / 60), m: rounded % 60 };
    }

    function openBookingTimePicker() {
        var startVal = document.getElementById('bookingStartTime').value || '07:00';
        var endVal = document.getElementById('bookingEndTime').value || '08:00';
        
        mtpState.start = parseLabel(startVal);
        mtpState.end   = parseLabel(endVal);
        
        if (!isValidOperational(mtpState.start)) mtpState.start = { h: MTP_WORK_START, m: 0 };
        if (!isValidOperational(mtpState.end))   mtpState.end   = { h: MTP_WORK_END, m: 0 };

        if (isSelectedDateToday() && minsOf(mtpState.start) < nowMinutes()) {
            mtpState.start = nextValidStartToday();
            if (minsOf(mtpState.end) <= minsOf(mtpState.start)) {
                var newEndMins = minsOf(mtpState.start) + 30;
                if (newEndMins > MTP_WORK_END * 60) newEndMins = MTP_WORK_END * 60;
                mtpState.end = { h: Math.floor(newEndMins / 60), m: newEndMins % 60 };
            }
        }
        
        mtpState.activeField = 'start';
        mtpState.mode = 'hour';
        mtpState.open = true;
        document.getElementById('mtpOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        renderClock();
        updateDigitalDisplay();
        hideMtpError();
    }

    function closeTimePicker(revert) {
        mtpState.open = false;
        document.getElementById('mtpOverlay').classList.remove('open');
        document.body.style.overflow = '';
        hideMtpError();
    }

    function setActiveField(field) {
        mtpState.activeField = field;
        mtpState.mode = 'hour';
        renderClock();
        updateDigitalDisplay();
        hideMtpError();
    }

    // FUNGSI BARU: Klik angka Jam/Menit langsung
    function selectUnit(field, mode) {
        mtpState.activeField = field;
        mtpState.mode = mode;
        renderClock();
        updateDigitalDisplay();
        hideMtpError();
    }

    function toggleTimeMode() {
        mtpState.mode = mtpState.mode === 'hour' ? 'minute' : 'hour';
        renderClock();
        updateDigitalDisplay();
    }

    function isDisabled(h, m) {
        if (mtpState.activeField === 'end') {
            var startMins = minsOf(mtpState.start);
            var currentMins = h * 60 + m;
            return currentMins <= startMins;
        }
        // Jam mulai: kalau tanggal yang dipilih hari ini, jam yang sudah lewat tidak bisa dipilih
        if (mtpState.activeField === 'start' && isSelectedDateToday()) {
            var slotMins = h * 60 + m;
            if (slotMins < nowMinutes()) return true;
        }
        return false;
    }

    function selectHour(h24) {
        if (isDisabled(h24, 0) && isDisabled(h24, 30)) return;
        var t = mtpState[mtpState.activeField];
        t.h = h24;
        hideMtpError();
        mtpState.mode = 'minute';
        renderClock();
        updateDigitalDisplay();
    }

    function selectMinute(m) {
        if (isDisabled(mtpState[mtpState.activeField].h, m)) return;
        var t = mtpState[mtpState.activeField];
        t.m = m;
        hideMtpError();
        updateDigitalDisplay();

        // AUTO-SWITCH LOGIC
        if (mtpState.activeField === 'start') {
            mtpState.activeField = 'end';
            mtpState.mode = 'hour';
            
            if (minsOf(mtpState.end) <= minsOf(mtpState.start)) {
                var newEndMins = minsOf(mtpState.start) + 30;
                if (newEndMins > MTP_WORK_END * 60) newEndMins = MTP_WORK_END * 60;
                mtpState.end = { h: Math.floor(newEndMins / 60), m: newEndMins % 60 };
            }
        }
        
        renderClock();
        updateDigitalDisplay();
    }

    function renderClock() {
        var clock = document.getElementById('mtpClock');
        clock.querySelectorAll('.mtp-num').forEach(function (n) { n.remove(); });

        var t = mtpState[mtpState.activeField];
        var R = 38.5; 
        var C = 50;   

        if (mtpState.mode === 'hour') {
            var hours = [];
            for (var h = MTP_WORK_START; h <= MTP_WORK_END; h++) hours.push(h);
            var totalHours = hours.length;
            var angleStep = 360 / totalHours;
            var offset = hours.indexOf(12); // 12 at top

            hours.forEach(function(h, i) {
                var angle = ((i - offset) * angleStep - 90) * Math.PI / 180;
                var x = C + R * Math.cos(angle);
                var y = C + R * Math.sin(angle);
                var el = document.createElement('div');
                el.className = 'mtp-num';
                el.textContent = pad2(h);
                el.style.left = x + '%';
                el.style.top = y + '%';
                
                var h00Disabled = isDisabled(h, 0);
                var h30Disabled = isDisabled(h, 30);
                
                if (h00Disabled && h30Disabled) {
                    el.classList.add('disabled');
                } else {
                    el.addEventListener('click', function() { selectHour(h); });
                    if (t.h === h) el.classList.add('selected');
                }
                clock.appendChild(el);
            });

            var selIdx = hours.indexOf(t.h);
            if (selIdx !== -1) {
                var handAngle = ((selIdx - offset) * angleStep - 90) * Math.PI / 180;
                var handX = C + 35 * Math.cos(handAngle);
                var handY = C + 35 * Math.sin(handAngle);
                setHand(handX, handY);
            }
            document.getElementById('mtpModeHint').textContent = mtpState.activeField === 'start' 
                ? 'Pilih Jam Mulai (07 - 16)' 
                : 'Pilih Jam Selesai (07 - 16)';
        } else {
            var minutes = [0, 30];
            minutes.forEach(function(m, i) {
                var angle = (i * 180 - 90) * Math.PI / 180;
                var x = C + R * Math.cos(angle);
                var y = C + R * Math.sin(angle);
                var el = document.createElement('div');
                el.className = 'mtp-num';
                el.textContent = pad2(m);
                el.style.left = x + '%';
                el.style.top = y + '%';
                
                if (isDisabled(t.h, m)) {
                    el.classList.add('disabled');
                } else {
                    el.addEventListener('click', function() { selectMinute(m); });
                    if (t.m === m) el.classList.add('selected');
                }
                clock.appendChild(el);
            });

            var selIdxM = minutes.indexOf(t.m);
            if (selIdxM !== -1) {
                var handAngleM = (selIdxM * 180 - 90) * Math.PI / 180;
                var handXM = C + 35 * Math.cos(handAngleM);
                var handYM = C + 35 * Math.sin(handAngleM);
                setHand(handXM, handYM);
            }
            document.getElementById('mtpModeHint').textContent = mtpState.activeField === 'start' 
                ? 'Pilih Menit Mulai (00 / 30)' 
                : 'Pilih Menit Selesai (00 / 30)';
        }
    }

    function setHand(x, y) {
        var hand = document.getElementById('mtpHand');
        hand.setAttribute('x2', x);
        hand.setAttribute('y2', y);
    }

    function updateDigitalDisplay() {
        var s = mtpState.start, e = mtpState.end;
        document.getElementById('mtpStartHour').textContent = pad2(s.h);
        document.getElementById('mtpStartMin').textContent = pad2(s.m);
        document.getElementById('mtpEndHour').textContent = pad2(e.h);
        document.getElementById('mtpEndMin').textContent = pad2(e.m);

        document.querySelectorAll('.mtp-field').forEach(function (f) {
            f.classList.toggle('active', f.dataset.field === mtpState.activeField);
        });

        var prefix = mtpState.activeField === 'start' ? 'mtpStart' : 'mtpEnd';
        var hourEl = document.getElementById(prefix + 'Hour');
        var minEl = document.getElementById(prefix + 'Min');
        document.querySelectorAll('.mtp-digital .mtp-unit').forEach(function (u) { u.classList.remove('mode-active'); });
        if (mtpState.mode === 'hour' && hourEl) hourEl.classList.add('mode-active');
        if (mtpState.mode === 'minute' && minEl) minEl.classList.add('mode-active');
    }

    function showMtpError(msg) {
        var err = document.getElementById('mtpError');
        err.querySelector('span').textContent = msg;
        err.classList.add('show');
    }
    function hideMtpError() {
        document.getElementById('mtpError').classList.remove('show');
    }

    function applyTimePicker() {
        if (!isValidOperational(mtpState.start) || !isValidOperational(mtpState.end)) {
            showMtpError('Waktu harus dalam rentang 07:00–16:00.');
            return;
        }
        if (minsOf(mtpState.start) >= minsOf(mtpState.end)) {
            showMtpError('Jam mulai harus lebih awal dari jam selesai.');
            return;
        }
        var startLabel = pad2(mtpState.start.h) + ':' + pad2(mtpState.start.m);
        var endLabel = pad2(mtpState.end.h) + ':' + pad2(mtpState.end.m);
        
        document.getElementById('bookingStartTime').value = startLabel;
        document.getElementById('bookingEndTime').value = endLabel;
        document.getElementById('bookingTimeLabel').textContent = startLabel + ' – ' + endLabel;
        
        closeTimePicker(false);
        checkAvailability(); // Trigger check after selecting time
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mtpState.open) closeTimePicker(true);
    });

    // Form submit
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Mengirim...';

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send"></i> Ajukan Peminjaman';
            }
        })
        .catch(err => {
            this.submit();
        });
    });
</script>
@endsection