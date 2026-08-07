@extends('templates.template')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        
        <!-- Profile Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-user-circle me-2"></i>
                                Profile Pengguna
                            </h4>
                            <div>
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Profile Content -->
        <div class="row">
            <!-- Left Column - Profile Photo & Basic Info -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <!-- Profile Photo -->
                        <div class="profile-photo mb-4">
                            @if($employee && $employee->photo_path)
                                <img src="{{ asset('storage/' . $employee->photo_path) }}" 
                                     alt="Profile Photo" 
                                     class="rounded-circle profile-img"
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #e9ecef;">
                            @else
                                <div class="profile-placeholder rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                     style="width: 150px; height: 150px; background-color: #f8f9fa; border: 4px solid #e9ecef;">
                                    <i class="fas fa-user" style="font-size: 4rem; color: #adb5bd;"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Basic Info -->
                        <h4 class="mb-1">{{ $employee->full_name ?? $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $employee->position->name ?? 'Belum ada jabatan' }}</p>
                        <p class="text-muted small mb-3">{{ $employee->division->name ?? 'Belum ada divisi' }}</p>
                        
                        <!-- Status Badge -->
                        @if($employee && $employee->employment_status)
                            <span class="badge bg-{{ $employee->employment_status === 'active' ? 'success' : ($employee->employment_status === 'inactive' ? 'warning' : 'danger') }}">
                                {{ ucfirst($employee->employment_status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Statistik Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <div class="counter text-primary">{{ $user->bookings()->count() }}</div>
                                <small class="text-muted">Total Booking</small>
                            </div>
                            <div class="col-6">
                                <div class="counter text-success">{{ $user->bookings()->where('status', 1)->count() }}</div>
                                <small class="text-muted">Disetujui</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Detailed Information -->
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Personal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Nama Lengkap</label>
                                <p class="form-control-plaintext">{{ $employee->full_name ?? 'Belum diisi' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Email</label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">NIP</label>
                                <p class="form-control-plaintext">{{ $employee->nip ?? 'Belum diisi' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Jenis Kelamin</label>
                                <p class="form-control-plaintext">
                                    @if($employee && $employee->gender)
                                        {{ $employee->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                    @else
                                        Belum diisi
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Tanggal Lahir</label>
                                <p class="form-control-plaintext">
                                    {{ $employee && $employee->birth_date ? $employee->birth_date->format('d F Y') : 'Belum diisi' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">No. Telepon</label>
                                <p class="form-control-plaintext">{{ $employee->phone_number ?? 'Belum diisi' }}</p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold text-muted">Alamat</label>
                                <p class="form-control-plaintext">{{ $employee->address ?? 'Belum diisi' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-briefcase me-2"></i>
                            Informasi Pekerjaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Divisi</label>
                                <p class="form-control-plaintext">{{ $employee->division->name ?? 'Belum ditentukan' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Jabatan</label>
                                <p class="form-control-plaintext">{{ $employee->position->name ?? 'Belum ditentukan' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Status Karyawan</label>
                                <p class="form-control-plaintext">
                                    @if($employee && $employee->employment_status)
                                        <span class="badge bg-{{ $employee->employment_status === 'active' ? 'success' : ($employee->employment_status === 'inactive' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($employee->employment_status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Belum ditentukan</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Role</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->role == 0 ? 'danger' : 'primary' }}">
                                        {{ $user->role == 0 ? 'Administrator' : 'User' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-cog me-2"></i>
                            Informasi Akun
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Bergabung Sejak</label>
                                <p class="form-control-plaintext">{{ $user->created_at->format('d F Y') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Terakhir Diperbarui</label>
                                <p class="form-control-plaintext">{{ $user->updated_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>
                        
                       
                      

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.update.password') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="fas fa-key me-2"></i>
                        Ubah Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label">Password Lama</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('custom-css')
<style>
    .profile-img {
        transition: all 0.3s ease;
    }
    
    .profile-img:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .counter {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.2;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
        transition: all 0.15s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .form-control-plaintext {
        padding: 0.375rem 0;
        margin-bottom: 0;
        font-size: 0.875rem;
        line-height: 1.5;
        color: #495057;
        background-color: transparent;
        border: none;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .profile-placeholder {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>
@endpush