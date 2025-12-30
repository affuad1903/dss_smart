<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\ParameterKriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

/**
 * Controller untuk Penilaian Alternatif
 */
class PenilaianController extends Controller
{
    /**
     * Tampilkan form penilaian
     */
    public function index()
    {
        $alternatif = Alternatif::get();
        $kriteria = Kriteria::with('parameters')->get();
        
        // Ambil data penilaian yang sudah ada
        $penilaian = Penilaian::with(['alternatif', 'kriteria'])->get();
        
        // Format data penilaian untuk ditampilkan dalam tabel
        $dataPenilaian = [];
        foreach ($alternatif as $alt) {
            $row = ['alternatif' => $alt];
            foreach ($kriteria as $krit) {
                $nilai = $penilaian->where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();
                $row[$krit->kode] = $nilai;
            }
            $dataPenilaian[] = $row;
        }

        return view('penilaian.index', compact('alternatif', 'kriteria', 'dataPenilaian'));
    }

    /**
     * Tampilkan form input nilai untuk alternatif dan kriteria tertentu
     */
    public function create(Request $request)
    {
        $alternatif = Alternatif::findOrFail($request->alternatif_id);
        $kriteria = Kriteria::with('parameters')->findOrFail($request->kriteria_id);
        
        // Cek apakah sudah ada penilaian
        $penilaian = Penilaian::where('alternatif_id', $alternatif->id)
            ->where('kriteria_id', $kriteria->id)
            ->first();

        return view('penilaian.create', compact('alternatif', 'kriteria', 'penilaian'));
    }

    /**
     * Simpan atau update penilaian
     */
    public function store(Request $request)
    {
        // Ambil kriteria untuk cek tipe validasi
        $kriteria = Kriteria::with('parameters')->findOrFail($request->kriteria_id);
        
        // Validasi berbeda untuk kriteria C2 (kategorikal) dan kriteria lainnya (numerikal)
        if ($kriteria->kode == 'C2') {
            // Kriteria C2: Aksesibilitas Transportasi (kategorikal)
            $request->validate([
                'alternatif_id' => 'required|exists:alternatif,id',
                'kriteria_id' => 'required|exists:kriteria,id',
                'nilai_aktual' => 'required|string',
                'nilai_parameter_manual' => 'required|integer|min:1|max:5',
            ]);
        } else {
            // Kriteria C1, C3, C4: Numerikal
            $request->validate([
                'alternatif_id' => 'required|exists:alternatif,id',
                'kriteria_id' => 'required|exists:kriteria,id',
                'nilai_aktual' => 'required|numeric',
            ]);
        }
        
        // Tentukan nilai parameter berdasarkan nilai aktual
        $nilaiParameter = $this->getNilaiParameter($kriteria, $request->nilai_aktual, $request->nilai_parameter_manual);

        // Simpan atau update penilaian
        Penilaian::updateOrCreate(
            [
                'alternatif_id' => $request->alternatif_id,
                'kriteria_id' => $request->kriteria_id,
            ],
            [
                'nilai_aktual' => $request->nilai_aktual,
                'nilai_parameter' => $nilaiParameter,
            ]
        );

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan');
    }

    /**
     * Tentukan nilai parameter berdasarkan nilai aktual
     */
    private function getNilaiParameter($kriteria, $nilaiAktual, $nilaiManual = null)
    {
        // Jika kriteria C2 (kategorikal), gunakan nilai manual
        if ($kriteria->kode == 'C2' && $nilaiManual) {
            return $nilaiManual;
        }

        // Untuk kriteria numerikal, cari parameter yang sesuai
        foreach ($kriteria->parameters as $param) {
            if ($param->isInRange($nilaiAktual)) {
                return $param->nilai;
            }
        }

        // Default return 1 jika tidak ditemukan
        return 1;
    }

    /**
     * Hapus penilaian
     */
    public function destroy($id)
    {
        $penilaian = Penilaian::findOrFail($id);
        $penilaian->delete();

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil dihapus');
    }
}
