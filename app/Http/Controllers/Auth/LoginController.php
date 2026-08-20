<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'nip';
    }

    /**
     * FIX: browser (terutama lewat tombol Back atau saat tab lama dibuka
     * ulang) kadang menyajikan halaman login dari cache/bfcache alih-alih
     * memuat ulang dari server — halaman versi cache itu membawa token
     * CSRF LAMA yang sudah tidak cocok dengan sesi saat ini, jadi begitu
     * disubmit langsung gagal (token mismatch / "419"). Header di bawah
     * ini memberi tahu browser untuk selalu memuat ulang halaman login
     * dari server, bukan dari cache, supaya token yang dipakai selalu
     * yang terbaru.
     */
    public function showLoginForm()
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        return Auth::attempt(['nip' => $credentials['nip'], 'password' => $credentials['password']], $request->filled('remember'));
    }

    protected function credentials(Request $request)
    {
        return $request->only('nip', 'password');
    }
}