@extends('layouts.auth')

@section('title', 'Login - SIAP')

@section('content')
<style>
    /* ============================================================
       DESIGN TOKENS — palet disegarkan: lebih terang & hidup,
       tidak lagi terkesan gelap/tua seperti oranye-hijau sebelumnya.
       ============================================================ */
    :root {
        --brand-orange: #FF9142;
        --brand-orange-light: #FFB27A;
        --brand-orange-dark: #F2751A;
        --brand-green: #22C55E;
        --brand-green-light: #4ADE80;
        --brand-green-dark: #16A34A;
        --brand-gradient: linear-gradient(135deg, var(--brand-orange) 0%, var(--brand-green) 100%);
        --brand-gradient-hover: linear-gradient(135deg, var(--brand-orange-dark) 0%, var(--brand-green-dark) 100%);
        --brand-gradient-soft: linear-gradient(135deg, rgba(255,145,66,0.10) 0%, rgba(34,197,94,0.10) 100%);

        --ui-bg: #f6f8fb;
        --ui-card-bg: #ffffff;
        --ui-border: #e7ebf1;
        --ui-text-primary: #10182b;
        --ui-text-secondary: #5b6577;
        --ui-text-muted: #97a1b3;
        --ui-shadow: 0 30px 70px -20px rgba(16, 24, 43, 0.18);
        --ui-shadow-sm: 0 4px 14px rgba(16, 24, 43, 0.06);
        --ui-radius: 28px;
        --ui-radius-sm: 14px;

        --input-bg: #f8fafc;
        --input-border: #e7ebf1;
        --input-focus-border: var(--brand-orange);
        --input-focus-shadow: 0 0 0 4px rgba(255, 145, 66, 0.14);
        --input-height: 50px;
        --input-height-mobile: 52px;

        --wa-green: #25D366;
        --wa-green-dark: #128C7E;

        --font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* ============================================================
       BASE RESET & CENTERING
       ============================================================ */
    * { -webkit-tap-highlight-color: transparent; }
    html, body { height: 100%; }

    body.auth-page {
        background: var(--ui-bg);
        font-family: var(--font-family);
        min-height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 2rem;
        margin: 0;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        overflow-x: hidden;
        position: relative;
    }

    /* Wash gradient dekoratif — sangat halus, bukan warna gelap penuh */
    body.auth-page::before {
        content: '';
        position: fixed; top: -15%; right: -10%; width: 50vw; height: 50vw;
        background: radial-gradient(circle, rgba(255,145,66,0.08), transparent 70%);
        pointer-events: none; z-index: 0;
    }
    body.auth-page::after {
        content: '';
        position: fixed; bottom: -15%; left: -10%; width: 42vw; height: 42vw;
        background: radial-gradient(circle, rgba(34,197,94,0.08), transparent 70%);
        pointer-events: none; z-index: 0;
    }

    /* ============================================================
       WRAPPER
       FIX (bug modal tertutup layer gelap): sebelumnya elemen ini
       dinamai `.auth-wrapper` — nama yang SAMA PERSIS dengan div
       pembungkus di layouts/auth.blade.php (yang membungkus SELURUH
       @yield('content'), termasuk modal Lupa Kata Sandi). Karena CSS
       di sini tidak ter-scope, aturan `position:relative; z-index:1`
       ikut kena ke wrapper milik LAYOUT itu juga — akibatnya SELURUH
       konten halaman (termasuk modal) jadi "terjebak" dalam stacking
       context ber-z-index rendah, kalah tumpuk dari modal-backdrop
       Bootstrap (z-index:1050), jadi modal terlihat ada tapi tidak
       bisa diklik apa pun (ketutup lapisan gelap transparan).
       Diganti nama jadi `.login-panel` yang unik, tidak bentrok
       dengan elemen manapun di layout.
       ============================================================ */
    .login-panel {
        width: 100%;
        max-width: 840px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
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
        position: relative;
        z-index: 1;
    }

    /* ============================================================
       BRAND PANEL
       ============================================================ */
    .auth-brand-panel {
        background: var(--brand-gradient);
        padding: 3.1rem 2.4rem;
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
        position: absolute; inset: 0;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,0.14) 0%, transparent 55%),
            radial-gradient(circle at 85% 80%, rgba(255,255,255,0.10) 0%, transparent 50%);
        pointer-events: none;
    }

    /* Aksen cahaya statis halus — diposisikan pas di tengah panel
       supaya tetap proporsional berapa pun tinggi panelnya. */
    .auth-brand-panel::after {
        content: '';
        position: absolute; top: 50%; left: 50%; width: 340px; height: 340px;
        transform: translate(-50%, -50%);
        background: radial-gradient(circle, rgba(255,255,255,0.10) 0%, transparent 65%);
        pointer-events: none;
    }

    .auth-brand-panel > * { position: relative; z-index: 2; }

    .auth-app-name {
        font-size: 2.2rem; font-weight: 700; letter-spacing: -0.01em; line-height: 1.15;
        margin-bottom: 0.3rem;
    }
    .auth-app-sub {
        font-size: 1rem; font-weight: 500; opacity: 0.95; letter-spacing: 0.2px;
        margin-bottom: 0.9rem;
    }
    .auth-tagline {
        font-size: 0.88rem; line-height: 1.62; opacity: 0.88; max-width: 290px;
        margin: 0 auto; font-weight: 400;
    }

    /* Dua logo disandingkan sejajar — SIAP & PCS setara, bukan lagi
       logo utama + badge teks kecil. */
    .brand-logo-row {
        display: flex; align-items: center; justify-content: center; gap: 14px;
        margin-bottom: 1.4rem;
    }
    .brand-logo-box {
        width: 84px; height: 84px;
        background: rgba(255,255,255,0.16);
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        padding: 13px; border-radius: 24px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.13);
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        flex-shrink: 0;
    }
    .brand-logo-box:hover { transform: scale(1.05) rotate(-2deg); }
    .brand-logo-box svg { width: 100%; height: 100%; display: block; border-radius: 14px; overflow: hidden; }
    .brand-logo-box img { width: 100%; height: 100%; object-fit: contain; border-radius: 14px; }

    /* ============================================================
       FORM PANEL
       ============================================================ */
    .auth-form-panel {
        padding: 2.9rem 2.7rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--ui-card-bg);
        position: relative;
    }

    .form-heading { margin-bottom: 1.9rem; }
    .form-heading h1 {
        font-size: 1.55rem; font-weight: 700; color: var(--ui-text-primary);
        letter-spacing: -0.02em; margin-bottom: 0.3rem;
    }
    .form-heading p {
        color: var(--ui-text-secondary); font-size: 0.92rem; margin-bottom: 0; font-weight: 500;
    }

    /* ============================================================
       FIELD INPUT — flexbox murni: ikon dan input sama-sama anak flex
       dalam satu kontainer, jadi sejajar otomatis oleh browser — tidak
       mungkin salah posisi.
       ============================================================ */
    .field-group { margin-bottom: 1.05rem; }

    .field-control {
        display: flex; align-items: center; gap: 12px;
        height: var(--input-height); padding: 0 16px;
        border: 1.5px solid var(--input-border); border-radius: var(--ui-radius-sm);
        background-color: var(--input-bg);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .field-control:focus-within {
        border-color: var(--input-focus-border);
        background-color: #ffffff;
        box-shadow: var(--input-focus-shadow);
    }
    .field-control.has-error { border-color: #ef4444; background-color: #fef7f7; }
    .field-control.has-error:focus-within { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12); }

    .field-icon {
        font-size: 1.02rem; color: var(--ui-text-muted); flex-shrink: 0;
        width: 20px; text-align: center; transition: color 0.2s ease;
    }
    .field-control:focus-within .field-icon { color: var(--brand-orange); }
    .field-control.has-error .field-icon { color: #ef4444; }

    .field-input {
        flex: 1; min-width: 0; height: 100%;
        border: none; background: none; outline: none;
        font-size: 0.94rem; font-weight: 500; color: var(--ui-text-primary);
        font-family: inherit;
    }
    .field-input::placeholder { color: var(--ui-text-muted); font-weight: 400; }

    .field-toggle-btn {
        flex-shrink: 0; background: none; border: none; color: var(--ui-text-muted);
        padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
        border-radius: 50%; cursor: pointer; transition: color 0.2s, background 0.2s; font-size: 1rem;
    }
    .field-toggle-btn:hover { color: var(--brand-orange-dark); background: rgba(255, 145, 66, 0.08); }
    .field-toggle-btn:active { transform: scale(0.92); }

    /* ============================================================
       ALERT MODERN — komponen acuan untuk seluruh halaman (error,
       sukses, peringatan, info). Kartu ringan dengan ikon + border
       tipis, bukan sekadar teks merah polos.
       ============================================================ */
    .alert-modern {
        display: flex; align-items: flex-start; gap: 9px;
        padding: 10px 13px; border-radius: 11px;
        font-size: 0.81rem; font-weight: 500; line-height: 1.5;
        border: 1px solid transparent;
    }
    .alert-modern .alert-modern-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
    .alert-modern.alert-error { background: #fef2f2; border-color: #fecdd3; color: #c0263c; }
    .alert-modern.alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
    .alert-modern.alert-warning { background: #fffbeb; border-color: #fde68a; color: #b45309; }
    .alert-modern.alert-info { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }

    .field-alert { margin-top: 0.5rem; }

    /* ============================================================
       FORM OPTIONS
       ============================================================ */
    .form-options {
        display: flex; justify-content: space-between; align-items: center;
        margin: 0.4rem 0 1.4rem 0; flex-wrap: wrap; gap: 0.5rem;
    }

    .form-check-custom .form-check-input {
        width: 1.1rem; height: 1.1rem; border: 1.5px solid var(--ui-border);
        border-radius: 5px; margin-top: 0.15rem; cursor: pointer; transition: all 0.15s;
    }
    .form-check-custom .form-check-input:checked {
        background-color: var(--brand-orange); border-color: var(--brand-orange);
        box-shadow: 0 0 0 3px rgba(255, 145, 66, 0.18);
    }
    .form-check-custom .form-check-label {
        font-size: 0.86rem; color: var(--ui-text-secondary); cursor: pointer;
        padding-left: 0.25rem; font-weight: 500;
    }

    .forgot-link {
        background: none; border: none; color: var(--brand-orange-dark);
        font-weight: 600; font-size: 0.86rem; cursor: pointer; padding: 0;
        transition: color 0.2s; text-decoration: none;
    }
    .forgot-link:hover { color: var(--brand-green-dark); text-decoration: underline; }

    /* ============================================================
       BUTTON LOGIN
       ============================================================ */
    .btn-login {
        width: 100%; height: var(--input-height);
        background: var(--brand-gradient); border: none; border-radius: var(--ui-radius-sm);
        font-weight: 600; font-size: 0.94rem; color: #fff;
        transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.3s ease;
        box-shadow: 0 8px 22px rgba(255, 145, 66, 0.32);
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        letter-spacing: 0.1px; cursor: pointer; position: relative; overflow: hidden;
    }
    .btn-login:hover {
        background: var(--brand-gradient-hover); transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(34, 197, 94, 0.32); color: #fff;
    }
    .btn-login:active { transform: translateY(0); box-shadow: 0 6px 14px rgba(255, 145, 66, 0.28); }
    .btn-login:disabled { opacity: 0.75; cursor: not-allowed; transform: none; }

    .btn-login::after {
        content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0;
        border-radius: 50%; background: rgba(255, 255, 255, 0.3); transform: translate(-50%, -50%);
        transition: width 0.5s ease, height 0.5s ease;
    }
    .btn-login:active::after { width: 320px; height: 320px; }

    /* ============================================================
       FOOTER
       ============================================================ */
    .login-footer {
        margin-top: 1.75rem; text-align: center; font-size: 0.76rem; color: var(--ui-text-muted);
        display: flex; align-items: center; justify-content: center; gap: 0.4rem; flex-wrap: wrap;
        border-top: 1px solid var(--ui-border); padding-top: 1.25rem;
    }
    .login-footer a {
        color: var(--ui-text-secondary); text-decoration: none; transition: color 0.2s ease; font-weight: 600;
    }
    .login-footer a:hover { color: var(--brand-orange-dark); }

    /* ============================================================
       MODAL FORGOT PASSWORD
       background & z-index dibuat eksplisit supaya modal selalu
       solid/opaque, dan sekarang TIDAK LAGI terjebak stacking context
       manapun karena `.login-panel` (lihat catatan di atas) sudah
       nama unik yang tidak bentrok dengan elemen apa pun di layout.
       ============================================================ */
    .forgot-modal { z-index: 1060; }
    .forgot-modal .modal-content {
        border: none; border-radius: var(--ui-radius); overflow: hidden;
        box-shadow: 0 30px 70px rgba(16,24,43,0.20);
        background-color: #ffffff;
        background-clip: padding-box;
        isolation: isolate;
    }
    .forgot-modal .modal-header {
        background: var(--brand-gradient); color: #fff; border-bottom: none;
        padding: 1.5rem 1.75rem; justify-content: center; position: relative;
    }
    .forgot-modal .modal-header .modal-title { font-weight: 700; font-size: 1.2rem; letter-spacing: -0.1px; }
    .forgot-modal .modal-header .btn-close {
        position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%);
        filter: brightness(0) invert(1); opacity: 0.85; transition: opacity 0.2s;
    }
    .forgot-modal .modal-header .btn-close:hover { opacity: 1; }
    .forgot-modal .modal-body { padding: 2.5rem 2rem; text-align: center; background-color: #ffffff; }
    .wa-icon-circle {
        width: 78px; height: 78px; background: rgba(37,211,102,0.10); border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;
    }
    .wa-icon-circle i { font-size: 2.3rem; color: var(--wa-green-dark); }
    .modal-help-text {
        color: var(--ui-text-secondary); font-size: 0.93rem; max-width: 380px;
        margin: 0 auto 1.25rem auto; line-height: 1.6; font-weight: 500;
    }
    .btn-wa-chat {
        background: var(--wa-green); color: #fff; border: none; border-radius: var(--ui-radius-sm);
        padding: 12px 28px; font-weight: 600; font-size: 0.92rem; transition: all 0.3s ease;
        display: inline-flex; align-items: center; gap: 10px; text-decoration: none;
    }
    .btn-wa-chat:hover {
        background: var(--wa-green-dark); color: #fff; transform: translateY(-2px);
        box-shadow: 0 10px 26px rgba(37,211,102,0.35);
    }

    /* ============================================================
       BANTUAN — dipindah ke DALAM panel kiri, tepat di bawah tagline,
       bukan mengambang di viewport lagi. Tetap dibuat mencolok
       (pill putih kontras dengan gradient panel, ikon WA dengan
       pulse ring, teks "Klik disini" eksplisit) supaya user yang
       bingung langsung tahu ini bisa diklik tanpa perlu menebak.
       ============================================================ */
    .auth-help-inline {
        margin-top: 1.5rem;
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255,255,255,0.16);
        backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        border: 1.5px solid rgba(255,255,255,0.35);
        border-radius: 999px; padding: 10px 20px 10px 12px;
        color: #fff; text-decoration: none; cursor: pointer;
        transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), background 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 8px 22px rgba(0,0,0,0.12);
    }
    .auth-help-inline:hover {
        transform: translateY(-2px) scale(1.02);
        background: rgba(255,255,255,0.24);
        box-shadow: 0 12px 28px rgba(0,0,0,0.16);
        color: #fff;
    }
    .auth-help-inline-icon {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        background: var(--wa-green); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; position: relative;
    }
    .auth-help-inline-icon .pulse-ring {
        position: absolute; inset: 0; border-radius: 50%;
        border: 2px solid #fff; animation: loginHelpPulse 2s ease-out infinite;
    }
    @keyframes loginHelpPulse {
        0% { transform: scale(1); opacity: 0.7; }
        100% { transform: scale(1.7); opacity: 0; }
    }
    .auth-help-inline-text { text-align: left; line-height: 1.3; }
    .auth-help-inline-text .label { font-size: 0.84rem; font-weight: 700; }
    .auth-help-inline-text .sub { font-size: 0.71rem; opacity: 0.85; font-weight: 500; }

    @media (max-width: 575.98px) {
        .auth-help-inline { margin-top: 1.1rem; padding: 8px 16px 8px 10px; gap: 8px; }
        .auth-help-inline-icon { width: 28px; height: 28px; font-size: 0.9rem; }
        .auth-help-inline-text .label { font-size: 0.76rem; }
        .auth-help-inline-text .sub { font-size: 0.65rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .auth-help-inline-icon .pulse-ring { animation: none; }
    }

    /* ============================================================
       ANIMASI MASUK
       ============================================================ */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-18px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInScale { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

    .auth-card { animation: fadeInUp 0.55s cubic-bezier(0.16, 1, 0.3, 1); }
    .brand-logo-row { animation: fadeInScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s both; }
    .auth-app-name, .auth-app-sub, .auth-tagline, .auth-help-inline { animation: fadeInDown 0.55s ease-out both; }
    .auth-app-name { animation-delay: 0.18s; }
    .auth-app-sub { animation-delay: 0.24s; }
    .auth-tagline { animation-delay: 0.3s; }
    .auth-help-inline { animation-delay: 0.36s; }
    .form-heading, .field-group, .form-options, .btn-login {
        animation: fadeInUp 0.5s ease-out both;
    }
    .form-heading { animation-delay: 0.2s; }
    .field-group:nth-of-type(1) { animation-delay: 0.28s; }
    .field-group:nth-of-type(2) { animation-delay: 0.34s; }
    .form-options { animation-delay: 0.4s; }
    .btn-login { animation-delay: 0.46s; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }

    /* ============================================================
       RESPONSIVE: TABLET
       ============================================================ */
    @media (max-width: 991.98px) {
        body.auth-page { padding: 1.5rem; align-items: center; }
        .auth-card { min-height: auto; }
        .auth-card > .row { min-height: auto; }
        .auth-brand-panel { min-height: 300px; padding: 2.5rem 1.75rem; }
        .brand-logo-box { width: 74px; height: 74px; padding: 11px; border-radius: 20px; }
        .brand-logo-row { margin-bottom: 1rem; }
        .auth-app-name { font-size: 2.2rem; }
        .auth-app-sub { font-size: 1rem; }
        .auth-tagline { font-size: 0.88rem; max-width: 100%; }
        .auth-form-panel { padding: 2.5rem 2rem; }
    }

    /* ============================================================
       MOBILE NATIVE APP LAYOUT (< 768px)
       ============================================================ */
    @media (max-width: 767.98px) {
        body.auth-page { padding: 0; background: #fff; align-items: stretch; }
        body.auth-page::before, body.auth-page::after { display: none; }
        .auth-brand-panel::after { width: 200px; height: 200px; }

        .login-panel { min-height: 100vh; min-height: 100dvh; width: 100%; max-width: 100%; }

        .auth-card {
            border-radius: 0; box-shadow: none; min-height: 100vh; min-height: 100dvh;
            display: flex; flex-direction: column; background: #fff;
        }
        .auth-card > .row { flex: 1; min-height: 100vh; min-height: 100dvh; margin: 0; flex-direction: column; }
        .auth-card > .row > [class*="col-"] { padding: 0; flex: none; }
        .auth-card > .row > .col-lg-6:nth-child(2) { flex: 1; display: flex; }

        .auth-brand-panel {
            min-height: auto; padding: 2.75rem 1.5rem 3.25rem 1.5rem;
            border-radius: 0 0 32px 32px; flex-shrink: 0;
        }
        .brand-logo-box { width: 56px; height: 56px; padding: 8px; border-radius: 16px; }
        .brand-logo-row { margin-bottom: 0.7rem; gap: 10px; }
        .auth-app-name { font-size: 1.7rem; }
        .auth-app-sub { font-size: 0.82rem; margin-bottom: 0.65rem; }
        .auth-tagline { display: none; }

        .auth-form-panel {
            padding: 2.25rem 1.5rem 1.5rem 1.5rem; margin-top: -1.5rem; z-index: 2; position: relative;
            background: #fff; border-radius: 24px 24px 0 0; box-shadow: 0 -10px 20px rgba(16,24,43,0.05);
            flex: 1; display: flex; flex-direction: column; width: 100%;
        }

        .form-heading { margin-bottom: 1.4rem; }
        .form-heading h1 { font-size: 1.4rem; }
        .form-heading p { font-size: 0.84rem; }

        .field-group { margin-bottom: 0.85rem; }
        .field-control { height: var(--input-height-mobile); padding: 0 14px; border-radius: 12px; }
        .field-icon { font-size: 0.95rem; }
        .field-input { font-size: 16px; }

        .form-options { margin: 0.25rem 0 1.15rem 0; }
        .btn-login { height: var(--input-height-mobile); font-size: 0.92rem; border-radius: 12px; }

        .login-footer { margin-top: auto; padding-top: 1.75rem; border-top: none; padding-bottom: env(safe-area-inset-bottom, 1rem); }

        .forgot-modal .modal-dialog { margin: 1rem; }
        .forgot-modal .modal-content { border-radius: 20px; }
        .forgot-modal .modal-body { padding: 2rem 1.25rem; }
    }

    /* ============================================================
       SMALL MOBILE (< 576px)
       ============================================================ */
    @media (max-width: 575.98px) {
        .auth-brand-panel { padding: 2.25rem 1.25rem 2.75rem 1.25rem; }
        .brand-logo-box { width: 50px; height: 50px; padding: 7px; border-radius: 15px; }
        .brand-logo-row { gap: 8px; }
        .auth-app-name { font-size: 1.5rem; }
        .auth-form-panel { padding: 1.85rem 1.15rem 1.15rem 1.15rem; }
        .form-heading h1 { font-size: 1.3rem; }
        .field-control { height: 50px; }
        .field-input { font-size: 16px; }
        .form-options { flex-direction: column; align-items: flex-start; gap: 0.7rem; }
        .btn-login { font-size: 0.88rem; }
        .login-footer { font-size: 0.68rem; }
    }

    /* ============================================================
       LANDSCAPE MOBILE
       ============================================================ */
    @media (max-width: 991.98px) and (orientation: landscape) and (max-height: 500px) {
        body.auth-page { padding: 1rem; align-items: center; background: var(--ui-bg); }
        .login-panel { min-height: auto; }
        .auth-card { min-height: auto; border-radius: var(--ui-radius); box-shadow: var(--ui-shadow); }
        .auth-card > .row { min-height: auto; flex-direction: row; }
        .auth-brand-panel { min-height: 100%; padding: 1.5rem; border-radius: 0; }
        .brand-logo-box { width: 46px; height: 46px; padding: 6px; }
        .brand-logo-row { margin-bottom: 0.5rem; }
        .auth-app-name { font-size: 1.3rem; }
        .auth-app-sub { font-size: 0.72rem; }
        .auth-tagline { display: none; }
        .auth-form-panel { padding: 1.5rem; margin-top: 0; border-radius: 0; box-shadow: none; }
        .form-heading { margin-bottom: 0.9rem; }
        .form-heading h1 { font-size: 1.15rem; }
        .field-group { margin-bottom: 0.6rem; }
        .field-control { height: 42px; }
        .field-input { font-size: 15px; }
        .btn-login { height: 42px; }
        .login-footer { margin-top: 0.85rem; padding-top: 0.85rem; }
        .auth-help-inline { display: none; }
    }

    /* ============================================================
       ULTRA-WIDE / LAYAR BESAR — jaga proporsi tetap nyaman dibaca
       ============================================================ */
    @media (min-width: 1600px) {
        .login-panel { max-width: 980px; }
    }
</style>

<div class="login-panel">

    <div class="auth-card">
        <div class="row g-0">

            <!-- ====================================================
                 SISI KIRI – BRANDING
                 ==================================================== -->
            <div class="col-lg-6">
                <div class="auth-brand-panel w-100">
                    <div class="brand-logo-row">
                        <div class="brand-logo-box siap-box">
                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                                <defs>
                                    <linearGradient id="siapIconGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#f2994a"/>
                                        <stop offset="50%" stop-color="#8fa23f"/>
                                        <stop offset="100%" stop-color="#1fae6e"/>
                                    </linearGradient>
                                </defs>
                                <rect x="4" y="4" width="192" height="192" rx="44" fill="url(#siapIconGrad)"/>
                                <g transform="translate(45,51.1) scale(0.191)" fill="#ffffff">
                                    <path d="M288 0a64 64 0 1 1 0 128 64 64 0 1 1 0-128zm96 312c0 25-12.7 47-32 59.9l0 44.1-128 0 0-44.1C204.7 359 192 337 192 312l0-40c0-53 43-96 96-96s96 43 96 96l0 40zm16 104l0-27.3c19.9-22.5 32-52.2 32-84.7l0-32c0-26.7-7.3-51.6-19.9-73.1 11.1-4.5 23.2-6.9 35.9-6.9 53 0 96 43 96 96l0 32c0 23.7-12.9 44.4-32 55.4l0 40.6-112 0zM64 375.4C44.9 364.4 32 343.7 32 320l0-32c0-53 43-96 96-96 12.7 0 24.8 2.5 35.9 6.9-12.6 21.4-19.9 46.4-19.9 73.1l0 32c0 32.5 12.1 62.1 32 84.7l0 27.3-112 0 0-40.6zM128 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112zm320 0a56 56 0 1 1 0 112 56 56 0 1 1 0-112zM24 464l528 0c13.3 0 24 10.7 24 24s-10.7 24-24 24L24 512c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                                </g>
                            </svg>
                        </div>
                        <div class="brand-logo-box company-box">
                            <img src="{{ asset('assets/images/icon/icon.png') }}" alt="PT Petrokopindo Cipta Selaras">
                        </div>
                    </div>
                    <div class="auth-app-name">SIAP</div>
                    <div class="auth-app-sub">Sistem Informasi Administrasi Rapat</div>
                    <p class="auth-tagline">
                        Kelola ruang rapat, jadwal pertemuan, dan administrasi kehadiran secara terintegrasi, efektif, dan efisien.
                    </p>

                    <a href="https://wa.me/6281332867075?text=Halo%20Admin%20SIAP%2C%20saya%20butuh%20bantuan."
                       target="_blank" rel="noopener noreferrer" class="auth-help-inline" aria-label="Butuh bantuan? Klik untuk chat admin via WhatsApp">
                        <span class="auth-help-inline-icon">
                            <span class="pulse-ring"></span>
                            <i class="bi bi-whatsapp"></i>
                        </span>
                        <span class="auth-help-inline-text">
                            <span class="label d-block">Butuh Bantuan?</span>
                            <span class="sub d-block">Klik disini untuk chat admin SIAP</span>
                        </span>
                    </a>
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

                        {{-- Flash message umum (bukan validasi field) — dipakai
                             antara lain saat sesi/token CSRF sudah kedaluwarsa,
                             supaya pesannya jelas bukan halaman error mentah. --}}
                        @if(session('error'))
                            <div class="alert-modern alert-error field-alert" style="margin-bottom: 1.25rem;">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                            </div>
                        @endif
                        @if(session('success'))
                            <div class="alert-modern" style="margin-bottom: 1.25rem; background: #f0fdf4; border-color: #bbf7d0; color: #15803d;">
                                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                            </div>
                        @endif

                        <!-- NIK -->
                        <div class="field-group">
                            <div class="field-control @error('nip') has-error @enderror">
                                <i class="bi bi-person-badge field-icon"></i>
                                <input id="nip" type="text"
                                       class="field-input"
                                       name="nip" value="{{ old('nip', request()->cookie('remembered_nip')) }}"
                                       required autocomplete="off" autofocus
                                       placeholder="NIK Karyawan" aria-label="NIK">
                            </div>
                            @error('nip')
                                <div class="alert-modern alert-error field-alert">
                                    <i class="bi bi-exclamation-circle-fill alert-modern-icon"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="field-group">
                            <div class="field-control @error('password') has-error @enderror">
                                <i class="bi bi-lock field-icon"></i>
                                <input id="password" type="password"
                                       class="field-input"
                                       name="password" required autocomplete="current-password"
                                       placeholder="Kata sandi" aria-label="Password">
                                <button type="button" class="field-toggle-btn" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan kata sandi">
                                    <i class="bi bi-eye-slash" id="passwordEyeIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="alert-modern alert-error field-alert">
                                    <i class="bi bi-exclamation-circle-fill alert-modern-icon"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Options -->
                        <div class="form-options">
                            <div>
                                {{-- FIX: sebelumnya cuma dicentang ulang kalau submit
                                     sebelumnya gagal validasi (old('remember')) — sekarang
                                     juga otomatis tercentang kalau memang ada NIK yang
                                     "diingat" dari kunjungan sebelumnya, supaya statusnya
                                     konsisten dengan field NIK yang sudah otomatis terisi. --}}
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                       {{ (old('remember') || request()->cookie('remembered_nip')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
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