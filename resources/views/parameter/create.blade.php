@extends('layouts.app')

@section('title', 'Tambah Parameter - SPK SMART')
@section('page-title', 'Tambah Parameter Kriteria')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Form Tambah Parameter
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Kriteria:</strong> {{ $kriteria->kode }} - {{ $kriteria->nama }}
                </div>
                
                <form action="{{ route('parameter.store') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Parameter <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('deskripsi') is-invalid @enderror" 
                               id="deskripsi" 
                               name="deskripsi" 
                               value="{{ old('deskripsi') }}"
                               placeholder="Contoh: < 100 kg atau Motor"
                               required>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nilai" class="form-label">Nilai (Skor) <span class="text-danger">*</span></label>
                        <select class="form-control @error('nilai') is-invalid @enderror" 
                                id="nilai" 
                                name="nilai" 
                                required>
                            <option value="">Pilih Nilai</option>
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('nilai') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                            @endfor
                        </select>
                        <small class="text-muted">Nilai 1 (terendah) sampai 5 (tertinggi)</small>
                        @error('nilai')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="batas_bawah" class="form-label">Batas Bawah</label>
                                <input type="number" 
                                       class="form-control @error('batas_bawah') is-invalid @enderror" 
                                       id="batas_bawah" 
                                       name="batas_bawah" 
                                       value="{{ old('batas_bawah') }}"
                                       step="0.01"
                                       placeholder="Kosongkan jika tidak ada">
                                <small class="text-muted">Untuk kriteria kategorikal, kosongkan</small>
                                @error('batas_bawah')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="batas_atas" class="form-label">Batas Atas</label>
                                <input type="number" 
                                       class="form-control @error('batas_atas') is-invalid @enderror" 
                                       id="batas_atas" 
                                       name="batas_atas" 
                                       value="{{ old('batas_atas') }}"
                                       step="0.01"
                                       placeholder="Kosongkan jika tidak ada">
                                <small class="text-muted">Untuk kriteria kategorikal, kosongkan</small>
                                @error('batas_atas')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('parameter.index') }}" class="btn btn-secondary">
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
                    <strong>Untuk kriteria numerikal (C1, C3, C4):</strong><br>
                    Isi batas bawah dan batas atas untuk mendefinisikan range nilai.
                </p>
                <p class="text-muted small">
                    <strong>Untuk kriteria kategorikal (C2):</strong><br>
                    Kosongkan batas bawah dan batas atas, cukup isi deskripsi dan nilai.
                </p>
                <p class="text-muted small mb-0">
                    <strong>Contoh:</strong><br>
                    • Deskripsi: "< 100 kg"<br>
                    • Nilai: 1<br>
                    • Batas Bawah: (kosong)<br>
                    • Batas Atas: 100
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
