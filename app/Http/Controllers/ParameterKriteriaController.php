<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\ParameterKriteria;
use Illuminate\Http\Request;

/**
 * Controller untuk CRUD Parameter Kriteria
 */
class ParameterKriteriaController extends Controller
{
    /**
     * Tampilkan daftar parameter kriteria
     */
    public function index()
    {
        $kriteria = Kriteria::with('parameters')->orderBy('kode')->get();
        return view('parameter.index', compact('kriteria'));
    }

    /**
     * Tampilkan form tambah parameter
     */
    public function create(Request $request)
    {
        $kriteriaId = $request->kriteria_id;
        $kriteria = Kriteria::findOrFail($kriteriaId);
        
        return view('parameter.create', compact('kriteria'));
    }

    /**
     * Simpan parameter baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'deskripsi' => 'required|string|max:255',
            'nilai' => 'required|integer|min:1|max:5',
            'batas_bawah' => 'nullable|numeric',
            'batas_atas' => 'nullable|numeric',
        ]);

        ParameterKriteria::create($request->all());

        return redirect()->route('parameter.index')
            ->with('success', 'Parameter kriteria berhasil ditambahkan');
    }

    /**
     * Tampilkan form edit parameter
     */
    public function edit(ParameterKriteria $parameter)
    {
        return view('parameter.edit', compact('parameter'));
    }

    /**
     * Update parameter
     */
    public function update(Request $request, ParameterKriteria $parameter)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
            'nilai' => 'required|integer|min:1|max:5',
            'batas_bawah' => 'nullable|numeric',
            'batas_atas' => 'nullable|numeric',
        ]);

        $parameter->update($request->all());

        return redirect()->route('parameter.index')
            ->with('success', 'Parameter kriteria berhasil diperbarui');
    }

    /**
     * Hapus parameter
     */
    public function destroy(ParameterKriteria $parameter)
    {
        $parameter->delete();

        return redirect()->route('parameter.index')
            ->with('success', 'Parameter kriteria berhasil dihapus');
    }
}
