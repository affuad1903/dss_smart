<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\Request;

/**
 * Controller untuk CRUD Data Alternatif (Bank Sampah)
 */
class AlternatifController extends Controller
{
    /**
     * Tampilkan daftar alternatif
     */
    public function index()
    {
        $alternatif = Alternatif::get();
        return view('alternatif.index', compact('alternatif'));
    }

    /**
     * Tampilkan form tambah alternatif
     */
    public function create()
    {
        $kode = Alternatif::generateKode();
        return view('alternatif.create', compact('kode'));
    }

    /**
     * Simpan alternatif baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Alternatif::create([
            'kode' => Alternatif::generateKode(),
            'nama' => $request->nama,
        ]);

        return redirect()->route('alternatif.index')
            ->with('success', 'Data alternatif berhasil ditambahkan');
    }

    /**
     * Tampilkan form edit alternatif
     */
    public function edit(Alternatif $alternatif)
    {
        return view('alternatif.edit', compact('alternatif'));
    }

    /**
     * Update alternatif
     */
    public function update(Request $request, Alternatif $alternatif)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $alternatif->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('alternatif.index')
            ->with('success', 'Data alternatif berhasil diperbarui');
    }

    /**
     * Hapus alternatif
     */
    public function destroy(Alternatif $alternatif)
    {
        $alternatif->delete();

        return redirect()->route('alternatif.index')
            ->with('success', 'Data alternatif berhasil dihapus');
    }
}
