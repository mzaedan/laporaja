@extends('layouts.admin')

@section('title', 'Laporan Selesai')

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Laporan Selesai</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan Selesai</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Laporan</th>
                            <th>Pelapor</th>
                            <th>Kategori Laporan</th>
                            <th>Judul Laporan</th>
                            <th>Urgensi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $report->code }}</td>
                                <td>{{ $report->resident->user->name ?? 'Tidak ada nama' }}</td>
                                <td>{{ $report->reportCategory->name }}</td>
                                <td>{{ $report->title }}</td>
                                <td>
                                    @if ($report->urgency_level == 3)
                                        <span class="badge badge-danger">Tinggi</span>
                                    @elseif($report->urgency_level == 2)
                                        <span class="badge badge-warning">Sedang</span>
                                    @else
                                        <span class="badge badge-info">Rendah</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($report->latestStatus)
                                        @if ($report->latestStatus->status == 'completed')
                                            <span class="badge badge-success">Selesai</span>
                                        @elseif($report->latestStatus->status == 'rejected')
                                            <span class="badge badge-danger">Ditolak</span>
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">Tidak ada status</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.report.show', $report->id) }}"
                                            class="btn btn-info btn-sm mr-1">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
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
