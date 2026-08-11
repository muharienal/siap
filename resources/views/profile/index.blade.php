@extends('templates.template')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')

@section('content')
<style>
    .section-card {
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        overflow: hidden; margin-bottom: var(--space-5);
    }
    .section-card:last-child { margin-bottom: 0; }
    .form-section-block { padding: 1.5rem; }
    .section-title {
        font-size: 1.05rem; font-weight: 700; color: var(--text-primary);
        margin: 0 0 1.25rem 0; padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color-light);
        display: flex; align-items: center; gap: 8px;
    }
    .section-title i {
        width: 28px; height: 28px; border-radius: 8px; background: rgba(249, 115, 22, 0.1);
        color: var(--brand-orange); display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem;
    }

    /* Profile hero */
    .profile-hero {
        display: flex; align-items: center; gap: var(--space-4);
        padding: 1.5rem; flex-wrap: wrap;
    }
    .profile-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        background: var(--brand-gradient); color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; font-weight: 800; flex-shrink: 0;
        box-shadow: 0 6px 16px rgba(249,115,22,0.25);
    }
    .profile-name { font-size: var(--font-size-xl); font-weight: 700; color: var(--text-primary); margin: 0; }
    .profile-sub { color: var(--text-muted); font-size: var(--font-size-sm); margin-top: 4px; }
    .role-pill {
        font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: var(--radius-pill);
        display: inline-block; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 8px;
    }
    .role-pill.admin { background: rgba(249,115,22,0.12); color: var(--brand-orange-dark); }
    .role-pill.sesdir { background: rgba(139,92,246,0.12); color: #7c3aed; }
    .role-pill.karyawan { background: rgba(59,130,246,0.12); color: #1d4ed8; }

    .info-grid { display: flex; flex-direction: column; gap: 10px; }
    .info-item { display: flex; align-items: center; gap: 14px; padding: 12px 14px; background: var(--bg-input); border-radius: var(--radius-sm); }
    .info-item-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0; }
    .info-item-icon.icon-blue { background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); }
    .info-item-icon.icon-orange { background: rgba(249, 115, 22, 0.1); color: var(--brand-orange); }
    .info-item-icon.icon-green { background: rgba(16, 185, 129, 0.1); color: var(--brand-green); }
    .info-item-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .info-item-label { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.4px; }
    .info-item-value { font-weight: 600; color: var(--text-primary); font-size: 0.9rem; }

    .form-hint {
        font-size: var(--font-size-xs); color: var(--text-muted); margin-top: 6px;
        display: flex; align-items: center; gap: 6px;
    }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); }
    .form-group { margin-bottom: var(--space-4); }
    .form-group:last-child { margin-bottom: 0; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-label-modern { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .modern-input {
        width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); background: var(--bg-input); font-size: 0.9rem; color: var(--text-primary);
        transition: all 0.2s; outline: none;
    }
    .modern-input:focus { border-color: var(--brand-orange); background: var(--bg-card); box-shadow: 0 0 0 3px rgba(249,115,22,0.12); }

    .btn-submit {
        height: 44px; padding: 0 var(--space-6); border-radius: var(--radius-sm); border: none; background: var(--brand-gradient);
        color: white; font-weight: 600; font-size: var(--font-size-sm); cursor: pointer; transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(249,115,22,0.2); display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-submit:hover { box-shadow: 0 6px 16px rgba(16,185,129,0.25); transform: translateY(-1px); }

    .alert-box {
        padding: var(--space-3) var(--space-4); border-radius: var(--radius-sm); margin-bottom: var(--space-4);
        font-size: var(--font-size-sm); display: flex; align-items: center; gap: 8px;
    }
    .alert-box.success { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #047857; }
    .alert-box.error { background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.15); color: #dc2626; }
    .alert-box ul { margin: 0; padding-left: var(--space-4); }

    @media (max-width: 767.98px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

@if(session('success'))
    <div class="alert-box success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-box error"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert-box error">
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

@php
    $roleMap = [
        1 => ['label' => 'Admin', 'class' => 'admin'],
        2 => ['label' => 'Karyawan', 'class' => 'karyawan'],
        3 => ['label' => 'Sesdir', 'class' => 'sesdir'],
    ];
    $roleInfo = $roleMap[$user->role] ?? ['label' => 'Karyawan', 'class' => 'karyawan'];
    $initial = strtoupper(mb_substr($user->full_name ?? 'U', 0, 1));
@endphp

<div class="section-card">
    <div class="profile-hero">
        <div class="profile-avatar">{{ $initial }}</div>
        <div>
            <h1 class="profile-name">{{ $user->full_name }}</h1>
            <div class="profile-sub">NIK: {{ $user->nip }}</div>
            <span class="role-pill {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span>
        </div>
    </div>

    <div class="form-section-block" style="padding-top: 0;">
        <div class="section-title"><i class="bi bi-person-vcard"></i> Informasi Kepegawaian</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item-icon icon-blue"><i class="bi bi-diagram-3-fill"></i></div>
                <div class="info-item-text">
                    <span class="info-item-label">Divisi</span>
                    <span class="info-item-value">{{ $user->division->name ?? '-' }}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-item-icon icon-orange"><i class="bi bi-briefcase-fill"></i></div>
                <div class="info-item-text">
                    <span class="info-item-label">Bidang</span>
                    <span class="info-item-value">{{ $user->position->name ?? '-' }}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-item-icon icon-green"><i class="bi bi-check-circle-fill"></i></div>
                <div class="info-item-text">
                    <span class="info-item-label">Status Akun</span>
                    <span class="info-item-value">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
        </div>
        <div class="form-hint">
            <i class="bi bi-info-circle"></i> Data di atas hanya bisa diubah oleh Admin lewat menu Manajemen Pengguna.
        </div>
    </div>
</div>

<div class="section-card">
    <div class="form-section-block">
        <div class="section-title"><i class="bi bi-shield-lock"></i> Ubah Password</div>
        <form action="{{ route('profile.update.password') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label-modern">Password Lama <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" class="modern-input" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label class="form-label-modern">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="modern-input" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter">
                </div>
                <div class="form-group">
                    <label class="form-label-modern">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="modern-input" required minlength="8" autocomplete="new-password">
                </div>
            </div>
            <button type="submit" class="btn-submit"><i class="bi bi-key-fill"></i> Simpan Password Baru</button>
        </form>
    </div>
</div>
@endsection