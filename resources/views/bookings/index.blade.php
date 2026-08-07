@extends('templates.template')

@section('page_title', 'Peminjaman Ruangan')
@section('page_subtitle', 'Kelola seluruh aktivitas peminjaman ruangan')

@section('content')
<style>
    .booking-content {
        padding: var(--space-6) var(--space-7);
        max-width: 1680px;
        margin: 0 auto;
        width: 100%;
        padding-bottom: 100px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: var(--space-4);
        margin-bottom: var(--space-6);
    }
    .stat-mini {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        padding: var(--space-4);
        display: flex;
        align-items: center;
        gap: var(--space-3);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-mini:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }
    .stat-mini-icon {
        width: 44px; height: 44px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .stat-mini-icon.blue { background: rgba(59,130,246,0.1); color: var(--brand-blue); }
    .stat-mini-icon.amber { background: rgba(245,158,11,0.12); color: #b45309; }
    .stat-mini-icon.green { background: rgba(16,185,129,0.1); color: var(--brand-green-dark); }
    .stat-mini-icon.red { background: rgba(239,68,68,0.1); color: #b91c1c; }
    .stat-mini-icon.purple { background: rgba(139,92,246,0.1); color: #7c3aed; }
    .stat-mini-value { font-size: var(--font-size-xl); font-weight: 800; color: var(--text-primary); line-height: 1; }
    .stat-mini-label { font-size: 11px; color: var(--text-secondary); font-weight: 600; margin-top: 2px; }

    /* Toolbar */
    .booking-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: var(--space-4);
        margin-bottom: var(--space-5);
    }
    .booking-toolbar h1 { font-size: var(--font-size-2xl); font-weight: 800; color: var(--text-primary); margin: 0; }
    .booking-toolbar .actions { display: flex; gap: var(--space-3); flex-wrap: wrap; }

    .btn-primary-sm {
        height: 42px;
        padding: 0 var(--space-5);
        background: var(--brand-gradient);
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-inverse);
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        text-decoration: none;
        box-shadow: 0 2px 10px rgba(249, 115, 22, 0.16);
        transition: all 0.2s;
    }
    .btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16, 185, 129, 0.2); color: var(--text-inverse); }

    .btn-outline-sm {
        height: 42px;
        padding: 0 var(--space-5);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-outline-sm:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    /* Filter Bar */
    .filter-bar {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: var(--space-4) var(--space-5);
        margin-bottom: var(--space-5);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: var(--space-3);
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
    .filter-select, .filter-input {
        height: 40px;
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        min-width: 150px;
    }
    .filter-search {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .filter-search input {
        width: 100%;
        height: 40px;
        padding: 0 var(--space-3) 0 38px;
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
    }
    .filter-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    /* Bulk Action Bar */
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
    }
    .bulk-action-bar.show { display: flex; }
    .bulk-action-bar .selected-count { font-weight: 700; }
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

    /* Booking Cards (Mobile) */
    .booking-cards { display: none; flex-direction: column; gap: var(--space-3); }
    .booking-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: var(--space-4);
        border-left: 4px solid transparent;
        transition: all 0.2s;
    }
    .booking-card:active { transform: scale(0.99); }
    .booking-card.status-pending { border-left-color: #f59e0b; }
    .booking-card.status-approved { border-left-color: #10b981; }
    .booking-card.status-rejected { border-left-color: #ef4444; }

    .booking-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--space-3);
        margin-bottom: var(--space-3);
    }
    .booking-card-title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
    }
    .booking-card-time {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .booking-card-body {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        padding: var(--space-3) 0;
        border-top: 1px solid var(--border-color-light);
    }
    .booking-card-actions {
        display: flex;
        gap: var(--space-2);
        margin-top: var(--space-3);
    }

    /* Booking Table (Desktop) */
    .booking-table-wrap {
        display: block;
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .booking-table {
        width: 100%;
        margin-bottom: 0;
    }
    .booking-table thead {
        background: var(--bg-body);
        border-bottom: 2px solid var(--border-color);
    }
    .booking-table thead th {
        padding: var(--space-3) var(--space-4);
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
        padding: var(--space-3) var(--space-4);
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        vertical-align: middle;
    }
    .booking-table .room-cell { font-weight: 600; }
    .booking-table .purpose-cell { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .booking-table .checkbox-cell { width: 40px; text-align: center; }

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

    /* Action Buttons */
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
    .action-btn.approve:hover { border-color: #10b981; color: #10b981; }
    .action-btn.reject:hover { border-color: #ef4444; color: #ef4444; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: var(--space-9) var(--space-4);
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: 2px dashed var(--border-color);
    }
    .empty-state i { font-size: 3rem; color: var(--border-color); margin-bottom: var(--space-4); }
    .empty-state h4 { font-weight: 700; color: var(--text-primary); margin-bottom: var(--space-2); }
    .empty-state p { color: var(--text-muted); margin-bottom: var(--space-4); }

    /* Mobile Bottom Bar */
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
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 991.98px) {
        .booking-content { padding: var(--space-4); padding-bottom: 90px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: var(--space-3); }
        .booking-toolbar { flex-direction: column; align-items: stretch; }
        .booking-toolbar .actions { display: none; }
        .filter-bar { display: none; }
        .booking-cards { display: flex; }
        .booking-table-wrap { display: none; }
        .mobile-action-bar { display: block; }
        .mobile-filter-btn { display: flex !important; }
    }
    @media (min-width: 992px) {
        .booking-cards { display: none !important; }
        .mobile-filter-btn { display: none !important; }
        .offcanvas-filter { display: none !important; }
    }
    @media (max-width: 575.98px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .stat-mini { padding: var(--space-3); }
        .stat-mini-icon { width: 36px; height: 36px; font-size: 1rem; }
        .stat-mini-value { font-size: var(--font-size-lg); }
    }
</style>

<div class="booking-content">

    {{-- ========== TOOLBAR ========== --}}
    <div class="booking-toolbar">
        <div>
            <h1>Peminjaman Ruangan</h1>
            <p class="text-muted mb-0 mt-1">
                <i class="bi bi-calendar-check"></i>
                Kelola seluruh aktivitas peminjaman ruangan
            </p>
        </div>
        <div class="actions">
            @if(Auth::user()->role == 1)
            <a href="{{ route('bookings.export', request()->query()) }}" class="btn-outline-sm">
                <i class="bi bi-download"></i> Export
            </a>
            @endif
            <a href="{{ route('bookings.create') }}" class="btn-primary-sm">
                <i class="bi bi-plus-lg"></i> Tambah Peminjaman
            </a>
        </div>
    </div>

    {{-- ========== STATS CARDS ========== --}}
    <div class="stats-grid">
        <div class="stat-mini">
            <div class="stat-mini-icon blue"><i class="bi bi-clipboard-data"></i></div>
            <div>
                <div class="stat-mini-value">{{ $stats['total'] }}</div>
                <div class="stat-mini-label">Total Booking</div>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-mini-value">{{ $stats['pending'] }}</div>
                <div class="stat-mini-label">Pending</div>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon green"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-mini-value">{{ $stats['approved'] }}</div>
                <div class="stat-mini-label">Disetujui</div>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon red"><i class="bi bi-x-circle"></i></div>
            <div>
                <div class="stat-mini-value">{{ $stats['rejected'] }}</div>
                <div class="stat-mini-label">Ditolak</div>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon purple"><i class="bi bi-calendar-event"></i></div>
            <div>
                <div class="stat-mini-value">{{ $stats['today'] }}</div>
                <div class="stat-mini-label">Hari Ini</div>
            </div>
        </div>
    </div>

    {{-- ========== FILTER BAR (Desktop) ========== --}}
    <div class="filter-bar">
        <form action="{{ route('bookings.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-3 w-100">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Ruangan</label>
                <select name="room" class="filter-select">
                    <option value="">Semua</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
            </div>

            <div class="filter-group">
                <label class="filter-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
            </div>

            <div class="filter-group">
                <label class="filter-label">Urutkan</label>
                <select name="sort" class="filter-select">
                    <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal ↑</option>
                    <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Tanggal ↓</option>
                </select>
            </div>

            <div class="filter-search">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Cari tujuan, nama, NIP..." value="{{ request('search') }}">
            </div>

            <div class="d-flex gap-2 align-self-end">
                <button type="submit" class="btn-primary-sm">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('bookings.index') }}" class="btn-outline-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ========== MOBILE FILTER BUTTON ========== --}}
    <div class="d-grid mb-4 mobile-filter-btn" style="display: none;">
        <button class="btn btn-outline-primary btn-lg" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
            <i class="bi bi-funnel"></i> Filter & Pencarian
            @if(request()->hasAny(['status', 'room', 'date_from', 'date_to', 'search', 'sort']))
                <span class="badge bg-danger ms-2">Aktif</span>
            @endif
        </button>
    </div>

    {{-- ========== MOBILE FILTER OFFCANVAS ========== --}}
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="filterOffcanvas" style="height: auto; max-height: 85vh;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title"><i class="bi bi-funnel"></i> Filter Peminjaman</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('bookings.index') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select form-select-lg">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Ruangan</label>
                    <select name="room" class="form-select form-select-lg">
                        <option value="">Semua Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ request('room') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Dari</label>
                        <input type="date" name="date_from" class="form-control form-control-lg" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Sampai</label>
                        <input type="date" name="date_to" class="form-control form-control-lg" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pencarian</label>
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="Tujuan, nama, NIP..." value="{{ request('search') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Urutkan</label>
                    <select name="sort" class="form-select form-select-lg">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal Terdekat</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Tanggal Terjauh</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg flex-fill">
                        <i class="bi bi-check-lg"></i> Terapkan
                    </button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ========== BULK ACTION BAR ========== --}}
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

    {{-- ========== BOOKING LIST ========== --}}
    @if($bookings->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>Tidak ada peminjaman ditemukan</h4>
            <p>@if(request()->hasAny(['status', 'room', 'date_from', 'date_to', 'search'])) Coba ubah filter pencarian Anda @else Mulai buat peminjaman baru untuk ruangan yang Anda butuhkan @endif</p>
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
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        @php
                            $statusClass = $booking->status == 0 ? 'pending' : ($booking->status == 1 ? 'approved' : 'rejected');
                            $statusLabel = $booking->status == 0 ? 'Pending' : ($booking->status == 1 ? 'Disetujui' : 'Ditolak');
                        @endphp
                        <tr class="booking-row" data-id="{{ $booking->id }}">
                            @if(Auth::user()->role == 1)
                            <td class="checkbox-cell">
                                <input type="checkbox" class="form-check-input booking-checkbox" value="{{ $booking->id }}" onchange="updateBulkBar()" {{ $booking->status != 0 ? 'disabled' : '' }}>
                            </td>
                            @endif
                            <td class="room-cell">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stat-mini-icon blue" style="width: 36px; height: 36px; font-size: 1rem;">
                                        <i class="bi bi-door-open"></i>
                                    </div>
                                    <div>
                                        <div>{{ $booking->room->name }}</div>
                                        <small class="text-muted">Kapasitas {{ $booking->room->capacity }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
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
                            <td class="purpose-cell" title="{{ $booking->purpose }}">{{ $booking->purpose }}</td>
                            <td>
                                <span class="status-badge status-{{ $statusClass }}">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="action-btn view" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(Auth::user()->role == 1 || (Auth::user()->role == 2 && $booking->status == 0))
                                    <a href="{{ route('bookings.edit', $booking->id) }}" class="action-btn edit" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    @if(Auth::user()->role == 1 && $booking->status == 0)
                                    <button type="button" class="action-btn approve" title="Setujui" onclick="quickApprove({{ $booking->id }})">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    @endif
                                    <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
                @endphp
                <div class="booking-card status-{{ $statusClass }}">
                    <div class="booking-card-header">
                        <div>
                            <div class="booking-card-title">{{ $booking->room->name }}</div>
                            <div class="booking-card-time">
                                <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}
                                <i class="bi bi-clock ms-2"></i> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                        </div>
                        <span class="status-badge status-{{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="booking-card-body">
                        <div class="mb-1"><strong>Tujuan:</strong> {{ Str::limit($booking->purpose, 80) }}</div>
                        <div class="text-muted small">
                            <i class="bi bi-person"></i> {{ $booking->user->full_name ?? 'Unknown' }}
                        </div>
                    </div>
                    <div class="booking-card-actions">
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        @if(Auth::user()->role == 1 || (Auth::user()->role == 2 && $booking->status == 0))
                        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                        @if(Auth::user()->role == 1 && $booking->status == 0)
                        <button type="button" class="btn btn-success btn-sm" onclick="quickApprove({{ $booking->id }})">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- Mobile Action Bar --}}
<div class="mobile-action-bar">
    <a href="{{ route('bookings.create') }}" class="btn-book-mobile">
        <i class="bi bi-plus-lg"></i> Tambah Peminjaman
    </a>
</div>

<script>
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
        document.getElementById('selectAll').checked = false;
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

    function quickApprove(bookingId) {
        if (!confirm('Setujui booking ini?')) return;

        fetch(`/bookings/${bookingId}/approve`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
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
            window.location.reload();
        });
    }
</script>
@endsection