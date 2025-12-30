@extends('layouts.app')

@section('title', 'Edit Alternatif - SPK SMART')
@section('page-title', 'Edit Data Alternatif')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Form Edit Alternatif
            </div>
            <div class="card-body">
                <form action="{{ route('alternatif.update', $alternatif->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Alternatif</label>
                        <input type="text" 
                               class="form-control" 
                               id="kode" 
                               value="{{ $alternatif->kode }}" 
                               disabled>
                        <small class="text-muted">Kode tidak dapat diubah</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Bank Sampah <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama') is-invalid @enderror" 
                               id="nama" 
                               name="nama" 
                               value="{{ old('nama', $alternatif->nama) }}"
                               required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update
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
                <h6 class="text-primary"><i class="bi bi-info-circle"></i> Informasi</h6>
                <p class="text-muted small mb-0">
                    Anda hanya dapat mengubah nama Bank Sampah. Kode alternatif tidak dapat diubah 
                    untuk menjaga konsistensi data penilaian.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
