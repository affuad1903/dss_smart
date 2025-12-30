@extends('layouts.app')

@section('title', 'Edit Parameter - SPK SMART')
@section('page-title', 'Edit Parameter Kriteria')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Form Edit Parameter
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Kriteria:</strong> {{ $parameter->kriteria->kode }} - {{ $parameter->kriteria->nama }}
                </div>
                
                <form action="{{ route('parameter.update', $parameter->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Parameter <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('deskripsi') is-invalid @enderror" 
                               id="deskripsi" 
                               name="deskripsi" 
                               value="{{ old('deskripsi', $parameter->deskripsi) }}"
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
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('nilai', $parameter->nilai) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                            @endfor
                        </select>
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
                                       value="{{ old('batas_bawah', $parameter->batas_bawah) }}"
                                       step="0.01">
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
                                       value="{{ old('batas_atas', $parameter->batas_atas) }}"
                                       step="0.01">
                                @error('batas_atas')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update
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
                <h6 class="text-primary"><i class="bi bi-info-circle"></i> Informasi</h6>
                <p class="text-muted small mb-0">
                    Edit parameter kriteria sesuai kebutuhan. Pastikan nilai yang dimasukkan 
                    konsisten dengan parameter lainnya dalam kriteria yang sama.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
