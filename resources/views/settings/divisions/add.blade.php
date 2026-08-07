@extends('templates.template')
@section('content')

 <div class="content-page">

            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Form Tambah Divisi</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                
                                <!-- Error Messages -->
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="/settings/divisions" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <div class="form-group">
                                        <label for="namaDivisi">Nama Divisi</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="namaDivisi"
                                            placeholder="Masukkan nama divisi" value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div> 


                                     <div class="form-group">
                                        <label for="deskripsiDivisi">Deskripsi</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="deskripsiDivisi" rows="3" placeholder="Masukkan deskripsi divisi">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                     </div>
                                    
                                   

                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="/settings/divisions" class="btn btn-secondary">Batal</a>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                    
    </div>

@endsection 