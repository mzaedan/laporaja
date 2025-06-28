@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="min-vh-100 py-4">
    <div class="container" style="max-width: 40rem;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark fs-2 fw-bold mb-0">Notifications</h2>
        </div>

        <div class="bg-white rounded-4 shadow-lg overflow-hidden border">
            @if($statuses && count($statuses))
                <!-- Notification List -->
                <div class="notification-container p-4">
                    @foreach($statuses as $status)
                        @if($status->report && $status->report->resident_id == auth()->user()?->resident?->id)
                            <div class="notification-item mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <!-- Header Row with better alignment -->
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="d-flex align-items-start flex-grow-1">
                                                <!-- Status Icon -->
                                                <div class="status-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center
                                                    @switch($status->status)
                                                        @case('delivered') bg-info @break
                                                        @case('in_process') bg-warning @break
                                                        @case('completed') bg-success @break
                                                        @case('rejected') bg-danger @break
                                                    @endswitch"
                                                    style="width: 40px; height: 40px; border-radius: 50%;">
                                                    @switch($status->status)
                                                        @case('delivered')
                                                            <i class="bi bi-inbox text-white"></i>
                                                            @break
                                                        @case('in_process')
                                                            <i class="bi bi-clock text-white"></i>
                                                            @break
                                                        @case('completed')
                                                            <i class="bi bi-check-circle text-white"></i>
                                                            @break
                                                        @case('rejected')
                                                            <i class="bi bi-x-circle text-white"></i>
                                                            @break
                                                    @endswitch
                                                </div>
                                                
                                                <!-- Report Info -->
                                                <div class="flex-grow-1 min-w-0">
                                                    <h6 class="mb-1 fw-bold text-dark">
                                                        Laporan #{{ $status->report?->code }}
                                                    </h6>
                                                    <div class="mb-2">
                                                        <span class="badge rounded-pill
                                                            @switch($status->status)
                                                                @case('delivered') bg-info @break
                                                                @case('in_process') bg-warning @break
                                                                @case('completed') bg-success @break
                                                                @case('rejected') bg-danger @break
                                                            @endswitch">
                                                            @switch($status->status)
                                                                @case('delivered') Diterima @break
                                                                @case('in_process') Diproses @break
                                                                @case('completed') Selesai @break
                                                                @case('rejected') Ditolak @break
                                                            @endswitch
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Status Message -->
                                        <div class="status-message mb-2">
                                            @switch($status->status)
                                                @case('delivered')
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-success me-2">✅</span>
                                                        <p class="mb-0 text-dark">Laporan Anda telah diterima dan akan segera diproses</p>
                                                    </div>
                                                    @break
                                                @case('in_process')
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-warning me-2">⏳</span>
                                                        <p class="mb-0 text-dark">Laporan Anda sedang dalam proses penanganan</p>
                                                    </div>
                                                    @break
                                                @case('completed')
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-success me-2">✅</span>
                                                        <p class="mb-0 text-dark">Laporan Anda telah selesai ditangani</p>
                                                    </div>
                                                    <!-- Feedback Form -->
                                                    @if(empty($status->report->feedback))
                                                    <form action="{{ route('feedback.store', $status->report->id) }}" method="POST" class="mt-3 border-top pt-3">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="impression" class="form-label fw-bold">Kesan</label>
                                                            <textarea name="impression" id="impression" class="form-control" rows="2" required>{{ old('impression') }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="message" class="form-label fw-bold">Pesan</label>
                                                            <textarea name="message" id="message" class="form-control" rows="2" required>{{ old('message') }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Tingkat Kepuasan</label>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="small">Sangat Tidak Puas</span>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="satisfaction" id="satisfaction{{ $i }}" value="{{ $i }}" {{ old('satisfaction') == $i ? 'checked' : '' }} required>
                                                                        <label class="form-check-label" for="satisfaction{{ $i }}">{{ $i }}</label>
                                                                    </div>
                                                                @endfor
                                                                <span class="small">Sangat Puas</span>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill">Kirim Feedback</button>
                                                    </form>
                                                    @else
                                                    <div class="alert alert-success mt-3 mb-0 p-2 small">Feedback sudah dikirim. Terima kasih!</div>
                                                    @endif
                                                    @break
                                                @case('rejected')
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-danger me-2">❌</span>
                                                        <p class="mb-0 text-dark">Laporan Anda ditolak</p>
                                                    </div>
                                                    @break
                                            @endswitch
                                        </div>
                                        
                                        <!-- Timestamp -->
                                        <div class="text-muted small mb-3">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $status->created_at?->diffForHumans() }}
                                        </div>
                                        
                                        <!-- Description -->
                                        @if($status->description)
                                            <div class="description-box mb-3">
                                                <div class="bg-light rounded p-3 border-start border-4 border-primary">
                                                    <h6 class="fw-bold mb-2 text-dark">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        Detail Informasi:
                                                    </h6>
                                                    <p class="mb-0 text-dark">{{ $status->description }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Evidence Button - Moved to bottom -->
                                        @if($status->image)
                                            <div class="text-center">
                                                <a href="{{ asset('storage/'. $status->image) }}"
                                                   target="_blank"
                                                   class="btn btn-outline-success btn-sm rounded-pill">
                                                    <i class="bi bi-eye me-1"></i>
                                                    Lihat Bukti
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="empty-state text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">Belum Ada Notifikasi</h5>
                    <p class="text-muted mb-4">Anda akan menerima notifikasi ketika ada update status laporan</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Custom CSS untuk memperbaiki tampilan */
.notification-container {
    /* Removed overflow restrictions */
}

.notification-item {
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}

.notification-item:hover {
    transform: translateX(5px);
    border-left-color: #007bff;
}

.card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
}

.card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.status-circle {
    min-width: 40px;
    min-height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    white-space: nowrap;
}

.description-box {
    margin-top: 1rem;
}

.description-box .bg-light {
    background-color: #f8f9fa !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        max-width: 100% !important;
        padding: 0 15px;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
    
    .status-circle {
        min-width: 45px;
        min-height: 45px;
        font-size: 1.1rem;
    }
    
    .fs-2 {
        font-size: 1.75rem !important;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1rem !important;
    }
    
    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }
    
    .status-circle {
        min-width: 40px;
        min-height: 40px;
        font-size: 1rem;
    }
    
    .fs-2 {
        font-size: 1.5rem !important;
    }
}

/* Perbaikan untuk text yang terpotong */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Mengatur spacing yang lebih baik */
.notification-item:last-child {
    margin-bottom: 0;
}

/* Hover effect untuk button */
.btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}
</style>
@endsection