@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('templates.template')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
{{-- BLOK INISIALISASI VARIABEL --}}
@php
    // FIX: sebelumnya dihitung per SLOT 30 menit yang terisi di
    // $bookingSchedule, bukan per booking unik — jadi 1 booking berdurasi
    // 2 jam (4 slot) kehitung 4 kali. Sekarang dihitung dari $dayBookings
    // (daftar booking asli hari ini), 1 booking = 1 hitungan.
    $totalBookingsToday = isset($dayBookings) ? $dayBookings->count() : 0;
    $totalPending = isset($dayBookings) ? $dayBookings->where('status', 0)->count() : 0;
    $totalApproved = isset($dayBookings) ? $dayBookings->where('status', 1)->count() : 0;
    $totalRejected = isset($dayBookings) ? $dayBookings->where('status', 2)->count() : 0;

    // FIX: sebelumnya ruangan dianggap "sibuk" kalau punya booking APA
    // SAJA hari ini (walau jadwalnya masih nanti sore) — bukan cek apakah
    // ada rapat yang BENAR-BENAR sedang berlangsung SEKARANG. Sekarang
    // dicek waktu saat ini ada di antara jam mulai-selesai booking yang
    // sudah disetujui.
    $rightNow = Carbon::now();
    $totalRoomsToday = isset($allRooms) ? $allRooms->count() : 0;
    $busyRoomIds = isset($dayBookings)
        ? $dayBookings->where('status', 1)->filter(function ($b) use ($rightNow) {
            return $rightNow->between(Carbon::parse($b->start_time), Carbon::parse($b->end_time));
        })->pluck('room_id')->unique()
        : collect([]);
    $availableRoomsNow = max(0, $totalRoomsToday - $busyRoomIds->count());

    $userName = Auth::user()->full_name ?? Auth::user()->name ?? 'User';
    $selectedRoomValue = (!empty($selectedRoomIds) && !in_array('all', $selectedRoomIds)) ? $selectedRoomIds[0] : '';

    // FIX: booking berstatus Ditolak sebelumnya tetap dirender di kalender
    // (dengan pola diagonal "terkunci"), padahal ditolak berarti slot itu
    // sebenarnya KOSONG dan bisa dipesan lagi — bukan sedang dipakai.
    // Jadi hanya Pending & Disetujui yang ditampilkan di grid/agenda.
    $visibleBookings = isset($dayBookings) ? $dayBookings->where('status', '!=', 2) : collect([]);
    
    $roomsData = [];
    if (isset($rooms)) {
        $roomsData = $rooms->map(function($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'photos' => $r->photos ? $r->photos->map(function($p) { return $p->photo_url; })->toArray() : []
            ];
        })->values()->toArray();
    }
@endphp

<style>
    /* ============================================================
       PALET WARNA STATUS — DASHBOARD
       Satu sumber kebenaran untuk semua warna status (Pending/
       Disetujui/Ditolak) & info yang dipakai berulang di halaman ini
       (kartu statistik, legend jadwal, kotak jadwal kalender, jam
       live). Dibangun DI ATAS variabel brand yang sudah ada di
       layout utama (--brand-orange, --brand-green, --brand-blue) —
       bukan menggantikannya. Sebelumnya nilai-nilai ini (mis. #f59e0b,
       #10b981, #ef4444) ditulis ulang mentah-mentah di banyak tempat
       berbeda di file ini; sekarang cukup diubah di satu tempat kalau
       suatu saat perlu disesuaikan, dan lebih mudah diaudit konsistensinya.
       ============================================================ */
    :root {
        --status-pending-solid: #f59e0b;
        --status-pending-bg: rgba(245, 158, 11, 0.12);
        --status-pending-text: #b45309;

        --status-approved-solid: #10b981;
        --status-approved-bg: rgba(16, 185, 129, 0.1);
        --status-approved-text: #047857;

        --status-rejected-solid: #ef4444;
        --status-rejected-bg: rgba(239, 68, 68, 0.08);
        --status-rejected-text: #b91c1c;

        --status-info-solid: var(--brand-blue);
        --status-info-bg: rgba(59, 130, 246, 0.1);
        --status-info-text: #1d4ed8;

        --status-neutral-solid: #94a3b8;

        /* Warna "Priority" — HARUS SAMA dengan badge Priority di halaman
           Detail Peminjaman (resources/views/bookings/show.blade.php),
           supaya makna "booking tipe Priority" konsisten di seluruh
           aplikasi, tidak beda warna di tiap halaman. */
        --priority-solid: #7c3aed;
        --priority-bg: rgba(139, 92, 246, 0.12);
    }

    /* ============================================================
       DASHBOARD SPECIFIC STYLES
       ============================================================ */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: var(--space-4);
        margin-bottom: var(--space-6);
    }
    .db-toolbar h1 {
        font-size: var(--font-size-2xl);
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.02em;
    }
    .db-meta {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-2) 0 0;
        display: flex;
        align-items: center;
        gap: var(--space-3);
        flex-wrap: wrap;
    }
    .actions {
        display: flex;
        gap: var(--space-3);
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    .badge-holiday {
        font-size: var(--font-size-xs);
        font-weight: 600;
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        background: var(--status-pending-bg);
        color: var(--status-pending-text);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-live-time {
        font-size: var(--font-size-xs);
        font-weight: 700;
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        background: var(--status-info-bg);
        color: var(--status-info-text);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-variant-numeric: tabular-nums;
    }
    .pulse-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--status-info-text);
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(29, 78, 216, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(29, 78, 216, 0); }
        100% { box-shadow: 0 0 0 0 rgba(29, 78, 216, 0); }
    }

    .btn-primary-sm {
        height: 44px;
        padding: 0 var(--space-5);
        background: var(--brand-orange);
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-inverse);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover {
        background: var(--brand-orange-dark);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(249, 115, 22, 0.25);
        color: var(--text-inverse);
    }

    /* Stat Cards */
    .db-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--space-4);
        margin-bottom: var(--space-6);
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: var(--space-5);
        display: flex;
        align-items: center;
        gap: var(--space-4);
        transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        min-width: 0;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }

    /* Statistik Pemakaian (khusus Admin) */
    .stats-charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); margin-bottom: var(--space-5); }
    .chart-card {
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card); padding: 1.25rem 1.5rem;
        min-width: 0; /* FIX: item grid harus boleh menyusut, defaultnya min-width:auto
                         bikin canvas Chart.js mendorong grid (dan halaman) melebar ke
                         samping di HP — inilah sebab card kepotong/scroll horizontal. */
        overflow: hidden;
    }
    .chart-card-title {
        font-size: 0.92rem; font-weight: 700; color: var(--text-primary);
        margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;
    }
    .chart-card-title i { color: var(--brand-orange); }
    .chart-canvas-wrapper { position: relative; height: 220px; width: 100%; }
    .chart-card-caption { text-align: center; color: var(--text-muted); font-size: 0.78rem; margin-top: 10px; }
    @media (max-width: 900px) { .stats-charts-grid { grid-template-columns: 1fr; } }
    .stat-icon {
        width: 56px; height: 56px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: var(--status-info-bg);    color: var(--brand-blue); }
    .stat-icon.green  { background: rgba(16,185,129,0.1);     color: var(--brand-green-dark); }
    .stat-icon.amber  { background: var(--status-pending-bg); color: var(--status-pending-text); }
    .stat-body { min-width: 0; }
    .stat-value {
        font-size: var(--font-size-2xl);
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .stat-value small { font-size: var(--font-size-sm); font-weight: 500; color: var(--text-muted); }
    .stat-label {
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
        font-weight: 600;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Control Bar */
    .db-controls {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: var(--space-4);
        margin-bottom: var(--space-6);
        background: var(--bg-card);
        padding: var(--space-4) var(--space-5);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
    }
    .date-navigator {
        display: flex;
        align-items: stretch;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        overflow: hidden;
        height: 44px;
    }
    .date-navigator > * { border: none; border-radius: 0; }
    .btn-today {
        height: 44px;
        padding: 0 var(--space-4);
        background: var(--brand-orange);
        color: var(--text-inverse);
        font-weight: 600;
        font-size: var(--font-size-sm);
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        cursor: pointer;
        white-space: nowrap;
        box-shadow: none;
    }
    .btn-today:hover { background: var(--brand-orange-dark); }
    .date-nav-btn {
        width: 44px;
        background: transparent;
        color: var(--text-secondary);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-left: 1px solid var(--border-color) !important;
        transition: all var(--transition-fast);
    }
    .date-nav-btn:hover {
        color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.04);
    }
    .date-display {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        padding: 0 var(--space-4);
        font-size: var(--font-size-sm);
        font-weight: 500;
        color: var(--text-primary);
        cursor: pointer;
        background: var(--bg-input);
        border-left: 1px solid var(--border-color) !important;
    }
    .date-display i { color: var(--brand-orange); }
    .date-display input {
        border: none;
        background: transparent;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        cursor: pointer;
        min-width: 120px;
    }

    .db-divider {
        width: 1px;
        align-self: stretch;
        background: var(--border-color-light);
    }

    .db-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        position: relative;
    }
    .db-filter-group label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }
    .db-select {
        height: 44px;
        min-width: 170px;
        padding: 0 42px 0 var(--space-3);
        font-size: var(--font-size-sm);
        font-weight: 500;
        font-family: inherit;
        color: var(--text-primary);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        cursor: pointer;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 16px;
    }
    .db-select:hover {
        border-color: #cbd5e1;
    }
    .db-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }

    .db-controls .filter-spacer { flex: 1; }

    .time-trigger {
        height: 44px;
        padding: 0 var(--space-4);
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        white-space: nowrap;
        transition: all var(--transition-fast);
    }
    .time-trigger:hover { border-color: var(--brand-orange); background: var(--bg-card); }
    .time-trigger i.bi-clock { color: var(--brand-orange); }
    .time-trigger .chevron { color: var(--text-muted); font-size: 0.65rem; margin-left: 2px; }

    /* Reset Filter Button */
    .btn-reset-filter {
        height: 44px;
        padding: 0 var(--space-4);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: var(--font-size-sm);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        transition: all var(--transition-fast);
    }
    .btn-reset-filter:hover {
        border-color: var(--brand-orange);
        color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.04);
    }

    /* ============================================================
       MATERIAL TIME PICKER
       ============================================================ */
    .mtp-overlay {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.4);
        animation: mtpFadeIn 0.18s ease;
        padding: 16px;
    }
    .mtp-overlay.open { display: flex; }
    .mtp-dialog {
        background: var(--bg-card);
        border-radius: 20px;
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
        width: 420px; 
        max-width: 100%; 
        max-height: 92vh;
        overflow-y: auto;
        animation: mtpSlideUp 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes mtpFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes mtpSlideUp { from { opacity: 0; transform: translateY(24px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }

    .mtp-header { padding: 24px 28px 8px; }
    .mtp-title {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-muted);
    }
    .mtp-subtitle {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .mtp-time-display {
        display: flex;
        justify-content: center;
        gap: 28px;
        padding: 16px 28px 12px;
    }
    .mtp-field {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        padding: 10px 16px;
        border-radius: 14px;
        transition: background var(--transition-fast);
    }
    .mtp-field:hover { background: var(--bg-hover); }
    .mtp-field.active .mtp-digital { color: var(--brand-orange); }
    .mtp-field.active .mtp-field-label { color: var(--brand-orange-dark); }
    .mtp-field-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }
    .mtp-digital {
        font-size: 44px;
        font-weight: 400;
        color: var(--text-primary);
        line-height: 1;
        display: flex;
        align-items: baseline;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .mtp-digital .mtp-colon { opacity: 0.45; padding: 0 2px; }
    .mtp-digital .mtp-unit {
        cursor: pointer;
        padding: 0 4px;
        border-radius: 8px;
        transition: background var(--transition-fast);
    }
    .mtp-digital .mtp-unit:hover { background: rgba(249, 115, 22, 0.08); }
    .mtp-digital .mtp-unit.mode-active {
        background: rgba(249, 115, 22, 0.14);
        color: var(--brand-orange);
    }

    .mtp-error {
        display: none;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #b91c1c;
        background: rgba(239, 68, 68, 0.08);
        border-radius: var(--radius-xs);
        padding: 8px 14px;
        margin: 4px 28px 0;
    }
    .mtp-error.show { display: flex; }

    .mtp-clock-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 28px 12px;
    }
    .mtp-clock {
        position: relative;
        width: 280px; 
        height: 280px;
        max-width: 80vw;
        max-height: 80vw;
        border-radius: 50%;
        background: var(--bg-body);
        user-select: none;
        flex-shrink: 0;
        margin: 0 auto;
    }
    .mtp-num {
        position: absolute;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 16px;
        font-weight: 500;
        color: var(--text-primary);
        cursor: pointer;
        transform: translate(-50%, -50%);
        transition: background 0.2s, color 0.2s, transform 0.2s;
        background: transparent;
        z-index: 2;
    }
    .mtp-num:hover:not(.disabled) { background: rgba(249, 115, 22, 0.12); }
    .mtp-num:active:not(.disabled) { transform: translate(-50%, -50%) scale(0.95); }
    .mtp-num.selected {
        background: var(--brand-orange);
        color: #fff;
        font-weight: 700;
        transform: translate(-50%, -50%) scale(1.05);
    }
    .mtp-num.disabled {
        color: var(--text-muted);
        opacity: 0.3;
        cursor: not-allowed;
        pointer-events: none;
    }
    .mtp-hand-svg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }
    .mtp-hand {
        stroke: var(--brand-orange);
        stroke-width: 2;
        stroke-linecap: round;
        transition: x2 0.2s ease, y2 0.2s ease;
        vector-effect: non-scaling-stroke;
    }
    .mtp-center-dot {
        position: absolute;
        left: 50%; top: 50%;
        width: 12px; height: 12px;
        background: var(--brand-orange);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 3;
    }
    .mtp-mode-hint {
        font-size: 12px;
        font-weight: 600;
        color: var(--brand-orange-dark);
        margin-top: 12px;
        height: 20px;
        text-align: center;
        background: rgba(249, 115, 22, 0.08);
        padding: 4px 14px;
        border-radius: 20px;
    }

    .mtp-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px 20px;
    }
    .mtp-icon-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 20px;
        cursor: pointer;
        padding: 10px;
        border-radius: 8px;
        transition: all var(--transition-fast);
    }
    .mtp-icon-btn:hover { background: var(--bg-hover); color: var(--brand-orange); }
    .mtp-actions { display: flex; gap: 8px; }
    .mtp-btn {
        height: 40px;
        padding: 0 20px;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all var(--transition-fast);
    }
    .mtp-btn-cancel { background: transparent; color: var(--text-secondary); }
    .mtp-btn-cancel:hover { background: var(--bg-hover); }
    .mtp-btn-ok { background: var(--brand-orange); color: #fff; }
    .mtp-btn-ok:hover { background: var(--brand-orange-dark); }

    /* ============================================================
       SCHEDULE GRID
       ============================================================ */
    .schedule-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .schedule-card .card-header {
        padding: var(--space-4) var(--space-5);
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-3);
        background: var(--bg-card);
    }
    .schedule-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .schedule-card .card-header .title i { color: var(--brand-orange); font-size: 1.2rem; }
    .schedule-card .card-header .title .date-info {
        font-weight: 400;
        font-size: var(--font-size-sm);
        color: var(--text-muted);
        margin-left: var(--space-1);
    }
    .schedule-card .card-header .header-right {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        flex-wrap: wrap;
    }
    .current-badge {
        background: var(--brand-orange);
        color: #fff;
        border: none;
        padding: 6px var(--space-3);
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }
    .current-badge:hover { filter: brightness(0.95); }

    .schedule-legend {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--space-5);
        padding: var(--space-3) var(--space-5);
        border-top: 1px solid var(--border-color-light);
        background: var(--bg-body);
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
        font-weight: 500;
    }
    .schedule-legend .legend-item { display: flex; align-items: center; gap: 6px; }
    .schedule-legend .dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .schedule-legend .dot.available { background: var(--brand-green); }
    .schedule-legend .dot.pending   { background: var(--status-pending-solid); }
    .schedule-legend .dot.approved  { background: var(--status-approved-solid); }
    .schedule-legend .dot.rejected  { background: var(--status-rejected-solid); }
    .schedule-legend .dot.current   { background: var(--brand-orange); box-shadow: 0 0 0 3px rgba(249,115,22,0.2); }
    .schedule-legend .dot.past      { background: var(--status-neutral-solid); }

    .schedule-card .card-body {
        padding: 0;
        overflow: auto;
        position: relative;
        max-height: 640px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }

    .cal-grid-header {
        display: grid;
        position: sticky;
        top: 0;
        z-index: 20;
        background: var(--bg-card);
        border-bottom: 2px solid var(--border-color);
        min-width: fit-content;
    }
    .cal-header-cell { padding: var(--space-3); }
    .cal-time-header {
        position: sticky;
        left: 0;
        z-index: 21;
        background: var(--bg-card);
        border-right: 2px solid var(--border-color);
    }

    /* ============================================================
       ROOM HEADER — REDESIGN
       Foto ditumpuk di atas (bulat, dengan ring tipis), nama & badge
       kapasitas di bawahnya, semua center-aligned. Layout stacked ini
       dipilih karena tetap simetris & rapi berapa pun panjang nama
       ruangannya (dulu, layout foto-di-samping bikin foto "menggantung"
       tidak sejajar begitu nama ruangan wrap ke 2 baris).
       ============================================================ */
    .cal-room-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 8px;
        padding: 18px 14px 16px;
        border-right: 1px solid var(--border-color-light);
        background: var(--bg-card);
        transition: background var(--transition-fast);
    }
    .cal-room-header:hover {
        background: var(--bg-body);
    }
    .cal-room-photo {
        width: 56px; height: 56px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid var(--bg-card);
        outline: 2px solid var(--border-color-light);
        box-shadow: 0 2px 6px rgba(15,23,42,0.08);
        transition: all var(--transition-fast);
        flex-shrink: 0;
    }
    .cal-room-photo:hover {
        outline-color: var(--brand-orange);
        transform: scale(1.06);
        box-shadow: 0 4px 12px rgba(249,115,22,0.18);
    }
    .cal-room-photo-placeholder {
        width: 56px; height: 56px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted);
        font-size: 1.3rem;
        background: var(--bg-body);
        outline: 2px solid var(--border-color-light);
        cursor: default;
        flex-shrink: 0;
    }
    .cal-room-info {
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }
    .cal-room-name {
        font-weight: 700;
        font-size: 13.5px;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        text-transform: none;
        white-space: normal;
        overflow-wrap: break-word;
        line-height: 1.3;
        max-width: 100%;
    }
    .cal-room-capacity {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        width: fit-content;
        font-size: 10.5px;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--bg-body);
        border: 1px solid var(--border-color-light);
        padding: 2px 9px;
        border-radius: var(--radius-pill);
    }
    .cal-room-header:hover .cal-room-capacity {
        border-color: var(--border-color);
    }
    .cal-room-meta-row {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        max-width: 100%;
    }
    .cal-room-location {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        width: 100%;
        max-width: 100%;
        justify-content: center;
        font-size: 10px;
        font-weight: 500;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cal-room-location i { color: var(--brand-orange); flex-shrink: 0; font-size: 9.5px; }

    .cal-grid-body-wrapper { position: relative; min-width: fit-content; }
    .cal-grid-body { display: grid; }
    
    .cal-time-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background: var(--bg-card);
        border-right: 2px solid var(--border-color);
        background-image: linear-gradient(to bottom, var(--border-color-light) 1px, transparent 1px);
        background-size: 100% 64px;
        background-position: 0 0;
    }
    .cal-time-label {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 6px;
        font-size: var(--font-size-xs);
        font-weight: 700;
        color: var(--brand-orange-dark);
        box-sizing: border-box;
        height: 64px; 
    }
    .cal-time-label.hour-mark { border-bottom: 1px solid var(--border-color); }

    .cal-room-col {
        position: relative;
        border-right: 1px solid var(--border-color);
        background-image: linear-gradient(to bottom, var(--border-color-light) 1px, transparent 1px);
        background-size: 100% 64px;
        background-position: 0 0;
    }

    /* Slot yang masih bisa dipesan */
    .cal-slot-hover {
        position: absolute;
        left: 0; right: 0;
        cursor: pointer;
        z-index: 1;
        height: 64px; 
    }
    .cal-slot-hover-inner {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: calc(100% - 4px);
        margin: 2px 6px;
        border: 1.5px dashed var(--brand-green);
        border-radius: var(--radius-sm);
        background: rgba(16, 185, 129, 0.08);
        color: var(--brand-green-dark);
        font-size: var(--font-size-xs);
        font-weight: 700;
    }
    .cal-slot-hover:hover .cal-slot-hover-inner { display: flex; }

    /* Slot yang sudah lewat (TIDAK BISA DIPESAN) */
    .cal-slot-past {
        position: absolute;
        left: 0; right: 0;
        height: 64px;
        z-index: 1;
        background-color: rgba(243, 244, 246, 0.6);
        background-image: repeating-linear-gradient(
            135deg,
            transparent,
            transparent 10px,
            rgba(226, 232, 240, 0.4) 10px,
            rgba(226, 232, 240, 0.4) 20px
        );
        pointer-events: none; /* Nonaktifkan klik */
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }
    .cal-slot-past i {
        opacity: 0.4;
        font-size: 0.8rem;
    }

    .cal-event {
        position: absolute;
        left: 6px; right: 6px;
        border-radius: 12px;
        padding: 9px 11px 9px 13px;
        z-index: 2;
        cursor: pointer;
        /* FIX: sebelumnya "hidden" — ini MEMOTONG tooltip kaya-info yang
           posisinya di luar kotak kartu (position:absolute, muncul di
           atas/bawah kartu saat hover). Diubah ke "visible"; aman karena
           potongan sudut membulat aksen kiri (::before) sudah punya
           border-radius sendiri yang presisi, tidak bergantung pada
           overflow:hidden induknya. Judul yang panjang tetap terpotong
           rapi kok — itu ditangani overflow:hidden di .cal-event-title
           sendiri (line-clamp), bukan di sini. */
        overflow: visible;
        border: 1px solid transparent;
        box-shadow: 0 1px 4px rgba(15,23,42,0.05);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 4px;
        backdrop-filter: saturate(1.1);
    }
    .cal-event.is-readonly { cursor: default; }
    .cal-event.is-readonly:hover { box-shadow: 0 1px 4px rgba(15,23,42,0.05); transform: none; }
    .cal-event::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        border-radius: 12px 0 0 12px;
        background: var(--event-accent, var(--brand-orange));
    }
    .cal-event:hover {
        box-shadow: 0 12px 28px rgba(15,23,42,0.14);
        transform: translateY(-2px) scale(1.012);
        z-index: 5;
    }
    .cal-event-top {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        min-width: 0;
    }
    .cal-event-avatar {
        width: 21px; height: 21px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700;
        color: #fff;
        box-shadow: 0 0 0 2px rgba(255,255,255,0.7);
    }
    .cal-event-title {
        font-weight: 700;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        white-space: normal;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.28;
        min-width: 0;
        letter-spacing: -0.01em;
        word-break: break-word;
    }
    .cal-event-sub {
        font-size: 11px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }
    .cal-event-time {
        font-size: 10.5px;
        color: var(--text-muted);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        font-variant-numeric: tabular-nums;
    }
    .cal-event-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 3px 8px 3px 6px;
        border-radius: 999px;
        width: fit-content;
        margin-top: auto;
        background: rgba(255,255,255,0.6);
    }
    .cal-event-status::before {
        content: '';
        width: 5px; height: 5px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .cal-event-priority-flag {
        position: absolute;
        top: 6px; right: 6px;
        font-size: 11px;
        filter: drop-shadow(0 1px 1px rgba(0,0,0,0.15));
    }
    .cal-event.is-compact .cal-event-sub,
    .cal-event.is-compact .cal-event-status { display: none; }

    /* ============================================================
       BARIS "BIDANG" — info tambahan di bawah nama peminjam. Ikut
       disembunyikan di kartu ringkas (is-compact) supaya kartu
       booking singkat/rapat tidak dipaksa menampilkan baris yang
       tidak akan muat.
       ============================================================ */
    .cal-event.is-compact .cal-event-bidang { display: none; }
    .cal-event-bidang { margin-top: -2px; }

    /* ============================================================
       TOOLTIP KAYA INFORMASI — menggantikan title="" bawaan browser
       (default browser: polos, lambat muncul ~1 detik, tanpa
       styling). Muncul di atas kartu saat hover, selalu berisi info
       LENGKAP (judul penuh, peminjam, bidang, ruangan, jam, status)
       apa pun ukuran kartu aslinya — jadi kalaupun teks di kartu
       terpotong karena sempit, detail lengkapnya tetap terlihat
       jelas tinggal arahkan kursor. Pola visualnya sama seperti
       tooltip fasilitas di halaman Tambah Peminjaman, supaya
       konsisten satu sama lain.
       ============================================================ */
    .cal-event-tooltip {
        position: absolute;
        bottom: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%) translateY(6px);
        background: #1e293b;
        color: #fff;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 500;
        min-width: 220px;
        max-width: 300px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.18s ease, transform 0.18s ease;
        z-index: 50;
        box-shadow: 0 14px 32px rgba(0,0,0,0.28);
        pointer-events: none;
        text-align: left;
        white-space: normal;
    }
    .cal-event-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1e293b;
    }
    .cal-event-tooltip-title {
        font-weight: 700; font-size: 13.5px; color: #fff;
        margin-bottom: 8px; line-height: 1.35;
        padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.15);
    }
    .cal-event-tooltip-row {
        display: flex; align-items: center; gap: 7px;
        color: #cbd5e1; line-height: 1.5;
        margin-top: 4px;
    }
    .cal-event-tooltip-row:first-of-type { margin-top: 0; }
    .cal-event-tooltip-row i { color: #94a3b8; font-size: 11px; flex-shrink: 0; width: 12px; text-align: center; }
    /* Hover di kartu (bukan di readonly) memunculkan tooltip. z-index
       kartu dinaikkan saat hover supaya tooltip-nya tidak ketiban
       kartu tetangga yang posisinya lebih "atas" secara dokumen. */
    .cal-event:hover { z-index: 20; }
    .cal-event:hover .cal-event-tooltip {
        opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0);
    }
    /* Kalau kartu ada di baris paling atas kalender, tooltip yang
       muncul ke ATAS akan terpotong tak terlihat (kepotong tepi
       layar) — dibalik ke bawah kartu untuk kartu pertama di tiap
       kolom ruangan (yang jamnya paling awal, otomatis di posisi
       paling atas grid). */
    .cal-room-col > .cal-event:first-child .cal-event-tooltip {
        bottom: auto; top: calc(100% + 10px);
    }
    .cal-room-col > .cal-event:first-child .cal-event-tooltip::after {
        top: auto; bottom: 100%;
        border-top-color: transparent; border-bottom-color: #1e293b;
    }

    .cal-weekend-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: var(--text-muted);
        font-weight: 600;
        background: repeating-linear-gradient(45deg, var(--bg-body), var(--bg-body) 10px, var(--border-color-light) 10px, var(--border-color-light) 20px);
    }

    .cal-now-line { position: absolute; left: 80px; right: 0; height: 0; border-top: 2px solid #ef4444; z-index: 15; pointer-events: none; }
    .cal-now-dot { position: absolute; left: -5px; top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.2); }

    .no-results { padding: var(--space-9); text-align: center; color: var(--text-muted); font-size: var(--font-size-md); }
    .no-results i { font-size: 2.75rem; display: block; margin-bottom: var(--space-3); color: var(--border-color); }

    /* ============================================================
       AGENDA LIST (MOBILE) — ditampilkan hanya di layar kecil,
       menggantikan grid horizontal supaya terasa seperti native app.
    ============================================================ */
    .agenda-mobile { display: none; }
    .agenda-room-tabs {
        display: flex;
        gap: 8px;
        padding: var(--space-3) var(--space-4);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-bottom: 1px solid var(--border-color-light);
        background: var(--bg-body);
        scrollbar-width: none;
    }
    .agenda-room-tabs::-webkit-scrollbar { display: none; }
    .agenda-room-tab {
        flex-shrink: 0;
        padding: 8px 16px;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .agenda-room-tab.active {
        background: var(--brand-orange);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 3px 10px rgba(249,115,22,0.25);
    }
    .agenda-room-panel { display: none; }
    .agenda-room-panel.active { display: block; }
    .agenda-empty {
        padding: var(--space-8) var(--space-4);
        text-align: center;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 14px;
    }
    .agenda-list { padding: var(--space-3) var(--space-4) var(--space-4); display: flex; flex-direction: column; gap: 8px; }
    .agenda-item {
        display: flex;
        align-items: center;
        gap: 12px;
        border-radius: 12px;
        padding: 12px;
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .agenda-item:active { transform: scale(0.98); }
    .agenda-item.is-readonly { cursor: default; }
    .agenda-item.is-readonly:active { transform: none; }
    .agenda-item-time {
        flex-shrink: 0;
        width: 46px;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-primary);
        font-variant-numeric: tabular-nums;
        display: flex;
        flex-direction: column;
        line-height: 1.3;
    }
    .agenda-item-time span { font-size: 10px; font-weight: 600; color: var(--text-muted); }
    .agenda-item-body { flex: 1; min-width: 0; }
    .agenda-item-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: flex-start;
        gap: 5px;
        line-height: 1.3;
    }
    .agenda-item-title .title-text {
        /* FIX: line-clamp:2 dihapus — baris list ini punya ruang lebar
           yang cukup, judul sekarang ditampilkan penuh tanpa dipotong. */
        word-break: break-word;
    }
    .agenda-item-title i { margin-top: 3px; flex-shrink: 0; }
    .agenda-item-sub {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
        margin-top: 2px;
        white-space: normal;
        word-break: break-word;
        display: flex; align-items: center; gap: 5px; flex-wrap: wrap;
    }
    .agenda-item-sub-divider { color: var(--text-muted); }
    .agenda-item-status {
        flex-shrink: 0;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 4px 9px 4px 7px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255,255,255,0.6);
    }
    .agenda-item-status::before {
        content: '';
        width: 5px; height: 5px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .agenda-item-booked {
        border-left: 4px solid var(--event-accent, var(--brand-orange));
        box-shadow: 0 1px 4px rgba(15,23,42,0.05);
        border-radius: 12px;
    }
    .agenda-item-available {
        border: 1.5px dashed var(--brand-green);
        background: rgba(16,185,129,0.05);
    }
    .agenda-item-available .agenda-item-time { color: var(--brand-green-dark); }
    .agenda-item-available .agenda-item-empty-label { color: var(--brand-green-dark); font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 6px; }
    .agenda-item-past {
        background: var(--bg-body);
        opacity: 0.6;
    }
    .agenda-item-past .agenda-item-empty-label { color: var(--text-muted); font-size: 13px; display: flex; align-items: center; gap: 6px; }

    /* ============================================================
       LIGHTBOX FOTO — desain disamakan dengan halaman Ruang Rapat
       (publik) & Manajemen Ruang Rapat (admin)
       ============================================================ */
    .gallery-lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 14, 25, 0.94);
        backdrop-filter: blur(6px);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        padding: 24px;
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

    /* Loading */
    .db-ajax-loading .schedule-card,
    .db-ajax-loading .db-stats-grid { opacity: 0.55; pointer-events: none; transition: opacity 0.15s; }

    /* ============================================================
       RESPONSIVE DASHBOARD
       ============================================================ */
    @media (max-width: 991.98px) {
        .db-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .db-controls { flex-direction: column; align-items: stretch; padding: var(--space-4); }
        .db-controls .filter-spacer { display: none; }
        .db-divider { display: none; }
        .date-navigator { width: 100%; }
        .date-navigator .date-display { flex: 1; }
        .date-navigator .date-display input { min-width: 0; width: 100%; }
        .db-filter-group { width: 100%; }
        .db-filter-group .db-select,
        .db-filter-group .time-trigger { width: 100%; }
        .btn-reset-filter { width: 100%; justify-content: center; }
        .db-toolbar { flex-direction: column; align-items: stretch; }
        .db-toolbar .actions { display: flex; }
        .db-toolbar .actions .btn-primary-sm { flex: 1; justify-content: center; }
        .schedule-card .card-header .title .date-info { display: none; }
        .cal-time-col, .cal-time-header { min-width: 60px !important; }
        .cal-room-header, .cal-room-col { min-width: 150px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 48px; height: 48px; }
        .cal-room-name { font-size: 12.5px; }
        .schedule-card .card-body { max-height: 500px; }
    }

    @media (max-width: 767.98px) {
        .cal-grid-header, .cal-grid-body-wrapper { display: none; }
        .agenda-mobile { display: block; }
        .schedule-card .card-body { max-height: none; overflow: visible; }
        .schedule-card .card-header .title .date-info { display: none; }
    }

    @media (max-width: 575.98px) {
        .db-stats-grid { grid-template-columns: 1fr; gap: var(--space-3); }
        .stat-card { padding: var(--space-4); gap: var(--space-3); }
        .stat-card .stat-icon { width: 44px; height: 44px; font-size: 1.2rem; }
        .stat-card .stat-value { font-size: var(--font-size-xl); }
        .cal-room-header, .cal-room-col { min-width: 130px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 42px; height: 42px; }
        .cal-event-title, .cal-event-sub { font-size: 11px; }
        .schedule-legend { gap: var(--space-3); padding: var(--space-3); }
        .db-toolbar h1 { font-size: var(--font-size-xl); }
        .schedule-card .card-header { flex-direction: column; align-items: flex-start; }
        
        .mtp-dialog { width: calc(100vw - 32px); }
        .mtp-header { padding: 20px 20px 4px; }
        .mtp-time-display { gap: 16px; padding: 8px 20px 4px; }
        .mtp-digital { font-size: 36px; }
        .mtp-field { padding: 8px 12px; }
        .mtp-clock { width: 240px; height: 240px; }
        .mtp-footer { padding: 12px 16px 16px; }
        
        .db-toolbar .db-meta { font-size: var(--font-size-xs); }
        .badge-holiday, .badge-live-time { font-size: 10px; padding: 3px 8px; }
    }

    @media (max-width: 991.98px) and (orientation: landscape) and (max-height: 500px) {
        .db-stats-grid { grid-template-columns: repeat(3, 1fr); }
        .db-toolbar { flex-direction: row; align-items: center; }
        .db-toolbar .actions { display: flex; }
        .schedule-card .card-body { max-height: 320px; }
    }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .db-controls { animation: fadeUp 0.3s ease forwards; }
    .schedule-card { animation: fadeUp 0.4s ease forwards; }
    .stat-card { animation: fadeUp 0.2s ease forwards; }
    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
</style>

<div class="db-toolbar">
    <div>
        <p class="db-meta">
            <span id="liveDateTime"><i class="bi bi-calendar3 me-1"></i> <span id="liveDate">Memuat...</span></span>
            <span class="badge-live-time"><span class="pulse-dot"></span> <span id="liveTime">00:00:00</span></span>
        </p>
    </div>
    <div class="actions">
        <a href="{{ route('bookings.create') }}" class="btn-primary-sm">
            <i class="bi bi-plus-lg"></i> Tambah Peminjaman
        </a>
    </div>
</div>

<div class="db-stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-building"></i></div>
        <div class="stat-body">
            <div class="stat-value" id="statAvailable">{{ $availableRoomsNow }}<small>/ {{ $totalRoomsToday }}</small></div>
            <div class="stat-label">Ruangan tersedia sekarang</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-body">
            <div class="stat-value" id="statBookings">{{ $totalBookingsToday }}</div>
            <div class="stat-label">Booking hari ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-body">
            <div class="stat-value" id="statPending">{{ $totalPending }}</div>
            <div class="stat-label">Menunggu persetujuan</div>
        </div>
    </div>
</div>

@if(Auth::user()->role == 1 && $roomUsageStats)
<div class="stats-charts-grid">
    <div class="chart-card">
        <div class="chart-card-title"><i class="bi bi-bar-chart-fill"></i> Ruangan Paling Sering Dipakai</div>
        <div class="chart-canvas-wrapper"><canvas id="roomUsageChart"></canvas></div>
        @if($roomUsageStats->sum('approved_count') == 0)
            <div class="chart-card-caption">Belum ada booking disetujui — semua ruangan masih 0.</div>
        @endif
    </div>
    <div class="chart-card">
        <div class="chart-card-title"><i class="bi bi-clock-history"></i> Jam Sibuk (30 Hari Terakhir)</div>
        <div class="chart-canvas-wrapper"><canvas id="busyHoursChart"></canvas></div>
        @if(array_sum($busyHoursStats) == 0)
            <div class="chart-card-caption">Belum ada aktivitas booking pada 30 hari terakhir.</div>
        @endif
    </div>
</div>
<script>
    // FIX: dibungkus DOMContentLoaded karena <script src="chart.js"> dimuat
    // di bagian bawah layout (setelah @yield('content')) — tanpa ini,
    // script di bawah jalan duluan sebelum Chart.js siap, jadi typeof Chart
    // selalu 'undefined' dan chart gagal dibuat tanpa error yang kelihatan
    // (persis gejala kotak kosong tanpa pesan apa pun).
    document.addEventListener('DOMContentLoaded', function() {
        // Chart tetap ditampilkan walau datanya kosong (semua bar bernilai 0)
        // supaya jelas ini bukan bug/loading gagal, cuma memang belum ada data.
        var roomCtx = document.getElementById('roomUsageChart');
        if (roomCtx && typeof Chart !== 'undefined') {
            new Chart(roomCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($roomUsageStats->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($roomUsageStats->pluck('approved_count')) !!},
                        backgroundColor: '#FF9142',
                        borderRadius: 6,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 5,
                            ticks: { precision: 0, stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                            grid: { display: false },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#64748b', font: { size: 11, weight: '600' } }
                        }
                    }
                }
            });
        } else if (roomCtx) {
            console.error('Chart.js belum termuat saat roomUsageChart diinisialisasi.');
        }

        var busyCtx = document.getElementById('busyHoursChart');
        if (busyCtx && typeof Chart !== 'undefined') {
            new Chart(busyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($busyHoursStats)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($busyHoursStats)) !!},
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 5,
                            ticks: { precision: 0, stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                            grid: { display: false },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#64748b', font: { size: 11, weight: '600' } }
                        }
                    }
                }
            });
        } else if (busyCtx) {
            console.error('Chart.js belum termuat saat busyHoursChart diinisialisasi.');
        }
    });
</script>
@endif

<div class="db-controls">
    <div class="db-filter-group">
        <label for="dateSelector">Tanggal</label>
        <div class="date-navigator" role="group" aria-label="Navigasi Tanggal">
            <button type="button" class="btn-today" onclick="resetToToday()" title="Hari Ini">
                <i class="bi bi-calendar-date"></i> Hari Ini
            </button>
            <button type="button" class="date-nav-btn" onclick="shiftDate(-1)" aria-label="Tanggal sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>
            <label class="date-display">
                <input type="date" id="dateSelector" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" onchange="filterByDate(this.value)" autocomplete="off" aria-label="Pilih tanggal">
            </label>
            <button type="button" class="date-nav-btn" onclick="shiftDate(1)" aria-label="Tanggal berikutnya">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="db-divider"></div>

    <div class="db-filter-group" id="timePickerWrapper">
        <label for="timeTrigger">Jam Kerja</label>
        <button type="button" class="time-trigger" id="timeTrigger" onclick="openTimePicker()" aria-haspopup="dialog" aria-expanded="false">
            <i class="bi bi-clock"></i>
            <span id="timeTriggerLabel">{{ $startTime ?? '07:00' }} - {{ $endTime ?? '16:00' }}</span>
            <i class="bi bi-chevron-down chevron"></i>
        </button>
    </div>

    <div class="db-divider"></div>

    <div class="db-filter-group">
        <label for="roomFilter">Ruangan</label>
        <select id="roomFilter" class="db-select" onchange="filterByRoom(this.value)">
            <option value="">Semua Ruangan</option>
            @if(isset($allRooms))
                @foreach($allRooms as $room)
                    <option value="{{ $room->id }}" {{ (string)$selectedRoomValue === (string)$room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="db-divider"></div>

    <div class="db-filter-group">
        <label for="statusFilterSelect">Status</label>
        <select id="statusFilterSelect" class="db-select" onchange="filterByStatus(this.value)">
            <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ ($statusFilter ?? '') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ ($statusFilter ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
    </div>

    <div class="filter-spacer"></div>

    <button type="button" class="btn-reset-filter" onclick="resetFilters()">
        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
    </button>
</div>

<div class="schedule-card">
    <div class="card-header">
        <div class="title">
            <i class="bi bi-table"></i> Jadwal Peminjaman Ruangan
            <span class="date-info">{{ Carbon::parse($selectedDate ?? date('Y-m-d'))->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
        <div class="header-right">
            @if($isWeekend ?? false)
                <span><i class="bi bi-lock"></i> Libur</span>
            @endif
        </div>
    </div>

    @include('pages.partials._schedule-body', ['timeSlots' => $timeSlots ?? [], 'rooms' => $rooms ?? collect(), 'isWeekend' => $isWeekend ?? false, 'isToday' => $isToday ?? false, 'selectedDate' => $selectedDate ?? date('Y-m-d'), 'visibleBookings' => $visibleBookings ?? collect(), 'statusFilter' => $statusFilter ?? 'all'])

    <div class="schedule-legend">
        <span class="legend-item"><span class="dot available"></span> Tersedia</span>
        <span class="legend-item"><span class="dot approved"></span> Disetujui</span>
        <span class="legend-item"><span class="dot pending"></span> Pending</span>
        <span class="legend-item"><span class="dot past"></span> Waktu Lewat</span>
        @if($isToday && !$isWeekend)
            <span class="legend-item"><span class="dot current"></span> Waktu sekarang</span>
        @endif
    </div>
</div>

<script type="application/json" id="roomsDataJson">@json($roomsData)</script>

<!-- TIME PICKER -->
<div class="mtp-overlay" id="mtpOverlay" onclick="if(event.target===this)closeTimePicker(true)">
    <div class="mtp-dialog" role="dialog" aria-modal="true" aria-labelledby="mtpTitle">
        <div class="mtp-header">
            <div class="mtp-title" id="mtpTitle">Pilih Jam Kerja</div>
            <div class="mtp-subtitle">Rentang 07:00 - 16:00</div>
        </div>
        <div class="mtp-time-display">
            <div class="mtp-field active" data-field="start" onclick="setActiveField('start')">
                <div class="mtp-field-label">Mulai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="mtpStartHour" onclick="event.stopPropagation();selectUnit('start','hour')">07</span><span class="mtp-colon">:</span><span class="mtp-unit" id="mtpStartMin" onclick="event.stopPropagation();selectUnit('start','minute')">00</span>
                </div>
            </div>
            <div class="mtp-field" data-field="end" onclick="setActiveField('end')">
                <div class="mtp-field-label">Selesai</div>
                <div class="mtp-digital">
                    <span class="mtp-unit" id="mtpEndHour" onclick="event.stopPropagation();selectUnit('end','hour')">16</span><span class="mtp-colon">:</span><span class="mtp-unit" id="mtpEndMin" onclick="event.stopPropagation();selectUnit('end','minute')">00</span>
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
        </div>
        <div class="mtp-footer">
            <button type="button" class="mtp-icon-btn" onclick="toggleTimeMode()" title="Ganti mode" aria-label="Ganti mode"></button>
            <div class="mtp-actions">
                <button type="button" class="mtp-btn mtp-btn-cancel" onclick="closeTimePicker(true)">Batal</button>
                <button type="button" class="mtp-btn mtp-btn-ok" onclick="applyTimePicker()">OK</button>
            </div>
        </div>
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

@push('scripts')
<script>
(function () {
    'use strict';

    // REAL-TIME DATE & TIME
    function updateLiveDateTime() {
        var now = new Date();
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        var dateStr = now.toLocaleDateString('id-ID', options);
        var timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        var dateEl = document.getElementById('liveDate');
        var timeEl = document.getElementById('liveTime');
        if (dateEl) dateEl.textContent = dateStr;
        if (timeEl) timeEl.textContent = timeStr;
    }
    setInterval(updateLiveDateTime, 1000);
    updateLiveDateTime();

    // GALERI LIGHTBOX — desain disamakan dengan halaman Ruang Rapat & Manajemen Ruang Rapat
    var roomsData = [];
    try { roomsData = JSON.parse(document.getElementById('roomsDataJson').textContent); } catch (e) { roomsData = []; }
    var currentPhotoIndex = 0;
    var currentPhotos = [];
    var currentRoomName = '';

    window.openGallery = function (roomId) {
        var room = roomsData.find(function (r) { return r.id == roomId; });
        if (!room || !room.photos || room.photos.length === 0) return;
        currentPhotos = room.photos;
        currentPhotoIndex = 0;
        currentRoomName = room.name || '';
        updateLightbox();
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    function updateLightbox() {
        document.getElementById('lightboxImage').src = currentPhotos[currentPhotoIndex];
        document.getElementById('lightboxImage').alt = currentRoomName || 'Foto Ruangan';
        document.getElementById('lightboxCaption').textContent = currentRoomName;
        document.getElementById('lightboxCounter').textContent = (currentPhotoIndex + 1) + ' dari ' + currentPhotos.length;

        var multi = currentPhotos.length > 1;
        document.getElementById('lightboxPrev').style.display = multi ? 'flex' : 'none';
        document.getElementById('lightboxNext').style.display = multi ? 'flex' : 'none';

        var thumbs = document.getElementById('lightboxThumbnails');
        thumbs.innerHTML = '';
        if (multi) {
            currentPhotos.forEach(function (p, idx) {
                var thumb = document.createElement('img');
                thumb.src = p; thumb.alt = 'Thumbnail ' + (idx + 1);
                thumb.className = idx === currentPhotoIndex ? 'active' : '';
                thumb.onclick = function () { currentPhotoIndex = idx; updateLightbox(); };
                thumbs.appendChild(thumb);
            });
        }
    }

    window.closeLightbox = function () {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    };
    window.navigateLightbox = function (dir) {
        var i = currentPhotoIndex + dir;
        if (i < 0 || i >= currentPhotos.length) return;
        currentPhotoIndex = i;
        updateLightbox();
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

    // AJAX LOADER
    var isLoading = false;
    function getCurrentParams() {
        var url = new URL(window.location.href);
        var params = {};
        ['date', 'rooms', 'status', 'start_time', 'end_time'].forEach(function (k) {
            var v = url.searchParams.get(k);
            if (v) params[k] = v;
        });
        var dateInput = document.getElementById('dateSelector');
        if (dateInput && dateInput.value) params.date = dateInput.value;
        return params;
    }

    function loadDashboard(params, opts) {
        opts = opts || {};
        if (isLoading) return;
        isLoading = true;
        var url = new URL(window.location.origin + window.location.pathname);
        Object.keys(params).forEach(function (k) {
            var v = params[k];
            if (v !== '' && v != null) url.searchParams.set(k, v);
        });
        if (!opts.skipPushState) history.pushState({ params: params }, '', url.toString());

        document.body.classList.add('db-ajax-loading');

        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                function replaceBy(sel) {
                    var cur = document.querySelector(sel);
                    var neu = doc.querySelector(sel);
                    if (cur && neu) cur.replaceWith(neu);
                }
                replaceBy('.db-stats-grid');
                replaceBy('.schedule-card');
                var newRdj = doc.querySelector('#roomsDataJson');
                var curRdj = document.getElementById('roomsDataJson');
                if (newRdj && curRdj) {
                    curRdj.textContent = newRdj.textContent;
                    try { roomsData = JSON.parse(newRdj.textContent); } catch (e) {}
                }
                var newDate = doc.querySelector('#dateSelector');
                if (newDate) document.getElementById('dateSelector').value = newDate.value;
                var newLabel = doc.querySelector('#timeTriggerLabel');
                var curLabel = document.getElementById('timeTriggerLabel');
                if (newLabel && curLabel) curLabel.textContent = newLabel.textContent;
                var newRoom = doc.querySelector('#roomFilter');
                if (newRoom) document.getElementById('roomFilter').value = newRoom.value;
                var newStatus = doc.querySelector('#statusFilterSelect');
                if (newStatus) document.getElementById('statusFilterSelect').value = newStatus.value;
                var nowLine = document.getElementById('nowLine');
                if (nowLine) setTimeout(scrollToNow, 300);
            })
            .catch(function () { window.location.href = url.toString(); })
            .finally(function () {
                isLoading = false;
                document.body.classList.remove('db-ajax-loading');
            });
    }

    window.filterByDate = function (d) { var p = getCurrentParams(); p.date = d; loadDashboard(p); };
    window.shiftDate = function (days) {
        var input = document.getElementById('dateSelector');
        var base = input && input.value ? new Date(input.value) : new Date();
        base.setDate(base.getDate() + days);
        filterByDate(base.getFullYear() + '-' + String(base.getMonth()+1).padStart(2,'0') + '-' + String(base.getDate()).padStart(2,'0'));
    };
    window.resetToToday = function () {
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        var p = getCurrentParams();
        p.date = y + '-' + m + '-' + d;
        loadDashboard(p);
    };
    window.filterByRoom = function (id) { var p = getCurrentParams(); if (id) p.rooms = id; else delete p.rooms; loadDashboard(p); };
    window.filterByStatus = function (s) { var p = getCurrentParams(); if (s && s !== 'all') p.status = s; else delete p.status; loadDashboard(p); };
    
    // Reset All Filters
    window.resetFilters = function() {
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        
        var params = {
            date: y + '-' + m + '-' + d,
            start_time: '07:00',
            end_time: '16:00'
        };
        loadDashboard(params);
        
        // Reset manual UI elements
        var dateInput = document.getElementById('dateSelector');
        if (dateInput) dateInput.value = params.date;
        
        var timeLabel = document.getElementById('timeTriggerLabel');
        if (timeLabel) timeLabel.textContent = '07:00 - 16:00';
        
        var roomFilter = document.getElementById('roomFilter');
        if (roomFilter) roomFilter.value = '';
        
        var statusFilter = document.getElementById('statusFilterSelect');
        if (statusFilter) statusFilter.value = 'all';
    };
    
    window.addEventListener('popstate', function () { window.location.reload(); });

    // TIME PICKER
    var MTP_WORK_START = 7, MTP_WORK_END = 16;
    var mtpState = { open: false, activeField: 'start', mode: 'hour', start: { h: 7, m: 0 }, end: { h: 16, m: 0 } };

    function pad2(n) { return String(n).padStart(2, '0'); }
    function minsOf(t) { return t.h * 60 + t.m; }
    function isValidOperational(t) { var m = minsOf(t); return m >= MTP_WORK_START * 60 && m <= MTP_WORK_END * 60; }
    function parseLabel(str) { var p = String(str).trim().split(':'); return { h: parseInt(p[0], 10) || 7, m: parseInt((p[1] || '0').split(/\s/)[0], 10) || 0 }; }

    window.openTimePicker = function () {
        var labelEl = document.getElementById('timeTriggerLabel');
        var parts = labelEl.textContent.split('-');
        mtpState.start = parseLabel(parts[0] || '07:00');
        mtpState.end = parseLabel(parts[1] || '16:00');
        if (!isValidOperational(mtpState.start)) mtpState.start = { h: MTP_WORK_START, m: 0 };
        if (!isValidOperational(mtpState.end)) mtpState.end = { h: MTP_WORK_END, m: 0 };
        mtpState.activeField = 'start'; mtpState.mode = 'hour'; mtpState.open = true;
        document.getElementById('mtpOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        renderClock(); updateDigitalDisplay(); hideMtpError();
    };
    window.closeTimePicker = function () { mtpState.open = false; document.getElementById('mtpOverlay').classList.remove('open'); document.body.style.overflow = ''; hideMtpError(); };
    window.setActiveField = function (f) { mtpState.activeField = f; mtpState.mode = 'hour'; renderClock(); updateDigitalDisplay(); hideMtpError(); };
    window.selectUnit = function (f, m) { mtpState.activeField = f; mtpState.mode = m; renderClock(); updateDigitalDisplay(); hideMtpError(); };
    window.toggleTimeMode = function () { mtpState.mode = mtpState.mode === 'hour' ? 'minute' : 'hour'; renderClock(); updateDigitalDisplay(); };

    function isDisabled(h, m) {
        if (mtpState.activeField === 'end') { return (h * 60 + m) <= minsOf(mtpState.start); }
        return false;
    }
    window.selectHour = function (h) {
        if (isDisabled(h, 0) && isDisabled(h, 30)) return;
        mtpState[mtpState.activeField].h = h; hideMtpError();
        mtpState.mode = 'minute'; renderClock(); updateDigitalDisplay();
    };
    window.selectMinute = function (m) {
        if (isDisabled(mtpState[mtpState.activeField].h, m)) return;
        mtpState[mtpState.activeField].m = m; hideMtpError(); updateDigitalDisplay();
        if (mtpState.activeField === 'start') {
            mtpState.activeField = 'end'; mtpState.mode = 'hour';
            if (minsOf(mtpState.end) <= minsOf(mtpState.start)) {
                var n = minsOf(mtpState.start) + 30;
                if (n > MTP_WORK_END * 60) n = MTP_WORK_END * 60;
                mtpState.end = { h: Math.floor(n / 60), m: n % 60 };
            }
        }
        renderClock(); updateDigitalDisplay();
    };

    function renderClock() {
        var clock = document.getElementById('mtpClock');
        clock.querySelectorAll('.mtp-num').forEach(function (n) { n.remove(); });
        var t = mtpState[mtpState.activeField];
        var R = 38.5, C = 50;
        if (mtpState.mode === 'hour') {
            var hours = [];
            for (var h = MTP_WORK_START; h <= MTP_WORK_END; h++) hours.push(h);
            var total = hours.length, step = 360 / total, offset = hours.indexOf(12);
            hours.forEach(function (h, i) {
                var a = ((i - offset) * step - 90) * Math.PI / 180;
                var x = C + R * Math.cos(a), y = C + R * Math.sin(a);
                var el = document.createElement('div');
                el.className = 'mtp-num'; el.textContent = pad2(h);
                el.style.left = x + '%'; el.style.top = y + '%';
                if (isDisabled(h, 0) && isDisabled(h, 30)) { el.classList.add('disabled'); }
                else { el.addEventListener('click', function () { selectHour(h); }); if (t.h === h) el.classList.add('selected'); }
                clock.appendChild(el);
            });
            var si = hours.indexOf(t.h);
            if (si !== -1) { var ha = ((si - offset) * step - 90) * Math.PI / 180; setHand(C + 35 * Math.cos(ha), C + 35 * Math.sin(ha)); }
        } else {
            [0, 30].forEach(function (m, i) {
                var a = (i * 180 - 90) * Math.PI / 180;
                var x = C + R * Math.cos(a), y = C + R * Math.sin(a);
                var el = document.createElement('div');
                el.className = 'mtp-num'; el.textContent = pad2(m);
                el.style.left = x + '%'; el.style.top = y + '%';
                if (isDisabled(t.h, m)) { el.classList.add('disabled'); }
                else { el.addEventListener('click', function () { selectMinute(m); }); if (t.m === m) el.classList.add('selected'); }
                clock.appendChild(el);
            });
            var sm = [0, 30].indexOf(t.m);
            if (sm !== -1) { var ma = (sm * 180 - 90) * Math.PI / 180; setHand(C + 35 * Math.cos(ma), C + 35 * Math.sin(ma)); }
        }
    }
    function setHand(x, y) { document.getElementById('mtpHand').setAttribute('x2', x); document.getElementById('mtpHand').setAttribute('y2', y); }

    function updateDigitalDisplay() {
        var s = mtpState.start, e = mtpState.end;
        document.getElementById('mtpStartHour').textContent = pad2(s.h);
        document.getElementById('mtpStartMin').textContent = pad2(s.m);
        document.getElementById('mtpEndHour').textContent = pad2(e.h);
        document.getElementById('mtpEndMin').textContent = pad2(e.m);
        document.querySelectorAll('.mtp-field').forEach(function (f) { f.classList.toggle('active', f.dataset.field === mtpState.activeField); });
        var pre = mtpState.activeField === 'start' ? 'mtpStart' : 'mtpEnd';
        var he = document.getElementById(pre + 'Hour'), me = document.getElementById(pre + 'Min');
        document.querySelectorAll('.mtp-digital .mtp-unit').forEach(function (u) { u.classList.remove('mode-active'); });
        if (mtpState.mode === 'hour' && he) he.classList.add('mode-active');
        if (mtpState.mode === 'minute' && me) me.classList.add('mode-active');
    }
    function showMtpError(msg) { var err = document.getElementById('mtpError'); err.querySelector('span').textContent = msg; err.classList.add('show'); }
    function hideMtpError() { document.getElementById('mtpError').classList.remove('show'); }

    window.applyTimePicker = function () {
        if (!isValidOperational(mtpState.start) || !isValidOperational(mtpState.end)) { showMtpError('Waktu harus dalam rentang 07:00–16:00.'); return; }
        if (minsOf(mtpState.start) >= minsOf(mtpState.end)) { showMtpError('Jam mulai harus lebih awal dari jam selesai.'); return; }
        var sl = pad2(mtpState.start.h) + ':' + pad2(mtpState.start.m);
        var el = pad2(mtpState.end.h) + ':' + pad2(mtpState.end.m);
        document.getElementById('timeTriggerLabel').textContent = sl + ' - ' + el;
        closeTimePicker();
        var p = getCurrentParams(); p.start_time = sl; p.end_time = el;
        loadDashboard(p);
    };
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && mtpState.open) closeTimePicker(); });

    // GRID ACTIONS
    window.showBookingDetails = function (id) { window.location.href = '/bookings/' + id; };
    window.quickBook = function (roomId, date, time) {
        var url = new URL('{{ route("bookings.create") }}');
        url.searchParams.set('room', roomId);
        url.searchParams.set('date', date);
        url.searchParams.set('start_time', time);
        window.location.href = url.toString();
    };
    window.scrollToNow = function () {
        var line = document.getElementById('nowLine');
        var container = document.getElementById('scheduleBody');
        if (line && container) { container.scrollTop = Math.max(0, line.offsetTop - 80); }
    };
    if (document.getElementById('nowLine')) { setTimeout(scrollToNow, 300); }

    // AGENDA LIST (MOBILE) — ganti ruangan yang ditampilkan
    window.switchAgendaRoom = function (roomId) {
        document.querySelectorAll('.agenda-room-tab').forEach(function (el) {
            el.classList.toggle('active', String(el.dataset.roomTab) === String(roomId));
        });
        document.querySelectorAll('.agenda-room-panel').forEach(function (el) {
            el.classList.toggle('active', String(el.dataset.roomPanel) === String(roomId));
        });
    };

})();
</script>
@endpush

{{-- ============================================================
     LIVE UPDATE — polling berkala (bukan WebSocket, server belum
     punya infrastrukturnya). Kalender jadwal di-refresh otomatis
     tiap beberapa detik memakai filter/tanggal yang SAMA seperti
     yang sedang aktif di layar, supaya kalau ada booking baru/
     berubah status dari device/akun lain, admin/karyawan yang
     sedang lihat dashboard ini langsung melihatnya tanpa reload.
     ============================================================ --}}
@push('scripts')
<script>
(function () {
    var lastSignature = 'initial';

    createLivePoller({
        url: window.location.pathname + window.location.search,
        interval: 15000,
        onUpdate: function (data) {
            if (data.signature === lastSignature) return;
            lastSignature = data.signature;

            var container = document.getElementById('scheduleBody');
            if (!container) return;

            // Simpan posisi scroll dulu — supaya kalau admin sedang lihat
            // jam tertentu di jadwal, tidak tiba-tiba lompat ke atas
            // setiap kali ada pembaruan.
            var scrollTop = container.scrollTop;
            var scrollLeft = container.scrollLeft;

            container.outerHTML = data.schedule;

            var refreshed = document.getElementById('scheduleBody');
            if (refreshed) {
                refreshed.scrollTop = scrollTop;
                refreshed.scrollLeft = scrollLeft;
            }
        }
    });
})();
</script>
@endpush
@endsection