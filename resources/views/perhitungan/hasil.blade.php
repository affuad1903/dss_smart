@extends('layouts.app')

@section('title', 'Hasil Akhir - SPK SMART')
@section('page-title', 'Hasil Akhir dan Perankingan')

@section('content')
<!-- Info Alternatif Terpilih -->
<div class="alert alert-success">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <strong><i class="bi bi-check-circle"></i> Hasil Perhitungan untuk:</strong>
            <span class="badge bg-success ms-2">{{ $alternatif->count() }} Alternatif</span>
            <br>
            <small class="text-white">
                @foreach($alternatif as $alt)
                    <span class="badge bg-light text-dark me-1">{{ $alt->kode }}</span>
                @endforeach
            </small>
        </div>
        <form action="{{ route('perhitungan.index') }}" method="GET" class="d-inline">
            @foreach($alternatifIds as $id)
            <input type="hidden" name="alternatif_ids[]" value="{{ $id }}">
            @endforeach
            <button type="submit" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Lihat Proses Perhitungan
            </button>
        </form>
    </div>
</div>

<!-- Notifikasi -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Tombol Aksi -->
<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="{{ route('hasil.export.csv') }}" class="btn btn-success">
        <i class="bi bi-file-earmark-spreadsheet"></i> Export ke CSV
    </a>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#simpanHistoryModal">
        <i class="bi bi-save"></i> Simpan ke History
    </button>
    <a href="{{ route('history.index') }}" class="btn btn-info">
        <i class="bi bi-clock-history"></i> Lihat History
    </a>
    <small class="text-muted align-self-center ms-2">
        <i class="bi bi-info-circle"></i> Simpan hasil perhitungan untuk dokumentasi
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

<!-- Grafik Visualisasi -->
@if($ranking && count($ranking) > 0)
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-bar-chart-fill"></i> Grafik Visualisasi Perankingan
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Grafik Bar Chart -->
            <div class="col-md-12 mb-4">
                <h6 class="text-primary">Perbandingan Nilai Preferensi</h6>
                <canvas id="chartNilaiPreferensi" height="80"></canvas>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- Grafik Radar Chart -->
            <div class="col-md-6">
                <h6 class="text-primary">Perbandingan Top 5 Alternatif</h6>
                <canvas id="chartRadar"></canvas>
            </div>
            
            <!-- Grafik Pie Chart -->
            <div class="col-md-6">
                <h6 class="text-primary">Distribusi Nilai Preferensi</h6>
                <canvas id="chartPie"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

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

<!-- Modal Simpan History -->
<div class="modal fade" id="simpanHistoryModal" tabindex="-1" aria-labelledby="simpanHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="simpanHistoryModalLabel">
                    <i class="bi bi-save"></i> Simpan ke History Perhitungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('history.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Perhitungan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="judul" 
                               name="judul" 
                               placeholder="Contoh: Perhitungan Periode Januari 2026"
                               value="Perhitungan {{ date('d F Y H:i') }}"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" 
                                  id="keterangan" 
                                  name="keterangan" 
                                  rows="3"
                                  placeholder="Tambahkan catatan atau keterangan tentang perhitungan ini (opsional)"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <small>History akan menyimpan snapshot data saat ini termasuk alternatif, kriteria, penilaian, dan hasil perhitungan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

@if($ranking && count($ranking) > 0)
<script>
// Data untuk grafik
const labels = {!! json_encode(array_map(function($item) { 
    return $item['alternatif']->kode . ' - ' . (strlen($item['alternatif']->nama) > 20 ? substr($item['alternatif']->nama, 0, 20) . '...' : $item['alternatif']->nama); 
}, $ranking)) !!};

const nilaiPreferensi = {!! json_encode(array_map(function($item) { 
    return $item['nilai_v']; 
}, $ranking)) !!};

// Warna gradient untuk bar chart
const colors = nilaiPreferensi.map((value, index) => {
    if (index === 0) return 'rgba(255, 193, 7, 0.8)'; // Gold untuk rank 1
    if (index === 1) return 'rgba(192, 192, 192, 0.8)'; // Silver untuk rank 2
    if (index === 2) return 'rgba(205, 127, 50, 0.8)'; // Bronze untuk rank 3
    return 'rgba(75, 192, 192, 0.8)'; // Teal untuk lainnya
});

const borderColors = colors.map(color => color.replace('0.8', '1'));

// 1. Bar Chart - Nilai Preferensi
const ctxBar = document.getElementById('chartNilaiPreferensi').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Nilai Preferensi (V)',
            data: nilaiPreferensi,
            backgroundColor: colors,
            borderColor: borderColors,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            title: {
                display: true,
                text: 'Ranking Bank Sampah Berdasarkan Nilai Preferensi',
                font: {
                    size: 16,
                    weight: 'bold'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Nilai V: ' + context.parsed.y.toFixed(4);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 1,
                title: {
                    display: true,
                    text: 'Nilai Preferensi (V)'
                },
                ticks: {
                    callback: function(value) {
                        return value.toFixed(2);
                    }
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Alternatif Bank Sampah'
                }
            }
        }
    }
});

// Data untuk top 5 alternatif
const top5Labels = labels.slice(0, Math.min(5, labels.length));
const top5Data = nilaiPreferensi.slice(0, Math.min(5, nilaiPreferensi.length));

// 2. Radar Chart - Top 5 Alternatif
const ctxRadar = document.getElementById('chartRadar').getContext('2d');
new Chart(ctxRadar, {
    type: 'radar',
    data: {
        labels: top5Labels,
        datasets: [{
            label: 'Nilai Preferensi Top 5',
            data: top5Data,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            }
        },
        scales: {
            r: {
                beginAtZero: true,
                max: 1,
                ticks: {
                    stepSize: 0.2,
                    callback: function(value) {
                        return value.toFixed(1);
                    }
                }
            }
        }
    }
});

// 3. Pie Chart - Distribusi Nilai
const ctxPie = document.getElementById('chartPie').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: top5Labels,
        datasets: [{
            data: top5Data,
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(192, 192, 192, 0.8)',
                'rgba(205, 127, 50, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ],
            borderColor: [
                'rgba(255, 193, 7, 1)',
                'rgba(192, 192, 192, 1)',
                'rgba(205, 127, 50, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(2);
                        return context.label + ': ' + context.parsed.toFixed(4) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>
@endif
@endpush

@endsection
