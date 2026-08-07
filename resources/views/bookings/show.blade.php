@extends('templates.template')

@section('page_title', 'Detail Peminjaman')
@section('page_subtitle', 'Informasi lengkap peminjaman ruangan')

@section('content')
<style>
    .detail-content {
        padding: var(--space-6) var(--space-7);
        max-width: 1000px;
        margin: 0 auto;
        width: 100%;
        padding-bottom: 100px;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-5);
        flex-wrap: wrap;
        gap: var(--space-3);
    }
    .detail-header h1 { font-size: var(--font-size-2xl); font-weight: 800; color: var(--text-primary); margin: 0; }

    .btn-back {
        height: 42px;
        padding: 0 var(--space-4);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-secondary);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange); }

    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: var(--space-5);
    }

    .detail-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .card-section {
        padding: var(--space-5);
        border-bottom: 1px solid var(--border-color-light);
    }
    .card-section:last-child { border-bottom: none; }

    .section-title {
        font-size: var(--font-size-md);
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: var(--space-4);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title i { color: var(--brand-orange); }

    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--space-4);
        flex-wrap: wrap;
    }
    .booking-room {
        font-size: var(--font-size-xl);
        font-weight: 800;
        color: var(--text-primary);
    }
    .booking-date {
        color: var(--text-secondary);
        font-size: var(--font-size-sm);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge {
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: var(--radius-pill);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .status-pending { background: rgba(245,158,11,0.12); color: #b45309; }
    .status-approved { background: rgba(16,185,129,0.12); color: #047857; }
    .status-rejected { background: rgba(239,68,68,0.12); color: #b91c1c; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-4);
    }
    .info-item {
        background: var(--bg-body);
        padding: var(--space-3) var(--space-4);
        border-radius: var(--radius-sm);
    }
    .info-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .info-value {
        font-size: var(--font-size-sm);
        font-weight: 600;
        color: var(--text-primary);
    }

    .purpose-box {
        background: var(--bg-body);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        line-height: 1.6;
    }

    .facility-list-detail {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }
    .facility-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-3);
        background: var(--bg-body);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
    }
    .facility-detail-name { font-weight: 600; color: var(--text-primary); }
    .facility-detail-qty {
        background: var(--brand-orange);
        color: white;
        padding: 2px 10px;
        border-radius: var(--radius-pill);
        font-size: 11px;
        font-weight: 700;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 32px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border-color);
    }
    .timeline-item {
        position: relative;
        padding-bottom: var(--space-5);
    }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 3px solid var(--brand-orange);
    }
    .timeline-item.success::before { border-color: #10b981; }
    .timeline-item.danger::before { border-color: #ef4444; }
    .timeline-item.info::before { border-color: var(--brand-blue); }
    .timeline-icon {
        position: absolute;
        left: -32px;
        top: -2px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
    }
    .timeline-icon.primary { background: var(--brand-orange); }
    .timeline-icon.success { background: #10b981; }
    .timeline-icon.danger { background: #ef4444; }
    .timeline-icon.info { background: var(--brand-blue); }
    .timeline-title {
        font-weight: 700;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
    }
    .timeline-desc {
        font-size: var(--font-size-xs);
        color: var(--text-secondary);
        margin-top: 2px;
    }
    .timeline-time {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* QR Code */
    .qr-section {
        text-align: center;
        padding: var(--space-4);
        background: var(--bg-body);
        border-radius: var(--radius-sm);
    }
    .qr-code {
        margin: var(--space-3) auto;
        width: 200px;
        height: 200px;
        background: white;
        padding: 10px;
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-card);
    }
    .qr-code img { width: 100%; height: 100%; }
    .qr-label {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: var(--space-2);
    }

    /* Rejection Alert */
    .rejection-alert {
        background: rgba(239,68,68,0.05);
        border: 1px solid rgba(239,68,68,0.2);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
        margin-bottom: var(--space-4);
    }
    .rejection-alert-title {
        font-weight: 700;
        color: #b91c1c;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: var(--space-2);
    }
    .rejection-alert-text {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }

    /* Action Buttons */
    .action-bar {
        display: flex;
        gap: var(--space-3);
        margin-top: var(--space-5);
        flex-wrap: wrap;
    }
    .btn-action {
        height: 48px;
        padding: 0 var(--space-5);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        flex: 1;
        justify-content: center;
    }
    .btn-edit { background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary); }
    .btn-edit:hover { border-color: var(--brand-orange); color: var(--brand-orange); }
    .btn-approve { background: #10b981; color: white; }
    .btn-approve:hover { background: #059669; }
    .btn-reject { background: #ef4444; color: white; }
    .btn-reject:hover { background: #dc2626; }
    .btn-delete { background: var(--bg-card); border: 1px solid #ef4444; color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: white; }

    @media (max-width: 991.98px) {
        .detail-content { padding: var(--space-4); }
        .detail-grid { grid-template-columns: 1fr; }
        .info-grid { grid-template-columns: 1fr; }
        .action-bar { flex-direction: column; }
        .btn-action { width: 100%; }
    }
</style>

<div class="detail-content">
    <div class="detail-header">
        <div>
            <h1>Detail Peminjaman</h1>
            <p class="text-muted mb-0 mt-1">Informasi lengkap peminjaman ruangan</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="detail-grid">
        {{-- Left Column --}}
        <div>
            {{-- Main Info --}}
            <div class="detail-card mb-4">
                <div class="card-section">
                    <div class="booking-header">
                        <div>
                            <div class="booking-room">{{ $booking->room->name }}</div>
                            <div class="booking-date">
                                <i class="bi bi-calendar3"></i>
                                {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                            </div>
                        </div>
                        @php
                            $statusClass = $booking->status == 0 ? 'pending' : ($booking->status == 1 ? 'approved' : 'rejected');
                            $statusLabel = $booking->status == 0 ? 'Pending' : ($booking->status == 1 ? 'Disetujui' : 'Ditolak');
                        @endphp
                        <span class="status-badge status-{{ $statusClass }}">
                            <i class="bi bi-circle-fill" style="font-size: 6px;"></i> {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="card-section">
                    <div class="section-title">
                        <i class="bi bi-info-circle"></i> Informasi Peminjaman
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Jam Mulai</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} WIB</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jam Selesai</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Peminjam</div>
                            <div class="info-value">{{ $booking->user->full_name ?? 'Unknown' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tipe Booking</div>
                            <div class="info-value">{{ $booking->booking_type }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-section">
                    <div class="section-title">
                        <i class="bi bi-card-text"></i> Keperluan
                    </div>
                    <div class="purpose-box">{{ $booking->purpose }}</div>
                </div>

                @if($booking->bookingFacilities->count() > 0)
                <div class="card-section">
                    <div class="section-title">
                        <i class="bi bi-box-seam"></i> Fasilitas Diminta
                    </div>
                    <div class="facility-list-detail">
                        @foreach($booking->bookingFacilities as $bf)
                            <div class="facility-detail-item">
                                <div class="facility-detail-name">{{ $bf->facility->name }}</div>
                                <span class="facility-detail-qty">Qty: {{ $bf->quantity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Timeline --}}
            <div class="detail-card">
                <div class="card-section">
                    <div class="section-title">
                        <i class="bi bi-clock-history"></i> Timeline Peminjaman
                    </div>
                    <div class="timeline">
                        @foreach($timeline as $item)
                            <div class="timeline-item {{ $item['color'] }}">
                                <div class="timeline-icon {{ $item['color'] }}">
                                    <i class="bi {{ $item['icon'] }}"></i>
                                </div>
                                <div class="timeline-title">{{ $item['title'] }}</div>
                                <div class="timeline-desc">{{ $item['description'] }}</div>
                                @if($item['time'])
                                <div class="timeline-time">{{ \Carbon\Carbon::parse($item['time'])->locale('id')->isoFormat('D MMM Y, HH:mm') }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div>
            @if($booking->status == 2 && $booking->rejection_reason)
            <div class="detail-card mb-4">
                <div class="card-section">
                    <div class="rejection-alert">
                        <div class="rejection-alert-title">
                            <i class="bi bi-exclamation-triangle"></i> Alasan Penolakan
                        </div>
                        <div class="rejection-alert-text">{{ $booking->rejection_reason }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($booking->status == 1 && $booking->absent_code)
            <div class="detail-card mb-4">
                <div class="card-section">
                    <div class="section-title">
                        <i class="bi bi-qr-code"></i> Kode Absensi
                    </div>
                    <div class="qr-section">
                        <p class="text-muted small mb-2">Tunjukkan QR ini kepada peserta rapat untuk absensi</p>
                        <div class="qr-code" id="qrCodeContainer"></div>
                        <div class="qr-label">Kode: {{ $booking->absent_code }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($booking->attendances->count() > 0)
            <div class="detail-card">
                <div class="card-section">
                    <div class="section-title">
                        <i class="bi bi-people"></i> Daftar Hadir ({{ $booking->attendances->count() }})
                    </div>
                    <div class="facility-list-detail">
                        @foreach($booking->attendances as $att)
                            <div class="facility-detail-item">
                                <div>
                                    <div class="facility-detail-name">{{ $att->user->full_name ?? $att->guest_name }}</div>
                                    <small class="text-muted">{{ $att->guest_institution ?? 'Internal' }}</small>
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="action-bar">
        @if(Auth::user()->role == 1 || (Auth::user()->role == 2 && $booking->status == 0))
        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn-action btn-edit">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endif

        @if(Auth::user()->role == 1 && $booking->status == 0)
        <form action="{{ route('bookings.approve', $booking->id) }}" method="POST" style="flex: 1;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-action btn-approve w-100">
                <i class="bi bi-check-lg"></i> Setujui
            </button>
        </form>

        <button type="button" class="btn-action btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="bi bi-x-lg"></i> Tolak
        </button>
        @endif

        <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Hapus peminjaman ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-action btn-delete w-100">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bookings.reject', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle text-danger"></i> Tolak Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Berikan alasan penolakan untuk peminjam:</p>
                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Minimal 10 karakter..." required minlength="10" maxlength="500"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg"></i> Tolak Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($booking->status == 1 && $booking->absent_code)
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var url = "{{ url('/booking/meet/' . $booking->absent_code) }}";
        new QRCode(document.getElementById("qrCodeContainer"), {
            text: url,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endif
@endsection