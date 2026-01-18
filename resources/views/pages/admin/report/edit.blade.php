@extends('layouts.admin')

@section('title', 'Edit Data Laporan')

@section('content')

<!-- Page Heading -->
<a href="{{ route('admin.report.index') }}" class="btn btn-danger mb-3">Kembali</a>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Laporan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.report.update', $report->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code">Kode</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ $report->code }}" disabled>
            </div>

            <div class="form-group">
                <label for="resident">Pelapor/Masyarakat</label>
                <select name="resident_id" class="form-control @error('resident_id') is-invalid @enderror">
                    @foreach ($residents as $resident)
                        <option value="{{ $resident->id }}" 
                            {{ old('resident_id', $report->resident_id) == $resident->id ? 'selected' : '' }}>
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
                        <option value="{{ $category->id }}" 
                            {{ old('report_category_id', $report->report_category_id) == $category->id ? 'selected' : '' }}>
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
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                    value="{{ old('title', $report->title) }}">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Laporan</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $report->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">Bukti Laporan</label>
                <br />
                @if ($report->image)
                    <img src="{{ asset('storage/' . $report->image) }}" alt="Bukti Laporan" class="mt-2" width="200" style="padding-bottom: 20px;">
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude"
                    value="{{ old('latitude', $report->latitude) }}">
                @error('latitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude"
                    value="{{ old('longitude', $report->longitude) }}">
                @error('longitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Alamat Laporan</label>
                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="5">{{ old('address', $report->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Priority Scoring Section -->
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">🎯 Priority Scoring</h6>
                    <small>Update prioritas berdasarkan kriteria berikut</small>
                    @if($report->calculated_priority_score)
                        <div class="mt-2">
                            <span class="badge badge-light">Current Score: {{ $report->calculated_priority_score }}</span>
                            <span class="badge" style="background-color: {{ $report->priority_color === 'red' ? '#dc3545' : ($report->priority_color === 'orange' ? '#fd7e14' : ($report->priority_color === 'yellow' ? '#ffc107' : ($report->priority_color === 'blue' ? '#0d6efd' : '#6c757d'))) }}; color: {{ $report->priority_color === 'yellow' ? 'black' : 'white' }};">{{ $report->priority_level }}</span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="community_impact_score">Dampak ke Masyarakat (40%)</label>
                                <select name="community_impact_score" class="form-control @error('community_impact_score') is-invalid @enderror" id="community_impact_score">
                                    <option value="">-- Pilih Dampak --</option>
                                    <option value="1" {{ old('community_impact_score', $report->community_impact_score) == '1' ? 'selected' : '' }}>1 - Minimal (Beberapa individu)</option>
                                    <option value="2" {{ old('community_impact_score', $report->community_impact_score) == '2' ? 'selected' : '' }}>2 - Rendah (Kelompok kecil)</option>
                                    <option value="3" {{ old('community_impact_score', $report->community_impact_score) == '3' ? 'selected' : '' }}>3 - Sedang (Sebagian komunitas)</option>
                                    <option value="4" {{ old('community_impact_score', $report->community_impact_score) == '4' ? 'selected' : '' }}>4 - Tinggi (Mayoritas komunitas)</option>
                                    <option value="5" {{ old('community_impact_score', $report->community_impact_score) == '5' ? 'selected' : '' }}>5 - Sangat Tinggi (Seluruh komunitas)</option>
                                </select>
                                @error('community_impact_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="damage_level_score">Tingkat Kerusakan (30%)</label>
                                <select name="damage_level_score" class="form-control @error('damage_level_score') is-invalid @enderror" id="damage_level_score">
                                    <option value="">-- Pilih Kerusakan --</option>
                                    <option value="1" {{ old('damage_level_score', $report->damage_level_score) == '1' ? 'selected' : '' }}>1 - Minimal (Perbaikan kecil)</option>
                                    <option value="2" {{ old('damage_level_score', $report->damage_level_score) == '2' ? 'selected' : '' }}>2 - Ringan (Perbaikan minor)</option>
                                    <option value="3" {{ old('damage_level_score', $report->damage_level_score) == '3' ? 'selected' : '' }}>3 - Sedang (Perbaikan signifikan)</option>
                                    <option value="4" {{ old('damage_level_score', $report->damage_level_score) == '4' ? 'selected' : '' }}>4 - Parah (Rekonstruksi major)</option>
                                    <option value="5" {{ old('damage_level_score', $report->damage_level_score) == '5' ? 'selected' : '' }}>5 - Sangat Parah (Penggantian total)</option>
                                </select>
                                @error('damage_level_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="time_urgency_score">Urgensi Waktu (30%)</label>
                                <select name="time_urgency_score" class="form-control @error('time_urgency_score') is-invalid @enderror" id="time_urgency_score">
                                    <option value="">-- Pilih Urgensi --</option>
                                    <option value="1" {{ old('time_urgency_score', $report->time_urgency_score) == '1' ? 'selected' : '' }}>1 - Tidak Mendesak (Minggu/bulan)</option>
                                    <option value="2" {{ old('time_urgency_score', $report->time_urgency_score) == '2' ? 'selected' : '' }}>2 - Rendah (Beberapa hari)</option>
                                    <option value="3" {{ old('time_urgency_score', $report->time_urgency_score) == '3' ? 'selected' : '' }}>3 - Sedang (1-2 hari)</option>
                                    <option value="4" {{ old('time_urgency_score', $report->time_urgency_score) == '4' ? 'selected' : '' }}>4 - Tinggi (Beberapa jam)</option>
                                    <option value="5" {{ old('time_urgency_score', $report->time_urgency_score) == '5' ? 'selected' : '' }}>5 - Sangat Mendesak (Segera)</option>
                                </select>
                                @error('time_urgency_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="scoring_notes">Catatan Penilaian</label>
                        <textarea class="form-control @error('scoring_notes') is-invalid @enderror" id="scoring_notes" name="scoring_notes" rows="3" placeholder="Jelaskan alasan penilaian prioritas (opsional)">{{ old('scoring_notes', $report->scoring_notes) }}</textarea>
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

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Real-time priority calculation
    $('#community_impact_score, #damage_level_score, #time_urgency_score').on('change', function() {
        calculatePriorityPreview();
    });
    
    // Initialize preview if all fields have values
    calculatePriorityPreview();
    
    function calculatePriorityPreview() {
        const community = parseInt($('#community_impact_score').val()) || 0;
        const damage = parseInt($('#damage_level_score').val()) || 0;
        const urgency = parseInt($('#time_urgency_score').val()) || 0;
        
        if (community > 0 && damage > 0 && urgency > 0) {
            // Calculate priority score using the same formula as backend
            const score = (community * 0.4) + (damage * 0.3) + (urgency * 0.3);
            
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
                        <strong>New Score: ${score.toFixed(2)}</strong>
                        <span class="badge ml-2" style="background-color: ${color}; color: ${textColor};">${level}</span>
                    </div>
                    <small class="text-muted">
                        Breakdown: (${community} × 0.4) + (${damage} × 0.3) + (${urgency} × 0.3)
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
