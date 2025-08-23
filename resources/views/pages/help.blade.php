@extends('layouts.app')

@section('title', 'Bantuan dan Dukungan - LapoRaja')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-light me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h4 class="mb-0 fw-bold">Bantuan dan Dukungan</h4>
    </div>

    <!-- Introduction Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body text-center py-4">
            <div class="mb-3">
                <div class="icon-circle bg-primary text-white mx-auto">
                    <i class="fas fa-info-circle"></i>
                </div>
            </div>
            <h5 class="card-title mb-3">Panduan Kategori Laporan</h5>
            <p class="text-secondary">
                Untuk membantu penanganan yang lebih efektif, laporan dikategorikan berdasarkan tingkat prioritas. 
                Silakan pilih kategori yang sesuai dengan kondisi yang ingin Anda laporkan.
            </p>
        </div>
    </div>

    <!-- Priority High -->
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Laporan Prioritas Tinggi
            </h5>
            <small class="text-light">Keamanan dan Keselamatan Publik</small>
        </div>
        <div class="card-body">
            <p class="text-danger fw-semibold mb-3">
                <i class="fas fa-clock me-2"></i>
                Perlu penanganan segera karena mengancam keselamatan warga
            </p>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-tree text-danger me-2"></i>
                    Pohon tumbang menutup jalan utama desa
                </li>
                <li class="mb-2">
                    <i class="fas fa-bridge-water text-danger me-2"></i>
                    Jembatan penghubung desa rusak parah dan tidak bisa dipakai
                </li>
                <li class="mb-2">
                    <i class="fas fa-fire text-danger me-2"></i>
                    Kebakaran kecil di rumah warga atau fasilitas umum
                </li>
                <li class="mb-2">
                    <i class="fas fa-bolt text-danger me-2"></i>
                    Kabel listrik putus dan membahayakan warga
                </li>
                <li class="mb-2">
                    <i class="fas fa-faucet-drip text-danger me-2"></i>
                    Pipa air bersih pecah sehingga warga tidak dapat akses air
                </li>
            </ul>
        </div>
    </div>

    <!-- Priority Medium -->
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-circle me-2"></i>
                Laporan Prioritas Sedang
            </h5>
            <small>Infrastruktur dan Fasilitas Umum</small>
        </div>
        <div class="card-body">
            <p class="text-warning fw-semibold mb-3">
                <i class="fas fa-clock me-2"></i>
                Masalah mulai mengganggu aktivitas warga, perlu ditangani dalam waktu dekat
            </p>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-road text-warning me-2"></i>
                    Jalan desa berlubang, tapi masih bisa dilewati
                </li>
                <li class="mb-2">
                    <i class="fas fa-water text-warning me-2"></i>
                    Selokan mampet menyebabkan genangan kecil
                </li>
                <li class="mb-2">
                    <i class="fas fa-trash text-warning me-2"></i>
                    Sampah menumpuk di TPS
                </li>
                <li class="mb-2">
                    <i class="fas fa-water text-warning me-2"></i>
                    Sampah di sungai
                </li>
                <li class="mb-2">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    Lampu jalan padam di beberapa titik
                </li>
                <li class="mb-2">
                    <i class="fas fa-building text-warning me-2"></i>
                    Atap balai desa bocor saat hujan
                </li>
            </ul>
        </div>
    </div>

    <!-- Priority Low -->
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Laporan Prioritas Rendah
            </h5>
            <small class="text-light">Administrasi dan Layanan Umum</small>
        </div>
        <div class="card-body">
            <p class="text-info fw-semibold mb-3">
                <i class="fas fa-clock me-2"></i>
                Masalah administratif yang perlu ditangani secara rutin
            </p>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="fas fa-file-alt text-info me-2"></i>
                    Keterlambatan pelayanan surat keterangan domisili
                </li>
                <li class="mb-2">
                    <i class="fas fa-edit text-info me-2"></i>
                    Kesalahan kecil dalam pencatatan data warga (misalnya salah tulis nama di daftar RT)
                </li>
                <li class="mb-2">
                    <i class="fas fa-shield-alt text-info me-2"></i>
                    Jadwal ronda malam tidak jalan sesuai giliran
                </li>
                <li class="mb-2">
                    <i class="fas fa-broom text-info me-2"></i>
                    Kebersihan balai RW kurang terjaga
                </li>
                <li class="mb-2">
                    <i class="fas fa-sign text-info me-2"></i>
                    Papan informasi warga sudah usang atau rusak ringan
                </li>
            </ul>
        </div>
    </div>

    <!-- Tips Card -->
    <div class="card mb-4 border-0 bg-light">
        <div class="card-body">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-lightbulb text-warning me-2"></i>
                Tips Membuat Laporan yang Efektif
            </h6>
            <ul class="list-unstyled text-description">
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Pilih kategori prioritas yang sesuai dengan kondisi lapangan
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Sertakan foto untuk memperjelas kondisi yang dilaporkan
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Tuliskan lokasi yang spesifik dan mudah ditemukan
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Berikan deskripsi yang jelas dan detail
                </li>
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Untuk laporan prioritas tinggi, segera hubungi petugas terkait
                </li>
            </ul>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-phone me-2"></i>
                Kontak Darurat
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Polisi</small>
                    <div class="fw-bold">110</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Pemadam Kebakaran</small>
                    <div class="fw-bold">113</div>
                </div>
                <div class="col-6 mt-2">
                    <small class="text-muted">Ambulans</small>
                    <div class="fw-bold">118</div>
                </div>
                <div class="col-6 mt-2">
                    <small class="text-muted">SAR</small>
                    <div class="fw-bold">115</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row g-2 mb-5">
        <div class="col-6">
            <a href="{{ route('report.create') }}" class="btn btn-primary w-100">
                <i class="fas fa-plus me-2"></i>
                Buat Laporan
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('report.myreport') }}" class="btn btn-outline-primary w-100">
                <i class="fas fa-list me-2"></i>
                Laporan Saya
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endsection