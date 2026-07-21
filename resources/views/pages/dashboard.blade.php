@php
    use Carbon\Carbon;
@endphp

@extends('templates.template')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Jadwal peminjaman ruangan hari ini')

@section('content')
<style>
    /* ============================================================
       DASHBOARD STYLE
       ============================================================ */
    .dashboard-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-3);
        margin-bottom: var(--space-5);
    }
    .dashboard-header .greeting h1 {
        font-size: var(--font-size-xl);
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.02em;
    }
    .dashboard-header .greeting p {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin: var(--space-1) 0 0 0;
        display: flex;
        align-items: center;
        gap: var(--space-2);
        flex-wrap: wrap;
    }
    .dashboard-header .greeting p .clock {
        font-weight: 500;
        color: var(--text-secondary);
    }
    .dashboard-header .actions {
        display: flex;
        gap: var(--space-2);
        flex-wrap: wrap;
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

    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: var(--space-3);
        margin-bottom: var(--space-5);
        background: var(--bg-card);
        padding: var(--space-3) var(--space-5);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        border: none;
        min-height: 68px;
    }
    .filter-bar .form-group {
        flex: 0 0 auto;
        min-width: 160px;
    }
    .filter-bar .form-group label {
        font-weight: 600;
        font-size: var(--font-size-xs);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--text-muted);
        margin-bottom: var(--space-1);
        display: block;
    }
    .filter-bar .form-control,
    .filter-bar .form-select {
        height: 38px;
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        width: 100%;
        transition: border var(--transition-fast), box-shadow var(--transition-fast);
    }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color: var(--brand-orange);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.06);
        outline: none;
    }
    .filter-bar .btn-today {
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
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.15);
    }
    .filter-bar .btn-today:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.20);
    }
    .filter-bar .btn-refresh {
        height: 38px;
        width: 38px;
        padding: 0;
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-secondary);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .filter-bar .btn-refresh:hover {
        border-color: var(--brand-orange);
        color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.04);
    }
    .filter-bar .btn-refresh:active {
        transform: rotate(180deg);
        transition: transform 0.4s ease;
    }
    .filter-bar .work-hours {
        font-size: var(--font-size-sm);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: var(--space-2);
        white-space: nowrap;
        height: 38px;
        font-weight: 500;
        margin-left: auto;
    }
    .filter-bar .work-hours i {
        font-size: 1rem;
        color: var(--brand-orange);
    }
    .filter-bar .filter-spacer {
        flex: 1;
    }

    .schedule-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .schedule-card .card-header {
        padding: var(--space-3) var(--space-5);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
        min-height: 52px;
    }
    .schedule-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .schedule-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
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
        font-size: var(--font-size-sm);
        color: var(--text-muted);
    }
    .schedule-card .card-header .header-right .current-badge {
        background: var(--brand-orange);
        color: #fff;
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
    }

    .schedule-card .card-body {
        padding: 0;
        overflow-x: auto;
        position: relative;
        max-height: 600px;
        overflow-y: auto;
        scroll-behavior: smooth;
    }

    .table-schedule {
        width: 100%;
        border-collapse: collapse;
        font-size: var(--font-size-sm);
        min-width: 720px;
    }
    .table-schedule thead th {
        position: sticky;
        top: 0;
        z-index: 20;
        background: var(--bg-card);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: var(--font-size-xs);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: var(--space-2) var(--space-2);
        border-bottom: 2px solid var(--border-color-light);
        text-align: center;
        white-space: nowrap;
        min-width: 140px;
        height: auto;
        vertical-align: top;
        padding-top: var(--space-2);
    }
    .table-schedule thead th:first-child {
        min-width: 60px;
        position: sticky;
        left: 0;
        z-index: 25;
        background: var(--bg-card);
        border-right: 1px solid var(--border-color-light);
        vertical-align: middle;
        padding-top: var(--space-2);
    }
    .table-schedule thead th .room-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .table-schedule thead th .room-header .room-photo {
        width: 80px;
        height: 80px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        cursor: pointer;
        transition: all var(--transition-fast);
        border: 2px solid transparent;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        background: var(--bg-body);
    }
    .table-schedule thead th .room-header .room-photo:hover {
        transform: scale(1.05);
        border-color: var(--brand-orange);
        box-shadow: 0 4px 16px rgba(249, 115, 22, 0.15);
    }
    .table-schedule thead th .room-header .room-photo-placeholder {
        width: 80px;
        height: 80px;
        border-radius: var(--radius-sm);
        background: var(--bg-body);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 2rem;
        border: 1px dashed var(--border-color);
        cursor: default;
    }
    .table-schedule thead th .room-header .room-name {
        font-weight: 700;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        margin-top: 2px;
    }
    .table-schedule thead th .room-header .room-capacity {
        font-weight: 400;
        font-size: var(--font-size-xs);
        color: var(--text-muted);
    }
    .table-schedule thead th .room-header .photo-count {
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        background: rgba(0,0,0,0.05);
        padding: 0 8px;
        border-radius: var(--radius-pill);
        cursor: pointer;
        transition: all var(--transition-fast);
    }
    .table-schedule thead th .room-header .photo-count:hover {
        background: rgba(249, 115, 22, 0.10);
        color: var(--brand-orange-dark);
    }

    .table-schedule tbody td {
        padding: var(--space-2) var(--space-2);
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color-light);
        min-width: 140px;
        height: 52px;
        background: var(--bg-card);
        transition: background var(--transition-fast);
    }
    .table-schedule tbody td:first-child {
        position: sticky;
        left: 0;
        z-index: 10;
        background: var(--bg-card);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--brand-orange-dark);
        border-right: 1px solid var(--border-color-light);
        min-width: 60px;
        padding: var(--space-2) var(--space-2);
        text-align: center;
    }
    .table-schedule tbody tr:nth-child(even) td:not(:first-child) {
        background: #fafbfc;
    }
    .table-schedule tbody tr:nth-child(even) td:first-child {
        background: var(--bg-card);
    }
    .table-schedule tbody tr:hover td:not(:first-child) {
        background: rgba(249, 115, 22, 0.03);
    }
    .table-schedule tbody tr:hover td:first-child {
        background: var(--bg-card);
    }
    .table-schedule tbody td:hover:not(:first-child) {
        background: rgba(249, 115, 22, 0.05) !important;
    }
    .table-schedule tbody tr.current-time td:not(:first-child) {
        background: rgba(249, 115, 22, 0.06);
        border-left: 2px solid var(--brand-orange);
        border-right: 2px solid var(--brand-orange);
    }
    .table-schedule tbody tr.current-time td:first-child {
        background: var(--bg-card);
        color: var(--brand-orange-dark);
        font-weight: 700;
        border-right: 2px solid var(--brand-orange);
    }

    .slot-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: var(--space-1) var(--space-2);
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 500;
        transition: all var(--transition-fast);
        gap: var(--space-1);
        min-height: 28px;
        max-width: 100%;
        border: 1px solid transparent;
        cursor: default;
    }
    .slot-badge.available {
        background: rgba(16, 185, 129, 0.08);
        color: var(--brand-green-dark);
        border-color: rgba(16, 185, 129, 0.12);
        cursor: pointer;
    }
    .slot-badge.available:hover {
        background: rgba(16, 185, 129, 0.16);
        transform: scale(1.02);
    }
    .slot-badge.booked {
        background: rgba(59, 130, 246, 0.08);
        color: var(--brand-blue-dark);
        border-color: rgba(59, 130, 246, 0.12);
        cursor: pointer;
    }
    .slot-badge.booked:hover {
        background: rgba(59, 130, 246, 0.16);
        transform: translateY(-1px);
    }
    .slot-badge.booked .b-name {
        font-weight: 600;
        max-width: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .slot-badge.booked .b-time {
        font-weight: 400;
        opacity: 0.7;
        font-size: var(--font-size-xs);
    }
    .slot-badge.booked .b-status {
        display: inline-block;
        background: rgba(255,255,255,0.5);
        padding: 0 var(--space-1);
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        color: var(--text-primary);
    }
    .slot-badge.pending {
        background: rgba(245, 158, 11, 0.08);
        color: #d97706;
        border-color: rgba(245, 158, 11, 0.12);
    }
    .slot-badge.rejected {
        background: rgba(239, 68, 68, 0.06);
        color: #dc2626;
        border-color: rgba(239, 68, 68, 0.08);
    }
    .slot-badge.weekend {
        background: rgba(148, 163, 184, 0.06);
        color: var(--text-muted);
        border-color: rgba(148, 163, 184, 0.10);
        cursor: not-allowed;
    }

    .legend-wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--space-4);
        padding: var(--space-2) var(--space-4);
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: var(--space-1);
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
    }
    .legend-item .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .legend-item .dot.available { background: var(--brand-green); }
    .legend-item .dot.booked { background: var(--brand-blue); }
    .legend-item .dot.pending { background: #f59e0b; }
    .legend-item .dot.rejected { background: #ef4444; }
    .legend-item .dot.current { background: var(--brand-orange); border: 2px solid var(--bg-card); box-shadow: 0 0 0 2px var(--brand-orange); }

    .no-results {
        padding: var(--space-7);
        text-align: center;
        color: var(--text-muted);
        font-size: var(--font-size-md);
    }
    .no-results i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: var(--space-3);
        color: var(--border-color);
    }

    .gallery-lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.92);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease;
        flex-direction: column;
    }
    .gallery-lightbox.active {
        display: flex;
    }
    .gallery-lightbox .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 85%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .gallery-lightbox .lightbox-content img {
        max-width: 85vw;
        max-height: 75vh;
        object-fit: contain;
        border-radius: var(--radius-sm);
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .gallery-lightbox .close-lightbox {
        position: absolute;
        top: -40px;
        right: -10px;
        font-size: 2.5rem;
        color: #fff;
        background: none;
        border: none;
        cursor: pointer;
        transition: transform var(--transition-fast);
        line-height: 1;
        z-index: 10;
    }
    .gallery-lightbox .close-lightbox:hover {
        transform: scale(1.2);
    }
    .gallery-lightbox .nav-lightbox {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        color: #fff;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        cursor: pointer;
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gallery-lightbox .nav-lightbox:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-50%) scale(1.05);
    }
    .gallery-lightbox .nav-lightbox.prev { left: 20px; }
    .gallery-lightbox .nav-lightbox.next { right: 20px; }
    .gallery-lightbox .lightbox-caption {
        margin-top: 16px;
        color: #fff;
        font-size: var(--font-size-sm);
        background: rgba(0,0,0,0.3);
        padding: var(--space-2) var(--space-4);
        border-radius: var(--radius-pill);
        max-width: 80%;
        text-align: center;
    }
    .gallery-lightbox .lightbox-counter {
        margin-top: 8px;
        color: rgba(255,255,255,0.6);
        font-size: var(--font-size-sm);
    }
    .gallery-lightbox .lightbox-thumbnails {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        max-width: 80%;
        overflow-x: auto;
        padding: 4px;
        background: rgba(0,0,0,0.2);
        border-radius: var(--radius-sm);
    }
    .gallery-lightbox .lightbox-thumbnails img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: var(--radius-xs);
        cursor: pointer;
        border: 2px solid transparent;
        transition: all var(--transition-fast);
    }
    .gallery-lightbox .lightbox-thumbnails img:hover {
        border-color: rgba(255,255,255,0.5);
    }
    .gallery-lightbox .lightbox-thumbnails img.active {
        border-color: var(--brand-orange);
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 991.98px) {
        .dashboard-content { padding: var(--space-3); }
        .filter-bar { flex-direction: column; align-items: stretch; padding: var(--space-3); }
        .filter-bar .form-group { min-width: 100%; }
        .filter-bar .work-hours { margin-left: 0; align-self: flex-start; }
        .filter-bar .btn-today { align-self: flex-start; }
        .filter-bar .btn-refresh { align-self: flex-start; }
        .table-schedule { font-size: var(--font-size-xs); min-width: 600px; }
        .table-schedule thead th { min-width: 100px; height: auto; padding: var(--space-1) var(--space-1); }
        .table-schedule thead th .room-header .room-photo { width: 60px; height: 60px; }
        .table-schedule thead th .room-header .room-photo-placeholder { width: 60px; height: 60px; font-size: 1.5rem; }
        .table-schedule tbody td { min-width: 100px; height: 44px; }
        .slot-badge { font-size: var(--font-size-xs); padding: var(--space-1) var(--space-1); min-height: 24px; }
        .slot-badge.booked .b-name { max-width: 40px; }
        .schedule-card .card-header .title .date-info { display: none; }
        .dashboard-header { flex-direction: column; align-items: flex-start; }
        .dashboard-header .actions { width: 100%; justify-content: flex-start; }
        .gallery-lightbox .nav-lightbox { width: 36px; height: 36px; font-size: 1.2rem; }
        .gallery-lightbox .nav-lightbox.prev { left: 8px; }
        .gallery-lightbox .nav-lightbox.next { right: 8px; }
        .gallery-lightbox .lightbox-content img { max-width: 92vw; max-height: 60vh; }
    }
    @media (max-width: 575.98px) {
        .dashboard-content { padding: var(--space-2); }
        .filter-bar { padding: var(--space-2); }
        .schedule-card .card-header { flex-direction: column; align-items: flex-start; }
        .schedule-card .card-body { max-height: 400px; }
        .table-schedule { font-size: var(--font-size-xs); min-width: 480px; }
        .table-schedule thead th { min-width: 70px; font-size: var(--font-size-xs); height: auto; padding: var(--space-1) var(--space-1); }
        .table-schedule thead th .room-header .room-photo { width: 44px; height: 44px; }
        .table-schedule thead th .room-header .room-photo-placeholder { width: 44px; height: 44px; font-size: 1rem; }
        .table-schedule thead th .room-header .room-name { font-size: var(--font-size-xs); }
        .table-schedule tbody td { min-width: 70px; height: 36px; padding: var(--space-1) var(--space-1); }
        .table-schedule tbody td:first-child { min-width: 40px; font-size: var(--font-size-xs); }
        .slot-badge { font-size: var(--font-size-xs); padding: var(--space-1) var(--space-1); min-height: 20px; }
        .slot-badge.booked .b-name { max-width: 30px; }
        .legend-wrapper { gap: var(--space-2); }
        .legend-item { font-size: var(--font-size-xs); }
        .schedule-card .card-header .title { font-size: var(--font-size-sm); }
        .dashboard-header .greeting h1 { font-size: var(--font-size-lg); }
        .dashboard-header .greeting p { font-size: var(--font-size-xs); }
        .gallery-lightbox .lightbox-thumbnails img { width: 36px; height: 36px; }
        .gallery-lightbox .nav-lightbox { width: 30px; height: 30px; font-size: 1rem; }
        .gallery-lightbox .close-lightbox { font-size: 2rem; top: -30px; right: 0; }
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .filter-bar { animation: fadeUp 0.3s ease forwards; }
    .schedule-card { animation: fadeUp 0.4s ease forwards; }

    .schedule-card .card-body::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    .schedule-card .card-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .schedule-card .card-body::-webkit-scrollbar-thumb {
        background: var(--text-muted);
        border-radius: var(--radius-pill);
    }
    .schedule-card .card-body::-webkit-scrollbar-thumb:hover {
        background: var(--text-secondary);
    }
    .schedule-card .card-body {
        scrollbar-width: thin;
        scrollbar-color: var(--text-muted) transparent;
    }
</style>

<div class="dashboard-content">

    @php
        $totalRooms = $allRooms->count();
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
        $totalSlots = count($timeSlots) * $totalRooms;
        $availableSlots = $totalSlots - $totalBookingsToday;

        $userName = Auth::user()->full_name ?? Auth::user()->name ?? 'User';
    @endphp

    <!-- ========== HEADER ========== -->
    <div class="dashboard-header">
        <div class="greeting">
            <h1>Selamat datang kembali, {{ $userName }}</h1>
            <p>
                <span><i class="bi bi-calendar3 me-1"></i> {{ Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <span>·</span>
                <span class="clock"><i class="bi bi-clock me-1"></i> <span id="liveClock">{{ Carbon::now()->format('H:i:s') }}</span> WIB</span>
                @if($isWeekend)
                    <span class="badge bg-warning" style="font-size:var(--font-size-xs); padding:2px 10px;">Libur</span>
                @endif
            </p>
        </div>
        <div class="actions">
            <a href="{{ route('bookings.create') }}" class="btn-primary-sm">
                <i class="bi bi-plus-lg"></i> Booking
            </a>
            <button class="btn-outline-sm" onclick="window.location.reload()">
                <i class="bi bi-arrow-repeat"></i> Refresh
            </button>
        </div>
    </div>

    <!-- ========== FILTER BAR ========== -->
    <div class="filter-bar">
        <div class="form-group">
            <label for="dateSelector">Tanggal</label>
            <input type="date" id="dateSelector" name="date" class="form-control" value="{{ $selectedDate }}" onchange="filterByDate(this.value)" {{ $isWeekend ? 'disabled' : '' }}>
        </div>
        <div class="form-group" style="min-width:120px;">
            <label for="roomFilter">Ruangan</label>
            <select id="roomFilter" name="room" class="form-select" onchange="filterByRoom(this.value)">
                <option value="all">Semua</option>
                @foreach($allRooms as $room)
                    <option value="{{ $room->id }}" {{ in_array($room->id, $selectedRoomIds) ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-today" onclick="resetToToday()">
            <i class="bi bi-arrow-clockwise"></i> Hari Ini
        </button>
        <button class="btn-refresh" onclick="window.location.reload()" title="Refresh">
            <i class="bi bi-arrow-repeat"></i>
        </button>
        <div class="filter-spacer"></div>
        <div class="work-hours">
            <i class="bi bi-clock"></i> 07:00 – 16:00 WIB
        </div>
    </div>

    <!-- ========== SCHEDULE TABLE ========== -->
    <div class="schedule-card">
        <div class="card-header">
            <div class="title">
                <i class="bi bi-table"></i> Jadwal Peminjaman Ruangan
                <span class="date-info">
                    {{ Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </span>
            </div>
            <div class="header-right">
                @if($isWeekend)
                    <span style="color:var(--text-muted);"><i class="bi bi-lock"></i> Libur</span>
                @endif
                @if($isToday && !$isWeekend && $currentSlot)
                    <span class="current-badge">
                        <i class="bi bi-dot"></i> {{ $currentSlot }}
                    </span>
                @endif
                <span style="font-size:var(--font-size-xs); color:var(--text-muted);">
                    {{ count($timeSlots) }} slot · {{ $rooms->count() }} ruangan
                </span>
            </div>
        </div>
        <div class="card-body">
            @php
                $hasResults = false;
                foreach ($bookingSchedule as $roomId => $slots) {
                    foreach ($slots as $time => $booking) {
                        if ($booking) {
                            $hasResults = true;
                            break 2;
                        }
                    }
                }
            @endphp

            @if($hasResults || count($timeSlots) > 0)
                <table class="table-schedule" id="scheduleTable">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            @foreach($rooms as $room)
                                @php
                                    $photos = $room->photos ?? collect();
                                    $hasPhoto = $photos->count() > 0;
                                    $firstPhoto = $hasPhoto ? $photos->first()->photo_url : null;
                                    $photoCount = $photos->count();
                                @endphp
                                <th>
                                    <div class="room-header">
                                        @if($hasPhoto)
                                            <img src="{{ $firstPhoto }}"
                                                 class="room-photo"
                                                 onclick="openGallery({{ $room->id }}, {{ $loop->index }})"
                                                 alt="{{ $room->name }}"
                                                 title="Klik untuk lihat foto"
                                                 loading="lazy">
                                            <span class="photo-count" onclick="openGallery({{ $room->id }}, {{ $loop->index }})">
                                                <i class="bi bi-images"></i> {{ $photoCount }}
                                            </span>
                                        @else
                                            <div class="room-photo-placeholder">
                                                <i class="bi bi-building"></i>
                                            </div>
                                        @endif
                                        <div class="room-name">{{ $room->name }}</div>
                                        <div class="room-capacity">
                                            <i class="bi bi-people"></i> {{ $room->capacity }}
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $timeSlot)
                            @php
                                $isCurrent = ($isToday && !$isWeekend && $timeSlot == $currentSlot);
                            @endphp
                            <tr class="{{ $isCurrent ? 'current-time' : '' }}">
                                <td>{{ $timeSlot }}</td>
                                @foreach($rooms as $room)
                                    @php
                                        $booking = $bookingSchedule[$room->id][$timeSlot] ?? null;
                                        $statusClass = 'available';
                                        $statusIcon = '<i class="bi bi-check-circle"></i>';
                                        $extra = '';
                                        $isBooked = false;

                                        if ($isWeekend) {
                                            $statusClass = 'weekend';
                                            $statusIcon = '<i class="bi bi-calendar-x"></i>';
                                            $extra = 'Libur';
                                        } elseif ($booking) {
                                            if ($booking->status == 0) {
                                                $statusClass = 'pending';
                                                $statusIcon = '<i class="bi bi-clock"></i>';
                                                $extra = 'Pending';
                                            } elseif ($booking->status == 1) {
                                                $statusClass = 'booked';
                                                $isBooked = true;
                                                $extra = '
                                                    <span class="b-name">' . e($booking->user->full_name ?? 'Unknown') . '</span>
                                                    <span class="b-time">' . Carbon::parse($booking->start_time)->format('H:i') . '–' . Carbon::parse($booking->end_time)->format('H:i') . '</span>
                                                    <span class="b-status">Disetujui</span>
                                                ';
                                            } elseif ($booking->status == 2) {
                                                $statusClass = 'rejected';
                                                $statusIcon = '<i class="bi bi-x-circle"></i>';
                                                $extra = 'Ditolak';
                                            }
                                        }
                                    @endphp
                                    <td>
                                        @if($isBooked)
                                            <div class="slot-badge booked" onclick="showBookingDetails({{ $booking->id }})" title="Klik untuk detail">
                                                {!! $extra !!}
                                            </div>
                                        @elseif($booking && $booking->status == 0)
                                            <div class="slot-badge pending" title="Pending">
                                                <i class="bi bi-clock"></i> {{ $extra }}
                                                <span style="font-size:var(--font-size-xs);display:block;opacity:0.7;">
                                                    {{ $booking->user->full_name ?? 'Unknown' }}
                                                </span>
                                            </div>
                                        @elseif($booking && $booking->status == 2)
                                            <div class="slot-badge rejected" title="Ditolak">
                                                <i class="bi bi-x-circle"></i> {{ $extra }}
                                                <span style="font-size:var(--font-size-xs);display:block;opacity:0.7;">
                                                    {{ $booking->user->full_name ?? 'Unknown' }}
                                                </span>
                                            </div>
                                        @elseif($isWeekend)
                                            <div class="slot-badge weekend" title="Hari libur">
                                                <i class="bi bi-calendar-x"></i> {{ $extra }}
                                            </div>
                                        @else
                                            <div class="slot-badge available" title="Klik untuk booking" onclick="quickBook({{ $room->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}')">
                                                <i class="bi bi-check-circle"></i> Tersedia
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-results">
                    <i class="bi bi-inbox"></i>
                    <p>Tidak ada jadwal untuk tanggal yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ========== LEGEND ========== -->
    <div style="margin-top:var(--space-4); background:var(--bg-card); border-radius:var(--radius-card); border:1px solid var(--border-color-light); box-shadow:var(--shadow-card);">
        <div class="legend-wrapper">
            <div class="legend-item"><span class="dot available"></span> Tersedia (klik)</div>
            <div class="legend-item"><span class="dot booked"></span> Disetujui</div>
            <div class="legend-item"><span class="dot pending"></span> Pending</div>
            <div class="legend-item"><span class="dot rejected"></span> Ditolak</div>
            @if($isToday && !$isWeekend)
                <div class="legend-item"><span class="dot current"></span> Sekarang</div>
            @endif
            @if($isWeekend)
                <div class="legend-item"><span style="color:var(--text-muted);"><i class="bi bi-calendar-x"></i></span> Libur</div>
            @endif
        </div>
    </div>

</div>

<!-- ========== LIGHTBOX ========== -->
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

        // ========== LIGHTBOX DATA (dari controller) ==========
        var roomsData = @json($roomsData);

        var currentRoomIndex = 0;
        var currentPhotoIndex = 0;
        var currentPhotos = [];

        window.openGallery = function(roomId, index) {
            var room = roomsData.find(function(r) { return r.id == roomId; });
            if (!room || !room.photos || room.photos.length === 0) {
                return;
            }

            currentRoomIndex = index;
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
                thumb.onclick = function() {
                    currentPhotoIndex = idx;
                    updateLightbox(roomName);
                };
                thumbs.appendChild(thumb);
            });

            var activeThumb = thumbs.querySelector('.active');
            if (activeThumb) {
                activeThumb.scrollIntoView({ block: 'nearest', inline: 'center' });
            }
        }

        window.closeLightbox = function() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        };

        window.navigateLightbox = function(direction) {
            var newIndex = currentPhotoIndex + direction;
            if (newIndex < 0 || newIndex >= currentPhotos.length) {
                return;
            }
            currentPhotoIndex = newIndex;
            var room = roomsData[currentRoomIndex];
            updateLightbox(room ? room.name : '');
        };

        document.addEventListener('keydown', function(e) {
            if (document.getElementById('lightbox').classList.contains('active')) {
                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    navigateLightbox(-1);
                } else if (e.key === 'ArrowRight') {
                    navigateLightbox(1);
                }
            }
        });

        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this || e.target === document.querySelector('.lightbox-content')) {
                closeLightbox();
            }
        });

        // ========== FILTER FUNCTIONS ==========
        window.filterByDate = function(selectedDate) {
            const url = new URL(window.location);
            url.searchParams.set('date', selectedDate);
            window.location.href = url.toString();
        };

        window.filterByRoom = function(roomId) {
            const url = new URL(window.location);
            if (roomId !== 'all') {
                url.searchParams.set('room', roomId);
            } else {
                url.searchParams.delete('room');
            }
            window.location.href = url.toString();
        };

        window.resetToToday = function() {
            const today = new Date().toISOString().split('T')[0];
            const input = document.getElementById('dateSelector');
            if (input) {
                input.value = today;
            }
            const url = new URL(window.location);
            url.searchParams.set('date', today);
            url.searchParams.delete('room');
            window.location.href = url.toString();
        };

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

        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const el = document.getElementById('liveClock');
            if (el) {
                el.textContent = h + ':' + m + ':' + s;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        const currentRow = document.querySelector('.table-schedule tbody tr.current-time');
        if (currentRow) {
            const container = currentRow.closest('.card-body');
            if (container) {
                setTimeout(function() {
                    const rowTop = currentRow.offsetTop - container.offsetTop - 40;
                    container.scrollTop = Math.max(0, rowTop);
                }, 350);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof bootstrap !== 'undefined') {
                    document.querySelectorAll('[title]').forEach(el => new bootstrap.Tooltip(el, {
                        placement: 'top',
                        delay: { show: 300, hide: 100 }
                    }));
                }
            });
        } else {
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[title]').forEach(el => new bootstrap.Tooltip(el, {
                    placement: 'top',
                    delay: { show: 300, hide: 100 }
                }));
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        const roomParam = urlParams.get('room');
        if (roomParam) {
            const select = document.getElementById('roomFilter');
            if (select) {
                select.value = roomParam;
            }
        }

        document.addEventListener('keydown', function(e) {
            const input = document.getElementById('dateSelector');
            if (!input) return;
            if (e.key === 'ArrowLeft' && e.ctrlKey) {
                e.preventDefault();
                const d = new Date(input.value);
                d.setDate(d.getDate() - 1);
                input.value = d.toISOString().split('T')[0];
                filterByDate(input.value);
            } else if (e.key === 'ArrowRight' && e.ctrlKey) {
                e.preventDefault();
                const d = new Date(input.value);
                d.setDate(d.getDate() + 1);
                input.value = d.toISOString().split('T')[0];
                filterByDate(input.value);
            }
        });

    })();
</script>
@endsection