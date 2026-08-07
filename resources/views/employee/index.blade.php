@extends('templates.template')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Data Karyawan</h4>
                            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Karyawan
                            </a>
                        </div>

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

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover text-center mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Foto</th>
                                        <th>NIP</th>
                                        <th>Nama Lengkap</th>
                                        <th>Divisi</th>
                                        <th>Jabatan</th>
                                        <th>Jenis Kelamin</th>
                                        <th>No. HP</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $index => $employee)
                                    <tr>
                                        <td>{{ $employees->firstItem() + $index }}</td>
                                        <td>
                                            @if($employee->photo_path)
                                                <img src="{{ asset('storage/' . $employee->photo_path) }}" 
                                                     alt="{{ $employee->full_name }}" 
                                                     class="img-fluid rounded-circle" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $employee->nip }}</td>
                                        <td>{{ $employee->full_name }}</td>
                                        <td>{{ $employee->division->name ?? '-' }}</td>
                                        <td>{{ $employee->position->name ?? '-' }}</td>
                                        <td>{{ $employee->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        <td>{{ $employee->phone_number }}</td>
                                        <td>
                                            @if($employee->employment_status == 'active')
                                                <span class="badge badge-success">Aktif</span>
                                            @elseif($employee->employment_status == 'inactive')
                                                <span class="badge badge-warning">Tidak Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Diberhentikan</span>
                                            @endif
                                        </td>
                                        <td>
                                           
                                           
                                            <div class="d-flex gap-2">
                                               
                                                <a href="{{ route('employees.show', $employee->id) }}" 
                                                   class="btn btn-sm btn-info" 
                                                  >
                                                  Detail
                                                </a>

                                                <a href="{{ route('employees.edit', $employee->id) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                  >
                                                  Edit
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                       
                                                        onclick="deleteEmployee({{ $employee->id }})">
                                                    Hapus
                                                </button>
                                            </div>
                                            
                                            <form id="delete-form-{{ $employee->id }}" 
                                                  action="{{ route('employees.destroy', $employee->id) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data karyawan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($employees->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Menampilkan {{ $employees->firstItem() ?? 0 }} sampai {{ $employees->lastItem() ?? 0 }} 
                                        dari {{ $employees->total() }} data
                                    </small>
                                </div>
                                <div>
                                    {{ $employees->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteEmployee(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data karyawan akan dihapus secara permanen!",
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

    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Auto hide alerts
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 4000);
</script>
@endpush

@endsection