@extends('templates.template')

@section('page_title', 'Edit Bidang')
@section('page_subtitle', 'Ubah data bidang / jabatan')

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
        <h1 class="greeting-title">Edit Bidang</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-briefcase me-1"></i> Ubah data bidang / jabatan</span>
        </div>
    </div>

    <div class="form-card">
        <div class="card-header">
            <h5 class="card-title" style="font-weight:700; margin:0;"><i class="bi bi-pencil-square me-2"></i>Form Bidang</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert-danger">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('settings.positions.update', $position->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Bidang <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $position->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $position->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('settings.positions.index') }}" class="btn-cancel"><i class="bi bi-x-lg"></i> Batal</a>
                    <button type="submit" class="btn-submit"><i class="bi bi-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection