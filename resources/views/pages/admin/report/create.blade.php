@extends('layouts.admin')

@section('title', 'Tambah Data Laporan')

@section('content')


 <!-- Page Heading -->
<a href="{{ route('admin.report.index') }}" class="btn btn-danger mb-3">Kembali</a>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Laporan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.report.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="code">Kode</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="AUTO" disabled>
                <div class="invalid-feedback">
                    @error('code')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="resident">Pelapor/Masyarakat</label>
                <select name="resident_id" class="form-control @error('resident_id') is-invalid @enderror">
                    @foreach ($residents as $resident)
                        <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                            {{ $resident->user->email }} - {{ $resident->user->name }}
                        </option>
                    @endforeach
                </select>
                @error('resident_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="category">Kategori Laporan</label>
                <select name="report_category_id" class="form-control @error('report_category_id') is-invalid @enderror">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('report_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('report_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="title">Judul Laporan</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}">
                <div class="invalid-feedback">
                    @error('title')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi Laporan</label>
                <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" row=5></textarea>
                <div class="invalid-feedback">
                    @error('description')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="image">Bukti Laporan</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" value="{{old('image')}}">
                <div class="invalid-feedback">
                    @error('image')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{old('latitude')}}">
                <div class="invalid-feedback">
                    @error('latitude')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="longitude">longitude</label>
                <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{old('longitude')}}">
                <div class="invalid-feedback">
                    @error('longitude')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="address">Alamat Laporan</label>
                <textarea type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" row=5></textarea>
                <div class="invalid-feedback">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            
            <!-- Priority Scoring Section -->
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">📋 Informasi Prioritas Laporan</h6>
                    <small>Informasi ini membantu menentukan tingkat prioritas penanganan laporan</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="community_impact_score">Jumlah Warga Terdampak</label>
                                <select name="community_impact_score" class="form-control @error('community_impact_score') is-invalid @enderror" id="community_impact_score">
                                    <option value="">-- Pilih Jumlah Warga --</option>
                                    <option value="1" {{ old('community_impact_score') == '1' ? 'selected' : '' }}>1-5 orang</option>
                                    <option value="2" {{ old('community_impact_score') == '2' ? 'selected' : '' }}>6-20 orang</option>
                                    <option value="3" {{ old('community_impact_score') == '3' ? 'selected' : '' }}>21-50 orang</option>
                                    <option value="4" {{ old('community_impact_score') == '4' ? 'selected' : '' }}>51-100 orang</option>
                                    <option value="5" {{ old('community_impact_score') == '5' ? 'selected' : '' }}>Lebih dari 100 orang</option>
                                </select>
                                @error('community_impact_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="damage_level_score">Tingkat Kerusakan</label>
                                <select name="damage_level_score" class="form-control @error('damage_level_score') is-invalid @enderror" id="damage_level_score">
                                    <option value="">-- Pilih Tingkat Kerusakan --</option>
                                    <option value="1" {{ old('damage_level_score') == '1' ? 'selected' : '' }}>Kerusakan ringan</option>
                                    <option value="2" {{ old('damage_level_score') == '2' ? 'selected' : '' }}>Kerusakan sedang</option>
                                    <option value="3" {{ old('damage_level_score') == '3' ? 'selected' : '' }}>Kerusakan cukup parah</option>
                                    <option value="4" {{ old('damage_level_score') == '4' ? 'selected' : '' }}>Kerusakan parah</option>
                                    <option value="5" {{ old('damage_level_score') == '5' ? 'selected' : '' }}>Kerusakan sangat parah</option>
                                </select>
                                @error('damage_level_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="time_urgency_score">Tingkat Urgensi</label>
                                <select name="time_urgency_score" class="form-control @error('time_urgency_score') is-invalid @enderror" id="time_urgency_score">
                                    <option value="">-- Pilih Tingkat Urgensi --</option>
                                    <option value="1" {{ old('time_urgency_score') == '1' ? 'selected' : '' }}>Tidak mendesak</option>
                                    <option value="2" {{ old('time_urgency_score') == '2' ? 'selected' : '' }}>Kurang mendesak</option>
                                    <option value="3" {{ old('time_urgency_score') == '3' ? 'selected' : '' }}>Cukup mendesak</option>
                                    <option value="4" {{ old('time_urgency_score') == '4' ? 'selected' : '' }}>Mendesak</option>
                                    <option value="5" {{ old('time_urgency_score') == '5' ? 'selected' : '' }}>Sangat mendesak</option>
                                </select>
                                @error('time_urgency_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="scoring_notes">Catatan Penilaian</label>
                        <textarea class="form-control @error('scoring_notes') is-invalid @enderror" id="scoring_notes" name="scoring_notes" rows="3" placeholder="Jelaskan alasan penilaian prioritas (opsional)">{{ old('scoring_notes') }}</textarea>
                        @error('scoring_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info" id="priority_preview" style="display: none;">
                        <strong>Preview Prioritas:</strong>
                        <div id="priority_result"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Real-time priority calculation
    $('#category_score, #community_impact_score, #damage_level_score, #time_urgency_score').on('change', function() {
        calculatePriorityPreview();
    });
    
    function calculatePriorityPreview() {
        const category = parseInt($('#category_score').val()) || 0;
        const community = parseInt($('#community_impact_score').val()) || 0;
        const damage = parseInt($('#damage_level_score').val()) || 0;
        const urgency = parseInt($('#time_urgency_score').val()) || 0;
        
        if (category > 0 && community > 0 && damage > 0 && urgency > 0) {
            // Calculate priority score using the new formula: Category (45%) + Community (25%) + Damage (20%) + Urgency (10%)
            const score = (category * 0.45) + (community * 0.25) + (damage * 0.20) + (urgency * 0.10);
            
            // Determine priority level
            let level, color, textColor;
            if (score >= 4.5) {
                level = 'Kritis';
                color = '#dc3545';
                textColor = 'white';
            } else if (score >= 3.5) {
                level = 'Tinggi';
                color = '#fd7e14';
                textColor = 'white';
            } else if (score >= 2.5) {
                level = 'Sedang';
                color = '#ffc107';
                textColor = 'black';
            } else if (score >= 1.5) {
                level = 'Rendah';
                color = '#0d6efd';
                textColor = 'white';
            } else {
                level = 'Minimal';
                color = '#6c757d';
                textColor = 'white';
            }
            
            const resultHtml = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Score: ${score.toFixed(2)}</strong>
                        <span class="badge ml-2" style="background-color: ${color}; color: ${textColor};">${level}</span>
                    </div>
                    <small class="text-muted">
                        Breakdown: (${category} × 0.45) + (${community} × 0.25) + (${damage} × 0.20) + (${urgency} × 0.10)
                    </small>
                </div>
            `;
            
            $('#priority_result').html(resultHtml);
            $('#priority_preview').show();
        } else {
            $('#priority_preview').hide();
        }
    }
});
</script>
@endpush