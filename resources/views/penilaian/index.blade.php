@extends('layouts.app')

@section('title', 'Penilaian - SPK SMART')
@section('page-title', 'Penilaian Alternatif')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clipboard-data"></i> Pilih Alternatif untuk Dinilai</span>
        <button type="button" class="btn btn-primary btn-sm" id="btnProsesInput" disabled>
            <i class="bi bi-pencil-square"></i> Proses Input Penilaian
        </button>
    </div>
    <div class="card-body">
        @if($alternatif->count() > 0 && $kriteria->count() > 0)
        <form id="formChecklist" action="{{ route('penilaian.create') }}" method="GET">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th width="100">Kode</th>
                            <th>Nama Bank Sampah</th>
                            <th width="120" class="text-center">Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataPenilaian as $row)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="alternatif_ids[]" value="{{ $row['alternatif']->id }}" 
                                       class="form-check-input checkbox-alternatif">
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $row['alternatif']->kode }}</span>
                            </td>
                            <td>{{ $row['alternatif']->nama }}</td>
                            <td class="text-center">
                                @if($row['all_filled'])
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Lengkap
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-exclamation-circle"></i> Belum Lengkap
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info" 
                                        onclick="showDetail({{ $row['alternatif']->id }}, '{{ $row['alternatif']->nama }}')">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
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

<!-- Modal Detail Penilaian -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye"></i> Detail Penilaian - <span id="modalAlternatifNama"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="tableDetailPenilaian">
                    <thead class="table-light">
                        <tr>
                            <th>Kriteria</th>
                            <th width="150" class="text-center">Nilai Aktual</th>
                            <th width="100" class="text-center">Parameter</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Cara Penggunaan</h6>
        <ol class="text-muted mb-0">
            <li>Centang alternatif (Bank Sampah) yang ingin dinilai menggunakan checkbox</li>
            <li>Klik tombol <strong>"Proses Input Penilaian"</strong> di kanan atas</li>
            <li>Masukkan nilai untuk semua kriteria dalam satu halaman</li>
            <li>Simpan untuk menyimpan semua penilaian sekaligus</li>
            <li>Gunakan tombol <strong>"Detail"</strong> untuk melihat penilaian yang sudah ada</li>
        </ol>
    </div>
</div>

<script>
// Data penilaian untuk modal
const dataPenilaianJson = @json($dataPenilaian);
const kriteriaJson = @json($kriteria);

// Check/uncheck all
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.checkbox-alternatif');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateButtonState();
});

// Update button state saat checkbox berubah
document.querySelectorAll('.checkbox-alternatif').forEach(cb => {
    cb.addEventListener('change', updateButtonState);
});

function updateButtonState() {
    const checked = document.querySelectorAll('.checkbox-alternatif:checked').length;
    const btn = document.getElementById('btnProsesInput');
    btn.disabled = checked === 0;
    
    // Update check all state
    const total = document.querySelectorAll('.checkbox-alternatif').length;
    const checkAll = document.getElementById('checkAll');
    checkAll.checked = checked === total && total > 0;
    checkAll.indeterminate = checked > 0 && checked < total;
}

// Submit form saat tombol diklik
document.getElementById('btnProsesInput').addEventListener('click', function() {
    document.getElementById('formChecklist').submit();
});

// Show detail modal
function showDetail(alternatifId, alternatifNama) {
    document.getElementById('modalAlternatifNama').textContent = alternatifNama;
    
    // Find data
    const data = dataPenilaianJson.find(d => d.alternatif.id === alternatifId);
    
    // Build table
    let tbody = '';
    kriteriaJson.forEach(krit => {
        const nilai = data[krit.kode];
        tbody += `<tr>
            <td><strong>${krit.kode}</strong> - ${krit.nama}</td>
            <td class="text-center">
                ${nilai ? (krit.kode === 'C2' ? nilai.nilai_aktual : parseFloat(nilai.nilai_aktual).toFixed(2)) : '<span class="text-muted">Belum dinilai</span>'}
            </td>
            <td class="text-center">
                ${nilai ? `<span class="badge bg-success">${nilai.nilai_parameter}</span>` : '<span class="text-muted">-</span>'}
            </td>
        </tr>`;
    });
    
    document.querySelector('#tableDetailPenilaian tbody').innerHTML = tbody;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();
}
</script>

@endsection
