@extends('layouts.app')

@section('title', 'Tambah Kriteria - SPK SMART')
@section('page-title', 'Tambah Kriteria Baru')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Form Tambah Kriteria
            </div>
            <div class="card-body">
                <form action="{{ route('kriteria.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Kriteria <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('kode') is-invalid @enderror" 
                               id="kode" 
                               name="kode"
                               value="{{ old('kode') }}"
                               placeholder="Contoh: C5, C6, dst"
                               required>
                        <small class="text-muted">Kode unik untuk kriteria (tidak boleh sama dengan kriteria lain)</small>
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama') is-invalid @enderror" 
                               id="nama" 
                               name="nama"
                               value="{{ old('nama') }}"
                               placeholder="Contoh: Tingkat Kebersihan"
                               required>
                        <small class="text-muted">Nama deskriptif untuk kriteria penilaian</small>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe Kriteria <span class="text-danger">*</span></label>
                        <select class="form-select @error('tipe') is-invalid @enderror" 
                                id="tipe" 
                                name="tipe" 
                                required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="benefit" {{ old('tipe') == 'benefit' ? 'selected' : '' }}>
                                Benefit (Semakin besar semakin baik)
                            </option>
                            <option value="cost" {{ old('tipe') == 'cost' ? 'selected' : '' }}>
                                Cost (Semakin kecil semakin baik)
                            </option>
                        </select>
                        <small class="text-muted">Pilih tipe kriteria sesuai dengan karakteristiknya</small>
                        @error('tipe')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="bobot" class="form-label">Bobot Kriteria <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('bobot') is-invalid @enderror" 
                               id="bobot" 
                               name="bobot" 
                               value="{{ old('bobot', number_format($sisaBobot, 2, '.', '')) }}"
                               step="0.01"
                               min="0"
                               max="1"
                               required>
                        <small class="text-muted">
                            Masukkan nilai antara 0 dan 1 (contoh: 0.1 untuk 10%)
                            <br>
                            <strong>Sisa bobot yang tersedia: {{ number_format($sisaBobot, 2) }}</strong>
                        </small>
                        @error('bobot')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Real-time Total Bobot Calculator -->
                    <div id="totalBobotInfo" class="alert" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calculator"></i>
                                <strong>Total Bobot Setelah Penambahan:</strong>
                            </span>
                            <span id="totalBobotValue" class="badge fs-6"></span>
                        </div>
                        <small id="totalBobotMessage" class="d-block mt-2"></small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Info:</strong> Total bobot saat ini: <strong>{{ number_format($totalBobotExisting, 2) }}</strong>. 
                        Anda dapat menambahkan kriteria dengan bobot apapun, namun pastikan total bobot semua kriteria = 1 
                        sebelum melakukan perhitungan SMART.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">
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
                <h6 class="text-primary"><i class="bi bi-info-circle"></i> Panduan</h6>
                <p class="text-muted small">
                    Kriteria adalah faktor yang digunakan dalam penilaian. 
                    Setiap kriteria memiliki bobot yang menunjukkan tingkat kepentingannya.
                </p>
                
                <h6 class="text-primary mt-3">Tipe Kriteria:</h6>
                <ul class="text-muted small">
                    <li><strong>Benefit:</strong> Nilai besar = lebih baik<br>
                        <small>Contoh: Volume sampah, kepadatan penduduk</small>
                    </li>
                    <li><strong>Cost:</strong> Nilai kecil = lebih baik<br>
                        <small>Contoh: Jarak ke TPA, biaya operasional</small>
                    </li>
                </ul>
                
                <h6 class="text-primary mt-3">Contoh Bobot:</h6>
                <ul class="text-muted small mb-0">
                    <li>0.1 = 10% (kepentingan rendah)</li>
                    <li>0.2 = 20% (kepentingan sedang)</li>
                    <li>0.3 = 30% (kepentingan tinggi)</li>
                    <li>0.4 = 40% (kepentingan sangat tinggi)</li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="text-warning"><i class="bi bi-exclamation-triangle"></i> Perhatian</h6>
                <ul class="text-muted small mb-0">
                    <li>Kode kriteria harus <strong>unik</strong></li>
                    <li>Total bobot semua kriteria harus <strong>= 1</strong></li>
                    <li>Setelah menambah kriteria, Anda perlu menambahkan <strong>parameter kriteria</strong> untuk setiap kriteria</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bobotInput = document.getElementById('bobot');
    const totalBobotInfo = document.getElementById('totalBobotInfo');
    const totalBobotValue = document.getElementById('totalBobotValue');
    const totalBobotMessage = document.getElementById('totalBobotMessage');
    
    // Data bobot kriteria yang sudah ada (dari PHP)
    const totalBobotExisting = {{ $totalBobotExisting }};
    
    // Fungsi untuk menghitung dan menampilkan total bobot
    function updateTotalBobot() {
        const newBobot = parseFloat(bobotInput.value) || 0;
        const totalBobot = totalBobotExisting + newBobot;
        const totalBobotRounded = Math.round(totalBobot * 100) / 100;
        
        // Tampilkan info box
        totalBobotInfo.style.display = 'block';
        
        // Update nilai
        totalBobotValue.textContent = totalBobotRounded.toFixed(2);
        
        // Update warna dan pesan berdasarkan total
        if (totalBobotRounded === 1.00) {
            totalBobotInfo.className = 'alert alert-success';
            totalBobotValue.className = 'badge bg-success fs-6';
            totalBobotMessage.innerHTML = '<i class="bi bi-check-circle-fill"></i> Total bobot sudah benar (100%)';
        } else if (totalBobotRounded > 1.00) {
            totalBobotInfo.className = 'alert alert-warning';
            totalBobotValue.className = 'badge bg-warning text-dark fs-6';
            const selisih = (totalBobotRounded - 1.00).toFixed(2);
            totalBobotMessage.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Total bobot melebihi 1 sebesar ' + selisih + '. Total akan menjadi ' + (totalBobotRounded * 100).toFixed(0) + '%.';
        } else {
            totalBobotInfo.className = 'alert alert-warning';
            totalBobotValue.className = 'badge bg-warning text-dark fs-6';
            const selisih = (1.00 - totalBobotRounded).toFixed(2);
            totalBobotMessage.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Total bobot kurang dari 1 sebesar ' + selisih + '. Masih bisa menambah ' + (selisih * 100) + '% lagi.';
        }
    }
    
    // Event listener untuk input bobot
    bobotInput.addEventListener('input', updateTotalBobot);
    bobotInput.addEventListener('change', updateTotalBobot);
    
    // Jalankan saat halaman dimuat
    updateTotalBobot();
});
</script>
@endpush

@endsection
