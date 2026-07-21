@extends('layouts.auth')

@section('title', 'Error Absensi')

@section('content')
<div class="card auth-card">
    <div class="row g-0">
        <!-- Left Side - Error Info -->
        <div class="col-md-6 d-none d-md-block">
            <div class="auth-left">
                <center>
                   <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 120px; height: 120px; margin-bottom: 15px;" />    
                </center>   

                <h2 class="auth-title">Oops! Ada Masalah</h2>
                <p class="auth-subtitle">
                    Terjadi kendala saat memproses absensi Anda. Silakan hubungi penyelenggara jika masalah ini terus berlanjut.
                </p>
            </div>
        </div>

        <!-- Right Side - Error Message -->
        <div class="col-md-6">
            <div class="auth-right">
                <!-- Mobile Logo -->
                <div class="d-md-none text-center mb-4">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 100px; height: 100px; margin-bottom: 10px;" />
                </div>

                <div class="text-center">
                    <div class="error-icon mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="login-form-title text-warning">Absensi Tidak Dapat Diproses</h1>
                    <div class="alert alert-warning mt-3">
                        {{ $message ?? 'Terjadi kesalahan yang tidak diketahui.' }}
                    </div>

                    @if(isset($booking))
                        <div class="meeting-summary mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-3">Detail Meeting</h6>
                            <div class="text-start">
                                <div class="mb-2">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <strong>Ruangan:</strong> {{ $booking->room->name }}
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-calendar me-2"></i>
                                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->starttime)->format('d M Y') }}
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-clock me-2"></i>
                                    <strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->starttime)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->endtime)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-login">
                            <i class="bi bi-house me-2"></i>
                            Kembali ke Beranda
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection