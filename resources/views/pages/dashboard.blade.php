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
        padding: var(--space-6) var(--space-7);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }

    /* ---------- Toolbar (judul halaman + aksi utama) ---------- */
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
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.02em;
    }
    .db-toolbar .db-meta {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-2) 0 0 0;
        display: flex;
        align-items: center;
        gap: var(--space-3);
        flex-wrap: wrap;
    }
    .db-toolbar .actions {
        display: flex;
        gap: var(--space-3);
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    .badge-holiday {
        font-size: var(--font-size-xs);
        font-weight: 600;
        padding: 3px 12px;
        border-radius: var(--radius-pill);
        background: rgba(245, 158, 11, 0.12);
        color: #b45309;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-primary-sm {
        height: 42px;
        padding: 0 var(--space-5);
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
        gap: var(--space-2);
        text-decoration: none;
        box-shadow: 0 2px 10px rgba(249, 115, 22, 0.16);
    }
    .btn-primary-sm:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.2);
        color: var(--text-inverse);
    }
    .btn-outline-sm {
        height: 42px;
        padding: 0 var(--space-5);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        text-decoration: none;
    }
    .btn-outline-sm:hover {
        border-color: var(--brand-orange);
        color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.04);
    }
    .btn-outline-sm.is-loading i { animation: spin 0.7s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .btn-primary-sm:focus-visible,
    .btn-outline-sm:focus-visible,
    .date-nav-btn:focus-visible,
    .btn-today:focus-visible,
    .db-select:focus-visible,
    .time-trigger:focus-visible {
        outline: 2px solid var(--brand-orange);
        outline-offset: 2px;
    }

    /* ============================================================
       STAT CARDS
       ============================================================ */
    .db-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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
    .stat-card .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .stat-card .stat-icon.blue   { background: rgba(59,130,246,0.1);  color: var(--brand-blue); }
    .stat-card .stat-icon.green  { background: rgba(16,185,129,0.1); color: var(--brand-green-dark); }
    .stat-card .stat-icon.amber  { background: rgba(245,158,11,0.12); color: #b45309; }
    .stat-card .stat-icon.red    { background: rgba(239,68,68,0.1);  color: #b91c1c; }
    .stat-card .stat-body { min-width: 0; }
    .stat-card .stat-value {
        font-size: var(--font-size-2xl);
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .stat-card .stat-value small { font-size: var(--font-size-sm); font-weight: 500; color: var(--text-muted); }
    .stat-card .stat-label {
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
        font-weight: 600;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ============================================================
       CONTROL BAR (navigasi tanggal + filter)
       ============================================================ */
    .db-controls {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--space-4);
        margin-bottom: var(--space-6);
        background: var(--bg-card);
        padding: var(--space-4) var(--space-5);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
    }

    /* Grup navigasi tanggal — satu unit visual (today | ‹ tanggal › | date-picker) */
    .date-navigator {
        display: flex;
        align-items: stretch;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        overflow: hidden;
        height: 42px;
    }
    .date-navigator > * { border: none; border-radius: 0; }
    .date-navigator .btn-today {
        height: 42px;
        padding: 0 var(--space-4);
        background: var(--brand-gradient);
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
    .date-navigator .btn-today:hover { filter: brightness(1.05); }
    .date-nav-btn {
        width: 42px;
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
    }

    .db-divider {
        width: 1px;
        align-self: stretch;
        background: var(--border-color-light);
    }

    /* Filter sekunder: label kecil supaya jelas beda prioritas dari navigasi tanggal */
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
        height: 42px;
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        min-width: 170px;
        cursor: pointer;
    }

    .db-controls .filter-spacer { flex: 1; }

    /* ---------- Material-style Time Range Picker ---------- */
    .time-trigger {
        height: 42px;
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

    .time-picker-popover {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        z-index: 200;
        width: 320px;
        background: var(--bg-card);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-dropdown);
        border: 1px solid var(--border-color-light);
        padding: var(--space-5);
        display: none;
    }
    .time-picker-popover.open { display: block; animation: fadeUp 0.15s ease forwards; }
    .time-picker-popover .tp-title {
        font-size: var(--font-size-xs);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .time-picker-popover .tp-subtitle {
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        margin-bottom: var(--space-4);
    }
    .tp-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-3);
        margin-bottom: var(--space-4);
    }
    .tp-col-label {
        font-size: var(--font-size-xs);
        font-weight: 700;
        color: var(--text-secondary);
        margin-bottom: var(--space-2);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .tp-col-label i { color: var(--brand-orange); }
    .tp-list {
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-sm);
        background: var(--bg-body);
    }
    .tp-option {
        padding: var(--space-2) var(--space-3);
        font-size: var(--font-size-sm);
        font-weight: 500;
        color: var(--text-primary);
        cursor: pointer;
        text-align: center;
        transition: background var(--transition-fast), color var(--transition-fast);
        border-bottom: 1px solid var(--border-color-light);
    }
    .tp-option:last-child { border-bottom: none; }
    .tp-option:hover:not(.disabled) { background: rgba(249,115,22,0.08); }
    .tp-option.selected {
        background: var(--brand-gradient);
        color: var(--text-inverse);
        font-weight: 700;
    }
    .tp-option.disabled {
        color: var(--text-muted);
        opacity: 0.4;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .tp-error {
        font-size: var(--font-size-xs);
        color: #b91c1c;
        background: rgba(239,68,68,0.08);
        border-radius: var(--radius-xs);
        padding: var(--space-2) var(--space-3);
        margin-bottom: var(--space-3);
        display: none;
        align-items: center;
        gap: 6px;
    }
    .tp-error.show { display: flex; }
    .tp-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: var(--space-2);
        border-top: 1px solid var(--border-color-light);
        padding-top: var(--space-4);
    }
    .tp-reset {
        background: none;
        border: none;
        font-size: var(--font-size-xs);
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        padding: var(--space-2) var(--space-2);
    }
    .tp-reset:hover { color: var(--brand-orange-dark); }
    .tp-actions { display: flex; gap: var(--space-2); }
    .tp-btn {
        height: 38px;
        padding: 0 var(--space-4);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
        font-weight: 600;
        cursor: pointer;
        border: none;
    }
    .tp-btn-cancel { background: transparent; color: var(--text-secondary); }
    .tp-btn-cancel:hover { background: var(--bg-hover); }
    .tp-btn-apply { background: var(--brand-gradient); color: var(--text-inverse); }
    .tp-btn-apply:hover { filter: brightness(1.05); }
    .tp-overlay { position: fixed; inset: 0; z-index: 199; display: none; }
    .tp-overlay.open { display: block; }

    /* ---------- Quick stats: memberi fungsi pada data yang sudah dihitung ---------- */
    .db-quickstats { display: none; }

    /* ---------- Kartu jadwal ---------- */
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
        padding: 5px var(--space-3);
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }
    .current-badge:hover { filter: brightness(0.95); }

    /* Legenda terintegrasi di footer kartu */
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
    .schedule-legend .dot.pending { background: #f59e0b; }
    .schedule-legend .dot.approved { background: #10b981; }
    .schedule-legend .dot.rejected { background: #ef4444; }
    .schedule-legend .dot.current { background: var(--brand-orange); box-shadow: 0 0 0 3px rgba(249,115,22,0.2); }

    .schedule-card .card-body {
        padding: 0;
        overflow: auto;
        position: relative;
        max-height: 640px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: var(--text-muted) transparent;
    }
    .schedule-card .card-body::-webkit-scrollbar { width: 6px; height: 6px; }
    .schedule-card .card-body::-webkit-scrollbar-thumb { background: var(--text-muted); border-radius: var(--radius-pill); }

    /* ---------- Grid kalender ---------- */
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
    .cal-room-header {
        display: flex;
        align-items: center;
        gap: 10px;
        border-right: 1px solid var(--border-color);
        background: var(--bg-body);
    }
    .cal-room-photo {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all var(--transition-fast);
        flex-shrink: 0;
    }
    .cal-room-photo:hover { transform: scale(1.06); border-color: var(--brand-orange); }
    .cal-room-photo:focus-visible { outline: 2px solid var(--brand-orange); outline-offset: 2px; }
    .cal-room-photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 1.5rem;
        background: var(--bg-card);
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
    .cal-room-capacity { font-size: var(--font-size-xs); color: var(--text-muted); margin-top: 2px; }

    .cal-grid-body-wrapper { position: relative; min-width: fit-content; }
    .cal-grid-body { display: grid; }
    .cal-time-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background: var(--bg-card);
        border-right: 2px solid var(--border-color);
    }
    .cal-time-label {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 6px;
        font-size: var(--font-size-xs);
        font-weight: 700;
        color: var(--brand-orange-dark);
        border-bottom: 1px solid var(--border-color-light);
        box-sizing: border-box;
    }
    .cal-time-label.hour-mark { border-bottom-color: var(--border-color); }
    .cal-room-col {
        position: relative;
        border-right: 1px solid var(--border-color);
        background-image: repeating-linear-gradient(
            to bottom,
            var(--border-color) 0px, var(--border-color) 1px, transparent 1px, transparent 64px
        ), repeating-linear-gradient(
            to bottom,
            transparent 0px, transparent 63px, var(--border-color-light) 63px, var(--border-color-light) 64px, transparent 64px, transparent 127px, var(--border-color-light) 127px, var(--border-color-light) 128px
        );
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
        border: 1.5px dashed var(--brand-green);
        border-radius: var(--radius-sm);
        background: rgba(16, 185, 129, 0.08);
        color: var(--brand-green-dark);
        font-size: var(--font-size-xs);
        font-weight: 700;
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
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        transition: box-shadow var(--transition-fast), transform var(--transition-fast);
    }
    .cal-event:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.14); transform: translateY(-1px); }
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
    .cal-now-dot { position: absolute; left: -5px; top: -5px; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.2); }

    .no-results { padding: var(--space-9); text-align: center; color: var(--text-muted); font-size: var(--font-size-md); }
    .no-results i { font-size: 2.75rem; display: block; margin-bottom: var(--space-3); color: var(--border-color); }

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

    /* ---------- Responsive ---------- */
    @media (max-width: 1199.98px) {
        .db-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 991.98px) {
        .dashboard-content { padding: var(--space-4); }
        .db-controls { flex-direction: column; align-items: stretch; padding: var(--space-4); }
        .db-controls .filter-spacer { display: none; }
        .db-divider { display: none; }
        .date-navigator { width: 100%; }
        .date-navigator .date-display { flex: 1; }
        .db-filter-group { width: 100%; }
        .db-filter-group .db-select,
        .db-filter-group .time-trigger { width: 100%; }
        .time-picker-popover { left: 0; right: 0; width: auto; }
        .db-toolbar { flex-direction: column; align-items: stretch; }
        .db-toolbar .actions { width: 100%; }
        .db-toolbar .actions .btn-primary-sm,
        .db-toolbar .actions .btn-outline-sm { flex: 1; justify-content: center; }
        .schedule-card .card-header .title .date-info { display: none; }
        .cal-time-col, .cal-time-header { min-width: 60px !important; }
        .cal-room-header, .cal-room-col { min-width: 180px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 48px; height: 48px; }
        .cal-room-name { font-size: var(--font-size-xs); }
        .schedule-card .card-body { max-height: 500px; }
    }
    @media (max-width: 575.98px) {
        .dashboard-content { padding: var(--space-3); }
        .db-stats-grid { grid-template-columns: repeat(2, 1fr); gap: var(--space-3); }
        .stat-card { padding: var(--space-4); gap: var(--space-3); }
        .stat-card .stat-icon { width: 42px; height: 42px; font-size: 1.1rem; }
        .stat-card .stat-value { font-size: var(--font-size-lg); }
        .cal-room-header, .cal-room-col { min-width: 150px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 40px; height: 40px; }
        .cal-event-title, .cal-event-sub { font-size: 11px; }
        .schedule-legend { gap: var(--space-3); }
        .db-toolbar h1 { font-size: var(--font-size-xl); }
        .schedule-card .card-header { flex-direction: column; align-items: flex-start; }
    }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .db-controls { animation: fadeUp 0.3s ease forwards; }
    .schedule-card { animation: fadeUp 0.4s ease forwards; }
</style>

<div class="dashboard-content">

    @php
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

    {{-- ========== TOOLBAR ========== --}}
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
                <i class="bi bi-plus-lg"></i> Booking Baru
            </a>
            <button type="button" class="btn-outline-sm" id="refreshBtn" onclick="doRefresh(this)">
                <i class="bi bi-arrow-repeat"></i> Refresh
            </button>
        </div>
    </div>

    {{-- ========== STAT CARDS ========== --}}
    <div class="db-stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-building"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ $availableRoomsNow }}<small>/ {{ $totalRoomsToday }}</small></div>
                <div class="stat-label">Ruangan tersedia sekarang</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ $totalBookingsToday }}</div>
                <div class="stat-label">Booking hari ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ $totalPending }}</div>
                <div class="stat-label">Menunggu persetujuan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ $totalRejected }}</div>
                <div class="stat-label">Ditolak hari ini</div>
            </div>
        </div>
    </div>

    {{-- ========== CONTROL BAR: navigasi tanggal + filter ========== --}}
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

        {{-- ===== Material-style Jam Kerja Time Range Picker ===== --}}
        <div class="db-filter-group" id="timePickerWrapper">
            <label>Jam Kerja</label>
            <button type="button" class="time-trigger" id="timeTrigger" onclick="toggleTimePicker()" aria-haspopup="true" aria-expanded="false">
                <i class="bi bi-clock"></i>
                <span id="timeTriggerLabel">{{ $startTime }} – {{ $endTime }}</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>

            <div class="time-picker-popover" id="timePickerPopover">
                <div class="tp-title">Pilih Jam Operasional</div>
                <div class="tp-subtitle">Rentang 07:00–16:00, interval 30 menit</div>

                <div class="tp-error" id="tpError"><i class="bi bi-exclamation-triangle"></i> <span>Jam mulai harus lebih awal dari jam selesai.</span></div>

                <div class="tp-columns">
                    <div>
                        <div class="tp-col-label"><i class="bi bi-play-fill"></i> Mulai</div>
                        <div class="tp-list" id="tpStartList"></div>
                    </div>
                    <div>
                        <div class="tp-col-label"><i class="bi bi-stop-fill"></i> Selesai</div>
                        <div class="tp-list" id="tpEndList"></div>
                    </div>
                </div>

                <div class="tp-footer">
                    <button type="button" class="tp-reset" onclick="resetTimePicker()">Reset ke 07:00–16:00</button>
                    <div class="tp-actions">
                        <button type="button" class="tp-btn tp-btn-cancel" onclick="closeTimePicker(true)">Batal</button>
                        <button type="button" class="tp-btn tp-btn-apply" onclick="applyTimePicker()">Terapkan</button>
                    </div>
                </div>
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
                                     onclick="openGallery({{ $room->id }})"
                                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openGallery({{ $room->id }});}"
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
                                @php $isHour = Str::endsWith($timeSlot, ':00'); @endphp
                                <div class="cal-time-label {{ $isHour ? 'hour-mark' : '' }}" style="height: {{ $slotHeight }}px;">{{ $timeSlot }}</div>
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

        {{-- ========== LEGENDA ========== --}}
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
            var lb = document.getElementById('lightbox');
            if (!lb || !lb.classList.contains('active')) return;
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

        // ========== FILTER: JAM KERJA — Material-style Time Range Picker ==========
        const WORK_START_MIN = 7 * 60;   // 07:00
        const WORK_END_MIN   = 16 * 60;  // 16:00
        const STEP_MIN       = 30;

        function pad(n) { return String(n).padStart(2, '0'); }
        function toLabel(mins) { return pad(Math.floor(mins / 60)) + ':' + pad(mins % 60); }
        function toMinutes(label) {
            const parts = label.split(':');
            return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
        }

        function buildSlots() {
            const slots = [];
            for (let m = WORK_START_MIN; m <= WORK_END_MIN; m += STEP_MIN) slots.push(m);
            return slots;
        }
        const ALL_SLOTS = buildSlots();

        let tpStart = toMinutes('{{ $startTime }}');
        let tpEnd = toMinutes('{{ $endTime }}');
        if (isNaN(tpStart) || tpStart < WORK_START_MIN || tpStart > WORK_END_MIN) tpStart = WORK_START_MIN;
        if (isNaN(tpEnd) || tpEnd < WORK_START_MIN || tpEnd > WORK_END_MIN) tpEnd = WORK_END_MIN;
        if (tpStart >= tpEnd) { tpStart = WORK_START_MIN; tpEnd = WORK_END_MIN; }

        function renderTimeLists() {
            const startList = document.getElementById('tpStartList');
            const endList = document.getElementById('tpEndList');
            startList.innerHTML = '';
            endList.innerHTML = '';

            ALL_SLOTS.forEach(function(m) {
                // Mulai: harus lebih kecil dari waktu selesai saat ini
                const startOpt = document.createElement('div');
                startOpt.className = 'tp-option' + (m === tpStart ? ' selected' : '') + (m >= tpEnd ? ' disabled' : '');
                startOpt.textContent = toLabel(m);
                startOpt.setAttribute('role', 'option');
                if (m < tpEnd) {
                    startOpt.addEventListener('click', function() { tpStart = m; renderTimeLists(); });
                }
                startList.appendChild(startOpt);

                // Selesai: harus lebih besar dari waktu mulai saat ini
                const endOpt = document.createElement('div');
                endOpt.className = 'tp-option' + (m === tpEnd ? ' selected' : '') + (m <= tpStart ? ' disabled' : '');
                endOpt.textContent = toLabel(m);
                endOpt.setAttribute('role', 'option');
                if (m > tpStart) {
                    endOpt.addEventListener('click', function() { tpEnd = m; renderTimeLists(); });
                }
                endList.appendChild(endOpt);
            });

            document.getElementById('tpError').classList.remove('show');

            // Scroll ke item terpilih
            const selStart = startList.querySelector('.selected');
            const selEnd = endList.querySelector('.selected');
            if (selStart) selStart.scrollIntoView({ block: 'center' });
            if (selEnd) selEnd.scrollIntoView({ block: 'center' });
        }

        window.toggleTimePicker = function() {
            const popover = document.getElementById('timePickerPopover');
            const trigger = document.getElementById('timeTrigger');
            const isOpen = popover.classList.contains('open');
            if (isOpen) {
                closeTimePicker(true);
            } else {
                renderTimeLists();
                popover.classList.add('open');
                trigger.setAttribute('aria-expanded', 'true');
            }
        };

        window.closeTimePicker = function(revert) {
            const popover = document.getElementById('timePickerPopover');
            popover.classList.remove('open');
            document.getElementById('timeTrigger').setAttribute('aria-expanded', 'false');
            if (revert) {
                // Kembalikan pilihan ke nilai aktif (dari server) jika dibatalkan
                tpStart = toMinutes(document.getElementById('timeTriggerLabel').textContent.split(' – ')[0]);
                tpEnd = toMinutes(document.getElementById('timeTriggerLabel').textContent.split(' – ')[1]);
            }
        };

        window.resetTimePicker = function() {
            tpStart = WORK_START_MIN;
            tpEnd = WORK_END_MIN;
            renderTimeLists();
        };

        window.applyTimePicker = function() {
            if (tpStart >= tpEnd) {
                const err = document.getElementById('tpError');
                err.classList.add('show');
                return;
            }
            const startLabel = toLabel(tpStart);
            const endLabel = toLabel(tpEnd);
            document.getElementById('timeTriggerLabel').textContent = startLabel + ' – ' + endLabel;
            document.getElementById('timePickerPopover').classList.remove('open');

            const url = new URL(window.location);
            url.searchParams.set('start_time', startLabel);
            url.searchParams.set('end_time', endLabel);
            window.location.href = url.toString();
        };

        // Tutup popover saat klik di luar area, atau tekan Escape
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('timePickerWrapper');
            const popover = document.getElementById('timePickerPopover');
            if (popover.classList.contains('open') && wrapper && !wrapper.contains(e.target)) {
                closeTimePicker(true);
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const popover = document.getElementById('timePickerPopover');
                if (popover.classList.contains('open')) closeTimePicker(true);
            }
        });

        // ========== FILTER: RUANGAN & STATUS ==========
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

        // ========== REFRESH (mempertahankan filter aktif) ==========
        window.doRefresh = function(btn) {
            if (btn) btn.classList.add('is-loading');
            window.location.reload();
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

        // Auto-scroll ke waktu sekarang saat halaman dimuat
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