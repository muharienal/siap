@extends('templates.template')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')

@section('content')
<style>
    .db-toolbar {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: var(--space-4); margin-bottom: var(--space-5);
    }
    .notif-tabs { display: flex; gap: 8px; }
    .notif-tab {
        height: 40px; padding: 0 18px; border-radius: var(--radius-pill);
        border: 1px solid var(--border-color); background: var(--bg-card);
        color: var(--text-secondary); font-weight: 600; font-size: var(--font-size-sm);
        display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
        transition: all 0.2s;
    }
    .notif-tab:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }
    .notif-tab.active { background: var(--brand-gradient); border-color: transparent; color: white; box-shadow: 0 4px 12px rgba(249,115,22,0.2); }
    .notif-tab .count-pill {
        background: rgba(255,255,255,0.25); padding: 1px 8px; border-radius: 999px; font-size: 11px;
    }
    .notif-tab:not(.active) .count-pill { background: rgba(249,115,22,0.12); color: var(--brand-orange-dark); }

    .btn-outline-sm {
        height: 40px; padding: 0 var(--space-4); border-radius: var(--radius-sm);
        font-weight: 600; font-size: var(--font-size-sm); background: var(--bg-card);
        border: 1px solid var(--border-color); color: var(--text-secondary);
        display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;
    }
    .btn-outline-sm:hover { border-color: var(--brand-orange); color: var(--brand-orange-dark); }
    .btn-outline-sm:disabled { opacity: 0.5; cursor: not-allowed; }

    .section-card {
        background: var(--bg-card); border: 1px solid var(--border-color-light);
        border-radius: var(--radius-card); box-shadow: var(--shadow-card); overflow: hidden;
    }

    .notif-row {
        display: flex; align-items: flex-start; gap: 14px;
        padding: var(--space-4) var(--space-5);
        border-bottom: 1px solid var(--border-color-light);
        text-decoration: none; color: inherit;
        transition: background 0.15s ease;
        cursor: pointer;
    }
    .notif-row:last-child { border-bottom: none; }
    .notif-row:hover { background: var(--bg-hover); }
    .notif-row.is-unread { background: rgba(249,115,22,0.03); }

    .notif-row-icon {
        width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(249,115,22,0.1); color: var(--brand-orange);
        font-size: 1.05rem;
    }
    .notif-row-body { flex: 1; min-width: 0; }
    .notif-row-message { font-size: var(--font-size-sm); color: var(--text-primary); line-height: 1.5; }
    .notif-row.is-unread .notif-row-message { font-weight: 600; }
    .notif-row-meta { display: flex; align-items: center; gap: 10px; margin-top: 6px; flex-wrap: wrap; }
    .notif-row-time { font-size: 11px; color: var(--text-muted); font-weight: 500; }
    .notif-row-room {
        font-size: 11px; font-weight: 600; color: var(--brand-blue);
        display: inline-flex; align-items: center; gap: 4px;
    }
    .notif-row-dot {
        width: 8px; height: 8px; border-radius: 50%; background: var(--brand-orange);
        flex-shrink: 0; margin-top: 6px;
    }

    .empty-state { text-align: center; padding: var(--space-9) var(--space-5); }
    .empty-state i { font-size: 2.75rem; color: var(--border-color); display: block; margin-bottom: var(--space-3); }
    .empty-state p { color: var(--text-muted); margin: 0; }

    @media (max-width: 575.98px) {
        .db-toolbar { flex-direction: column; align-items: stretch; }
        .notif-tabs { justify-content: space-between; }
        .notif-tab { flex: 1; justify-content: center; }
    }
</style>

<div class="db-toolbar">
    <div class="notif-tabs">
        <a href="{{ route('notifications.index') }}" class="notif-tab {{ $filter === 'all' ? 'active' : '' }}">
            Semua
        </a>
        <a href="{{ route('notifications.index', ['status' => 'unread']) }}" class="notif-tab {{ $filter === 'unread' ? 'active' : '' }}">
            Belum Dibaca @if($unreadCount > 0)<span class="count-pill">{{ $unreadCount }}</span>@endif
        </a>
    </div>
    <button type="button" class="btn-outline-sm" id="markAllReadBtn" {{ $unreadCount == 0 ? 'disabled' : '' }}>
        <i class="bi bi-check2-all"></i> Tandai Semua Dibaca
    </button>
</div>

@include('notifications.partials._list', ['notifications' => $notifications, 'filter' => $filter])

<script>
    // FIX (siap live-update): sebelumnya listener klik di-attach LANGSUNG
    // ke tiap elemen .notif-row satu-satu saat halaman dimuat. Begitu
    // daftar ini di-refresh otomatis (live-polling), elemen lama diganti
    // elemen baru yang TIDAK punya listener — klik jadi tidak berfungsi
    // sama sekali sampai reload manual. Sekarang pakai event delegation
    // di document (BUKAN di #notifListWrapper — wrapper itu sendiri yang
    // di-swap tiap polling, jadi listener yang nempel di situ akan ikut
    // hilang juga setiap kali swap terjadi) — dengan delegasi ke document,
    // listener tetap berfungsi walau isi di dalamnya berganti-ganti.
    document.addEventListener('click', function (e) {
        var row = e.target.closest('.notif-row');
        if (!row) return;
        var isUnread = row.dataset.unread === '1';
        if (!isUnread) return; // sudah dibaca, biarkan link jalan normal

        e.preventDefault();
        var id = row.dataset.id;
        var targetUrl = row.getAttribute('href');
        fetch('{{ route("notifications.mark-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notification_id: id })
        }).finally(function () {
            window.location.href = targetUrl;
        });
    });

    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            markAllBtn.disabled = true;
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(function () {
                window.location.reload();
            });
        });
    }

    // ============================================================
    // LIVE UPDATE — polling berkala (bukan WebSocket). Daftar
    // notifikasi di-refresh otomatis kalau ada notifikasi baru masuk
    // (mis. booking disetujui/ditolak/dialihkan dari device lain),
    // tanpa reload manual.
    // ============================================================
    (function () {
        var lastSignature = 'initial';

        createLivePoller({
            url: window.location.pathname + window.location.search,
            interval: 20000,
            onUpdate: function (data) {
                if (data.signature === lastSignature) return;
                lastSignature = data.signature;

                var el = document.getElementById('notifListWrapper');
                if (el) el.outerHTML = data.list;

                // Badge "Belum Dibaca" & tombol "Tandai Semua Dibaca" di
                // toolbar ikut disinkronkan (jumlahnya bisa berubah).
                var tabUnread = document.querySelector('.notif-tab:nth-child(2)');
                if (tabUnread) {
                    var pill = tabUnread.querySelector('.count-pill');
                    if (data.unread_count > 0) {
                        if (!pill) {
                            pill = document.createElement('span');
                            pill.className = 'count-pill';
                            tabUnread.appendChild(pill);
                        }
                        pill.textContent = data.unread_count;
                    } else if (pill) {
                        pill.remove();
                    }
                }
                var btn = document.getElementById('markAllReadBtn');
                if (btn) btn.disabled = data.unread_count == 0;
            }
        });
    })();
</script>
@endsection