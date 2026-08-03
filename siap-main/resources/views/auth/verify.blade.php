@extends('layouts.auth')

@section('title', 'Verifikasi Email')

@section('content')
<div class="card auth-card">
    <div class="row g-0">
        <!-- Left Side - Branding -->
        <div class="col-md-6 d-none d-md-block">
            <div class="auth-left">
               
                <center>
                   <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 120px; height: 120px; margin-bottom: 15px;" />    
                </center>   

                <h2 class="auth-title">Verifikasi Email SIAP</h2>
                <p class="auth-subtitle">
                    Kami telah mengirimkan link verifikasi ke alamat email Anda. 
                    Silakan periksa inbox atau folder spam untuk melanjutkan proses verifikasi.
                </p>
            </div>
        </div>

        <!-- Right Side - Verify Form -->
        <div class="col-md-6">
            <div class="auth-right">
                <!-- Mobile Logo -->
                <div class="d-md-none text-center mb-4">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 100px; height: 100px; margin-bottom: 10px;" />
                </div>

                <h1 class="login-form-title">Verifikasi Email</h1>
                <p class="login-form-subtitle">Periksa email Anda untuk link verifikasi</p>

                <!-- Success Message -->
                @if (session('resent'))
                    <div class="alert alert-success border-0 rounded-3 mb-4">
                        <i class="bi bi-check-circle me-2"></i>
                        Link verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                @endif

                <!-- Information -->
                <div class="text-center mb-4">
                    <div class="verification-icon mb-3">
                        <i class="bi bi-envelope-check text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-muted">
                        Sebelum melanjutkan, silakan periksa email Anda untuk mendapatkan link verifikasi.
                    </p>
                </div>

                <!-- Resend Form -->
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="text-center mt-3">
                    <span class="text-muted">Kembali ke halaman </span>
                    <a class="btn-forgot" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
