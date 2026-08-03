@extends('layouts.auth')

@section('title', 'Absensi Meeting')

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
        margin-bottom: 1.5rem;
    }

    .meeting-info {
        text-align: left;
        width: 100%;
        max-width: 280px;
        font-size: 0.92rem;
        line-height: 1.7;
    }
    .meeting-info strong {
        font-weight: 700;
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

    .form-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--ui-text-primary);
        letter-spacing: -0.01em;
        margin-bottom: 0.4rem;
    }

    .form-subtitle {
        color: var(--ui-text-secondary);
        font-size: 0.95rem;
        margin-bottom: 1.75rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--ui-text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .form-control {
        width: 100%;
        height: 52px;
        border: 1.5px solid var(--ui-border);
        border-radius: var(--ui-radius-sm);
        padding: 0 1rem;
        font-size: 0.95rem;
        color: var(--ui-text-primary);
        background: #f8fafc;
        transition: border-color 0.15s ease, background 0.15s ease;
        margin-bottom: 1.5rem;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--brand-orange);
        background: #fff;
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
        cursor: pointer;
    }
    .btn-login:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 25px -8px rgba(249, 115, 22, 0.4);
    }

    .btn-forgot {
        color: var(--brand-orange-dark);
        font-weight: 600;
        text-decoration: none;
    }
    .btn-forgot:hover {
        text-decoration: underline;
    }

    .alert-danger {
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.18);
        color: #dc2626;
        padding: 0.9rem 1.1rem;
        border-radius: var(--ui-radius-sm);
        font-size: 0.88rem;
        margin-bottom: 1.25rem;
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
                    <h2>Absensi Meeting</h2>
                    <div class="meeting-info">
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
                </div>
            </div>

            <!-- SISI KANAN -->
            <div class="col-lg-6">
                <div class="auth-form-panel">

                    <div class="auth-mobile-logo">
                        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" />
                    </div>

                    <h1 class="form-title">Form Absensi</h1>
                    <p class="form-subtitle">Silakan isi data Anda untuk absensi</p>

                    @if($errors->any())
                        <div class="alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('attendance.submit', $booking->absent_code) }}">
                        @csrf

                        <label for="name" class="form-label">
                            <i class="bi bi-person me-2"></i>Nama Lengkap
                        </label>
                        <input id="name" type="text" class="form-control"
                               name="name" value="{{ old('name') }}" required
                               placeholder="Masukkan nama lengkap Anda">

                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-check-circle me-2"></i>
                            Konfirmasi Absensi
                        </button>

                        <div class="text-center mt-3">
                            <span class="text-muted">Sudah punya akun? </span>
                            <a href="{{ route('login') }}" class="btn-forgot">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Login di sini
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection