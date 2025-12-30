@extends('layouts.app')

@section('title', 'Parameter Kriteria - SPK SMART')
@section('page-title', 'Parameter Kriteria Penilaian')

@section('content')
@foreach($kriteria as $krit)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <span class="badge bg-info">{{ $krit->kode }}</span>
            {{ $krit->nama }}
        </span>
        <a href="{{ route('parameter.create', ['kriteria_id' => $krit->id]) }}" 
           class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Parameter
        </a>
    </div>
    <div class="card-body">
        @if($krit->parameters->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Deskripsi Parameter</th>
                        <th width="100" class="text-center">Nilai</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($krit->parameters->sortBy('nilai') as $index => $param)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $param->deskripsi }}</td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $param->nilai }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('parameter.edit', $param->id) }}" 
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('parameter.destroy', $param->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus parameter ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-muted py-3">
            <i class="bi bi-inbox"></i> Belum ada parameter untuk kriteria ini
        </div>
        @endif
    </div>
</div>
@endforeach

<div class="card">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Informasi Parameter Kriteria</h6>
        <p class="text-muted">
            Parameter kriteria adalah range nilai atau kategori yang digunakan untuk memberikan 
            skor pada setiap kriteria. Nilai parameter berkisar antara 1-5, dimana nilai yang 
            lebih tinggi menunjukkan kondisi yang lebih baik/prioritas.
        </p>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-primary">Contoh Parameter C1 (Volume Sampah):</h6>
                <ul class="text-muted small">
                    <li>< 100 kg = Nilai 1</li>
                    <li>100-300 kg = Nilai 2</li>
                    <li>300-500 kg = Nilai 3</li>
                    <li>500-700 kg = Nilai 4</li>
                    <li>> 700 kg = Nilai 5</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Contoh Parameter C2 (Transportasi):</h6>
                <ul class="text-muted small">
                    <li>Motor = Nilai 1</li>
                    <li>Pick Up = Nilai 2</li>
                    <li>Truk Kecil = Nilai 3</li>
                    <li>Truk Besar = Nilai 4</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
