@extends('layouts.admin')

@section('title', 'Detail Data Laporan')

@section('content')

<a href="{{ route('admin.report.index') }}" class="btn btn-danger mb-3">Kembali</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Laporan</h6>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <td>Kode Laporan</td>
                <td>{{ $report->code }}</td>
            </tr>
            <tr>
                <td>Pelapor</td>
                <td>{{ $report->resident->user->email }} - {{ $report->resident->user->name }}</td>
            </tr>
            <tr>
                <td>Kategori Laporan</td>
                <td>{{ $report->reportCategory->name }}</td>
            </tr>
            <tr>
                <td>Judul Laporan</td>
                <td>{{ $report->title }}</td>
            </tr>
            <tr>
                <td>Deskripsi Laporan</td>
                <td>{{ $report->description }}</td>
            </tr>
             <tr>
                <td>Gambar/Icon</td>
                <td>
                    <img src="{{ asset('storage/' . $report->image) }}" alt="bukti-laporan" width="200">
                </td>
            </tr>
            <tr>
                <td>Latitude</td>
                <td>{{ $report->latitude }}</td>
            </tr>
            <tr>
                <td>Longitude</td>
                <td>{{ $report->longitude }}</td>
            </tr>
            <tr>
                <td>Map View</td>
                <td>
                    <div id="map" style="height:300px"></div>
                </td>
            </tr>
            <tr>
                <td>Alamat Laporan</td>
                <td>{{ $report->address }}</td>
            </tr>
            <tr>
                <td>Tanggal Masuk/Diproses</td>
                <td>{{ $report->created_at->format('d/m/Y H:i:s') }} ({{ $report->created_at->diffForHumans() }})</td>
            </tr>
            @if($report->hasPriorityScoring())
            <tr>
                <td>Tingkat Urgensi</td>
                <td>
                    <span class="badge {{ $report->priority_badge_class }}" style="font-size: 0.9rem;">
                        {{ $report->priority_level }} ({{ $report->calculated_priority_score }})
                    </span>
                </td>
            </tr>
            <tr>
                <td>Breakdown Scoring</td>
                <td>
                    <small>
                        <strong>Kategori:</strong> {{ $report->category_score }}/5 ({{ round($report->category_score * 0.40, 2) }})<br>
                        <strong>Dampak Komunitas:</strong> {{ $report->community_impact_score }}/5 ({{ round($report->community_impact_score * 0.30, 2) }})<br>
                        <strong>Tingkat Kerusakan:</strong> {{ $report->damage_level_score }}/5 ({{ round($report->damage_level_score * 0.20, 2) }})<br>
                        <strong>Urgensi Waktu:</strong> {{ $report->time_urgency_score }}/5 ({{ round($report->time_urgency_score * 0.10, 2) }})
                    </small>
                </td>
            </tr>
            @if($report->scoring_notes)
            @endif
            @endif
        </table>
    </div>
</div>

<div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Status Laporan</h6>
        </div>
        <div class="card-body">
            <a href="{{ route('admin.report-status.create', $report->id) }}" class="btn btn-primary mb-3">Tambah Progres Laporan</a>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report->reportStatuses as $status)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td> 
                                    @if ($status->image)
                                   @php
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                        $filePath = $status->image;
                                        $isImage = false;
                                        $fileUrl = $filePath ? asset('storage/' . $filePath) : null;
                                        if ($filePath) {
                                            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, $imageExtensions);
                                        }
                                    @endphp
                                    @if ($filePath)
                                        @if ($isImage)
                                            <img src="{{ $fileUrl }}" alt="image" width="100">
                                        @else
                                            <a href="{{ $fileUrl }}" target="_blank" style="text-decoration:none;">
                                                <i class="fa fa-file-alt fa-2x text-secondary"></i>
                                                <span class="d-block small">Download Bukti</span>
                                            </a>
                                        @endif
                                    @else
                                        Bukti Belum Tersedia
                                    @endif
                                    @else
                                        Bukti Belum Tersedia
                                    @endif
                                </td>
                                <td>{{ $status->status }}</td>
                                <td>{{ $status->description }}</td>
                                <td>{{ $status->created_at->format('d/m/Y H:i:s') }} ({{ $status->created_at->diffForHumans() }})</td>
                                <td>
                                    <a href="{{ route('admin.report-status.edit', $status->id) }}" class="btn btn-warning">Edit</a>

                                    <form action="{{ route('admin.report-status.destroy', $status->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<div class="card shadow mb-5">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Feedback Laporan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Pesan</th>
                        <th>Kesan</th>
                        <th>Nilai</th>
                        <th>Tanggal Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($report->feedbacks ?? []) as $feedback)
                        <tr>
                            <td>{{ $feedback->impression }}</td>
                            <td>{{ $feedback->message }}</td>
                            <td>{{ $feedback->satisfaction }}</td>
                            <td>{{ $feedback->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada feedback untuk laporan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Prevent caching and ensure fresh data
    $(document).ready(function() {
        // Add cache busting to all navigation links
        $('a[href*="admin"]:not([data-toggle]):not(.no-refresh)').on('click', function(e) {
            const href = $(this).attr('href');
            if (href && href.indexOf('#') !== 0) {
                const separator = href.indexOf('?') > -1 ? '&' : '?';
                const freshUrl = href + separator + '_refresh=' + Date.now();
                $(this).attr('href', freshUrl);
            }
        });
        
        // Auto-refresh report data - increased interval for better UX
        let refreshInterval = setInterval(function() {
            if (document.hasFocus() && !document.hidden) {
                // Only refresh if there's actual activity or changes needed
                // You can uncomment the line below if you want automatic refresh
                // refreshReportData();
                
                // Show a subtle notification that data can be refreshed
                showRefreshNotification();
            }
        }, 300000); // 5 minutes instead of 1 minute
        
        // Clear interval when navigating away
        $(window).on('beforeunload', function() {
            clearInterval(refreshInterval);
        });
        
        // Function to show a subtle refresh notification
        function showRefreshNotification() {
            if (!$('.refresh-available-indicator').length) {
                const indicator = $('<div class="refresh-available-indicator position-fixed" style="top: 10px; right: 10px; z-index: 9999; background: rgba(33, 150, 243, 0.9); color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"><i class="fas fa-info-circle"></i> Data dapat diperbarui <button class="btn btn-sm btn-light ml-2" style="padding: 2px 6px; font-size: 10px;" onclick="refreshReportData(); $(this).closest(\'.refresh-available-indicator\').remove();">Refresh</button></div>');
                $('body').append(indicator);
                
                // Auto-hide after 10 seconds
                setTimeout(function() {
                    indicator.fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 10000);
            }
        }
        
        // Function to refresh report data without full page reload
        function refreshReportData() {
            const reportId = {{ $report->id }};
            
            // Remove any existing refresh notifications
            $('.refresh-available-indicator').remove();
            
            // Add visual indicator
            if (!$('.refresh-indicator').length) {
                $('body').append('<div class="refresh-indicator position-fixed" style="top: 10px; right: 10px; z-index: 9999; background: rgba(40, 167, 69, 0.9); color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"><i class="fas fa-sync fa-spin"></i> Memperbarui data...</div>');
            }
            
            // Smooth refresh with cache busting
            setTimeout(function() {
                window.location.href = window.location.pathname + '?_refresh=' + Date.now();
            }, 800);
        }
        
        // Enhanced manual refresh button
        if (!$('.manual-refresh-btn').length) {
            $('.card-header:first').append('<button type="button" class="btn btn-sm btn-success manual-refresh-btn ml-2" onclick="refreshReportData()" title="Refresh Data"><i class="fas fa-sync"></i> Refresh Data</button>');
        }
        
        // Add keyboard shortcut for refresh (F5 or Ctrl+R)
        $(document).on('keydown', function(e) {
            if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                e.preventDefault();
                refreshReportData();
            }
        });
        
        // Make refresh function globally available
        window.refreshReportData = refreshReportData;
    });

    // Ambil data koordinat dari backend
    var latitude = {{ $report->latitude }};
    var longitude = {{ $report->longitude }};

    // Inisialisasi map dengan koordinat dari laporan
    var map = L.map('map').setView([latitude, longitude], 16); // zoom 16 agar lokasi lebih dekat

    // Tambahkan tile layer dari OpenStreetMap
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Tambahkan marker dengan popup
    L.marker([latitude, longitude]).addTo(map)
        .bindPopup('Lokasi Laporan: {{ $report->address }}')
        .openPopup();
        
    // Quick priority scoring update function
    function updatePriorityScoring(reportId, community, damage, urgency, notes) {
        if (!confirm('Are you sure you want to update the priority scoring?')) {
            return;
        }
        
        // Disable buttons during request
        $('button[onclick*="updatePriorityScoring"]').prop('disabled', true);
        
        $.ajax({
            url: '/api/priority-scoring/reports/' + reportId,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache'
            },
            data: JSON.stringify({
                community_impact_score: community,
                damage_level_score: damage,
                time_urgency_score: urgency,
                scoring_notes: notes
            }),
            success: function(response) {
                if (response.success) {
                    alert('Priority scoring updated successfully!');
                    // Force page refresh to show updated data
                    window.location.href = window.location.pathname + '?_updated=' + Date.now();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error updating priority scoring';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function() {
                // Re-enable buttons
                $('button[onclick*="updatePriorityScoring"]').prop('disabled', false);
            }
        });
    }
    
    // Make function globally available
    window.updatePriorityScoring = updatePriorityScoring;
</script>
@endsection
