@extends('templates.template')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Detail Complaint #{{ $complaint->id }}</h4>
                        </div>
                        <div>
                            @if($complaint->user_id == Auth::id() && $complaint->status == 'pending')
                                <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-warning mr-2">
                                    <i class="ri-edit-line"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('complaints.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line"></i> Kembali
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Session Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Left Column - Complaint Details -->
                            <div class="col-md-8">
                                <!-- Complaint Header -->
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar-40 rounded-circle bg-primary d-flex align-items-center justify-content-center mr-3">
                                            <span class="text-white font-weight-bold">
                                                {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $complaint->user->name }}</h5>
                                            <small class="text-muted">{{ $complaint->created_at->format('d F Y, H:i') }}</small>
                                            <div class="mt-1">
                                                <span class="badge badge-primary">#{{ $complaint->category }}</span>
                                                @if($complaint->booking)
                                                    <span class="badge badge-secondary">#booking-{{ $complaint->booking_id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge badge-lg badge-{{ $complaint->status == 'pending' ? 'warning' : ($complaint->status == 'in_progress' ? 'info' : 'success') }}">
                                        {{ $complaint->status == 'pending' ? 'PENDING' : ($complaint->status == 'in_progress' ? 'DIPROSES' : 'SELESAI') }}
                                    </span>
                                </div>

                                <!-- Complaint Content -->
                                <div class="complaint-content mb-4">
                                    <h6 class="text-muted mb-2">Deskripsi Complaint:</h6>
                                    <div class="bg-light p-3 rounded">
                                        {!! $complaint->description !!}
                                    </div>
                                </div>

                                <!-- Evidence -->
                                @if($complaint->evidence_path)
                                    <div class="evidence-section mb-4">
                                        <h6 class="text-muted mb-2">Bukti Pendukung:</h6>
                                        <div class="bg-light p-3 rounded">
                                            @if(in_array(pathinfo($complaint->evidence_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                <img src="{{ asset('storage/' . $complaint->evidence_path) }}" 
                                                     alt="Evidence" 
                                                     class="img-fluid rounded" 
                                                     style="max-width: 100%; max-height: 400px;">
                                            @else
                                                <a href="{{ asset('storage/' . $complaint->evidence_path) }}" 
                                                   target="_blank" 
                                                   class="btn btn-outline-primary">
                                                    <i class="ri-download-line"></i> Download File Bukti
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Related Booking -->
                                @if($complaint->booking)
                                    <div class="related-booking mb-4">
                                        <h6 class="text-muted mb-2">Peminjaman Terkait:</h6>
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $complaint->booking->room->name }}</h6>
                                                <p class="card-text">
                                                    <strong>Waktu:</strong> {{ $complaint->booking->start_time->format('d F Y, H:i') }} - {{ $complaint->booking->end_time->format('H:i') }}<br>
                                                    <strong>Status:</strong> 
                                                    <span class="badge badge-{{ $complaint->booking->status == 'approved' ? 'success' : ($complaint->booking->status == 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($complaint->booking->status) }}
                                                    </span>
                                                </p>
                                                <a href="{{ route('bookings.show', $complaint->booking) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-external-link-line"></i> Lihat Detail Booking
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Admin Response -->
                                @if($complaint->admin_response)
                                    <div class="admin-response">
                                        <h6 class="text-muted mb-2">Respon Admin:</h6>
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">
                                                <i class="ri-admin-line mr-2"></i>Respon dari Tim Admin
                                            </div>
                                            <div class="card-body">
                                                {!! $complaint->admin_response !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Admin Response Form (Only for Admin) -->
                                @if(Auth::user()->role == 1 && $complaint->status != 2)
                                    <div id="respond" class="admin-respond-section mt-4">
                                        <h6 class="text-muted mb-3">Respon Admin:</h6>
                                        <form action="{{ route('complaints.respond', $complaint) }}" method="POST">
                                            @csrf
                                            
                                            <div class="form-group">
                                                <label for="admin_response">Respon <span class="text-danger">*</span></label>
                                                <textarea name="admin_response" 
                                                          id="admin_response" 
                                                          class="form-control @error('admin_response') is-invalid @enderror" 
                                                          required>{{ old('admin_response', $complaint->admin_response) }}</textarea>
                                                @error('admin_response')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="status">Status <span class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                                    <option value=0 {{ old('status', $complaint->status) == 0 ? 'selected' : '' }}>Pending</option>
                                                    <option value=1 {{ old('status', $complaint->status) == 1 ? 'selected' : '' }}>Diproses</option>
                                                    <option value=2 {{ old('status', $complaint->status) == 2 ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <button type="submit" class="btn btn-success">
                                                <i class="ri-send-plane-line"></i> Kirim Respon
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column - Info Panel -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Informasi Complaint</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td class="font-weight-bold">ID:</td>
                                                <td>#{{ $complaint->id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Kategori:</td>
                                                <td>
                                                    <span class="badge badge-primary">{{ ucfirst($complaint->category) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Status:</td>
                                                <td>
                                                    <span class="badge badge-{{ $complaint->status == 'pending' ? 'warning' : ($complaint->status == 'in_progress' ? 'info' : 'success') }}">
                                                        {{ $complaint->status == 'pending' ? 'Pending' : ($complaint->status == 'in_progress' ? 'Diproses' : 'Selesai') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Dibuat:</td>
                                                <td>{{ $complaint->created_at->format('d M Y, H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Diupdate:</td>
                                                <td>{{ $complaint->updated_at->format('d M Y, H:i') }}</td>
                                            </tr>
                                            @if($complaint->booking)
                                                <tr>
                                                    <td class="font-weight-bold">Ruangan:</td>
                                                    <td>{{ $complaint->booking->room->name }}</td>
                                                </tr>
                                            @endif
                                        </table>

                                        <!-- Actions -->
                                        @if($complaint->user_id == Auth::id())
                                            <div class="mt-3">
                                                @if($complaint->status == 'pending')
                                                    <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-warning btn-block mb-2">
                                                        <i class="ri-edit-line"></i> Edit Complaint
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-block" 
                                                            onclick="deleteComplaint({{ $complaint->id }})">
                                                        <i class="ri-delete-bin-line"></i> Hapus Complaint
                                                    </button>
                                                    
                                                    <form id="delete-form-{{ $complaint->id }}" 
                                                          action="{{ route('complaints.destroy', $complaint) }}" 
                                                          method="POST" 
                                                          style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @else
                                                    <div class="alert alert-info">
                                                        <small><i class="ri-information-line mr-1"></i> Complaint sedang diproses oleh admin</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize CKEditor for admin response
    @if(Auth::user()->role == 1 && $complaint->status != 'resolved')
    CKEDITOR.replace('admin_response', {
        height: 200,
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
    @endif

    function deleteComplaint(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Complaint akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Auto hide alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 4000);
</script>
@endpush