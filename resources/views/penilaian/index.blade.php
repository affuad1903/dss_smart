@extends('layouts.app')

@section('title', 'Penilaian - SPK SMART')
@section('page-title', 'Penilaian Alternatif')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-clipboard-data"></i> Tabel Penilaian Alternatif
    </div>
    <div class="card-body">
        @if($alternatif->count() > 0 && $kriteria->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" width="100">Kode</th>
                        <th rowspan="2">Nama Bank Sampah</th>
                        @foreach($kriteria as $krit)
                        <th>{{ $krit->kode }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($kriteria as $krit)
                        <th class="small">{{ $krit->nama }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataPenilaian as $row)
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $row['alternatif']->kode }}</span>
                        </td>
                        <td>{{ $row['alternatif']->nama }}</td>
                        @foreach($kriteria as $krit)
                        <td class="text-center">
                            @if(isset($row[$krit->kode]) && $row[$krit->kode])
                                <span class="badge bg-success">{{ $row[$krit->kode]->nilai_parameter }}</span>
                                <br>
                                <small class="text-muted">
                                    @if($krit->kode == 'C2')
                                        {{ $row[$krit->kode]->nilai_aktual }}
                                    @else
                                        ({{ number_format($row[$krit->kode]->nilai_aktual, 2) }})
                                    @endif
                                </small>
                                <br>
                                <a href="{{ route('penilaian.create', ['alternatif_id' => $row['alternatif']->id, 'kriteria_id' => $krit->id]) }}" 
                                   class="btn btn-sm btn-warning mt-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @else
                                <a href="{{ route('penilaian.create', ['alternatif_id' => $row['alternatif']->id, 'kriteria_id' => $krit->id]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus"></i> Nilai
                                </a>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #ffc107;"></i>
            <p class="text-muted mt-3">
                @if($alternatif->count() == 0)
                    Belum ada data alternatif. Silakan tambahkan data alternatif terlebih dahulu.
                @elseif($kriteria->count() == 0)
                    Belum ada data kriteria. Silakan tambahkan data kriteria terlebih dahulu.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Informasi Penilaian</h6>
        <p class="text-muted">
            Pada tabel penilaian, Anda perlu memberikan nilai untuk setiap alternatif (Bank Sampah) 
            berdasarkan setiap kriteria. Nilai yang ditampilkan adalah <strong>nilai parameter</strong> 
            (1-5) yang dihitung berdasarkan <strong>nilai aktual</strong> yang Anda masukkan.
        </p>
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Cara Penilaian:</h6>
                <ol class="text-muted small">
                    <li>Klik tombol <strong>"+ Nilai"</strong> pada sel yang ingin dinilai</li>
                    <li>Masukkan nilai aktual sesuai kriteria</li>
                    <li>Sistem akan menentukan nilai parameter secara otomatis</li>
                    <li>Nilai parameter akan digunakan dalam perhitungan SMART</li>
                </ol>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Penjelasan Nilai:</h6>
                <ul class="text-muted small">
                    <li><strong>Badge hijau:</strong> Nilai parameter (1-5)</li>
                    <li><strong>Angka dalam kurung:</strong> Nilai aktual yang diinputkan</li>
                    <li><strong>Tombol edit (pensil):</strong> Mengubah penilaian</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
