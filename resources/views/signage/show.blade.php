<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $room->name }} &middot; Digital Signage</title>
    <link rel="icon" href="{{ asset('assets/images/icon/icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-orange: #f97316;
            --brand-orange-dark: #ea580c;
            --brand-green: #10b981;
            --brand-green-dark: #059669;
            --brand-gradient: linear-gradient(135deg, var(--brand-orange) 0%, var(--brand-green) 100%);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --bg-body: #f4f6f9;
            --bg-card: #ffffff;
            --border-color: #e7ebf1;
            --red: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 100%; height: 100%; overflow: hidden;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: var(--bg-body); color: var(--text-primary);
        }
        body::before {
            content: ''; position: fixed; top: -20%; right: -12%; width: 55vw; height: 55vw;
            background: radial-gradient(circle, rgba(249,115,22,0.06), transparent 70%);
            z-index: 0; pointer-events: none;
        }
        body::after {
            content: ''; position: fixed; bottom: -18%; left: -10%; width: 45vw; height: 45vw;
            background: radial-gradient(circle, rgba(16,185,129,0.07), transparent 70%);
            z-index: 0; pointer-events: none;
        }

        .signage-wrapper {
            position: relative; z-index: 1;
            width: 100vw; height: 100vh;
            display: flex; flex-direction: column;
            padding: clamp(20px, 2.5vw, 44px);
            gap: clamp(16px, 2vw, 26px);
        }

        /* ===== HEADER ===== */
        .signage-header {
            display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
            background: var(--bg-card); border-radius: clamp(18px, 1.6vw, 26px);
            padding: clamp(16px, 1.8vw, 26px) clamp(20px, 2.2vw, 32px);
            box-shadow: 0 4px 20px rgba(15,23,42,0.05); border: 1px solid var(--border-color);
        }
        .signage-brand-row { display: flex; align-items: center; gap: 18px; }
        .signage-logo-group { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .signage-logo {
            width: clamp(48px, 4.2vw, 68px); height: clamp(48px, 4.2vw, 68px); border-radius: 16px;
            box-shadow: 0 4px 16px rgba(16,185,129,0.25); flex-shrink: 0; overflow: hidden;
        }
        .signage-logo svg { display: block; width: 100%; height: 100%; }
        .signage-logo.company { background: #fff; border: 1px solid var(--border-color); box-shadow: 0 4px 16px rgba(15,23,42,0.08); padding: 8px; }
        .signage-logo.company img { width: 100%; height: 100%; object-fit: contain; }
        .signage-logo-divider { font-size: clamp(16px, 1.6vw, 22px); color: var(--text-muted); font-weight: 300; }
        .signage-room-info h1 {
            font-size: clamp(24px, 3.2vw, 44px); font-weight: 700; color: var(--text-primary);
            letter-spacing: -0.02em; line-height: 1.1;
        }
        .signage-room-meta {
            font-size: clamp(12px, 1.05vw, 16px); color: var(--text-secondary); font-weight: 600;
            margin-top: 8px; display: flex; align-items: center; gap: 16px;
        }
        .signage-room-meta span { display: flex; align-items: center; gap: 6px; }
        .signage-sub-brand { font-size: clamp(10.5px, 0.85vw, 13px); color: var(--text-muted); font-weight: 600; margin-top: 4px; }

        .signage-clock { text-align: right; flex-shrink: 0; }
        .signage-time {
            font-size: clamp(30px, 4vw, 56px); font-weight: 800; line-height: 1;
            font-variant-numeric: tabular-nums; letter-spacing: -0.02em;
            background: var(--brand-gradient); -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .signage-date { font-size: clamp(12px, 1vw, 16px); color: var(--text-secondary); font-weight: 600; margin-top: 8px; }

        /* ===== MAIN STATUS ===== */
        .signage-status-main {
            flex: 1; border-radius: clamp(20px, 2vw, 32px);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: clamp(20px, 3vw, 48px);
            min-height: 0; position: relative; overflow: hidden;
            box-shadow: 0 16px 44px -12px rgba(15,23,42,0.16);
        }
        .signage-status-main.available { background: var(--brand-gradient); }
        .signage-status-main.occupied { background: linear-gradient(135deg, #ef4444 0%, #f97316 100%); }

        .status-ring {
            width: clamp(72px, 8vw, 118px); height: clamp(72px, 8vw, 118px); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: clamp(30px, 3.6vw, 50px); margin-bottom: clamp(14px, 1.6vw, 22px);
            background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.4); color: #fff;
        }

        .status-label {
            font-size: clamp(28px, 4.6vw, 66px); font-weight: 800; letter-spacing: -0.01em; line-height: 1; color: #fff;
        }
        .status-sub { font-size: clamp(14px, 1.4vw, 20px); color: rgba(255,255,255,0.92); font-weight: 600; margin-top: 14px; }

        .current-meeting {
            margin-top: clamp(20px, 2.6vw, 34px); background: rgba(255,255,255,0.18);
            backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);
            border-radius: 20px; padding: clamp(18px, 2.2vw, 30px); width: 100%; max-width: 640px;
        }
        .meeting-title { font-size: clamp(18px, 2vw, 28px); font-weight: 800; color: #fff; }
        .meeting-organizer {
            font-size: clamp(13px, 1.2vw, 17px); color: rgba(255,255,255,0.92); font-weight: 600;
            margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .meeting-time {
            font-size: clamp(14px, 1.5vw, 22px); color: #fff; font-weight: 800; margin-top: 12px;
            font-variant-numeric: tabular-nums;
        }
        .meeting-progress { height: 7px; background: rgba(255,255,255,0.25); border-radius: 99px; margin-top: 18px; overflow: hidden; }
        .meeting-progress-bar { height: 100%; background: #fff; border-radius: 99px; transition: width 1s linear; }

        /* ===== FOOTER ===== */
        .signage-footer { display: flex; gap: clamp(16px, 2vw, 28px); flex-shrink: 0; }

        .signage-upcoming {
            flex: 1; background: var(--bg-card); border: 1px solid var(--border-color);
            border-radius: clamp(16px, 1.5vw, 24px); padding: clamp(16px, 1.7vw, 24px); min-width: 0;
            box-shadow: 0 4px 20px rgba(15,23,42,0.05);
        }
        .upcoming-title {
            font-size: clamp(11.5px, 0.95vw, 14.5px); font-weight: 800; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;
            display: flex; align-items: center; gap: 8px;
        }
        .upcoming-list { display: flex; gap: 14px; overflow: hidden; }
        .upcoming-item {
            flex: 1; min-width: 0; background: var(--bg-body); border-radius: 14px;
            padding: clamp(12px, 1.2vw, 18px); border-left: 4px solid var(--brand-orange);
        }
        .upcoming-time {
            font-size: clamp(13px, 1.15vw, 17px); font-weight: 800; color: var(--brand-orange-dark);
            font-variant-numeric: tabular-nums;
        }
        .upcoming-purpose {
            font-size: clamp(13px, 1.1vw, 16px); font-weight: 700; color: var(--text-primary); margin-top: 5px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .upcoming-organizer {
            font-size: clamp(11px, 0.9vw, 13px); color: var(--text-muted); font-weight: 600; margin-top: 3px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .upcoming-empty { color: var(--text-muted); font-size: clamp(13px, 1.05vw, 16px); font-weight: 600; padding: 10px 0; }

        .signage-qr {
            flex-shrink: 0; background: var(--bg-card); border: 1px solid var(--border-color);
            border-radius: clamp(16px, 1.5vw, 24px); padding: clamp(14px, 1.6vw, 20px);
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;
            box-shadow: 0 4px 20px rgba(15,23,42,0.05);
        }
        .qr-box { width: clamp(80px, 8.5vw, 130px); height: clamp(80px, 8.5vw, 130px); background: #fff; border-radius: 12px; padding: 6px; border: 1px solid var(--border-color); }
        .qr-box img, .qr-box canvas { width: 100%; height: 100%; }
        .qr-caption { font-size: clamp(10px, 0.85vw, 13px); color: var(--text-muted); font-weight: 600; text-align: center; max-width: 140px; }

        /* ===== PORTRAIT ===== */
        body.signage-portrait .signage-header { flex-direction: column; align-items: flex-start; gap: 14px; }
        body.signage-portrait .signage-clock { text-align: left; }
        body.signage-portrait .signage-status-main { flex: 1.3; }
        body.signage-portrait .signage-footer { flex-direction: column; }
        body.signage-portrait .upcoming-list { flex-direction: column; }
        body.signage-portrait .signage-qr { flex-direction: row; width: 100%; justify-content: flex-start; gap: 16px; }
        body.signage-portrait .qr-caption { text-align: left; max-width: none; }

        .offline-banner {
            position: fixed; top: 0; left: 0; right: 0; background: #1e293b; color: #fbbf24;
            text-align: center; font-size: 13px; font-weight: 700; padding: 6px; display: none; z-index: 999;
        }
    </style>
</head>
<body class="signage-{{ $display->orientation }}">

    <div class="offline-banner" id="offlineBanner"><i class="bi bi-wifi-off"></i> Koneksi terputus. Mencoba menyambung kembali...</div>

    <div class="signage-wrapper">
        <header class="signage-header">
            <div class="signage-brand-row">
                <div class="signage-logo-group">
                    <div class="signage-logo">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                            <defs>
                                <linearGradient id="siapTvGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#f2994a"/>
                                    <stop offset="50%" stop-color="#8fa23f"/>
                                    <stop offset="100%" stop-color="#1fae6e"/>
                                </linearGradient>
                            </defs>
                            <rect x="4" y="4" width="192" height="192" rx="44" fill="url(#siapTvGrad)"/>
                            <g transform="translate(45,51.1) scale(0.191)" fill="#ffffff">
                                <path d="M288 0a64 64 0 1 1 0 128 64 64 0 1 1 0-128zm96 312c0 25-12.7 47-32 59.9l0 44.1-128 0 0-44.1C204.7 359 192 337 192 312l0-40c0-53 43-96 96-96s96 43 96 96l0 40zm16 104l0-27.3c19.9-22.5 32-52.2 32-84.7l0-32c0-26.7-7.3-51.6-19.9-73.1 11.1-4.5 23.2-6.9 35.9-6.9 53 0 96 43 96 96l0 32c0 23.7-12.9 44.4-32 55.4l0 40.6-112 0zM64 375.4C44.9 364.4 32 343.7 32 320l0-32c0-53 43-96 96-96 12.7 0 24.8 2.5 35.9 6.9-12.6 21.4-19.9 46.4-19.9 73.1l0 32c0 32.5 12.1 62.1 32 84.7l0 27.3-112 0 0-40.6zM128 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112zm320 0a56 56 0 1 1 0 112 56 56 0 1 1 0-112zM24 464l528 0c13.3 0 24 10.7 24 24s-10.7 24-24 24L24 512c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                            </g>
                        </svg>
                    </div>
                    <div class="signage-logo company">
                        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="PT Petrokopindo Cipta Selaras">
                    </div>
                </div>
                <div class="signage-room-info">
                    <h1>{{ $room->name }}</h1>
                    <div class="signage-room-meta">
                        <span><i class="bi bi-geo-alt-fill"></i> {{ $room->location }}</span>
                        <span><i class="bi bi-people-fill"></i> {{ $room->capacity }} orang</span>
                    </div>
                    <div class="signage-sub-brand">SIAP &middot; Sistem Informasi Administrasi Rapat</div>
                </div>
            </div>
            <div class="signage-clock">
                <div class="signage-time" id="liveClock">--:--</div>
                <div class="signage-date">{{ $now->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
            </div>
        </header>

        <main class="signage-status-main {{ $currentBooking ? 'occupied' : 'available' }}">
            @if($currentBooking)
                @php
                    $bStart = \Carbon\Carbon::parse($currentBooking->start_time);
                    $bEnd = \Carbon\Carbon::parse($currentBooking->end_time);
                    $totalMin = max(1, $bStart->diffInMinutes($bEnd));
                    $elapsedMin = max(0, $bStart->diffInMinutes($now));
                    $progressPct = min(100, max(0, round(($elapsedMin / $totalMin) * 100)));
                @endphp
                <div class="status-ring"><i class="bi bi-x-lg"></i></div>
                <div class="status-label">SEDANG DIGUNAKAN</div>
                <div class="current-meeting">
                    <div class="meeting-title">{{ $currentBooking->purpose }}</div>
                    <div class="meeting-organizer"><i class="bi bi-person-fill"></i> {{ $currentBooking->user->full_name ?? 'Unknown' }}</div>
                    <div class="meeting-time">{{ $bStart->format('H:i') }} &ndash; {{ $bEnd->format('H:i') }} WIB</div>
                    <div class="meeting-progress"><div class="meeting-progress-bar" style="width: {{ $progressPct }}%;"></div></div>
                </div>
            @else
                <div class="status-ring"><i class="bi bi-check-lg"></i></div>
                <div class="status-label">TERSEDIA</div>
                <div class="status-sub">Ruangan siap digunakan sekarang</div>
            @endif
        </main>

        <footer class="signage-footer">
            @if($display->show_upcoming)
                <div class="signage-upcoming">
                    <div class="upcoming-title"><i class="bi bi-clock-history"></i> Jadwal Berikutnya Hari Ini</div>
                    <div class="upcoming-list">
                        @forelse($upcomingBookings as $b)
                            <div class="upcoming-item">
                                <div class="upcoming-time">{{ \Carbon\Carbon::parse($b->start_time)->format('H:i') }}&ndash;{{ \Carbon\Carbon::parse($b->end_time)->format('H:i') }}</div>
                                <div class="upcoming-purpose">{{ $b->purpose }}</div>
                                <div class="upcoming-organizer">{{ $b->user->full_name ?? 'Unknown' }}</div>
                            </div>
                        @empty
                            <div class="upcoming-empty">Tidak ada jadwal lain hari ini.</div>
                        @endforelse
                    </div>
                </div>
            @endif

            @if($display->show_qr)
                <div class="signage-qr">
                    <div class="qr-box" id="qrContainer"></div>
                    <div class="qr-caption">Scan untuk booking ruangan ini</div>
                </div>
            @endif
        </footer>
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
            new QRCode(qrContainer, {
                text: "{{ route('bookings.create') }}?room={{ $room->id }}",
                width: 130, height: 130,
                colorDark: "#000000", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.H
            });
        }
        @endif

        setTimeout(function () {
            window.location.reload();
        }, {{ $display->refresh_seconds }} * 1000);

        window.addEventListener('offline', function () {
            document.getElementById('offlineBanner').style.display = 'block';
        });
        window.addEventListener('online', function () {
            document.getElementById('offlineBanner').style.display = 'none';
        });
    </script>
</body>
</html>