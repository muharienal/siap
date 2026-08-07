<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP">
        <span>SIAP</span>
    </div>

    <nav style="flex:1;">
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="/rooms" class="nav-item {{ request()->is('rooms*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Ruang Meeting
        </a>
        <a href="/bookings" class="nav-item {{ request()->is('bookings*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event-fill"></i> Peminjaman
        </a>


        @if (Auth::user()->role == 1)
            <div class="section-title">Pengaturan</div>

            <a href="/settings/rooms" class="nav-item {{ request()->is('settings/rooms*') ? 'active' : '' }}">
                <i class="bi bi-door-open-fill"></i> Ruangan
            </a>
            <a href="/settings/facilities" class="nav-item {{ request()->is('settings/facilities*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Fasilitas
            </a>
            <a href="/settings/divisions" class="nav-item {{ request()->is('settings/divisions*') ? 'active' : '' }}">
                <i class="bi bi-diagram-2-fill"></i> Divisi
            </a>
            <a href="/settings/positions" class="nav-item {{ request()->is('settings/positions*') ? 'active' : '' }}">
                <i class="bi bi-briefcase-fill"></i> Bidang
            </a>
            <a href="/settings/users" class="nav-item {{ request()->is('settings/users*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Pengguna
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <img class="avatar-sm" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name ?? Auth::user()->name ?? 'User') }}&background=f97316&color=fff&bold=true&size=34" alt="Avatar">
        <div class="user-info">
            <div class="name">{{ Auth::user()->full_name ?? Auth::user()->name ?? 'User' }}</div>
            <div class="role">{{ Auth::user()->role == 1 ? 'Admin' : 'Karyawan' }}</div>
        </div>
        <span class="status-dot"></span>
    </div>
</aside>