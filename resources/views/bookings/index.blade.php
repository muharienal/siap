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

    .btn-primary-sm {
        height: 44px;
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
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
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
    .stat-body { min-width: 0; }
    .stat-value {
        font-size: var(--font-size-2xl);
        font-weight: 800;
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
    .btn-today:hover { filter: brightness(1.05); }
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
    .type-priority { background: rgba(255, 145, 66, 0.12); color: #F2751A; }
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
        <a href="{{ route('bookings.export', request()->query()) }}" class="btn-outline-sm">
            <i class="bi bi-download"></i> Export
        </a>
    </div>
    <div class="actions">
        <a href="{{ route('bookings.create') }}" class="btn-primary-sm">
            <i class="bi bi-plus-lg"></i> Tambah Peminjaman
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-clipboard-data"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Total Booking</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">Menunggu Persetujuan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-label">Disetujui</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>
</div>

<form action="{{ route('bookings.index') }}" method="GET" id="filterForm">
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
                    <input type="date" id="dateSelector" name="date" value="{{ request('date') }}" onchange="document.getElementById('filterForm').submit()" autocomplete="off" aria-label="Pilih tanggal">
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
                <span id="timeTriggerLabel">{{ request('start_time', '07:00') }} - {{ request('end_time', '16:00') }}</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
        </div>

        <div class="db-divider"></div>

        <div class="db-filter-group">
            <label for="roomFilter">Ruangan</label>
            <select id="roomFilter" class="db-select" name="room" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Ruangan</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ request('room') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="db-divider"></div>

        <div class="db-filter-group">
            <label for="statusFilterSelect">Status</label>
            <select id="statusFilterSelect" class="db-select" name="status" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="filter-spacer"></div>

        <button type="button" class="btn-reset-filter" onclick="resetFilters()">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
        </button>
    </div>
</form>

@if(Auth::user()->role == 1)
<div class="bulk-action-bar" id="bulkActionBar">
    <div class="selected-count">
        <i class="bi bi-check2-square"></i> <span id="selectedCount">0</span> booking dipilih
    </div>
    <div class="actions">
        <button type="button" class="bulk-btn" onclick="bulkAction('approve')">
            <i class="bi bi-check-circle"></i> Setujui Semua
        </button>
        <button type="button" class="bulk-btn danger" onclick="bulkAction('reject')">
            <i class="bi bi-x-circle"></i> Tolak Semua
        </button>
        <button type="button" class="bulk-btn" onclick="clearSelection()">
            <i class="bi bi-x"></i> Batal
        </button>
    </div>
</div>
@endif

<div class="list-context-hint">
    @if(request()->filled('date'))
        <i class="bi bi-funnel"></i>
        Menampilkan peminjaman untuk <strong>{{ \Carbon\Carbon::parse(request('date'))->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong>
    @elseif($hasDateFilter)
        <i class="bi bi-funnel"></i>
        Menampilkan peminjaman sesuai rentang tanggal yang dipilih
    @else
        <i class="bi bi-calendar-week"></i>
        Menampilkan <strong>seluruh peminjaman hari ini &amp; mendatang</strong>, diurutkan dari yang paling dekat waktunya
    @endif
</div>

<div class="booking-list-wrapper">
    @if($bookings->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>Tidak ada peminjaman ditemukan</h4>
            <p>@if(request()->hasAny(['status', 'room', 'date', 'start_time', 'end_time'])) Coba ubah filter pencarian Anda @else Mulai buat peminjaman baru untuk ruangan yang Anda butuhkan @endif</p>
        </div>
    @else
        {{-- Desktop Table --}}
        <div class="booking-table-wrap">
            <table class="booking-table" id="bookingTable">
                <thead>
                    <tr>
                        @if(Auth::user()->role == 1)
                        <th class="checkbox-cell">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll(this)">
                        </th>
                        @endif
                        <th>Ruangan</th>
                        <th>Tanggal & Waktu</th>
                        <th>Peminjam</th>
                        @if(Auth::user()->role == 1)
                        <th>Jenis</th>
                        @endif
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        @php
                            $statusClass = $booking->status == 0 ? 'pending' : ($booking->status == 1 ? 'approved' : 'rejected');
                            $statusLabel = $booking->status == 0 ? 'Pending' : ($booking->status == 1 ? 'Disetujui' : 'Ditolak');
                            $typeClass = $booking->booking_type == 'Priority' ? 'priority' : 'regular';
                            $typeLabel = $booking->booking_type == 'Priority' ? 'Priority' : 'Reguler';
                            $roomPhoto = $booking->room->photos->isNotEmpty() ? $booking->room->photos->first()->photo_url : null;

                            // Badge hari relatif (Hari Ini / Besok / Lusa / X Hari Lagi)
                            // supaya user langsung tahu kapan booking ini tanpa mikir tanggal.
                            $bookingDate = \Carbon\Carbon::parse($booking->start_time)->startOfDay();
                            $todayDate = \Carbon\Carbon::today();
                            $diffDays = $todayDate->diffInDays($bookingDate);
                            if ($bookingDate->lt($todayDate)) $diffDays = -$diffDays;

                            if ($diffDays === 0) {
                                $dayBadge = ['label' => 'Hari Ini', 'class' => 'today'];
                            } elseif ($diffDays === 1) {
                                $dayBadge = ['label' => 'Besok', 'class' => 'soon'];
                            } elseif ($diffDays === 2) {
                                $dayBadge = ['label' => 'Lusa', 'class' => 'soon'];
                            } elseif ($diffDays > 2 && $diffDays <= 14) {
                                $dayBadge = ['label' => $diffDays . ' Hari Lagi', 'class' => 'upcoming'];
                            } elseif ($diffDays < 0) {
                                $dayBadge = ['label' => abs($diffDays) . ' Hari Lalu', 'class' => 'past'];
                            } else {
                                $dayBadge = null;
                            }
                        @endphp
                        <tr class="booking-row" data-id="{{ $booking->id }}">
                            @if(Auth::user()->role == 1)
                            <td class="checkbox-cell">
                                <input type="checkbox" class="form-check-input booking-checkbox" value="{{ $booking->id }}" onchange="updateBulkBar()" {{ $booking->status != 0 ? 'disabled' : '' }}>
                            </td>
                            @endif
                            <td class="room-cell">
                                <div class="room-info-cell">
                                    <div class="room-thumbnail">
                                        @if($roomPhoto)
                                            <img src="{{ $roomPhoto }}" alt="{{ $booking->room->name }}">
                                        @else
                                            <i class="bi bi-door-open"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div>{{ $booking->room->name }}</div>
                                        <small class="text-muted">Kapasitas {{ $booking->room->capacity }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($dayBadge)
                                    <span class="day-badge {{ $dayBadge['class'] }}">{{ $dayBadge['label'] }}</span><br>
                                @endif
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                <div>{{ $booking->user->full_name ?? 'Unknown' }}</div>
                                <small class="text-muted">{{ $booking->user->nip ?? '-' }}</small>
                            </td>
                            @if(Auth::user()->role == 1)
                            <td>
                                <span class="type-badge type-{{ $typeClass }}">
                                    <i class="bi bi-{{ $booking->booking_type == 'Priority' ? 'star-fill' : 'circle' }}"></i>
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            @endif
                            <td class="purpose-cell" title="{{ $booking->purpose }}">{{ $booking->purpose }}</td>
                            <td>
                                <span class="status-badge status-{{ $statusClass }}">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> {{ $statusLabel }}
                                </span>
                                @if($booking->displaced_by_id && Auth::user()->role == 1)
                                    <br>
                                    <span class="conflict-badge">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Bentrok
                                    </span>
                                @endif
                            </td>
                            <td class="reason-cell" data-reason="{{ $booking->rejection_reason ?? '-' }}">
                                {{ $booking->rejection_reason ? Str::limit($booking->rejection_reason, 30) : '-' }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="action-btn view" title="Pratinjau">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="booking-cards">
            @foreach($bookings as $booking)
                @php
                    $statusClass = $booking->status == 0 ? 'pending' : ($booking->status == 1 ? 'approved' : 'rejected');
                    $statusLabel = $booking->status == 0 ? 'Pending' : ($booking->status == 1 ? 'Disetujui' : 'Ditolak');
                    $typeClass = $booking->booking_type == 'Priority' ? 'priority' : 'regular';
                    $typeLabel = $booking->booking_type == 'Priority' ? 'Priority' : 'Reguler';
                    $roomPhoto = $booking->room->photos->isNotEmpty() ? $booking->room->photos->first()->photo_url : null;

                    $bookingDate = \Carbon\Carbon::parse($booking->start_time)->startOfDay();
                    $todayDate = \Carbon\Carbon::today();
                    $diffDays = $todayDate->diffInDays($bookingDate);
                    if ($bookingDate->lt($todayDate)) $diffDays = -$diffDays;

                    if ($diffDays === 0) {
                        $dayBadge = ['label' => 'Hari Ini', 'class' => 'today'];
                    } elseif ($diffDays === 1) {
                        $dayBadge = ['label' => 'Besok', 'class' => 'soon'];
                    } elseif ($diffDays === 2) {
                        $dayBadge = ['label' => 'Lusa', 'class' => 'soon'];
                    } elseif ($diffDays > 2 && $diffDays <= 14) {
                        $dayBadge = ['label' => $diffDays . ' Hari Lagi', 'class' => 'upcoming'];
                    } elseif ($diffDays < 0) {
                        $dayBadge = ['label' => abs($diffDays) . ' Hari Lalu', 'class' => 'past'];
                    } else {
                        $dayBadge = null;
                    }
                @endphp
                <div class="booking-card status-{{ $statusClass }}">
                    <div class="booking-card-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="room-thumbnail">
                                @if($roomPhoto)
                                    <img src="{{ $roomPhoto }}" alt="{{ $booking->room->name }}">
                                @else
                                    <i class="bi bi-door-open"></i>
                                @endif
                            </div>
                            <div>
                                <div class="booking-card-title">{{ $booking->room->name }}</div>
                                @if($dayBadge)
                                    <span class="day-badge {{ $dayBadge['class'] }}">{{ $dayBadge['label'] }}</span>
                                @endif
                                <div class="booking-card-time">
                                    <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}
                                    <i class="bi bi-clock ms-2"></i> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="status-badge status-{{ $statusClass }}">{{ $statusLabel }}</span>
                            @if($booking->displaced_by_id && Auth::user()->role == 1)
                                <span class="conflict-badge" style="margin-top:0;">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Bentrok
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="booking-card-body">
                        <div class="booking-card-meta">
                            @if(Auth::user()->role == 1)
                            <span class="type-badge type-{{ $typeClass }}">
                                <i class="bi bi-{{ $booking->booking_type == 'Priority' ? 'star-fill' : 'circle' }}"></i>
                                {{ $typeLabel }}
                            </span>
                            @endif
                            <small class="text-muted">
                                <i class="bi bi-person"></i> {{ $booking->user->full_name ?? 'Unknown' }}
                            </small>
                        </div>
                        <div class="mb-1"><strong>Tujuan:</strong> {{ Str::limit($booking->purpose, 80) }}</div>
                        @if($booking->rejection_reason)
                        <div class="text-danger small mt-2">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Alasan Ditolak:</strong> {{ $booking->rejection_reason }}
                        </div>
                        @endif
                    </div>
                    <div class="booking-card-actions">
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-eye"></i> Pratinjau
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@if($bookings->hasPages())
<div class="d-flex justify-content-center mt-5">
    {{ $bookings->appends(request()->query())->links() }}
</div>
@endif

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
    // Filter Form Functions
    function shiftDate(days) {
        var input = document.getElementById('dateSelector');
        var base = input && input.value ? new Date(input.value) : new Date();
        base.setDate(base.getDate() + days);
        var y = base.getFullYear();
        var m = String(base.getMonth() + 1).padStart(2, '0');
        var d = String(base.getDate()).padStart(2, '0');
        input.value = y + '-' + m + '-' + d;
        document.getElementById('filterForm').submit();
    }

    function resetToToday() {
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        var dateInput = document.getElementById('dateSelector');
        if (dateInput) {
            dateInput.value = y + '-' + m + '-' + d;
            document.getElementById('filterForm').submit();
        }
    }

    function resetFilters() {
        window.location.href = '{{ route('bookings.index') }}';
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

        fetch('{{ route("bookings.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ booking_ids: ids, action: action })
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
    var MTP_WORK_START = 7, MTP_WORK_END = 16;
    var mtpState = { open: false, activeField: 'start', mode: 'hour', start: { h: 7, m: 0 }, end: { h: 16, m: 0 } };

    function pad2(n) { return String(n).padStart(2, '0'); }
    function minsOf(t) { return t.h * 60 + t.m; }
    function isValidOperational(t) { var m = minsOf(t); return m >= MTP_WORK_START * 60 && m <= MTP_WORK_END * 60; }
    function parseLabel(str) { var p = String(str).trim().split('-'); var s = p[0] ? p[0].trim().split(':') : ['07','00']; var e = p[1] ? p[1].trim().split(':') : ['16','00']; return { start: { h: parseInt(s[0], 10) || 7, m: parseInt(s[1], 10) || 0 }, end: { h: parseInt(e[0], 10) || 16, m: parseInt(e[1], 10) || 0 } }; }

    window.openTimePicker = function () {
        var labelEl = document.getElementById('timeTriggerLabel');
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
        var sl = pad2(mtpState.start.h) + ':' + pad2(mtpState.start.m);
        var el = pad2(mtpState.end.h) + ':' + pad2(mtpState.end.m);
        
        document.getElementById('timeTriggerLabel').textContent = sl + ' - ' + el;
        closeTimePicker();
        
        var form = document.getElementById('filterForm');
        var startInput = document.createElement('input');
        startInput.type = 'hidden';
        startInput.name = 'start_time';
        startInput.value = sl;
        form.appendChild(startInput);

        var endInput = document.createElement('input');
        endInput.type = 'hidden';
        endInput.name = 'end_time';
        endInput.value = el;
        form.appendChild(endInput);

        form.submit();
    };
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && mtpState.open) closeTimePicker(); });
</script>
@endsection