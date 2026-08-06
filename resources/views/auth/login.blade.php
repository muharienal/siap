@extends('layouts.auth')

@section('title', 'Login - SIAP')

@section('content')
<style>
    /* ============================================================
       DESIGN TOKENS
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
        --ui-shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.08);
        --ui-radius: 28px;
        --ui-radius-sm: 14px;

        --input-bg: #f8fafc;
        --input-border: #e2e8f0;
        --input-focus-border: var(--brand-orange);
        --input-focus-shadow: 0 0 0 4px rgba(249, 115, 22, 0.12);
        --input-height: 54px;
        --input-height-mobile: 52px;

        --wa-green: #25D366;
        --wa-green-dark: #128C7E;

        --font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* ============================================================
       BASE RESET & CENTERING
       ============================================================ */
    * {
        -webkit-tap-highlight-color: transparent;
    }

    html, body {
        height: 100%;
    }

    body.auth-page {
        background: var(--ui-bg);
        font-family: var(--font-family);
        min-height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        margin: 0;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        overflow-x: hidden;
    }

    /* ============================================================
       WRAPPER & ZOOM DESKTOP
       ============================================================ */
    .auth-wrapper {
        width: 100%;
        max-width: 960px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    @media (min-width: 992px) {
        .auth-wrapper {
            zoom: 0.9;
        }
        /* Fallback untuk Firefox */
        @-moz-document url-prefix() {
            .auth-wrapper {
                zoom: 1;
                -moz-transform: scale(0.9);
                -moz-transform-origin: center center;
            }
        }
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
        position: relative;
        z-index: 1;
    }

    /* ============================================================
       BRAND PANEL
       ============================================================ */
    .auth-brand-panel {
        background: var(--brand-gradient);
        padding: 4rem 3rem;
        height: 100%;
        min-height: 520px;
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
        -webkit-backdrop-filter: blur(6px);
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
       FORM PANEL
       ============================================================ */
    .auth-form-panel {
        padding: 3.5rem 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--ui-card-bg);
        position: relative;
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
        height: var(--input-height);
        padding: 0 48px 0 48px;
        font-size: 0.95rem;
        border: 2px solid var(--input-border);
        border-radius: var(--ui-radius-sm);
        background-color: var(--input-bg);
        color: var(--ui-text-primary);
        transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        width: 100%;
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

    .input-group-icon .form-control:active {
        transform: scale(0.998);
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
        width: 36px;
        height: 36px;
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
    .password-toggle-btn:active {
        transform: translateY(-50%) scale(0.92);
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
       FORM OPTIONS
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
       BUTTON LOGIN
       ============================================================ */
    .btn-login {
        width: 100%;
        height: var(--input-height);
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
        cursor: pointer;
        position: relative;
        overflow: hidden;
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

    .btn-login::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.5s ease, height 0.5s ease;
    }
    .btn-login:active::after {
        width: 300px;
        height: 300px;
    }

    /* ============================================================
       FOOTER
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
       ANIMASI
       ============================================================ */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .auth-card {
        animation: fadeInUp 0.5s ease-out;
    }

    .auth-brand-panel > * {
        animation: slideInDown 0.6s ease-out;
    }

    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* ============================================================
       RESPONSIVE: TABLET
       ============================================================ */
    @media (max-width: 991.98px) {
        body.auth-page {
            padding: 1.5rem;
            align-items: center;
        }
        .auth-card {
            min-height: auto;
        }
        .auth-card > .row {
            min-height: auto;
        }
        .auth-brand-panel {
            min-height: 280px;
            padding: 2.5rem 1.5rem;
        }
        .auth-logo {
            width: 90px;
            height: 90px;
            padding: 16px;
            margin-bottom: 1rem;
        }
        .auth-app-name {
            font-size: 2.4rem;
        }
        .auth-app-sub {
            font-size: 1rem;
        }
        .auth-tagline {
            font-size: 0.9rem;
            max-width: 100%;
        }
        .auth-form-panel {
            padding: 2.5rem 2rem;
        }
    }

    /* ============================================================
       MOBILE NATIVE APP LAYOUT (< 768px)
       ============================================================ */
    @media (max-width: 767.98px) {
        body.auth-page {
            padding: 0;
            background: #fff;
            align-items: stretch;
        }

        .auth-wrapper {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            max-width: 100%;
        }

        .auth-card {
            border-radius: 0;
            box-shadow: none;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .auth-card > .row {
            flex: 1;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            flex-direction: column;
        }

        .auth-card > .row > [class*="col-"] {
            padding: 0;
            flex: none;
        }

        .auth-card > .row > .col-lg-6:nth-child(2) {
            flex: 1;
            display: flex;
        }

        /* Header kompak dengan rounded bottom */
        .auth-brand-panel {
            min-height: auto;
            padding: 3rem 1.5rem 3.5rem 1.5rem;
            border-radius: 0 0 32px 32px;
            flex-shrink: 0;
        }
        .auth-logo {
            width: 72px;
            height: 72px;
            padding: 12px;
            border-radius: 22px;
            margin-bottom: 0.75rem;
        }
        .auth-app-name {
            font-size: 1.8rem;
        }
        .auth-app-sub {
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        .auth-tagline {
            display: none;
        }

        /* Form panel "naik" menutupi header */
        .auth-form-panel {
            padding: 2.5rem 1.5rem 1.5rem 1.5rem;
            margin-top: -1.5rem;
            z-index: 2;
            position: relative;
            background: #fff;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -8px 16px rgba(0,0,0,0.05);
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .form-heading {
            margin-bottom: 1.5rem;
        }
        .form-heading h1 {
            font-size: 1.5rem;
        }
        .form-heading p {
            font-size: 0.85rem;
        }

        /* Input anti-zoom iOS */
        .input-group-icon {
            margin-bottom: 1rem;
        }
        .input-group-icon .form-control {
            height: var(--input-height-mobile);
            font-size: 16px;
            padding: 0 44px 0 44px;
            border-radius: 12px;
        }
        .input-group-icon .input-icon {
            left: 16px;
            font-size: 1rem;
        }

        .form-options {
            margin: 0.25rem 0 1.25rem 0;
        }

        .btn-login {
            height: var(--input-height-mobile);
            font-size: 0.95rem;
            border-radius: 12px;
        }

        /* Footer di dorong ke bawah */
        .login-footer {
            margin-top: auto;
            padding-top: 2rem;
            border-top: none;
            padding-bottom: env(safe-area-inset-bottom, 1rem);
        }

        /* Modal responsive */
        .forgot-modal .modal-dialog {
            margin: 1rem;
        }
        .forgot-modal .modal-content {
            border-radius: 20px;
        }
        .forgot-modal .modal-body {
            padding: 2rem 1.25rem;
        }
    }

    /* ============================================================
       SMALL MOBILE (< 576px)
       ============================================================ */
    @media (max-width: 575.98px) {
        .auth-brand-panel {
            padding: 2.5rem 1.5rem 3rem 1.5rem;
        }
        .auth-logo {
            width: 64px;
            height: 64px;
            padding: 10px;
            border-radius: 18px;
        }
        .auth-app-name {
            font-size: 1.6rem;
        }

        .auth-form-panel {
            padding: 2rem 1.25rem 1.25rem 1.25rem;
        }

        .form-heading h1 {
            font-size: 1.4rem;
        }

        .input-group-icon .form-control {
            height: 50px;
            font-size: 16px;
        }

        .form-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .btn-login {
            font-size: 0.9rem;
        }

        .login-footer {
            font-size: 0.7rem;
        }
    }

    /* ============================================================
       LANDSCAPE MOBILE
       ============================================================ */
    @media (max-width: 991.98px) and (orientation: landscape) and (max-height: 500px) {
        body.auth-page {
            padding: 1rem;
            align-items: center;
            background: var(--ui-bg);
        }
        .auth-wrapper {
            min-height: auto;
        }
        .auth-card {
            min-height: auto;
            border-radius: var(--ui-radius);
            box-shadow: var(--ui-shadow);
        }
        .auth-card > .row {
            min-height: auto;
            flex-direction: row;
        }
        .auth-brand-panel {
            min-height: 100%;
            padding: 1.5rem;
            border-radius: 0;
        }
        .auth-logo {
            width: 56px;
            height: 56px;
            padding: 8px;
            margin-bottom: 0.5rem;
        }
        .auth-app-name {
            font-size: 1.4rem;
        }
        .auth-app-sub {
            font-size: 0.75rem;
        }
        .auth-tagline {
            display: none;
        }
        .auth-form-panel {
            padding: 1.5rem;
            justify-content: center;
            margin-top: 0;
            border-radius: 0;
            box-shadow: none;
        }
        .form-heading {
            margin-bottom: 1rem;
        }
        .form-heading h1 {
            font-size: 1.2rem;
        }
        .input-group-icon {
            margin-bottom: 0.75rem;
        }
        .input-group-icon .form-control {
            height: 44px;
            font-size: 16px;
        }
        .btn-login {
            height: 44px;
        }
        .login-footer {
            margin-top: 1rem;
            padding-top: 1rem;
        }
    }
</style>

<div class="auth-wrapper">

    <div class="auth-card">
        <div class="row g-0">

            <!-- ====================================================
                 SISI KIRI – BRANDING
                 ==================================================== -->
            <div class="col-lg-6">
                <div class="auth-brand-panel w-100">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" class="auth-logo" />
                    <div class="auth-app-name">SIAP</div>
                    <div class="auth-app-sub">Sistem Informasi Administrasi Rapat</div>
                    <p class="auth-tagline">
                        Kelola ruang rapat, jadwal pertemuan, dan administrasi kehadiran secara terintegrasi, efektif, dan efisien.
                    </p>
                </div>
            </div>

            <!-- ====================================================
                 SISI KANAN – FORM LOGIN
                 ==================================================== -->
            <div class="col-lg-6">
                <div class="auth-form-panel">

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

                    <!-- FOOTER COPYRIGHT -->
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

        function preventDoubleSubmit() {
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-login');
            if (!form || !submitBtn) return;

            let isSubmitting = false;
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';

                setTimeout(function() {
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk ke SIAP';
                }, 5000);
            });
        }

        function init() {
            initPasswordToggle();
            preventDoubleSubmit();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
@endpush
@endsection