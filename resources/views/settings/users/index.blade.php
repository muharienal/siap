@extends('templates.template')

@section('page_title', 'Manajemen Pengguna')
@section('page_subtitle', 'Kelola data karyawan dan akun pengguna')

@section('content')
<style>
    .settings-content {
        padding: var(--space-5) var(--space-6);
        max-width: 1680px;
        margin: 0 auto;
        flex: 1;
        width: 100%;
    }
    .table-card {
        background: var(--bg-card);
        border-radius: var(--radius-card);
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .table-card .card-header {
        padding: var(--space-3) var(--space-5);
        background: transparent;
        border-bottom: 1px solid var(--border-color-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-2);
        min-height: 48px;
    }
    .table-card .card-header .title {
        font-weight: 700;
        font-size: var(--font-size-md);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .table-card .card-header .title i {
        color: var(--brand-orange);
        font-size: 1.2rem;
    }
    .table-card .card-body {
        padding: 0;
        overflow-x: auto;
    }
    .table-booking {
        width: 100%;
        border-collapse: collapse;
        font-size: var(--font-size-sm);
        min-width: 700px;
    }
    .table-booking thead th {
        background: var(--bg-card);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: var(--font-size-xs);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: var(--space-2) var(--space-3);
        border-bottom: 1px solid var(--border-color-light);
        text-align: left;
        height: 38px;
    }
    .table-booking tbody td {
        padding: var(--space-2) var(--space-3);
        border-bottom: 1px solid var(--border-color-light);
        height: 44px;
        background: var(--bg-card);
        transition: background var(--transition-fast);
    }
    .table-booking tbody tr:nth-child(even) td { background: #fafbfc; }
    .table-booking tbody tr:hover td { background: rgba(249,115,22,0.03); }
    .btn-action-group { display:flex; gap:4px; flex-wrap:wrap; }
    .btn-action {
        padding: 4px 10px;
        border-radius: var(--radius-sm);
        font-size: var(--font-size-xs);
        border: none;
        transition: all var(--transition-fast);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        font-weight: 500;
    }
    .btn-action.edit {
        background: rgba(245,158,11,0.08);
        color: #d97706;
    }
    .btn-action.edit:hover {
        background: rgba(245,158,11,0.16);
    }
    .btn-action.delete {
        background: rgba(239,68,68,0.06);
        color: #dc2626;
    }
    .btn-action.delete:hover {
        background: rgba(239,68,68,0.12);
    }
    .btn-today {
        height: 38px;
        padding: 0 var(--space-4);
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
        gap: var(--space-1);
        white-space: nowrap;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(249,115,22,0.15);
    }
    .btn-today:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16,185,129,0.20);
        color: var(--text-inverse);
    }
    .badge-status {
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        font-size: var(--font-size-xs);
        font-weight: 600;
        display: inline-block;
    }
    .badge-status.active {
        background: rgba(16,185,129,0.12);
        color: var(--brand-green-dark);
    }
    .badge-status.inactive {
        background: rgba(239,68,68,0.08);
        color: #dc2626;
    }
    .badge-status.admin {
        background: rgba(249,115,22,0.12);
        color: var(--brand-orange-dark);
    }
    .badge-status.user {
        background: rgba(59,130,246,0.08);
        color: var(--brand-blue-dark);
    }
    .no-results {
        padding: var(--space-6);
        text-align: center;
        color: var(--text-muted);
    }
    .no-results i {
        font-size: 2rem;
        display: block;
        margin-bottom: var(--space-3);
        color: var(--border-color);
    }
    @media (max-width:991.98px) {
        .settings-content { padding: var(--space-3); }
    }
    @media (max-width:575.98px) {
        .settings-content { padding: var(--space-2); }
        .table-booking { font-size: var(--font-size-xs); min-width: 480px; }
    }
</style>

<div class="settings-content">

    <div class="greeting-section" style="margin-bottom: var(--space-5);">
        <h1 class="greeting-title">Manajemen Pengguna</h1>
        <div class="greeting-sub">
            <span><i class="bi bi-people-fill me-1"></i> Kelola data karyawan dan akun pengguna</span>
        </div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <div class="title">
                <i class="bi bi-table"></i> Daftar Pengguna
                <span style="font-weight:400; font-size:var(--font-size-sm); color:var(--text-muted); margin-left:var(--space-1);">
                    {{ $users->count() }} total
                </span>
            </div>
            <a href="{{ route('settings.users.create') }}" class="btn-today">
                <i class="bi bi-plus-lg"></i> Tambah Pengguna
            </a>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <table class="table-booking">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>NIK</th>
                            <th style="min-width:160px;">Nama Lengkap</th>
                            <th>Divisi</th>
                            <th>Bidang</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th style="min-width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nip }}</td>
                                <td><strong>{{ $item->full_name }}</strong></td>
                                <td>{{ $item->division->name ?? '-' }}</td>
                                <td>{{ $item->position->name ?? '-' }}</td>
                                <td>
                                    @if($item->role == 1)
                                        <span class="badge-status admin">Admin</span>
                                    @else
                                        <span class="badge-status user">Pengguna</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge-status active">Aktif</span>
                                    @else
                                        <span class="badge-status inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-action-group">
                                        <a href="{{ route('settings.users.edit', $item->id) }}" class="btn-action edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('settings.users.destroy', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-results">
                    <i class="bi bi-people"></i>
                    <p>Belum ada pengguna</p>
                    <a href="{{ route('settings.users.create') }}" style="color:var(--brand-orange); text-decoration:none; font-weight:600;">Tambah pengguna pertama</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection