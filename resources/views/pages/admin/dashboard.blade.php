@extends('layouts.admin')

@section('content')
    <h1>Dashboard</h1>

    {{-- Notifikasi laporan belum selesai --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5 class="mb-2">Notifikasi/Pengingat Laporan</h5>
                @if(isset($notifikasi) && count($notifikasi) > 0)
                    <ul class="mb-0">
                        @foreach($notifikasi as $laporan)
                            <li>
                                <strong>{{ $laporan->title }}</strong> (Kode: {{ $laporan->code }})<br>
                                Status: <span class="badge bg-info text-dark">{{ $laporan->latestReportStatus->status }}</span>,
                                Tanggal Laporan: {{ $laporan->created_at->format('d-m-Y') }}
                                <br>
                                <a href="#" class="btn btn-sm btn-outline-primary mt-1">Lihat Detail</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted">Tidak ada laporan yang perlu perhatian khusus saat ini.</span>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Laporan</h6>
                    <p class="card-text">{{ \App\Models\Report::count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Jumlah User</h6>
                    <p class="card-text">{{ \App\Models\Resident::count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Kategori Laporan</h6>
                    <p class="card-text">{{ \App\Models\ReportCategory::count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
@endsection