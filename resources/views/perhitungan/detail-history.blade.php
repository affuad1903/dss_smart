@extends('layouts.app')

@section('title', 'Detail History - SPK SMART')
@section('page-title', 'Detail History Perhitungan')

@section('content')
<!-- Tombol Kembali -->
<div class="mb-3">
    <a href="{{ route('history.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke History
    </a>
</div>

<!-- Informasi Perhitungan -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-info-circle"></i> Informasi Perhitungan
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="150">Judul:</th>
                        <td><strong>{{ $hasil->judul }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal:</th>
                        <td>{{ $hasil->created_at->format('d F Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <th>User:</th>
                        <td>{{ $hasil->user_name }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="150">Jumlah Alternatif:</th>
                        <td><span class="badge bg-primary">{{ $hasil->jumlah_alternatif }}</span></td>
                    </tr>
                    <tr>
                        <th>Jumlah Kriteria:</th>
                        <td><span class="badge bg-info">{{ $hasil->jumlah_kriteria }}</span></td>
                    </tr>
                    <tr>
                        <th>Total Bobot:</th>
                        <td>
                            <span class="badge {{ $hasil->total_bobot_kriteria == 1 ? 'bg-success' : 'bg-warning' }}">
                                {{ number_format($hasil->total_bobot_kriteria, 2) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if($hasil->keterangan)
        <div class="alert alert-light">
            <strong>Keterangan:</strong> {{ $hasil->keterangan }}
        </div>
        @endif
    </div>
</div>

<!-- Hasil Perankingan -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-trophy"></i> Hasil Perankingan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light text-center">
                    <tr>
                        <th width="80">Ranking</th>
                        <th width="100">Kode</th>
                        <th>Nama Alternatif</th>
                        <th width="150">Nilai Preferensi (V)</th>
                        <th width="150">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasil->hasil_akhir as $item)
                    <tr class="{{ $item['rank'] == 1 ? 'table-success' : ($item['rank'] <= 3 ? 'table-warning' : '') }}">
                        <td class="text-center">
                            @if($item['rank'] == 1)
                                <span class="badge bg-warning text-dark fs-5">
                                    <i class="bi bi-trophy-fill"></i> {{ $item['rank'] }}
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6">{{ $item['rank'] }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $item['alternatif']['kode'] }}</span>
                        </td>
                        <td><strong>{{ $item['alternatif']['nama'] }}</strong></td>
                        <td class="text-center">
                            <span class="badge bg-success fs-6">{{ number_format($item['nilai_v'], 4) }}</span>
                        </td>
                        <td class="text-center">
                            @if($item['rank'] == 1)
                                <span class="badge bg-success">PRIORITAS UTAMA</span>
                            @elseif($item['rank'] <= 3)
                                <span class="badge bg-warning text-dark">Prioritas Tinggi</span>
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

<!-- Grafik Visualisasi History -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-bar-chart-fill"></i> Grafik Visualisasi Hasil
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h6 class="text-primary">Grafik Perankingan</h6>
                <canvas id="chartHistory" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Data Kriteria yang Digunakan -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-list-check"></i> Kriteria yang Digunakan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th width="80">Kode</th>
                        <th>Nama Kriteria</th>
                        <th width="120">Tipe</th>
                        <th width="120">Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasil->data_kriteria as $krit)
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $krit['kode'] }}</span>
                        </td>
                        <td>{{ $krit['nama'] }}</td>
                        <td class="text-center">
                            <span class="badge {{ $krit['tipe'] == 'benefit' ? 'bg-primary' : 'bg-danger' }}">
                                {{ ucfirst($krit['tipe']) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $krit['bobot'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Daftar Alternatif -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-building"></i> Alternatif yang Dinilai
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($hasil->data_alternatif as $alt)
            <div class="col-md-6 mb-2">
                <div class="border rounded p-2">
                    <span class="badge bg-primary">{{ $alt['kode'] ?? 'N/A' }}</span>
                    <strong>{{ $alt['nama'] ?? 'Tidak ada nama' }}</strong>
                    @if(isset($alt['alamat']) && $alt['alamat'])
                    <br><small class="text-muted">{{ $alt['alamat'] }}</small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Kesimpulan -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-success"><i class="bi bi-check-circle"></i> Kesimpulan</h6>
        @php
            $pemenang = $hasil->hasil_akhir[0];
        @endphp
        <p class="mb-0">
            Berdasarkan perhitungan metode SMART pada <strong>{{ $hasil->created_at->format('d F Y') }}</strong>, 
            alternatif <strong>{{ $pemenang['alternatif']['kode'] }} - {{ $pemenang['alternatif']['nama'] }}</strong> 
            mendapatkan nilai preferensi tertinggi sebesar <strong>{{ number_format($pemenang['nilai_v'], 4) }}</strong> 
            dan menjadi <strong class="text-success">PRIORITAS UTAMA</strong> sebagai HUB Program 3R SMART.
        </p>
    </div>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Data dari history
const historyData = @json($hasil->hasil_akhir);

const labels = historyData.map(item => {
    const nama = item.alternatif.nama;
    return item.alternatif.kode + ' - ' + (nama.length > 20 ? nama.substring(0, 20) + '...' : nama);
});

const nilaiPreferensi = historyData.map(item => item.nilai_v);

// Warna gradient
const colors = nilaiPreferensi.map((value, index) => {
    if (index === 0) return 'rgba(255, 193, 7, 0.8)'; // Gold
    if (index === 1) return 'rgba(192, 192, 192, 0.8)'; // Silver
    if (index === 2) return 'rgba(205, 127, 50, 0.8)'; // Bronze
    return 'rgba(75, 192, 192, 0.8)';
});

const borderColors = colors.map(color => color.replace('0.8', '1'));

// Bar Chart
const ctx = document.getElementById('chartHistory').getContext('2d');
new Chart(ctx, {
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
                text: 'Hasil Perankingan - {{ $hasil->judul }}',
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
</script>
@endpush

@endsection
