@extends('layouts.admin')

@section('title', 'Tambah Data Progres Laporan')

@section('content')


 <!-- Page Heading -->
<a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-danger mb-3">Kembali</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Progres Laporan {{ $report->code }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.report-status.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">
            <div class="form-group">
                <label for="image">Bukti</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                <div class="invalid-feedback">
                    @error('image')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="judul">Status</label>
                <select name="status" class="form-control @error('status') is-invalid @enderror">

                    <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>
                       Delivered
                    </option>

                    <option value="in_process" {{ old('status') == 'in_process' ? 'selected' : '' }}>
                       In Proces
                    </option>
               
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                       Completed
                    </option>

                    <option value="completed" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                       Rejected
                    </option>
                </select>
                <div class="invalid-feedback">
                    @error('status')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" rows="5">{{ old('description' )}}</textarea>
                <div class="invalid-feedback">
                    @error('description')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@endsection