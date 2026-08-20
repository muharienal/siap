@extends('layouts.auth')

@section('title', 'Error Presensi')

@section('content')
<style>
    :root {
        --brand-orange: #FF9142;
        --brand-orange-dark: #F2751A;
        --brand-green: #22C55E;
        --brand-green-dark: #16A34A;
        --brand-gradient: linear-gradient(160deg, var(--brand-orange) 0%, var(--brand-green) 100%);

        --ui-bg: #f1f5f9;
        --ui-card-bg: #ffffff;
        --ui-border: #e2e8f0;
        --ui-text-primary: #0f172a;
        --ui-text-secondary: #475569;
        --ui-text-muted: #94a3b8;
        --ui-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.15);
        --ui-radius: 28px;
        --ui-radius-sm: 14px;

        --font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body.auth-page {
        background: var(--ui-bg);
        font-family: var(--font-family);
    }

    /* Nama class sengaja unik (bukan .auth-wrapper) supaya tidak bentrok
       dengan div pembungkus milik layouts/auth.blade.php. */
    .attend-status-wrapper {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
    }

    .attend-status-card {
        border: none;
        border-radius: var(--ui-radius);
        overflow: hidden;
        background: var(--ui-card-bg);
        box-shadow: var(--ui-shadow);
        width: 100%;
    }

    .attend-status-brand {
        background: var(--brand-gradient);
        padding: 3.5rem 2.5rem;
        height: 100%;
        min-height: 480px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .attend-status-brand::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(255,255,255,0.10) 0%, transparent 60%),
            radial-gradient(circle at 80% 70%, rgba(255,255,255,0.08) 0%, transparent 50%);
        pointer-events: none;
    }

    .attend-status-brand > * {
        position: relative;
        z-index: 2;
    }

    .attend-logo {
        width: 90px;
        height: 90px;
        object-fit: contain;
        background: rgba(255,255,255,0.16);
        backdrop-filter: blur(6px);
        padding: 16px;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        margin-bottom: 1.5rem;
    }

    .attend-status-brand h2 {
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 0.75rem;
    }

    .attend-status-brand p {
        font-size: 0.95rem;
        line-height: 1.6;
        opacity: 0.92;
        max-width: 320px;
        margin: 0 auto;
        font-weight: 400;
    }

    .attend-status-content {
        padding: 3rem 2.8rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--ui-card-bg);
    }

    .attend-mobile-logo {
        display: none;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .attend-mobile-logo img {
        width: 64px;
        height: 64px;
        object-fit: contain;
    }

    .status-icon-ring {
        width: 84px; height: 84px; border-radius: 50%;
        background: rgba(245,158,11,0.14);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem auto;
    }
    .status-icon-ring i { font-size: 2.3rem; color: #b45309; }

    .status-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--ui-text-primary);
        letter-spacing: -0.01em;
        margin-bottom: 1rem;
    }

    .status-alert {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #b45309;
        padding: 0.9rem 1.1rem;
        border-radius: var(--ui-radius-sm);
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .meeting-summary {
        background: #f8fafc;
        border: 1px solid var(--ui-border);
        border-radius: var(--ui-radius-sm);
        padding: 1.25rem 1.4rem;
        text-align: left;
        margin-bottom: 1.75rem;
    }
    .meeting-summary h6 {
        color: var(--ui-text-primary);
        font-size: 0.95rem;
        font-weight: 700;
    }
    .meeting-summary div {
        color: var(--ui-text-secondary);
        font-size: 0.9rem;
    }
    .meeting-summary i {
        color: var(--brand-orange-dark);
    }

    .btn-login {
        width: 100%;
        height: 54px;
        background: var(--brand-gradient);
        border: none;
        border-radius: var(--ui-radius-sm);
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        text-decoration: none;
    }
    .btn-login:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 25px -8px rgba(255, 145, 66, 0.4);
    }

    .btn-secondary-link {
        width: 100%;
        height: 54px;
        border: 2px solid var(--ui-border);
        border-radius: var(--ui-radius-sm);
        color: var(--ui-text-secondary);
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: none;
        margin-top: 0.75rem;
        text-decoration: none;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .btn-secondary-link:hover {
        background: var(--ui-bg);
        color: var(--ui-text-secondary);
        border-color: #cbd5e1;
    }

    @media (max-width: 991.98px) {
        .attend-mobile-logo { display: block; }
    }
</style>

<div class="attend-status-wrapper">
    <div class="attend-status-card">
        <div class="row g-0">
            <!-- SISI KIRI -->
            <div class="col-lg-6 d-none d-lg-flex">
                <div class="attend-status-brand w-100">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" class="attend-logo" />
                    <h2>Oops! Ada Masalah</h2>
                    <p>Terjadi kendala saat memproses presensi Anda. Silakan hubungi penyelenggara jika masalah ini terus berlanjut.</p>
                </div>
            </div>

            <!-- SISI KANAN -->
            <div class="col-lg-6">
                <div class="attend-status-content">

                    <div class="attend-mobile-logo">
                        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" />
                    </div>

                    <div class="text-center">
                        <div class="status-icon-ring">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>

                        <h1 class="status-title" style="color: #b45309;">Presensi Tidak Dapat Diproses</h1>

                        <div class="status-alert">
                            {{ $message ?? 'Terjadi kesalahan yang tidak diketahui.' }}
                        </div>

                        @if(isset($booking))
                            <div class="meeting-summary">
                                <h6 class="mb-3">Detail Meeting</h6>
                                <div class="mb-2">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <strong>Ruangan:</strong> {{ $booking->room->name }}
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-calendar me-2"></i>
                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}
                                </div>
                                <div>
                                    <i class="bi bi-clock me-2"></i>
                                    <strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </div>
                            </div>
                        @endif

                        <a href="{{ url('/') }}" class="btn btn-login">
                            <i class="bi bi-house"></i> Kembali ke Beranda
                        </a>
                        <a href="javascript:history.back()" class="btn-secondary-link">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection