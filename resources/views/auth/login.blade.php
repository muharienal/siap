@extends('layouts.auth')

@section('title', 'Login - SIAP')

@section('content')
<style>
    /* ============================================================
       ROOT VARIABLES
       ============================================================ */
    :root {
        --brand-orange: #f97316;
        --brand-orange-dark: #ea580c;
        --brand-green: #10b981;
        --brand-green-dark: #059669;
        --brand-gradient: linear-gradient(145deg, var(--brand-orange) 0%, var(--brand-green) 100%);
        --brand-gradient-hover: linear-gradient(145deg, var(--brand-orange-dark) 0%, var(--brand-green-dark) 100%);

        --ui-bg: #f1f5f9;
        --ui-card-bg: #ffffff;
        --ui-border: #e2e8f0;
        --ui-text-primary: #0f172a;
        --ui-text-secondary: #475569;
        --ui-text-muted: #94a3b8;
        --ui-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.15);
        --ui-radius: 28px;
        --ui-radius-sm: 14px;

        --input-bg: #f8fafc;
        --input-border: #e2e8f0;
        --input-focus-border: var(--brand-orange);
        --input-focus-shadow: 0 0 0 4px rgba(249, 115, 22, 0.12);

        --wa-green: #25D366;
        --wa-green-dark: #128C7E;

        --font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* ============================================================
       GLOBAL RESET / BASE – CENTERING SEMPURNA
       ============================================================ */
    body.auth-page {
        background: var(--ui-bg);
        font-family: var(--font-family);
        min-height: 100vh;
        min-height: 100dvh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        margin: 0;
    }

    .auth-wrapper {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* ============================================================
       CARD UTAMA
       ============================================================ */
    .auth-card {
        border: none;
        border-radius: var(--ui-radius);
        overflow: hidden;
        background: var(--ui-card-bg);
        box-shadow: var(--ui-shadow);
        width: 100%;
        transition: transform 0.3s ease;
    }

    /* ============================================================
       SISI KIRI – BRANDING
       ============================================================ */
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
        width: 120px;
        height: 120px;
        object-fit: contain;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(6px);
        padding: 20px;
        border-radius: 30px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        margin-bottom: 1.75rem;
        transition: transform 0.3s ease;
    }
    .auth-logo:hover {
        transform: scale(1.02);
    }

    .auth-app-name {
        font-size: 2.8rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }

    .auth-app-sub {
        font-size: 1.1rem;
        font-weight: 400;
        opacity: 0.9;
        letter-spacing: 0.3px;
        margin-bottom: 1rem;
    }

    .auth-tagline {
        font-size: 0.95rem;
        line-height: 1.6;
        opacity: 0.85;
        max-width: 320px;
        margin: 0 auto;
        font-weight: 300;
    }

    /* ============================================================
       SISI KANAN – FORM
       ============================================================ */
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
        margin-bottom: 1.75rem;
    }
    .auth-mobile-logo img {
        width: 72px;
        height: 72px;
        object-fit: contain;
        margin-bottom: 0.5rem;
    }
    .auth-mobile-logo .app-name-mobile {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--brand-orange-dark);
        letter-spacing: -0.3px;
    }
    .auth-mobile-logo .app-sub-mobile {
        font-size: 0.8rem;
        color: var(--ui-text-muted);
        display: block;
    }

    .form-heading {
        margin-bottom: 2rem;
    }
    .form-heading h1 {
        font-size: 1.85rem;
        font-weight: 700;
        color: var(--ui-text-primary);
        letter-spacing: -0.02em;
        margin-bottom: 0.25rem;
    }
    .form-heading p {
        color: var(--ui-text-secondary);
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    /* ============================================================
       INPUT MODERN
       ============================================================ */
    .input-group-icon {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .input-group-icon .input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ui-text-muted);
        font-size: 1.1rem;
        pointer-events: none;
        transition: color 0.2s;
        z-index: 4;
    }

    .input-group-icon .form-control {
        height: 54px;
        padding: 0 48px 0 48px;
        font-size: 0.95rem;
        border: 2px solid var(--input-border);
        border-radius: var(--ui-radius-sm);
        background-color: var(--input-bg);
        color: var(--ui-text-primary);
        transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
    }

    .input-group-icon .form-control:focus {
        border-color: var(--input-focus-border);
        background-color: #ffffff;
        box-shadow: var(--input-focus-shadow);
        outline: none;
    }

    .input-group-icon .form-control.is-invalid {
        border-color: #dc3545;
        background-image: none;
    }
    .input-group-icon .form-control.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.12);
    }

    .password-toggle-btn {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--ui-text-muted);
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        cursor: pointer;
        transition: color 0.2s, background 0.2s;
        z-index: 4;
    }
    .password-toggle-btn:hover {
        color: var(--brand-orange-dark);
        background: rgba(249, 115, 22, 0.06);
    }

    .invalid-feedback-custom {
        font-size: 0.8rem;
        color: #dc3545;
        margin-top: 0.35rem;
        padding-left: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .invalid-feedback-custom i {
        font-size: 0.9rem;
    }

    /* ============================================================
       REMEMBER & FORGOT
       ============================================================ */
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0.5rem 0 1.5rem 0;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .form-check-custom .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        border: 2px solid var(--ui-border);
        border-radius: 4px;
        margin-top: 0.15rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .form-check-custom .form-check-input:checked {
        background-color: var(--brand-orange);
        border-color: var(--brand-orange);
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
    }
    .form-check-custom .form-check-label {
        font-size: 0.9rem;
        color: var(--ui-text-secondary);
        cursor: pointer;
        padding-left: 0.25rem;
    }

    .forgot-link {
        background: none;
        border: none;
        color: var(--brand-orange-dark);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        padding: 0;
        transition: color 0.2s, text-decoration 0.2s;
        text-decoration: none;
    }
    .forgot-link:hover {
        color: var(--brand-green-dark);
        text-decoration: underline;
    }

    /* ============================================================
       TOMBOL LOGIN
       ============================================================ */
    .btn-login {
        width: 100%;
        height: 54px;
        background: var(--brand-gradient);
        border: none;
        border-radius: var(--ui-radius-sm);
        font-weight: 600;
        font-size: 1rem;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(249, 115, 22, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        letter-spacing: 0.3px;
    }
    .btn-login:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(16, 185, 129, 0.35);
        color: #fff;
    }
    .btn-login:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
    }

    /* ============================================================
       FOOTER COPYRIGHT DI DALAM CARD
       ============================================================ */
    .login-footer {
        margin-top: 1.75rem;
        text-align: center;
        font-size: 0.8rem;
        color: var(--ui-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        flex-wrap: wrap;
        border-top: 1px solid var(--ui-border);
        padding-top: 1.25rem;
    }
    .login-footer a {
        color: var(--ui-text-secondary);
        text-decoration: none;
        transition: color 0.2s ease;
        font-weight: 500;
    }
    .login-footer a:hover {
        color: var(--brand-orange-dark);
    }
    .login-footer .dot-sep {
        display: inline-block;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: var(--ui-text-muted);
    }

    /* ============================================================
       MODAL FORGOT PASSWORD
       ============================================================ */
    .forgot-modal .modal-content {
        border: none;
        border-radius: var(--ui-radius);
        overflow: hidden;
        box-shadow: 0 30px 70px rgba(0,0,0,0.20);
    }
    .forgot-modal .modal-header {
        background: var(--brand-gradient);
        color: #fff;
        border-bottom: none;
        padding: 1.5rem 1.75rem;
        justify-content: center;
        position: relative;
    }
    .forgot-modal .modal-header .modal-title {
        font-weight: 700;
        font-size: 1.35rem;
        letter-spacing: -0.2px;
    }
    .forgot-modal .modal-header .btn-close {
        position: absolute;
        right: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        filter: brightness(0) invert(1);
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .forgot-modal .modal-header .btn-close:hover {
        opacity: 1;
    }
    .forgot-modal .modal-body {
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .wa-icon-circle {
        width: 80px;
        height: 80px;
        background: rgba(37,211,102,0.10);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
    }
    .wa-icon-circle i {
        font-size: 2.4rem;
        color: var(--wa-green-dark);
    }
    .modal-help-text {
        color: var(--ui-text-secondary);
        font-size: 0.95rem;
        max-width: 380px;
        margin: 0 auto 1.25rem auto;
        line-height: 1.6;
    }
    .modal-wa-number {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--ui-text-primary);
        letter-spacing: 0.5px;
        margin-bottom: 1.75rem;
        background: var(--input-bg);
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        display: inline-block;
        border: 1px solid var(--ui-border);
    }
    .btn-wa-chat {
        background: var(--wa-green);
        color: #fff;
        border: none;
        border-radius: var(--ui-radius-sm);
        padding: 12px 28px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }
    .btn-wa-chat:hover {
        background: var(--wa-green-dark);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37,211,102,0.35);
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 991.98px) {
        .auth-brand-panel {
            min-height: 260px;
            padding: 2.5rem 1.5rem;
        }
        .auth-logo {
            width: 80px;
            height: 80px;
            padding: 14px;
            margin-bottom: 1rem;
        }
        .auth-app-name {
            font-size: 2.2rem;
        }
        .auth-app-sub {
            font-size: 0.95rem;
        }
        .auth-tagline {
            font-size: 0.85rem;
            max-width: 100%;
        }
        .auth-form-panel {
            padding: 2.5rem 2rem;
        }
        .login-footer {
            font-size: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
        }
    }

    @media (max-width: 767.98px) {
        body.auth-page {
            padding: 1rem;
            align-items: flex-start;
            padding-top: 1.5rem;
        }
        .auth-card {
            border-radius: 20px;
        }
        .auth-brand-panel {
            min-height: 180px;
            padding: 1.75rem 1rem;
        }
        .auth-logo {
            width: 64px;
            height: 64px;
            padding: 10px;
            border-radius: 20px;
            margin-bottom: 0.75rem;
        }
        .auth-app-name {
            font-size: 1.6rem;
        }
        .auth-app-sub {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }
        .auth-tagline {
            display: none;
        }
        .auth-form-panel {
            padding: 1.75rem 1.25rem;
        }
        .auth-mobile-logo {
            display: block;
        }
        .form-heading h1 {
            font-size: 1.5rem;
        }
        .form-heading p {
            font-size: 0.85rem;
        }
        .input-group-icon .form-control {
            height: 48px;
            font-size: 0.9rem;
            padding: 0 44px 0 44px;
        }
        .btn-login {
            height: 48px;
            font-size: 0.95rem;
        }
        .forgot-modal .modal-body {
            padding: 2rem 1.25rem;
        }
        .modal-wa-number {
            font-size: 1.1rem;
        }
        .login-footer {
            font-size: 0.7rem;
            flex-direction: column;
            gap: 0.2rem;
        }
        .login-footer .dot-sep {
            display: none;
        }
    }

    @media (max-width: 575.98px) {
        .auth-form-panel {
            padding: 1.5rem 1rem;
        }
        .form-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .forgot-link {
            font-size: 0.85rem;
        }
        .btn-login {
            font-size: 0.9rem;
        }
        .auth-brand-panel {
            min-height: 140px;
            padding: 1.25rem 0.75rem;
        }
        .auth-app-name {
            font-size: 1.3rem;
        }
        .auth-logo {
            width: 54px;
            height: 54px;
            padding: 8px;
            margin-bottom: 0.5rem;
        }
        .login-footer {
            font-size: 0.65rem;
        }
    }

    .text-brand-orange {
        color: var(--brand-orange-dark);
    }
    .gap-2 {
        gap: 0.5rem;
    }
</style>

<div class="auth-wrapper">

    <div class="auth-card">
        <div class="row g-0">
            <!-- SISI KIRI -->
            <div class="col-lg-6 d-none d-lg-flex">
                <div class="auth-brand-panel w-100">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" class="auth-logo" />
                    <div class="auth-app-name">SIAP</div>
                    <div class="auth-app-sub">Sistem Informasi Administrasi Rapat</div>
                    <p class="auth-tagline">
                        Kelola ruang rapat, jadwal pertemuan, dan administrasi kehadiran secara terintegrasi, efektif, dan efisien.
                    </p>
                </div>
            </div>

            <!-- SISI KANAN - FORM LOGIN -->
            <div class="col-lg-6">
                <div class="auth-form-panel">

                    <!-- Mobile Logo -->
                    <div class="auth-mobile-logo">
                        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" />
                        <div class="app-name-mobile">SIAP</div>
                        <span class="app-sub-mobile">Sistem Informasi Administrasi Rapat</span>
                    </div>

                    <!-- Heading -->
                    <div class="form-heading">
                        <h1>Selamat Datang 👋</h1>
                        <p>Masuk ke akun Anda untuk melanjutkan</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- NIK -->
                        <div class="input-group-icon">
                            <i class="bi bi-person-badge input-icon"></i>
                            <input id="nip" type="text"
                                   class="form-control @error('nip') is-invalid @enderror"
                                   name="nip" value="{{ old('nip') }}"
                                   required autocomplete="off" autofocus
                                   placeholder="NIK Karyawan" aria-label="NIK">
                            @error('nip')
                                <div class="invalid-feedback-custom">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="input-group-icon">
                            <i class="bi bi-lock input-icon"></i>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password"
                                   placeholder="Kata sandi" aria-label="Password">
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan kata sandi">
                                <i class="bi bi-eye-slash" id="passwordEyeIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback-custom">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Options -->
                        <div class="form-options">
                            <div class="form-check form-check-custom">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                            <button type="button" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                Lupa kata sandi?
                            </button>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Masuk ke SIAP
                        </button>
                    </form>

                    <!-- FOOTER COPYRIGHT DI DALAM CARD (posisi seperti "Login menggunakan NIK..." sebelumnya) -->
                    <div class="login-footer">
                        <span>&copy; 2026</span>
                        <a href="#">IT PT Petrokopindo Cipta Selaras</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL FORGOT PASSWORD -->
<div class="modal fade forgot-modal" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Lupa Kata Sandi?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="wa-icon-circle">
                    <i class="bi bi-whatsapp"></i>
                </div>
                <p class="modal-help-text">
                    Jika Anda lupa kata sandi atau mengalami kendala login, hubungi admin SIAP melalui WhatsApp di nomor berikut:
                </p>
                <a href="https://wa.me/628xxxx?text=Halo%20Admin,%20saya%20lupa%20password%20akun%20SIAP%20saya."
                   target="_blank"
                   class="btn-wa-chat"
                   rel="noopener noreferrer">
                    <i class="bi bi-whatsapp"></i>
                    Chat Admin via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
@push('scripts')
<script>
    (function() {
        'use strict';

        function initPasswordToggle() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEyeIcon');

            if (!toggleBtn || !passwordInput || !eyeIcon) return;

            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.classList.toggle('bi-eye-slash');
                eyeIcon.classList.toggle('bi-eye');
                passwordInput.focus();
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPasswordToggle);
        } else {
            initPasswordToggle();
        }

    })();
</script>
@endpush
@endsection