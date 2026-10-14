@extends('templates.template')

@section('title', 'Peminjaman Ruangan')
@section('page_title', 'Peminjaman Ruangan')

@section('content')
<style>
    /* ============================================================
       TOOLBAR (SAMA PERSIS DENGAN DASHBOARD)
       ============================================================ */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
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

    /* Solid oranye (bukan gradasi lagi) — disamakan dengan palet
       btn-modal-primary yang sudah didefinisikan sebelumnya, supaya
       makna & tampilan warna tombol konsisten di seluruh aplikasi. */
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

    .btn-outline-sm {
        height: 44px;
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

    /* Stat Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: var(--space-4);
        margin-bottom: var(--space-5);
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
    .stat-icon {
        width: 56px; height: 56px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: rgba(59,130,246,0.1);  color: var(--brand-blue); }
    .stat-icon.amber  { background: rgba(245,158,11,0.12); color: #b45309; }
    .stat-icon.green  { background: rgba(16,185,129,0.1);  color: var(--brand-green-dark); }
    .stat-icon.red    { background: rgba(239,68,68,0.1);   color: #b91c1c; }
    .stat-icon.slate  { background: rgba(100,116,139,0.12); color: #475569; }
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
    .stat-label {
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
        font-weight: 600;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ============================================================
       FILTER CONTROLS (SAMA PERSIS DENGAN DASHBOARD)
       ============================================================ */
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
        outline: none;
    }

    .db-divider {
        width: 1px;
        align-self: stretch;
        background: var(--border-color-light);
    }

    /* Badge hari relatif (Hari Ini / Besok / Lusa / dst) */
    .day-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 2px 8px;
        border-radius: var(--radius-pill);
        width: fit-content;
        margin-bottom: 3px;
    }
    .day-badge.today { background: rgba(249, 115, 22, 0.12); color: var(--brand-orange-dark); }
    .day-badge.soon { background: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
    .day-badge.upcoming { background: var(--bg-hover); color: var(--text-secondary); }
    .day-badge.past { background: rgba(148, 163, 184, 0.15); color: var(--text-muted); }

    /* Kalimat konteks: menjelaskan apa yang sedang ditampilkan */
    .list-context-hint {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin-bottom: var(--space-4);
        padding: var(--space-3) var(--space-4);
        background: var(--bg-hover);
        border-radius: var(--radius-sm);
    }
    .list-context-hint i { color: var(--brand-orange); font-size: 1rem; }
    .list-context-hint strong { color: var(--text-primary); }

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
    .db-select:hover { border-color: #cbd5e1; }
    .db-select:focus {
        border-color: var(--brand-orange);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
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
        text-decoration: none;
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
       BOOKING LIST
       ============================================================ */
    .bulk-action-bar {
        display: none;
        background: var(--brand-blue);
        color: white;
        padding: var(--space-3) var(--space-5);
        border-radius: var(--radius-sm);
        margin-bottom: var(--space-4);
        align-items: center;
        justify-content: space-between;
        gap: var(--space-3);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    .bulk-action-bar.show { display: flex; animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .bulk-action-bar .selected-count { font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .bulk-action-bar .actions { display: flex; gap: var(--space-2); }
    .bulk-btn {
        height: 36px;
        padding: 0 var(--space-4);
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: var(--radius-xs);
        color: white;
        font-size: var(--font-size-sm);
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .bulk-btn:hover { background: rgba(255,255,255,0.25); }
    .bulk-btn.danger { background: rgba(239,68,68,0.3); border-color: rgba(239,68,68,0.5); }

    .booking-list-wrapper {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    /* Desktop Table */
    .booking-table-wrap { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .booking-table { width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0; }
    .booking-table thead {
        background: var(--bg-body);
        border-bottom: 2px solid var(--border-color);
    }
    .booking-table thead th {
        padding: 1.1rem 1.5rem; 
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        white-space: nowrap;
    }
    .booking-table tbody tr {
        border-bottom: 1px solid var(--border-color-light);
        transition: background 0.15s;
    }
    .booking-table tbody tr:hover { background: var(--bg-hover); }
    .booking-table tbody tr.selected { background: rgba(59,130,246,0.05); }
    .booking-table tbody tr:last-child { border-bottom: none; }
    .booking-table td {
        padding: 1.25rem 1.5rem; 
        font-size: 14px;
        color: var(--text-primary);
        vertical-align: middle;
        line-height: 1.5;
    }
    .booking-table .room-cell { font-weight: 600; }
    .booking-table .purpose-cell { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .booking-table .checkbox-cell { width: 40px; text-align: center; }
    
    /* Room Thumbnail in Table */
    .room-info-cell { display: flex; align-items: center; gap: 14px; }
    .room-thumbnail {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        overflow: hidden;
        background-color: var(--bg-input);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid var(--border-color-light);
    }
    .room-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .room-thumbnail i {
        color: var(--text-muted);
        font-size: 1.2rem;
    }

    /* Type Badges */
    .type-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: var(--radius-pill);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    {{-- Warna "Priority" (ungu) disamakan dengan badge Priority di halaman
         Detail Peminjaman & kalender Dashboard — sebelumnya oranye di sini,
         beda sendiri dari 2 tempat lain yang sudah pakai ungu. --}}
    .type-priority { background: rgba(139, 92, 246, 0.12); color: #7c3aed; }
    .type-regular { background: rgba(100, 116, 139, 0.12); color: #475569; }

    /* Status Badges */
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: var(--radius-pill);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .status-pending { background: rgba(245,158,11,0.12); color: #b45309; }
    .status-approved { background: rgba(16,185,129,0.12); color: #047857; }
    .status-rejected { background: rgba(239,68,68,0.12); color: #b91c1c; }
    .status-cancelled { background: rgba(100,116,139,0.14); color: #475569; }

    .conflict-badge {
        font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-pill);
        background: rgba(249,115,22,0.12); color: var(--brand-orange-dark);
        display: inline-flex; align-items: center; gap: 4px; text-transform: uppercase; letter-spacing: 0.3px;
        white-space: nowrap; margin-top: 4px;
    }

    /* Rejection Reason Tooltip */
    .reason-cell {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
        position: relative;
    }
    .reason-cell:hover::after {
        content: attr(data-reason);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: var(--text-primary);
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        white-space: normal;
        width: 200px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin-bottom: 4px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-xs);
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .action-btn:hover { border-color: var(--brand-orange); color: var(--brand-orange); }
    .action-btn.view:hover { border-color: var(--brand-blue); color: var(--brand-blue); }
    .action-btn.edit:hover { border-color: var(--brand-orange); color: var(--brand-orange); }
    .action-btn.delete:hover { border-color: #ef4444; color: #ef4444; }
    .action-btn.approve:hover { border-color: #22C55E; color: #22C55E; background: rgba(34,197,94,0.05); }

    /* Mobile Cards */
    .booking-cards { display: none; flex-direction: column; gap: var(--space-3); padding: var(--space-3); }
    .booking-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
        border-left: 4px solid transparent;
        transition: all 0.2s;
    }
    .booking-card:active { transform: scale(0.99); }
    .booking-card.status-pending { border-left-color: #f59e0b; }
    .booking-card.status-approved { border-left-color: #22C55E; }
    .booking-card.status-rejected { border-left-color: #ef4444; }
    .booking-card.status-cancelled { border-left-color: #64748b; }

    .booking-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--space-3);
        margin-bottom: var(--space-3);
    }
    .booking-card-title { font-weight: 700; font-size: var(--font-size-md); color: var(--text-primary); }
    .booking-card-time {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .booking-card-body {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        padding: var(--space-3) 0;
        border-top: 1px solid var(--border-color-light);
    }
    .booking-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-2);
        margin-bottom: var(--space-2);
    }
    .booking-card-actions {
        display: flex;
        gap: var(--space-2);
        margin-top: var(--space-3);
        border-top: 1px solid var(--border-color-light);
        padding-top: var(--space-3);
    }

    .empty-state {
        text-align: center;
        padding: var(--space-9) var(--space-4);
    }
    .empty-state i { font-size: 3rem; color: var(--border-color); margin-bottom: var(--space-4); }
    .empty-state h4 { font-weight: 700; color: var(--text-primary); margin-bottom: var(--space-2); }
    .empty-state p { color: var(--text-muted); margin-bottom: var(--space-4); }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 1199.98px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 991.98px) {
        .db-toolbar { flex-direction: column; align-items: stretch; }
        
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
        
        .booking-table-wrap { display: none; }
        .booking-cards { display: flex; }
    }

    @media (max-width: 575.98px) {
        .stats-grid { grid-template-columns: 1fr; gap: var(--space-3); }
        .stat-card { padding: var(--space-4); gap: var(--space-3); }
        .stat-card .stat-icon { width: 44px; height: 44px; font-size: 1.2rem; }
        .stat-card .stat-value { font-size: var(--font-size-xl); }
        
        .mtp-dialog { width: calc(100vw - 32px); }
        .mtp-header { padding: 20px 20px 4px; }
        .mtp-time-display { gap: 16px; padding: 8px 20px 4px; }
        .mtp-digital { font-size: 36px; }
        .mtp-field { padding: 8px 12px; }
        .mtp-clock { width: 240px; height: 240px; }
        .mtp-footer { padding: 12px 16px 16px; }
    }

    @media (min-width: 992px) {
        .booking-cards { display: none !important; }
    }

    /* Animations */
    @keyframes fadeUp { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .db-controls { animation: fadeUp 0.3s ease forwards; }
    .booking-list-wrapper { animation: fadeUp 0.4s ease forwards; }
    .stat-card { animation: fadeUp 0.2s ease forwards; }
    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.2s; }

    /* ============================================================
       SECTION TABEL PER-STATUS — tiga tabel independen (Pending/
       Disetujui/Ditolak) menggantikan tabel gabungan + Riwayat
       Peminjaman yang lama. Tiap section punya heading + filter
       sendiri, tapi strukturnya seragam lewat perulangan di HTML.

       FIX: sebelumnya judul mengambang bebas TANPA card sama sekali,
       lalu filter jadi card sendiri, lalu tabel jadi card terpisah
       lagi — 3 blok visual lepas-lepas per section, terlihat
       berantakan/membingungkan (tidak jelas mana yang satu kesatuan).
       Sekarang ketiganya (judul + filter + tabel) digabung jadi SATU
       card besar per status, dengan pembatas garis tipis antar bagian
       di dalamnya — pola yang sama seperti card lain di aplikasi ini.
       ============================================================ */
    .status-table-section {
        margin-top: var(--space-5);
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .status-table-section:first-of-type { margin-top: 0; }

    .status-table-heading {
        display: flex; align-items: center; gap: 10px;
        font-size: 1.1rem; font-weight: 700; color: var(--text-primary);
        padding: var(--space-5) var(--space-5) var(--space-4);
    }
    .status-table-heading .status-export-btn {
        margin-left: auto; font-size: 0.85rem; font-weight: 600;
    }
    .status-table-heading .heading-icon {
        width: 32px; height: 32px; border-radius: 9px;
        display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;
    }
    .status-table-heading.pending .heading-icon  { background: var(--status-pending-bg, rgba(245,158,11,0.12)); color: var(--status-pending-text, #b45309); }
    .status-table-heading.approved .heading-icon { background: rgba(16,185,129,0.1); color: var(--brand-green-dark); }
    .status-table-heading.rejected .heading-icon { background: var(--status-rejected-bg, rgba(239,68,68,0.08)); color: var(--status-rejected-text, #b91c1c); }
    .status-table-count {
        font-size: 0.78rem; font-weight: 700; color: var(--text-muted);
        background: var(--bg-body); border: 1px solid var(--border-color-light);
        padding: 2px 10px; border-radius: 999px; margin-left: 2px;
    }

    /* Filter di dalam card TIDAK lagi jadi kartu sendiri (dulu punya
       bg/border/shadow sendiri) — sekarang jadi satu strip menyatu di
       dalam card besar, dipisah garis tipis atas-bawah saja. */
    .status-table-section .db-controls {
        background: var(--bg-card);
        border: none; border-radius: 0; box-shadow: none;
        margin: 0; padding: var(--space-4) var(--space-5);
        border-top: 1px solid var(--border-color-light);
        border-bottom: 1px solid var(--border-color-light);
    }

    .status-table-section .list-context-hint {
        margin: var(--space-4) var(--space-5) 0;
        background: transparent; padding: 0;
    }

    /* Tabel/kartu mobile juga tidak lagi jadi kartu sendiri — menyatu
       flush di dalam card besar (batas luarnya sudah ditangani card
       induk). */
    .status-table-section .booking-list-wrapper {
        background: transparent; border: none; border-radius: 0; box-shadow: none;
        margin: var(--space-4) 0 var(--space-5);
    }
</style>

{{-- FIX: controller mengirim session('success')/session('error') di banyak
     aksi (approve, tolak, hapus, alihkan, dll) tapi sebelumnya tidak ada
     halaman yang menampilkannya — pesan hilang begitu saja tanpa terlihat
     user. Sekarang ditampilkan pakai komponen alert acuan. --}}
@if(session('success'))
    <div class="alert-modern alert-success">
        <i class="bi bi-check-circle-fill alert-modern-icon"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="alert-modern alert-error">
        <i class="bi bi-exclamation-circle-fill alert-modern-icon"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="db-toolbar">
    <div>
        @if(Auth::user()->role == 1)
        <a href="{{ route('bookings.export', ['scope' => 'all']) }}" class="btn-outline-sm"
           title="Export SEMUA data peminjaman (semua status, tanpa filter)">
            <i class="bi bi-download"></i> Export Semua
        </a>
        @endif
    </div>
    <div class="actions">
        <a href="{{ route('bookings.create') }}" class="btn-primary-sm">
            <i class="bi bi-plus-lg"></i> Tambah Peminjaman
        </a>
    </div>
</div>

@include('bookings.partials._stats', ['stats' => $stats])

{{-- ============================================================
     TIGA TABEL INDEPENDEN — Pending, Disetujui, Ditolak.
     Menggantikan 1 tabel gabungan (difilter dropdown Status) +
     tabel "Riwayat Peminjaman" terpisah yang lama. Ketiga tabel di
     bawah dibangun dari satu perulangan supaya strukturnya konsisten
     (tidak ada risiko 3 salinan HTML manual saling berbeda seiring
     waktu) — sumber datanya beda ($pendingBookings/$approvedBookings/
     $rejectedBookings), tapi markup & filternya identik.

     CATATAN: filter "Status" yang dulu ada di tabel gabungan SENGAJA
     tidak ikut direplikasi di sini — karena tabel di bawah ini sudah
     tetap terkunci ke satu status masing-masing, dropdown Status di
     dalamnya justru akan kontradiktif (mis. pilih "Ditolak" di tabel
     Pending tidak akan pernah menampilkan apa pun). Filter lain
     (Tanggal, Jam Kerja, Ruangan, Reset) tetap identik dengan tabel
     lama.
     ============================================================ --}}
@php
    $statusTables = [
        [
            'prefix' => 'pending', 'label' => 'Menunggu Persetujuan', 'icon' => 'bi-hourglass-split',
            'heading_class' => 'pending', 'bookings' => $pendingBookings, 'hasDateFilter' => $pendingHasDateFilter,
            'showBulk' => Auth::user()->role == 1,
        ],
        [
            'prefix' => 'approved', 'label' => 'Disetujui', 'icon' => 'bi-check-circle',
            'heading_class' => 'approved', 'bookings' => $approvedBookings, 'hasDateFilter' => $approvedHasDateFilter,
            'showBulk' => false,
        ],
        [
            // FIX: judul diganti "Ditolak/Dibatalkan" — tabel ini memang
            // menampung DUA jenis booking berbeda maknanya (ditolak
            // langsung dari Pending, ATAU sempat Disetujui lalu
            // dibatalkan admin), keduanya sama-sama status==2. Label
            // per-baris di dalam tabelnya sendiri sudah otomatis
            // membedakan Ditolak vs Dibatalkan lewat Booking::status_label.
            'prefix' => 'rejected', 'label' => 'Ditolak/Dibatalkan', 'icon' => 'bi-x-circle',
            'heading_class' => 'rejected', 'bookings' => $rejectedBookings, 'hasDateFilter' => $rejectedHasDateFilter,
            'showBulk' => false,
        ],
    ];
@endphp

@foreach($statusTables as $t)
<div class="status-table-section">
    <div class="status-table-heading {{ $t['heading_class'] }}">
        <span class="heading-icon"><i class="bi {{ $t['icon'] }}"></i></span>
        Peminjaman {{ $t['label'] }}
        <span class="status-table-count">{{ $t['bookings']->total() }}</span>

        @if(Auth::user()->role == 1)
            @php
                // Export per card: bawa status card ini + filter card ini saja
                // (tanggal & ruangan). Filter card lain tidak ikut terbawa.
                $exportParams = array_filter([
                    'scope' => $t['prefix'],
                    $t['prefix'] . '_date' => request($t['prefix'] . '_date'),
                    $t['prefix'] . '_date_from' => request($t['prefix'] . '_date_from'),
                    $t['prefix'] . '_date_to' => request($t['prefix'] . '_date_to'),
                    $t['prefix'] . '_room' => request($t['prefix'] . '_room'),
                ], fn ($v) => filled($v));
            @endphp
            <a href="{{ route('bookings.export', $exportParams) }}" class="btn-outline-sm status-export-btn"
               title="Export tabel &quot;{{ $t['label'] }}&quot; sesuai filter Tanggal &amp; Ruangan di card ini">
                <i class="bi bi-download"></i> Export
            </a>
        @endif
    </div>

    <form action="{{ route('bookings.index') }}" method="GET" id="{{ $t['prefix'] }}FilterForm">
        {{-- Bawa serta filter & halaman tabel LAIN (kalau ada) supaya tidak
             ke-reset saat form tabel ini disubmit — ketiganya independen. --}}
        @foreach($statusTables as $other)
            @continue($other['prefix'] === $t['prefix'])
            @foreach(['date', 'room', 'start_time', 'end_time', 'page'] as $suffix)
                @php $paramName = $other['prefix'] . '_' . $suffix; @endphp
                @if(request()->filled($paramName))
                    <input type="hidden" name="{{ $paramName }}" value="{{ request($paramName) }}">
                @endif
            @endforeach
        @endforeach

        <div class="db-controls">
            <div class="db-filter-group">
                <label for="{{ $t['prefix'] }}DateSelector">Tanggal</label>
                <div class="date-navigator" role="group" aria-label="Navigasi Tanggal">
                    <button type="button" class="btn-today" onclick="resetToToday('{{ $t['prefix'] }}')" title="Hari Ini">
                        <i class="bi bi-calendar-date"></i> Hari Ini
                    </button>
                    <button type="button" class="date-nav-btn" onclick="shiftDate(-1, '{{ $t['prefix'] }}')" aria-label="Tanggal sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <label class="date-display">
                        <input type="date" id="{{ $t['prefix'] }}DateSelector" name="{{ $t['prefix'] }}_date"
                               value="{{ request($t['prefix'] . '_date') }}"
                               onchange="document.getElementById('{{ $t['prefix'] }}FilterForm').submit()"
                               autocomplete="off" aria-label="Pilih tanggal">
                    </label>
                    <button type="button" class="date-nav-btn" onclick="shiftDate(1, '{{ $t['prefix'] }}')" aria-label="Tanggal berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="db-divider"></div>

            <div class="db-filter-group">
                <label for="{{ $t['prefix'] }}TimeTrigger">Jam Kerja</label>
                <button type="button" class="time-trigger" id="{{ $t['prefix'] }}TimeTrigger" onclick="openTimePicker('{{ $t['prefix'] }}')" aria-haspopup="dialog" aria-expanded="false">
                    <i class="bi bi-clock"></i>
                    <span id="{{ $t['prefix'] }}TimeTriggerLabel">{{ request($t['prefix'] . '_start_time', '07:00') }} - {{ request($t['prefix'] . '_end_time', '16:00') }}</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </button>
            </div>

            <div class="db-divider"></div>

            <div class="db-filter-group">
                <label for="{{ $t['prefix'] }}RoomFilter">Ruangan</label>
                <select id="{{ $t['prefix'] }}RoomFilter" class="db-select" name="{{ $t['prefix'] }}_room" onchange="document.getElementById('{{ $t['prefix'] }}FilterForm').submit()">
                    <option value="">Semua Ruangan</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request($t['prefix'] . '_room') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-spacer"></div>

            <button type="button" class="btn-reset-filter" onclick="resetFilters('{{ $t['prefix'] }}')">
                <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
            </button>
        </div>
    </form>

    @include('bookings.partials._main-list', [
        'bookings' => $t['bookings'],
        'hasDateFilter' => $t['hasDateFilter'],
        'prefix' => $t['prefix'],
        'showBulk' => $t['showBulk'],
    ])
</div>
@endforeach

<!-- MATERIAL TIME PICKER -->
<div class="mtp-overlay" id="mtpOverlay" onclick="if(event.target===this)closeTimePicker()">
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
                <button type="button" class="mtp-btn mtp-btn-cancel" onclick="closeTimePicker()">Batal</button>
                <button type="button" class="mtp-btn mtp-btn-ok" onclick="applyTimePicker()">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter Form Functions — sekarang menerima "prefix" karena ada TIGA
    // form filter independen di halaman ini (pending/approved/rejected),
    // bukan cuma satu seperti sebelumnya.
    function shiftDate(days, prefix) {
        var input = document.getElementById(prefix + 'DateSelector');
        var base = input && input.value ? new Date(input.value) : new Date();
        base.setDate(base.getDate() + days);
        var y = base.getFullYear();
        var m = String(base.getMonth() + 1).padStart(2, '0');
        var d = String(base.getDate()).padStart(2, '0');
        input.value = y + '-' + m + '-' + d;
        document.getElementById(prefix + 'FilterForm').submit();
    }

    function resetToToday(prefix) {
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        var dateInput = document.getElementById(prefix + 'DateSelector');
        if (dateInput) {
            dateInput.value = y + '-' + m + '-' + d;
            document.getElementById(prefix + 'FilterForm').submit();
        }
    }

    // Reset HANYA filter tabel ini (prefix-nya) — filter 2 tabel status
    // lain di URL yang sama TIDAK ikut hilang, supaya benar-benar independen.
    function resetFilters(prefix) {
        var url = new URL(window.location.href);
        ['date', 'room', 'start_time', 'end_time', 'page'].forEach(function (suffix) {
            url.searchParams.delete(prefix + '_' + suffix);
        });
        window.location.href = url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '');
    }

    // Bulk Action Functions
    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.booking-checkbox:not(:disabled)').forEach(cb => {
            cb.checked = checkbox.checked;
            cb.closest('tr').classList.toggle('selected', checkbox.checked);
        });
        updateBulkBar();
    }

    function updateBulkBar() {
        const checked = document.querySelectorAll('.booking-checkbox:checked:not(:disabled)');
        const bar = document.getElementById('bulkActionBar');
        const count = document.getElementById('selectedCount');

        if (checked.length > 0) {
            bar.classList.add('show');
            count.textContent = checked.length;
        } else {
            bar.classList.remove('show');
        }

        document.querySelectorAll('.booking-row').forEach(row => {
            const cb = row.querySelector('.booking-checkbox');
            if (cb) row.classList.toggle('selected', cb.checked);
        });
    }

    function clearSelection() {
        document.querySelectorAll('.booking-checkbox:checked').forEach(cb => {
            cb.checked = false;
            cb.closest('tr').classList.remove('selected');
        });
        const selectAll = document.getElementById('selectAll');
        if(selectAll) selectAll.checked = false;
        updateBulkBar();
    }

    function bulkAction(action) {
        const checked = document.querySelectorAll('.booking-checkbox:checked:not(:disabled)');
        if (checked.length === 0) return;

        const ids = Array.from(checked).map(cb => cb.value);
        const message = action === 'approve' ? 'Menyetujui' : 'Menolak';

        if (!confirm(`${message} ${ids.length} booking yang dipilih?`)) return;

        // Penolakan WAJIB beralasan (sama seperti tombol Tolak di halaman detail)
        // — alasan ini dikirim ke setiap peminjam lewat notifikasi.
        let reason = null;
        if (action === 'reject') {
            reason = prompt('Alasan penolakan untuk peminjam (minimal 10 karakter):');
            if (reason === null) return;
            reason = reason.trim();
            if (reason.length < 10) {
                alert('Alasan penolakan minimal 10 karakter.');
                return;
            }
        }

        fetch('{{ route("bookings.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ booking_ids: ids, action: action, rejection_reason: reason })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat memproses');
        });
    }

    // Material Time Picker Script
    // Satu modal dipakai bersama oleh SEMUA tabel (elemen dekoratif ini
    // memang tidak benar-benar memfilter apa pun di server — cuma
    // tersimpan di URL & ditampilkan balik, sama seperti perilaku
    // sebelumnya), tapi perlu tahu tabel mana yang sedang memakainya
    // supaya label & form yang benar yang diperbarui.
    var MTP_WORK_START = 7, MTP_WORK_END = 16;
    var mtpState = { open: false, activeField: 'start', mode: 'hour', start: { h: 7, m: 0 }, end: { h: 16, m: 0 } };
    var mtpActivePrefix = null;

    function pad2(n) { return String(n).padStart(2, '0'); }
    function minsOf(t) { return t.h * 60 + t.m; }
    function isValidOperational(t) { var m = minsOf(t); return m >= MTP_WORK_START * 60 && m <= MTP_WORK_END * 60; }
    function parseLabel(str) { var p = String(str).trim().split('-'); var s = p[0] ? p[0].trim().split(':') : ['07','00']; var e = p[1] ? p[1].trim().split(':') : ['16','00']; return { start: { h: parseInt(s[0], 10) || 7, m: parseInt(s[1], 10) || 0 }, end: { h: parseInt(e[0], 10) || 16, m: parseInt(e[1], 10) || 0 } }; }

    window.openTimePicker = function (prefix) {
        mtpActivePrefix = prefix;
        var labelEl = document.getElementById(prefix + 'TimeTriggerLabel');
        var parsed = parseLabel(labelEl.textContent);
        mtpState.start = parsed.start;
        mtpState.end = parsed.end;
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
        if (!mtpActivePrefix) { closeTimePicker(); return; }

        var sl = pad2(mtpState.start.h) + ':' + pad2(mtpState.start.m);
        var el = pad2(mtpState.end.h) + ':' + pad2(mtpState.end.m);

        document.getElementById(mtpActivePrefix + 'TimeTriggerLabel').textContent = sl + ' - ' + el;
        closeTimePicker();

        var form = document.getElementById(mtpActivePrefix + 'FilterForm');

        // Hapus hidden input lama dulu (kalau ada dari kali sebelumnya
        // picker ini dipakai) supaya tidak menumpuk duplikat tiap kali
        // dibuka-tutup berkali-kali.
        var oldStart = form.querySelector('input[name="' + mtpActivePrefix + '_start_time"]');
        if (oldStart) oldStart.remove();
        var oldEnd = form.querySelector('input[name="' + mtpActivePrefix + '_end_time"]');
        if (oldEnd) oldEnd.remove();

        var startInput = document.createElement('input');
        startInput.type = 'hidden';
        startInput.name = mtpActivePrefix + '_start_time';
        startInput.value = sl;
        form.appendChild(startInput);

        var endInput = document.createElement('input');
        endInput.type = 'hidden';
        endInput.name = mtpActivePrefix + '_end_time';
        endInput.value = el;
        form.appendChild(endInput);

        form.submit();
    };
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && mtpState.open) closeTimePicker(); });
</script>

{{-- ============================================================
     LIVE UPDATE — polling berkala (bukan WebSocket, server belum
     punya infrastrukturnya). Kartu statistik & KETIGA tabel status
     (Pending/Disetujui/Ditolak) di-refresh otomatis tiap beberapa
     detik, masing-masing memakai filter sendiri yang sedang aktif di
     layar, supaya hasilnya selalu konsisten dengan apa yang sedang
     dilihat. Form filter tidak ikut di-refresh (supaya tidak
     mengganggu interaksi yang sedang berlangsung).
     ============================================================ --}}
<script>
(function () {
    var lastSignature = 'initial';

    function isAnyModalOpen() {
        return document.querySelector('.modal.show') !== null || document.getElementById('bulkActionBar')?.classList.contains('show');
    }

    function hasCheckedBoxes() {
        return document.querySelectorAll('.booking-checkbox:checked').length > 0;
    }

    createLivePoller({
        url: function () { return window.location.pathname + window.location.search; },
        interval: 15000,
        // Jangan tukar DOM kalau admin sedang menyeleksi checkbox untuk aksi
        // massal — supaya seleksinya tidak hilang tiba-tiba.
        shouldPause: function () { return isAnyModalOpen() || hasCheckedBoxes(); },
        onUpdate: function (data) {
            if (data.signature === lastSignature) return;
            lastSignature = data.signature;

            var statsEl = document.getElementById('liveStatsGrid');
            if (statsEl) statsEl.outerHTML = data.stats;

            var map = { pending: data.pending_results, approved: data.approved_results, rejected: data.rejected_results };
            Object.keys(map).forEach(function (prefix) {
                var el = document.getElementById(prefix + 'ResultsArea');
                if (el && map[prefix]) el.outerHTML = map[prefix];
            });
        }
    });
})();
</script>
@endsection