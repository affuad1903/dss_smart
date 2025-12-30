@extends('layouts.app')

@section('title', 'Hasil Akhir - SPK SMART')
@section('page-title', 'Hasil Akhir dan Perankingan')

@section('content')
<!-- Tombol Export CSV -->
<div class="mb-3">
    <a href="{{ route('hasil.export.csv') }}" class="btn btn-success">
        <i class="bi bi-file-earmark-spreadsheet"></i> Export ke CSV
    </a>
    <small class="text-muted ms-2">
        <i class="bi bi-info-circle"></i> Export hasil perankingan untuk pelaporan
    </small>
</div>

<!-- Tabel Hasil Perhitungan -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-table"></i> Tabel Hasil Perhitungan Metode SMART
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Kode</th>
                        <th rowspan="2">Nama Bank Sampah</th>
                        <th colspan="{{ $kriteria->count() }}">Nilai Utilitas × Bobot</th>
                        <th rowspan="2">Nilai Preferensi (V)</th>
                    </tr>
                    <tr>
                        @foreach($kriteria as $krit)
                        <th>{{ $krit->kode }}<br><small>({{ $krit->bobot }})</small></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($nilaiPreferensi as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary">{{ $item['alternatif']->kode }}</span></td>
                        <td class="text-start">{{ $item['alternatif']->nama }}</td>
                        @foreach($kriteria as $krit)
                        @php
                            $utilitas = $item['utilitas'][$krit->kode];
                            $bobotUtilitas = $utilitas * $krit->bobot;
                        @endphp
                        <td>{{ number_format($bobotUtilitas, 4) }}</td>
                        @endforeach
                        <td><strong class="text-primary">{{ number_format($item['nilai_v'], 4) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="alert alert-warning mt-3 mb-0">
            <strong><i class="bi bi-lightbulb"></i> Rumus Nilai Preferensi (V):</strong><br>
            <code>V(a) = Σ (Bobot × Utilitas)</code><br><br>
            <strong>Keterangan:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>V(a)</strong> = Nilai preferensi alternatif (hasil akhir)</li>
                <li><strong>Bobot</strong> = Bobot kriteria (C1=0.4, C2=0.3, C3=0.2, C4=0.1)</li>
                <li><strong>Utilitas</strong> = Nilai utilitas yang telah dinormalisasi (0-1)</li>
                <li><strong>Σ</strong> = Penjumlahan untuk semua kriteria</li>
            </ul>
            <p class="mt-2 mb-0">
                Nilai V yang lebih tinggi menunjukkan alternatif yang lebih baik dan menjadi 
                prioritas utama sebagai HUB Program 3R SMART.
            </p>
        </div>
    </div>
</div>

<!-- Tabel Perankingan -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-trophy"></i> Perankingan Bank Sampah (Hasil Akhir)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light text-center">
                    <tr>
                        <th width="80">Ranking</th>
                        <th width="100">Kode</th>
                        <th>Nama Bank Sampah</th>
                        <th width="150">Nilai Preferensi (V)</th>
                        <th width="150">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $item)
                    <tr class="{{ $item['rank'] == 1 ? 'table-success' : '' }}">
                        <td class="text-center">
                            @if($item['rank'] == 1)
                                <span class="badge bg-warning text-dark" style="font-size: 1.2rem;">
                                    <i class="bi bi-trophy-fill"></i> #{{ $item['rank'] }}
                                </span>
                            @else
                                <span class="badge bg-secondary" style="font-size: 1.1rem;">
                                    #{{ $item['rank'] }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $item['alternatif']->kode }}</span>
                        </td>
                        <td>
                            <strong>{{ $item['alternatif']->nama }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info" style="font-size: 1.1rem;">
                                {{ number_format($item['nilai_v'], 4) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($item['rank'] == 1)
                                <span class="badge bg-success">
                                    <i class="bi bi-star-fill"></i> PRIORITAS UTAMA
                                </span>
                            @elseif($item['rank'] <= 3)
                                <span class="badge bg-primary">Prioritas Tinggi</span>
                            @else
                                <span class="badge bg-secondary">Prioritas Rendah</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Kesimpulan -->
<div class="card mt-3">
    <div class="card-header bg-success text-white">
        <i class="bi bi-check-circle"></i> Kesimpulan
    </div>
    <div class="card-body">
        @if($ranking && count($ranking) > 0)
        <div class="row">
            <div class="col-md-8">
                <h5 class="text-success">
                    <i class="bi bi-trophy-fill"></i> Bank Sampah Prioritas Terbaik
                </h5>
                <h4 class="mb-3">{{ $ranking[0]['alternatif']->nama }}</h4>
                <p class="text-muted">
                    Berdasarkan perhitungan metode SMART dengan mempertimbangkan 4 kriteria penilaian 
                    (Volume Timbulan Sampah, Aksesibilitas Transportasi, Kepadatan Penduduk, dan Jarak ke TPA), 
                    Bank Sampah <strong>{{ $ranking[0]['alternatif']->nama }}</strong> mendapatkan nilai 
                    preferensi tertinggi sebesar <strong>{{ number_format($ranking[0]['nilai_v'], 4) }}</strong>.
                </p>
                <div class="alert alert-success">
                    <i class="bi bi-star-fill"></i>
                    <strong>Rekomendasi:</strong> Bank Sampah ini sangat cocok dijadikan sebagai 
                    <strong>HUB (Titik Pengumpulan) Program 3R SMART</strong> karena memiliki nilai 
                    terbaik di antara semua alternatif yang dinilai.
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <div style="font-size: 4rem; color: #ffc107;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h3 class="text-success mb-2">Ranking #1</h3>
                        <h5>{{ $ranking[0]['alternatif']->kode }}</h5>
                        <p class="mb-0"><strong>{{ $ranking[0]['alternatif']->nama }}</strong></p>
                        <hr>
                        <h4 class="text-primary mb-0">{{ number_format($ranking[0]['nilai_v'], 4) }}</h4>
                        <small class="text-muted">Nilai Preferensi</small>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #ffc107;"></i>
            <p class="text-muted mt-3">
                Belum ada data penilaian. Silakan lakukan penilaian terlebih dahulu.
            </p>
            <a href="{{ route('penilaian.index') }}" class="btn btn-primary">
                <i class="bi bi-clipboard-data"></i> Mulai Penilaian
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Informasi Metode SMART -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Tentang Metode SMART</h6>
        <p class="text-muted mb-2">
            <strong>SMART (Simple Multi Attribute Rating Technique)</strong> adalah metode pengambilan 
            keputusan multi kriteria yang sederhana dan mudah dipahami. Metode ini menggunakan:
        </p>
        <ol class="text-muted">
            <li><strong>Pembobotan Kriteria:</strong> Setiap kriteria memiliki bobot kepentingan (total = 1)</li>
            <li><strong>Normalisasi:</strong> Nilai diubah ke skala 0-1 menggunakan min-max</li>
            <li><strong>Perhitungan Preferensi:</strong> Nilai akhir dihitung dengan perkalian bobot × utilitas</li>
            <li><strong>Perankingan:</strong> Alternatif diurutkan dari nilai preferensi tertinggi</li>
        </ol>
        <p class="text-muted mb-0">
            Hasil perhitungan ini dapat digunakan sebagai dasar pengambilan keputusan dalam menentukan 
            prioritas Bank Sampah yang akan dijadikan HUB Program 3R SMART.
        </p>
    </div>
</div>
@endsection
