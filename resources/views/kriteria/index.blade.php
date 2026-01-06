@extends('layouts.app')

@section('title', 'Data Kriteria - SPK SMART')
@section('page-title', 'Data Kriteria Penilaian')

@section('content')
<!-- Notifikasi Warning untuk Total Bobot -->
@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Perhatian!</strong> {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-x-circle-fill"></i>
    <strong>Error!</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check"></i> Daftar Kriteria Penilaian</span>
        <a href="{{ route('kriteria.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Kriteria
        </a>
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
                        <th width="150" class="text-center">Aksi</th>
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
                            <div class="btn-group" role="group">
                                <a href="{{ route('kriteria.edit', $item->id) }}" 
                                   class="btn btn-sm btn-warning"
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kriteria.destroy', $item->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria {{ $item->kode }} - {{ $item->nama }}?\n\nPeringatan: Kriteria yang sudah digunakan dalam penilaian tidak dapat dihapus!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Bobot:</strong></td>
                        <td class="text-center">
                            <strong>
                                <span class="badge {{ abs($totalBobot - 1) < 0.01 ? 'bg-success' : 'bg-danger' }}">
                                    {{ number_format($totalBobot, 2) }}
                                </span>
                            </strong>
                        </td>
                        <td></td>
                    </tr>
                    @if(abs($totalBobot - 1) >= 0.01)
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="alert alert-danger mb-0 py-2">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>Peringatan:</strong> Total bobot tidak sama dengan 1! 
                                Perhitungan SMART membutuhkan total bobot = 1 untuk hasil yang akurat.
                                <br><small>Silakan edit bobot kriteria agar total menjadi 1.00</small>
                            </div>
                        </td>
                    </tr>
                    @endif
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
