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
     * Tampilkan halaman checklist alternatif
     */
    public function index()
    {
        $alternatif = Alternatif::all();
        $kriteria = Kriteria::with('parameters')->get();
        
        // Ambil data penilaian yang sudah ada
        $penilaian = Penilaian::with(['alternatif', 'kriteria'])->get();
        
        // Format data penilaian untuk ditampilkan dalam tabel
        $dataPenilaian = [];
        foreach ($alternatif as $alt) {
            $row = ['alternatif' => $alt];
            $allFilled = true;
            foreach ($kriteria as $krit) {
                $nilai = $penilaian->where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();
                $row[$krit->kode] = $nilai;
                if (!$nilai) {
                    $allFilled = false;
                }
            }
            $row['all_filled'] = $allFilled;
            $dataPenilaian[] = $row;
        }

        return view('penilaian.index', compact('alternatif', 'kriteria', 'dataPenilaian'));
    }

    /**
     * Tampilkan form input batch untuk alternatif yang dipilih
     */
    public function create(Request $request)
    {
        // Jika tidak ada alternatif_ids dari checklist, redirect ke index
        if (!$request->has('alternatif_ids') || empty($request->alternatif_ids)) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Silakan pilih alternatif terlebih dahulu');
        }

        $alternatifIds = $request->alternatif_ids;
        $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
        $kriteria = Kriteria::with('parameters')->orderBy('kode')->get();
        
        // Ambil penilaian yang sudah ada
        $existingPenilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();

        return view('penilaian.create', compact('alternatif', 'kriteria', 'existingPenilaian'));
    }

    /**
     * Simpan atau update penilaian batch
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'penilaian' => 'required|array',
            'penilaian.*.alternatif_id' => 'required|exists:alternatif,id',
            'penilaian.*.kriteria_id' => 'required|exists:kriteria,id',
            'penilaian.*.nilai_aktual' => 'required',
        ]);

        $kriteria = Kriteria::with('parameters')->get()->keyBy('id');
        $successCount = 0;

        foreach ($request->penilaian as $data) {
            $kriteriaItem = $kriteria->get($data['kriteria_id']);
            
            // Tentukan nilai parameter
            if ($kriteriaItem->kode == 'C2') {
                // Kategorikal - ambil dari input manual
                $nilaiParameter = $data['nilai_parameter'] ?? 1;
            } else {
                // Numerikal - hitung dari range
                $nilaiParameter = $this->getNilaiParameter($kriteriaItem, $data['nilai_aktual']);
            }

            // Simpan atau update penilaian
            Penilaian::updateOrCreate(
                [
                    'alternatif_id' => $data['alternatif_id'],
                    'kriteria_id' => $data['kriteria_id'],
                ],
                [
                    'nilai_aktual' => $data['nilai_aktual'],
                    'nilai_parameter' => $nilaiParameter,
                ]
            );
            
            $successCount++;
        }

        return redirect()->route('penilaian.index')
            ->with('success', "Berhasil menyimpan $successCount penilaian");
    }

    /**
     * Tentukan nilai parameter berdasarkan nilai aktual
     */
    private function getNilaiParameter($kriteria, $nilaiAktual, $nilaiManual = null)
    {
        // Jika kriteria C2 (kategorikal), gunakan nilai manual jika ada
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
