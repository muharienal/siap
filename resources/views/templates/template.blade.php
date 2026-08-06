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
            --brand-orange: #f97316;
            --brand-orange-dark: #ea580c;
            --brand-green: #10b981;
            --brand-green-dark: #059669;
            --brand-blue: #3b82f6;
            --brand-gradient: linear-gradient(145deg, var(--brand-orange) 0%, var(--brand-green) 100%);
            --brand-gradient-hover: linear-gradient(145deg, var(--brand-orange-dark) 0%, var(--brand-green-dark) 100%);

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
        .sidebar-brand img { width: 40px; height: 40px; object-fit: contain; }
        .sidebar-brand span {
            font-weight: 800;
            font-size: var(--font-size-xl);
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.3px;
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

        /* ============================================================
           RESPONSIVE: MOBILE (< 992px) - NATIVE APP FEEL
           ============================================================ */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
                box-shadow: var(--shadow-dropdown);
                width: 280px;
                padding-top: var(--safe-top);
            }
            .app-sidebar.open { transform: translateX(0); }
            
            .app-content {
                margin-left: 0;
                padding-bottom: 70px; /* Space for bottom nav */
            }
            
            .sidebar-toggle { display: flex; }
            
            .app-header {
                padding: 0 var(--space-4);
                height: 64px;
                padding-top: var(--safe-top);
                height: calc(64px + var(--safe-top));
            }
            
            .page-title {
                font-size: var(--font-size-lg);
            }
            
            .user-name {
                display: none;
            }
            
            .user-avatar {
                padding: var(--space-1);
            }
            
            .dropdown-notif {
                min-width: 300px;
                position: fixed !important;
                right: 16px !important;
                left: auto !important;
                transform: none !important;
                top: 70px !important;
            }
            
            .main-content {
                padding: var(--space-4);
            }
            
            .app-footer {
                display: none; /* Sembunyikan footer di mobile, ganti bottom nav */
            }
            
            /* Mobile Bottom Nav */
            .mobile-bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: var(--bg-card);
                border-top: 1px solid var(--border-color-light);
                padding: var(--space-2) var(--space-1);
                z-index: 1020;
                padding-bottom: calc(var(--space-2) + var(--safe-bottom));
                justify-content: space-around;
                box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
            }
            
            .mobile-bottom-nav .nav-btn {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
                padding: var(--space-1);
                background: none;
                border: none;
                color: var(--text-muted);
                font-size: 10px;
                font-weight: 600;
                text-decoration: none;
                transition: color var(--transition-fast);
                min-height: 48px;
                justify-content: center;
            }
            
            .mobile-bottom-nav .nav-btn i {
                font-size: 1.3rem;
            }
            
            .mobile-bottom-nav .nav-btn.active,
            .mobile-bottom-nav .nav-btn:hover {
                color: var(--brand-orange);
            }
        }

        .mobile-bottom-nav {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- SIDEBAR -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP">
                <span>SIAP</span>
            </div>

            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a href="/bookings" class="nav-item {{ request()->is('bookings*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event-fill"></i> Peminjaman
                </a>
                <a href="/rooms" class="nav-item {{ request()->is('rooms*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Ruang Meeting
                </a>

                @if (Auth::user()->role == 1)
                    <div class="section-title">Pengaturan</div>

                    <a href="/settings/rooms" class="nav-item {{ request()->is('settings/rooms*') ? 'active' : '' }}">
                        <i class="bi bi-door-open-fill"></i> Ruangan
                    </a>
                    <a href="/settings/facilities" class="nav-item {{ request()->is('settings/facilities*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i> Fasilitas
                    </a>
                    <a href="/settings/users" class="nav-item {{ request()->is('settings/users*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Pengguna
                    </a>
                    <a href="/settings/positions" class="nav-item {{ request()->is('settings/positions*') ? 'active' : '' }}">
                        <i class="bi bi-briefcase-fill"></i> Bidang
                    </a>
                    <a href="/settings/divisions" class="nav-item {{ request()->is('settings/divisions*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-2-fill"></i> Divisi
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

    <!-- MOBILE BOTTOM NAV -->
    <nav class="mobile-bottom-nav">
        <a href="/dashboard" class="nav-btn {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <a href="/bookings" class="nav-btn {{ request()->is('bookings*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event-fill"></i>
            <span>Booking</span>
        </a>
        <a href="/rooms" class="nav-btn {{ request()->is('rooms*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span>Ruang</span>
        </a>
        <a href="{{ route('profile') }}" class="nav-btn {{ request()->is('profile*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span>Profil</span>
        </a>
    </nav>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) { updateNotificationUI(response); },
                error: function() {
                    $('#notificationList').html('<div class="text-center py-3 text-muted" style="font-size:var(--font-size-sm);"><i class="bi bi-exclamation-triangle"></i> Gagal memuat</div>');
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
                html += `
                    <a href="#" class="notif-item" data-id="${notif.id}" style="${bg}">
                        <div class="n-icon"><i class="bi bi-bell"></i></div>
                        <div class="n-content">
                            <div>${notif.message}</div>
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
                    markAsRead(id, this);
                });
            });
        }

        function markAsRead(id, element) {
            if (typeof $ === 'undefined') return;
            $.ajax({
                url: '{{ route("notifications.mark-read") }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { notification_id: id },
                success: function() { loadNotifications(); },
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
        
        setInterval(loadNotifications, 30000);

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
    
    @stack('scripts')
</body>
</html>