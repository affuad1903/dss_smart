@extends('layouts.app')

@section('title', 'Input Penilaian - SPK SMART')
@section('page-title', 'Input Penilaian Batch')

@section('content')
<form action="{{ route('penilaian.store') }}" method="POST" id="formPenilaian">
    @csrf
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clipboard-data"></i> Form Penilaian Alternatif</span>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Semua
                </button>
                <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="alert alert-info">
                <strong><i class="bi bi-info-circle"></i> Informasi:</strong>
                <ul class="mb-0">
                    <li>Isi nilai untuk semua kriteria pada setiap alternatif</li>
                    <li>Sistem akan menghitung nilai parameter secara otomatis untuk kriteria numerikal</li>
                    <li>Lihat keterangan range parameter di bagian bawah setiap input</li>
                </ul>
            </div>

            @foreach($alternatif as $alt)
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>{{ $alt->kode }} - {{ $alt->nama }}</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($kriteria as $krit)
                        @php
                            $existingNilai = $existingPenilaian->where('alternatif_id', $alt->id)
                                ->where('kriteria_id', $krit->id)
                                ->first();
                        @endphp
                        <div class="col-md-6 mb-3">
                            <div class="border p-3 rounded h-100">
                                <label class="form-label fw-bold text-primary">
                                    {{ $krit->kode }} - {{ $krit->nama }}
                                    <span class="text-danger">*</span>
                                </label>
                                
                                @if($krit->kode == 'C2')
                                    <!-- Kriteria C2: Kategorikal (Aksesibilitas Transportasi) -->
                                    <select class="form-control @error('penilaian.*.nilai_aktual') is-invalid @enderror" 
                                            name="penilaian[{{ $alt->id }}_{{ $krit->id }}][nilai_aktual]" 
                                            onchange="updateNilaiParameterKategori(this, '{{ $alt->id }}_{{ $krit->id }}')"
                                            required>
                                        <option value="">Pilih Jenis Transportasi</option>
                                        @foreach($krit->parameters as $param)
                                        <option value="{{ $param->deskripsi }}" 
                                                data-nilai="{{ $param->nilai }}"
                                                {{ $existingNilai && $existingNilai->nilai_aktual == $param->deskripsi ? 'selected' : '' }}>
                                            {{ $param->deskripsi }} (Nilai: {{ $param->nilai }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" 
                                           name="penilaian[{{ $alt->id }}_{{ $krit->id }}][nilai_parameter]" 
                                           id="nilai_param_{{ $alt->id }}_{{ $krit->id }}"
                                           value="{{ $existingNilai->nilai_parameter ?? '' }}">
                                @else
                                    <!-- Kriteria Numerikal (C1, C3, C4) -->
                                    <input type="number" 
                                           class="form-control @error('penilaian.*.nilai_aktual') is-invalid @enderror" 
                                           name="penilaian[{{ $alt->id }}_{{ $krit->id }}][nilai_aktual]" 
                                           value="{{ $existingNilai->nilai_aktual ?? '' }}"
                                           step="0.01"
                                           placeholder="Masukkan nilai"
                                           required>
                                    <small class="text-muted">
                                        @if($krit->kode == 'C1')
                                            Masukkan volume sampah dalam kg
                                        @elseif($krit->kode == 'C3')
                                            Masukkan kepadatan penduduk dalam Jiwa/Km²
                                        @elseif($krit->kode == 'C4')
                                            Masukkan jarak ke TPA dalam Km
                                        @endif
                                    </small>
                                @endif
                                
                                <!-- Hidden fields -->
                                <input type="hidden" name="penilaian[{{ $alt->id }}_{{ $krit->id }}][alternatif_id]" value="{{ $alt->id }}">
                                <input type="hidden" name="penilaian[{{ $alt->id }}_{{ $krit->id }}][kriteria_id]" value="{{ $krit->id }}">
                                
                                <!-- Parameter range info -->
                                <div class="alert alert-secondary mt-2 mb-0 small">
                                    <strong>Range Parameter:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($krit->parameters as $param)
                                        <li>{{ $param->deskripsi }} = <strong>Nilai {{ $param->nilai }}</strong></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Simpan Semua Penilaian
                </button>
                <a href="{{ route('penilaian.index') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </div>
    </div>
</form>

<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-lightbulb"></i> Panduan Input Penilaian</h6>
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold">Kriteria Numerikal (C1, C3, C4):</h6>
                <ul class="text-muted small">
                    <li>Masukkan nilai aktual sesuai satuan yang diminta</li>
                    <li>Sistem akan mencocokkan dengan range parameter</li>
                    <li>Nilai parameter (1-5) ditentukan otomatis</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Kriteria Kategorikal (C2):</h6>
                <ul class="text-muted small">
                    <li>Pilih jenis transportasi dari dropdown</li>
                    <li>Nilai parameter sudah tertera pada pilihan</li>
                    <li>Nilai akan otomatis tersimpan saat dipilih</li>
                </ul>
            </div>
        </div>
        <div class="alert alert-warning mt-2 mb-0">
            <i class="bi bi-exclamation-triangle"></i> <strong>Catatan:</strong> 
            Pastikan semua field terisi sebelum menyimpan. Nilai parameter akan dihitung berdasarkan nilai aktual yang Anda masukkan.
        </div>
    </div>
</div>

<script>
// Update nilai parameter untuk kriteria kategorikal
function updateNilaiParameterKategori(selectElement, key) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const nilai = selectedOption.getAttribute('data-nilai');
    document.getElementById('nilai_param_' + key).value = nilai || '';
}

// Initialize nilai parameter untuk kriteria C2 yang sudah terisi
document.addEventListener('DOMContentLoaded', function() {
    const kategoriSelects = document.querySelectorAll('select[onchange*="updateNilaiParameterKategori"]');
    kategoriSelects.forEach(select => {
        if (select.value) {
            const key = select.getAttribute('onchange').match(/'([^']+)'/)[1];
            updateNilaiParameterKategori(select, key);
        }
    });
});

// Konfirmasi sebelum submit
document.getElementById('formPenilaian').addEventListener('submit', function(e) {
    const inputs = this.querySelectorAll('input[required], select[required]');
    let emptyCount = 0;
    
    inputs.forEach(input => {
        if (!input.value) {
            emptyCount++;
        }
    });
    
    if (emptyCount > 0) {
        if (!confirm(`Ada ${emptyCount} field yang belum terisi. Yakin ingin melanjutkan?`)) {
            e.preventDefault();
        }
    }
});
</script>

@endsection
