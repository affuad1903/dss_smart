@extends('layouts.app')

@section('title', 'Tambah Alternatif - SPK SMART')
@section('page-title', 'Tambah Data Alternatif')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Form Tambah Alternatif
            </div>
            <div class="card-body">
                <form action="{{ route('alternatif.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Alternatif</label>
                        <input type="text" 
                               class="form-control" 
                               id="kode" 
                               value="{{ $kode }}" 
                               disabled>
                        <small class="text-muted">Kode akan dibuat otomatis</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Bank Sampah <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama') is-invalid @enderror" 
                               id="nama" 
                               name="nama" 
                               value="{{ old('nama') }}"
                               placeholder="Contoh: Bank Sampah Hijau Lestari"
                               required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('alternatif.index') }}" class="btn btn-secondary">
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
                <p class="text-muted small mb-0">
                    Masukkan nama Bank Sampah yang akan dinilai. Kode alternatif akan dibuat 
                    secara otomatis (A1, A2, A3, dst). Pastikan nama Bank Sampah jelas dan unik.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
