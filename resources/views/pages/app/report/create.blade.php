@extends('layouts.no-nav')

@section('title', 'Tambah Laporan')

@section('content')
<div class="max-w-screen-sm mx-auto bg-white min-vh-100 p-3">
    <h3 class="mb-3">Laporkan segera masalahmu di sini!</h3>

    <p class="text-description">
        Isi form di bawah ini dengan baik dan benar sehingga kami dapat memvalidasi dan menangani laporan anda secepatnya
    </p>

    <form action="{{ route('report.store') }}" method="POST" class="mt-4" enctype="multipart/form-data" enctype="multipart/form-data">
        @csrf
       <input type="hidden" id="latitude" name="latitude">
       <input type="hidden" id="longitude" name="longitude">

        <div class="mb-3">
            <label for="title" class="form-label">Judul Laporan</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}">
            @error('title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="report_category_id" class="form-label">Kategori Laporan</label>
            <select class="form-select @error('report_category_id') is-invalid @enderror" id="report_category_id" name="report_category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('report_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('report_category_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Bukti Laporan</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" style="display: none;">
            <img alt="image" id="image-preview" class="img-fluid rounded-2 mb-3 border" style="max-height: 300px; object-fit: cover;">
            @error('image')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Ceritakan Laporan Kamu</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="map" class="form-label">Lokasi Laporan</label>
            <div id="map" style="height: 300px;" class="border rounded"></div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Alamat Lengkap</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button class="btn btn-primary w-100 mt-2" type="submit">
            Laporkan
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    var imageBase64 = localStorage.getItem('image');

    function base64ToBlob(base64, mime) {
        var byteString = atob(base64.split(',')[1]);
        var ab = new ArrayBuffer(byteString.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        return new Blob([ab], { type: mime });
    }

    function setFileInputFromBase64(base64) {
        if (!base64) return;
        var blob = base64ToBlob(base64, 'image/jpeg');
        var file = new File([blob], 'image.jpg', { type: 'image/jpeg' });

        var imageInput = document.getElementById('image');
        var dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        imageInput.files = dataTransfer.files;

        var imagePreview = document.getElementById('image-preview');
        imagePreview.src = URL.createObjectURL(file);
    }

    setFileInputFromBase64(imageBase64);
</script>
@endsection
