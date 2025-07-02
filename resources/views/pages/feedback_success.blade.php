@extends('layouts.no-nav')

@section('title', 'Feedback Berhasil Dikirim')

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center vh-75">
    <div id="lottie"></div>

    <h6 class="fw-bold text-center mb-2">Terima kasih telah mengisi feedback!</h6>
    <p class="text-center mb-4">Masukan Anda sangat berarti untuk meningkatkan layanan kami.</p>

    <a href="{{ route('home') }}" class="btn btn-primary py-2 px-4">
        Kembali ke Beranda
    </a>
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
        path: '{{ asset('assets/app/lottie/feedback-success.json') }}'
    })
</script>
@endsection
