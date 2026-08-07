@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="card auth-card">
    <div class="row g-0">
        <!-- Left Side - Branding -->
        <div class="col-md-6 d-none d-md-block">
            <div class="auth-left"> 
                <h2 class="auth-title">Reset Password</h2>
                <p class="auth-subtitle">
                    Jangan khawatir jika Anda lupa password. Masukkan email Anda dan kami akan mengirimkan 
                    link untuk reset password ke email tersebut.
                </p>
            </div>
        </div>

        <!-- Right Side - Reset Form -->
        <div class="col-md-6">
            <div class="auth-right"> 

                 <!-- Mobile Logo -->
                <div class="d-md-none text-center mb-4">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 100px; height: 100px; margin-bottom: 10px;" />
                </div>

                <h1 class="login-form-title">Lupa Password?</h1>
                <p class="login-form-subtitle">Masukkan email untuk reset password</p>

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('status') }} 
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-2"></i>Email Address
                        </label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                               placeholder="Masukkan email Anda">
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-envelope-check me-2"></i>
                        Kirim Link Reset Password
                    </button>

                    <div class="text-center mt-3">
                        <a class="btn-forgot" href="{{ route('login') }}">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
