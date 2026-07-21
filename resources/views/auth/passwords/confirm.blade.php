@extends('layouts.auth')

@section('title', 'Konfirmasi Password')

@section('content')
<div class="card auth-card">
    <div class="row g-0">
        <!-- Left Side - Branding -->
        <div class="col-md-6 d-none d-md-block">
            <div class="auth-left">
               
                <center>
                   <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 120px; height: 120px; margin-bottom: 15px;" />    
                </center>   

                <h2 class="auth-title">Konfirmasi Akses SIAP</h2>
                <p class="auth-subtitle">
                    Untuk keamanan akun Anda, silakan konfirmasi password sebelum melanjutkan 
                    ke area yang memerlukan verifikasi tambahan.
                </p>
            </div>
        </div>

        <!-- Right Side - Confirm Password Form -->
        <div class="col-md-6">
            <div class="auth-right">
                <!-- Mobile Logo -->
                <div class="d-md-none text-center mb-4">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 100px; height: 100px; margin-bottom: 10px;" />
                </div>

                <h1 class="login-form-title">Konfirmasi Password</h1>
                <p class="login-form-subtitle">Silakan konfirmasi password Anda untuk melanjutkan</p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-2"></i>Password
                        </label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password" 
                               placeholder="Masukkan password Anda">
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-check-circle me-2"></i>
                        Konfirmasi Password
                    </button>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <span class="text-muted">Lupa password? </span>
                            <a class="btn-forgot" href="{{ route('password.request') }}">
                                <i class="bi bi-key me-1"></i>
                                Reset Password
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
