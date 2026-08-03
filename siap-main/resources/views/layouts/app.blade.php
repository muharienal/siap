<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIAP - Sistem Administrasi Rapat')</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <!-- Global Design System (STATIC) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <aside class="col-auto sidebar" style="min-height: 100vh; width: 280px; position: sticky; top: 0; height: 100vh; overflow-y: auto;">
                <div class="d-flex align-items-center gap-3 mb-4 px-2">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP" style="width: 44px; height: 44px; object-fit: contain;">
                    <span style="font-weight: 800; font-size: 1.4rem; background: var(--brand-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        SIAP
                    </span>
                </div>

                <nav class="nav flex-column">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                    <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event-fill"></i> Peminjaman
                    </a>
                    <a href="{{ route('complaints.index') }}" class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-left-text-fill"></i> Complaints
                    </a>
                    <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Karyawan
                    </a>
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill"></i> Settings
                    </a>
                </nav>

                <div class="mt-auto pt-4 border-top border-light">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start" style="background: none; border: none; color: var(--ui-text-secondary);">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </button>
                    </form>
                    <div class="mt-2 px-2" style="font-size: 0.75rem; color: var(--ui-text-muted);">
                        {{ auth()->user()->name ?? 'User' }} · {{ auth()->user()->email ?? '' }}
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="col p-4" style="background: var(--ui-bg); min-height: 100vh;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0" style="color: var(--ui-text-primary);">
                            @yield('page_title', 'Dashboard')
                        </h4>
                        <small class="text-muted">@yield('breadcrumb', 'Kelola seluruh aktivitas rapat Anda di sini.')</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-success" style="padding: 0.5rem 1rem;">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Online
                        </span>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=f97316&color=fff&bold=true&size=40" 
                             alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--brand-orange);">
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show animate-fade-in-up" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show animate-fade-in-up" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>