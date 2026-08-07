@extends('templates.template')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Manajemen Complaints</h4>
                        <p class="mb-0">Total Complaints: <strong>{{ $complaints->total() }}</strong></p>
                    </div>
                    <div>
                        <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                            <i class="ri-add-line"></i> Buat Complaint Baru
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
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

                        <!-- Filters -->
                        <form method="GET" action="{{ route('complaints.index') }}" class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cari deskripsi complaint" 
                                       aria-label="Search">
                            </div>
                            <div class="col-md-3 mb-3">
                                <select class="form-control" name="category">
                                    <option value="">Pilih kategori</option>
                                    <option value="peminjaman" {{ request('category') == 'peminjaman' ? 'selected' : '' }}>Peminjaman Ruangan</option>
                                    <option value="peralatan" {{ request('category') == 'peralatan' ? 'selected' : '' }}>Peralatan</option>
                                    <option value="karyawan" {{ request('category') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                    <option value="lainnya" {{ request('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select class="form-control" name="status">
                                    <option value="">Pilih status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button type="submit" class="btn btn-outline-primary btn-block">Filter</button>
                            </div>
                        </form>

                        <!-- Complaint Items -->
                        <div id="complaintList">
                            @forelse($complaints as $complaint)
                            <div class="complaint-item border rounded p-4 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar-40 rounded-circle bg-primary d-flex align-items-center justify-content-center mr-3">
                                            <span class="text-white font-weight-bold">
                                                {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $complaint->user->name }}</h6>
                                            <small class="text-muted">{{ $complaint->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <span class="badge badge-{{ $complaint->status == 0 ? 'warning' : ($complaint->status == 1 ? 'info' : 'success') }}">
                                        {{ $complaint->status == 0 ? 'PENDING' : ($complaint->status == 1 ? 'DIPROSES' : 'SELESAI') }}
                                    </span>
                                </div>
                                
                                <h5 class="mb-2">
                                    <a href="{{ route('complaints.show', $complaint) }}" class="text-decoration-none">
                                        Complaint {{ ucfirst($complaint->category) }} #{{ $complaint->id }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-3">
                                    {{ Str::limit(strip_tags($complaint->description), 200) }}
                                </p>

                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        @if($complaint->booking)
                                            <div class="mr-4">
                                                <i class="ri-calendar-line mr-1"></i>
                                                <span class="text-muted">{{ $complaint->booking->room->name ?? 'N/A' }}</span>
                                            </div>
                                        @endif
                                        @if($complaint->evidence_path)
                                            <div class="mr-4">
                                                <i class="ri-attachment-line mr-1"></i>
                                                <span class="text-muted">Ada Bukti</span>
                                            </div>
                                        @endif
                                        @if($complaint->admin_response)
                                            <div>
                                                <i class="ri-message-2-line mr-1"></i>
                                                <span class="text-muted">Ada Respon Admin</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="btn-group" role="group">
                                        <a href="{{ route('complaints.show', $complaint) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ri-eye-line"></i> Detail
                                        </a>
                                        
                                        @if($complaint->user_id == Auth::id() && $complaint->status == 0)
                                            <a href="{{ route('complaints.edit', $complaint) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="ri-edit-line"></i> Edit
                                            </a>
                                        @endif

                                        @if($complaint->user_id == Auth::id() || Auth::user()->role == 1)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteComplaint({{ $complaint->id }})">
                                                <i class="ri-delete-bin-line"></i> Hapus
                                            </button>
                                        @endif
                                        
                                        @if(Auth::user()->role == 1 && $complaint->status != 2)
                                            <a href="{{ route('complaints.show', $complaint) }}#respond" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="ri-reply-line"></i> Respon
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <span class="badge badge-primary">#{{ $complaint->category }}</span>
                                    @if($complaint->booking)
                                        <span class="badge badge-secondary">#booking-{{ $complaint->booking_id }}</span>
                                    @endif
                                </div>

                                <!-- Hidden delete form -->
                                @if($complaint->user_id == Auth::id() || Auth::user()->role == 1)
                                    <form id="delete-form-{{ $complaint->id }}" 
                                          action="{{ route('complaints.destroy', $complaint) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="ri-file-list-3-line" style="font-size: 3rem; color: #dee2e6;"></i>
                                <h5 class="mt-3 text-muted">Belum ada complaint</h5>
                                <p class="text-muted">Mulai buat complaint pertama Anda</p>
                                <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line"></i> Buat Complaint
                                </a>
                            </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        @if($complaints->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <p class="mb-0 text-muted">
                                        Menampilkan {{ $complaints->firstItem() }} sampai {{ $complaints->lastItem() }} 
                                        dari {{ $complaints->total() }} complaints
                                    </p>
                                </div>
                                <nav aria-label="Page navigation">
                                    {{ $complaints->appends(request()->query())->links() }}
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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