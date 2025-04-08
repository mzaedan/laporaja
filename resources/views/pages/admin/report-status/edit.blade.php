@extends('layouts.admin')

@section('title', 'Edit Data Progres Laporan')

@section('content')


 <!-- Page Heading -->
<a href="{{ route('admin.report.show', $status->report_id) }}" class="btn btn-danger mb-3">Kembali</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Progres Laporan {{ $status->report->code }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.report-status.update', $status->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="report_id" value="{{ $status->report_id }}">
             <div class="form-group">
                <label for="image">Bukti</label>
                @if ($status->image)
                    <img src="{{ asset('storage/'. $status->image) }}" alt="Bukti" width="200">
                @endif
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
                    <option value="delivered" {{ old('status', $status->status) == 'delivered' ? 'selected' : '' }}>
                        Delivered
                    </option>
                    <option value="in_process" {{ old('status', $status->status) == 'in_process' ? 'selected' : '' }}>
                        In Process
                    </option>
                    <option value="completed" {{ old('status', $status->status) == 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>
                    <option value="rejected" {{ old('status', $status->status) == 'rejected' ? 'selected' : '' }}>
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
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $status->description) }}</textarea>
                @error('description')
                    {{ $message }}
                @enderror
            </div>
        
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@endsection