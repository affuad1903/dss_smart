@extends('layouts.app')

@section('title', 'Perhitungan SMART - SPK SMART')
@section('page-title', 'Perhitungan Metode SMART')

@section('content')
<!-- 1. Tabel Penilaian (Nilai Parameter) -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-table"></i> 1. Tabel Penilaian Alternatif (Nilai Parameter)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Bank Sampah</th>
                        @foreach($kriteria as $krit)
                        <th>{{ $krit->kode }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($tabelPenilaian as $row)
                    <tr>
                        <td><span class="badge bg-primary">{{ $row['alternatif']->kode }}</span></td>
                        <td class="text-start">{{ $row['alternatif']->nama }}</td>
                        @foreach($kriteria as $krit)
                        <td>{{ $row[$krit->kode] }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mt-3 mb-0">
            <strong><i class="bi bi-info-circle"></i> Penjelasan:</strong><br>
            Tabel ini menampilkan nilai parameter (1-5) untuk setiap alternatif berdasarkan 
            kriteria penilaian. Nilai ini didapat dari konversi nilai aktual ke dalam skala parameter.
        </div>
    </div>
</div>

<!-- 2. Nilai Ekstrem Kriteria -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-bar-chart"></i> 2. Nilai Ekstrem Kriteria (Min-Max)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th>Kriteria</th>
                        @foreach($kriteria as $krit)
                        <th>
                            {{ $krit->kode }}<br>
                            <small class="badge {{ $krit->tipe == 'benefit' ? 'bg-primary' : 'bg-danger' }}">
                                {{ ucfirst($krit->tipe ?? 'benefit') }}
                            </small>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td><strong>Minimum</strong></td>
                        @foreach($kriteria as $krit)
                        <td>{{ $nilaiEkstrem[$krit->kode]['min'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td><strong>Maksimum</strong></td>
                        @foreach($kriteria as $krit)
                        <td>{{ $nilaiEkstrem[$krit->kode]['max'] }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mt-3 mb-0">
            <strong><i class="bi bi-info-circle"></i> Penjelasan:</strong><br>
            Nilai minimum dan maksimum digunakan untuk normalisasi nilai dalam perhitungan 
            utilitas. Nilai ini merupakan nilai terkecil dan terbesar dari semua alternatif 
            untuk setiap kriteria.
        </div>
    </div>
</div>

<!-- 3. Nilai Utilitas (Normalisasi) -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-calculator"></i> 3. Nilai Utilitas (Normalisasi)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Bank Sampah</th>
                        @foreach($kriteria as $krit)
                        <th>
                            U({{ $krit->kode }})<br>
                            <small class="badge {{ $krit->tipe == 'benefit' ? 'bg-primary' : 'bg-danger' }}">
                                {{ ucfirst($krit->tipe ?? 'benefit') }}
                            </small>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($nilaiUtilitas as $row)
                    <tr>
                        <td><span class="badge bg-primary">{{ $row['alternatif']->kode }}</span></td>
                        <td class="text-start">{{ $row['alternatif']->nama }}</td>
                        @foreach($kriteria as $krit)
                        <td>{{ number_format($row[$krit->kode], 4) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="alert alert-warning mt-3 mb-0">
            <strong><i class="bi bi-lightbulb"></i> Rumus Normalisasi (Nilai Utilitas):</strong><br>
            <div class="mt-2">
                <strong>Untuk kriteria Benefit (semakin besar semakin baik):</strong><br>
                <code>U(a) = (Nilai - Min) / (Max - Min)</code>
            </div>
            <div class="mt-2">
                <strong>Untuk kriteria Cost (semakin kecil semakin baik):</strong><br>
                <code>U(a) = (Max - Nilai) / (Max - Min)</code>
            </div>
            <br>
            <strong>Keterangan:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>U(a)</strong> = Nilai utilitas alternatif</li>
                <li><strong>Nilai</strong> = Nilai parameter alternatif untuk kriteria tertentu</li>
                <li><strong>Min</strong> = Nilai minimum dari semua alternatif untuk kriteria tertentu</li>
                <li><strong>Max</strong> = Nilai maksimum dari semua alternatif untuk kriteria tertentu</li>
            </ul>
            <p class="mt-2 mb-0">
                Normalisasi mengubah nilai parameter ke skala 0-1 untuk memudahkan perbandingan 
                antar kriteria yang berbeda. Rumus berbeda digunakan untuk kriteria Benefit dan Cost.
            </p>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-primary mb-0">
                    <i class="bi bi-arrow-right-circle"></i> Langkah Selanjutnya
                </h6>
                <p class="text-muted small mb-0">
                    Lihat hasil akhir perhitungan dan perankingan Bank Sampah
                </p>
            </div>
            <a href="{{ route('hasil.index') }}" class="btn btn-primary">
                <i class="bi bi-trophy"></i> Lihat Hasil Akhir
            </a>
        </div>
    </div>
</div>
@endsection
