@extends('templates.template')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                Edit Profile
                            </h4>
                            <div>
                                <a href="{{ route('profile') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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

        <!-- Edit Form -->
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Left Column - Photo Upload -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-camera me-2"></i>
                                Foto Profile
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <!-- Current Photo Preview -->
                            <div class="photo-preview mb-3">
                                @if($employee && $employee->photo_path)
                                    <img src="{{ asset('storage/' . $employee->photo_path) }}" 
                                         alt="Current Photo" 
                                         id="currentPhoto"
                                         class="rounded-circle img-thumbnail"
                                         style="width: 200px; height: 200px; object-fit: cover;">
                                @else
                                    <div id="currentPhoto" class="rounded-circle img-thumbnail mx-auto d-flex align-items-center justify-content-center"
                                         style="width: 200px; height: 200px; background-color: #f8f9fa;">
                                        <i class="fas fa-user" style="font-size: 5rem; color: #adb5bd;"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Photo Upload -->
                            <div class="form-group mb-3">
                                <input type="file" 
                                       class="form-control @error('photo') is-invalid @enderror" 
                                       id="photo" 
                                       name="photo" 
                                       accept="image/*"
                                       onchange="previewImage(event)">
                                <small class="text-muted">Format: JPG, PNG, GIF (Max: 2MB)</small>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remove Photo Button -->
                            @if($employee && $employee->photo_path)
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removePhoto()">
                                    <i class="fas fa-trash me-1"></i>
                                    Hapus Foto
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Form Fields -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>
                                Informasi Dasar
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('full_name') is-invalid @enderror" 
                                           id="full_name" 
                                           name="full_name" 
                                           value="{{ old('full_name', $employee->full_name ?? '') }}" 
                                           required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nip" class="form-label">NIP</label>
                                    <input type="text" 
                                           class="form-control @error('nip') is-invalid @enderror" 
                                           id="nip" 
                                           name="nip" 
                                           value="{{ old('nip', $employee->nip ?? '') }}">
                                    @error('nip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control @error('gender') is-invalid @enderror" 
                                            id="gender" 
                                            name="gender" 
                                            required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('gender', $employee->gender ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('gender', $employee->gender ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                    <input type="date" 
                                           class="form-control @error('birth_date') is-invalid @enderror" 
                                           id="birth_date" 
                                           name="birth_date" 
                                           value="{{ old('birth_date', $employee && $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">No. Telepon</label>
                                    <input type="text" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="{{ old('phone_number', $employee->phone_number ?? '') }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" 
                                              name="address" 
                                              rows="3">{{ old('address', $employee->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <label for="division_id" class="form-label">Divisi</label>
                                    <select class="form-control @error('division_id') is-invalid @enderror" 
                                            id="division_id" 
                                            name="division_id">
                                        <option value="">Pilih Divisi</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" 
                                                {{ old('division_id', $employee->division_id ?? '') == $division->id ? 'selected' : '' }}>
                                                {{ $division->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('division_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="position_id" class="form-label">Jabatan</label>
                                    <select class="form-control @error('position_id') is-invalid @enderror" 
                                            id="position_id" 
                                            name="position_id">
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}" 
                                                {{ old('position_id', $employee->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="employment_status" class="form-label">Status Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('employment_status') is-invalid @enderror" 
                                            id="employment_status" 
                                            name="employment_status" 
                                            required>
                                        <option value="">Pilih Status</option>
                                        <option value="active" {{ old('employment_status', $employee->employment_status ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('employment_status', $employee->employment_status ?? '') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                        <option value="terminated" {{ old('employment_status', $employee->employment_status ?? '') === 'terminated' ? 'selected' : '' }}>Berhenti</option>
                                    </select>
                                    @error('employment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('profile') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('custom-css')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .img-thumbnail {
        border: 3px solid #dee2e6;
    }
    
    .photo-preview {
        position: relative;
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .gap-2 {
        gap: 0.5rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Preview uploaded image
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('currentPhoto');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '';
                preview.style.backgroundImage = 'none';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'rounded-circle img-thumbnail';
                img.style.width = '200px';
                img.style.height = '200px';
                img.style.objectFit = 'cover';
                
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Remove photo function
    function removePhoto() {
        if (confirm('Apakah Anda yakin ingin menghapus foto profile?')) {
            // Create a form and submit to remove photo endpoint
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("profile.remove.photo") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>
@endpush