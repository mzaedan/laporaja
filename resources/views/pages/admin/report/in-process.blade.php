@extends('layouts.admin')

@section('title', 'Laporan yang Sedang Proses')

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Laporan yang Sedang Proses</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan yang Sedang Proses</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th style="text-align: center;">Kode Laporan</th>
                            <th style="text-align: center;">Pelapor</th>
                            <th style="text-align: center;">Kategori Laporan</th>
                            <th style="text-align: center;">Judul Laporan</th>
                            <th style="width: 120px; text-align: center;">Waktu Masuk</th>
                            <th style="width: 80px; text-align: center;">Urgensi</th>
                            <th style="width: 85px; text-align: center;">Status</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td style="text-align: center;">{{ $loop->iteration }}</td>
                                <td>{{ $report->code }}</td>
                                <td>{{ $report->resident->user->name ?? 'Tidak ada nama' }}</td>
                                <td>{{ $report->reportCategory->name }}</td>
                                <td>{{ $report->title }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $report->created_at->format('d/m/Y H:i:s') }}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if($report->urgency_level == 3)
                                        <span class="badge badge-danger" style="font-size: 0.75rem;">Tinggi</span>
                                    @elseif($report->urgency_level == 2)
                                        <span class="badge badge-warning" style="font-size: 0.75rem;">Sedang</span>
                                    @else
                                        <span class="badge badge-success" style="font-size: 0.75rem;">Rendah</span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @php
                                        $status = $report->reportStatuses->last()->status ?? null;
                                    @endphp
                                    @if ($status === 'in_process')
                                        <span class="badge badge-warning" style="font-size: 0.75rem;">Diproses</span>
                                    @else
                                        <span class="badge badge-secondary" style="font-size: 0.75rem;">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.report.edit', $report->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-info btn-sm me-1" title="Show">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.report.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('addon-style')
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('addon-script')
    <!-- Page level plugins -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
@endpush