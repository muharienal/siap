@extends('templates.template')

@section('page_title', 'Tambah Fasilitas')
@section('page_subtitle', 'Buat fasilitas baru untuk ruangan')

@section('content')
<style>
    .form-content {
        padding: var(--space-5) var(--space-6);
        max-width: 800px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .form-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .form-card .card-header {
        padding: var(--space-4) var(--space-5);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
    }
    .form-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
        margin: 0;
    }
    .form-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
    .form-card .card-body {
        padding: var(--space-5);
    }
    .form-group {
        margin-bottom: var(--space-4);
    }
    .form-group .form-label {
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: block;
        margin-bottom: var(--space-1);
    }
    .form-group .form-label .text-danger {
        color: #ef4444;
    }
    .form-control, .form-select {
        height: 42px;
        padding: 0 var(--space-3);
        font-size: var(--font-size-sm);
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        width: 100%;
        transition: border var(--transition-fast), box-shadow var(--transition-fast);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--brand-orange);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.06);
        outline: none;
    }
    textarea.form-control {
        height: auto;
        min-height: 80px;
        resize: vertical;
        padding: var(--space-2) var(--space-3);
    }
    .btn-submit {
        height: 42px;
        padding: 0 var(--space-5);
        background: var(--brand-gradient);
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-inverse);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        box-shadow: 0 2px 8px rgba(249,115,22,0.15);
        text-decoration: none;
    }
    .btn-submit:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16,185,129,0.20);
        color: var(--text-inverse);
    }
    .btn-cancel {
        height: 42px;
        padding: 0 var(--space-5);
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        text-decoration: none;
    }
    .btn-cancel:hover {
        border-color: var(--brand-orange);
        color: var(--brand-orange-dark);
        background: rgba(249,115,22,0.04);
    }
    .alert-danger {
        background: rgba(239,68,68,0.06);
        border: 1px solid rgba(239,68,68,0.10);
        color: #dc2626;
        padding: var(--space-3) var(--space-4);
        border-radius: var(--radius-sm);
        margin-bottom: var(--space-4);
    }
    .alert-danger ul {
        margin: 0;
        padding-left: var(--space-4);
    }
    @media (max-width:991.98px) {
        .form-content { padding: var(--space-3); }
        .form-card .card-body { padding: var(--space-4); }
    }
    @media (max-width:575.98px) {
        .form-content { padding: var(--space-2); }
        .form-card .card-body { padding: var(--space-3); }
    }
</style>

<div class="form-content">

    <div class="greeting-section" style="margin-bottom: var(--space-5);">
        <h1 class="greeting-title">Tambah Fasilitas</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-tools me-1"></i> Tambah fasilitas baru untuk ruangan rapat</span>
        </div>
    </div>

    <div class="form-card">
        <div class="card-header">
            <h5 class="title"><i class="bi bi-plus-circle"></i> Form Fasilitas</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('settings.facilities.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Proyektor, Whiteboard, dll." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan fasilitas ini...">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                    <input type="text" name="storage_location" class="form-control" value="{{ old('storage_location') }}" placeholder="Contoh: Gudang Lantai 1, Rak A" required>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('settings.facilities.master') }}" class="btn-cancel">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection