@extends('layouts.app')

@section('title', 'Feedback Laporan')

@section('content')
<div class="min-vh-100 py-4">
    <div class="container" style="max-width: 40rem;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark fs-2 fw-bold mb-0">Feedback Laporan</h2>
        </div>
        <div class="bg-white rounded-4 shadow-lg overflow-hidden border p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('feedback.store', $report->id) }}" method="POST">
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
                    <div class="row g-0 justify-content-center mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="col-2 text-center">
                                <input class="form-check-input" type="radio" name="satisfaction" id="satisfaction{{ $i }}" value="{{ $i }}" {{ old('satisfaction') == $i ? 'checked' : '' }} required>
                                <label class="form-check-label d-block" for="satisfaction{{ $i }}">{{ $i }}</label>
                            </div>
                        @endfor
                    </div>
                    <div class="row">
                        <div class="col-6 text-start small">Sangat Tidak Puas</div>
                        <div class="col-6 text-end small">Sangat Puas</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill">Kirim Feedback</button>
            </form>
        </div>
    </div>
</div>
@endsection
