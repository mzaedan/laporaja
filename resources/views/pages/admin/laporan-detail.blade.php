@extends('layouts.admin')

@section('content')
    <h1>Detail Laporan</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $laporan->title }}</h5>
            <p class="card-text"><strong>Kode:</strong> {{ $laporan->code }}</p>
            <p class="card-text"><strong>Kategori:</strong> {{ $laporan->reportCategory->name ?? '-' }}</p>
            <p class="card-text"><strong>Status Terakhir:</strong> <span class="badge bg-info text-dark">{{ $laporan->latestReportStatus->status ?? '-' }}</span></p>
            <p class="card-text"><strong>Deskripsi:</strong> {{ $laporan->description }}</p>
            <p class="card-text"><strong>Tanggal Laporan:</strong> {{ $laporan->created_at->format('d-m-Y') }}</p>
            <p class="card-text"><strong>Pelapor:</strong> {{ $laporan->resident->user->name ?? '-' }}</p>
            @if($laporan->image)
                <p><img src="{{ asset('storage/' . $laporan->image) }}" alt="Gambar Laporan" style="max-width:300px;"></p>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection
