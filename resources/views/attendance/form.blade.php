@extends('layouts.auth')

@section('title', 'Presensi Meeting')

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
        padding: 2rem 1.5rem;
    }

    .attend-wrapper {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
    }

    .attend-card {
        border: none;
        border-radius: var(--ui-radius);
        overflow: hidden;
        background: var(--ui-card-bg);
        box-shadow: var(--ui-shadow);
        width: 100%;
        display: grid;
        /* 3 kolom: info rapat | form | daftar hadir — kolom form
           diberi sedikit lebih lebar karena isinya (label + input)
           butuh ruang paling banyak dibanding dua kolom lain. */
        grid-template-columns: 0.92fr 1.08fr 0.92fr;
    }

    /* ================= KOLOM 1 — INFORMASI RAPAT ================= */
    .col-meeting-info {
        background: var(--brand-gradient);
        padding: 2.75rem 2.1rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .col-meeting-info::before {
        content: '';
        position: absolute; inset: 0;
        background-image:
            radial-gradient(circle at 20% 20%, rgba(255,255,255,0.10) 0%, transparent 55%),
            radial-gradient(circle at 85% 80%, rgba(255,255,255,0.08) 0%, transparent 50%);
        pointer-events: none;
    }
    .col-meeting-info > * { position: relative; z-index: 2; }

    .attend-logo {
        width: 62px; height: 62px; object-fit: contain;
        background: rgba(255,255,255,0.16);
        backdrop-filter: blur(6px);
        padding: 11px;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.12);
        margin-bottom: 1.4rem;
    }
    .col-meeting-info h2 {
        font-size: 1.55rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 1.6rem; line-height: 1.2;
    }

    .meeting-info-list { display: flex; flex-direction: column; gap: 1.05rem; }
    .meeting-info-item { display: flex; align-items: flex-start; gap: 11px; }
    .meeting-info-item .info-icon {
        width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
        background: rgba(255,255,255,0.16);
        display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
    }
    .meeting-info-item .info-text { min-width: 0; }
    .meeting-info-item .info-label {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        color: rgba(255,255,255,0.75); margin-bottom: 2px;
    }
    .meeting-info-item .info-value { font-size: 0.94rem; font-weight: 600; line-height: 1.4; word-break: break-word; }

    /* ================= KOLOM 2 — FORM ================= */
    .col-form {
        padding: 2.75rem 2.3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--ui-card-bg);
        border-left: 1px solid var(--ui-border);
        border-right: 1px solid var(--ui-border);
    }

    .attend-mobile-header { display: none; }

    .form-title { font-size: 1.4rem; font-weight: 700; color: var(--ui-text-primary); letter-spacing: -0.01em; margin-bottom: 0.35rem; }
    .form-subtitle { color: var(--ui-text-secondary); font-size: 0.9rem; margin-bottom: 1.6rem; }

    .form-label {
        font-weight: 600; font-size: 0.85rem; color: var(--ui-text-primary);
        margin-bottom: 0.45rem; display: flex; align-items: center;
    }
    .form-control {
        width: 100%; height: 50px;
        border: 1.5px solid var(--ui-border); border-radius: var(--ui-radius-sm);
        padding: 0 1rem; font-size: 0.92rem; color: var(--ui-text-primary);
        background: #f8fafc; transition: border-color 0.15s ease, background 0.15s ease;
        margin-bottom: 1.3rem;
    }
    .form-control:focus { outline: none; border-color: var(--brand-orange); background: #fff; }

    .btn-login {
        width: 100%; height: 52px;
        background: var(--brand-gradient); border: none; border-radius: var(--ui-radius-sm);
        color: #fff; font-weight: 600; font-size: 0.92rem;
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        text-decoration: none; cursor: pointer;
    }
    .btn-login:hover {
        color: #fff; transform: translateY(-1px);
        box-shadow: 0 10px 25px -8px rgba(255, 145, 66, 0.4);
    }

    .alert-danger {
        background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.18);
        color: #dc2626; padding: 0.85rem 1rem; border-radius: var(--ui-radius-sm);
        font-size: 0.85rem; margin-bottom: 1.2rem;
    }

    /* ================= KOLOM 3 — SUDAH ABSEN ================= */
    .col-attendees {
        padding: 2.75rem 2rem;
        display: flex;
        flex-direction: column;
        background: #fafbfc;
    }
    .attendees-header {
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
        margin-bottom: 1.3rem;
    }
    .attendees-header h3 {
        font-size: 1.02rem; font-weight: 700; color: var(--ui-text-primary); margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .attendees-header h3 i { color: var(--brand-green-dark); font-size: 1rem; }
    .attendees-count-pill {
        background: rgba(16,185,129,0.12); color: var(--brand-green-dark);
        font-size: 0.74rem; font-weight: 700; padding: 3px 11px; border-radius: 999px; flex-shrink: 0;
    }

    .attendees-scroll { flex: 1; overflow-y: auto; max-height: 420px; padding-right: 4px; }
    .attendee-card {
        display: flex; align-items: center; gap: 10px;
        padding: 0.7rem 0.85rem; border-radius: 12px;
        background: #fff; border: 1px solid var(--ui-border);
        margin-bottom: 0.55rem;
    }
    .attendee-card .attendee-avatar {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
        background: rgba(34,197,94,0.12); color: var(--brand-green-dark);
        display: flex; align-items: center; justify-content: center; font-size: 0.95rem;
    }
    .attendee-card .attendee-text { min-width: 0; flex: 1; }
    .attendee-card .attendee-name {
        font-weight: 600; font-size: 0.86rem; color: var(--ui-text-primary);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .attendee-card .attendee-institution {
        font-size: 0.76rem; color: var(--ui-text-muted);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .attendee-card .attendee-time {
        font-size: 0.74rem; font-weight: 600; color: var(--ui-text-muted); flex-shrink: 0;
    }

    .attendees-empty {
        flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
        text-align: center; padding: 2rem 1rem; color: var(--ui-text-muted);
    }
    .attendees-empty i { font-size: 2rem; color: #cbd5e1; margin-bottom: 0.75rem; }
    .attendees-empty p { font-size: 0.85rem; margin: 0; }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 991.98px) {
        body.auth-page { padding: 0; background: #fff; }
        .attend-wrapper { max-width: 100%; }
        .attend-card {
            grid-template-columns: 1fr;
            border-radius: 0;
            box-shadow: none;
        }

        /* Info rapat dipadatkan jadi header ringkas di mobile — info
           lengkap (ruangan/tanggal/waktu/keperluan) tetap ada tapi
           dalam bentuk chip inline, bukan panel gradasi penuh setinggi
           layar seperti di desktop, supaya form (aksi utama) langsung
           terlihat tanpa banyak scroll. */
        .col-meeting-info { padding: 1.75rem 1.5rem 1.5rem 1.5rem; }
        .attend-logo { width: 48px; height: 48px; padding: 9px; margin-bottom: 1rem; }
        .col-meeting-info h2 { font-size: 1.25rem; margin-bottom: 1.1rem; }
        .meeting-info-list { flex-direction: row; flex-wrap: wrap; gap: 0.6rem; }
        .meeting-info-item {
            flex: 1 1 auto; min-width: 44%;
            background: rgba(255,255,255,0.12); border-radius: 12px; padding: 0.6rem 0.75rem;
        }
        .meeting-info-item .info-icon { width: 26px; height: 26px; font-size: 0.78rem; }
        .meeting-info-item .info-label { font-size: 0.62rem; }
        .meeting-info-item .info-value { font-size: 0.82rem; }

        .col-form {
            padding: 1.75rem 1.5rem; border-left: none; border-right: none;
            border-top: 1px solid var(--ui-border);
        }
        .form-title { font-size: 1.25rem; }
        .form-control { height: 50px; font-size: 16px; }

        .col-attendees {
            padding: 1.5rem 1.5rem 2rem 1.5rem;
            border-top: 1px solid var(--ui-border);
            background: #fafbfc;
        }
        .attendees-scroll { max-height: 280px; }
    }

    @media (max-width: 575.98px) {
        .meeting-info-item { min-width: 100%; }
    }
</style>

<div class="attend-wrapper">
    <div class="attend-card">

        {{-- ====================================================
             KOLOM 1 — INFORMASI RAPAT
             ==================================================== --}}
        <div class="col-meeting-info">
            <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" class="attend-logo">
            <h2>Presensi Meeting</h2>
            <div class="meeting-info-list">
                <div class="meeting-info-item">
                    <span class="info-icon"><i class="bi bi-door-open"></i></span>
                    <div class="info-text">
                        <div class="info-label">Ruangan</div>
                        <div class="info-value">{{ $booking->room->name }}</div>
                    </div>
                </div>
                <div class="meeting-info-item">
                    <span class="info-icon"><i class="bi bi-calendar3"></i></span>
                    <div class="info-text">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                    </div>
                </div>
                <div class="meeting-info-item">
                    <span class="info-icon"><i class="bi bi-clock"></i></span>
                    <div class="info-text">
                        <div class="info-label">Waktu</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB</div>
                    </div>
                </div>
                <div class="meeting-info-item">
                    <span class="info-icon"><i class="bi bi-bookmark"></i></span>
                    <div class="info-text">
                        <div class="info-label">Keperluan</div>
                        <div class="info-value">{{ $booking->purpose }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================
             KOLOM 2 — FORM ABSENSI
             ==================================================== --}}
        <div class="col-form">
            <h1 class="form-title">Form Presensi</h1>
            <p class="form-subtitle">Silakan isi data Anda untuk presensi</p>

            @if($errors->any())
                <div class="alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div class="alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('attendance.submit', $booking->absent_code) }}">
                @csrf

                <label for="name" class="form-label">
                    <i class="bi bi-person me-2"></i>Nama Lengkap
                </label>
                <input id="name" type="text" class="form-control"
                       name="name" value="{{ old('name') }}" required
                       placeholder="Masukkan nama lengkap Anda">

                <label for="institution" class="form-label">
                    <i class="bi bi-building me-2"></i>Bidang/Instansi/Perusahaan <span class="text-muted" style="font-weight:400;">(opsional)</span>
                </label>
                <input id="institution" type="text" class="form-control"
                       name="institution" value="{{ old('institution') }}"
                       placeholder="Bidang/instansi/perusahaan Anda">

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-check-circle me-2"></i>
                    Konfirmasi Presensi
                </button>
            </form>
        </div>

        {{-- ====================================================
             KOLOM 3 — SUDAH ABSEN
             ==================================================== --}}
        <div class="col-attendees">
            <div class="attendees-header">
                <h3><i class="bi bi-people-fill"></i> Sudah Absen</h3>
                <span class="attendees-count-pill">{{ $attendees->count() }} orang</span>
            </div>

            @if($attendees->isEmpty())
                <div class="attendees-empty">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada yang absen.<br>Jadilah yang pertama!</p>
                </div>
            @else
                <div class="attendees-scroll">
                    @foreach($attendees as $attendee)
                        <div class="attendee-card">
                            <span class="attendee-avatar"><i class="bi bi-check-lg"></i></span>
                            <div class="attendee-text">
                                <div class="attendee-name">{{ $attendee->guest_name }}</div>
                                @if($attendee->guest_institution)
                                    <div class="attendee-institution">{{ $attendee->guest_institution }}</div>
                                @endif
                            </div>
                            <span class="attendee-time">{{ \Carbon\Carbon::parse($attendee->check_in_time)->format('H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection