<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP">
        <span>SIAP</span>
    </div>

    <nav style="flex:1;">
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="/bookings" class="nav-item {{ request()->is('bookings*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event-fill"></i> Peminjaman
        </a>
        <a href="/rooms" class="nav-item {{ request()->is('rooms*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Ruang Meeting
        </a>
        <!-- <a href="/complaints" class="nav-item {{ request()->is('complaints*') ? 'active' : '' }}">
            <i class="bi bi-chat-left-text-fill"></i> Complaint
        </a> -->

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

<div class="sidebar-footer" style="padding:16px; text-align:left; border-top:1px solid #f1f5f9;">
    <div style="font-size:12px; color:#9ca3af; line-height:1.6;">
        &copy; 2026<br>
        <span style="font-weight:600; color:#6b7280;">IT PT Petrokopindo Cipta Selaras</span>
    </div>
</div>
</aside>