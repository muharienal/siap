@extends('templates.template')

@section('title', 'Digital Signage')
@section('page_title', 'Digital Signage')

@section('content')
<style>
    .db-toolbar {
        display: flex; justify-content: space-between; align-items: flex-start;
        flex-wrap: wrap; gap: var(--space-4); margin-bottom: var(--space-5);
    }
    .db-toolbar-desc { font-size: var(--font-size-sm); color: var(--text-muted); max-width: 640px; margin-top: 4px; line-height: 1.6; }

    .signage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: var(--space-5);
    }

    /* Kartu "Papan Semua Ruangan" — ditonjolkan, beda dari kartu per-ruangan */
    .allboard-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: var(--radius-card); box-shadow: var(--shadow-hover);
        padding: var(--space-5); margin-bottom: var(--space-5);
        display: flex; align-items: center; justify-content: space-between; gap: var(--space-4);
        flex-wrap: wrap; position: relative; overflow: hidden;
    }
    .allboard-card::before {
        content: ''; position: absolute; top: -40%; right: -10%; width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(249,115,22,0.25) 0%, transparent 70%);
    }
    .allboard-icon {
        width: 56px; height: 56px; border-radius: 16px; background: rgba(249,115,22,0.15);
        color: var(--brand-orange); display: flex; align-items: center; justify-content: center;
        font-size: 1.7rem; flex-shrink: 0;
    }
    .allboard-info { display: flex; align-items: center; gap: var(--space-4); flex: 1; min-width: 260px; position: relative; z-index: 1; }
    .allboard-title { font-size: 1.15rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; }
    .allboard-badge {
        font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 999px;
        background: rgba(249,115,22,0.2); color: #fdba74; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .allboard-desc { font-size: 0.85rem; color: #cbd5e1; margin-top: 4px; max-width: 480px; }
    .allboard-status { font-size: 11px; font-weight: 700; margin-top: 8px; display: inline-flex; align-items: center; gap: 6px; }
    .allboard-status.on { color: #6ee7b7; }
    .allboard-status.off { color: #94a3b8; }
    .allboard-actions { display: flex; gap: 10px; flex-shrink: 0; position: relative; z-index: 1; }
    .btn-allboard {
        height: 42px; padding: 0 18px; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; font-size: 13px;
        display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-allboard:hover { background: rgba(255,255,255,0.15); color: #fff; }
    .btn-allboard.primary { background: var(--brand-gradient); border-color: transparent; }
    .signage-card {
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card);
        overflow: hidden; display: flex; flex-direction: column;
    }
    .signage-card-image {
        width: 100%; height: 150px; background: var(--bg-input); position: relative; overflow: hidden;
    }
    .signage-card-image img { width: 100%; height: 100%; object-fit: cover; }
    .signage-card-image-placeholder {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); font-size: 2.5rem; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    }
    .signage-status-badge {
        position: absolute; top: 10px; right: 10px; padding: 4px 10px;
        border-radius: var(--radius-pill); font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .signage-status-badge.on { background: rgba(16,185,129,0.9); color: #fff; }
    .signage-status-badge.off { background: rgba(148,163,184,0.9); color: #fff; }

    .signage-card-body { padding: var(--space-4); display: flex; flex-direction: column; gap: 10px; flex: 1; }
    .signage-card-title { font-size: var(--font-size-md); font-weight: 700; color: var(--text-primary); margin: 0; }
    .signage-card-meta { font-size: var(--font-size-sm); color: var(--text-secondary); display: flex; align-items: center; gap: 6px; }
    .signage-card-meta i { color: var(--text-muted); }

    .signage-config-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 2px; }
    .signage-chip {
        font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: var(--radius-pill);
        background: rgba(59,130,246,0.08); color: var(--brand-blue); display: inline-flex; align-items: center; gap: 4px;
    }

    .signage-card-footer {
        display: flex; gap: 8px; padding: var(--space-3) var(--space-4);
        border-top: 1px solid var(--border-color-light); margin-top: auto;
    }
    .btn-signage-action {
        flex: 1; height: 38px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);
        background: var(--bg-card); color: var(--text-secondary); font-weight: 600; font-size: 12.5px;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; transition: all 0.2s;
        text-decoration: none;
    }
    .btn-signage-action:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }
    .btn-signage-action.primary { background: var(--brand-gradient); border-color: transparent; color: #fff; }
    .btn-signage-action.primary:hover { color: #fff; filter: brightness(0.96); }

    /* ============================================================
       MODAL KONFIGURASI TAMPILAN
    ============================================================ */
    .modern-modal .modal-dialog { max-width: 620px; }
    .modern-modal .modal-content {
        border: 1px solid var(--border-color-light); border-radius: var(--radius-card);
        overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .modern-modal .modal-header {
        background: var(--bg-card); border-bottom: 1px solid var(--border-color-light);
        padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
    }
    .modern-modal .modal-title { font-size: 1.15rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
    .modern-modal .modal-title i { color: var(--brand-orange); font-size: 1.4rem; }
    .modern-modal .btn-close { background: none; border: none; font-size: 1.5rem; color: var(--text-muted); opacity: 0.7; }
    .modern-modal .modal-body { padding: 1.5rem; }

    .orientation-picker { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: var(--space-4); }
    .orientation-option {
        border: 2px solid var(--border-color); border-radius: var(--radius-sm); padding: var(--space-4);
        display: flex; flex-direction: column; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s;
        background: var(--bg-input);
    }
    .orientation-option:hover { border-color: var(--brand-orange); }
    .orientation-option.selected { border-color: var(--brand-orange); background: rgba(249,115,22,0.05); }
    .orientation-preview {
        border: 2px solid var(--text-muted); border-radius: 4px; background: var(--bg-card);
    }
    .orientation-preview.landscape { width: 64px; height: 42px; }
    .orientation-preview.portrait { width: 42px; height: 64px; }
    .orientation-option.selected .orientation-preview { border-color: var(--brand-orange); }
    .orientation-label { font-weight: 600; font-size: 0.85rem; color: var(--text-secondary); }
    .orientation-option.selected .orientation-label { color: var(--brand-orange-dark); }

    /* Pemilih tema tampilan TV (terang/gelap) — pola visualnya disamakan
       persis dengan pemilih orientasi di atas supaya konsisten. */
    .theme-picker { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: var(--space-4); }
    .theme-option {
        border: 2px solid var(--border-color); border-radius: var(--radius-sm); padding: var(--space-4);
        display: flex; flex-direction: column; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s;
        background: var(--bg-input);
    }
    .theme-option:hover { border-color: var(--brand-orange); }
    .theme-option.selected { border-color: var(--brand-orange); background: rgba(255,145,66,0.05); }
    .theme-preview {
        width: 64px; height: 42px; border-radius: 6px; border: 2px solid var(--text-muted);
        display: flex; align-items: center; justify-content: center; gap: 3px;
    }
    .theme-preview.light { background: #f4f6f9; }
    .theme-preview.dark { background: #0a0e17; }
    .theme-preview span { width: 8px; height: 8px; border-radius: 50%; }
    .theme-preview.light span { background: linear-gradient(135deg, #FF9142, #22C55E); }
    .theme-preview.dark span { background: linear-gradient(135deg, #FF9142, #22C55E); box-shadow: 0 0 6px rgba(255,145,66,0.5); }
    .theme-option.selected .theme-preview { border-color: var(--brand-orange); }
    .theme-label { font-weight: 600; font-size: 0.85rem; color: var(--text-secondary); }
    .theme-option.selected .theme-label { color: var(--brand-orange-dark); }

    .form-label-modern { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .modern-select {
        width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--border-color);
        border-radius: var(--radius-sm); background: var(--bg-input); font-size: 0.9rem; color: var(--text-primary); outline: none;
    }
    .modern-select:focus { border-color: var(--brand-orange); background: var(--bg-card); box-shadow: 0 0 0 3px rgba(249,115,22,0.12); }

    .toggle-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: var(--space-3) var(--space-4); background: var(--bg-input); border-radius: var(--radius-sm); margin-bottom: 10px;
    }
    .toggle-row-label { font-weight: 600; font-size: 0.88rem; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
    .toggle-row-label small { display: block; font-weight: 400; color: var(--text-muted); font-size: 0.76rem; margin-top: 2px; }
    .form-switch .form-check-input { width: 42px; height: 24px; cursor: pointer; }
    .form-switch .form-check-input:checked { background-color: var(--brand-orange); border-color: var(--brand-orange); }

    .signage-link-box {
        display: flex; align-items: center; gap: 8px; background: var(--bg-input);
        border: 1px dashed var(--border-color); border-radius: var(--radius-sm); padding: 10px 12px; margin-top: var(--space-3);
    }
    .signage-link-box input {
        flex: 1; border: none; background: none; font-size: 0.8rem; color: var(--text-secondary); outline: none;
    }
    .signage-link-box button {
        background: none; border: none; color: var(--brand-blue); cursor: pointer; font-size: 1.1rem; flex-shrink: 0;
    }

    .btn-modal-cancel {
        height: 42px; padding: 0 20px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);
        background: var(--bg-card); color: var(--text-secondary); font-weight: 600; cursor: pointer;
    }
    .btn-modal-submit {
        height: 42px; padding: 0 24px; border-radius: var(--radius-sm); border: none; background: var(--brand-gradient);
        color: white; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(249,115,22,0.2);
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-modal-regen {
        height: 42px; padding: 0 16px; border-radius: var(--radius-sm); border: 1px solid rgba(239,68,68,0.3);
        background: rgba(239,68,68,0.05); color: #dc2626; font-weight: 600; cursor: pointer; font-size: 0.85rem;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-modal-regen:hover { background: rgba(239,68,68,0.1); }

    .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
    .toast-notification {
        min-width: 320px; max-width: 400px; background: var(--bg-card); border-radius: var(--radius-sm);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15); padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px;
        animation: toastSlideIn 0.3s ease forwards; border-left: 4px solid;
    }
    .toast-notification.success { border-left-color: #10b981; }
    .toast-notification.error { border-left-color: #ef4444; }
    .toast-icon { font-size: 1.2rem; margin-top: 2px; }
    .toast-notification.success .toast-icon { color: #10b981; }
    .toast-notification.error .toast-icon { color: #ef4444; }
    .toast-content { flex: 1; }
    .toast-title { font-weight: 700; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 2px; }
    .toast-message { font-size: 0.82rem; color: var(--text-secondary); }
    .toast-close { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem; }
    @keyframes toastSlideIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes toastSlideOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(30px); } }
</style>

<div class="db-toolbar">
    <div>
        <div class="db-toolbar-desc">
            <i class="bi bi-info-circle"></i> Setiap ruangan punya link unik untuk ditayangkan di layar, tanpa perlu login.
        </div>
    </div>
</div>

<div class="allboard-card">
    <div class="allboard-info">
        <div class="allboard-icon"><i class="bi bi-display"></i></div>
        <div>
            <div class="allboard-title">
                Papan Semua Ruangan
                <span class="allboard-badge">Direkomendasikan</span>
            </div>
            <div class="allboard-desc">
                Satu layar menampilkan status seluruh ruangan sekaligus, cocok untuk lobby/area umum.
            </div>
            <div class="allboard-status {{ $allBoard && $allBoard->is_active ? 'on' : 'off' }}">
                <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                {{ $allBoard && $allBoard->is_active ? 'Aktif & Terkonfigurasi' : 'Belum Diatur' }}
            </div>
        </div>
    </div>
    <div class="allboard-actions">
        <button type="button" class="btn-allboard primary" onclick='openAllBoardModal(@json($allBoard))'>
            <i class="bi bi-sliders"></i> Kelola Tampilan
        </button>
        @if($allBoard)
            <a href="{{ route('signage.show', $allBoard->display_token) }}" target="_blank" class="btn-allboard" title="Buka Preview">
                <i class="bi bi-box-arrow-up-right"></i> Preview
            </a>
        @endif
    </div>
</div>

<div style="display:flex; align-items:center; gap:10px; margin-bottom: var(--space-4);">
    <div style="flex:1; height:1px; background: var(--border-color-light);"></div>
    <span style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">
        Atau atur per ruangan
    </span>
    <div style="flex:1; height:1px; background: var(--border-color-light);"></div>
</div>

@include('settings.signage.partials._signage-grid', ['rooms' => $rooms])

{{-- ========================================== --}}
{{-- MODAL: KELOLA PAPAN SEMUA RUANGAN          --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="allBoardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-display"></i> Kelola Papan Semua Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="allBoardForm">
                    <label class="form-label-modern">Orientasi Layar</label>
                    <div class="orientation-picker">
                        <div class="orientation-option" data-orientation="landscape" onclick="selectAllBoardOrientation('landscape')">
                            <div class="orientation-preview landscape"></div>
                            <span class="orientation-label">Landscape (TV Datar)</span>
                        </div>
                        <div class="orientation-option" data-orientation="portrait" onclick="selectAllBoardOrientation('portrait')">
                            <div class="orientation-preview portrait"></div>
                            <span class="orientation-label">Portrait (TV Berdiri)</span>
                        </div>
                    </div>
                    <input type="hidden" name="orientation" id="allBoardOrientation" value="landscape">

                    <label class="form-label-modern">Tema Warna</label>
                    <div class="theme-picker">
                        <div class="theme-option" data-theme="light" onclick="selectAllBoardTheme('light')">
                            <div class="theme-preview light"><span></span></div>
                            <span class="theme-label">Terang</span>
                        </div>
                        <div class="theme-option" data-theme="dark" onclick="selectAllBoardTheme('dark')">
                            <div class="theme-preview dark"><span></span></div>
                            <span class="theme-label">Gelap</span>
                        </div>
                    </div>
                    <input type="hidden" name="theme" id="allBoardTheme" value="light">

                    <div class="toggle-row">
                        <div class="toggle-row-label">
                            <i class="bi bi-qr-code"></i>
                            <span>Tampilkan QR Booking
                                <small>QR umum, pengunjung pilih sendiri ruangan saat booking</small>
                            </span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_qr" id="allBoardShowQr" checked>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-modern">Auto-Refresh Layar</label>
                        <select name="refresh_seconds" id="allBoardRefreshSeconds" class="modern-select">
                            <option value="30">Tiap 30 detik</option>
                            <option value="60" selected>Tiap 1 menit</option>
                            <option value="120">Tiap 2 menit</option>
                            <option value="300">Tiap 5 menit</option>
                        </select>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-row-label">
                            <i class="bi bi-power"></i>
                            <span>Tampilan Aktif
                                <small>Non-aktifkan sementara tanpa menghapus link</small>
                            </span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="allBoardIsActive" checked>
                        </div>
                    </div>

                    <div id="allBoardLinkSection" style="display:none;">
                        <label class="form-label-modern mt-2">Link Tampilan</label>
                        <div class="signage-link-box">
                            <input type="text" id="allBoardDisplayUrl" readonly>
                            <button type="button" onclick="copyAllBoardLink()" title="Salin Link"><i class="bi bi-clipboard"></i></button>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn-modal-regen" id="allBoardRegenBtn">
                                <i class="bi bi-arrow-repeat"></i> Buat Ulang Link (link lama tidak berlaku)
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-modal-submit"><i class="bi bi-save"></i> Simpan Konfigurasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL: KELOLA TAMPILAN                     --}}
{{-- ========================================== --}}
<div class="modal fade modern-modal" id="signageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-tv-fill"></i> Kelola Tampilan: <span id="signageModalRoomName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="signageForm">
                    <label class="form-label-modern">Orientasi Layar</label>
                    <div class="orientation-picker">
                        <div class="orientation-option" data-orientation="landscape" onclick="selectOrientation('landscape')">
                            <div class="orientation-preview landscape"></div>
                            <span class="orientation-label">Landscape (TV Datar)</span>
                        </div>
                        <div class="orientation-option" data-orientation="portrait" onclick="selectOrientation('portrait')">
                            <div class="orientation-preview portrait"></div>
                            <span class="orientation-label">Portrait (TV Berdiri)</span>
                        </div>
                    </div>
                    <input type="hidden" name="orientation" id="signageOrientation" value="landscape">

                    <label class="form-label-modern">Tema Warna</label>
                    <div class="theme-picker">
                        <div class="theme-option" data-theme="light" onclick="selectTheme('light')">
                            <div class="theme-preview light"><span></span></div>
                            <span class="theme-label">Terang</span>
                        </div>
                        <div class="theme-option" data-theme="dark" onclick="selectTheme('dark')">
                            <div class="theme-preview dark"><span></span></div>
                            <span class="theme-label">Gelap</span>
                        </div>
                    </div>
                    <input type="hidden" name="theme" id="signageTheme" value="light">

                    <div class="toggle-row">
                        <div class="toggle-row-label">
                            <i class="bi bi-qr-code"></i>
                            <span>Tampilkan QR Booking
                                <small>Peserta bisa scan untuk langsung booking ruangan ini</small>
                            </span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_qr" id="signageShowQr" checked>
                        </div>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-row-label">
                            <i class="bi bi-list-ul"></i>
                            <span>Tampilkan Jadwal Berikutnya
                                <small>Daftar rapat setelah ini, hari yang sama</small>
                            </span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_upcoming" id="signageShowUpcoming" checked>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label-modern">Jumlah Jadwal Ditampilkan</label>
                            <select name="upcoming_count" id="signageUpcomingCount" class="modern-select">
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ $i == 3 ? 'selected' : '' }}>{{ $i }} jadwal</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-modern">Auto-Refresh Layar</label>
                            <select name="refresh_seconds" id="signageRefreshSeconds" class="modern-select">
                                <option value="30">Tiap 30 detik</option>
                                <option value="60" selected>Tiap 1 menit</option>
                                <option value="120">Tiap 2 menit</option>
                                <option value="300">Tiap 5 menit</option>
                            </select>
                        </div>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-row-label">
                            <i class="bi bi-power"></i>
                            <span>Tampilan Aktif
                                <small>Non-aktifkan sementara tanpa menghapus link</small>
                            </span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="signageIsActive" checked>
                        </div>
                    </div>

                    <div id="signageLinkSection" style="display:none;">
                        <label class="form-label-modern mt-2">Link Tampilan</label>
                        <div class="signage-link-box">
                            <input type="text" id="signageDisplayUrl" readonly>
                            <button type="button" onclick="copySignageLink()" title="Salin Link"><i class="bi bi-clipboard"></i></button>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn-modal-regen" id="signageRegenBtn">
                                <i class="bi bi-arrow-repeat"></i> Buat Ulang Link (link lama tidak berlaku)
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-modal-submit"><i class="bi bi-save"></i> Simpan Konfigurasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
    function showToast(type, title, message, duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        const iconMap = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill' };
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

    var currentRoomId = null;
    var signageModal, allBoardModal;

    // ============================================================
    // PAPAN SEMUA RUANGAN
    // ============================================================
    function selectAllBoardOrientation(value) {
        document.getElementById('allBoardOrientation').value = value;
        document.querySelectorAll('#allBoardModal .orientation-option').forEach(el => {
            el.classList.toggle('selected', el.dataset.orientation === value);
        });
    }

    function selectAllBoardTheme(value) {
        document.getElementById('allBoardTheme').value = value;
        document.querySelectorAll('#allBoardModal .theme-option').forEach(el => {
            el.classList.toggle('selected', el.dataset.theme === value);
        });
    }

    function openAllBoardModal(allBoard) {
        selectAllBoardOrientation(allBoard ? allBoard.orientation : 'landscape');
        selectAllBoardTheme(allBoard && allBoard.theme ? allBoard.theme : 'light');
        document.getElementById('allBoardShowQr').checked = allBoard ? !!allBoard.show_qr : true;
        document.getElementById('allBoardRefreshSeconds').value = allBoard ? allBoard.refresh_seconds : 60;
        document.getElementById('allBoardIsActive').checked = allBoard ? !!allBoard.is_active : true;

        var linkSection = document.getElementById('allBoardLinkSection');
        if (allBoard && allBoard.display_token) {
            linkSection.style.display = 'block';
            document.getElementById('allBoardDisplayUrl').value = "{{ url('/signage') }}/" + allBoard.display_token;
        } else {
            linkSection.style.display = 'none';
        }

        allBoardModal.show();
    }

    function copyAllBoardLink() {
        var input = document.getElementById('allBoardDisplayUrl');
        input.select();
        navigator.clipboard.writeText(input.value).then(function () {
            showToast('success', 'Tersalin!', 'Link tampilan disalin ke clipboard.', 2500);
        }).catch(function () {
            document.execCommand('copy');
        });
    }

    // ============================================================
    // PER RUANGAN
    // ============================================================
    function selectOrientation(value) {
        document.getElementById('signageOrientation').value = value;
        document.querySelectorAll('#signageModal .orientation-option').forEach(el => {
            el.classList.toggle('selected', el.dataset.orientation === value);
        });
    }

    function selectTheme(value) {
        document.getElementById('signageTheme').value = value;
        document.querySelectorAll('#signageModal .theme-option').forEach(el => {
            el.classList.toggle('selected', el.dataset.theme === value);
        });
    }

    function openSignageModal(roomId, roomName, display) {
        currentRoomId = roomId;
        document.getElementById('signageModalRoomName').textContent = roomName;

        selectOrientation(display ? display.orientation : 'landscape');
        selectTheme(display && display.theme ? display.theme : 'light');
        document.getElementById('signageShowQr').checked = display ? !!display.show_qr : true;
        document.getElementById('signageShowUpcoming').checked = display ? !!display.show_upcoming : true;
        document.getElementById('signageUpcomingCount').value = display ? display.upcoming_count : 3;
        document.getElementById('signageRefreshSeconds').value = display ? display.refresh_seconds : 60;
        document.getElementById('signageIsActive').checked = display ? !!display.is_active : true;

        var linkSection = document.getElementById('signageLinkSection');
        if (display && display.display_url) {
            linkSection.style.display = 'block';
            document.getElementById('signageDisplayUrl').value = display.display_url;
        } else {
            linkSection.style.display = 'none';
        }

        signageModal.show();
    }

    function copySignageLink() {
        var input = document.getElementById('signageDisplayUrl');
        input.select();
        navigator.clipboard.writeText(input.value).then(function () {
            showToast('success', 'Tersalin!', 'Link tampilan disalin ke clipboard.', 2500);
        }).catch(function () {
            document.execCommand('copy');
        });
    }

    // ============================================================
    // FIX: bootstrap.bundle.min.js dimuat di bagian bawah layout utama
    // (setelah @yield('content')) — kalau new bootstrap.Modal(...) dan
    // addEventListener langsung dijalankan di sini (bukan di dalam
    // DOMContentLoaded), errornya "bootstrap is not defined" akan
    // menghentikan SELURUH script ini, bikin semua tombol "Kelola
    // Tampilan" tidak merespons sama sekali. Fungsi di atas tetap
    // didefinisikan langsung (global) supaya bisa dipanggil dari
    // atribut onclick="" di HTML kapan pun tombolnya diklik.
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        signageModal = new bootstrap.Modal(document.getElementById('signageModal'));
        allBoardModal = new bootstrap.Modal(document.getElementById('allBoardModal'));

        document.getElementById('allBoardForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var data = {
                orientation: document.getElementById('allBoardOrientation').value,
                theme: document.getElementById('allBoardTheme').value,
                show_qr: document.getElementById('allBoardShowQr').checked ? 1 : 0,
                refresh_seconds: document.getElementById('allBoardRefreshSeconds').value,
                is_active: document.getElementById('allBoardIsActive').checked ? 1 : 0,
            };

            fetch('{{ route("settings.signage.save-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('success', 'Berhasil!', res.message, 3000);
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    showToast('error', 'Gagal', res.message || 'Gagal menyimpan konfigurasi.', 5000);
                }
            })
            .catch(() => showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000));
        });

        document.getElementById('allBoardRegenBtn').addEventListener('click', function () {
            if (!confirm('Link lama akan langsung tidak berlaku. Lanjutkan?')) return;

            fetch('{{ route("settings.signage.regenerate-all") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('allBoardDisplayUrl').value = res.display_url;
                    showToast('success', 'Berhasil!', res.message, 3500);
                } else {
                    showToast('error', 'Gagal', res.message || 'Gagal membuat ulang link.', 5000);
                }
            })
            .catch(() => showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000));
        });

        document.getElementById('signageForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var data = {
                orientation: formData.get('orientation'),
                theme: formData.get('theme'),
                show_qr: document.getElementById('signageShowQr').checked ? 1 : 0,
                show_upcoming: document.getElementById('signageShowUpcoming').checked ? 1 : 0,
                upcoming_count: formData.get('upcoming_count'),
                refresh_seconds: formData.get('refresh_seconds'),
                is_active: document.getElementById('signageIsActive').checked ? 1 : 0,
            };

            fetch('/settings/signage/' + currentRoomId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('success', 'Berhasil!', res.message, 3000);
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    showToast('error', 'Gagal', res.message || 'Gagal menyimpan konfigurasi.', 5000);
                }
            })
            .catch(() => showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000));
        });

        document.getElementById('signageRegenBtn').addEventListener('click', function () {
            if (!confirm('Link lama akan langsung tidak berlaku. Lanjutkan?')) return;

            fetch('/settings/signage/' + currentRoomId + '/regenerate', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('signageDisplayUrl').value = res.display_url;
                    showToast('success', 'Berhasil!', res.message, 3500);
                } else {
                    showToast('error', 'Gagal', res.message || 'Gagal membuat ulang link.', 5000);
                }
            })
            .catch(() => showToast('error', 'Error', 'Terjadi kesalahan jaringan.', 5000));
        });
    });

    // ============================================================
    // LIVE UPDATE — polling berkala (bukan WebSocket). Grid status
    // konfigurasi tiap ruangan di-refresh otomatis kalau ada admin
    // lain yang mengubah pengaturan tampilan TV-nya, tanpa reload
    // manual. Jeda otomatis kalau modal Kelola Tampilan terbuka.
    // ============================================================
    (function () {
        var lastSignature = 'initial';

        createLivePoller({
            url: window.location.pathname,
            interval: 20000,
            shouldPause: function () { return document.querySelector('.modal.show') !== null; },
            onUpdate: function (data) {
                if (data.signature === lastSignature) return;
                lastSignature = data.signature;

                var el = document.getElementById('signageGrid');
                if (el) el.outerHTML = data.grid;
            }
        });
    })();
</script>
@endsection