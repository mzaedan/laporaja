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
            <label for="community_impact_score" class="form-label">Jumlah Warga Terdampak</label>
            <select class="form-select @error('community_impact_score') is-invalid @enderror" 
                    id="community_impact_score" name="community_impact_score" onchange="calculatePriority()">
                <option value="1" {{ old('community_impact_score', 1) == 1 ? 'selected' : '' }}>1-5 orang</option>
                <option value="2" {{ old('community_impact_score', 1) == 2 ? 'selected' : '' }}>6-20 orang</option>
                <option value="3" {{ old('community_impact_score', 1) == 3 ? 'selected' : '' }}>21-50 orang</option>
                <option value="4" {{ old('community_impact_score', 1) == 4 ? 'selected' : '' }}>51-100 orang</option>
                <option value="5" {{ old('community_impact_score', 1) == 5 ? 'selected' : '' }}>Lebih dari 100 orang</option>
            </select>
            @error('community_impact_score')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="damage_level_score" class="form-label">Tingkat Kerusakan</label>
            <select class="form-select @error('damage_level_score') is-invalid @enderror" 
                    id="damage_level_score" name="damage_level_score" onchange="calculatePriority()">
                <option value="1" {{ old('damage_level_score', 1) == 1 ? 'selected' : '' }}>Kerusakan ringan</option>
                <option value="2" {{ old('damage_level_score', 1) == 2 ? 'selected' : '' }}>Kerusakan sedang</option>
                <option value="3" {{ old('damage_level_score', 1) == 3 ? 'selected' : '' }}>Kerusakan cukup parah</option>
                <option value="4" {{ old('damage_level_score', 1) == 4 ? 'selected' : '' }}>Kerusakan parah</option>
                <option value="5" {{ old('damage_level_score', 1) == 5 ? 'selected' : '' }}>Kerusakan sangat parah</option>
            </select>
            @error('damage_level_score')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="time_urgency_score" class="form-label">Tingkat Urgensi</label>
            <select class="form-select @error('time_urgency_score') is-invalid @enderror" 
                    id="time_urgency_score" name="time_urgency_score" onchange="calculatePriority()">
                <option value="1" {{ old('time_urgency_score', 1) == 1 ? 'selected' : '' }}>Tidak mendesak</option>
                <option value="2" {{ old('time_urgency_score', 1) == 2 ? 'selected' : '' }}>Kurang mendesak</option>
                <option value="3" {{ old('time_urgency_score', 1) == 3 ? 'selected' : '' }}>Cukup mendesak</option>
                <option value="4" {{ old('time_urgency_score', 1) == 4 ? 'selected' : '' }}>Mendesak</option>
                <option value="5" {{ old('time_urgency_score', 1) == 5 ? 'selected' : '' }}>Sangat mendesak</option>
            </select>
            @error('time_urgency_score')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Hidden priority score for form submission -->
        <input type="hidden" id="calculated_priority_score" name="calculated_priority_score" value="1.0">

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

    // Category-based auto-scoring mapping
    const categoryPriorityMap = {
        'Keamanan': 5,
        'Kesehatan': 5,
        'Infrastruktur': 4,
        'Lingkungan': 3,
        'Sosial': 3,
        'Administrasi': 2,
        'Lainnya': 2
    };

    function getCategoryScore() {
        const categorySelect = document.getElementById('report_category_id');
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const categoryName = selectedOption.text;
        
        // Find matching category in our priority map
        for (const [key, value] of Object.entries(categoryPriorityMap)) {
            if (categoryName.toLowerCase().includes(key.toLowerCase())) {
                return value;
            }
        }
        
        // Default score if no match found
        return 3;
    }

    function calculatePriority() {
        // Get values
        const categoryScore = getCategoryScore();
        const communityImpact = parseInt(document.getElementById('community_impact_score').value) || 1;
        const damageLevel = parseInt(document.getElementById('damage_level_score').value) || 1;
        const timeUrgency = parseInt(document.getElementById('time_urgency_score').value) || 1;
        
        // Calculate weighted score using the priority scoring formula
        const totalScore = (categoryScore * 0.45) + (communityImpact * 0.25) + (damageLevel * 0.20) + (timeUrgency * 0.10);
        
        // Update hidden field for form submission
        document.getElementById('calculated_priority_score').value = totalScore.toFixed(2);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial image from localStorage
        setFileInputFromBase64(imageBase64);
        
        // Calculate initial priority
        calculatePriority();
        
        // Add event listener for category change
        document.getElementById('report_category_id').addEventListener('change', calculatePriority);
    });
</script>
@endsection
