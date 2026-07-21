@extends('templates.template')

@section('page_title', 'Detail Peminjaman')
@section('page_subtitle', 'Informasi lengkap peminjaman ruangan')

@section('content')
<style>
    .detail-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1200px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .detail-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
        margin-bottom: var(--space-5);
    }
    .detail-card .card-header {
        padding: var(--space-4) var(--space-5);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
    }
    .detail-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .detail-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
    .detail-card .card-body {
        padding: var(--space-5);
    }
    .detail-table {
        width: 100%;
        border-collapse: collapse;
        font-size: var(--font-size-sm);
    }
    .detail-table tr td, .detail-table tr th {
        padding: var(--space-2) var(--space-3);
        border-bottom: 1px solid var(--border-color-light);
        vertical-align: middle;
    }
    .detail-table tr th {
        font-weight: 600;
        color: var(--text-secondary);
        width: 160px;
        background: var(--bg-body);
    }
    .detail-table tr td {
        color: var(--text-primary);
    }
    .detail-table .badge-status {
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        display: inline-block;
    }
    .detail-table .badge-status.pending { background: rgba(245,158,11,0.12); color: #d97706; }
    .detail-table .badge-status.approved { background: rgba(16,185,129,0.12); color: var(--brand-green-dark); }
    .detail-table .badge-status.rejected { background: rgba(239,68,68,0.08); color: #dc2626; }

    .tab-nav {
        display: flex;
        gap: var(--space-1);
        border-bottom: 1px solid var(--border-color-light);
        padding: 0 var(--space-5);
        margin-bottom: var(--space-4);
    }
    .tab-nav .tab-link {
        padding: var(--space-2) var(--space-4);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all var(--transition-fast);
        text-decoration: none;
        display: inline-block;
    }
    .tab-nav .tab-link:hover { color: var(--brand-orange-dark); }
    .tab-nav .tab-link.active {
        color: var(--brand-orange-dark);
        border-bottom-color: var(--brand-orange);
    }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; animation: fadeUp 0.3s ease forwards; }

    .btn-action-admin {
        padding: 6px 16px;
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
        font-weight: 600;
        border: none;
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-action-admin.approve {
        background: var(--brand-green);
        color: #fff;
    }
    .btn-action-admin.approve:hover {
        background: var(--brand-green-dark);
    }
    .btn-action-admin.reject {
        background: #ef4444;
        color: #fff;
    }
    .btn-action-admin.reject:hover {
        background: #dc2626;
    }
    .btn-action-admin.warning {
        background: #f59e0b;
        color: #fff;
    }
    .btn-action-admin.warning:hover {
        background: #d97706;
    }

    @media (max-width:991.98px) {
        .detail-content { padding: var(--space-3); }
        .detail-card .card-body { padding: var(--space-4); }
        .detail-table tr th { width: 120px; }
    }
    @media (max-width:575.98px) {
        .detail-content { padding: var(--space-2); }
        .detail-card .card-body { padding: var(--space-3); }
        .detail-table tr th { width: 100px; font-size: var(--font-size-xs); }
        .detail-table tr td { font-size: var(--font-size-xs); }
        .tab-nav { flex-wrap: wrap; padding: 0 var(--space-3); }
        .tab-nav .tab-link { font-size: var(--font-size-xs); padding: var(--space-1) var(--space-2); }
    }
</style>

<div class="detail-content">

    <div class="greeting-section" style="margin-bottom: var(--space-5);">
        <h1 class="greeting-title">Detail Peminjaman</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
            <span>·</span>
            <span class="clock"><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-nav">
        <a class="tab-link active" data-tab="detail">Detail Peminjaman</a>
        <a class="tab-link" data-tab="attendance">Kehadiran Peserta</a>
    </div>

    <!-- Tab: Detail -->
    <div class="tab-pane active" id="tab-detail">
        <div class="detail-card">
            <div class="card-header">
                <div class="title">
                    <i class="bi bi-info-circle"></i> Informasi Peminjaman
                </div>
                <div>
                    @if($booking->status == 0)
                        <span class="badge-status pending">Pending</span>
                    @elseif($booking->status == 1)
                        <span class="badge-status approved">Disetujui</span>
                    @else
                        <span class="badge-status rejected">Ditolak</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="detail-table">
                    <tr>
                        <th>Peminjam</th>
                        <td>{{ $booking->user->employee->full_name ?? $booking->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $booking->user->email }}</td>
                    </tr>
                    <tr>
                        <th>Ruangan</th>
                        <td>{{ $booking->room->name }} (Kapasitas: {{ $booking->room->capacity }} orang)</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Waktu</th>
                        <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Tujuan</th>
                        <td>{{ $booking->purpose }}</td>
                    </tr>
                    <tr>
                        <th>Fasilitas</th>
                        <td>
                            @if($booking->bookingFacilities->count() > 0)
                                <ul style="margin:0; padding-left:var(--space-4);">
                                    @foreach($booking->bookingFacilities as $bf)
                                        <li>{{ $bf->facility->name }} ({{ $bf->quantity }})</li>
                                    @endforeach
                                </ul>
                            @else
                                <span style="color:var(--text-muted);">Tidak ada</span>
                            @endif
                        </td>
                    </tr>
                    @if($booking->processed_by)
                        <tr>
                            <th>Diproses Oleh</th>
                            <td>{{ $booking->processedBy->name ?? 'Admin' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Diproses</th>
                            <td>{{ \Carbon\Carbon::parse($booking->processed_at)->format('d M Y, H:i') }}</td>
                        </tr>
                    @endif
                    @if($booking->rejection_reason)
                        <tr>
                            <th>Alasan Ditolak</th>
                            <td>{{ $booking->rejection_reason }}</td>
                        </tr>
                    @endif
                </table>

                @if(Auth::user()->role == 1)
                    <div class="mt-4" style="display:flex; gap:var(--space-2); flex-wrap:wrap; border-top:1px solid var(--border-color-light); padding-top:var(--space-4);">
                        @if($booking->status == 0)
                            <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action-admin approve" onclick="return confirm('Yakin ingin menyetujui booking ini?')">
                                    <i class="bi bi-check-lg"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('bookings.reject', $booking) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action-admin reject" onclick="return confirm('Yakin ingin menolak booking ini?')">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </form>
                        @elseif($booking->status == 1)
                            <button class="btn-action-admin warning" onclick="showRejectModal()">
                                <i class="bi bi-ban"></i> Batalkan Persetujuan
                            </button>
                            <a href="{{ route('bookings.edit', $booking) }}" class="btn-action-admin" style="background:var(--brand-blue); color:#fff;">
                                <i class="bi bi-pencil"></i> Edit Booking
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab: Kehadiran -->
    <div class="tab-pane" id="tab-attendance">
        <div class="detail-card">
            <div class="card-header">
                <div class="title">
                    <i class="bi bi-people-fill"></i> Daftar Kehadiran
                </div>
                <div>
                    <span style="font-size:var(--font-size-xs); color:var(--text-muted);">
                        Total: {{ $booking->attendances->count() }} peserta
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($booking->attendances->count() > 0)
                    <table class="table-booking" style="width:100%; border-collapse:collapse; font-size:var(--font-size-sm);">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">No</th>
                                <th style="text-align:left; padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">Nama</th>
                                <th style="text-align:left; padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">Email</th>
                                <th style="text-align:left; padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">Check In</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->attendances as $att)
                                <tr>
                                    <td style="padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">{{ $loop->iteration }}</td>
                                    <td style="padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">{{ $att->guest_name ?? $att->user->employee->full_name ?? '-' }}</td>
                                    <td style="padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">{{ $att->user->email ?? '-' }}</td>
                                    <td style="padding:var(--space-2) var(--space-3); border-bottom:1px solid var(--border-color-light);">{{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i:s') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-results" style="padding:var(--space-6); text-align:center; color:var(--text-muted);">
                        <i class="bi bi-people" style="font-size:2rem; display:block; margin-bottom:var(--space-3); color:var(--border-color);"></i>
                        <p>Belum ada peserta yang melakukan absensi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:var(--radius-card); box-shadow:var(--shadow-dropdown);">
            <div class="modal-header" style="padding:var(--space-4) var(--space-5); border-bottom:1px solid var(--border-color-light);">
                <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-exclamation-triangle text-warning me-2"></i> Batalkan Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bookings.reject', $booking) }}" method="POST" id="rejectForm">
                @csrf @method('PATCH')
                <div class="modal-body" style="padding:var(--space-5);">
                    <div class="alert alert-warning" style="background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.10); color:#d97706; padding:var(--space-3) var(--space-4); border-radius:var(--radius-sm); margin-bottom:var(--space-4);">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Perhatian!</strong> Anda akan membatalkan persetujuan booking ini.
                    </div>
                    <div class="form-group">
                        <label for="rejection_reason" class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" placeholder="Jelaskan alasan..." required></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirmReject" required>
                        <label class="form-check-label" for="confirmReject" style="font-size:var(--font-size-sm);">Saya yakin ingin membatalkan persetujuan</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border-color-light); padding:var(--space-4) var(--space-5);">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-submit" style="background:#ef4444; box-shadow:none;" id="confirmRejectBtn" disabled>
                        <i class="bi bi-ban"></i> Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tabs
        document.querySelectorAll('.tab-link').forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.tab-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const target = this.dataset.tab;
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                document.getElementById('tab-' + target).classList.add('active');
            });
        });

        // Reject Modal
        window.showRejectModal = function() {
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        };

        document.getElementById('confirmReject')?.addEventListener('change', function() {
            document.getElementById('confirmRejectBtn').disabled = !this.checked;
        });

        document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
            const reason = document.getElementById('rejection_reason').value.trim();
            if (!reason || reason.length < 10) {
                e.preventDefault();
                alert('Alasan harus diisi minimal 10 karakter');
                return false;
            }
            if (!document.getElementById('confirmReject').checked) {
                e.preventDefault();
                alert('Anda harus mencentang konfirmasi');
                return false;
            }
        });
    });
</script>
@endsection