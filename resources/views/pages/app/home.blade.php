@extends('layouts.app')

@section('title', 'Home')

@section('content')

@auth
    <h6 class="greeting">Hi, {{ Auth::user()->name ?? '' }}👋 </h6>
@endauth

@guest
    <h6 class="greeting">Selamat Datang, Di Aplikasi LaporAja</h6>
@endguest
    <h4 class="home-headline">Laporkan masalahmu dan kami segera atasi itu</h4>

    <div class="d-flex align-items-start justify-content-between gap-2 py-3 overflow-auto" id="category"
        style="white-space: nowrap;">

        @foreach ($categories as $category   )
             <a href="{{ route('report.index', ['category' => $category->name]) }}" class="category d-flex flex-column align-items-center text-decoration-none" style="min-width: 80px;">
                <div class="icon d-flex align-items-center justify-content-center" style="height: 60px; width: 60px; margin-bottom: 8px;">
                    <img src="{{ asset('storage/'.$category->image) }}" alt="icon" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                </div>
                <p class="text-center mb-0" style="font-size: 12px; line-height: 1.2;">{{ $category->name }}</p>
            </a>
        @endforeach
    </div>

    <div class="py-3" id="reports">
        <div class="d-flex justify-content-between align-items-center">
            <h6>Pengaduan terbaru</h6>
            <a href="{{route('report.index')}}" class="text-primary text-decoration-none show-more">
                Lihat semua
            </a>
        </div>

        <div class="d-flex flex-column gap-3 mt-3">
            @foreach ($reports as $report )
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
            @endforeach  
        </div>
    </div>
@endsection