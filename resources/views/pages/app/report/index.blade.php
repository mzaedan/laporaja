@extends('layouts.app')

@section('title', 'Daftar Pengaduan')

@section('content')
    <div class="py-3" id="reports">
        <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">{{ $reports->count(); }} List Pengaduan</p>

            <div class="dropdown">
                <button class="btn btn-filter dropdown-toggle" type="button" id="statusFilter" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-filter me-2"></i>
                    Filter Status
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statusFilter">
                    <li><a class="dropdown-item {{ !request()->status ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">Semua Status</a></li>
                    <li><a class="dropdown-item {{ request()->status === 'delivered' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'delivered']) }}">Terkirim</a></li>
                    <li><a class="dropdown-item {{ request()->status === 'in_process' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'in_process']) }}">Diproses</a></li>
                    <li><a class="dropdown-item {{ request()->status === 'completed' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}">Selesai</a></li>
                    <li><a class="dropdown-item {{ request()->status === 'rejected' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Ditolak</a></li>
                </ul>
            </div>
        </div>
        @if (request()->category)
            <p>Kategori {{ request()->category }}</p>
        @endif

   

        <div class="d-flex flex-column gap-3 mt-3">
            @foreach ($reports as $report)
                <div class="card card-report border-0 shadow-none">
                    <a href="{{ route('report.show', $report->code) }}" class="text-decoration-none text-dark">
                        <div class="card-body p-0">
                            <div class="card-report-image position-relative mb-2">
                                <img src="{{ asset('storage/' . $report->image) }}" alt="">

                                    @if ($report->reportStatuses->last()->status === 'delivered' )
                                        <div class="badge-status on-process">
                                            Terkirim
                                        </div>
                                    @endif

                                    @if ($report->reportStatuses->last()->status === 'in_process')
                                        <div class="badge-status on-process">
                                            Diproses
                                        </div>
                                    @endif

                                    @if ($report->reportStatuses->last()->status === 'completed')
                                        <div class="badge-status done">
                                            Selesai
                                        </div>
                                    @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div class="d-flex align-items-center ">
                                    <img src="{{ asset('assets/app/images/icons/MapPin.png') }}" alt="map pin" class="icon me-2">
                                    <p class="text-primary city">
                                        {{ $report->address }}
                                    </p>
                                </div>
                                <p class="text-secondary date">
                                    {{ \Carbon\Carbon::parse($report->created_at)->format('d M Y H:i') }}
                                </p>
                            </div>

                            <h1 class="card-title">
                                {{ $report->title }}
                            </h1>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection