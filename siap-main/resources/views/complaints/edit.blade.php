@extends('templates.template')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Complaint</h4>
                        </div>
                        <div>
                            <a href="{{ route('complaints.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Error Messages -->
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

                        <form action="{{ route('complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Kategori Complaint <span class="text-danger">*</span></label>
                                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="peminjaman" {{ old('category', $complaint->category) == 'peminjaman' ? 'selected' : '' }}>Peminjaman Ruangan</option>
                                            <option value="peralatan" {{ old('category', $complaint->category) == 'peralatan' ? 'selected' : '' }}>Peralatan</option>
                                            <option value="karyawan" {{ old('category', $complaint->category) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                            <option value="lainnya" {{ old('category', $complaint->category) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="booking_id">Terkait Peminjaman (Opsional)</label>
                                        <select name="booking_id" id="booking_id" class="form-control @error('booking_id') is-invalid @enderror">
                                            <option value="">-- Pilih Peminjaman (Jika Ada) --</option>
                                            @foreach($bookings as $booking)
                                                <option value="{{ $booking->id }}" {{ old('booking_id', $complaint->booking_id) == $booking->id ? 'selected' : '' }}>
                                                    {{ $booking->room->name }} - {{ $booking->start_time->format('d M Y H:i') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('booking_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Pilih jika complaint terkait dengan peminjaman ruangan tertentu</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi Complaint <span class="text-danger">*</span></label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          required>{{ old('description', $complaint->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Jelaskan masalah yang Anda alami dengan detail</small>
                            </div>

                            <div class="form-group">
                                <label for="evidence">Bukti Pendukung (Opsional)</label>
                                @if($complaint->evidence_path)
                                    <div class="current-evidence mb-2">
                                        <label class="form-label">File Saat Ini:</label>
                                        <div>
                                            @if(in_array(pathinfo($complaint->evidence_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                <img src="{{ asset('storage/' . $complaint->evidence_path) }}" 
                                                     alt="Evidence" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 200px; max-height: 200px;">
                                            @else
                                                <a href="{{ asset('storage/' . $complaint->evidence_path) }}" 
                                                   target="_blank" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="ri-file-line"></i> Lihat File
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" 
                                           name="evidence" 
                                           id="evidence" 
                                           class="custom-file-input @error('evidence') is-invalid @enderror" 
                                           accept="image/*,.pdf,.doc,.docx">
                                    <label class="custom-file-label" for="evidence">
                                        {{ $complaint->evidence_path ? 'Ganti file...' : 'Pilih file...' }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Format yang didukung: JPG, PNG, PDF, DOC, DOCX. Maksimal 5MB. Kosongkan jika tidak ingin mengubah.
                                </small>
                                @error('evidence')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="alert alert-warning">
                                    <i class="ri-information-line mr-2"></i>
                                    <strong>Perhatian:</strong> Anda hanya dapat mengedit complaint yang masih berstatus "Pending".
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('complaints.index') }}" class="btn btn-secondary">
                                    <i class="ri-close-line"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Update Complaint
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.css">
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    // Initialize CKEditor
    CKEDITOR.replace('description', {
        height: 300,
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
            { name: 'styles', items: ['Format'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize'] },
            { name: 'others', items: ['-'] },
            { name: 'about', items: ['About'] }
        ],
        removePlugins: 'elementspath',
        resize_enabled: false
    });

    // File input label update
    $('#evidence').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Pilih file...');
    });

    // Auto hide alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 5000);
</script>
@endpush