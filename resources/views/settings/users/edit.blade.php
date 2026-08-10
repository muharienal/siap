@extends('templates.template')

@section('title', 'Edit Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')
<style>
    .db-toolbar {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: var(--space-4); margin-bottom: var(--space-5);
    }
    .btn-back {
        height: 44px; padding: 0 var(--space-5); border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm); background: var(--bg-card);
        border: 1px solid var(--border-color); color: var(--text-secondary);
        display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-back:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    .section-card {
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .card-columns { display: grid; grid-template-columns: 1fr 1fr; }
    .card-col-left { border-right: 1px solid var(--border-color-light); }
    .card-col-left .form-section-block { border-bottom: 1px solid var(--border-color-light); }
    .card-col-left .form-section-block:last-child { border-bottom: none; }
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

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); }
    .form-group { margin-bottom: var(--space-4); }
    .form-group:last-child { margin-bottom: 0; }
    .form-label-modern { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .modern-input, .modern-select {
        width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); background: var(--bg-input); font-size: 0.9rem; color: var(--text-primary);
        transition: all 0.2s; outline: none;
    }
    .modern-input:focus, .modern-select:focus { border-color: var(--brand-orange); background: var(--bg-card); box-shadow: 0 0 0 3px rgba(249,115,22,0.12); }
    .form-hint { font-size: var(--font-size-xs); color: var(--text-muted); margin-top: 4px; display: block; }

    .form-actions { display: flex; justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-5); }
    .btn-cancel {
        height: 44px; padding: 0 var(--space-5); border-radius: var(--radius-sm); border: 1px solid var(--border-color);
        background: var(--bg-card); color: var(--text-secondary); font-weight: 600; font-size: var(--font-size-sm);
        cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-cancel:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }
    .btn-submit {
        height: 44px; padding: 0 var(--space-6); border-radius: var(--radius-sm); border: none; background: var(--brand-gradient);
        color: white; font-weight: 600; font-size: var(--font-size-sm); cursor: pointer; transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(249,115,22,0.2); display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-submit:hover { box-shadow: 0 6px 16px rgba(16,185,129,0.25); transform: translateY(-1px); }

    .alert-danger {
        background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.15); color: #dc2626;
        padding: var(--space-3) var(--space-4); border-radius: var(--radius-sm); margin-bottom: var(--space-4);
    }
    .alert-danger ul { margin: 0; padding-left: var(--space-4); }

    @media (max-width: 767.98px) {
        .form-grid { grid-template-columns: 1fr; }
        .card-columns { grid-template-columns: 1fr; }
        .card-col-left { border-right: none; border-bottom: 1px solid var(--border-color-light); }
    }
</style>

<div class="db-toolbar">
    <a href="{{ route('settings.users.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if($errors->any())
    <div class="alert-danger">
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('settings.users.update', $user->id) }}" method="POST">
    @csrf @method('PUT')

    {{-- SATU CARD BESAR, 2 KOLOM --}}
    <div class="section-card">
        <div class="card-columns">
            <div class="card-col-left">
                <div class="form-section-block">
                    <div class="section-title"><i class="bi bi-person-vcard"></i> Data Diri</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label-modern">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nip" class="modern-input" value="{{ old('nip', $user->nip) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label-modern">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="modern-input" value="{{ old('full_name', $user->full_name) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-section-block">
                    <div class="section-title"><i class="bi bi-diagram-3"></i> Divisi &amp; Bidang</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label-modern">Divisi <span class="text-danger">*</span></label>
                            <select name="division_id" id="divisionSelect" class="modern-select" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Belum ada divisi yang dibutuhkan? Kelola lewat tombol "Kelola Divisi" di halaman daftar pengguna.</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label-modern">Bidang <span class="text-danger">*</span></label>
                            <select name="position_id" id="positionSelect" class="modern-select" required>
                                <option value="">Pilih Bidang</option>
                            </select>
                            <span class="form-hint">Daftar Bidang menyesuaikan Divisi yang dipilih.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-col-right">
                <div class="form-section-block">
                    <div class="section-title"><i class="bi bi-shield-lock"></i> Akun &amp; Akses</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label-modern">Role <span class="text-danger">*</span></label>
                            <select name="role" class="modern-select" required>
                                <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>Karyawan</option>
                                <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>Sesdir</option>
                                <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>Admin</option>
                            </select>
                            <span class="form-hint">Sesdir &amp; Admin otomatis mendapat prioritas persetujuan saat membuat peminjaman ruangan.</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label-modern">Password <span class="text-muted">(kosongkan jika tidak diubah)</span></label>
                            <input type="password" name="password" class="modern-input" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="form-group">
                            <label class="form-label-modern">Status Akun</label>
                            <select name="is_active" class="modern-select">
                                <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('settings.users.index') }}" class="btn-cancel"><i class="bi bi-x-lg"></i> Batal</a>
        <button type="submit" class="btn-submit"><i class="bi bi-save"></i> Update Pengguna</button>
    </div>
</form>

<script>
    // Bidang mengikuti Divisi yang dipilih (hierarki Divisi -> Bidang).
    var allPositions = @json($positions);
    var oldPositionId = {{ old('position_id', $user->position_id) ? (int) old('position_id', $user->position_id) : 'null' }};

    function filterPositionsByDivision(divisionId, preselectId) {
        var posSelect = document.getElementById('positionSelect');
        var filtered = allPositions.filter(function(p) {
            return String(p.division_id) === String(divisionId);
        });

        if (!divisionId) {
            posSelect.innerHTML = '<option value="">Pilih Divisi terlebih dahulu</option>';
            return;
        }

        if (filtered.length === 0) {
            posSelect.innerHTML = '<option value="">Belum ada Bidang di Divisi ini</option>';
            return;
        }

        var html = '<option value="">Pilih Bidang</option>';
        filtered.forEach(function(p) {
            var selected = preselectId && parseInt(preselectId) === p.id ? 'selected' : '';
            html += `<option value="${p.id}" ${selected}>${p.name}</option>`;
        });
        posSelect.innerHTML = html;
    }

    document.getElementById('divisionSelect').addEventListener('change', function() {
        filterPositionsByDivision(this.value, null);
    });

    // Inisialisasi dengan Divisi & Bidang pengguna yang sedang diedit
    filterPositionsByDivision(document.getElementById('divisionSelect').value, oldPositionId);
</script>
@endsection