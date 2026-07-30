@php
    use Carbon\Carbon;
@endphp

@extends('templates.template')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Jadwal peminjaman ruangan hari ini')

@section('content')
<style>
    /* ============================================================
       DASHBOARD — layout dasar
       ============================================================ */
    .dashboard-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }

    /* ---------- Toolbar (judul halaman + aksi utama) ---------- */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-3);
        margin-bottom: var(--space-4);
    }
    .db-toolbar h1 {
        font-size: var(--font-size-xl);
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.02em;
    }
    .db-toolbar .db-meta {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-1) 0 0 0;
        display: flex;
        align-items: center;
        gap: var(--space-2);
        flex-wrap: wrap;
    }
    .db-toolbar .actions {
        display: flex;
        gap: var(--space-2);
        flex-wrap: wrap;
    }
    .badge-holiday {
        font-size: var(--font-size-xs);
        font-weight: 600;
        padding: 2px 10px;
        border-radius: var(--radius-pill);
        background: rgba(245, 158, 11, 0.12);
        color: #b45309;
        display: inline-flex;
        align-items: center;
        gap: 4px;
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
    .btn-outline-sm {
        height: 38px;
        padding: 0 var(--space-4);
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
        text-decoration: none;
    }
    .btn-outline-sm:hover {
        border-color: var(--brand-orange);
        color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.04);
    }
    .btn-primary-sm:focus-visible,
    .btn-outline-sm:focus-visible,
    .date-nav-btn:focus-visible,
    .btn-today:focus-visible,
    .db-select:focus-visible {
        outline: 2px solid var(--brand-orange);
        outline-offset: 2px;
    }

    /* ---------- Control bar (navigasi tanggal + filter) ---------- */
    .db-controls {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--space-4);
        margin-bottom: var(--space-4);
        background: var(--bg-card);
        padding: var(--space-3) var(--space-5);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        min-height: 68px;
    }

    /* Grup navigasi tanggal — satu unit visual (today | ‹ tanggal › | date-picker) */
    .date-navigator {
        display: flex;
        align-items: stretch;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        overflow: hidden;
        height: 38px;
    }
    .date-navigator > * { border: none; border-radius: 0; }
    .date-navigator .btn-today {
        height: 38px;
        padding: 0 var(--space-4);
        background: var(--brand-gradient);
        color: var(--text-inverse);
        font-weight: 600;
        font-size: var(--font-size-sm);
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
        cursor: pointer;
        white-space: nowrap;
        box-shadow: none;
    }
    .date-navigator .btn-today:hover { filter: brightness(1.05); }
    .date-nav-btn {
        width: 38px;
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
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
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
    }

    .db-divider {
        width: 1px;
        align-self: stretch;
        background: var(--border-color-light);
    }

    /* Filter sekunder: jam kerja, ruangan, status — dikelompokkan & diberi label kecil supaya jelas berbeda prioritas dari navigasi tanggal */
    .db-filter-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .db-filter-group label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--text-muted);
    }
    .db-filter-row { display: flex; align-items: center; gap: var(--space-2); }
    .db-filter-row input[type="time"],
    .db-select {
        height: 34px;
        padding: 0 var(--space-2);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xs);
        color: var(--text-primary);
    }
    .db-filter-row input[type="time"] { width: 100px; }
    .db-select { min-width: 150px; }
    .db-filter-row i { color: var(--text-muted); font-size: var(--font-size-sm); }

    .db-controls .filter-spacer { flex: 1; }

    /* ---------- Quick stats: memberi fungsi pada data yang sudah dihitung ---------- */
    .db-quickstats {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-2);
        margin-bottom: var(--space-4);
    }
    .qs-chip {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-2) var(--space-4);
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-pill);
        box-shadow: var(--shadow-card);
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }
    .qs-chip b { color: var(--text-primary); font-weight: 700; }
    .qs-chip .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .qs-chip .dot.green { background: var(--brand-green); }
    .qs-chip .dot.amber { background: #f59e0b; }
    .qs-chip .dot.red { background: #ef4444; }
    .qs-chip .dot.blue { background: var(--brand-blue); }

    /* ---------- Kartu jadwal ---------- */
    .schedule-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .schedule-card .card-header {
        padding: var(--space-3) var(--space-5);
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-3);
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
        padding: 4px var(--space-3);
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .current-badge:hover { filter: brightness(0.95); }

    /* Legenda terintegrasi di footer kartu — tidak lagi jadi kartu terpisah */
    .schedule-legend {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--space-4);
        padding: var(--space-2) var(--space-5);
        border-top: 1px solid var(--border-color-light);
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
    }
    .schedule-legend .legend-item { display: flex; align-items: center; gap: var(--space-1); }
    .schedule-legend .dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .schedule-legend .dot.available { background: var(--brand-green); }
    .schedule-legend .dot.pending { background: #f59e0b; }
    .schedule-legend .dot.approved { background: #10b981; }
    .schedule-legend .dot.rejected { background: #ef4444; }
    .schedule-legend .dot.current { background: var(--brand-orange); box-shadow: 0 0 0 2px rgba(249,115,22,0.25); }

    .schedule-card .card-body {
        padding: 0;
        overflow: auto;
        position: relative;
        max-height: 620px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: var(--text-muted) transparent;
    }
    .schedule-card .card-body::-webkit-scrollbar { width: 6px; height: 6px; }
    .schedule-card .card-body::-webkit-scrollbar-thumb { background: var(--text-muted); border-radius: var(--radius-pill); }

    /* ---------- Grid kalender (satu-satunya implementasi jadwal yang benar-benar dipakai) ---------- */
    .cal-grid-header {
        display: grid;
        position: sticky;
        top: 0;
        z-index: 20;
        background: var(--bg-card);
        border-bottom: 2px solid var(--border-color-light);
        min-width: fit-content;
    }
    .cal-header-cell { padding: var(--space-2); }
    .cal-time-header {
        position: sticky;
        left: 0;
        z-index: 21;
        background: var(--bg-card);
        border-right: 1px solid var(--border-color-light);
    }
    .cal-room-header {
        display: flex;
        align-items: center;
        gap: 10px;
        border-right: 1px solid var(--border-color-light);
    }
    .cal-room-photo {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all var(--transition-fast);
        flex-shrink: 0;
    }
    .cal-room-photo:hover { transform: scale(1.05); border-color: var(--brand-orange); }
    .cal-room-photo:focus-visible { outline: 2px solid var(--brand-orange); outline-offset: 2px; }
    .cal-room-photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 1.6rem;
        background: var(--bg-body);
        border: 1px dashed var(--border-color);
        cursor: default;
        flex-shrink: 0;
    }
    .cal-room-info { text-align: left; min-width: 0; }
    .cal-room-name {
        font-weight: 700;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cal-room-capacity { font-size: var(--font-size-xs); color: var(--text-muted); }

    .cal-grid-body-wrapper { position: relative; min-width: fit-content; }
    .cal-grid-body { display: grid; }
    .cal-time-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background: var(--bg-card);
        border-right: 1px solid var(--border-color-light);
    }
    .cal-time-label {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 4px;
        font-size: var(--font-size-xs);
        font-weight: 700;
        color: var(--brand-orange-dark);
        border-bottom: 1px solid var(--border-color-light);
        box-sizing: border-box;
    }
    .cal-room-col {
        position: relative;
        border-right: 1px solid var(--border-color-light);
        background-image: repeating-linear-gradient(to bottom, var(--border-color-light) 0px, var(--border-color-light) 1px, transparent 1px, transparent 64px);
    }
    .cal-slot-hover {
        position: absolute;
        left: 0;
        right: 0;
        cursor: pointer;
        z-index: 1;
    }
    .cal-slot-hover-inner {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: calc(100% - 4px);
        margin: 2px 6px;
        border: 1.5px dashed var(--brand-orange);
        border-radius: var(--radius-sm);
        background: rgba(249, 115, 22, 0.06);
        color: var(--brand-orange-dark);
        font-size: var(--font-size-xs);
        font-weight: 600;
    }
    .cal-slot-hover:hover .cal-slot-hover-inner,
    .cal-slot-hover:focus-visible .cal-slot-hover-inner { display: flex; }
    .cal-slot-hover:focus-visible { outline: 2px solid var(--brand-orange); outline-offset: -2px; border-radius: var(--radius-sm); }

    .cal-event {
        position: absolute;
        left: 6px;
        right: 6px;
        border-left: 4px solid;
        border-radius: var(--radius-sm);
        padding: 6px 10px;
        z-index: 2;
        cursor: pointer;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        transition: box-shadow var(--transition-fast), transform var(--transition-fast);
    }
    .cal-event:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.12); transform: translateY(-1px); }
    .cal-event:focus-visible { outline: 2px solid var(--brand-orange); outline-offset: 1px; }
    .cal-event-title {
        font-weight: 700;
        font-size: var(--font-size-sm);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cal-event-sub {
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cal-event-time { font-size: var(--font-size-xs); color: var(--text-muted); margin-top: 2px; }
    .cal-event-status { display: inline-block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 2px; }

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
    .cal-now-dot { position: absolute; left: -5px; top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; }

    .no-results { padding: var(--space-7); text-align: center; color: var(--text-muted); font-size: var(--font-size-md); }
    .no-results i { font-size: 2.5rem; display: block; margin-bottom: var(--space-3); color: var(--border-color); }

    /* ---------- Lightbox galeri foto ruangan ---------- */
    .gallery-lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.92);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        animation: fadeIn 0.3s ease;
    }
    .gallery-lightbox.active { display: flex; }
    .gallery-lightbox .lightbox-content { position: relative; max-width: 90%; max-height: 85%; display: flex; flex-direction: column; align-items: center; }
    .gallery-lightbox .lightbox-content img { max-width: 85vw; max-height: 75vh; object-fit: contain; border-radius: var(--radius-sm); box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .gallery-lightbox .close-lightbox { position: absolute; top: -40px; right: -10px; font-size: 2.5rem; color: #fff; background: none; border: none; cursor: pointer; line-height: 1; z-index: 10; }
    .gallery-lightbox .nav-lightbox { position: absolute; top: 50%; transform: translateY(-50%); font-size: 2rem; color: #fff; background: rgba(255,255,255,0.1); border: none; border-radius: 50%; width: 48px; height: 48px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .gallery-lightbox .nav-lightbox:hover { background: rgba(255,255,255,0.2); }
    .gallery-lightbox .nav-lightbox.prev { left: 20px; }
    .gallery-lightbox .nav-lightbox.next { right: 20px; }
    .gallery-lightbox .lightbox-caption { margin-top: 16px; color: #fff; font-size: var(--font-size-sm); background: rgba(0,0,0,0.3); padding: var(--space-2) var(--space-4); border-radius: var(--radius-pill); max-width: 80%; text-align: center; }
    .gallery-lightbox .lightbox-counter { margin-top: 8px; color: rgba(255,255,255,0.6); font-size: var(--font-size-sm); }
    .gallery-lightbox .lightbox-thumbnails { display: flex; gap: 8px; margin-top: 12px; max-width: 80%; overflow-x: auto; padding: 4px; background: rgba(0,0,0,0.2); border-radius: var(--radius-sm); }
    .gallery-lightbox .lightbox-thumbnails img { width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-xs); cursor: pointer; border: 2px solid transparent; }
    .gallery-lightbox .lightbox-thumbnails img.active { border-color: var(--brand-orange); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* ---------- Responsive — ditarget ke elemen yang benar-benar dirender (.cal-*) ---------- */
    @media (max-width: 991.98px) {
        .dashboard-content { padding: var(--space-3); }
        .db-controls { flex-direction: column; align-items: stretch; padding: var(--space-3); }
        .db-controls .filter-spacer { display: none; }
        .db-divider { display: none; }
        .date-navigator { width: 100%; }
        .date-navigator .date-display { flex: 1; }
        .db-filter-row .db-select { flex: 1; }
        .db-toolbar { flex-direction: column; align-items: flex-start; }
        .db-toolbar .actions { width: 100%; }
        .schedule-card .card-header .title .date-info { display: none; }
        .cal-time-col, .cal-time-header { min-width: 56px !important; }
        .cal-room-header, .cal-room-col { min-width: 168px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 48px; height: 48px; }
        .cal-room-name { font-size: var(--font-size-xs); }
        .schedule-card .card-body { max-height: 480px; }
    }
    @media (max-width: 575.98px) {
        .dashboard-content { padding: var(--space-2); }
        .db-quickstats { gap: var(--space-1); }
        .qs-chip { font-size: 11px; padding: 6px 10px; }
        .cal-room-header, .cal-room-col { min-width: 140px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 40px; height: 40px; }
        .cal-event-title, .cal-event-sub { font-size: 11px; }
        .schedule-legend { gap: var(--space-2); }
        .db-toolbar h1 { font-size: var(--font-size-lg); }
    }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .db-controls { animation: fadeUp 0.3s ease forwards; }
    .schedule-card { animation: fadeUp 0.4s ease forwards; }
</style>

<div class="dashboard-content">

    @php
        // Data ini SUDAH dihitung controller sebelumnya tapi tidak pernah ditampilkan.
        // Sekarang dipakai untuk quick-stats strip (bukan logika baru, hanya menampilkan yang sudah ada).
        $totalBookingsToday = 0;
        $totalPending = 0;
        $totalApproved = 0;
        $totalRejected = 0;
        foreach ($bookingSchedule as $roomId => $slots) {
            foreach ($slots as $time => $booking) {
                if ($booking) {
                    $totalBookingsToday++;
                    if ($booking->status == 0) $totalPending++;
                    elseif ($booking->status == 1) $totalApproved++;
                    elseif ($booking->status == 2) $totalRejected++;
                }
            }
        }
        $totalRoomsToday = $allRooms->count();
        $busyRoomIds = $dayBookings->where('status', '!=', 2)->pluck('room_id')->unique();
        $availableRoomsNow = max(0, $totalRoomsToday - $busyRoomIds->count());

        $userName = Auth::user()->full_name ?? Auth::user()->name ?? 'User';

        $selectedRoomValue = (!empty($selectedRoomIds) && !in_array('all', $selectedRoomIds)) ? $selectedRoomIds[0] : '';
    @endphp

    {{-- ========== TOOLBAR: judul halaman + aksi utama (tanpa duplikasi salam topbar) ========== --}}
    <div class="db-toolbar">
        <div>
            <h1>Dashboard</h1>
            <p class="db-meta">
                <span><i class="bi bi-calendar3 me-1"></i>{{ Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                @if($isWeekend)
                    <span class="badge-holiday"><i class="bi bi-moon-stars"></i> Libur akhir pekan</span>
                @endif
            </p>
        </div>
        <div class="actions">
            <a href="{{ route('bookings.create') }}" class="btn-primary-sm">
                <i class="bi bi-plus-lg"></i> Booking
            </a>
            <button type="button" class="btn-outline-sm" onclick="window.location.reload()">
                <i class="bi bi-arrow-repeat"></i> Refresh
            </button>
        </div>
    </div>

    {{-- ========== CONTROL BAR: navigasi tanggal (utama) + filter sekunder ========== --}}
    <div class="db-controls">
        <div class="date-navigator">
            <button type="button" class="btn-today" onclick="resetToToday()">
                <i class="bi bi-arrow-clockwise"></i> Hari Ini
            </button>
            <button type="button" class="date-nav-btn" onclick="shiftDate(-1)" aria-label="Tanggal sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="date-nav-btn" onclick="shiftDate(1)" aria-label="Tanggal berikutnya">
                <i class="bi bi-chevron-right"></i>
            </button>
            <label class="date-display">
                <i class="bi bi-calendar3"></i>
                <input type="date" id="dateSelector" name="date" value="{{ $selectedDate }}" onchange="filterByDate(this.value)" aria-label="Pilih tanggal">
            </label>
        </div>

        <div class="db-divider"></div>

        <div class="db-filter-group">
            <label for="startTimeInput">Jam Kerja</label>
            <div class="db-filter-row">
                <input type="time" id="startTimeInput" value="{{ $startTime }}" onchange="filterByTimeRange()" aria-label="Jam mulai">
                <i class="bi bi-arrow-right"></i>
                <input type="time" id="endTimeInput" value="{{ $endTime }}" onchange="filterByTimeRange()" aria-label="Jam selesai">
            </div>
        </div>

        <div class="db-divider"></div>

        <div class="db-filter-group">
            <label for="roomFilter">Ruangan</label>
            <select id="roomFilter" class="db-select" onchange="filterByRoom(this.value)">
                <option value="">Semua Ruangan</option>
                @foreach($allRooms as $room)
                    <option value="{{ $room->id }}" {{ (string)$selectedRoomValue === (string)$room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="db-filter-group">
            <label for="statusFilterSelect">Status</label>
            <select id="statusFilterSelect" class="db-select" onchange="filterByStatus(this.value)">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="filter-spacer"></div>
    </div>

    {{-- ========== QUICK STATS: ringkasan hari ini (data yang sudah dihitung, kini punya fungsi) ========== --}}
    <div class="db-quickstats">
        <span class="qs-chip"><span class="dot blue"></span> <b>{{ $totalRoomsToday }}</b> ruangan &middot; <b>{{ $availableRoomsNow }}</b> tersedia sekarang</span>
        <span class="qs-chip"><span class="dot green"></span> <b>{{ $totalBookingsToday }}</b> booking hari ini</span>
        @if($totalPending > 0)
            <span class="qs-chip"><span class="dot amber"></span> <b>{{ $totalPending }}</b> menunggu persetujuan</span>
        @endif
        @if($totalRejected > 0)
            <span class="qs-chip"><span class="dot red"></span> <b>{{ $totalRejected }}</b> ditolak</span>
        @endif
    </div>

    {{-- ========== JADWAL RUANGAN ========== --}}
    <div class="schedule-card">
        <div class="card-header">
            <div class="title">
                <i class="bi bi-table"></i> Jadwal Peminjaman Ruangan
                <span class="date-info">{{ Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            <div class="header-right">
                @if($isWeekend)
                    <span><i class="bi bi-lock"></i> Libur</span>
                @endif
                @if($isToday && !$isWeekend && $currentSlot)
                    <button type="button" class="current-badge" onclick="scrollToNow()" title="Klik untuk lompat ke waktu sekarang">
                        <i class="bi bi-dot"></i> Sekarang {{ $currentSlot }}
                    </button>
                @endif
                <span>{{ count($timeSlots) }} slot &middot; {{ $rooms->count() }} ruangan</span>
            </div>
        </div>

        <div class="card-body cal-body" id="scheduleBody">
            @php
                $slotHeight = 64; // px per 30 menit
                $pxPerMin = $slotHeight / 30;
                $slotCount = count($timeSlots);
                $gridHeight = ($slotCount + 1) * $slotHeight;

                $dayStartParts = explode(':', $timeSlots[0] ?? '07:00');
                $dayStartMinutes = ((int) $dayStartParts[0]) * 60 + ((int) $dayStartParts[1]);

                $nowMinutes = Carbon::now()->hour * 60 + Carbon::now()->minute;
                $nowTopPx = ($nowMinutes - $dayStartMinutes) * $pxPerMin;
                $showNowLine = $isToday && !$isWeekend && $nowTopPx >= 0 && $nowTopPx <= $gridHeight;

                $statusColors = [
                    0 => ['bg' => 'rgba(245,158,11,0.10)', 'border' => '#f59e0b', 'text' => '#b45309', 'label' => 'Pending'],
                    1 => ['bg' => 'rgba(16,185,129,0.10)', 'border' => '#10b981', 'text' => '#047857', 'label' => 'Disetujui'],
                    2 => ['bg' => 'rgba(239,68,68,0.08)',  'border' => '#ef4444', 'text' => '#b91c1c', 'label' => 'Ditolak'],
                ];
            @endphp

            @if($slotCount > 0 && $rooms->count() > 0)
                @php $gridCols = "80px repeat({$rooms->count()}, minmax(200px, 1fr))"; @endphp

                <div class="cal-grid-header" style="grid-template-columns: {{ $gridCols }};">
                    <div class="cal-header-cell cal-time-header"></div>
                    @foreach($rooms as $room)
                        @php
                            $photos = $room->photos ?? collect();
                            $hasPhoto = $photos->count() > 0;
                            $firstPhoto = $hasPhoto ? $photos->first()->photo_url : null;
                        @endphp
                        <div class="cal-header-cell cal-room-header">
                            @if($hasPhoto)
                                <img src="{{ $firstPhoto }}" class="cal-room-photo" tabindex="0"
                                     onclick="openGallery({{ $room->id }}, {{ $loop->index }})"
                                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openGallery({{ $room->id }}, {{ $loop->index }});}"
                                     alt="Lihat foto {{ $room->name }}" title="Klik untuk lihat foto" loading="lazy">
                            @else
                                <div class="cal-room-photo cal-room-photo-placeholder"><i class="bi bi-building"></i></div>
                            @endif
                            <div class="cal-room-info">
                                <div class="cal-room-name">{{ $room->name }}</div>
                                <div class="cal-room-capacity"><i class="bi bi-people"></i> Kapasitas {{ $room->capacity }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="cal-grid-body-wrapper">
                    <div class="cal-grid-body" style="grid-template-columns: {{ $gridCols }};">
                        <div class="cal-time-col" style="height: {{ $gridHeight }}px;">
                            @foreach($timeSlots as $timeSlot)
                                <div class="cal-time-label" style="height: {{ $slotHeight }}px;">{{ $timeSlot }}</div>
                            @endforeach
                        </div>

                        @foreach($rooms as $room)
                            <div class="cal-room-col {{ $isWeekend ? 'cal-weekend' : '' }}" style="height: {{ $gridHeight }}px;">
                                @if($isWeekend)
                                    <div class="cal-weekend-overlay"><i class="bi bi-calendar-x"></i> Libur</div>
                                @else
                                    @foreach($timeSlots as $index => $timeSlot)
                                        <div class="cal-slot-hover" role="button" tabindex="0"
                                             style="top: {{ $index * $slotHeight }}px; height: {{ $slotHeight }}px;"
                                             onclick="quickBook({{ $room->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}')"
                                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();quickBook({{ $room->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}');}"
                                             aria-label="Booking {{ $room->name }} jam {{ $timeSlot }}"
                                             title="Klik untuk booking jam {{ $timeSlot }}">
                                            <div class="cal-slot-hover-inner"><i class="bi bi-plus-lg"></i> Booking Ruangan</div>
                                        </div>
                                    @endforeach

                                    @foreach($dayBookings->where('room_id', $room->id) as $booking)
                                        @php
                                            $bStart = Carbon::parse($booking->start_time);
                                            $bEnd = Carbon::parse($booking->end_time);
                                            $bStartMin = $bStart->hour * 60 + $bStart->minute;
                                            $bEndMin = $bEnd->hour * 60 + $bEnd->minute;
                                            $topPx = max(0, ($bStartMin - $dayStartMinutes) * $pxPerMin);
                                            $heightPx = max(28, ($bEndMin - $bStartMin) * $pxPerMin);
                                            $colors = $statusColors[$booking->status] ?? $statusColors[0];
                                        @endphp
                                        <div class="cal-event" role="button" tabindex="0"
                                             onclick="showBookingDetails({{ $booking->id }})"
                                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();showBookingDetails({{ $booking->id }});}"
                                             aria-label="Detail booking {{ $booking->purpose }}, {{ $colors['label'] }}"
                                             title="Klik untuk lihat detail"
                                             style="top: {{ $topPx }}px; height: {{ $heightPx }}px; background: {{ $colors['bg'] }}; border-left-color: {{ $colors['border'] }};">
                                            <div class="cal-event-title" style="color: {{ $colors['text'] }};">
                                                <i class="bi bi-person-workspace"></i> {{ Str::limit($booking->purpose, 28) }}
                                            </div>
                                            <div class="cal-event-sub">{{ $booking->user->full_name ?? 'Unknown' }}</div>
                                            <div class="cal-event-time"><i class="bi bi-clock"></i> {{ $bStart->format('H:i') }} - {{ $bEnd->format('H:i') }}</div>
                                            <span class="cal-event-status" style="color: {{ $colors['text'] }};">{{ $colors['label'] }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($showNowLine)
                        <div class="cal-now-line" id="nowLine" style="top: {{ $nowTopPx }}px;"><span class="cal-now-dot"></span></div>
                    @endif
                </div>
            @else
                <div class="no-results">
                    <i class="bi bi-inbox"></i>
                    <p>Tidak ada jadwal untuk tanggal yang dipilih.</p>
                </div>
            @endif
        </div>

        {{-- ========== LEGENDA — terintegrasi, bukan kartu terpisah ========== --}}
        <div class="schedule-legend">
            <span class="legend-item"><span class="dot available"></span> Tersedia (klik untuk booking)</span>
            <span class="legend-item"><span class="dot approved"></span> Disetujui</span>
            <span class="legend-item"><span class="dot pending"></span> Pending</span>
            <span class="legend-item"><span class="dot rejected"></span> Ditolak</span>
            @if($isToday && !$isWeekend)
                <span class="legend-item"><span class="dot current"></span> Waktu sekarang</span>
            @endif
        </div>
    </div>

</div>

{{-- ========== LIGHTBOX GALERI FOTO ========== --}}
<div class="gallery-lightbox" id="lightbox">
    <div class="lightbox-content">
        <button class="close-lightbox" onclick="closeLightbox()" aria-label="Tutup">&times;</button>
        <button class="nav-lightbox prev" onclick="navigateLightbox(-1)" aria-label="Sebelumnya">&#10094;</button>
        <button class="nav-lightbox next" onclick="navigateLightbox(1)" aria-label="Selanjutnya">&#10095;</button>
        <img id="lightboxImage" src="" alt="Foto Ruangan">
        <div class="lightbox-caption" id="lightboxCaption"></div>
        <div class="lightbox-counter" id="lightboxCounter"></div>
        <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
    </div>
</div>

<script>
    (function() {
        'use strict';

        // ========== GALERI FOTO ==========
        var roomsData = @json($roomsData);
        var currentPhotoIndex = 0;
        var currentPhotos = [];

        window.openGallery = function(roomId) {
            var room = roomsData.find(function(r) { return r.id == roomId; });
            if (!room || !room.photos || room.photos.length === 0) return;

            currentPhotos = room.photos;
            currentPhotoIndex = 0;
            updateLightbox(room.name);
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        function updateLightbox(roomName) {
            var img = document.getElementById('lightboxImage');
            var caption = document.getElementById('lightboxCaption');
            var counter = document.getElementById('lightboxCounter');
            var thumbs = document.getElementById('lightboxThumbnails');

            var photo = currentPhotos[currentPhotoIndex];
            if (photo) {
                img.src = photo;
                img.alt = roomName || 'Foto Ruangan';
            }

            caption.textContent = (roomName || '') + ' - ' + (currentPhotoIndex + 1) + '/' + currentPhotos.length;
            counter.textContent = (currentPhotoIndex + 1) + ' dari ' + currentPhotos.length;

            thumbs.innerHTML = '';
            currentPhotos.forEach(function(p, idx) {
                var thumb = document.createElement('img');
                thumb.src = p;
                thumb.alt = 'Thumbnail ' + (idx + 1);
                thumb.className = idx === currentPhotoIndex ? 'active' : '';
                thumb.onclick = function() { currentPhotoIndex = idx; updateLightbox(roomName); };
                thumbs.appendChild(thumb);
            });

            var activeThumb = thumbs.querySelector('.active');
            if (activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'center' });
        }

        window.closeLightbox = function() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        };

        window.navigateLightbox = function(direction) {
            var newIndex = currentPhotoIndex + direction;
            if (newIndex < 0 || newIndex >= currentPhotos.length) return;
            currentPhotoIndex = newIndex;
            updateLightbox();
        };

        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('lightbox').classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') navigateLightbox(-1);
            else if (e.key === 'ArrowRight') navigateLightbox(1);
        });

        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this || e.target === document.querySelector('.lightbox-content')) closeLightbox();
        });

        // ========== FILTER: TANGGAL ==========
        window.filterByDate = function(selectedDate) {
            const url = new URL(window.location);
            url.searchParams.set('date', selectedDate);
            window.location.href = url.toString();
        };

        window.shiftDate = function(days) {
            const input = document.getElementById('dateSelector');
            let base;
            if (input && input.value) {
                const parts = input.value.split('-');
                base = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            } else {
                base = new Date();
            }
            base.setDate(base.getDate() + days);
            const y = base.getFullYear();
            const m = String(base.getMonth() + 1).padStart(2, '0');
            const d = String(base.getDate()).padStart(2, '0');
            filterByDate(`${y}-${m}-${d}`);
        };

        window.resetToToday = function() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            const today = `${y}-${m}-${d}`;
            const url = new URL(window.location);
            url.searchParams.set('date', today);
            url.searchParams.delete('rooms');
            url.searchParams.delete('status');
            window.location.href = url.toString();
        };

        // ========== FILTER: JAM KERJA ==========
        window.filterByTimeRange = function() {
            const start = document.getElementById('startTimeInput').value;
            const end = document.getElementById('endTimeInput').value;
            const url = new URL(window.location);
            if (start) url.searchParams.set('start_time', start);
            if (end) url.searchParams.set('end_time', end);
            window.location.href = url.toString();
        };

        // ========== FILTER: RUANGAN & STATUS (memakai parameter yang sudah didukung controller) ==========
        window.filterByRoom = function(roomId) {
            const url = new URL(window.location);
            if (roomId) url.searchParams.set('rooms', roomId);
            else url.searchParams.delete('rooms');
            window.location.href = url.toString();
        };

        window.filterByStatus = function(status) {
            const url = new URL(window.location);
            if (status && status !== 'all') url.searchParams.set('status', status);
            else url.searchParams.delete('status');
            window.location.href = url.toString();
        };

        // ========== AKSI GRID ==========
        window.showBookingDetails = function(bookingId) {
            window.location.href = '/bookings/' + bookingId;
        };

        window.quickBook = function(roomId, date, time) {
            const url = new URL('{{ route("bookings.create") }}');
            url.searchParams.set('room', roomId);
            url.searchParams.set('date', date);
            url.searchParams.set('start_time', time);
            window.location.href = url.toString();
        };

        window.scrollToNow = function() {
            const line = document.getElementById('nowLine');
            const container = document.getElementById('scheduleBody');
            if (line && container) {
                container.scrollTop = Math.max(0, line.offsetTop - 80);
            }
        };

        // Auto-scroll ke waktu sekarang saat halaman dimuat (tanpa jam berdetik yang mengganggu)
        const nowLine = document.getElementById('nowLine');
        if (nowLine) {
            setTimeout(function() { window.scrollToNow(); }, 300);
        }

        if (typeof bootstrap !== 'undefined') {
            const initTooltips = function() {
                document.querySelectorAll('[title]').forEach(el => new bootstrap.Tooltip(el, { placement: 'top', delay: { show: 300, hide: 100 } }));
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTooltips);
            } else {
                initTooltips();
            }
        }

        document.addEventListener('keydown', function(e) {
            const input = document.getElementById('dateSelector');
            if (!input || !e.ctrlKey) return;
            if (e.key === 'ArrowLeft') { e.preventDefault(); shiftDate(-1); }
            else if (e.key === 'ArrowRight') { e.preventDefault(); shiftDate(1); }
        });

    })();
</script>
@endsection