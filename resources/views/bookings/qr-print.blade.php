@extends('templates.template')

@section('title', 'QR Absensi - ' . $booking->room->name)
@section('page_title', 'QR Absensi')

@section('content')
<style>
    .btn-back {
        height: 44px; padding: 0 var(--space-5); border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm); background: var(--bg-card);
        border: 1px solid var(--border-color); color: var(--text-secondary);
        display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        cursor: pointer; transition: all 0.2s; margin-bottom: var(--space-5);
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .qr-print-card {
        max-width: 460px; margin: 0 auto;
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        padding: 2.5rem 2rem; text-align: center;
    }
    .qr-print-room { font-size: 1.3rem; font-weight: 800; color: var(--text-primary); margin: 0; }
    .qr-print-meta { color: var(--text-muted); font-size: 0.9rem; margin-top: 6px; font-weight: 500; }
    .qr-print-code {
        width: 240px; height: 240px; margin: 1.75rem auto;
        background: white; padding: 12px; border-radius: 14px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08); border: 1px solid var(--border-color-light);
    }
    .qr-print-code img { width: 100%; height: 100%; }
    .qr-print-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; letter-spacing: 0.3px; }
    .qr-print-hint { font-size: 0.82rem; color: var(--text-secondary); margin-top: 1.25rem; line-height: 1.6; }

    .btn-print {
        height: 44px; padding: 0 var(--space-6); border-radius: var(--radius-sm); border: none;
        background: var(--brand-gradient); color: white; font-weight: 600; font-size: var(--font-size-sm);
        cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(249,115,22,0.2);
        display: inline-flex; align-items: center; gap: 8px; margin-top: 1.5rem;
    }
    .btn-print:hover { box-shadow: 0 6px 16px rgba(16,185,129,0.25); transform: translateY(-1px); }

    @media print {
        .no-print { display: none !important; }
        .qr-print-card { box-shadow: none; border: none; padding: 0; }
    }
</style>

<a href="{{ route('bookings.show', $booking->id) }}" class="btn-back no-print">
    <i class="bi bi-arrow-left"></i> Kembali
</a>

<div class="qr-print-card">
    <h1 class="qr-print-room">{{ $booking->room->name }}</h1>
    <div class="qr-print-meta">
        {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}<br>
        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
    </div>

    <div class="qr-print-code" id="qrCodeContainer"></div>
    <div class="qr-print-label">Kode: {{ $booking->absent_code }}</div>

    <div class="qr-print-hint no-print">
        Cetak lalu tempel di pintu ruangan. Peserta cukup memindai QR ini dengan kamera HP untuk mengisi absensi kehadiran.
    </div>

    <button type="button" class="btn-print no-print" onclick="window.print()">
        <i class="bi bi-printer"></i> Cetak
    </button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var url = "{{ url('/booking/meet/' . $booking->absent_code) }}";
        new QRCode(document.getElementById("qrCodeContainer"), {
            text: url,
            width: 216,
            height: 216,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endsection