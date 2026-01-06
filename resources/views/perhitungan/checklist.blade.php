@extends('layouts.app')

@section('title', 'Pilih Alternatif - SPK SMART')
@section('page-title', 'Pilih Alternatif untuk Perhitungan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-check2-square"></i> Pilih Alternatif untuk Dihitung</span>
        <button type="button" class="btn btn-primary btn-sm" id="btnProsesHitung" disabled>
            <i class="bi bi-calculator"></i> Proses Perhitungan
        </button>
    </div>
    <div class="card-body">
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if($semuaAlternatif->count() > 0 && $kriteria->count() > 0)
        <form id="formChecklist" action="{{ route('perhitungan.index') }}" method="GET">
            <div class="alert alert-info">
                <strong><i class="bi bi-info-circle"></i> Informasi:</strong>
                <ul class="mb-0">
                    <li>Pilih alternatif (Bank Sampah) yang akan dihitung dengan metode SMART</li>
                    <li>Anda dapat memilih beberapa atau semua alternatif sekaligus</li>
                    <li>Perhitungan hanya akan dilakukan untuk alternatif yang dipilih</li>
                    <li>Pastikan alternatif yang dipilih sudah memiliki penilaian lengkap</li>
                </ul>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th width="100">Kode</th>
                            <th>Nama Bank Sampah</th>
                            <th width="120" class="text-center">Status Penilaian</th>
                            <th width="150" class="text-center">Kelengkapan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semuaAlternatif as $alt)
                        @php
                            $jumlahPenilaian = \App\Models\Penilaian::where('alternatif_id', $alt->id)->count();
                            $jumlahKriteria = $kriteria->count();
                            $isLengkap = $jumlahPenilaian >= $jumlahKriteria;
                            $persentase = $jumlahKriteria > 0 ? round(($jumlahPenilaian / $jumlahKriteria) * 100) : 0;
                        @endphp
                        <tr class="{{ !$isLengkap ? 'table-warning' : '' }}">
                            <td class="text-center">
                                <input type="checkbox" 
                                       name="alternatif_ids[]" 
                                       value="{{ $alt->id }}" 
                                       class="form-check-input checkbox-alternatif"
                                       {{ !$isLengkap ? 'disabled' : '' }}>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $alt->kode }}</span>
                            </td>
                            <td>
                                {{ $alt->nama }}
                                @if(!$isLengkap)
                                    <br><small class="text-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Penilaian belum lengkap
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($isLengkap)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Lengkap
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-exclamation-circle"></i> {{ $jumlahPenilaian }}/{{ $jumlahKriteria }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar {{ $isLengkap ? 'bg-success' : 'bg-warning' }}" 
                                         role="progressbar" 
                                         style="width: {{ $persentase }}%"
                                         aria-valuenow="{{ $persentase }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $persentase }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <span class="text-muted">
                        <strong id="countSelected">0</strong> alternatif dipilih
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Penilaian
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit" disabled>
                        <i class="bi bi-calculator"></i> Lanjut ke Perhitungan
                    </button>
                </div>
            </div>
        </form>
        @else
        <div class="text-center py-5">
            <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #ffc107;"></i>
            <p class="text-muted mt-3">
                @if($semuaAlternatif->count() == 0)
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
        <h6 class="text-primary"><i class="bi bi-lightbulb"></i> Petunjuk</h6>
        <ol class="text-muted mb-0">
            <li>Centang alternatif yang akan dihitung menggunakan metode SMART</li>
            <li>Hanya alternatif dengan penilaian lengkap yang dapat dipilih</li>
            <li>Klik tombol <strong>"Lanjut ke Perhitungan"</strong> untuk memulai proses perhitungan</li>
            <li>Sistem akan menghitung nilai utilitas dan perankingan untuk alternatif terpilih</li>
            <li>Hasil perhitungan dapat dilihat, diexport, dan disimpan ke history</li>
        </ol>
        
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle"></i> <strong>Catatan:</strong>
            Alternatif yang belum memiliki penilaian lengkap (semua kriteria) tidak dapat dipilih dan akan ditandai dengan warna kuning.
        </div>
    </div>
</div>

<script>
// Check/uncheck all (hanya yang enabled)
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.checkbox-alternatif:not(:disabled)');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateButtonState();
});

// Update button state saat checkbox berubah
document.querySelectorAll('.checkbox-alternatif').forEach(cb => {
    cb.addEventListener('change', updateButtonState);
});

function updateButtonState() {
    const checked = document.querySelectorAll('.checkbox-alternatif:checked').length;
    const btnSubmit = document.getElementById('btnSubmit');
    const btnProses = document.getElementById('btnProsesHitung');
    const countSelected = document.getElementById('countSelected');
    
    btnSubmit.disabled = checked === 0;
    btnProses.disabled = checked === 0;
    countSelected.textContent = checked;
    
    // Update check all state
    const enabledCheckboxes = document.querySelectorAll('.checkbox-alternatif:not(:disabled)');
    const checkedEnabled = document.querySelectorAll('.checkbox-alternatif:not(:disabled):checked').length;
    const checkAll = document.getElementById('checkAll');
    
    checkAll.checked = checkedEnabled === enabledCheckboxes.length && enabledCheckboxes.length > 0;
    checkAll.indeterminate = checkedEnabled > 0 && checkedEnabled < enabledCheckboxes.length;
}

// Submit form saat tombol diklik
document.getElementById('btnProsesHitung').addEventListener('click', function() {
    document.getElementById('formChecklist').submit();
});

// Initialize state
updateButtonState();
</script>

@endsection
