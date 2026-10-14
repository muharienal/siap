@extends('templates.template')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')
<style>
    /* ============================================
       PAGE HEADER / TOOLBAR (identik dengan halaman
       Manajemen Ruang Rapat)
    ============================================ */
    .db-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-4);
        margin-bottom: var(--space-5);
    }
    .db-toolbar .left-actions { display: flex; gap: var(--space-3); flex-wrap: wrap; }
    .db-toolbar .actions {
        display: flex;
        gap: var(--space-3);
        flex-wrap: wrap;
        flex-shrink: 0;
    }
    .btn-primary-sm,
    .btn-outline-sm {
        height: 44px; padding: 0 var(--space-5);
        border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; gap: 8px;
        text-decoration: none; cursor: pointer; transition: all 0.2s;
        white-space: nowrap; border: none;
    }
    .btn-primary-sm {
        background: var(--brand-gradient); color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25); color: white; }
    .btn-outline-sm {
        background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);
    }
    .btn-outline-sm:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }

    /* ============================================
       SECTION CARD (tabel pengguna)
    ============================================ */
    .section-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .section-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color-light);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--space-2);
    }
    .section-card-header .title {
        font-size: 1.05rem; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 8px;
    }
    .section-card-header .title i {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(249, 115, 22, 0.1); color: var(--brand-orange);
        display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem;
    }
    .section-card-header .count { font-weight: 400; font-size: var(--font-size-sm); color: var(--text-muted); }

    .data-table { width: 100%; border-collapse: collapse; font-size: var(--font-size-sm); min-width: 760px; }
    .data-table thead th {
        background: var(--bg-body); color: var(--text-secondary); font-weight: 700;
        font-size: var(--font-size-xs); text-transform: uppercase; letter-spacing: 0.3px;
        padding: var(--space-3) var(--space-4); border-bottom: 1px solid var(--border-color-light); text-align: left;
    }
    .data-table tbody td {
        padding: var(--space-3) var(--space-4); border-bottom: 1px solid var(--border-color-light);
        color: var(--text-primary);
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: rgba(249,115,22,0.03); }

    .role-pill, .status-pill {
        font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: var(--radius-pill);
        display: inline-block; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .role-pill.admin { background: rgba(249,115,22,0.12); color: var(--brand-orange-dark); }
    .role-pill.sesdir { background: rgba(139,92,246,0.12); color: #7c3aed; }
    .role-pill.karyawan { background: rgba(59,130,246,0.12); color: #1d4ed8; }
    .status-pill.active { background: rgba(16,185,129,0.12); color: #047857; }
    .status-pill.inactive { background: rgba(239,68,68,0.10); color: #b91c1c; }

    .action-btn {
        width: 32px; height: 32px; border-radius: var(--radius-xs);
        border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-secondary);
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .action-btn.edit:hover { border-color: var(--brand-orange); color: var(--brand-orange); }
    .action-btn.delete:hover { border-color: #ef4444; color: #ef4444; }

    .empty-state { text-align: center; padding: var(--space-9) var(--space-5); }
    .empty-state i { font-size: 2.5rem; color: var(--border-color); display: block; margin-bottom: var(--space-3); }
    .empty-state p { color: var(--text-muted); margin-bottom: var(--space-3); }

    /* ============================================
       MODAL MODERN CLEAN (STANDAR — sama seperti
       modal Data Fasilitas di halaman Ruang Rapat)
    ============================================ */
    .modern-modal .modal-dialog { max-width: 700px; }
    .modern-modal .modal-content {
        border: 1px solid var(--border-color-light); border-radius: var(--radius-card);
        overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .modern-modal .modal-header {
        background: var(--bg-card); border-bottom: 1px solid var(--border-color-light);
        padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
    }
    .modern-modal .modal-title { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
    .modern-modal .modal-title i { color: var(--brand-orange); font-size: 1.5rem; }
    .modern-modal .btn-close { background: none; border: none; font-size: 1.5rem; color: var(--text-muted); opacity: 0.7; transition: opacity 0.2s; }
    .modern-modal .btn-close:hover { opacity: 1; }
    .modern-modal .modal-body { padding: 0; display: flex; min-height: 350px; }

    .modern-input, .modern-select {
        width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); background: var(--bg-input); font-size: 0.9rem; color: var(--text-primary);
        transition: all 0.2s; outline: none;
    }
    .modern-input:focus, .modern-select:focus { border-color: var(--brand-orange); background: var(--bg-card); box-shadow: 0 0 0 3px rgba(249,115,22,0.12); }
    .form-label-modern { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }

    .btn-modal-cancel {
        height: 42px; padding: 0 20px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);
        background: var(--bg-card); color: var(--text-secondary); font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-modal-cancel:hover { background: var(--bg-hover); }
    .btn-modal-submit {
        height: 42px; padding: 0 24px; border-radius: var(--radius-sm); border: none; background: var(--brand-gradient);
        color: white; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(249,115,22,0.2);
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-modal-submit:hover { box-shadow: 0 6px 16px rgba(16,185,129,0.25); transform: translateY(-1px); }
    .btn-modal-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .fac-list-panel {
        flex: 1.2; border-right: 1px solid var(--border-color-light); padding: 1.25rem;
        background: var(--bg-body); overflow-y: auto; max-height: 500px;
    }
    .fac-list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .fac-list-title { font-size: 0.875rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
    .fac-item {
        background: var(--bg-card); padding: 12px 14px; border-radius: var(--radius-sm); margin-bottom: 8px;
        display: flex; align-items: center; justify-content: space-between; border: 1px solid transparent;
        transition: all 0.2s; cursor: pointer;
    }
    .fac-item:hover { border-color: var(--border-color); transform: translateX(2px); }
    .fac-item.active { border-color: var(--brand-orange); background: rgba(249, 115, 22, 0.04); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .fac-item-name { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); display: flex; flex-direction: column; gap: 2px; }
    .fac-item-name .name-row { display: flex; align-items: center; gap: 8px; }
    .fac-item-name .name-row i { color: var(--brand-blue); font-size: 1rem; }
    .fac-item-parent { font-size: 11px; font-weight: 500; color: var(--text-muted); padding-left: 24px; }
    .fac-actions-group { display: flex; gap: 4px; flex-shrink: 0; }
    .fac-btn { width: 30px; height: 30px; border-radius: 6px; border: none; background: transparent; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .fac-btn:hover { background: rgba(0,0,0,0.05); color: var(--text-primary); }
    .fac-btn.delete:hover { background: rgba(239,68,68,0.1); color: #dc2626; }
    .fac-form-panel { flex: 1; padding: 1.5rem; background: var(--bg-card); display: flex; flex-direction: column; }
    .fac-form-title { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px; }
    .fac-form-actions { margin-top: auto; display: flex; gap: 10px; padding-top: 1.5rem; }

    /* TOAST NOTIFICATION */
    .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
    .toast-notification {
        min-width: 320px; max-width: 400px; background: var(--bg-card); border-radius: var(--radius-sm);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15); padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px;
        pointer-events: auto; animation: toastSlideIn 0.3s ease forwards; border-left: 4px solid;
    }
    .toast-notification.success { border-left-color: #10b981; }
    .toast-notification.error { border-left-color: #ef4444; }
    .toast-notification.info { border-left-color: var(--brand-blue); }
    .toast-icon { font-size: 1.2rem; margin-top: 2px; }
    .toast-notification.success .toast-icon { color: #10b981; }
    .toast-notification.error .toast-icon { color: #ef4444; }
    .toast-notification.info .toast-icon { color: var(--brand-blue); }
    .toast-content { flex: 1; }
    .toast-title { font-weight: 700; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 2px; }
    .toast-message { font-size: 0.82rem; color: var(--text-secondary); }
    .toast-close { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem; line-height: 1; }
    @keyframes toastSlideIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes toastSlideOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(30px); } }

    @media (max-width: 991.98px) {
        .db-toolbar { flex-wrap: wrap; }
        .db-toolbar .left-actions, .db-toolbar .actions { width: 100%; }
        .db-toolbar .left-actions .btn-outline-sm, .db-toolbar .actions .btn-primary-sm { width: 100%; justify-content: center; }
        .modern-modal .modal-body { flex-direction: column; }
        .fac-list-panel { border-right: none; border-bottom: 1px solid var(--border-color-light); max-height: 220px; }
    }
</style>

<div class="db-toolbar">
    <div class="left-actions">
        <button type="button" class="btn-outline-sm" data-bs-toggle="modal" data-bs-target="#manageDivisionModal">
            <i class="bi bi-diagram-2-fill"></i> Kelola Divisi
        </button>
        <button type="button" class="btn-outline-sm" data-bs-toggle="modal" data-bs-target="#managePositionModal">
            <i class="bi bi-briefcase-fill"></i> Kelola Bidang
        </button>
    </div>
    <div class="actions">
        <a href="{{ route('settings.users.create') }}" class="btn-primary-sm">
            <i class="bi bi-plus-lg"></i> Tambah Pengguna
        </a>
    </div>
</div>

@include('settings.users.partials._user-table', ['users' => $users])

{{-- ========================================== --}}
{{-- MODAL: KELOLA DIVISI (terpisah)           --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="manageDivisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-diagram-2-fill"></i> Kelola Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="fac-list-panel">
                    <div class="fac-list-header"><span class="fac-list-title">Daftar Divisi</span></div>
                    <div id="divisionListContainer"></div>
                </div>
                <div class="fac-form-panel">
                    <h5 class="fac-form-title" id="divisionFormTitle"><i class="bi bi-plus-circle"></i> Tambah Divisi Baru</h5>
                    <form id="divisionForm" class="d-flex flex-column h-100">
                        @csrf
                        <input type="hidden" id="division_id" name="id">
                        <div class="mb-3">
                            <label class="form-label-modern">Nama Divisi</label>
                            <input type="text" class="modern-input" id="division_name" name="name" placeholder="Contoh: Divisi IT" required>
                        </div>
                        <div class="fac-form-actions">
                            <button type="button" class="btn-modal-cancel" onclick="resetDivisionForm()">Batal</button>
                            <button type="submit" class="btn-modal-submit" id="divisionSubmitBtn">Simpan Divisi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL: KELOLA BIDANG (terpisah)           --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="managePositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-briefcase-fill"></i> Kelola Bidang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="fac-list-panel">
                    <div class="fac-list-header"><span class="fac-list-title">Daftar Bidang</span></div>
                    <div id="positionListContainer"></div>
                </div>
                <div class="fac-form-panel">
                    <h5 class="fac-form-title" id="positionFormTitle"><i class="bi bi-plus-circle"></i> Tambah Bidang Baru</h5>
                    <form id="positionForm" class="d-flex flex-column h-100">
                        @csrf
                        <input type="hidden" id="position_id" name="id">
                        <div class="mb-3">
                            <label class="form-label-modern">Divisi</label>
                            <select class="modern-select" id="position_division_id" name="division_id" required>
                                <option value="">Pilih Divisi</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-modern">Nama Bidang</label>
                            <input type="text" class="modern-input" id="position_name" name="name" placeholder="Contoh: Staff" required>
                        </div>
                        <div class="fac-form-actions">
                            <button type="button" class="btn-modal-cancel" onclick="resetPositionForm()">Batal</button>
                            <button type="submit" class="btn-modal-submit" id="positionSubmitBtn">Simpan Bidang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
    // ============================================
    // TOAST NOTIFICATION SYSTEM
    // ============================================
    function showToast(type, title, message, duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        const iconMap = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill' };
        toast.innerHTML = `
            <i class="bi ${iconMap[type]} toast-icon"></i>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button type="button" class="toast-close" onclick="dismissToast(this.parentElement)">&times;</button>
        `;
        container.appendChild(toast);
        if (duration > 0) setTimeout(() => dismissToast(toast), duration);
    }
    function dismissToast(toast) {
        if (!toast) return;
        toast.style.animation = 'toastSlideOut 0.3s ease forwards';
        setTimeout(() => { if (toast.parentElement) toast.parentElement.removeChild(toast); }, 300);
    }

    // ============================================
    // DIVISI (level tertinggi) — CRUD AJAX
    // ============================================
    var divisionsData = @json($divisions);
    var divisionRouteStore = "{{ route('settings.divisions.store') }}";
    var divisionRouteUpdate = "{{ route('settings.divisions.update', ':id') }}".replace(':id', '');
    var divisionRouteDelete = "{{ route('settings.divisions.destroy', ':id') }}".replace(':id', '');

    function renderDivisionList() {
        var container = document.getElementById('divisionListContainer');
        if (divisionsData.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-inbox d-block fs-4 mb-2"></i> Belum ada data</div>';
            return;
        }
        var html = '';
        divisionsData.forEach(function(d) {
            html += `
                <div class="fac-item" id="division-item-${d.id}">
                    <div class="fac-item-name"><div class="name-row"><i class="bi bi-diagram-2"></i> ${d.name}</div></div>
                    <div class="fac-actions-group">
                        <button class="fac-btn" onclick="editDivision(${d.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="fac-btn delete" onclick="deleteDivision(${d.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function resetDivisionForm() {
        document.getElementById('divisionForm').reset();
        document.getElementById('division_id').value = '';
        document.getElementById('divisionFormTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Tambah Divisi Baru';
        document.getElementById('divisionSubmitBtn').textContent = 'Simpan Divisi';
        document.querySelectorAll('#divisionListContainer .fac-item').forEach(el => el.classList.remove('active'));
    }

    function editDivision(id) {
        var d = divisionsData.find(x => x.id === id);
        if (!d) return;
        document.getElementById('division_id').value = d.id;
        document.getElementById('division_name').value = d.name;
        document.getElementById('divisionFormTitle').innerHTML = '<i class="bi bi-pencil-circle"></i> Edit Divisi';
        document.getElementById('divisionSubmitBtn').textContent = 'Update Divisi';
        document.querySelectorAll('#divisionListContainer .fac-item').forEach(el => el.classList.remove('active'));
        document.getElementById('division-item-' + id).classList.add('active');
    }

    function deleteDivision(id) {
        if (!confirm('Yakin ingin menghapus divisi ini? Divisi yang masih punya Bidang di dalamnya tidak dapat dihapus.')) return;
        fetch(divisionRouteDelete + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-HTTP-Method-Override': 'DELETE', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                divisionsData = divisionsData.filter(x => x.id !== id);
                renderDivisionList();
                resetDivisionForm();
                showToast('success', 'Berhasil!', 'Divisi berhasil dihapus.', 3000);
            } else {
                showToast('error', 'Gagal', data.message || 'Gagal menghapus divisi.', 6000);
            }
        })
        .catch(() => showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000));
    }

    document.getElementById('divisionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('division_id').value;
        var url = id ? divisionRouteUpdate + id : divisionRouteStore;
        var method = id ? 'PUT' : 'POST';
        var formData = new FormData(this);
        var data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch(url, {
            method: method,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => { if (!r.ok) return r.json().then(err => { throw err; }); return r.json(); })
        .then(res => {
            if (res.success) {
                if (id) {
                    var index = divisionsData.findIndex(x => x.id === parseInt(id));
                    if (index !== -1) divisionsData[index] = res.data;
                    showToast('success', 'Berhasil!', 'Divisi berhasil diperbarui.', 3000);
                } else {
                    divisionsData.push(res.data);
                    showToast('success', 'Berhasil!', 'Divisi berhasil ditambahkan.', 3000);
                }
                renderDivisionList();
                resetDivisionForm();
                refreshPositionDivisionOptions();
            } else {
                showToast('error', 'Gagal', res.message || 'Gagal menyimpan divisi.', 5000);
            }
        })
        .catch(err => {
            if (err && err.errors) {
                showToast('error', 'Validasi Gagal', Object.values(err.errors).flat().join('<br>'), 6000);
            } else {
                showToast('error', 'Error', 'Terjadi kesalahan.', 5000);
            }
        });
    });

    // ============================================
    // BIDANG (di dalam Divisi) — CRUD AJAX
    // ============================================
    var positionsData = @json($positions->load('division'));
    var positionRouteStore = "{{ route('settings.positions.store') }}";
    var positionRouteUpdate = "{{ route('settings.positions.update', ':id') }}".replace(':id', '');
    var positionRouteDelete = "{{ route('settings.positions.destroy', ':id') }}".replace(':id', '');

    function renderPositionList() {
        var container = document.getElementById('positionListContainer');
        if (positionsData.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-inbox d-block fs-4 mb-2"></i> Belum ada data</div>';
            return;
        }
        var html = '';
        positionsData.forEach(function(p) {
            var divisionName = p.division ? p.division.name : 'Belum ada Divisi';
            html += `
                <div class="fac-item" id="position-item-${p.id}">
                    <div class="fac-item-name">
                        <div class="name-row"><i class="bi bi-briefcase"></i> ${p.name}</div>
                        <div class="fac-item-parent"><i class="bi bi-arrow-return-right"></i> ${divisionName}</div>
                    </div>
                    <div class="fac-actions-group">
                        <button class="fac-btn" onclick="editPosition(${p.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="fac-btn delete" onclick="deletePosition(${p.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function resetPositionForm() {
        document.getElementById('positionForm').reset();
        document.getElementById('position_id').value = '';
        document.getElementById('positionFormTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Tambah Bidang Baru';
        document.getElementById('positionSubmitBtn').textContent = 'Simpan Bidang';
        document.querySelectorAll('#positionListContainer .fac-item').forEach(el => el.classList.remove('active'));
    }

    function editPosition(id) {
        var p = positionsData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('position_id').value = p.id;
        document.getElementById('position_name').value = p.name;
        document.getElementById('position_division_id').value = p.division_id || '';
        document.getElementById('positionFormTitle').innerHTML = '<i class="bi bi-pencil-circle"></i> Edit Bidang';
        document.getElementById('positionSubmitBtn').textContent = 'Update Bidang';
        document.querySelectorAll('#positionListContainer .fac-item').forEach(el => el.classList.remove('active'));
        document.getElementById('position-item-' + id).classList.add('active');
    }

    function deletePosition(id) {
        if (!confirm('Yakin ingin menghapus bidang ini?')) return;
        fetch(positionRouteDelete + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-HTTP-Method-Override': 'DELETE', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                positionsData = positionsData.filter(x => x.id !== id);
                renderPositionList();
                resetPositionForm();
                showToast('success', 'Berhasil!', 'Bidang berhasil dihapus.', 3000);
            } else {
                showToast('error', 'Gagal', data.message || 'Gagal menghapus bidang.', 6000);
            }
        })
        .catch(() => showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000));
    }

    document.getElementById('positionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('position_id').value;
        var url = id ? positionRouteUpdate + id : positionRouteStore;
        var method = id ? 'PUT' : 'POST';
        var formData = new FormData(this);
        var data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch(url, {
            method: method,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => { if (!r.ok) return r.json().then(err => { throw err; }); return r.json(); })
        .then(res => {
            if (res.success) {
                if (id) {
                    var index = positionsData.findIndex(x => x.id === parseInt(id));
                    if (index !== -1) positionsData[index] = res.data;
                    showToast('success', 'Berhasil!', 'Bidang berhasil diperbarui.', 3000);
                } else {
                    positionsData.push(res.data);
                    showToast('success', 'Berhasil!', 'Bidang berhasil ditambahkan.', 3000);
                }
                renderPositionList();
                resetPositionForm();
            } else {
                showToast('error', 'Gagal', res.message || 'Gagal menyimpan bidang.', 5000);
            }
        })
        .catch(err => {
            if (err && err.errors) {
                showToast('error', 'Validasi Gagal', Object.values(err.errors).flat().join('<br>'), 6000);
            } else {
                showToast('error', 'Error', 'Terjadi kesalahan.', 5000);
            }
        });
    });

    // Setelah Divisi berubah, refresh pilihan Divisi di form Bidang
    // supaya divisi baru langsung bisa dipilih tanpa reload halaman.
    function refreshPositionDivisionOptions() {
        var select = document.getElementById('position_division_id');
        var currentValue = select.value;
        var options = '<option value="">Pilih Divisi</option>';
        divisionsData.forEach(function(d) {
            options += `<option value="${d.id}">${d.name}</option>`;
        });
        select.innerHTML = options;
        select.value = currentValue;
    }

    // Init
    renderDivisionList();
    renderPositionList();

    // ============================================================
    // LIVE UPDATE — polling berkala (bukan WebSocket). Tabel pengguna
    // di-refresh otomatis kalau ada admin lain yang menambah/mengubah/
    // menghapus pengguna, tanpa reload manual. Jeda otomatis kalau
    // ada modal (kelola divisi/bidang) terbuka.
    // ============================================================
    (function () {
        var lastSignature = 'initial';

        createLivePoller({
            url: function () { return window.location.pathname; },
            interval: 20000,
            shouldPause: function () { return document.querySelector('.modal.show') !== null; },
            onUpdate: function (data) {
                if (data.signature === lastSignature) return;
                lastSignature = data.signature;

                var el = document.getElementById('userListCard');
                if (el) el.outerHTML = data.table;
            }
        });
    })();
</script>
@endsection