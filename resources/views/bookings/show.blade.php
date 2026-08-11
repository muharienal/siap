@extends('templates.template')

@section('title', 'Detail Peminjaman')
@section('page_title', 'Detail Peminjaman')

@section('content')
<style>
    /* ============================================
       PAGE HEADER / TOOLBAR
       (disamakan persis dengan settings/rooms/show.blade.php
       agar posisi tombol Kembali & aksi identik di semua
       halaman detail)
    ============================================ */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap;
        gap: var(--space-4);
        margin-bottom: var(--space-5);
    }
    .db-toolbar .actions {
        display: flex;
        gap: var(--space-3);
        flex-wrap: nowrap;
        flex-shrink: 0;
    }

    .btn-back,
    .btn-primary-sm,
    .btn-outline-sm,
    .btn-approve-sm,
    .btn-danger-outline {
        height: 44px; padding: 0 var(--space-5);
        border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; gap: 8px;
        text-decoration: none; cursor: pointer; transition: all 0.2s;
        white-space: nowrap; border: none;
    }

    .btn-back {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .btn-outline-sm {
        background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);
    }
    .btn-outline-sm:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .btn-approve-sm {
        background: #10b981; color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    .btn-approve-sm:hover { background: #059669; }

    .btn-danger-outline {
        background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); color: #dc2626;
    }
    .btn-danger-outline:hover { background: rgba(239, 68, 68, 0.1); }

    /* ============================================
       SATU CARD BESAR DETAIL PEMINJAMAN
    ============================================ */
    .detail-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .card-section {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
    }
    .card-section:last-child { border-bottom: none; }

    .section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: var(--space-4);
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: none !important;
    }
    .section-title i {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(249, 115, 22, 0.1);
        color: var(--brand-orange);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
    }

    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: var(--space-4);
        flex-wrap: wrap;
    }
    .booking-room {
        font-size: var(--font-size-xl);
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }
    .booking-date {
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 500;
        margin-top: 6px;
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
        grid-template-columns: repeat(4, 1fr);
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

    /* Rejection Alert */
    .rejection-alert {
        background: rgba(239,68,68,0.05);
        border: 1px solid rgba(239,68,68,0.2);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
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

    /* Priority Conflict Alert */
    .conflict-alert {
        background: rgba(249,115,22,0.06);
        border: 1px solid rgba(249,115,22,0.25);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
    }
    .conflict-alert-title {
        font-weight: 700;
        color: var(--brand-orange-dark);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: var(--space-2);
    }
    .conflict-alert-text {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        line-height: 1.6;
    }
    .conflict-alert-text a { color: var(--brand-orange-dark); font-weight: 600; }
    .conflict-alert-actions { margin-top: var(--space-3); display: flex; gap: var(--space-2); flex-wrap: wrap; }

    .conflict-badge {
        font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-pill);
        background: rgba(249,115,22,0.12); color: var(--brand-orange-dark);
        display: inline-flex; align-items: center; gap: 4px; text-transform: uppercase; letter-spacing: 0.3px;
    }

    /* Info: booking Priority ini menyebabkan booking lain bentrok */
    .displaces-info {
        background: rgba(59,130,246,0.05);
        border: 1px solid rgba(59,130,246,0.2);
        border-radius: var(--radius-sm);
        padding: var(--space-4);
    }
    .displaces-info-title {
        font-weight: 700; color: var(--brand-blue);
        display: flex; align-items: center; gap: 6px; margin-bottom: var(--space-2);
    }
    .displaces-info-text { font-size: var(--font-size-sm); color: var(--text-secondary); margin-bottom: var(--space-2); }

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

    @media (max-width: 991.98px) {
        .db-toolbar { flex-wrap: wrap; }
        .db-toolbar .actions { width: 100%; }
        .db-toolbar .actions .btn-outline-sm,
        .db-toolbar .actions .btn-approve-sm,
        .db-toolbar .actions .btn-danger-outline,
        .db-toolbar .actions form { flex: 1; }
        .db-toolbar .actions .btn-outline-sm,
        .db-toolbar .actions .btn-approve-sm,
        .db-toolbar .actions .btn-danger-outline { width: 100%; justify-content: center; }
        .info-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 575.98px) {
        .db-toolbar .actions { flex-direction: column; gap: 8px; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="db-toolbar">
    <a href="{{ route('bookings.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div class="actions">
        @if(Auth::user()->role == 1 || (Auth::user()->role == 2 && $booking->status == 0))
        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn-outline-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endif

        @if(Auth::user()->role == 1 && $booking->status == 0)
        <form action="{{ route('bookings.approve', $booking->id) }}" method="POST" onsubmit="return confirm('Setujui booking ini?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-approve-sm">
                <i class="bi bi-check-lg"></i> Setujui
            </button>
        </form>
        <button type="button" class="btn-danger-outline" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="bi bi-x-lg"></i> Tolak
        </button>
        @endif

        @if(Auth::user()->role == 1 && $booking->displaced_by_id)
        <button type="button" class="btn-approve-sm" style="background:#f97316;" data-bs-toggle="modal" data-bs-target="#relocateModal">
            <i class="bi bi-arrow-left-right"></i> Alihkan ke Ruangan Lain
        </button>
        @endif

        <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Hapus peminjaman ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger-outline">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- SATU CARD BESAR: seluruh detail peminjaman --}}
<div class="detail-card">
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
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(Auth::user()->role == 1)
                    @if($booking->booking_type === 'Priority')
                        <span class="conflict-badge" style="background: rgba(139,92,246,0.12); color: #7c3aed;">
                            <i class="bi bi-star-fill"></i> Priority
                        </span>
                    @endif
                    @if($booking->displaced_by_id)
                        <span class="conflict-badge">
                            <i class="bi bi-exclamation-triangle-fill"></i> Bentrok
                        </span>
                    @endif
                @endif
                <span class="status-badge status-{{ $statusClass }}">
                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> {{ $statusLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- Alert konflik jadwal: admin melihat detail lengkap (siapa, ruangan
         prioritas mana). Selain admin — termasuk pemilik booking ini sendiri
         — hanya melihat versi netral tanpa kata "prioritas" sama sekali,
         supaya tidak ada yang tahu ada sistem Priority/Reguler. --}}
    @if($booking->displaced_by_id)
    <div class="card-section">
        <div class="conflict-alert">
            @if(Auth::user()->role == 1 && $booking->displacedBy)
                <div class="conflict-alert-title">
                    <i class="bi bi-exclamation-triangle-fill"></i> Konflik Jadwal Prioritas
                </div>
                <div class="conflict-alert-text">
                    Peminjaman ini bentrok dengan peminjaman <strong>prioritas</strong> dari
                    <strong>{{ $booking->displacedBy->user->full_name ?? 'Direksi' }}</strong>
                    di ruangan yang sama pada waktu yang sama
                    (<a href="{{ route('bookings.show', $booking->displacedBy->id) }}">lihat detail peminjaman prioritas &rarr;</a>).
                    Silakan alihkan peminjaman ini ke ruangan atau waktu lain.
                </div>
                <div class="conflict-alert-actions">
                    <button type="button" class="btn-approve-sm" style="background:#f97316;" data-bs-toggle="modal" data-bs-target="#relocateModal">
                        <i class="bi bi-arrow-left-right"></i> Alihkan ke Ruangan Lain
                    </button>
                </div>
            @else
                <div class="conflict-alert-title">
                    <i class="bi bi-info-circle-fill"></i> Sedang Ditinjau Admin
                </div>
                <div class="conflict-alert-text">
                    Peminjaman ini sedang ditinjau ulang oleh admin karena ada penyesuaian jadwal.
                    Admin akan segera menghubungi Anda jika perlu dialihkan ke ruangan atau waktu lain.
                </div>
            @endif
        </div>
    </div>
    @endif

    @if(Auth::user()->role == 1 && $booking->displaces->count() > 0)
    <div class="card-section">
        <div class="displaces-info">
            <div class="displaces-info-title">
                <i class="bi bi-info-circle-fill"></i> Dampak Prioritas
            </div>
            <div class="displaces-info-text">
                Peminjaman prioritas ini bentrok dengan {{ $booking->displaces->count() }} peminjaman reguler yang sudah ada.
                Admin sudah diberi tahu untuk mengalihkan jadwal peminjam lain tersebut.
            </div>
            <div class="facility-list-detail">
                @foreach($booking->displaces as $d)
                    <div class="facility-detail-item">
                        <div class="facility-detail-name">{{ $d->user->full_name ?? 'Unknown' }}</div>
                        <a href="{{ route('bookings.show', $d->id) }}" class="text-decoration-none small fw-semibold" style="color: var(--brand-blue);">
                            {{ $d->displaced_by_id ? 'Belum dialihkan' : 'Sudah dialihkan' }} &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="card-section">
        <div class="section-title">
            <i class="bi bi-info-circle"></i> Informasi Peminjaman
        </div>
        <div class="info-grid" style="{{ Auth::user()->role != 1 ? 'grid-template-columns: repeat(3, 1fr);' : '' }}">
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
            @if(Auth::user()->role == 1)
            <div class="info-item">
                <div class="info-label">Tipe Booking</div>
                <div class="info-value">{{ $booking->booking_type }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="card-section">
        <div class="section-title">
            <i class="bi bi-card-text"></i> Keperluan
        </div>
        <div class="purpose-box">{{ $booking->purpose }}</div>
    </div>

    @if($booking->relocation_note)
    <div class="card-section">
        <div class="displaces-info">
            <div class="displaces-info-title">
                <i class="bi bi-arrow-left-right"></i> Riwayat Pengalihan Ruangan
            </div>
            <div class="displaces-info-text mb-0">
                Peminjaman ini pernah dialihkan oleh admin. Catatan admin: <em>"{{ $booking->relocation_note }}"</em>
            </div>
        </div>
    </div>
    @endif

    @if($booking->consumption_qty || $booking->other_notes)
    <div class="card-section">
        <div class="section-title">
            <i class="bi bi-tools"></i> Kebutuhan Tambahan
        </div>
        <div class="facility-list-detail">
            @if($booking->consumption_qty)
                <div class="facility-detail-item">
                    <div class="facility-detail-name">Konsumsi</div>
                    <span class="facility-detail-qty">{{ $booking->consumption_qty }} Peserta</span>
                </div>
            @endif
            @if($booking->other_notes)
                <div class="info-item">
                    <div class="info-label">Lain-lain (Catatan)</div>
                    <div class="info-value" style="font-weight: 500; white-space: pre-line;">{{ $booking->other_notes }}</div>
                </div>
            @endif
        </div>
    </div>
    @endif

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

    @if($booking->status == 2 && $booking->rejection_reason)
    <div class="card-section">
        <div class="rejection-alert">
            <div class="rejection-alert-title">
                <i class="bi bi-exclamation-triangle"></i> Alasan Penolakan
            </div>
            <div class="rejection-alert-text">{{ $booking->rejection_reason }}</div>
        </div>
    </div>
    @endif

    @if($booking->status == 1 && $booking->absent_code)
    <div class="card-section">
        <div class="section-title">
            <i class="bi bi-qr-code"></i> Kode Absensi
        </div>
        <div class="qr-section">
            <p class="text-muted small mb-2">Tunjukkan QR ini kepada peserta rapat untuk absensi</p>
            <div class="qr-code" id="qrCodeContainer"></div>
            <div class="qr-label">Kode: {{ $booking->absent_code }}</div>
            <button type="button" class="btn-outline-sm mt-3" style="display:inline-flex;" data-bs-toggle="modal" data-bs-target="#printQrModal">
                <i class="bi bi-printer"></i> Cetak QR
            </button>
        </div>
    </div>
    @endif

    @if($booking->attendances->count() > 0)
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
    @endif

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

{{-- Print QR Modal — cetak QR tanpa pindah halaman --}}
@if($booking->status == 1 && $booking->absent_code)
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
    #printQrModal .qr-print-body { text-align: center; padding: 2rem 1.5rem; }
    #printQrModal .qr-print-room { font-size: 1.2rem; font-weight: 800; color: var(--text-primary); margin: 0; }
    #printQrModal .qr-print-meta { color: var(--text-muted); font-size: 0.85rem; margin-top: 4px; font-weight: 500; }
    #printQrModal .qr-print-code {
        width: 220px; height: 220px; margin: 1.5rem auto;
        background: #fff; padding: 12px; border-radius: 14px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08); border: 1px solid var(--border-color-light);
    }
    #printQrModal .qr-print-code img, #printQrModal .qr-print-code canvas { width: 100%; height: 100%; }
    #printQrModal .qr-print-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin-bottom: 1.5rem; }
    #printQrModal .qr-action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    #printQrModal .qr-action-btn {
        height: 44px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);
        background: var(--bg-card); color: var(--text-secondary); font-weight: 600; font-size: 0.85rem;
        display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s;
    }
    #printQrModal .qr-action-btn:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }
    #printQrModal .qr-action-btn.primary { background: var(--brand-gradient); border-color: transparent; color: #fff; grid-column: 1 / -1; }
    #printQrModal .qr-action-btn.primary:hover { color: #fff; filter: brightness(0.96); }

    /* ============================================================
       AREA CETAK KHUSUS — sengaja dipisah dari modal Bootstrap.
       Modal Bootstrap pakai flexbox untuk center vertikal isinya di
       layar, dan itu ikut kebawa saat print (menyebabkan konten
       "ngambang" jauh di bawah halaman kosong). Area ini disembunyikan
       total di layar (display:none) dan HANYA muncul saat print,
       mengalir normal dari atas halaman seperti dokumen biasa.
    ============================================================ */
    .qr-print-area { display: none; }
    @media print {
        body * { visibility: hidden; }
        .qr-print-area, .qr-print-area * { visibility: visible; }
        .qr-print-area {
            display: block !important;
            position: absolute; left: 0; top: 0; width: 100%;
        }
        .qr-print-page {
            max-width: 420px; margin: 50px auto 0; text-align: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .qr-print-page .brand { font-size: 12px; letter-spacing: 1px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 18px; }
        .qr-print-page h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .qr-print-page .meta { font-size: 13px; color: #475569; margin-bottom: 24px; }
        .qr-print-page .qr-box { width: 260px; height: 260px; margin: 0 auto 16px; padding: 14px; border: 2px solid #e2e8f0; border-radius: 16px; }
        .qr-print-page .qr-box img, .qr-print-page .qr-box canvas { width: 100%; height: 100%; }
        .qr-print-page .code-label { font-size: 12px; color: #64748b; font-weight: 700; margin-bottom: 20px; }
        .qr-print-page .instruction { font-size: 12px; color: #475569; line-height: 1.6; max-width: 320px; margin: 0 auto; }
    }
</style>
<div class="modal fade" id="printQrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-printer"></i> Cetak / Bagikan QR Absensi</h5>
                <button type="button" class="btn-close no-print" data-bs-dismiss="modal"></button>
            </div>
            <div class="qr-print-body">
                <h3 class="qr-print-room">{{ $booking->room->name }}</h3>
                <div class="qr-print-meta">
                    {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}<br>
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
                </div>
                <div class="qr-print-code" id="qrPrintModalContainer"></div>
                <div class="qr-print-label">Kode: {{ $booking->absent_code }}</div>

                <div class="qr-action-grid">
                    <button type="button" class="qr-action-btn" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                    <button type="button" class="qr-action-btn" id="qrDownloadBtn">
                        <i class="bi bi-download"></i> Download PNG
                    </button>
                    <button type="button" class="qr-action-btn primary" id="qrShareBtn">
                        <i class="bi bi-share-fill"></i> Bagikan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Konten khusus cetak, terpisah dari modal (lihat penjelasan di CSS) --}}
<div class="qr-print-area">
    <div class="qr-print-page">
        <div class="brand">SIAP &middot; Sistem Informasi Peminjaman Ruangan</div>
        <h1>{{ $booking->room->name }}</h1>
        <div class="meta">
            {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }} &middot;
            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
        </div>
        <div class="qr-box" id="qrPrintAreaContainer"></div>
        <div class="code-label">Kode: {{ $booking->absent_code }}</div>
        <div class="instruction">Pindai kode QR ini menggunakan kamera HP untuk mengisi absensi kehadiran rapat.</div>
    </div>
</div>

<script>
    (function () {
        var meetingUrl = "{{ url('/booking/meet/' . $booking->absent_code) }}";
        var shareText = "Absensi rapat {{ addslashes($booking->room->name) }} — " +
            "{{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y, H:i') }} WIB. " +
            "Isi kehadiran di sini: " + meetingUrl;
        var qrGenerated = false;

        function generateQrCodes() {
            if (qrGenerated) return;
            qrGenerated = true;
            new QRCode(document.getElementById('qrPrintModalContainer'), {
                text: meetingUrl, width: 196, height: 196,
                colorDark: "#000000", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.H
            });
            // QR kedua khusus untuk area cetak (dipisah dari modal, lihat CSS)
            new QRCode(document.getElementById('qrPrintAreaContainer'), {
                text: meetingUrl, width: 232, height: 232,
                colorDark: "#000000", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.H
            });
        }

        document.getElementById('printQrModal').addEventListener('shown.bs.modal', generateQrCodes);

        document.getElementById('qrDownloadBtn').addEventListener('click', function () {
            var canvas = document.querySelector('#qrPrintModalContainer canvas');
            if (!canvas) return;
            var link = document.createElement('a');
            link.download = 'QR-Absensi-{{ $booking->absent_code }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });

        document.getElementById('qrShareBtn').addEventListener('click', function () {
            var canvas = document.querySelector('#qrPrintModalContainer canvas');
            // Di HP: pakai native share sheet (bisa langsung kirim gambar QR ke WA/aplikasi lain).
            if (navigator.share && canvas) {
                canvas.toBlob(function (blob) {
                    var file = new File([blob], 'QR-Absensi-{{ $booking->absent_code }}.png', { type: 'image/png' });
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        navigator.share({ files: [file], title: 'QR Absensi {{ addslashes($booking->room->name) }}', text: shareText })
                            .catch(function () {});
                    } else {
                        navigator.share({ title: 'QR Absensi', text: shareText }).catch(function () {});
                    }
                });
            } else {
                // Fallback desktop: buka WhatsApp Web dengan teks siap kirim.
                window.open('https://wa.me/?text=' + encodeURIComponent(shareText), '_blank');
            }
        });
    })();
</script>
@endif

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

{{-- Relocate Modal (hanya render kalau booking sedang bentrok DAN yang
     melihat adalah admin — non-admin tidak boleh melihat markup ini sama
     sekali, bahkan lewat page-source, karena isinya menyebut "prioritas"). --}}
@if($booking->displaced_by_id && Auth::user()->role == 1)
<div class="modal fade" id="relocateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bookings.relocate', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-left-right" style="color: var(--brand-orange);"></i> Alihkan ke Ruangan Lain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Peminjaman ini bentrok dengan peminjaman prioritas dari <strong>{{ $booking->displacedBy->user->full_name ?? 'Direksi' }}</strong>.
                        Pilih ruangan/waktu pengganti dan berikan catatan untuk peminjam.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ruangan Pengganti</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Pilih ruangan</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">
                                    {{ $r->name }} &middot; Kapasitas {{ $r->capacity }} orang
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($booking->start_time)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Waktu Selesai</label>
                            <input type="datetime-local" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($booking->end_time)->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Catatan untuk Peminjam <span class="text-danger">*</span></label>
                        <textarea name="relocation_note" class="form-control" rows="3" placeholder="Contoh: Mohon maaf, ruangan dialihkan karena ada rapat direksi mendadak di jam yang sama..." required minlength="10" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" style="background: var(--brand-gradient); color: white;">
                        <i class="bi bi-check-lg"></i> Alihkan &amp; Kirim Notifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($booking->status == 1 && $booking->absent_code)
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