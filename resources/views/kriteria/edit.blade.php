@extends('layouts.app')

@section('title', 'Edit Kriteria - SPK SMART')
@section('page-title', 'Edit Bobot Kriteria')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Form Edit Bobot Kriteria
            </div>
            <div class="card-body">
                <form action="{{ route('kriteria.update', $kriteria->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Kriteria</label>
                        <input type="text" 
                               class="form-control" 
                               id="kode" 
                               value="{{ $kriteria->kode }}" 
                               disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Kriteria</label>
                        <input type="text" 
                               class="form-control" 
                               id="nama" 
                               value="{{ $kriteria->nama }}" 
                               disabled>
                        <small class="text-muted">Nama kriteria tidak dapat diubah</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe Kriteria <span class="text-danger">*</span></label>
                        <select class="form-select @error('tipe') is-invalid @enderror" 
                                id="tipe" 
                                name="tipe" 
                                required>
                            <option value="benefit" {{ old('tipe', $kriteria->tipe ?? 'benefit') == 'benefit' ? 'selected' : '' }}>
                                Benefit (Semakin besar semakin baik)
                            </option>
                            <option value="cost" {{ old('tipe', $kriteria->tipe ?? 'benefit') == 'cost' ? 'selected' : '' }}>
                                Cost (Semakin kecil semakin baik)
                            </option>
                        </select>
                        <small class="text-muted">Pilih tipe kriteria sesuai dengan karakteristiknya</small>
                        @error('tipe')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="bobot" class="form-label">Bobot Kriteria <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('bobot') is-invalid @enderror" 
                               id="bobot" 
                               name="bobot" 
                               value="{{ old('bobot', $kriteria->bobot) }}"
                               step="0.01"
                               min="0"
                               max="1"
                               required>
                        <small class="text-muted">Masukkan nilai antara 0 dan 1 (contoh: 0.4 untuk 40%)</small>
                        @error('bobot')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Pastikan total bobot semua kriteria tetap sama dengan 1.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update
                        </button>
                        <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
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
                    Bobot menunjukkan tingkat kepentingan kriteria dalam penilaian. 
                    Nilai bobot harus antara 0 dan 1.
                </p>
                <p class="text-muted small mb-0">
                    <strong>Contoh:</strong><br>
                    0.4 = 40%<br>
                    0.3 = 30%<br>
                    0.2 = 20%<br>
                    0.1 = 10%
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
