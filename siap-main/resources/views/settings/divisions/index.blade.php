@extends('templates.template')
@section('content')

 <div class="content-page">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <h4 class="card-title mb-3">Daftar Divisi</h4>
                                <div class="table-responsive">

                                    <!-- Session Messages -->
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }} 
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }} 
                                        </div>
                                    @endif

                                    @if($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <a href="/settings/divisions/create" class="btn btn-primary mb-3">
                                        <i class="fas fa-plus"></i> Tambah Divisi
                                    </a>

                                    <table class="table table-striped table-bordered table-hover table-checkable text-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>  
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @forelse($divisions as $index => $division)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $division->name }}</td> 
                                                <td>
                                                    <a href="/settings/divisions/{{ $division->id }}/edit" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>

                                                    <form action="/settings/divisions/{{ $division->id }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="if(confirm('Apakah Anda yakin ingin menghapus divisi {{ $division->name }}?')) { this.form.submit(); }">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                   
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3">Tidak ada data divisi.</td>
                                            </tr>
                                            @endforelse
                                                  
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

            </div>
        </div>

@endsection