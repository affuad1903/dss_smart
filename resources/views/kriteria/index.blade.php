@extends('layouts.app')

@section('title', 'Data Kriteria - SPK SMART')
@section('page-title', 'Data Kriteria Penilaian')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-check"></i> Daftar Kriteria Penilaian
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="80">No</th>
                        <th width="100">Kode</th>
                        <th>Nama Kriteria</th>
                        <th width="120" class="text-center">Tipe</th>
                        <th width="120" class="text-center">Bobot</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalBobot = 0; @endphp
                    @foreach($kriteria as $index => $item)
                    @php $totalBobot += $item->bobot; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-info">{{ $item->kode }}</span></td>
                        <td>{{ $item->nama }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item->tipe == 'benefit' ? 'bg-primary' : 'bg-danger' }}">
                                {{ ucfirst($item->tipe ?? 'benefit') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $item->bobot }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kriteria.edit', $item->id) }}" 
                               class="btn btn-sm btn-warning"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Bobot:</strong></td>
                        <td class="text-center">
                            <strong>
                                <span class="badge {{ $totalBobot == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ number_format($totalBobot, 2) }}
                                </span>
                            </strong>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Informasi Kriteria</h6>
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted mb-2">
                    <strong>Bobot kriteria</strong> menunjukkan tingkat kepentingan masing-masing kriteria 
                    dalam penilaian. Total bobot harus sama dengan <strong>1 (100%)</strong>.
                </p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Saat mengubah bobot, pastikan total tetap sama dengan 1.
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Tipe Kriteria:</h6>
                <ul class="text-muted small mb-3">
                    <li><strong>Benefit:</strong> Semakin besar nilainya semakin baik</li>
                    <li><strong>Cost:</strong> Semakin kecil nilainya semakin baik</li>
                </ul>
                <h6 class="text-primary">Penjelasan Kriteria:</h6>
                <ul class="text-muted small">
                    <li><strong>C1 (40%) - Benefit:</strong> Volume sampah lebih besar = lebih prioritas</li>
                    <li><strong>C2 (30%) - Benefit:</strong> Akses transportasi lebih besar = lebih prioritas</li>
                    <li><strong>C3 (20%) - Benefit:</strong> Kepadatan penduduk lebih tinggi = lebih prioritas</li>
                    <li><strong>C4 (10%) - Cost:</strong> Jarak ke TPA lebih dekat = lebih prioritas</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
