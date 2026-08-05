@php
    use Carbon\Carbon;
@endphp

@extends('templates.template')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Jadwal peminjaman ruangan hari ini')

@section('content')
<style>
    /* =====================================================================
       DASHBOARD — layout dasar
       ===================================================================== */
    .dashboard-content {
        padding: var(--space-6) var(--space-7);
        max-width: 1800px; 
        margin: 0 auto;
        flex: 1;
        width: 100%;
        padding-bottom: 100px;
    }

    /* Toolbar */
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
        margin: var(--space-2) 0 0;
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
    .badge-live-time {
        font-size: var(--font-size-xs);
        font-weight: 700;
        padding: 3px 10px;
        border-radius: var(--radius-pill);
        background: rgba(59, 130, 246, 0.1);
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-variant-numeric: tabular-nums;
    }
    .pulse-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #1d4ed8;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(29, 78, 216, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(29, 78, 216, 0); }
        100% { box-shadow: 0 0 0 0 rgba(29, 78, 216, 0); }
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

    /* Stat cards */
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
        width: 52px; height: 52px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .stat-card .stat-icon.blue   { background: rgba(59,130,246,0.1);  color: var(--brand-blue); }
    .stat-card .stat-icon.green  { background: rgba(16,185,129,0.1);  color: var(--brand-green-dark); }
    .stat-card .stat-icon.amber  { background: rgba(245,158,11,0.12); color: #b45309; }
    .stat-card .stat-icon.red    { background: rgba(239,68,68,0.1);   color: #b91c1c; }
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

    /* Control bar */
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

    /* Time trigger button */
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

    /* =====================================================================
       MATERIAL TIME PICKER — dialog modal (z-index sangat tinggi)
       ===================================================================== */
    .mtp-overlay {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.32);
        animation: mtpFadeIn 0.18s ease;
        padding: 16px;
    }
    .mtp-overlay.open { display: flex; }
    /* DIPERBAIKI: Lebar modal dikembalikan ke 400px agar proporsional */
    .mtp-dialog {
        background: var(--bg-card);
        border-radius: 16px;
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
        width: 400px; 
        max-width: 100%; 
        max-height: 92vh;
        overflow-y: auto;
        animation: mtpSlideUp 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes mtpFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes mtpSlideUp { from { opacity: 0; transform: translateY(24px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }

    .mtp-header { padding: 20px 24px 6px; }
    .mtp-title {
        font-size: 13px;
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
        gap: 24px;
        padding: 12px 24px 8px;
    }
    .mtp-field {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        padding: 8px 14px;
        border-radius: 12px;
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
    /* DIPERBAIKI: Font digital dikembalikan ke 42px agar tidak kebesaran */
    .mtp-digital {
        font-size: 42px; 
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
        padding: 0 3px;
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
        padding: 7px 12px;
        margin: 4px 24px 0;
    }
    .mtp-error.show { display: flex; }

    .mtp-clock-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 16px 24px 8px;
    }
    /* DIPERBAIKI: Ukuran jam dikembalikan ke 280px */
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
    /* DIPERBAIKI: Ukuran angka dikembalikan ke proporsi semula */
    .mtp-num {
        position: absolute;
        width: clamp(40px, 12vw, 44px);
        height: clamp(40px, 12vw, 44px);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: clamp(14px, 4vw, 16px); 
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
        padding: 4px 12px;
        border-radius: 20px;
    }

    .mtp-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px 16px;
    }
    .mtp-icon-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 18px;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: all var(--transition-fast);
    }
    .mtp-icon-btn:hover { background: var(--bg-hover); color: var(--brand-orange); }
    .mtp-actions { display: flex; gap: 8px; }
    .mtp-btn {
        height: 36px;
        padding: 0 18px;
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

    /* =====================================================================
       Kartu jadwal
       ===================================================================== */
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
    .schedule-legend .dot.pending   { background: #f59e0b; }
    .schedule-legend .dot.approved  { background: #10b981; }
    .schedule-legend .dot.rejected  { background: #ef4444; }
    .schedule-legend .dot.current   { background: var(--brand-orange); box-shadow: 0 0 0 3px rgba(249,115,22,0.2); }

    .schedule-card .card-body {
        padding: 0;
        overflow: auto;
        position: relative;
        max-height: 640px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: var(--text-muted) transparent;
        -webkit-overflow-scrolling: touch;
    }
    .schedule-card .card-body::-webkit-scrollbar { width: 6px; height: 6px; }
    .schedule-card .card-body::-webkit-scrollbar-thumb { background: var(--text-muted); border-radius: var(--radius-pill); }

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
        width: 60px; height: 60px;
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
        display: flex; align-items: center; justify-content: center;
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
        ),
        repeating-linear-gradient(
            to bottom,
            transparent 0px, transparent 63px, var(--border-color-light) 63px, var(--border-color-light) 64px, transparent 64px, transparent 127px, var(--border-color-light) 127px, var(--border-color-light) 128px
        );
    }
    .cal-slot-hover {
        position: absolute;
        left: 0; right: 0;
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
        left: 6px; right: 6px;
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

    /* Lightbox galeri foto ruangan */
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

    /* Loading indicator untuk AJAX */
    .db-ajax-loading .schedule-card,
    .db-ajax-loading .db-stats-grid { opacity: 0.55; pointer-events: none; transition: opacity 0.15s; }

    /* Mobile Bottom Action Bar */
    .mobile-action-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--bg-card);
        padding: 10px 16px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        display: none;
        z-index: 1020;
        border-top: 1px solid var(--border-color-light);
    }
    .btn-book-mobile {
        flex: 1;
        height: 50px;
        font-size: 16px;
        border-radius: 12px;
        font-weight: 600;
        background: var(--brand-gradient);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 1199.98px) {
        .db-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 991.98px) {
        .dashboard-content { padding: var(--space-4); padding-bottom: 90px; }
        .db-controls { flex-direction: column; align-items: stretch; padding: var(--space-4); }
        .db-controls .filter-spacer { display: none; }
        .db-divider { display: none; }
        .date-navigator { width: 100%; }
        .date-navigator .date-display { flex: 1; }
        .db-filter-group { width: 100%; }
        .db-filter-group .db-select,
        .db-filter-group .time-trigger { width: 100%; }
        .db-toolbar { flex-direction: column; align-items: stretch; }
        .db-toolbar .actions { display: none; }
        .db-toolbar .actions .btn-primary-sm,
        .db-toolbar .actions .btn-outline-sm { flex: 1; justify-content: center; }
        .schedule-card .card-header .title .date-info { display: none; }
        .cal-time-col, .cal-time-header { min-width: 60px !important; }
        .cal-room-header, .cal-room-col { min-width: 180px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 48px; height: 48px; }
        .cal-room-name { font-size: var(--font-size-xs); }
        .schedule-card .card-body { max-height: 500px; }
        .mobile-action-bar { display: flex; }
    }
    @media (max-width: 575.98px) {
        .dashboard-content { padding: var(--space-3); padding-bottom: 90px; }
        .db-stats-grid { grid-template-columns: 1fr 1fr; gap: var(--space-3); }
        .stat-card { padding: var(--space-4); gap: var(--space-3); }
        .stat-card .stat-icon { width: 42px; height: 42px; font-size: 1.1rem; }
        .stat-card .stat-value { font-size: var(--font-size-lg); }
        .cal-room-header, .cal-room-col { min-width: 150px !important; }
        .cal-room-photo, .cal-room-photo-placeholder { width: 40px; height: 40px; }
        .cal-event-title, .cal-event-sub { font-size: 11px; }
        .schedule-legend { gap: var(--space-3); }
        .db-toolbar h1 { font-size: var(--font-size-xl); }
        .schedule-card .card-header { flex-direction: column; align-items: flex-start; }
        
        /* Responsive Time Picker for Mobile */
        .mtp-dialog { width: 95vw; }
        .mtp-header { padding: 16px 20px 4px; }
        .mtp-time-display { gap: 16px; padding: 8px 20px 4px; }
        .mtp-digital { font-size: 36px; }
        .mtp-field { padding: 8px 12px; }
        .mtp-clock { width: 240px; height: 240px; }
        .mtp-footer { padding: 12px 20px 20px; }
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
                <span id="liveDateTime"><i class="bi bi-calendar3 me-1"></i> <span id="liveDate">Memuat...</span></span>
                <span class="badge-live-time"><span class="pulse-dot"></span> <span id="liveTime">00:00:00</span></span>
                @if($isWeekend)
                    <span class="badge-holiday"><i class="bi bi-moon-stars"></i> Libur akhir pekan</span>
                @endif
                @if($isToday && !$isWeekend)
                    <span class="badge-holiday" style="background: rgba(16,185,129,0.12); color: #047857;"><i class="bi bi-clock"></i> Hari ini</span>
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
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
            <div class="stat-body">
                <div class="stat-value" id="statRejected">{{ $totalRejected }}</div>
                <div class="stat-label">Ditolak hari ini</div>
            </div>
        </div>
    </div>

    {{-- ========== CONTROL BAR ========== --}}
    <div class="db-controls">
        <div class="date-navigator">
            <button type="button" class="btn-today" onclick="resetToToday()">
                <i class="bi bi-arrow-clockwise"></i> Hari Ini
            </button>
            <button type="button" class="date-nav-btn" onclick="shiftDate(-1)" aria-label="Tanggal sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>
            <label class="date-display">
                <i class="bi bi-calendar3"></i>
                <input type="date" id="dateSelector" name="date" value="{{ $selectedDate }}" onchange="filterByDate(this.value)" aria-label="Pilih tanggal">
            </label>
            <button type="button" class="date-nav-btn" onclick="shiftDate(1)" aria-label="Tanggal berikutnya">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        <div class="db-divider"></div>

        <div class="db-filter-group" id="timePickerWrapper">
            <label>Jam Kerja</label>
            <button type="button" class="time-trigger" id="timeTrigger" onclick="openTimePicker()" aria-haspopup="dialog" aria-expanded="false">
                <i class="bi bi-clock"></i>
                <span id="timeTriggerLabel">{{ $startTime }} – {{ $endTime }}</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
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
                <span>{{ count($timeSlots) }} slot · {{ $rooms->count() }} ruangan</span>
            </div>
        </div>

        <div class="card-body cal-body" id="scheduleBody">
            @php
                $slotHeight = 64;
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

<div class="mobile-action-bar">
    <a href="{{ route('bookings.create') }}" class="btn-book-mobile">
        <i class="bi bi-plus-lg"></i> Booking Baru
    </a>
</div>

<script type="application/json" id="roomsDataJson">@json($roomsData)</script>

{{-- ========== MATERIAL TIME PICKER DIALOG ========== --}}
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
                    <span class="mtp-unit" id="mtpEndHour" onclick="event.stopPropagation();selectUnit('end', 'hour')">16</span><span class="mtp-colon">:</span><span class="mtp-unit" id="mtpEndMin" onclick="event.stopPropagation();selectUnit('end', 'minute')">00</span>
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

{{-- ========== LIGHTBOX ========== --}}
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
    (function () {
        'use strict';

        // ===================================================================
        // REAL-TIME DATE & TIME
        // ===================================================================
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

        // ===================================================================
        // DATA GALERI
        // ===================================================================
        var roomsData = [];
        try {
            roomsData = JSON.parse(document.getElementById('roomsDataJson').textContent);
        } catch (e) { roomsData = []; }
        var currentPhotoIndex = 0;
        var currentPhotos = [];

        window.openGallery = function (roomId) {
            var room = roomsData.find(function (r) { return r.id == roomId; });
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
            if (photo) { img.src = photo; img.alt = roomName || 'Foto Ruangan'; }
            caption.textContent = (roomName || '') + ' - ' + (currentPhotoIndex + 1) + '/' + currentPhotos.length;
            counter.textContent = (currentPhotoIndex + 1) + ' dari ' + currentPhotos.length;
            thumbs.innerHTML = '';
            currentPhotos.forEach(function (p, idx) {
                var thumb = document.createElement('img');
                thumb.src = p; thumb.alt = 'Thumbnail ' + (idx + 1);
                thumb.className = idx === currentPhotoIndex ? 'active' : '';
                thumb.onclick = function () { currentPhotoIndex = idx; updateLightbox(roomName); };
                thumbs.appendChild(thumb);
            });
            var activeThumb = thumbs.querySelector('.active');
            if (activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'center' });
        }

        window.closeLightbox = function () {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        };

        window.navigateLightbox = function (direction) {
            var newIndex = currentPhotoIndex + direction;
            if (newIndex < 0 || newIndex >= currentPhotos.length) return;
            currentPhotoIndex = newIndex;
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
            if (e.target === this || e.target === document.querySelector('.lightbox-content')) closeLightbox();
        });

        // ===================================================================
        // AJAX LOADER — sinkronisasi tanpa reload
        // ===================================================================
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

            var refreshBtn = document.getElementById('refreshBtn');
            if (refreshBtn) refreshBtn.classList.add('is-loading');
            document.body.classList.add('db-ajax-loading');
            document.body.style.cursor = 'wait';

            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
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

                    initTooltips();
                    var nowLine = document.getElementById('nowLine');
                    if (nowLine) setTimeout(scrollToNow, 300);
                })
                .catch(function (err) {
                    console.error('Dashboard load error:', err);
                    window.location.href = url.toString();
                })
                .finally(function () {
                    isLoading = false;
                    if (refreshBtn) refreshBtn.classList.remove('is-loading');
                    document.body.classList.remove('db-ajax-loading');
                    document.body.style.cursor = '';
                });
        }

        window.filterByDate = function (selectedDate) {
            var params = getCurrentParams();
            params.date = selectedDate;
            loadDashboard(params);
        };

        window.shiftDate = function (days) {
            var input = document.getElementById('dateSelector');
            var base;
            if (input && input.value) {
                var parts = input.value.split('-');
                base = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            } else { base = new Date(); }
            base.setDate(base.getDate() + days);
            var y = base.getFullYear();
            var m = String(base.getMonth() + 1).padStart(2, '0');
            var d = String(base.getDate()).padStart(2, '0');
            filterByDate(y + '-' + m + '-' + d);
        };

        window.resetToToday = function () {
            var now = new Date();
            var y = now.getFullYear();
            var m = String(now.getMonth() + 1).padStart(2, '0');
            var d = String(now.getDate()).padStart(2, '0');
            var params = getCurrentParams();
            params.date = y + '-' + m + '-' + d;
            delete params.rooms;
            delete params.status;
            loadDashboard(params);
        }

        window.filterByRoom = function (roomId) {
            var params = getCurrentParams();
            if (roomId) params.rooms = roomId; else delete params.rooms;
            loadDashboard(params);
        };

        window.filterByStatus = function (status) {
            var params = getCurrentParams();
            if (status && status !== 'all') params.status = status; else delete params.status;
            loadDashboard(params);
        };

        window.doRefresh = function (btn) {
            if (btn) btn.classList.add('is-loading');
            window.location.reload();
        };

        window.addEventListener('popstate', function () { window.location.reload(); });

        // ===================================================================
        // MATERIAL TIME PICKER — dialog analog clock (24 Hour Format)
        // ===================================================================
        var MTP_WORK_START = 7;
        var MTP_WORK_END = 16;

        var mtpState = {
            open: false,
            activeField: 'start',
            mode: 'hour',
            start: { h: 7, m: 0 },
            end: { h: 16, m: 0 }
        };

        function pad2(n) { return String(n).padStart(2, '0'); }
        function minsOf(t) { return t.h * 60 + t.m; }
        function isValidOperational(t) {
            var m = minsOf(t);
            return m >= MTP_WORK_START * 60 && m <= MTP_WORK_END * 60;
        }

        function parseLabel(str) {
            var p = String(str).trim().split(':');
            return { h: parseInt(p[0], 10) || 7, m: parseInt((p[1] || '0').split(/\s/)[0], 10) || 0 };
        }

        window.openTimePicker = function () {
            var labelEl = document.getElementById('timeTriggerLabel');
            var parts = labelEl.textContent.split('–');
            mtpState.start = parseLabel(parts[0] || '07:00');
            mtpState.end   = parseLabel(parts[1] || '16:00');
            
            if (!isValidOperational(mtpState.start)) mtpState.start = { h: MTP_WORK_START, m: 0 };
            if (!isValidOperational(mtpState.end))   mtpState.end   = { h: MTP_WORK_END, m: 0 };
            
            mtpState.activeField = 'start';
            mtpState.mode = 'hour';
            mtpState.open = true;
            document.getElementById('mtpOverlay').classList.add('open');
            document.getElementById('timeTrigger').setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
            renderClock();
            updateDigitalDisplay();
            hideMtpError();
        };

        window.closeTimePicker = function (revert) {
            mtpState.open = false;
            document.getElementById('mtpOverlay').classList.remove('open');
            document.getElementById('timeTrigger').setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            hideMtpError();
        };

        window.setActiveField = function (field) {
            mtpState.activeField = field;
            mtpState.mode = 'hour';
            renderClock();
            updateDigitalDisplay();
            hideMtpError();
        };

        // FUNGSI BARU: Klik angka Jam/Menit langsung
        window.selectUnit = function(field, mode) {
            mtpState.activeField = field;
            mtpState.mode = mode;
            renderClock();
            updateDigitalDisplay();
            hideMtpError();
        };

        window.toggleTimeMode = function () {
            mtpState.mode = mtpState.mode === 'hour' ? 'minute' : 'hour';
            renderClock();
            updateDigitalDisplay();
        };

        function isDisabled(h, m) {
            if (mtpState.activeField === 'end') {
                var startMins = minsOf(mtpState.start);
                var currentMins = h * 60 + m;
                return currentMins <= startMins;
            }
            return false;
        }

        window.selectHour = function (h24) {
            if (isDisabled(h24, 0) && isDisabled(h24, 30)) return;
            var t = mtpState[mtpState.activeField];
            t.h = h24;
            hideMtpError();
            mtpState.mode = 'minute';
            renderClock();
            updateDigitalDisplay();
        };

        window.selectMinute = function (m) {
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
        };

        function renderClock() {
            var clock = document.getElementById('mtpClock');
            clock.querySelectorAll('.mtp-num').forEach(function (n) { n.remove(); });

            var t = mtpState[mtpState.activeField];
            var R = 38.5; // percentage radius
            var C = 50;   // percentage center

            if (mtpState.mode === 'hour') {
                var hours = [];
                for (var h = MTP_WORK_START; h <= MTP_WORK_END; h++) hours.push(h);
                var totalHours = hours.length;
                var angleStep = 360 / totalHours;
                var offset = hours.indexOf(12); // Put 12 at the top

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

        window.applyTimePicker = function () {
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
            document.getElementById('timeTriggerLabel').textContent = startLabel + ' – ' + endLabel;
            closeTimePicker(false);

            var params = getCurrentParams();
            params.start_time = startLabel;
            params.end_time = endLabel;
            loadDashboard(params);
        };

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mtpState.open) closeTimePicker(true);
        });

        // ===================================================================
        // AKSI GRID
        // ===================================================================
        window.showBookingDetails = function (bookingId) {
            window.location.href = '/bookings/' + bookingId;
        };

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
            if (line && container) {
                container.scrollTop = Math.max(0, line.offsetTop - 80);
            }
        };

        var nowLine = document.getElementById('nowLine');
        if (nowLine) {
            setTimeout(function () { window.scrollToNow(); }, 300);
        }

        // ===================================================================
        // TOOLTIPS
        // ===================================================================
        function initTooltips() {
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[title]').forEach(function (el) {
                    new bootstrap.Tooltip(el, { placement: 'top', delay: { show: 300, hide: 100 } });
                });
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTooltips);
        } else { initTooltips(); }

    })();
</script>
@endsection