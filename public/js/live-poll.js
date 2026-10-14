/**
 * live-poll.js — modul polling terpusat untuk semua fitur live-update SIAP.
 *
 * LATAR BELAKANG (bug yang diperbaiki modul ini):
 * Sebelumnya tiap halaman (Booking Detail, Dashboard, Daftar Peminjaman,
 * Manajemen Ruang, Pengguna, Signage, Notifikasi, Profile) punya skrip
 * polling sendiri-sendiri yang HANYA berhenti kalau tab sedang tidak
 * terlihat (document.hidden). Kalau tab dibiarkan terbuka & terlihat
 * berjam-jam tanpa disentuh, polling itu jalan TERUS setiap 8-20 detik
 * selamanya — dan kalau sesi sudah habis di tengah jalan, polling itu
 * tetap jalan terus-menerus menembak server sia-sia (tidak pernah
 * berhenti sendiri). Inilah penyebab aplikasi terasa berat setelah
 * dibiarkan lama: banyak request percuma menumpuk dan berebut jatah
 * proses server dengan aksi nyata yang sedang Anda coba lakukan.
 *
 * Modul ini memperbaikinya dengan SATU implementasi yang dipakai ulang
 * semua halaman:
 *  1. Berhenti polling kalau tidak ada aktivitas mouse/keyboard/scroll
 *     sama sekali selama IDLE_TIMEOUT_MS (default 3 menit) — bukan cuma
 *     saat tab disembunyikan, tapi juga saat tab terlihat tapi didiamkan.
 *  2. Begitu mendeteksi sesi sudah habis (401/419, atau server
 *     mengarahkan ke halaman login), polling itu DIHENTIKAN PERMANEN
 *     untuk halaman tersebut — tidak diam-diam mencoba lagi selamanya —
 *     dan tampilkan pita pemberitahuan yang jelas dengan tombol muat ulang.
 *  3. Kalau ada error jaringan biasa (bukan sesi habis), interval polling
 *     otomatis diperlambat bertahap (exponential backoff, maksimal 5
 *     menit) alih-alih terus mencoba secepat semula.
 *  4. Begitu ada aktivitas/tab aktif lagi, polling langsung dicoba ulang
 *     saat itu juga (tidak menunggu interval berikutnya).
 */
(function (global) {
    'use strict';

    var IDLE_TIMEOUT_MS = 3 * 60 * 1000; // 3 menit tanpa aktivitas = dianggap idle
    var MAX_BACKOFF_MS = 5 * 60 * 1000;  // interval polling tidak pernah lebih lambat dari 5 menit
    var ACTIVITY_EVENTS = ['mousemove', 'mousedown', 'keydown', 'wheel', 'touchstart', 'click'];

    var lastActivityAt = Date.now();
    ACTIVITY_EVENTS.forEach(function (evt) {
        document.addEventListener(evt, function () { lastActivityAt = Date.now(); }, { passive: true });
    });

    function isUserIdle() {
        return (Date.now() - lastActivityAt) > IDLE_TIMEOUT_MS;
    }

    var sessionExpiredShown = false;
    function showSessionExpiredBanner() {
        if (sessionExpiredShown) return;
        sessionExpiredShown = true;

        var banner = document.createElement('div');
        banner.id = 'liveSessionExpiredBanner';
        banner.setAttribute('role', 'alert');
        banner.style.cssText = 'position:fixed; top:0; left:0; right:0; z-index:99999; ' +
            'background:#fef3c7; color:#92400e; padding:12px 20px; text-align:center; ' +
            'font-family:inherit; font-weight:600; font-size:14px; ' +
            'box-shadow:0 2px 10px rgba(0,0,0,0.12); display:flex; align-items:center; ' +
            'justify-content:center; gap:12px; flex-wrap:wrap;';
        banner.innerHTML =
            '<span><i class="bi bi-exclamation-triangle-fill"></i> Sesi Anda sudah berakhir (terlalu lama tidak aktif).</span>' +
            '<button type="button" style="height:32px; padding:0 16px; border-radius:8px; border:none; ' +
            'background:#92400e; color:#fff; font-weight:600; font-size:13px; cursor:pointer;" ' +
            'onclick="window.location.reload()">Masuk Kembali</button>';
        document.body.prepend(banner);
    }

    /**
     * showBlockingOverlay() / hideBlockingOverlay() — dipakai setelah form
     * CRUD berhasil disubmit (AJAX) dan sedang menunggu redirect ke halaman
     * berikutnya. TANPA ini, jeda antara "toast berhasil muncul" sampai
     * "browser benar-benar pindah halaman" membuat halaman lama masih bisa
     * diklik-klik bebas (tombol lain, submit ulang, dst) — membingungkan
     * dan berisiko submit ganda. Overlay ini memblokir seluruh interaksi
     * halaman selama jeda itu, dengan indikator jelas bahwa sistem sedang
     * memproses, bukan macet/tidak merespons.
     */
    function showBlockingOverlay(message) {
        hideBlockingOverlay();
        var overlay = document.createElement('div');
        overlay.id = 'liveBlockingOverlay';
        // z-index SENGAJA di bawah toast-container (yang umumnya 10000 di
        // halaman-halaman form) — supaya toast "Berhasil!" tetap selalu
        // terlihat jelas di atas overlay, tidak tertutup.
        overlay.style.cssText = 'position:fixed; inset:0; z-index:9997; ' +
            'background:rgba(255,255,255,0.75); backdrop-filter:blur(2px); ' +
            'display:flex; flex-direction:column; align-items:center; justify-content:center; ' +
            'gap:14px; cursor:wait;';
        overlay.innerHTML =
            '<div style="width:40px; height:40px; border-radius:50%; border:3px solid rgba(249,115,22,0.2); ' +
            'border-top-color:#f97316; animation:liveOverlaySpin 0.7s linear infinite;"></div>' +
            '<div style="font-weight:600; font-size:14px; color:#334155; font-family:inherit;">' +
            (message || 'Memproses...') + '</div>';
        // Cegah klik/keyboard tembus ke elemen di baliknya selama overlay aktif.
        overlay.addEventListener('click', function (e) { e.stopPropagation(); }, true);

        if (!document.getElementById('liveOverlaySpinStyle')) {
            var style = document.createElement('style');
            style.id = 'liveOverlaySpinStyle';
            style.textContent = '@keyframes liveOverlaySpin { to { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }
        document.body.appendChild(overlay);
    }
    function hideBlockingOverlay() {
        var existing = document.getElementById('liveBlockingOverlay');
        if (existing) existing.remove();
    }

    /**
     * Membuat satu instance poller.
     *
     * @param {Object} config
     * @param {string} config.url - Endpoint yang di-poll (harus mengembalikan JSON).
     * @param {number} [config.interval=20000] - Jeda antar polling (ms) dalam kondisi normal.
     * @param {function(Object)} config.onUpdate - Dipanggil dengan data JSON setiap kali polling berhasil DAN datanya berbeda (pengecekan "berbeda" dilakukan oleh pemanggil lewat signature/updated_at, bukan oleh modul ini).
     * @param {function(): boolean} [config.shouldPause] - Kembalikan true untuk menunda polling sementara (misal: ada modal terbuka).
     * @param {number} [config.idleTimeoutMs] - Override waktu idle khusus untuk poller ini.
     * @returns {{stop: function(), pokeNow: function()}}
     */
    function createLivePoller(config) {
        // FIX: sebelumnya `url` SELALU berupa string yang "dibekukan" satu
        // kali saat poller ini pertama kali didaftarkan (biasanya
        // window.location.search dibaca sekali di awal). Untuk halaman
        // yang punya navigasi client-side TANPA reload penuh (mis.
        // Dashboard: tombol "<"/">" pindah tanggal lewat history.
        // pushState, bukan reload) — poller ini terus-menerus menembak
        // URL LAMA selamanya, tidak pernah tahu URL sudah berganti.
        // Akibatnya tiap beberapa detik dia menimpa balik tampilan yang
        // sudah dipindah user ke tanggal lain dengan data dari tanggal
        // AWAL saat halaman dimuat — persis gejala "geser tanggal, tahu-
        // tahu balik sendiri". Sekarang `url` boleh berupa FUNGSI yang
        // dipanggil ULANG setiap kali polling jalan, supaya selalu
        // membaca URL/state TERBARU, bukan yang dibekukan di awal.
        var urlConfig = config.url;
        function resolveUrl() {
            return typeof urlConfig === 'function' ? urlConfig() : urlConfig;
        }
        var baseInterval = config.interval || 20000;
        var onUpdate = config.onUpdate || function () {};
        var shouldPause = config.shouldPause || function () { return false; };
        var idleTimeoutMs = config.idleTimeoutMs || IDLE_TIMEOUT_MS;

        var currentInterval = baseInterval;
        var timerId = null;
        var stopped = false;
        var isFetching = false;

        function isIdleForThisPoller() {
            return (Date.now() - lastActivityAt) > idleTimeoutMs;
        }

        function schedule(delay) {
            if (stopped) return;
            clearTimeout(timerId);
            timerId = setTimeout(tick, delay != null ? delay : currentInterval);
        }

        function tick() {
            if (stopped) return;

            if (document.hidden || isIdleForThisPoller() || shouldPause() || isFetching) {
                schedule();
                return;
            }

            isFetching = true;
            fetch(resolveUrl(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
                .then(function (response) {
                    // Sesi habis: server mengarahkan ke halaman login (redirect
                    // mengubah URL akhir) atau balas 401/419. Berhenti permanen —
                    // jangan terus mencoba, itu sia-sia dan cuma membebani server.
                    var contentType = response.headers.get('content-type') || '';
                    if (
                        response.status === 401 ||
                        response.status === 419 ||
                        response.redirected ||
                        contentType.indexOf('application/json') === -1
                    ) {
                        stopped = true;
                        showSessionExpiredBanner();
                        return null;
                    }
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (data) {
                        onUpdate(data);
                        currentInterval = baseInterval; // request berhasil -> reset backoff
                    }
                })
                .catch(function () {
                    // Error jaringan biasa (bukan sesi habis) — perlambat bertahap,
                    // jangan terus menghantam server dengan kecepatan yang sama.
                    currentInterval = Math.min(currentInterval * 1.7, MAX_BACKOFF_MS);
                })
                .finally(function () {
                    isFetching = false;
                    schedule();
                });
        }

        // Begitu tab kembali aktif, langsung cek saat itu juga — tidak usah
        // menunggu interval berikutnya.
        function onVisibilityChange() {
            if (!document.hidden && !stopped) tick();
        }
        document.addEventListener('visibilitychange', onVisibilityChange);

        schedule(currentInterval);

        return {
            stop: function () {
                stopped = true;
                clearTimeout(timerId);
                document.removeEventListener('visibilitychange', onVisibilityChange);
            },
            pokeNow: function () {
                if (!stopped) tick();
            }
        };
    }

    global.createLivePoller = createLivePoller;
    global.isSiapUserIdle = isUserIdle;
    global.showSessionExpiredBanner = showSessionExpiredBanner;
    global.showBlockingOverlay = showBlockingOverlay;
    global.hideBlockingOverlay = hideBlockingOverlay;
})(window);