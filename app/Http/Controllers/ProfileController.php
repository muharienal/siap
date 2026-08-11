<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Halaman Profil Saya — read-only (NIK, Nama, Divisi, Bidang, Role, dikelola
     * admin lewat Manajemen Pengguna) + form ubah password sendiri.
     *
     * FIX: sebelumnya halaman ini punya form edit lengkap yang mengarah ke
     * kolom `email`/tabel `employees` yang sudah tidak ada di database —
     * setiap disimpan pasti error. Data identitas pengguna sekarang memang
     * hanya bisa diubah admin (konsisten dengan Manajemen Pengguna), jadi
     * halaman ini disederhanakan jadi tampilan + ganti password saja.
     */
    public function index()
    {
        $user = Auth::user()->load(['division', 'position']);

        return view('profile.index', compact('user'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Password lama tidak benar!');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui!');
    }
}