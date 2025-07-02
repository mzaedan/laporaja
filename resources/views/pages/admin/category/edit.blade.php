@extends('layouts.admin')

@section('title', 'Edit Data Masyarakat')

@section('content')


 <!-- Page Heading -->
<a href="{{ route('admin.report-category.index') }}" class="btn btn-danger mb-3">Kembali</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Kategori</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.report-category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="judul">Nama</label>
                <input type="name" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name',$category->name) }}">
                <div class="invalid-feedback">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <img src="{{ asset('storage/' .$category->image) }}" alt="icon-category" width="200" style="padding-bottom: 20px;">
                <br />
                <label for="image">Gambar/Icon <small class="text-muted">(Kosongkan jika tidak ingin mengganti)</small></label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                <div class="invalid-feedback">
                    @error('image')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@endsection