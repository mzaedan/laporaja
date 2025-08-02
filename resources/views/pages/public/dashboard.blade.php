@extends('layouts.app')

@section('title', 'Dashboard Masyarakat - LaporAja')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-11">
            <!-- Header -->
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark">Dashboard Laporan Masyarakat</h4>
                <p class="text-secondary">Statistik laporan untuk bulan <span id="current-month"></span></p>
            </div>

            <!-- Loading State -->
            <div id="loading-state" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-secondary mt-2">Memuat data...</p>
            </div>

            <!-- Dashboard Content -->
            <div id="dashboard-content" class="d-none">
                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Laporan -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-success bg-opacity-10 rounded-circle p-1 me-3 flex-shrink-0">
                                        <i class="fas fa-layer-group text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $totalReports }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Selesai -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-success bg-opacity-10 rounded-circle p-1 me-3 flex-shrink-0">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $completedReports }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Tertunda -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-warning bg-opacity-10 rounded-circle p-1 me-3 flex-shrink-0">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $pendingReports }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prioritas Tinggi -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-danger bg-opacity-10 rounded-circle p-1 me-3 flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-danger"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $highPriorityReports }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-4 mb-4">
                    <!-- Daily Reports Chart -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="card-title mb-0 fw-semibold">Laporan Harian</h6>
                            </div>
                            <div class="card-body pt-0">
                                <canvas id="daily-chart" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Status Distribution Chart -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="card-title mb-0 fw-semibold">Distribusi Status Laporan</h6>
                            </div>
                            <div class="card-body pt-0">
                                <canvas id="status-chart" height="260" style="margin-bottom:32px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="card-title mb-0 fw-semibold">Laporan Berdasarkan Kategori</h6>
                    </div>
                    <div class="card-body">
                        <div id="category-list" class="row g-3">
                            <!-- Categories will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="card-title mb-0 fw-semibold">Laporan Terbaru</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentReports as $report)
                                <a href="{{ route('report.show', $report['code']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $report['title'] }}</h6>
                                            <p class="mb-1 text-muted small">{{ $report['category'] }} • {{ $report['time_ago'] }}</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge @switch($report['status'])
                                                @case('completed') bg-success @break
                                                @case('in_process') bg-warning @break
                                                @case('delivered') bg-info @break
                                                @case('rejected') bg-danger @break
                                                @default bg-warning
                                            @endswitch rounded-pill">
                                                @switch($report['status'])
                                                    @case('completed') Selesai @break
                                                    @case('in_process') Diproses @break
                                                    @case('delivered') Terkirim @break
                                                    @case('rejected') Ditolak @break
                                                    @default {{ $report['status'] }}
                                                @endswitch
                                            </span>
                                            @if($report['priority'] === 'tinggi')
                                                <span class="badge bg-danger rounded-pill ms-1">Prioritas Tinggi</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="text-center text-muted p-3">Belum ada laporan</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data langsung dari controller
    const dailyReports = @json($dailyReports);
    const statusDistribution = @json($statusDistribution);
    const categoryDistribution = @json($categoryDistribution);

    let dailyChart, statusChart;

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Set current month
        const currentMonth = new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        document.getElementById('current-month').textContent = currentMonth;
        
        // Create charts with data from controller
        createDailyChart(dailyReports || []);
        createStatusChart(statusDistribution || []);
        createCategoryList(categoryDistribution || []);

        // Hide loader, show dashboard
        document.getElementById('loading-state').classList.add('d-none');
        document.getElementById('dashboard-content').classList.remove('d-none');
    });

    function createDailyChart(dailyData) {
        const ctx = document.getElementById('daily-chart').getContext('2d');
        
        if (dailyChart) {
            dailyChart.destroy();
        }
        
        const labels = dailyData.map(item => new Date(item.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
        const data = dailyData.map(item => item.count);
        
        dailyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data,
                    borderColor: '#16752B',
                    backgroundColor: 'rgba(22, 117, 43, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    function createStatusChart(statusData) {
    const ctx = document.getElementById('status-chart').getContext('2d');
    if (statusChart) {
        statusChart.destroy();
    }
    // Map status ke label dan warna
    const statusMap = {
        completed: { label: 'Selesai', color: '#28a745' },
        in_process: { label: 'Diproses', color: '#ffc107' },
        rejected: { label: 'Ditolak', color: '#dc3545' }
    };
    const labels = [];
    const data = [];
    const colors = [];
    Object.keys(statusMap).forEach(key => {
        const found = statusData.find(item => item.status === key);
        labels.push(statusMap[key].label);
        data.push(found ? found.count : 0);
        colors.push(statusMap[key].color);
    });

    statusChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: colors,
            borderWidth: 0
        }]
    },
});
}


    function createCategoryList(categoryData) {
        const container = document.getElementById('category-list');
        container.innerHTML = '';
        
        categoryData.forEach(category => {
            const categoryElement = document.createElement('div');
            categoryElement.className = 'col-6 col-md-4';
            categoryElement.innerHTML = `
                <div class="card border-0 bg-light">
                    <div class="card-body text-center p-3">
                        <h6 class="mb-1 fw-semibold">${category.category}</h6>
                        <p class="mb-0 text-primary fw-bold">${category.count}</p>
                    </div>
                </div>
            `;
            container.appendChild(categoryElement);
        });
    }

    function getStatusBadgeClass(status) {
        switch(status) {
            case 'completed': return 'bg-success';    // Green for completed
            case 'in_process': return 'bg-primary';   // Blue for in process
            case 'delivered': return 'bg-info';       // Light blue for delivered
            case 'rejected': return 'bg-danger';      // Red for rejected
            default: return 'bg-warning';             // Yellow/orange for any other status
        }
    }
</script>
@endsection
