@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="card auth-card">
    <div class="row g-0">
        <!-- Left Side - Branding -->
        <div class="col-md-6 d-none d-md-block">
            <div class="auth-left">
               
                <center>
                   <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 120px; height: 120px; margin-bottom: 15px;" />    
                </center>   

                <h2 class="auth-title">Reset Password SIAP</h2>
                <p class="auth-subtitle">
                    Buat password baru untuk mengakses kembali Sistem Informasi Alokasi Penggunaan. 
                    Pastikan password yang Anda buat aman dan mudah diingat.
                </p>
            </div>
        </div>

        <!-- Right Side - Reset Password Form -->
        <div class="col-md-6">
            <div class="auth-right">
                <!-- Mobile Logo -->
                <div class="d-md-none text-center mb-4">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 100px; height: 100px; margin-bottom: 10px;" />
                </div>

                <h1 class="login-form-title">Reset Password</h1>
                <p class="login-form-subtitle">Masukkan email dan password baru Anda</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-2"></i>Email Address
                        </label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus 
                               placeholder="Masukkan alamat email">
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-2"></i>Password Baru
                        </label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="new-password" 
                               placeholder="Masukkan password baru">
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
                               placeholder="Konfirmasi password baru">
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-key me-2"></i>
                        Reset Password
                    </button>

                    <div class="text-center mt-3">
                        <span class="text-muted">Sudah ingat password? </span>
                        <a class="btn-forgot" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
