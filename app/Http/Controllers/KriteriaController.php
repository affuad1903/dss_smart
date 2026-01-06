<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

/**
 * Controller untuk CRUD Data Kriteria
 */
class KriteriaController extends Controller
{
    /**
     * Tampilkan daftar kriteria
     */
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode')->get();
        return view('kriteria.index', compact('kriteria'));
    }

    /**
     * Tampilkan form tambah kriteria
     */
    public function create()
    {
        // Hitung total bobot kriteria yang sudah ada
        $totalBobotExisting = Kriteria::sum('bobot');
        $sisaBobot = 1 - $totalBobotExisting;
        
        return view('kriteria.create', compact('totalBobotExisting', 'sisaBobot'));
    }

    /**
     * Simpan kriteria baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:kriteria,kode',
            'nama' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:1',
            'tipe' => 'required|in:benefit,cost',
        ]);

        // Hitung total bobot setelah penambahan
        $totalBobot = Kriteria::sum('bobot') + $request->bobot;

        // Simpan kriteria baru
        Kriteria::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'bobot' => $request->bobot,
            'tipe' => $request->tipe,
        ]);

        // Berikan warning jika total bobot != 1
        if (round($totalBobot, 2) != 1.00) {
            return redirect()->route('kriteria.index')
                ->with('warning', 'Kriteria berhasil ditambahkan, namun total bobot saat ini adalah ' . 
                       number_format($totalBobot, 2) . '. Total bobot harus sama dengan 1 untuk perhitungan yang akurat!');
        }

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan. Total bobot: 1.00 ✓');
    }

    /**
     * Tampilkan form edit kriteria
     */
    public function edit(Kriteria $kriteria)
    {
        // Hitung total bobot kriteria lain (selain yang sedang diedit)
        $otherBobotSum = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
        
        return view('kriteria.edit', compact('kriteria', 'otherBobotSum'));
    }

    /**
     * Update kriteria (hanya bobot yang bisa diubah)
     */
    public function update(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'bobot' => 'required|numeric|min:0|max:1',
            'tipe' => 'required|in:benefit,cost',
        ]);

        // Hitung total bobot setelah update
        $totalBobot = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
        $totalBobot += $request->bobot;

        // Update kriteria
        $kriteria->update([
            'bobot' => $request->bobot,
            'tipe' => $request->tipe,
        ]);

        // Berikan warning jika total bobot != 1, tapi tetap simpan
        if (round($totalBobot, 2) != 1.00) {
            return redirect()->route('kriteria.index')
                ->with('warning', 'Bobot kriteria berhasil diperbarui, namun total bobot saat ini adalah ' . 
                       number_format($totalBobot, 2) . '. Total bobot harus sama dengan 1 untuk perhitungan yang akurat!');
        }

        return redirect()->route('kriteria.index')
            ->with('success', 'Bobot kriteria berhasil diperbarui. Total bobot: 1.00 ✓');
    }

    /**
     * Hapus kriteria
     */
    public function destroy(Kriteria $kriteria)
    {
        // Cek apakah kriteria ini sudah digunakan di penilaian
        if ($kriteria->penilaian()->count() > 0) {
            return redirect()->route('kriteria.index')
                ->with('error', 'Kriteria tidak dapat dihapus karena sudah digunakan dalam penilaian!');
        }

        // Cek apakah kriteria ini memiliki parameter
        if ($kriteria->parameters()->count() > 0) {
            return redirect()->route('kriteria.index')
                ->with('error', 'Kriteria tidak dapat dihapus karena masih memiliki parameter kriteria. Hapus parameter terlebih dahulu!');
        }

        $kode = $kriteria->kode;
        $kriteria->delete();

        // Hitung total bobot setelah penghapusan
        $totalBobot = Kriteria::sum('bobot');
        
        if (round($totalBobot, 2) != 1.00) {
            return redirect()->route('kriteria.index')
                ->with('warning', 'Kriteria ' . $kode . ' berhasil dihapus, namun total bobot saat ini adalah ' . 
                       number_format($totalBobot, 2) . '. Sesuaikan bobot kriteria lain agar total = 1.');
        }

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria ' . $kode . ' berhasil dihapus.');
    }
}
