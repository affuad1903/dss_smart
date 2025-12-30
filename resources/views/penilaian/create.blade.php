@extends('layouts.app')

@section('title', 'Input Penilaian - SPK SMART')
@section('page-title', 'Input Penilaian')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-data"></i> 
                {{ $penilaian ? 'Edit' : 'Tambah' }} Penilaian
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Alternatif:</strong><br>
                            {{ $alternatif->kode }} - {{ $alternatif->nama }}
                        </div>
                        <div class="col-md-6">
                            <strong>Kriteria:</strong><br>
                            {{ $kriteria->kode }} - {{ $kriteria->nama }}
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('penilaian.store') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="alternatif_id" value="{{ $alternatif->id }}">
                    <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">
                    
                    @if($kriteria->kode == 'C2')
                    <!-- Kriteria C2: Kategorikal (Aksesibilitas Transportasi) -->
                    <div class="mb-3">
                        <label for="nilai_aktual" class="form-label">
                            Jenis Transportasi <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('nilai_aktual') is-invalid @enderror" 
                                id="nilai_aktual" 
                                name="nilai_aktual" 
                                onchange="updateNilaiParameter()"
                                required>
                            <option value="">Pilih Jenis Transportasi</option>
                            @foreach($kriteria->parameters as $param)
                            <option value="{{ $param->deskripsi }}" 
                                    data-nilai="{{ $param->nilai }}"
                                    {{ old('nilai_aktual', $penilaian->nilai_aktual ?? '') == $param->deskripsi ? 'selected' : '' }}>
                                {{ $param->deskripsi }} (Nilai: {{ $param->nilai }})
                            </option>
                            @endforeach
                        </select>
                        @error('nilai_aktual')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <input type="hidden" name="nilai_parameter_manual" id="nilai_parameter_manual" value="{{ old('nilai_parameter_manual', $penilaian->nilai_parameter ?? '') }}">
                    
                    <div class="alert alert-secondary">
                        <strong>Nilai Parameter:</strong> 
                        <span id="nilai_display" class="badge bg-success">
                            {{ $penilaian->nilai_parameter ?? '-' }}
                        </span>
                    </div>
                    
                    @else
                    <!-- Kriteria Numerikal (C1, C3, C4) -->
                    <div class="mb-3">
                        <label for="nilai_aktual" class="form-label">
                            Nilai Aktual <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control @error('nilai_aktual') is-invalid @enderror" 
                               id="nilai_aktual" 
                               name="nilai_aktual" 
                               value="{{ old('nilai_aktual', $penilaian->nilai_aktual ?? '') }}"
                               step="0.01"
                               required>
                        <small class="text-muted">
                            @if($kriteria->kode == 'C1')
                                Masukkan volume sampah dalam kg
                            @elseif($kriteria->kode == 'C3')
                                Masukkan kepadatan penduduk dalam Jiwa/Km²
                            @elseif($kriteria->kode == 'C4')
                                Masukkan jarak ke TPA dalam Km
                            @endif
                        </small>
                        @error('nilai_aktual')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-secondary">
                        <strong>Parameter Range untuk Kriteria {{ $kriteria->kode }}:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($kriteria->parameters as $param)
                            <li class="small">
                                {{ $param->deskripsi }} = Nilai {{ $param->nilai }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        @if($penilaian)
                        <form action="{{ route('penilaian.destroy', $penilaian->id) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus penilaian ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-primary"><i class="bi bi-info-circle"></i> Panduan</h6>
                <p class="text-muted small">
                    <strong>Nilai Aktual</strong> adalah nilai riil yang Anda ukur atau amati.
                </p>
                <p class="text-muted small">
                    <strong>Nilai Parameter</strong> adalah konversi dari nilai aktual ke skala 1-5 
                    berdasarkan range yang telah ditentukan. Nilai ini akan digunakan dalam 
                    perhitungan metode SMART.
                </p>
                <p class="text-muted small mb-0">
                    Sistem akan menentukan nilai parameter secara otomatis berdasarkan nilai 
                    aktual yang Anda masukkan.
                </p>
            </div>
        </div>
    </div>
</div>

@if($kriteria->kode == 'C2')
<script>
function updateNilaiParameter() {
    const select = document.getElementById('nilai_aktual');
    const selectedOption = select.options[select.selectedIndex];
    const nilai = selectedOption.getAttribute('data-nilai');
    
    document.getElementById('nilai_parameter_manual').value = nilai || '';
    document.getElementById('nilai_display').textContent = nilai || '-';
}

// Update saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    updateNilaiParameter();
});
</script>
@endif
@endsection
