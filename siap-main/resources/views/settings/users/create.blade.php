@extends('templates.template')

@section('page_title', 'Tambah Pengguna')
@section('page_subtitle', 'Buat karyawan dan akun pengguna baru')

@section('content')
<style>
    .form-content { padding: var(--space-5) var(--space-6); max-width:800px; margin:0 auto; }
    .form-card { background:var(--bg-card); border-radius:var(--radius-card); border:none; box-shadow:var(--shadow-card); overflow:hidden; }
    .form-card .card-header { padding:var(--space-4) var(--space-5); border-bottom:1px solid var(--border-color-light); }
    .form-card .card-body { padding:var(--space-5); }
    .form-group { margin-bottom:var(--space-4); }
    .form-group .form-label { font-weight:600; font-size:var(--font-size-sm); color:var(--text-secondary); display:block; margin-bottom:var(--space-1); }
    .form-control, .form-select { height:42px; padding:0 var(--space-3); font-size:var(--font-size-sm); background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--radius-sm); width:100%; }
    .form-control:focus, .form-select:focus { border-color:var(--brand-orange); box-shadow:0 0 0 3px rgba(249,115,22,0.06); outline:none; }
    .btn-submit { height:42px; padding:0 var(--space-5); background:var(--brand-gradient); border:none; border-radius:var(--radius-sm); font-weight:600; font-size:var(--font-size-sm); color:#fff; transition:all var(--transition-fast); cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
    .btn-submit:hover { background:var(--brand-gradient-hover); transform:translateY(-1px); }
    .btn-cancel { height:42px; padding:0 var(--space-5); background:transparent; border:1px solid var(--border-color); border-radius:var(--radius-sm); font-weight:600; font-size:var(--font-size-sm); color:var(--text-secondary); transition:all var(--transition-fast); cursor:pointer; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
    .btn-cancel:hover { border-color:var(--brand-orange); color:var(--brand-orange-dark); }
    .alert-danger { background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.10); color:#dc2626; padding:var(--space-3) var(--space-4); border-radius:var(--radius-sm); margin-bottom:var(--space-4); }
    .alert-danger ul { margin:0; padding-left:var(--space-4); }
    @media (max-width:991.98px) { .form-content { padding:var(--space-3); } .form-card .card-body { padding:var(--space-4); } }
    @media (max-width:575.98px) { .form-content { padding:var(--space-2); } .form-card .card-body { padding:var(--space-3); } }
</style>

<div class="form-content">

    <div class="greeting-section" style="margin-bottom: var(--space-5);">
        <h1 class="greeting-title">Tambah Pengguna</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-person-plus me-1"></i> Isi data karyawan dan akun pengguna</span>
        </div>
    </div>

    <div class="form-card">
        <div class="card-header">
            <h5 class="card-title" style="font-weight:700; margin:0;"><i class="bi bi-plus-circle me-2"></i>Form Pengguna</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert-danger">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('settings.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Divisi <span class="text-danger">*</span></label>
                            <select name="division_id" class="form-select" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Bidang <span class="text-danger">*</span></label>
                            <select name="position_id" class="form-select" required>
                                <option value="">Pilih Bidang</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@domain.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="2" {{ old('role') == 2 ? 'selected' : '' }}>Pengguna</option>
                                <option value="1" {{ old('role') == 1 ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Status Akun</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('settings.users.index') }}" class="btn-cancel"><i class="bi bi-x-lg"></i> Batal</a>
                    <button type="submit" class="btn-submit"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection