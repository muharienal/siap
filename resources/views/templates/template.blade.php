<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f97316">
    <title>@yield('title', 'SIAP') - Sistem Informasi Administrasi Rapat</title>
    
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           SIAP DESIGN SYSTEM
           ============================================================ */
        :root {
            --brand-orange: #FF9142;
            --brand-orange-dark: #F2751A;
            --brand-green: #22C55E;
            --brand-green-dark: #16A34A;
            --brand-blue: #3b82f6;
            --brand-gradient: linear-gradient(135deg, var(--brand-orange) 0%, var(--brand-green) 100%);
            --brand-gradient-hover: linear-gradient(135deg, var(--brand-orange-dark) 0%, var(--brand-green-dark) 100%);

            --bg-body: #f5f7fa;
            --bg-card: #ffffff;
            --bg-hover: #f1f4f9;
            --bg-input: #f8fafc;
            --border-color: #e4e7ec;
            --border-color-light: #edf0f4;
            --grid-line-color: #f1f4f9;

            --text-primary: #0b1a33;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --text-inverse: #ffffff;

            --shadow-card: 0 4px 20px rgba(15, 23, 42, 0.05);
            --shadow-hover: 0 8px 30px rgba(15, 23, 42, 0.08);
            --shadow-dropdown: 0 16px 48px rgba(15, 23, 42, 0.12);

            --radius-card: 1rem;
            --radius-sm: 0.75rem;
            --radius-xs: 0.5rem;
            --radius-pill: 9999px;

            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.25rem;
            --space-6: 1.5rem;
            --space-7: 2rem;
            --space-8: 2.5rem;
            --space-9: 3rem;

            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-md: 1.125rem;
            --font-size-lg: 1.25rem;
            --font-size-xl: 1.5rem;
            --font-size-2xl: 1.75rem;

            --transition-fast: 0.15s ease;
            --transition-base: 0.2s ease;
            --transition-slow: 0.3s ease;

            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html { font-size: 100%; height: 100%; }
        body {
            font-family: var(--font-family);
            background: var(--bg-body);
            color: var(--text-primary);
            font-size: var(--font-size-base);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            min-height: 100%;
        }

        /* ============================================================
           APP LAYOUT - FIXED SIDEBAR & FLEX CONTENT
           ============================================================ */
        .app-wrapper {
            display: flex;
            position: relative;
            min-height: 100vh;
        }
        
        /* Zoom 0.9 untuk Desktop */
        @media (min-width: 992px) {
            .app-wrapper {
                zoom: 0.9;
            }
            @-moz-document url-prefix() {
                .app-wrapper {
                    zoom: 1;
                    -moz-transform: scale(0.9);
                    -moz-transform-origin: top left;
                    width: 111.11%;
                }
            }
            /* Fix height issue saat zoom */
            .app-content {
                min-height: calc(100vh / 0.9);
            }
            .app-sidebar {
                height: calc(100vh / 0.9);
            }
        }

        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color-light);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform var(--transition-slow);
        }
        
        .app-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: 250px;
            min-width: 0;
            background: var(--bg-body);
        }

        /* ============================================================
           SIDEBAR STYLES
           ============================================================ */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-5) var(--space-5) var(--space-4);
            border-bottom: 1px solid var(--border-color-light);
            flex-shrink: 0;
        }
        .sidebar-brand-icon {
            width: 42px; height: 42px; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(34,197,94,0.25);
            border-radius: 12px; overflow: hidden;
        }
        .sidebar-brand-icon svg { display: block; }
        .sidebar-brand-text { display: flex; flex-direction: column; min-width: 0; line-height: 1.25; }
        .sidebar-brand-name {
            font-weight: 700;
            font-size: var(--font-size-lg);
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.2px;
        }
        .sidebar-brand-sub {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--text-muted);
            white-space: normal;
            line-height: 1.3;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: var(--space-3) var(--space-3);
            -webkit-overflow-scrolling: touch;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 2px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            font-weight: 500;
            font-size: var(--font-size-sm);
            transition: all var(--transition-fast);
            text-decoration: none;
            margin-bottom: 2px;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            position: relative;
            min-height: 44px;
        }
        .nav-item i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
            color: var(--text-muted);
            transition: color var(--transition-fast);
        }
        .nav-badge {
            margin-left: auto;
            background: var(--brand-orange);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            padding: 4px 7px;
            border-radius: 999px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(249, 115, 22, 0.35);
        }
        .nav-item.active .nav-badge { background: #fff; color: var(--brand-orange-dark); }

        /* ============================================================
           ALERT MODERN — komponen acuan (dari halaman login), dipakai
           di halaman mana pun butuh notifikasi error/sukses/peringatan/
           info inline. Contoh: <div class="alert-modern alert-error">
           <i class="bi bi-exclamation-circle-fill alert-modern-icon"></i>
           <span>Pesan di sini</span></div>
           ============================================================ */
        .alert-modern {
            display: flex; align-items: flex-start; gap: 9px;
            padding: 11px 14px; border-radius: 11px;
            font-size: 0.85rem; font-weight: 500; line-height: 1.5;
            border: 1px solid transparent; margin-bottom: var(--space-4);
        }
        .alert-modern .alert-modern-icon { font-size: 1.05rem; flex-shrink: 0; margin-top: 1px; }
        .alert-modern.alert-error { background: #fef2f2; border-color: #fecdd3; color: #c0263c; }
        .alert-modern.alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .alert-modern.alert-warning { background: #fffbeb; border-color: #fde68a; color: #b45309; }
        .alert-modern.alert-info { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
        .nav-item:hover {
            background: var(--bg-hover);
            color: var(--brand-orange-dark);
        }
        .nav-item:hover i { color: var(--brand-orange); }
        .nav-item.active {
            background: rgba(249, 115, 22, 0.08);
            color: var(--brand-orange-dark);
            font-weight: 600;
        }
        .nav-item.active i { color: var(--brand-orange); }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 25%;
            height: 50%;
            width: 4px;
            background: var(--brand-gradient);
            border-radius: 0 4px 4px 0;
        }

        .section-title {
            font-size: var(--font-size-xs);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            padding: var(--space-5) var(--space-4) var(--space-2);
            margin-top: var(--space-2);
        }

        .sidebar-footer {
            flex-shrink: 0;
            padding: var(--space-4) var(--space-5);
            border-top: 1px solid var(--border-color-light);
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
            background: var(--bg-card);
        }

        /* ============================================================
           HEADER
           ============================================================ */
        .app-header {
            background: rgba(255, 255, 255, 0.85);
            border-bottom: 1px solid var(--border-color-light);
            padding: 0 var(--space-6);
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 1020;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            min-width: 0;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-secondary);
            padding: var(--space-2);
            cursor: pointer;
            display: none;
            border-radius: var(--radius-xs);
            transition: background var(--transition-fast);
            min-width: 44px;
            min-height: 44px;
            align-items: center;
            justify-content: center;
        }
        .sidebar-toggle:hover { background: var(--bg-hover); }
        
        .page-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }

        .icon-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--text-secondary);
            padding: var(--space-2);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-fast);
            position: relative;
            cursor: pointer;
        }
        .icon-btn:hover {
            background: var(--bg-hover);
            color: var(--brand-orange);
        }
        .badge-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid var(--bg-card);
        }

        .user-avatar {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            cursor: pointer;
            padding: var(--space-1) var(--space-3) var(--space-1) var(--space-1);
            border-radius: var(--radius-pill);
            transition: background var(--transition-fast);
            text-decoration: none;
            color: var(--text-primary);
            height: 44px;
        }
        .user-avatar:hover { background: var(--bg-hover); }
        .user-avatar img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color-light);
            flex-shrink: 0;
        }
        .user-name {
            font-size: var(--font-size-sm);
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
            display: flex;
            flex-direction: column;
        }
        .role-badge {
            font-weight: 400;
            font-size: var(--font-size-xs);
            color: var(--text-muted);
        }
        .chevron {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-left: var(--space-1);
        }

        /* ============================================================
           DROPDOWN NOTIFIKASI
           ============================================================ */
        .dropdown-notif {
            min-width: 360px;
            padding: 0;
            border: none;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-dropdown);
            overflow: hidden;
            background: var(--bg-card);
            border: 1px solid var(--border-color-light);
        }
        .notif-header {
            padding: var(--space-3) var(--space-4);
            border-bottom: 1px solid var(--border-color-light);
            font-weight: 700;
            font-size: var(--font-size-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-body {
            max-height: 360px;
            overflow-y: auto;
            padding: 0;
            -webkit-overflow-scrolling: touch;
        }
        .notif-item {
            padding: var(--space-3) var(--space-4);
            display: flex;
            align-items: flex-start;
            gap: var(--space-3);
            transition: background var(--transition-fast);
            border-bottom: 1px solid var(--border-color-light);
            cursor: pointer;
            text-decoration: none;
            color: var(--text-primary);
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: var(--bg-hover); }
        .n-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(249,115,22,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-orange);
            flex-shrink: 0;
            font-size: 1rem;
        }
        .n-content {
            flex: 1;
            font-size: var(--font-size-sm);
            min-width: 0;
        }
        .n-room {
            font-size: 11px;
            font-weight: 600;
            color: var(--brand-blue);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .n-time {
            font-size: var(--font-size-xs);
            color: var(--text-muted);
            margin-top: var(--space-1);
        }
        .notif-footer {
            padding: var(--space-3) var(--space-4);
            border-top: 1px solid var(--border-color-light);
            text-align: center;
            font-size: var(--font-size-sm);
            display: flex;
            justify-content: center;
            gap: var(--space-2);
            flex-wrap: wrap;
        }

        /* ============================================================
           MAIN CONTENT & FOOTER
           ============================================================ */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            padding: var(--space-6) var(--space-7);
            max-width: 1800px;
            width: 100%;
            margin: 0 auto;
        }

        .app-footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-color-light);
            padding: var(--space-3) var(--space-7);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: var(--font-size-xs);
            color: var(--text-muted);
            height: 48px;
            flex-shrink: 0;
            margin-top: auto; /* Dorong footer ke bawah */
        }
        .app-footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color var(--transition-fast);
        }
        .app-footer a:hover { color: var(--brand-orange-dark); }
        .footer-version {
            font-weight: 500;
            color: var(--text-muted);
        }

        /* Backdrop untuk mobile sidebar */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
            opacity: 0;
            transition: opacity var(--transition-slow);
        }
        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* ============================================================
           BOOTSTRAP OVERRIDE & UTILITIES
           ============================================================ */
        .btn {
            border-radius: var(--radius-sm);
            font-weight: 600;
            padding: var(--space-2) var(--space-4);
            transition: all var(--transition-fast);
            border: none;
            font-size: var(--font-size-sm);
            line-height: 1.4;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
            cursor: pointer;
        }
        .btn-primary {
            background: var(--brand-gradient);
            color: var(--text-inverse);
            box-shadow: 0 4px 12px rgba(249,115,22,0.2);
        }
        .btn-primary:hover {
            background: var(--brand-gradient-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16,185,129,0.25);
            color: var(--text-inverse);
        }
        .btn-outline-secondary {
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            background: transparent;
        }
        .btn-outline-secondary:hover {
            background: var(--bg-hover);
            border-color: var(--text-muted);
            color: var(--text-primary);
        }
        .btn-sm { height: 36px; padding: var(--space-1) var(--space-3); font-size: var(--font-size-xs); }

        .form-control, .form-select {
            height: 44px;
            padding: 0 var(--space-3);
            font-size: var(--font-size-sm);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background-color: var(--bg-input);
            color: var(--text-primary);
            transition: border var(--transition-fast), box-shadow var(--transition-fast);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-orange);
            background-color: var(--bg-card);
            box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
            outline: none;
        }
        .form-label {
            font-weight: 600;
            font-size: var(--font-size-xs);
            color: var(--text-secondary);
            margin-bottom: var(--space-1);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: var(--text-muted); border-radius: var(--radius-pill); }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-secondary); }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .mobile-bottom-nav {
            display: none;
        }

        /* ============================================================
           RESPONSIVE: MOBILE (< 992px) - NATIVE APP FEEL
           ============================================================ */
        @media (max-width: 991.98px) {
            /* FIX: sidebar drawer dihapus total di mobile/tablet — bukan lagi
               menu geser (hamburger), diganti sepenuhnya oleh floating bottom
               nav ala native app. */
            .app-sidebar {
                display: none;
            }

            .app-content {
                margin-left: 0;
                padding-bottom: 100px; /* Space for floating bottom nav */
                overflow-x: hidden; /* jaga-jaga anti horizontal scroll */
            }

            .sidebar-toggle { display: none; }

            .app-header {
                padding: 0 var(--space-4);
                height: 64px;
                padding-top: var(--safe-top);
                height: calc(64px + var(--safe-top));
            }
            
            .page-title {
                font-size: var(--font-size-lg);
            }

            /* FIX: bell notifikasi & avatar user di header disembunyikan di
               mobile/tablet — fungsinya sudah ada di bottom sheet "Menu"
               (Profil Saya, Notifikasi), jadi tidak perlu dobel. */
            .header-right {
                display: none;
            }
            
            .main-content {
                padding: var(--space-4);
            }
            
            .app-footer {
                display: none; /* Sembunyikan footer di mobile, ganti bottom nav */
            }
            
            /* Mobile Floating Bottom Nav — ala native app (bukan bar rata
               nempel tepi layar, tapi "melayang" dengan jarak & rounded) */
            .mobile-bottom-nav {
                display: flex;
                position: fixed;
                left: 14px;
                right: 14px;
                bottom: calc(14px + var(--safe-bottom));
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.6);
                border-radius: 26px;
                padding: 8px 6px;
                z-index: 1020;
                justify-content: space-around;
                box-shadow: 0 12px 32px rgba(15, 23, 42, 0.16), 0 2px 8px rgba(15, 23, 42, 0.08);
            }

            .mobile-bottom-nav .nav-btn {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
                padding: 8px 4px;
                background: none;
                border: none;
                color: var(--text-muted);
                font-size: 10px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.2s ease;
                min-height: 48px;
                justify-content: center;
                border-radius: 18px;
                position: relative;
            }

            .mobile-bottom-nav .nav-btn i {
                font-size: 1.3rem;
                transition: transform 0.2s ease;
            }

            .mobile-bottom-nav .nav-btn.active {
                color: var(--brand-orange-dark);
                background: rgba(249, 115, 22, 0.1);
            }
            .mobile-bottom-nav .nav-btn.active i { transform: translateY(-1px); }
            .mobile-bottom-nav .nav-btn:hover { color: var(--brand-orange); }

            .mobile-bottom-nav .nav-btn .nav-btn-badge {
                position: absolute;
                top: 2px;
                right: 18%;
                background: var(--brand-orange);
                color: #fff;
                font-size: 9px;
                font-weight: 700;
                min-width: 15px;
                height: 15px;
                border-radius: 999px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0 3px;
                box-shadow: 0 2px 4px rgba(249, 115, 22, 0.4);
            }
        }
    </style>

    <style>
        /* ============================================================
           BOTTOM SHEET "MENU LAINNYA" — dipicu tombol Menu di floating
           bottom nav. Cuma bisa dipicu di mobile/tablet (tombolnya
           disembunyikan di desktop), tapi style ini ditaruh di luar
           media query supaya berlaku konsisten kapan pun dipanggil.
        ============================================================ */
        .mobile-menu-sheet {
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            max-height: 80vh;
            padding-bottom: var(--safe-bottom);
        }
        .mobile-sheet-handle {
            width: 40px; height: 5px; border-radius: 999px;
            background: var(--border-color); margin: 10px auto 0;
        }
        .mobile-menu-sheet .offcanvas-header { padding: var(--space-3) var(--space-5) var(--space-2); }
        .mobile-menu-sheet .offcanvas-title { font-weight: 700; font-size: 1.05rem; color: var(--text-primary); }
        .mobile-menu-sheet .offcanvas-body { padding: 0 var(--space-4) var(--space-4); }

        .mobile-sheet-user {
            display: flex; align-items: center; gap: 12px;
            padding: var(--space-3) var(--space-3); background: var(--bg-hover);
            border-radius: var(--radius-sm); margin-bottom: var(--space-3);
        }
        .mobile-sheet-avatar {
            width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
            background: var(--brand-gradient); color: #fff; font-weight: 800; font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
        }
        .mobile-sheet-user-name { font-weight: 700; color: var(--text-primary); font-size: 0.92rem; }
        .mobile-sheet-user-role { font-size: 0.78rem; color: var(--text-muted); font-weight: 600; }

        .mobile-sheet-item {
            display: flex; align-items: center; gap: 14px;
            padding: 13px var(--space-3); border-radius: var(--radius-sm);
            color: var(--text-secondary); font-weight: 600; font-size: 0.92rem;
            text-decoration: none; position: relative;
        }
        .mobile-sheet-item:hover, .mobile-sheet-item:active { background: var(--bg-hover); color: var(--brand-orange-dark); }
        .mobile-sheet-item i { font-size: 1.2rem; width: 24px; text-align: center; color: var(--text-muted); }
        .mobile-sheet-item.text-danger i { color: #ef4444; }
        .mobile-sheet-item.text-danger:hover { color: #dc2626; background: rgba(239,68,68,0.06); }

        .mobile-sheet-badge {
            margin-left: auto; background: var(--brand-orange); color: #fff;
            font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px;
        }
        .mobile-sheet-divider {
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--text-muted); padding: var(--space-3) var(--space-3) var(--space-2);
            border-top: 1px solid var(--border-color-light); margin-top: 6px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- SIDEBAR -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                        <defs>
                            <linearGradient id="siapSidebarGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f2994a"/>
                                <stop offset="50%" stop-color="#8fa23f"/>
                                <stop offset="100%" stop-color="#1fae6e"/>
                            </linearGradient>
                        </defs>
                        <rect x="4" y="4" width="192" height="192" rx="44" fill="url(#siapSidebarGrad)"/>
                        <g transform="translate(45,51.1) scale(0.191)" fill="#ffffff">
                            <path d="M288 0a64 64 0 1 1 0 128 64 64 0 1 1 0-128zm96 312c0 25-12.7 47-32 59.9l0 44.1-128 0 0-44.1C204.7 359 192 337 192 312l0-40c0-53 43-96 96-96s96 43 96 96l0 40zm16 104l0-27.3c19.9-22.5 32-52.2 32-84.7l0-32c0-26.7-7.3-51.6-19.9-73.1 11.1-4.5 23.2-6.9 35.9-6.9 53 0 96 43 96 96l0 32c0 23.7-12.9 44.4-32 55.4l0 40.6-112 0zM64 375.4C44.9 364.4 32 343.7 32 320l0-32c0-53 43-96 96-96 12.7 0 24.8 2.5 35.9 6.9-12.6 21.4-19.9 46.4-19.9 73.1l0 32c0 32.5 12.1 62.1 32 84.7l0 27.3-112 0 0-40.6zM128 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112zm320 0a56 56 0 1 1 0 112 56 56 0 1 1 0-112zM24 464l528 0c13.3 0 24 10.7 24 24s-10.7 24-24 24L24 512c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                        </g>
                    </svg>
                </div>
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-name">SIAP</span>
                    <span class="sidebar-brand-sub">Sistem Informasi</span>
                    <span class="sidebar-brand-sub">Administrasi Rapat</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a href="/bookings" class="nav-item {{ request()->is('bookings*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event-fill"></i> Peminjaman Ruangan
                    @if(($sidebarPendingCount ?? 0) > 0)
                        <span class="nav-badge">{{ $sidebarPendingCount > 99 ? '99+' : $sidebarPendingCount }}</span>
                    @endif
                </a>
                <a href="/rooms" class="nav-item {{ request()->is('rooms*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Ruang Rapat
                </a>

                @if (Auth::user()->role == 1)
                    <div class="section-title">Pengaturan</div>

                    <a href="/settings/rooms" class="nav-item {{ request()->is('settings/rooms*') ? 'active' : '' }}">
                        <i class="bi bi-door-open-fill"></i> Manajemen Ruang Rapat
                    </a>
                    <!-- <a href="/settings/facilities" class="nav-item {{ request()->is('settings/facilities*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i> Fasilitas
                    </a> -->
                    <a href="/settings/users" class="nav-item {{ request()->is('settings/users*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Pengguna
                    </a>
                    <a href="/settings/signage" class="nav-item {{ request()->is('settings/signage*') ? 'active' : '' }}">
                        <i class="bi bi-tv-fill"></i> Digital Signage
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                &copy; 2026<br>
                <span>IT PT Petrokopindo</span><br>
                <span>Cipta Selaras</span>
            </div>
        </aside>

        <!-- SIDEBAR BACKDROP -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- MAIN CONTENT -->
        <div class="app-content">
            @php
                $displayName = Auth::user()->full_name ?? Auth::user()->name ?? 'User';
            @endphp

            <header class="app-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="page-title">@yield('page_title', 'Dashboard')</div>
                </div>

                <div class="header-right">
                    <div class="dropdown">
                        <button class="icon-btn" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
                            <i class="bi bi-bell"></i>
                            <span class="badge-dot" id="notificationBadge" style="display:none;"></span>
                        </button>
                        <div class="dropdown-menu dropdown-notif dropdown-menu-end" aria-labelledby="notificationDropdown">
                            <div class="notif-header">
                                <span>Notifikasi</span>
                                <span class="badge bg-primary" id="notificationCount" style="font-size:var(--font-size-xs); padding:var(--space-1) var(--space-3); border-radius:var(--radius-pill);">0</span>
                            </div>
                            <div class="notif-body" id="notificationList">
                                <div class="text-center py-3 text-muted" style="font-size:var(--font-size-sm);">
                                    <i class="bi bi-inbox me-1"></i> Memuat...
                                </div>
                            </div>
                            <div class="notif-footer">
                                <a href="#" id="markAllRead" style="font-weight:600; color:var(--brand-orange); text-decoration:none; font-size:var(--font-size-xs);">
                                    Tandai semua dibaca
                                </a>
                                <span style="color: var(--text-muted);">·</span>
                                <a href="{{ route('notifications.index') }}" style="font-weight:500; color:var(--text-secondary); text-decoration:none; font-size:var(--font-size-xs);">
                                    Lihat semua
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="user-avatar" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=f97316&color=fff&bold=true&size=64" alt="Avatar">
                            <span class="user-name">
                                {{ $displayName }}
                                <span class="role-badge">{{ Auth::user()->role == 1 ? 'Admin' : 'Karyawan' }}</span>
                            </span>
                            <span class="chevron"><i class="bi bi-chevron-down"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="border:none; border-radius:var(--radius-card); box-shadow:var(--shadow-dropdown); padding:var(--space-2); min-width:200px; border:1px solid var(--border-color-light);">
                            <li><a class="dropdown-item" href="{{ route('profile') }}" style="border-radius:var(--radius-xs); padding:var(--space-2) var(--space-3); display:flex; align-items:center; gap:var(--space-2); font-size:var(--font-size-sm);"><i class="bi bi-person"></i> Profile Saya</a></li>
                            <li><hr class="dropdown-divider" style="margin:var(--space-1) 0;"></li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="border-radius:var(--radius-xs); padding:var(--space-2) var(--space-3); display:flex; align-items:center; gap:var(--space-2); color:#ef4444; font-size:var(--font-size-sm);">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="main-content">
                @yield('content')
            </main>

            <footer class="app-footer">
                <span>
                </span>
                <span class="footer-version">
                    SIAP v1
                </span>
            </footer>
        </div>
    </div>

    <!-- MOBILE FLOATING BOTTOM NAV -->
    <nav class="mobile-bottom-nav">
        <a href="/dashboard" class="nav-btn {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <a href="/bookings" class="nav-btn {{ request()->is('bookings*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event-fill"></i>
            @if(($sidebarPendingCount ?? 0) > 0)
                <span class="nav-btn-badge">{{ $sidebarPendingCount > 9 ? '9+' : $sidebarPendingCount }}</span>
            @endif
            <span>Booking</span>
        </a>
        <a href="/rooms" class="nav-btn {{ request()->is('rooms*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span>Ruang</span>
        </a>
        <button type="button" class="nav-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileMoreMenu" aria-label="Menu Lainnya">
            <i class="bi bi-grid-3x3-gap-fill" id="mobileMoreIcon"></i>
            <span id="mobileMoreBadgeDot" class="nav-btn-badge" style="display:none; top:2px; right:16%;"></span>
            <span>Menu</span>
        </button>
    </nav>

    <!-- MOBILE "MENU LAINNYA" BOTTOM SHEET -->
    <div class="offcanvas offcanvas-bottom mobile-menu-sheet" tabindex="-1" id="mobileMoreMenu">
        <div class="mobile-sheet-handle"></div>
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu Lainnya</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mobile-sheet-user">
                <div class="mobile-sheet-avatar">{{ strtoupper(mb_substr(Auth::user()->full_name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div class="mobile-sheet-user-name">{{ Auth::user()->full_name }}</div>
                    <div class="mobile-sheet-user-role">{{ Auth::user()->role == 1 ? 'Admin' : (Auth::user()->role == 3 ? 'Sesdir' : 'Karyawan') }}</div>
                </div>
            </div>

            <a href="{{ route('profile') }}" class="mobile-sheet-item">
                <i class="bi bi-person-circle"></i> Profil Saya
            </a>
            <a href="{{ route('notifications.index') }}" class="mobile-sheet-item">
                <i class="bi bi-bell"></i> Notifikasi
                <span class="mobile-sheet-badge" id="mobileSheetNotifBadge" style="display:none;"></span>
            </a>

            @if(Auth::user()->role == 1)
                <div class="mobile-sheet-divider">Pengaturan</div>
                <a href="/settings/rooms" class="mobile-sheet-item">
                    <i class="bi bi-door-open"></i> Manajemen Ruang Rapat
                </a>
                <a href="/settings/users" class="mobile-sheet-item">
                    <i class="bi bi-people"></i> Pengguna
                </a>
                <a href="/settings/signage" class="mobile-sheet-item">
                    <i class="bi bi-tv"></i> Digital Signage
                </a>
            @endif

            <div class="mobile-sheet-divider"></div>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="mobile-sheet-item text-danger">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/live-poll.js') }}"></script>
    
    @if(config('app.key'))
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif

    <script>
    (function() {
        'use strict';

        // SIDEBAR TOGGLE (MOBILE)
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        function openSidebar() {
            sidebar.classList.add('open');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSidebar() {
            sidebar.classList.remove('open');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
            
            backdrop.addEventListener('click', closeSidebar);
            
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 991.98) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target) && sidebar.classList.contains('open')) {
                        closeSidebar();
                    }
                }
            });
            
            sidebar.querySelectorAll('.nav-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 991.98) {
                        closeSidebar();
                    }
                });
            });
        }

        // NOTIFICATIONS
        function loadNotifications() {
            if (typeof $ === 'undefined') return;
            $.ajax({
                url: '{{ route("notifications.get") }}',
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    updateNotificationUI(response);
                    notifPollBackoffMs = NOTIF_POLL_BASE_MS; // berhasil -> reset kecepatan polling normal
                },
                error: function(xhr) {
                    $('#notificationList').html('<div class="text-center py-3 text-muted" style="font-size:var(--font-size-sm);"><i class="bi bi-exclamation-triangle"></i> Gagal memuat</div>');
                    // FIX: sebelumnya polling ini jalan TERUS setiap 30 detik
                    // selamanya, bahkan setelah sesi habis (401/419) atau
                    // dibiarkan idle berjam-jam — menumpuk request percuma yang
                    // bikin aplikasi terasa berat. Sekarang berhenti permanen
                    // kalau sesinya sudah jelas habis, dan melambat bertahap
                    // untuk error lain (bukan terus mencoba secepat semula).
                    if (xhr.status === 401 || xhr.status === 419) {
                        notifPollStopped = true;
                        if (typeof showSessionExpiredBanner === 'function') showSessionExpiredBanner();
                    } else {
                        notifPollBackoffMs = Math.min(notifPollBackoffMs * 1.7, 5 * 60 * 1000);
                    }
                }
            });
        }

        function updateNotificationUI(response) {
            const notifications = response.notifications || [];
            const unreadCount = response.unread_count || 0;
            const badge = document.getElementById('notificationBadge');
            const countEl = document.getElementById('notificationCount');
            
            if (unreadCount > 0) {
                badge.style.display = 'block';
                countEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
            } else {
                badge.style.display = 'none';
                countEl.textContent = '0';
            }

            // Sinkronkan juga ke badge tombol "Menu" & item Notifikasi di
            // bottom sheet mobile, supaya konsisten dengan bell di header.
            const mobileMoreBadge = document.getElementById('mobileMoreBadgeDot');
            const mobileSheetBadge = document.getElementById('mobileSheetNotifBadge');
            if (mobileMoreBadge) mobileMoreBadge.style.display = unreadCount > 0 ? 'flex' : 'none';
            if (mobileSheetBadge) {
                if (unreadCount > 0) {
                    mobileSheetBadge.style.display = 'inline-block';
                    mobileSheetBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                } else {
                    mobileSheetBadge.style.display = 'none';
                }
            }
            
            const list = document.getElementById('notificationList');
            if (!list) return;
            
            if (notifications.length === 0) {
                list.innerHTML = '<div class="text-center py-4 text-muted" style="font-size:var(--font-size-sm);"><i class="bi bi-inbox d-block mb-2" style="font-size:2rem;"></i> Tidak ada notifikasi</div>';
                return;
            }
            
            let html = '';
            notifications.forEach(function(notif) {
                const isUnread = !notif.is_read;
                const bg = isUnread ? 'background:var(--bg-hover);' : '';
                const roomLine = notif.room_name ? `<div class="n-room"><i class="bi bi-door-open"></i> ${notif.room_name}</div>` : '';
                html += `
                    <a href="#" class="notif-item" data-id="${notif.id}" data-booking-id="${notif.booking_id || ''}" style="${bg}">
                        <div class="n-icon"><i class="bi bi-bell"></i></div>
                        <div class="n-content">
                            <div>${notif.message}</div>
                            ${roomLine}
                            <div class="n-time">${notif.created_at}</div>
                        </div>
                        ${isUnread ? '<span style="width:8px;height:8px;border-radius:50%;background:var(--brand-orange);flex-shrink:0;margin-top:8px;"></span>' : ''}
                    </a>
                `;
            });
            list.innerHTML = html;
            
            list.querySelectorAll('.notif-item').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const bookingId = this.dataset.bookingId;
                    // Tandai dibaca, lalu kalau notifikasi ini terkait booking,
                    // langsung arahkan ke halaman detail booking tersebut.
                    markAsRead(id, this, bookingId);
                });
            });
        }

        function markAsRead(id, element, bookingId) {
            if (typeof $ === 'undefined') return;
            $.ajax({
                url: '{{ route("notifications.mark-read") }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { notification_id: id },
                success: function() {
                    if (bookingId) {
                        window.location.href = '/bookings/' + bookingId;
                    } else {
                        loadNotifications();
                    }
                },
                error: function() { console.error('Gagal menandai notifikasi'); }
            });
        }

        const markAllReadBtn = document.getElementById('markAllRead');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (typeof $ === 'undefined') return;
                $.ajax({
                    url: '{{ route("notifications.mark-all-read") }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() { loadNotifications(); },
                    error: function() { console.error('Gagal menandai semua'); }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadNotifications);
        } else {
            loadNotifications();
        }

        // FIX: setInterval polos di bawah ini sebelumnya jalan tiap 30 detik
        // TANPA PERNAH BERHENTI, walau tab dibiarkan idle berjam-jam atau
        // sesi sudah habis. Sekarang pakai scheduler sendiri: skip kalau
        // idle/tab tersembunyi/sesi sudah habis, dan interval melambat
        // otomatis kalau lagi ada error jaringan (lihat notifPollBackoffMs
        // di dalam loadNotifications()).
        var NOTIF_POLL_BASE_MS = 30000;
        var notifPollBackoffMs = NOTIF_POLL_BASE_MS;
        var notifPollStopped = false;

        function scheduleNotifPoll() {
            if (notifPollStopped) return;
            setTimeout(function () {
                if (notifPollStopped) return;
                var idle = typeof isSiapUserIdle === 'function' ? isSiapUserIdle() : false;
                if (!document.hidden && !idle) {
                    loadNotifications();
                }
                scheduleNotifPoll();
            }, notifPollBackoffMs);
        }
        scheduleNotifPoll();

        // Begitu tab aktif lagi, langsung cek saat itu juga.
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && !notifPollStopped) loadNotifications();
        });

        // PREVENT iOS DOUBLE TAP ZOOM
        document.addEventListener('touchend', function(e) {
            const now = Date.now();
            if (now - (this.lastTouch || 0) <= 300) {
                e.preventDefault();
            }
            this.lastTouch = now;
        }, { passive: false });

    })();
    </script>

    {{-- ============================================================
         BUBBLE BANTUAN (WhatsApp Admin) — muncul di semua halaman
         karena ditaruh di layout utama.
    ============================================================ --}}
    <style>
        .help-bubble-fab {
            position: fixed; right: 24px; bottom: 24px; z-index: 1050;
            width: 56px; height: 56px; border-radius: 50%;
            background: #25D366; color: white; border: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; cursor: pointer;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.4);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .help-bubble-fab:hover { transform: scale(1.08); box-shadow: 0 10px 26px rgba(37, 211, 102, 0.5); }
        .help-bubble-fab .pulse-ring {
            position: absolute; inset: 0; border-radius: 50%;
            border: 2px solid #25D366; animation: helpPulse 2.2s ease-out infinite;
        }
        @keyframes helpPulse {
            0% { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .help-bubble-popup {
            position: fixed; right: 24px; bottom: 92px; z-index: 1050;
            width: 300px; max-width: calc(100vw - 48px);
            background: var(--bg-card); border-radius: var(--radius-card);
            box-shadow: var(--shadow-hover); border: 1px solid var(--border-color-light);
            overflow: hidden; transform: translateY(12px) scale(0.96); opacity: 0;
            visibility: hidden; transition: all 0.2s ease;
        }
        .help-bubble-popup.show { transform: translateY(0) scale(1); opacity: 1; visibility: visible; }
        .help-bubble-header {
            background: #25D366; color: white; padding: 16px 18px;
            display: flex; align-items: center; gap: 12px;
        }
        .help-bubble-header i { font-size: 1.6rem; }
        .help-bubble-header .title { font-weight: 700; font-size: 0.95rem; }
        .help-bubble-header .subtitle { font-size: 0.75rem; opacity: 0.9; }
        .help-bubble-body { padding: 16px 18px; }
        .help-bubble-body p { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 14px; line-height: 1.5; }
        .help-bubble-btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; height: 44px; border-radius: var(--radius-sm); border: none;
            background: #25D366; color: white; font-weight: 600; font-size: 0.88rem;
            text-decoration: none; transition: filter 0.2s ease;
        }
        .help-bubble-btn:hover { filter: brightness(0.95); color: white; }
        .help-bubble-close {
            background: none; border: none; color: white; opacity: 0.85;
            margin-left: auto; font-size: 1.1rem; cursor: pointer; align-self: flex-start;
        }
        .help-bubble-close:hover { opacity: 1; }

        @media (max-width: 991.98px) {
            .help-bubble-fab { bottom: calc(112px + var(--safe-bottom)); }
            .help-bubble-popup { bottom: calc(176px + var(--safe-bottom)); }
        }
        @media (max-width: 575.98px) {
            .help-bubble-fab { right: 16px; width: 52px; height: 52px; font-size: 1.4rem; }
            .help-bubble-popup { right: 16px; }
        }
    </style>

    <div class="help-bubble-popup" id="helpBubblePopup">
        <div class="help-bubble-header">
            <i class="bi bi-whatsapp"></i>
            <div>
                <div class="title">Butuh Bantuan?</div>
                <div class="subtitle">Admin SIAP memberikan bantuan</div>
            </div>
            <button type="button" class="help-bubble-close" onclick="toggleHelpBubble(false)" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="help-bubble-body">
            <p>Ada kendala pakai sistem peminjaman ruangan, atau butuh bantuan lain? Langsung chat admin lewat WhatsApp.</p>
            <a href="https://wa.me/6281332867075?text=Halo%20Admin%20SIAP%2C%20saya%20butuh%20bantuan." target="_blank" rel="noopener" class="help-bubble-btn">
                <i class="bi bi-whatsapp"></i> Chat Admin via WhatsApp
            </a>
        </div>
    </div>

    <button type="button" class="help-bubble-fab" id="helpBubbleFab" onclick="toggleHelpBubble()" aria-label="Bantuan">
        <span class="pulse-ring"></span>
        <i class="bi bi-headset"></i>
    </button>

    <script>
        function toggleHelpBubble(force) {
            const popup = document.getElementById('helpBubblePopup');
            const shouldShow = typeof force === 'boolean' ? force : !popup.classList.contains('show');
            popup.classList.toggle('show', shouldShow);
        }
        document.addEventListener('click', function (e) {
            const popup = document.getElementById('helpBubblePopup');
            const fab = document.getElementById('helpBubbleFab');
            if (popup.classList.contains('show') && !popup.contains(e.target) && !fab.contains(e.target)) {
                toggleHelpBubble(false);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>