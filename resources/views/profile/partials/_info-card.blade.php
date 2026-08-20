@php
    $roleMap = [
        1 => ['label' => 'Admin', 'class' => 'admin'],
        2 => ['label' => 'Karyawan', 'class' => 'karyawan'],
        3 => ['label' => 'Sesdir', 'class' => 'sesdir'],
    ];
    $roleInfo = $roleMap[$user->role] ?? ['label' => 'Karyawan', 'class' => 'karyawan'];
    $initial = strtoupper(mb_substr($user->full_name ?? 'U', 0, 1));
@endphp
<div class="section-card" id="profileInfoCard">
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
