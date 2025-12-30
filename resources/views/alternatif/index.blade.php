@extends('layouts.app')

@section('title', 'Data Alternatif - SPK SMART')
@section('page-title', 'Data Alternatif (Bank Sampah)')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-recycle"></i> Daftar Bank Sampah</span>
        <a href="{{ route('alternatif.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Alternatif
        </a>
    </div>
    <div class="card-body">
        @if($alternatif->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="80">No</th>
                        <th width="120">Kode</th>
                        <th>Nama Bank Sampah</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alternatif as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary">{{ $item->kode }}</span></td>
                        <td>{{ $item->nama }}</td>
                        <td class="text-center">
                            <a href="{{ route('alternatif.edit', $item->id) }}" 
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('alternatif.destroy', $item->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
            <p class="text-muted mt-3">Belum ada data alternatif</p>
            <a href="{{ route('alternatif.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Alternatif
            </a>
        </div>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Informasi</h6>
        <p class="text-muted mb-0">
            Alternatif adalah Bank Sampah yang akan dinilai dan dibandingkan untuk menentukan 
            prioritas terbaik sebagai HUB Program 3R SMART. Kode alternatif akan dibuat otomatis 
            (A1, A2, A3, dst).
        </p>
    </div>
</div>
@endsection
