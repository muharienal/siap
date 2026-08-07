@extends('templates.template')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Karyawan</h4>
                        </div>
                        <div>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Display validation errors -->
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="full_name">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="full_name" 
                                               id="full_name" 
                                               class="form-control @error('full_name') is-invalid @enderror" 
                                               value="{{ old('full_name', $employee->full_name) }}" 
                                               placeholder="Masukkan nama lengkap"
                                               required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="nip">NIP <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="nip" 
                                               id="nip" 
                                               class="form-control @error('nip') is-invalid @enderror" 
                                               value="{{ old('nip', $employee->nip) }}" 
                                               placeholder="Masukkan NIP"
                                               required>
                                        @error('nip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="division_id">Divisi <span class="text-danger">*</span></label>
                                        <select name="division_id" id="division_id" class="form-control @error('division_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Divisi --</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}" {{ (old('division_id', $employee->division_id) == $division->id) ? 'selected' : '' }}>
                                                    {{ $division->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('division_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="position_id">Jabatan <span class="text-danger">*</span></label>
                                        <select name="position_id" id="position_id" class="form-control @error('position_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                            @foreach($positions as $position)
                                                <option value="{{ $position->id }}" {{ (old('position_id', $employee->position_id) == $position->id) ? 'selected' : '' }}>
                                                    {{ $position->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('position_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="employment_status">Status Kepegawaian <span class="text-danger">*</span></label>
                                        <select name="employment_status" id="employment_status" class="form-control @error('employment_status') is-invalid @enderror" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="active" {{ (old('employment_status', $employee->employment_status) == 'active') ? 'selected' : '' }}>Aktif</option>
                                            <option value="inactive" {{ (old('employment_status', $employee->employment_status) == 'inactive') ? 'selected' : '' }}>Tidak Aktif</option>
                                            <option value="terminated" {{ (old('employment_status', $employee->employment_status) == 'terminated') ? 'selected' : '' }}>Diberhentikan</option>
                                        </select>
                                        @error('employment_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="L" {{ (old('gender', $employee->gender) == 'L') ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ (old('gender', $employee->gender) == 'P') ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="birth_date">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               name="birth_date" 
                                               id="birth_date" 
                                               class="form-control @error('birth_date') is-invalid @enderror" 
                                               value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}" 
                                               max="{{ date('Y-m-d') }}"
                                               required>
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_number">No. HP <span class="text-danger">*</span></label>
                                        <input type="tel" 
                                               name="phone_number" 
                                               id="phone_number" 
                                               class="form-control @error('phone_number') is-invalid @enderror" 
                                               value="{{ old('phone_number', $employee->phone_number) }}" 
                                               placeholder="Masukkan nomor HP"
                                               required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="address">Alamat <span class="text-danger">*</span></label>
                                        <textarea name="address" 
                                                  id="address" 
                                                  class="form-control @error('address') is-invalid @enderror" 
                                                  rows="4" 
                                                  placeholder="Masukkan alamat lengkap"
                                                  required>{{ old('address', $employee->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="photo">Foto Profil</label>
                                        @if($employee->photo_path)
                                            <div class="current-photo mb-2">
                                                <label class="form-label">Foto Saat Ini:</label>
                                                <div>
                                                    <img src="{{ asset('storage/' . $employee->photo_path) }}" 
                                                         alt="{{ $employee->full_name }}" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="custom-file">
                                            <input type="file" 
                                                   name="photo" 
                                                   id="photo" 
                                                   class="custom-file-input @error('photo') is-invalid @enderror" 
                                                   accept="image/*">
                                            <label class="custom-file-label" for="photo">
                                                {{ $employee->photo_path ? 'Ganti foto...' : 'Pilih foto...' }}
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">
                                            Format: JPG, JPEG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.
                                        </small>
                                        @error('photo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Photo Preview -->
                                    <div class="form-group">
                                        <div id="photo-preview" class="text-center" style="display: none;">
                                            <label class="form-label">Preview Foto Baru:</label>
                                            <div>
                                                <img id="preview-image" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // File input label update
    $('#photo').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
        
        // Show image preview
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
                $('#photo-preview').show();
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Auto hide alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 5000);
</script>
@endpush

@endsection 