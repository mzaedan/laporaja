@extends('layouts.app')

@section('title', 'Laporanmu')

@section('content')
<ul class="nav nav-tabs">
    @foreach(['delivered' => 'Terkirim', 'in_process' => 'Diproses', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $tabStatus => $tabLabel)
    <li class="nav-item">
        <a class="nav-link {{ $status === $tabStatus ? 'active' : '' }}" 
           href="?status={{ $tabStatus }}">
           {{ $tabLabel }}
        </a>
    </li>
    @endforeach
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="terkirim-tab-pane" role="tabpanel" aria-labelledby="terkirim-tab" tabindex="0">
        <div class="d-flex flex-column gap-3 mt-3">
            @forelse ($reports as $report)
                <div class="card card-report border-0 shadow-none">
                    <a href="{{ route('report.show', $report->code) }}" class="text-decoration-none text-dark">
                        <div class="card-body p-0">
                            <div class="card-report-image position-relative mb-2">
                                <img src="{{ asset('storage/' . $report->image) }}" alt="">

                                @if ($report->reportStatuses->last()->status === 'delivered')
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

                                @if ($report->reportStatuses->last()->status === 'rejected')
                                    <div class="badge-status done">
                                        Ditolak
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('assets/app/images/icons/MapPin.png') }}" alt="map pin" class="icon me-2">
                                    <p class="text-primary city">
                                        {{ \Str::substr($report->address,0,20) }}...
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
            @empty
                <div class="d-flex flex-column justify-content-center align-items-center" style="height: 75vh" id="no-reports">
                    <div id="lottie"></div>
                    <h5 class="mt-3">Belum ada laporan</h5>
                    <a href="" class="btn btn-primary py-2 px-4 mt-3">
                        Buat Laporan
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        var animation = bodymovin.loadAnimation({
            container: document.getElementById('lottie'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset('assets/app/lottie/not-found.json') }}'
        })
    </script>
@endsection