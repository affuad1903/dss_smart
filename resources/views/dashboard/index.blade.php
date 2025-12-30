@extends('layouts.app')

@section('title', 'Dashboard - SPK SMART')
@section('page-title', 'Dashboard Utama')

@section('content')
<div class="row">
    <!-- Card Statistik -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 3rem; color: #667eea;">
                    <i class="bi bi-recycle"></i>
                </div>
                <h2 class="mt-3 mb-0">{{ $totalAlternatif }}</h2>
                <p class="text-muted mb-0">Total Data Alternatif</p>
                <small class="text-muted">(Bank Sampah)</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 3rem; color: #764ba2;">
                    <i class="bi bi-list-check"></i>
                </div>
                <h2 class="mt-3 mb-0">{{ $totalKriteria }}</h2>
                <p class="text-muted mb-0">Total Data Kriteria</p>
                <small class="text-muted">(Kriteria Penilaian)</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Tentang Sistem
            </div>
            <div class="card-body">
                <h5 class="mb-3">Sistem Pendukung Keputusan Penentuan Prioritas Bank Sampah Sebagai HUB Program 3R SMART</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-bullseye"></i> Tujuan Sistem</h6>
                        <p class="text-muted">
                            Sistem ini bertujuan untuk membantu dalam menentukan prioritas Bank Sampah terbaik 
                            yang dapat dijadikan sebagai HUB (Titik Pengumpulan) dalam Program 3R 
                            (Reduce, Reuse, Recycle) SMART.
                        </p>
                        
                        <h6 class="text-primary mt-4"><i class="bi bi-gear"></i> Metode SMART</h6>
                        <p class="text-muted">
                            SMART (Simple Multi Attribute Rating Technique) adalah metode pengambilan keputusan 
                            multi kriteria yang menggunakan normalisasi dan pembobotan untuk menentukan 
                            alternatif terbaik.
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-clipboard-check"></i> Fungsi Aplikasi</h6>
                        <ul class="text-muted">
                            <li>Mengelola data Bank Sampah (Alternatif)</li>
                            <li>Mengelola data Kriteria penilaian dan bobotnya</li>
                            <li>Mengelola parameter nilai untuk setiap kriteria</li>
                            <li>Melakukan penilaian untuk setiap Bank Sampah</li>
                            <li>Menghitung nilai utilitas menggunakan metode SMART</li>
                            <li>Menentukan ranking Bank Sampah berdasarkan nilai preferensi</li>
                        </ul>
                        
                        <div class="alert alert-info mt-3">
                            <strong><i class="bi bi-lightbulb"></i> Catatan:</strong><br>
                            Bank Sampah dengan nilai preferensi tertinggi adalah prioritas terbaik 
                            untuk dijadikan HUB Program 3R SMART.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ol"></i> Kriteria Penilaian
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-primary">C1 - Volume Timbulan Sampah Wilayah</h6>
                            <p class="text-muted mb-1">Bobot: 40%</p>
                            <small class="text-muted">Semakin besar volume sampah, semakin cocok menjadi HUB</small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-primary">C2 - Aksesibilitas Transportasi DLH</h6>
                            <p class="text-muted mb-1">Bobot: 30%</p>
                            <small class="text-muted">Kemudahan akses kendaraan Dinas Lingkungan Hidup</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-primary">C3 - Kepadatan Penduduk</h6>
                            <p class="text-muted mb-1">Bobot: 20%</p>
                            <small class="text-muted">Semakin padat penduduk, semakin strategis lokasinya</small>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-primary">C4 - Jarak Bank Sampah ke TPA</h6>
                            <p class="text-muted mb-1">Bobot: 10%</p>
                            <small class="text-muted">Semakin dekat ke TPA, semakin efisien distribusinya</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-arrow-right-circle"></i> Langkah-langkah Penggunaan
            </div>
            <div class="card-body">
                <ol class="text-muted">
                    <li class="mb-2">Tambahkan <strong>Data Alternatif</strong> (Bank Sampah yang akan dinilai)</li>
                    <li class="mb-2">Periksa <strong>Data Kriteria</strong> dan sesuaikan bobot jika diperlukan</li>
                    <li class="mb-2">Periksa <strong>Parameter Kriteria</strong> untuk setiap kriteria penilaian</li>
                    <li class="mb-2">Lakukan <strong>Penilaian</strong> untuk setiap Bank Sampah berdasarkan kriteria</li>
                    <li class="mb-2">Lihat <strong>Perhitungan</strong> metode SMART secara detail</li>
                    <li class="mb-2">Lihat <strong>Hasil Akhir</strong> dan ranking Bank Sampah terbaik</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
