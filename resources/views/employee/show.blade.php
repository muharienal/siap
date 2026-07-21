@extends('templates.template')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Detail Karyawan</h4>
                        </div>
                        <div>
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning mr-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Left Column - Photo and Basic Info -->
                            <div class="col-md-4">
                                <div class="text-center mb-4">
                                    @if($employee->photo_path)
                                        <img src="{{ asset('storage/' . $employee->photo_path) }}" 
                                             alt="{{ $employee->full_name }}" 
                                             class="img-fluid rounded-circle mb-3" 
                                             style="width: 200px; height: 200px; object-fit: cover; border: 4px solid #007bff;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" 
                                             style="width: 200px; height: 200px; border: 4px solid #6c757d;">
                                            <i class="fas fa-user text-white" style="font-size: 4rem;"></i>
                                        </div>
                                    @endif
                                    <h3 class="mb-1">{{ $employee->full_name }}</h3>
                                    <p class="text-muted mb-2">{{ $employee->position->name ?? '-' }}</p>
                                    <span class="badge badge-{{ $employee->employment_status == 'active' ? 'success' : ($employee->employment_status == 'inactive' ? 'warning' : 'danger') }} badge-lg">
                                        {{ $employee->employment_status == 'active' ? 'Aktif' : ($employee->employment_status == 'inactive' ? 'Tidak Aktif' : 'Diberhentikan') }}
                                    </span>
                                </div>

                                <!-- Quick Info Cards -->
                                <div class="card border-left-primary shadow h-100 py-2 mb-3">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">NIP</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employee->nip }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-id-badge fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-left-info shadow h-100 py-2 mb-3">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Divisi</div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $employee->division->name ?? '-' }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-building fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Detailed Information -->
                            <div class="col-md-8">
                                <div class="row">
                                    <!-- Personal Information -->
                                    <div class="col-12">
                                        <h5 class="mb-3">
                                            <i class="fas fa-user mr-2"></i>Informasi Personal
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td class="font-weight-bold" style="width: 200px;">Nama Lengkap</td>
                                                        <td>{{ $employee->full_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Jenis Kelamin</td>
                                                        <td>{{ $employee->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Tanggal Lahir</td>
                                                        <td>{{ $employee->birth_date ? $employee->birth_date->format('d F Y') : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Umur</td>
                                                        <td>
                                                            @if($employee->birth_date)
                                                                {{ $employee->birth_date->age }} tahun
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">No. HP</td>
                                                        <td>
                                                            <a href="tel:{{ $employee->phone_number }}" class="text-decoration-none">
                                                                <i class="fas fa-phone mr-1"></i>{{ $employee->phone_number }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Alamat</td>
                                                        <td>{{ $employee->address }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <!-- Employment Information -->
                                    <div class="col-12">
                                        <h5 class="mb-3">
                                            <i class="fas fa-briefcase mr-2"></i>Informasi Kepegawaian
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td class="font-weight-bold" style="width: 200px;">NIP</td>
                                                        <td>{{ $employee->nip }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Divisi</td>
                                                        <td>{{ $employee->division->name ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Jabatan</td>
                                                        <td>{{ $employee->position->name ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Status Kepegawaian</td>
                                                        <td>
                                                            <span class="badge badge-{{ $employee->employment_status == 'active' ? 'success' : ($employee->employment_status == 'inactive' ? 'warning' : 'danger') }}">
                                                                {{ $employee->employment_status == 'active' ? 'Aktif' : ($employee->employment_status == 'inactive' ? 'Tidak Aktif' : 'Diberhentikan') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <!-- Account Information -->
                                    <div class="col-12">
                                        <h5 class="mb-3">
                                            <i class="fas fa-user-cog mr-2"></i>Informasi Akun
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td class="font-weight-bold" style="width: 200px;">Nama User</td>
                                                        <td>{{ $employee->user->name ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Email</td>
                                                        <td>
                                                            @if($employee->user && $employee->user->email)
                                                                <a href="mailto:{{ $employee->user->email }}" class="text-decoration-none">
                                                                    <i class="fas fa-envelope mr-1"></i>{{ $employee->user->email }}
                                                                </a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Role</td>
                                                        <td>
                                                            @if($employee->user)
                                                                @if($employee->user->role == 1)
                                                                    <span class="badge badge-danger">Admin</span>
                                                                @elseif($employee->user->role == 2)
                                                                    <span class="badge badge-warning">Manager</span>
                                                                @else
                                                                    <span class="badge badge-primary">User</span>
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Tanggal Dibuat</td>
                                                        <td>{{ $employee->created_at->format('d F Y, H:i') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="font-weight-bold">Terakhir Diupdate</td>
                                                        <td>{{ $employee->updated_at->format('d F Y, H:i') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                    </a>
                                    <div>
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning mr-2">
                                            <i class="fas fa-edit"></i> Edit Karyawan
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                onclick="deleteEmployee({{ $employee->id }})">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                                
                                <form id="delete-form-{{ $employee->id }}" 
                                      action="{{ route('employees.destroy', $employee->id) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
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
</script>
@endpush

@endsection