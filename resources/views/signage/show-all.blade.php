<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Status Ruang Rapat &middot; Digital Signage</title>
    <link rel="icon" href="{{ asset('assets/images/icon/icon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-orange: #FF9142;
            --brand-orange-dark: #F2751A;
            --brand-green: #22C55E;
            --brand-green-dark: #16A34A;
            --brand-gradient: linear-gradient(135deg, var(--brand-orange) 0%, var(--brand-green) 100%);
            --red: #ef4444;
            --red-bg: #fef2f2;
            --red-dark: #b91c1c;

            /* ===== TEMA TERANG (default) ===== */
            --bg-body: #f4f6f9;
            --bg-card: #ffffff;
            --bg-subtle: #f8fafc;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border-color: #e7ebf1;
            --shadow-card: 0 6px 24px rgba(15,23,42,0.06);
            --shadow-soft: 0 4px 16px rgba(15,23,42,0.05);
        }

        /* ===== TEMA GELAP ===== */
        body.theme-dark {
            --bg-body: #0a0e17;
            --bg-card: #151b2b;
            --bg-subtle: #1c2438;
            --text-primary: #f1f5f9;
            --text-secondary: #a8b3c7;
            --text-muted: #6b7690;
            --border-color: #262f47;
            --red-bg: rgba(239,68,68,0.12);
            --shadow-card: 0 6px 24px rgba(0,0,0,0.35);
            --shadow-soft: 0 4px 16px rgba(0,0,0,0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 100%; height: 100%; overflow: hidden;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: var(--bg-body); color: var(--text-primary);
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* Wash gradient brand halus di pojok — dikecilkan di dark mode
           supaya tidak terasa terlalu terang di tengah latar gelap */
        body::before {
            content: ''; position: fixed; top: -20%; right: -12%; width: 55vw; height: 55vw;
            background: radial-gradient(circle, rgba(255,145,66,0.06), transparent 70%);
            z-index: 0; pointer-events: none;
        }
        body::after {
            content: ''; position: fixed; bottom: -18%; left: -10%; width: 45vw; height: 45vw;
            background: radial-gradient(circle, rgba(34,197,94,0.07), transparent 70%);
            z-index: 0; pointer-events: none;
        }
        body.theme-dark::before { background: radial-gradient(circle, rgba(255,145,66,0.09), transparent 70%); }
        body.theme-dark::after { background: radial-gradient(circle, rgba(34,197,94,0.1), transparent 70%); }

        .signage-wrapper {
            position: relative; z-index: 1;
            width: 100vw; height: 100vh;
            display: flex; flex-direction: column;
            padding: clamp(20px, 2.4vw, 40px);
            gap: clamp(14px, 1.6vw, 22px);
        }

        /* ===== HEADER ===== */
        .signage-header {
            display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
            background: var(--bg-card); border-radius: clamp(16px, 1.4vw, 24px);
            padding: clamp(14px, 1.5vw, 22px) clamp(18px, 2vw, 30px);
            box-shadow: var(--shadow-soft); border: 1px solid var(--border-color);
        }
        .signage-brand-row { display: flex; align-items: center; gap: 16px; }
        .signage-logo-group { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .signage-logo {
            width: clamp(40px, 3.6vw, 58px); height: clamp(40px, 3.6vw, 58px); border-radius: 14px;
            box-shadow: 0 4px 14px rgba(34,197,94,0.25); flex-shrink: 0; overflow: hidden;
        }
        .signage-logo svg { display: block; width: 100%; height: 100%; }
        .signage-logo.company { background: #fff; border: 1px solid var(--border-color); box-shadow: var(--shadow-soft); padding: 6px; }
        .signage-logo.company img { width: 100%; height: 100%; object-fit: contain; }
        .signage-logo-divider { font-size: clamp(14px, 1.3vw, 18px); color: var(--text-muted); font-weight: 300; }
        .signage-title h1 { font-size: clamp(19px, 2.3vw, 32px); font-weight: 700; color: var(--text-primary); letter-spacing: -0.01em; line-height: 1.15; }
        .signage-title .subtitle { font-size: clamp(10.5px, 0.85vw, 13.5px); color: var(--text-muted); font-weight: 600; margin-top: 2px; }

        .signage-clock { text-align: right; flex-shrink: 0; }
        .signage-time {
            font-size: clamp(28px, 3.6vw, 50px); font-weight: 800; line-height: 1;
            font-variant-numeric: tabular-nums; letter-spacing: -0.02em;
            background: var(--brand-gradient); -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .signage-date { font-size: clamp(11px, 1vw, 15px); color: var(--text-secondary); font-weight: 600; margin-top: 6px; }

        .signage-legend { display: flex; gap: 22px; flex-shrink: 0; padding: 0 4px; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: clamp(11.5px, 0.9vw, 14px); font-weight: 700; color: var(--text-secondary); }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; }
        .legend-dot.available { background: var(--brand-green); }
        .legend-dot.occupied { background: var(--red); }

        /* ============================================================
           ROOM PANELS
           FIX (STABILITAS): sebelumnya baris grid pakai tinggi otomatis
           (auto), jadi tinggi tiap panel ikut berubah tergantung berapa
           banyak jadwal yang ada di ruangan itu — begitu jadwal berganti
           status (upcoming -> aktif -> selesai) atau halaman refresh,
           tinggi antar panel jadi tidak konsisten dan terlihat "naik
           turun". `grid-auto-rows: 1fr` memaksa semua baris grid punya
           tinggi SAMA & TETAP, murni berdasar jumlah ruangan & ukuran
           layar — bukan berdasar isi kontennya. Kalau jadwal kepanjangan,
           tinggal scroll di dalam panel (sudah ada overflow-y:auto).
           ============================================================ */
        .rooms-board {
            flex: 1; display: grid; gap: clamp(14px, 1.6vw, 22px);
            grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
            grid-auto-rows: 1fr;
            min-height: 0;
        }

        .room-panel {
            background: var(--bg-card); border-radius: clamp(18px, 1.6vw, 26px);
            border: 1px solid var(--border-color);
            display: flex; flex-direction: column; overflow: hidden; min-height: 0;
            box-shadow: var(--shadow-card);
        }

        .room-panel-header {
            padding: clamp(16px, 1.7vw, 24px); flex-shrink: 0;
            display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
            position: relative;
        }
        .room-panel-header::after {
            content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 4px;
        }
        .room-panel-header.available { background: linear-gradient(180deg, rgba(34,197,94,0.08), transparent); }
        .room-panel-header.available::after { background: linear-gradient(90deg, var(--brand-green), #6ee7b7); }
        .room-panel-header.occupied { background: linear-gradient(180deg, rgba(239,68,68,0.07), transparent); }
        .room-panel-header.occupied::after { background: linear-gradient(90deg, var(--red), #fca5a5); }

        .room-panel-name { font-size: clamp(18px, 1.7vw, 25px); font-weight: 700; color: var(--text-primary); line-height: 1.2; }
        .room-panel-meta {
            font-size: clamp(11px, 0.9vw, 13.5px); color: var(--text-muted); font-weight: 600; margin-top: 5px;
            display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
        }

        /* Lebar tetap (min-width) supaya badge "TERSEDIA" vs "AKTIF" tidak
           bikin header sedikit "kedut" walau angkanya beda panjang teks. */
        .room-panel-status {
            font-size: clamp(10.5px, 0.9vw, 13px); font-weight: 800; padding: 7px 16px; border-radius: 999px;
            white-space: nowrap; flex-shrink: 0; letter-spacing: 0.3px; color: #fff;
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            min-width: clamp(88px, 7.5vw, 108px);
        }
        .room-panel-status.available { background: var(--brand-green); box-shadow: 0 3px 10px rgba(34,197,94,0.35); }
        .room-panel-status.occupied { background: var(--red); box-shadow: 0 3px 10px rgba(239,68,68,0.35); }
        .status-pulse { width: 7px; height: 7px; border-radius: 50%; background: #fff; animation: pulseDot 1.6s ease-in-out infinite; flex-shrink: 0; }
        @keyframes pulseDot { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }

        .room-panel-schedule {
            flex: 1; overflow-y: auto; padding: clamp(10px, 1.1vw, 16px);
            display: flex; flex-direction: column; gap: clamp(8px, 0.8vw, 11px);
        }
        .room-panel-schedule::-webkit-scrollbar { width: 5px; }
        .room-panel-schedule::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 99px; }

        /* ============================================================
           SLOT JADWAL — 3 status: akan datang (default), aktif (sedang
           berlangsung), selesai. FIX (redaksi & tampilan "selesai"):
           sebelumnya slot yang sudah lewat cuma diberi opacity:0.5 —
           terkesan seperti elemen "disabled"/error, bukan status yang
           memang wajar (rapat memang sudah kelar). Diganti: teks tetap
           terbaca jelas (opacity penuh), ditandai label "SELESAI" yang
           rapi, bukan meredupkan semuanya.
           ============================================================ */
        .schedule-slot {
            display: flex; gap: clamp(12px, 1.1vw, 16px); align-items: center;
            padding: clamp(10px, 1vw, 14px) clamp(12px, 1.1vw, 16px);
            border-radius: 14px; background: var(--bg-subtle);
            border-left: 4px solid var(--border-color);
        }
        .schedule-slot.active { border-left-color: var(--red); background: var(--red-bg); }
        .schedule-slot.done { border-left-color: var(--border-color); background: transparent; border: 1px solid var(--border-color); border-left-width: 4px; }
        .schedule-slot.done .schedule-slot-purpose,
        .schedule-slot.done .schedule-slot-organizer { color: var(--text-muted); }

        .schedule-slot-time {
            font-weight: 800; font-size: clamp(12.5px, 1.05vw, 16px); color: var(--text-primary);
            font-variant-numeric: tabular-nums; flex-shrink: 0; min-width: clamp(66px, 5.6vw, 86px); line-height: 1.35;
        }
        .schedule-slot.done .schedule-slot-time { color: var(--text-muted); }
        .schedule-slot-time small { display: block; font-size: clamp(10px, 0.78vw, 11.5px); font-weight: 600; color: var(--text-muted); }

        .schedule-slot-info { flex: 1; min-width: 0; }
        .schedule-slot-purpose {
            font-size: clamp(12.5px, 1.05vw, 16px); font-weight: 700; color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .schedule-slot-organizer {
            font-size: clamp(10.5px, 0.9vw, 13px); color: var(--text-muted); font-weight: 600; margin-top: 3px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            display: flex; align-items: center; gap: 5px;
        }

        /* Tag status kecil di ujung kanan slot — lebar tetap (min-width)
           supaya "AKTIF" & "SELESAI" tidak menggeser konten sebelahnya
           walau jumlah hurufnya beda. Redaksi disamakan & disingkat
           dengan badge status ruangan (AKTIF, bukan lagi "Live"/"Digunakan"). */
        .schedule-slot-tag {
            font-size: 9.5px; font-weight: 800; padding: 4px 10px; border-radius: 999px;
            text-transform: uppercase; letter-spacing: 0.4px; flex-shrink: 0;
            min-width: 58px; text-align: center;
        }
        .schedule-slot-tag.active { color: #fff; background: var(--red); }
        .schedule-slot-tag.done { color: var(--text-muted); background: var(--border-color); }

        .schedule-empty {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: var(--text-muted); text-align: center; gap: 10px; padding: 20px;
        }
        .schedule-empty .empty-icon-ring {
            width: clamp(46px, 4vw, 60px); height: clamp(46px, 4vw, 60px); border-radius: 50%;
            background: rgba(34,197,94,0.1); border: 1.5px solid rgba(34,197,94,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: clamp(18px, 1.6vw, 24px); color: var(--brand-green-dark);
        }
        .schedule-empty span { font-size: clamp(12px, 1vw, 14.5px); font-weight: 700; }

        /* ===== FOOTER / QR ===== */
        .signage-footer { display: flex; align-items: center; justify-content: flex-end; gap: 16px; flex-shrink: 0; }
        .signage-qr {
            background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;
            padding: clamp(8px, 0.8vw, 12px); display: flex; align-items: center; gap: 10px;
            box-shadow: var(--shadow-soft);
        }
        /* FIX (skan lebih mudah): kotak QR diperbesar sedikit & dikasih
           padding putih lebih longgar supaya modul QR (kotak-kotak
           kecilnya) tidak terlalu rapat — pada layar TV yang kurang
           tajam, modul rapat itulah yang bikin susah discan. Kepadatan
           modul sendiri diatur di level generate QR-nya (lihat script,
           pakai correctLevel Low — bukan High) supaya polanya memang
           lebih longgar/sederhana, bukan cuma dibesarkan tampilannya. */
        .qr-box { width: clamp(56px, 4.8vw, 76px); height: clamp(56px, 4.8vw, 76px); background: #fff; border-radius: 10px; padding: 7px; flex-shrink: 0; border: 1px solid var(--border-color); }
        .qr-box img, .qr-box canvas { width: 100%; height: 100%; image-rendering: pixelated; }
        .qr-caption { font-size: clamp(9px, 0.75vw, 12px); color: var(--text-muted); font-weight: 600; max-width: 140px; }

        /* ===== PORTRAIT ===== */
        body.signage-portrait .signage-header { flex-direction: column; align-items: flex-start; gap: 14px; }
        body.signage-portrait .signage-clock { text-align: left; }
        body.signage-portrait .rooms-board { grid-template-columns: 1fr; }

        .offline-banner {
            position: fixed; top: 0; left: 0; right: 0; background: #1e293b; color: #fbbf24;
            text-align: center; font-size: 13px; font-weight: 700; padding: 6px; display: none; z-index: 999;
        }
    </style>
</head>
<body class="signage-{{ $display->orientation }} {{ $display->theme === 'dark' ? 'theme-dark' : '' }}">

    <div class="offline-banner" id="offlineBanner"><i class="bi bi-wifi-off"></i> Koneksi terputus. Mencoba menyambung kembali...</div>

    <div class="signage-wrapper">
        <header class="signage-header">
            <div class="signage-brand-row">
                <div class="signage-logo-group">
                    <div class="signage-logo">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                            <defs>
                                <linearGradient id="siapTvGradAll" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#f2994a"/>
                                    <stop offset="50%" stop-color="#8fa23f"/>
                                    <stop offset="100%" stop-color="#1fae6e"/>
                                </linearGradient>
                            </defs>
                            <rect x="4" y="4" width="192" height="192" rx="44" fill="url(#siapTvGradAll)"/>
                            <g transform="translate(45,51.1) scale(0.191)" fill="#ffffff">
                                <path d="M288 0a64 64 0 1 1 0 128 64 64 0 1 1 0-128zm96 312c0 25-12.7 47-32 59.9l0 44.1-128 0 0-44.1C204.7 359 192 337 192 312l0-40c0-53 43-96 96-96s96 43 96 96l0 40zm16 104l0-27.3c19.9-22.5 32-52.2 32-84.7l0-32c0-26.7-7.3-51.6-19.9-73.1 11.1-4.5 23.2-6.9 35.9-6.9 53 0 96 43 96 96l0 32c0 23.7-12.9 44.4-32 55.4l0 40.6-112 0zM64 375.4C44.9 364.4 32 343.7 32 320l0-32c0-53 43-96 96-96 12.7 0 24.8 2.5 35.9 6.9-12.6 21.4-19.9 46.4-19.9 73.1l0 32c0 32.5 12.1 62.1 32 84.7l0 27.3-112 0 0-40.6zM128 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112zm320 0a56 56 0 1 1 0 112 56 56 0 1 1 0-112zM24 464l528 0c13.3 0 24 10.7 24 24s-10.7 24-24 24L24 512c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                            </g>
                        </svg>
                    </div>
                    <span class="signage-logo-divider">&times;</span>
                    <div class="signage-logo company">
                        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="PT Petrokopindo Cipta Selaras">
                    </div>
                </div>
                <div class="signage-title">
                    <h1>Status Ruang Rapat</h1>
                    <div class="subtitle">SIAP &middot; Sistem Informasi Administrasi Rapat</div>
                </div>
            </div>
            <div class="signage-clock">
                <div class="signage-time" id="liveClock">--:--</div>
                <div class="signage-date">{{ $now->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
            </div>
        </header>

        <div class="signage-legend">
            <div class="legend-item"><span class="legend-dot available"></span> Tersedia</div>
            <div class="legend-item"><span class="legend-dot occupied"></span> Aktif</div>
        </div>

        @include('signage.partials._rooms-board', ['roomsSchedule' => $roomsSchedule, 'now' => $now, 'display' => $display])

        @if($display->show_qr)
        <div class="signage-footer">
            <div class="signage-qr">
                <div class="qr-box" id="qrContainer"></div>
                <div class="qr-caption">Scan untuk booking ruang rapat</div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        function tickClock() {
            var el = document.getElementById('liveClock');
            if (!el) return;
            var now = new Date();
            var h = String(now.getHours()).padStart(2, '0');
            var m = String(now.getMinutes()).padStart(2, '0');
            el.textContent = h + ':' + m;
        }
        tickClock();
        setInterval(tickClock, 1000);

        @if($display->show_qr)
        var qrContainer = document.getElementById('qrContainer');
        if (qrContainer) {
            // FIX: correctLevel diturunkan dari H (High) ke L (Low).
            // Tingkat koreksi error yang lebih tinggi membuat pola QR jadi
            // lebih PADAT (butuh lebih banyak modul kecil untuk data yang
            // sama) — pola padat itulah yang susah discan kamera HP kalau
            // layar TV-nya kurang tajam. Link booking cukup pendek, jadi
            // koreksi Low sudah lebih dari cukup dan hasilnya jauh lebih
            // longgar & mudah discan.
            new QRCode(qrContainer, {
                text: "{{ route('bookings.create') }}",
                width: 200, height: 200,
                colorDark: "#000000", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.L
            });
        }
        @endif

        // ============================================================
        // LIVE UPDATE — sama seperti papan single-room: dulu reload
        // penuh (kedip), sekarang cuma grid status ruangan yang ditukar
        // lewat AJAX, jam & QR tidak ikut kedip.
        // ============================================================
        var SIGNAGE_ALL_LIVE_URL = "{{ route('signage.show', $display->display_token) }}";
        function pollSignageAllLive() {
            fetch(SIGNAGE_ALL_LIVE_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (!data) return;
                    var boardEl = document.getElementById('roomsBoard');
                    if (boardEl && data.board) boardEl.outerHTML = data.board;
                    document.getElementById('offlineBanner').style.display = 'none';
                })
                .catch(function () {
                    document.getElementById('offlineBanner').style.display = 'block';
                });
        }
        setInterval(pollSignageAllLive, {{ max(10, (int) $display->refresh_seconds) }} * 1000);

        window.addEventListener('offline', function () {
            document.getElementById('offlineBanner').style.display = 'block';
        });
        window.addEventListener('online', function () {
            document.getElementById('offlineBanner').style.display = 'none';
            pollSignageAllLive();
        });
    </script>
</body>
</html>