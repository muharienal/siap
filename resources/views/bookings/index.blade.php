@php
    use Carbon\Carbon;
@endphp

@extends('templates.template')

@section('page_title', 'Peminjaman Ruangan')
@section('page_subtitle', 'Kelola semua peminjaman ruangan')

@section('content')
<style>
    .bookings-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }

    .filter-bar {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        padding: var(--space-4) var(--space-5);
        margin-bottom: var(--space-5);
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: var(--space-3);
        border: none;
        box-shadow: var(--shadow-card);
        min-height: 72px;
    }
    .filter-bar .form-group {
        flex: 0 0 auto;
        min-width: 140px;
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
        box-shadow: 0 0 0 3px rgba(249,115,22,0.06);
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
        box-shadow: 0 2px 8px rgba(249,115,22,0.15);
    }
    .filter-bar .btn-today:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16,185,129,0.20);
    }
    .filter-bar .btn-reset {
        height: 38px;
        padding: 0 var(--space-4);
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
        white-space: nowrap;
    }
    .filter-bar .btn-reset:hover {
        border-color: var(--brand-orange);
        color: var(--brand-orange-dark);
        background: rgba(249,115,22,0.04);
    }
    .filter-bar .filter-spacer {
        flex: 1;
    }

    .table-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .table-card .card-header {
        padding: var(--space-3) var(--space-5);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
        min-height: 48px;
    }
    .table-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .table-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
    .table-card .card-header .actions {
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .table-card .card-body {
        padding: 0;
        overflow-x: auto;
        position: relative;
    }

    .table-booking {
        width: 100%;
        border-collapse: collapse;
        font-size: var(--font-size-sm);
        min-width: 800px;
    }
    .table-booking thead th {
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
        border-bottom: 1px solid var(--border-color-light);
        text-align: center;
        white-space: nowrap;
        height: 38px;
    }
    .table-booking tbody td {
        padding: var(--space-2) var(--space-2);
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color-light);
        height: 48px;
        background: var(--bg-card);
        transition: background var(--transition-fast);
    }
    .table-booking tbody tr:nth-child(even) td {
        background: #fafbfc;
    }
    .table-booking tbody tr:hover td {
        background: rgba(249,115,22,0.03);
    }

    .badge-status {
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        display: inline-block;
        text-align: center;
        min-width: 80px;
    }
    .badge-status.pending {
        background: rgba(245,158,11,0.12);
        color: #d97706;
    }
    .badge-status.approved {
        background: rgba(16,185,129,0.12);
        color: var(--brand-green-dark);
    }
    .badge-status.rejected {
        background: rgba(239,68,68,0.08);
        color: #dc2626;
    }

    .btn-action {
        padding: 4px 10px;
        font-size: var(--font-size-xs);
        border-radius: var(--radius-sm);
        border: none;
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        font-weight: 500;
    }
    .btn-action.detail {
        background: rgba(59,130,246,0.08);
        color: var(--brand-blue-dark);
    }
    .btn-action.detail:hover {
        background: rgba(59,130,246,0.16);
    }
    .btn-action.edit {
        background: rgba(245,158,11,0.08);
        color: #d97706;
    }
    .btn-action.edit:hover {
        background: rgba(245,158,11,0.16);
    }
    .btn-action.qr {
        background: rgba(16,185,129,0.08);
        color: var(--brand-green-dark);
    }
    .btn-action.qr:hover {
        background: rgba(16,185,129,0.16);
    }

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

    @media (max-width: 991.98px) {
        .bookings-content { padding: var(--space-3); }
        .filter-bar { flex-direction: column; align-items: stretch; padding: var(--space-3); }
        .filter-bar .form-group { min-width: 100%; }
        .filter-bar .btn-today { align-self: flex-start; }
        .filter-bar .btn-reset { align-self: flex-start; }
        .table-booking { font-size: var(--font-size-xs); min-width: 600px; }
        .table-booking thead th { height: 34px; padding: var(--space-1) var(--space-1); }
        .table-booking tbody td { height: 40px; padding: var(--space-1) var(--space-1); }
        .badge-status { font-size: var(--font-size-xs); padding: 2px 8px; min-width: 60px; }
        .btn-action { font-size: var(--font-size-xs); padding: 2px 8px; }
    }
    @media (max-width: 575.98px) {
        .bookings-content { padding: var(--space-2); }
        .filter-bar { padding: var(--space-2); }
        .table-card .card-header { flex-direction: column; align-items: flex-start; }
        .table-card .card-header .actions { width: 100%; justify-content: flex-start; flex-wrap: wrap; }
        .table-booking { font-size: var(--font-size-xs); min-width: 480px; }
        .table-booking thead th { font-size: var(--font-size-xs); height: 30px; padding: var(--space-1) var(--space-1); }
        .table-booking tbody td { height: 36px; padding: var(--space-1) var(--space-1); }
        .badge-status { font-size: 10px; padding: 2px 6px; min-width: 50px; }
        .btn-action { font-size: 10px; padding: 2px 6px; }
    }
</style>

<div class="bookings-content">

    <div class="greeting-section" style="margin-bottom: var(--space-5);">
        <h1 class="greeting-title">Peminjaman Ruangan</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-calendar3 me-1"></i> {{ Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            <span>·</span>
            <span class="clock"><i class="bi bi-clock me-1"></i> <span id="liveClock">{{ Carbon::now()->format('H:i:s') }}</span> WIB</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <form class="filter-bar" method="GET" action="{{ route('bookings.index') }}">
        <div class="form-group">
            <label for="statusFilter"><i class="bi bi-funnel me-1"></i> Status</label>
            <select name="status" id="statusFilter" class="form-select">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="form-group">
            <label for="roomFilter"><i class="bi bi-door-open me-1"></i> Ruangan</label>
            <select name="room" id="roomFilter" class="form-select">
                <option value="">Semua</option>
                @foreach($rooms ?? [] as $room)
                    <option value="{{ $room->id }}" {{ request('room') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="dateFilter"><i class="bi bi-calendar-date me-1"></i> Tanggal</label>
            <input type="date" name="date" id="dateFilter" class="form-control" value="{{ request('date') }}">
        </div>

        <div class="form-group" style="flex:1; min-width:180px;">
            <label for="searchFilter"><i class="bi bi-search me-1"></i> Cari</label>
            <input type="text" name="search" id="searchFilter" class="form-control" placeholder="Nama, tujuan, ruangan..." value="{{ request('search') }}">
        </div>

        <button type="submit" class="btn-today">
            <i class="bi bi-search"></i> Filter
        </button>
        <a href="{{ route('bookings.index') }}" class="btn-reset">
            <i class="bi bi-arrow-counterclockwise"></i> Reset
        </a>
        <div class="filter-spacer"></div>
        <a href="{{ route('bookings.create') }}" class="btn-today" style="background: var(--brand-gradient);">
            <i class="bi bi-plus-lg"></i> Tambah Peminjaman
        </a>
    </form>

    <!-- Table -->
    <div class="table-card">
        <div class="card-header">
            <div class="title">
                <i class="bi bi-table"></i> Daftar Peminjaman
                <span style="font-weight:400; font-size:var(--font-size-sm); color:var(--text-muted); margin-left:var(--space-1);">
                    {{ $bookings->total() }} total
                </span>
            </div>
            <div class="actions">
                <span style="font-size:var(--font-size-xs); color:var(--text-muted);">
                    {{ $bookings->firstItem() ?? 0 }} – {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }}
                </span>
            </div>
        </div>
        <div class="card-body">
            @if($bookings->count() > 0)
                <table class="table-booking">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>Peminjam</th>
                            <th>Ruangan</th>
                            <th style="min-width:120px;">Tanggal</th>
                            <th style="min-width:110px;">Waktu</th>
                            <th style="min-width:100px;">Status</th>
                            <th style="min-width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($bookings->currentPage() - 1) * $bookings->perPage() }}</td>
                                <td>
                                    @if(Auth::user()->role == 1)
                                        {{ $item->user->employee->full_name ?? $item->user->name }}
                                    @else
                                        {{ Auth::user()->employee->full_name ?? Auth::user()->name }}
                                    @endif
                                </td>
                                <td>{{ $item->room->name }}</td>
                                <td>{{ Carbon::parse($item->start_time)->format('d M Y') }}</td>
                                <td>{{ Carbon::parse($item->start_time)->format('H:i') }} – {{ Carbon::parse($item->end_time)->format('H:i') }}</td>
                                <td>
                                    @if($item->status == 0)
                                        <span class="badge-status pending">Pending</span>
                                    @elseif($item->status == 1)
                                        <span class="badge-status approved">Disetujui</span>
                                    @elseif($item->status == 2)
                                        <span class="badge-status rejected">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex; gap:4px; justify-content:center; flex-wrap:wrap;">
                                        <a href="/bookings/{{ $item->id }}" class="btn-action detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        @if($item->status == 0)
                                            <a href="/bookings/{{ $item->id }}/edit" class="btn-action edit">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        @endif
                                        @if($item->status == 1 && $item->absent_code)
                                            <button type="button" class="btn-action qr" onclick="showQrCode({{ $item->id }})">
                                                <i class="bi bi-qr-code"></i> QR
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-results">
                    <i class="bi bi-inbox"></i>
                    <p>Tidak ada peminjaman ditemukan</p>
                    <p style="font-size:var(--font-size-sm); margin-top:var(--space-2);">
                        <a href="{{ route('bookings.create') }}" style="color:var(--brand-orange); text-decoration:none; font-weight:600;">
                            <i class="bi bi-plus-circle"></i> Buat peminjaman baru
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
        <div style="margin-top:var(--space-4); display:flex; justify-content:center;">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    @endif

</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:var(--radius-card); box-shadow:var(--shadow-dropdown);">
            <div class="modal-header" style="padding:var(--space-4) var(--space-5); border-bottom:1px solid var(--border-color-light);">
                <h5 class="modal-title" id="qrCodeModalLabel" style="font-weight:700; font-size:var(--font-size-md);">
                    <i class="bi bi-qr-code me-2" style="color:var(--brand-orange);"></i> QR Code Absensi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding:var(--space-5);">
                <div id="qrCodeContent">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="bookingInfo" class="mt-4" style="display:none;">
                    <div class="card" style="border:1px solid var(--border-color-light); border-radius:var(--radius-sm);">
                        <div class="card-body text-start" style="padding:var(--space-4);">
                            <h6 style="font-weight:600; font-size:var(--font-size-sm); margin-bottom:var(--space-2);">Detail Meeting</h6>
                            <div id="meetingDetails" style="font-size:var(--font-size-sm); color:var(--text-secondary);"></div>
                        </div>
                    </div>
                </div>
                <div id="attendanceUrl" class="mt-3" style="display:none;">
                    <label style="font-weight:600; font-size:var(--font-size-xs); color:var(--text-muted);">Link Absensi</label>
                    <div style="display:flex; gap:var(--space-2); align-items:center; flex-wrap:wrap;">
                        <input type="text" id="urlInput" class="form-control" readonly style="flex:1; min-width:200px; height:38px;">
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()" style="height:38px;">
                            <i class="bi bi-copy"></i> Salin
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="downloadQrCode()" style="height:38px;">
                            <i class="bi bi-download"></i> Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        'use strict';

        let currentQrCode = '';

        window.showQrCode = function(bookingId) {
            const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
            modal.show();

            const qrContent = document.getElementById('qrCodeContent');
            const bookingInfo = document.getElementById('bookingInfo');
            const attendanceUrl = document.getElementById('attendanceUrl');

            qrContent.innerHTML = `<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`;
            bookingInfo.style.display = 'none';
            attendanceUrl.style.display = 'none';

            fetch(`/bookings/${bookingId}/qr-code`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentQrCode = data.qr_code;
                    qrContent.innerHTML = data.qr_code;

                    const details = document.getElementById('meetingDetails');
                    details.innerHTML = `
                        <div><strong>Ruangan:</strong> ${data.booking_info.room}</div>
                        <div><strong>Tanggal:</strong> ${data.booking_info.date}</div>
                        <div><strong>Waktu:</strong> ${data.booking_info.time}</div>
                        <div><strong>Keperluan:</strong> ${data.booking_info.purpose}</div>
                    `;
                    bookingInfo.style.display = 'block';

                    document.getElementById('urlInput').value = data.attendance_url;
                    attendanceUrl.style.display = 'block';
                } else {
                    qrContent.innerHTML = `<div class="alert alert-danger">${data.message || 'Gagal memuat QR Code'}</div>`;
                }
            })
            .catch(() => {
                qrContent.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan saat memuat QR Code</div>`;
            });
        };

        window.copyToClipboard = function() {
            const input = document.getElementById('urlInput');
            input.select();
            navigator.clipboard?.writeText(input.value).then(() => {
                const btn = event?.target?.closest('button');
                if (btn) {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check"></i> Tersalin!';
                    setTimeout(() => btn.innerHTML = orig, 2000);
                }
            }).catch(() => {});
        };

        window.downloadQrCode = function() {
            if (!currentQrCode) return alert('QR Code belum tersedia');
            const blob = new Blob([currentQrCode], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'qr-code-absensi.svg';
            a.click();
            URL.revokeObjectURL(url);
        };

        // Live clock
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2,'0');
            const m = String(now.getMinutes()).padStart(2,'0');
            const s = String(now.getSeconds()).padStart(2,'0');
            const el = document.getElementById('liveClock');
            if (el) el.textContent = h + ':' + m + ':' + s;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Tooltips
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[title]').forEach(el => new bootstrap.Tooltip(el));
            }
        });
    })();
</script>
@endsection