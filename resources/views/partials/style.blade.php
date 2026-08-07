<!-- Bootstrap 5 + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Google Font: Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

<style>
    /* ============================================================
       SIAP DESIGN SYSTEM – Single Source of Truth
       Background #F5F7FA, shadow lembut
       ============================================================ */
    :root {
        /* Brand Colors */
        --brand-orange: #f97316;
        --brand-orange-dark: #ea580c;
        --brand-green: #10b981;
        --brand-green-dark: #059669;
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-gradient: linear-gradient(145deg, var(--brand-orange) 0%, var(--brand-green) 100%);
        --brand-gradient-hover: linear-gradient(145deg, var(--brand-orange-dark) 0%, var(--brand-green-dark) 100%);

        /* Surfaces */
        --bg-body: #f5f7fa;
        --bg-card: #ffffff;
        --bg-hover: #f1f4f9;
        --bg-input: #f1f4f9;
        --border-color: #e4e7ec;
        --border-color-light: #edf0f4;

        /* Text */
        --text-primary: #0b1a33;
        --text-secondary: #475569;
        --text-muted: #94a3b8;
        --text-inverse: #ffffff;

        /* Shadows */
        --shadow-card: 0 8px 24px rgba(15, 23, 42, 0.06);
        --shadow-hover: 0 12px 32px rgba(15, 23, 42, 0.08);
        --shadow-dropdown: 0 16px 48px rgba(15, 23, 42, 0.10);

        /* Border Radius */
        --radius-card: 1.25rem;
        --radius-sm: 0.75rem;
        --radius-xs: 0.5rem;
        --radius-pill: 9999px;

        /* Spacing */
        --space-1: 0.25rem;
        --space-2: 0.5rem;
        --space-3: 0.75rem;
        --space-4: 1rem;
        --space-5: 1.25rem;
        --space-6: 1.5rem;
        --space-7: 2rem;
        --space-8: 2.5rem;
        --space-9: 3rem;

        /* Typography */
        --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-size-xs: 0.75rem;
        --font-size-sm: 0.875rem;
        --font-size-base: 1rem;
        --font-size-md: 1.125rem;
        --font-size-lg: 1.25rem;
        --font-size-xl: 1.5rem;
        --font-size-2xl: 1.75rem;
        --font-size-3xl: 2.25rem;
        --font-size-4xl: 2.75rem;

        /* Transitions */
        --transition-fast: 0.15s ease;
        --transition-base: 0.2s ease;
        --transition-slow: 0.3s ease;

        /* Status Colors */
        --status-available: #10b981;
        --status-booked: #3b82f6;
        --status-pending: #f59e0b;
        --status-rejected: #ef4444;
        --status-current: #f97316;
    }

    /* ============================================================
       GLOBAL
       ============================================================ */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { font-size: 100%; }
    body {
        font-family: var(--font-family);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: var(--font-size-base);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        overflow-x: hidden;
    }

    /* ============================================================
       APP LAYOUT
       ============================================================ */
    .app-wrapper { display: flex; min-height: 100vh; }
    .app-content { flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

    /* ============================================================
       SIDEBAR
       ============================================================ */
    .app-sidebar {
        width: 220px;
        min-height: 100vh;
        background: var(--bg-card);
        border-right: 1px solid var(--border-color-light);
        flex-shrink: 0;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        padding: var(--space-4) var(--space-3);
        transition: transform var(--transition-slow), width var(--transition-slow);
        z-index: 100;
        display: flex;
        flex-direction: column;
    }
    .app-sidebar .sidebar-brand {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        padding: 0 var(--space-2) var(--space-4) var(--space-2);
        border-bottom: 1px solid var(--border-color-light);
        margin-bottom: var(--space-3);
    }
    .app-sidebar .sidebar-brand img { width: 34px; height: 34px; object-fit: contain; }
    .app-sidebar .sidebar-brand span {
        font-weight: 800;
        font-size: var(--font-size-lg);
        background: var(--brand-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.3px;
    }
    .app-sidebar .nav-item {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-2) var(--space-3);
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
    }
    .app-sidebar .nav-item i {
        font-size: 1.25rem;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
        color: var(--text-muted);
        transition: color var(--transition-fast);
    }
    .app-sidebar .nav-item:hover {
        background: var(--bg-hover);
        color: var(--brand-orange-dark);
        transform: translateX(2px);
    }
    .app-sidebar .nav-item:hover i { color: var(--brand-orange); }
    .app-sidebar .nav-item.active {
        background: rgba(249, 115, 22, 0.06);
        color: var(--brand-orange-dark);
    }
    .app-sidebar .nav-item.active i { color: var(--brand-orange); }
    .app-sidebar .nav-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        height: 60%;
        width: 3px;
        background: var(--brand-gradient);
        border-radius: 0 var(--radius-xs) var(--radius-xs) 0;
    }
    .app-sidebar .section-title {
        font-size: var(--font-size-xs);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        padding: var(--space-3) var(--space-3) var(--space-1);
        margin-top: var(--space-2);
    }
    .app-sidebar .sidebar-footer {
        margin-top: auto;
        padding-top: var(--space-3);
        border-top: 1px solid var(--border-color-light);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    .app-sidebar .sidebar-footer .avatar-sm {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color-light);
        flex-shrink: 0;
    }
    .app-sidebar .sidebar-footer .user-info { flex: 1; min-width: 0; }
    .app-sidebar .sidebar-footer .user-info .name {
        font-weight: 600;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .app-sidebar .sidebar-footer .user-info .role {
        font-size: var(--font-size-xs);
        color: var(--text-muted);
    }
    .app-sidebar .sidebar-footer .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand-green);
        display: inline-block;
        flex-shrink: 0;
        animation: pulse-dot 2s ease-in-out infinite;
    }

    /* ============================================================
       HEADER
       ============================================================ */
    .app-header {
        background: var(--bg-card);
        border-bottom: 1px solid var(--border-color-light);
        padding: 0 var(--space-6);
        height: 76px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        position: sticky;
        top: 0;
        z-index: 50;
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.88);
    }
    .app-header .header-left {
        display: flex;
        align-items: center;
        gap: var(--space-3);
    }
    .app-header .header-left .sidebar-toggle {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: var(--text-secondary);
        padding: var(--space-1);
        cursor: pointer;
        display: none;
        border-radius: var(--radius-xs);
        transition: background var(--transition-fast);
    }
    .app-header .header-left .sidebar-toggle:hover { background: var(--bg-hover); }
    .app-header .header-left .page-title {
        font-size: var(--font-size-2xl);
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.02em;
    }
    .app-header .header-right {
        display: flex;
        align-items: center;
        gap: var(--space-3);
    }
    .app-header .header-right .search-box {
        display: flex;
        align-items: center;
        background: var(--bg-input);
        border-radius: var(--radius-pill);
        padding: 0 var(--space-2) 0 var(--space-4);
        border: 1px solid transparent;
        transition: all var(--transition-fast);
        height: 42px;
        min-width: 360px;
    }
    .app-header .header-right .search-box:focus-within {
        border-color: var(--brand-orange);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.06);
        min-width: 480px;
    }
    .app-header .header-right .search-box input {
        border: none;
        background: transparent;
        outline: none;
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        padding: var(--space-1) 0;
        width: 100%;
    }
    .app-header .header-right .search-box input::placeholder {
        color: var(--text-muted);
        font-weight: 400;
    }
    .app-header .header-right .search-box .search-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        padding: var(--space-1);
        cursor: pointer;
        font-size: 1rem;
        transition: color var(--transition-fast);
    }
    .app-header .header-right .search-box .search-btn:hover { color: var(--brand-orange); }
    .app-header .header-right .icon-btn {
        background: none;
        border: none;
        font-size: 1.1rem;
        color: var(--text-secondary);
        padding: var(--space-1);
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-fast);
        position: relative;
        cursor: pointer;
    }
    .app-header .header-right .icon-btn:hover {
        background: var(--bg-hover);
        color: var(--brand-orange);
    }
    .app-header .header-right .icon-btn .badge-dot {
        position: absolute;
        top: -2px;
        right: -2px;
        min-width: 17px;
        height: 17px;
        padding: 0 4px;
        background: #ef4444;
        color: #fff;
        border-radius: 999px;
        border: 2px solid var(--bg-card);
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .app-header .header-right .user-avatar {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        cursor: pointer;
        padding: var(--space-1) var(--space-2) var(--space-1) var(--space-1);
        border-radius: var(--radius-pill);
        transition: background var(--transition-fast);
        text-decoration: none;
        color: var(--text-primary);
        height: 42px;
    }
    .app-header .header-right .user-avatar:hover { background: var(--bg-hover); }
    .app-header .header-right .user-avatar img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color-light);
        flex-shrink: 0;
    }
    .app-header .header-right .user-avatar .user-name {
        font-size: var(--font-size-sm);
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.2;
    }
    .app-header .header-right .user-avatar .user-name .role-badge {
        font-weight: 400;
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        margin-left: var(--space-1);
    }
    .app-header .header-right .user-avatar .chevron {
        font-size: 0.6rem;
        color: var(--text-muted);
        margin-left: var(--space-1);
    }

    /* ============================================================
       DROPDOWN NOTIFIKASI
       ============================================================ */
    .dropdown-notif {
        min-width: 340px;
        padding: 0;
        border: none;
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-dropdown);
        overflow: hidden;
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
    }
    .dropdown-notif .notif-header {
        padding: var(--space-2) var(--space-4);
        border-bottom: 1px solid var(--border-color-light);
        font-weight: 700;
        font-size: var(--font-size-sm);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dropdown-notif .notif-body {
        max-height: 300px;
        overflow-y: auto;
        padding: 0;
    }
    .dropdown-notif .notif-body .notif-item {
        padding: var(--space-2) var(--space-4);
        display: flex;
        align-items: flex-start;
        gap: var(--space-2);
        transition: background var(--transition-fast);
        border-bottom: 1px solid var(--border-color-light);
        cursor: pointer;
        text-decoration: none;
        color: var(--text-primary);
    }
    .dropdown-notif .notif-body .notif-item:last-child { border-bottom: none; }
    .dropdown-notif .notif-body .notif-item:hover { background: var(--bg-hover); }
    .dropdown-notif .notif-body .notif-item .n-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(249,115,22,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--brand-orange);
        flex-shrink: 0;
        font-size: 0.8rem;
    }
    .dropdown-notif .notif-body .notif-item .n-content {
        flex: 1;
        font-size: var(--font-size-sm);
    }
    .dropdown-notif .notif-body .notif-item .n-content .n-time {
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        margin-top: var(--space-1);
    }
    .dropdown-notif .notif-footer {
        padding: var(--space-2) var(--space-4);
        border-top: 1px solid var(--border-color-light);
        text-align: center;
        font-size: var(--font-size-sm);
    }

    /* ============================================================
       TOAST NOTIFIKASI (muncul otomatis saat ada notif baru)
       ============================================================ */
    .toast-container {
        position: fixed;
        top: var(--space-5);
        right: var(--space-5);
        z-index: 1080;
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
        width: 340px;
        max-width: calc(100vw - 2 * var(--space-4));
    }
    .app-toast {
        background: var(--bg-card);
        border: 1px solid var(--border-color-light);
        border-left: 4px solid var(--brand-orange);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-dropdown);
        padding: var(--space-3) var(--space-4);
        display: flex;
        gap: var(--space-2);
        align-items: flex-start;
        cursor: pointer;
        text-decoration: none;
        color: var(--text-primary);
        animation: toastIn 0.3s ease;
        position: relative;
    }
    .app-toast:hover { background: var(--bg-hover); }
    .app-toast.hiding { animation: toastOut 0.25s ease forwards; }
    .app-toast .t-icon {
        width: 32px; height: 32px; border-radius: 50%;
        background: rgba(249,115,22,0.10); color: var(--brand-orange);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 0.95rem;
    }
    .app-toast .t-content { flex: 1; min-width: 0; }
    .app-toast .t-title { font-weight: 700; font-size: var(--font-size-sm); margin-bottom: 2px; }
    .app-toast .t-message {
        font-size: var(--font-size-xs); color: var(--text-secondary);
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .app-toast .t-close {
        border: none; background: transparent; color: var(--text-muted);
        font-size: 0.85rem; flex-shrink: 0; cursor: pointer; padding: 2px;
        line-height: 1;
    }
    .app-toast .t-close:hover { color: var(--text-primary); }
    @keyframes toastIn {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes toastOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(30px); }
    }
    @media (max-width: 575.98px) {
        .toast-container { top: var(--space-3); right: var(--space-3); left: var(--space-3); width: auto; }
    }

    /* ============================================================
       FOOTER
       ============================================================ */
    .app-footer {
        background: var(--bg-card);
        border-top: 1px solid var(--border-color-light);
        padding: var(--space-2) var(--space-6);
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: var(--font-size-xs);
        color: var(--text-muted);
        height: 44px;
    }
    .app-footer a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color var(--transition-fast);
    }
    .app-footer a:hover { color: var(--brand-orange-dark); }
    .app-footer .footer-version {
        font-weight: 500;
        color: var(--text-muted);
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 1199.98px) {
        .app-header .header-right .search-box { min-width: 260px; }
        .app-header .header-right .search-box:focus-within { min-width: 340px; }
    }
    @media (max-width: 991.98px) {
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            transform: translateX(-100%);
            width: 280px;
            z-index: 1050;
            box-shadow: var(--shadow-dropdown);
            border-right: none;
        }
        .app-sidebar.open { transform: translateX(0); }
        .app-header .header-left .sidebar-toggle { display: block; }
        .app-header .header-right .search-box { min-width: 180px; }
        .app-header .header-right .search-box:focus-within { min-width: 240px; }
        .app-header .header-right .user-avatar .user-name .role-badge { display: none; }
        .dropdown-notif { min-width: 280px; right: -60px !important; }
        .app-footer { padding: var(--space-2) var(--space-4); flex-direction: column; height: auto; gap: var(--space-1); text-align: center; }
    }
    @media (max-width: 575.98px) {
        .app-header { padding: 0 var(--space-3); height: 60px; }
        .app-header .header-left .page-title { font-size: var(--font-size-xl); }
        .app-header .header-right .search-box { display: none; }
        .app-header .header-right .user-avatar .user-name { display: none; }
        .dropdown-notif { min-width: 260px; right: -20px !important; }
        .app-footer { padding: var(--space-1) var(--space-3); font-size: var(--font-size-xs); }
    }

    /* ============================================================
       BOOTSTRAP OVERRIDE
       ============================================================ */
    .btn {
        border-radius: var(--radius-sm);
        font-weight: 600;
        padding: var(--space-2) var(--space-4);
        transition: all var(--transition-fast);
        border: none;
        font-size: var(--font-size-sm);
        line-height: 1.4;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-1);
    }
    .btn-primary {
        background: var(--brand-gradient);
        color: var(--text-inverse);
        box-shadow: 0 4px 12px rgba(249,115,22,0.12);
    }
    .btn-primary:hover {
        background: var(--brand-gradient-hover);
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(16,185,129,0.18);
        color: var(--text-inverse);
    }
    .btn-outline-secondary {
        border: 1px solid var(--border-color-light);
        color: var(--text-secondary);
        background: transparent;
    }
    .btn-outline-secondary:hover {
        background: var(--bg-hover);
        border-color: var(--text-muted);
        color: var(--text-primary);
    }
    .btn-sm { height: 34px; padding: var(--space-1) var(--space-3); font-size: var(--font-size-xs); }

    .form-control, .form-select {
        height: 42px;
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
        box-shadow: 0 0 0 3px rgba(249,115,22,0.06);
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

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: var(--bg-body); }
    ::-webkit-scrollbar-thumb { background: var(--text-muted); border-radius: var(--radius-pill); }
    ::-webkit-scrollbar-thumb:hover { background: var(--text-secondary); }

    /* Tooltip */
    .tooltip .tooltip-inner {
        background: var(--text-primary);
        color: var(--text-inverse);
        font-size: var(--font-size-xs);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-xs);
        font-weight: 500;
    }
    .bs-tooltip-top .tooltip-arrow::before { border-top-color: var(--text-primary); }

    /* Focus ring */
    :focus-visible {
        outline: 2px solid var(--brand-orange);
        outline-offset: 2px;
    }

    /* Animations */
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.8); }
    }
</style>