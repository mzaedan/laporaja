@extends('layouts.admin')

@section('title', 'Laporan yang Sedang Proses')

@section('content')

<!-- Header Section -->
<div class="row mb-3">
    <div class="col-md-6">
        <h4 class="page-title">Laporan yang Sedang Proses</h4>
        <p class="text-muted small mb-0">
            <i class="fas fa-info-circle"></i> Halaman ini menampilkan laporan dengan status "Sedang Diproses" yang sedang ditangani
        </p>
    </div>
    <div class="col-md-6 d-flex justify-content-end">
        <!-- Export Excel Section -->
        <div class="card shadow-sm p-3 d-flex flex-wrap align-items-center gap-2 flex-row" style="background: #fff; border-radius: 10px;">
            <form action="{{ route('admin.report.export') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-0">
                <input type="hidden" name="status" value="in_process">
                <span class="text-muted fw-semibold">Export Excel:</span>

                <select name="month" class="form-select form-select-sm" style="min-width: 100px; margin-right:15px; margin-left:10px">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                            {{ date('M', mktime(0, 0, 0, $month, 1)) }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="form-select form-select-sm" style="min-width: 100px; margin-right:15px; margin-left:10px">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), date('Y')-5) as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-success btn-sm px-3">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow">
    <div class="card-header py-3">
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
                        <th style="width: 100px; text-align: center;">Priority Score</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td>{{ $report->code }}</td>
                            <td>{{ $report->resident->user->name ?? '' }}</td>
                            <td>{{ $report->reportCategory->name ?? '' }}</td>
                            <td>{{ $report->title }}</td>
                            <td style="text-align: center; vertical-align: middle;">{{ $report->created_at->format('d/m/Y H:i:s') }}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if($report->hasPriorityScoring())
                                    <span class="badge {{ $report->priority_badge_class }}" style="font-size: 0.75rem;">
                                        {{ $report->priority_level }} ({{ $report->calculated_priority_score }})
                                    </span>
                                    @if($report->effective_priority_score != $report->calculated_priority_score)
                                        <small class="d-block text-muted" style="font-size: 0.65rem;">
                                            Aging: {{ number_format($report->effective_priority_score, 2) }}
                                        </small>
                                    @endif
                                @else
                                    <span class="badge badge-secondary" style="font-size: 0.75rem;">Belum Dinilai</span>
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
        $(document).ready(function() {
            // Initialize DataTable with priority column sorting
            var table = $('#dataTable').DataTable({
                "order": [[ 6, "desc" ]], // Sort by priority score column (index 6) descending
                "columnDefs": [
                    {
                        "targets": 6, // Priority score column
                        "type": "num", // Treat as numeric for proper sorting
                        "render": function(data, type, row) {
                            if (type === 'sort' || type === 'type') {
                                // Extract numeric value for sorting
                                var match = data.match(/\d+(\.\d+)?/);
                                return match ? parseFloat(match[0]) : 0;
                            }
                            return data;
                        }
                    },
                    {
                        "targets": [0, 5, 6, 7], // No, Date, Priority, Action columns
                        "orderable": true
                    }
                ],
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
            
            // Priority filter functionality
            $('.filter-priority').on('click', function() {
                // Remove active class from all buttons
                $('.filter-priority').removeClass('btn-danger btn-warning btn-success btn-secondary')
                                    .addClass('btn-outline-secondary');
                
                // Add active class to clicked button
                var priority = $(this).data('priority');
                $(this).removeClass('btn-outline-secondary');
                
                switch(priority) {
                    case 'tinggi':
                        $(this).removeClass('btn-outline-danger').addClass('btn-danger');
                        break;
                    case 'sedang':
                        $(this).removeClass('btn-outline-warning').addClass('btn-warning');
                        break;
                    case 'rendah':
                        $(this).removeClass('btn-outline-success').addClass('btn-success');
                        break;
                    case 'no-scoring':
                        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                        break;
                    default:
                        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                }
                
                // Apply filter
                if (priority === 'all') {
                    table.column(6).search('').draw();
                } else if (priority === 'no-scoring') {
                    table.column(6).search('Belum Dinilai').draw();
                } else {
                    // Filter by priority level
                    var searchTerm;
                    switch(priority) {
                        case 'tinggi':
                            searchTerm = 'Tinggi';
                            break;
                        case 'sedang':
                            searchTerm = 'Sedang';
                            break;
                        case 'rendah':
                            searchTerm = 'Rendah';
                            break;
                    }
                    table.column(6).search(searchTerm).draw();
                }
            });
            
            // Initialize with "All" filter active
            $('.filter-priority[data-priority="all"]').removeClass('btn-outline-secondary').addClass('btn-secondary');
            
            // Add priority statistics tooltip
            $('[title]').tooltip();
            
            // Update priority statistics periodically
            function updatePriorityStats() {
                var visibleRows = table.rows({filter: 'applied'}).data().length;
                var totalRows = table.rows().data().length;
                
                // Optional: Add a statistics display
                console.log('Showing', visibleRows, 'of', totalRows, 'in-process reports');
            }
            
            // Call stats update when table is redrawn
            table.on('draw', updatePriorityStats);
            
            // Add hover effect for priority badges
            $(document).on('mouseenter', '.badge', function() {
                $(this).addClass('shadow-sm');
            }).on('mouseleave', '.badge', function() {
                $(this).removeClass('shadow-sm');
            });
        });
    </script>
@endpush