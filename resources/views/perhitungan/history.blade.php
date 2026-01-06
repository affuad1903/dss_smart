@extends('layouts.app')

@section('title', 'History Perhitungan - SPK SMART')
@section('page-title', 'History Perhitungan')

@section('content')
<!-- Notifikasi -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Tombol Kembali -->
<div class="mb-3">
    <a href="{{ route('hasil.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Hasil
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-clock-history"></i> Daftar History Perhitungan
    </div>
    <div class="card-body">
        @if($history->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Judul</th>
                        <th width="150">Tanggal</th>
                        <th width="100" class="text-center">Alternatif</th>
                        <th width="100" class="text-center">Kriteria</th>
                        <th width="100" class="text-center">Total Bobot</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $index => $item)
                    <tr>
                        <td>{{ $history->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $item->judul }}</strong>
                            @if($item->keterangan)
                            <br><small class="text-muted">{{ Str::limit($item->keterangan, 50) }}</small>
                            @endif
                            <br><small class="text-muted"><i class="bi bi-person"></i> {{ $item->user_name }}</small>
                        </td>
                        <td>
                            <small>
                                {{ $item->created_at->format('d/m/Y') }}<br>
                                {{ $item->created_at->format('H:i') }} WIB
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $item->jumlah_alternatif }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $item->jumlah_kriteria }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $item->total_bobot_kriteria == 1 ? 'bg-success' : 'bg-warning' }}">
                                {{ number_format($item->total_bobot_kriteria, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('history.show', $item->id) }}" 
                                   class="btn btn-sm btn-info"
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('history.delete', $item->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus history ini?')">
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
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $history->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d;"></i>
            <p class="text-muted mt-3">Belum ada history perhitungan.</p>
            <a href="{{ route('hasil.index') }}" class="btn btn-primary">
                <i class="bi bi-calculator"></i> Mulai Perhitungan
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Informasi -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Informasi History</h6>
        <p class="text-muted mb-0">
            History menyimpan snapshot lengkap dari hasil perhitungan termasuk data alternatif, kriteria, 
            penilaian, dan hasil akhir pada waktu tertentu. Anda dapat melihat detail perhitungan dan 
            perankingan dari setiap history yang tersimpan.
        </p>
    </div>
</div>
@endsection
