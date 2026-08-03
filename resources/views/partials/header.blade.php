@php
    $displayName = Auth::user()->full_name ?? Auth::user()->name ?? 'User';
@endphp

<header class="app-header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="page-title" style="font-size: var(--font-size-lg);">
            <span style="font-weight: 400;">Selamat datang,</span>
            <span style="font-weight: 600;">{{ $displayName }}</span>
        </div>
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
                    &middot;
                    <a href="{{ route('notifications.index') }}" style="font-weight:500; color:var(--text-secondary); text-decoration:none; font-size:var(--font-size-xs);">
                        Lihat semua
                    </a>
                </div>
            </div>
        </div>

        <div class="dropdown">
            <a href="#" class="user-avatar" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=f97316&color=fff&bold=true&size=32" alt="Avatar">
                <span class="user-name">
                    {{ $displayName }}
                    <span class="role-badge">{{ Auth::user()->role == 1 ? 'Admin' : 'Karyawan' }}</span>
                    <span class="chevron"><i class="bi bi-chevron-down"></i></span>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="border:none; border-radius:var(--radius-card); box-shadow:var(--shadow-dropdown); padding:var(--space-2); min-width:180px; border:1px solid var(--border-color-light);">
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