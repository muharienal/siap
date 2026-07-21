@extends('layouts.auth')

@section('title', 'Absensi Meeting')

@section('content')
<div class="card auth-card">
    <div class="row g-0">
        <!-- Left Side - Meeting Info -->
        <div class="col-md-6 d-none d-md-block">
            <div class="auth-left">
                <center>
                   <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 120px; height: 120px; margin-bottom: 15px;" />    
                </center>   

                <h2 class="auth-title">Absensi Meeting</h2>
                <div class="meeting-info">
                    <div class="mb-3">
                        <strong>Ruangan:</strong> {{ $booking->room->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}
                    </div>
                    <div class="mb-3">
                        <strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                    </div>
                    <div class="mb-3">
                        <strong>Keperluan:</strong> {{ $booking->purpose }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Attendance Form -->
        <div class="col-md-6">
            <div class="auth-right">
                <!-- Mobile Logo -->
                <div class="d-md-none text-center mb-4">
                    <img src="{{ asset('assets/images/icon/icon.png') }}" alt="SIAP Logo" style="width: 100px; height: 100px; margin-bottom: 10px;" />
                </div>

                <h1 class="login-form-title">Form Absensi</h1>
                <p class="login-form-subtitle">Silakan isi data Anda untuk absensi</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('attendance.submit', $booking->absent_code) }}">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="bi bi-person me-2"></i>Nama Lengkap
                        </label>
                        <input id="name" type="text" class="form-control" 
                               name="name" value="{{ old('name') }}" required 
                               placeholder="Masukkan nama lengkap Anda">
                    </div> 

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-check-circle me-2"></i>
                        Konfirmasi Absensi
                    </button>

                    <div class="text-center mt-3">
                        <span class="text-muted">Sudah punya akun? </span>
                        <a href="{{ route('login') }}" class="btn-forgot">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Login di sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection