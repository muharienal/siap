<?php

namespace App\Providers;

use App\Database\MySqlConnection;
use App\Models\Booking;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // FIX: RepositoryServiceProvider dihapus dari sini — provider itu
        // mem-bind ~20 interface repository (UserRepositoryInterface,
        // RoomRepositoryInterface, dll) ke class yang TIDAK PERNAH ada
        // filenya di app/Repositories/. Tidak bikin crash (binding di
        // Laravel malas/lazy), tapi murni dead weight yang di-load di
        // setiap request. Dicek dulu: tidak ada satu pun controller yang
        // type-hint interface-interface itu, jadi aman dihapus.

        // Register NotificationService
        $this->app->bind(\App\Services\NotificationService::class, function ($app) {
            return new \App\Services\NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FIX: pagination di seluruh aplikasi (Daftar Peminjaman, dll)
        // tampil rusak — panah Previous/Next raksasa tak berbentuk. Akar
        // masalahnya: Laravel 10 defaultnya merender view pagination
        // gaya TAILWIND (pakai SVG + class utility Tailwind seperti
        // "h-5 w-5" untuk ukuran ikon). Aplikasi ini 100% pakai Bootstrap
        // 5, TIDAK PERNAH memuat Tailwind CSS sama sekali — jadi class
        // ukuran itu tidak pernah berlaku, SVG-nya jatuh ke ukuran native
        // browser (raksasa). Diarahkan pakai view Bootstrap yang memang
        // sudah disediakan Laravel, sesuai framework CSS yang benar-benar
        // dipakai aplikasi ini.
        \Illuminate\Pagination\Paginator::useBootstrap();

        // Register custom MySQL connection to handle SSL issues in schema operations
        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new MySqlConnection($connection, $database, $prefix, $config);
        });

        // Badge angka "menunggu diproses" di menu sidebar Peminjaman Ruangan.
        // Admin: total booking pending yang perlu ditinjau (aksi mereka).
        // Karyawan/Sesdir: jumlah booking MEREKA SENDIRI yang masih pending
        // (belum diputuskan admin) — supaya tahu masih ada yang ditunggu.
        View::composer('templates.template', function ($view) {
            $count = 0;
            if (Auth::check()) {
                $user = Auth::user();
                $count = $user->role == 1
                    ? Booking::where('status', 0)->count()
                    : Booking::where('user_id', $user->id)->where('status', 0)->count();
            }
            $view->with('sidebarPendingCount', $count);
        });
    }
}