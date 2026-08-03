@extends('layouts.auth')

@section('title', 'Absensi Berhasil')

@section('content')
<style>
    :root {
        --brand-orange: #f97316;
        --brand-orange-dark: #ea580c;
        --brand-green: #10b981;
        --brand-green-dark: #059669;
        --brand-gradient: linear-gradient(145deg, var(--brand-orange) 0%, var(--brand-green) 100%);

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

    .auth-wrapper {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
    }

    .auth-card {
        border: none;
        border-radius: var(--ui-radius);
        overflow: hidden;
        background: var(--ui-card-bg);
        box-shadow: var(--ui-shadow);
        width: 100%;
    }

    .auth-brand-panel {
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

    .auth-brand-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(255,255,255,0.08) 0%, transparent 60%),
            radial-gradient(circle at 80% 70%, rgba(255,255,255,0.06) 0%, transparent 50%);
        pointer-events: none;
    }

    .auth-brand-panel > * {
        position: relative;
        z-index: 2;
    }

    .auth-logo {
        width: 90px;
        height: 90px;
        object-fit: contain;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(6px);
        padding: 16px;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        margin-bottom: 1.5rem;
    }

    .auth-brand-panel h2 {
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 0.75rem;
    }

    .auth-brand-panel p {
        font-size: 0.95rem;
        line-height: 1.6;
        opacity: 0.9;
        max-width: 320px;
        margin: 0 auto;
        font-weight: 300;
    }

    .auth-form-panel {
        padding: 3rem 2.8rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--ui-card-bg);
    }

    .auth-mobile-logo {
        display: none;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .auth-mobile-logo img {
        width: 64px;
        height: 64px;
        object-fit: contain;
    }

    .status-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
    }

    .status-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--ui-text-primary);
        letter-spacing: -0.01em;
        margin-bottom: 0.5rem;
    }

    .status-subtitle {
        color: var(--ui-text-secondary);
        font-size: 0.95rem;
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
        color: var(--ui-text-muted);
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
        box-shadow: 0 10px 25px -8px rgba(249, 115, 22, 0.4);
    }

    @media (max-width: 991.98px) {
        .auth-mobile-logo { display: block; }
    }
</style>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="row g-0">
            <!-- SISI KIRI -->
            <div class="col-lg-6 d-none d-lg-flex">
                <div class="auth-brand-panel w-100">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" class="auth-logo" />
                    <h2>Absensi Berhasil!</h2>
                    <p>Terima kasih telah melakukan absensi. Data kehadiran Anda telah tercatat dengan baik.</p>
                </div>
            </div>

            <!-- SISI KANAN -->
            <div class="col-lg-6">
                <div class="auth-form-panel">

                    <div class="auth-mobile-logo">
                        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" />
                    </div>

                    <div class="text-center">
                        <div class="status-icon">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>

                        <h1 class="status-title text-success">Absensi Berhasil!</h1>
                        <p class="status-subtitle">{{ $message ?? 'Data kehadiran Anda telah tercatat.' }}</p>

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
                                <div class="mb-2">
                                    <i class="bi bi-clock me-2"></i>
                                    <strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </div>
                                <div>
                                    <i class="bi bi-bookmark me-2"></i>
                                    <strong>Keperluan:</strong> {{ $booking->purpose }}
                                </div>
                            </div>
                        @endif

                        <a href="{{ url('/') }}" class="btn btn-login">
                            <i class="bi bi-house"></i> Kembali ke Beranda
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection