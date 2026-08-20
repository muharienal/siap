<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * FIX (bug keamanan kritis): sebelumnya SELURUH grup route /settings/*
 * (Manajemen Ruang Rapat, Fasilitas, Divisi, Bidang, Pengguna, Digital
 * Signage) cuma dilindungi middleware 'auth' (wajib login) TANPA
 * pengecekan role admin sama sekali — baik di level route maupun di
 * dalam masing-masing method controller-nya. Akibatnya siapa pun yang
 * login (termasuk Karyawan biasa) bisa langsung mengelola ruangan,
 * fasilitas, bahkan MEMBUAT/MENGHAPUS PENGGUNA LAIN, cukup dengan
 * mengetik URL-nya langsung — menu di sidebar cuma menyembunyikan
 * tautannya secara visual, bukan benar-benar membatasi akses.
 *
 * Middleware ini dibuat sesuai mekanisme RBAC yang SUNGGUHAN dipakai
 * aplikasi ini (kolom integer users.role, role 1 = Admin) — BUKAN
 * memakai middleware 'role' bawaan Spatie yang sebenarnya terdaftar
 * di Kernel, karena Spatie's role/permission system tidak pernah
 * benar-benar dipakai/diisi di aplikasi ini (kalau dipaksakan, middleware
 * itu justru akan selalu menolak SEMUA orang termasuk admin, karena
 * tidak ada user yang punya Spatie role).
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role != 1) {
            abort(403, 'Halaman ini hanya bisa diakses oleh Admin.');
        }

        return $next($request);
    }
}