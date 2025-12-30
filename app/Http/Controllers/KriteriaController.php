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
     * Tampilkan form edit kriteria
     */
    public function edit(Kriteria $kriteria)
    {
        return view('kriteria.edit', compact('kriteria'));
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

        // Validasi total bobot harus = 1
        $totalBobot = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
        $totalBobot += $request->bobot;

        if (round($totalBobot, 2) != 1.00) {
            return back()->withErrors([
                'bobot' => 'Total bobot semua kriteria harus sama dengan 1. Saat ini total: ' . $totalBobot
            ]);
        }

        $kriteria->update([
            'bobot' => $request->bobot,
            'tipe' => $request->tipe,
        ]);

        return redirect()->route('kriteria.index')
            ->with('success', 'Bobot kriteria berhasil diperbarui');
    }
}
