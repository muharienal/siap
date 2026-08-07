@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<div class="card auth-card">
    <div class="row g-0">
        <!-- Left Side - Branding -->
        <div class="col-md-6 d-none d-md-block">
            <div class="auth-left">
                <div class="auth-logo">
                    <i class="bi bi-building-check"></i>
                    SIAP
                </div>
                <h2 class="auth-title">Bergabung dengan SIAP</h2>
                <p class="auth-subtitle">
                    Daftarkan akun Anda untuk mulai menggunakan Sistem Informasi Administrasi Perkantoran 
                    yang akan memudahkan pengelolaan ruangan dan fasilitas.
                </p>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="col-md-6">
            <div class="auth-right">
                <!-- Mobile Logo -->
                <div class="d-md-none text-center mb-4">
                    <h3 class="text-primary">
                        <i class="bi bi-building-check"></i>
                        SIAP
                    </h3>
                </div>

                <h1 class="login-form-title">Daftar Akun</h1>
                <p class="login-form-subtitle">Buat akun baru untuk mengakses SIAP</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="bi bi-person me-2"></i>Nama Lengkap
                        </label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                               placeholder="Masukkan nama lengkap Anda">
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-2"></i>Email Address
                        </label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" 
                               placeholder="Masukkan email Anda">
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-2"></i>Password
                        </label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="new-password" 
                               placeholder="Buat password yang kuat">
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="form-label">
                            <i class="bi bi-lock-fill me-2"></i>Konfirmasi Password
                        </label>
                        <input id="password-confirm" type="password" class="form-control" 
                               name="password_confirmation" required autocomplete="new-password" 
                               placeholder="Ulangi password Anda">
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-person-plus me-2"></i>
                        Daftar ke SIAP
                    </button>

                    <div class="text-center mt-3">
                        <span class="text-muted">Sudah punya akun? </span>
                        <a class="btn-forgot" href="{{ route('login') }}">
                            Masuk di sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
