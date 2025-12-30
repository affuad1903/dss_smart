<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

/**
 * Controller untuk Perhitungan dan Hasil Akhir Metode SMART
 */
class PerhitunganController extends Controller
{
    /**
     * Tampilkan halaman perhitungan
     */
    public function index()
    {
        $alternatif = Alternatif::get();
        $kriteria = Kriteria::get();
        $penilaian = Penilaian::all();

        // 1. TABEL PENILAIAN (Nilai Parameter)
        $tabelPenilaian = $this->getTabelPenilaian($alternatif, $kriteria, $penilaian);

        // 2. NILAI EKSTREM (Min-Max per kriteria)
        $nilaiEkstrem = $this->getNilaiEkstrem($kriteria, $penilaian);

        // 3. NILAI UTILITAS (Normalisasi)
        $nilaiUtilitas = $this->getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem);

        return view('perhitungan.index', compact(
            'alternatif',
            'kriteria',
            'tabelPenilaian',
            'nilaiEkstrem',
            'nilaiUtilitas'
        ));
    }

    /**
     * Tampilkan halaman hasil akhir dan perankingan
     */
    public function hasil()
    {
        $alternatif = Alternatif::get();
        $kriteria = Kriteria::get();
        $penilaian = Penilaian::all();

        // Hitung nilai utilitas
        $nilaiEkstrem = $this->getNilaiEkstrem($kriteria, $penilaian);
        $nilaiUtilitas = $this->getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem);

        // 4. NILAI PREFERENSI (V)
        $nilaiPreferensi = $this->getNilaiPreferensi($alternatif, $kriteria, $nilaiUtilitas);

        // 5. RANKING (Urutan dari V terbesar)
        $ranking = $this->getRanking($nilaiPreferensi);

        return view('perhitungan.hasil', compact(
            'alternatif',
            'kriteria',
            'nilaiUtilitas',
            'nilaiPreferensi',
            'ranking'
        ));
    }

    /**
     * Mendapatkan tabel penilaian (nilai parameter)
     */
    private function getTabelPenilaian($alternatif, $kriteria, $penilaian)
    {
        $tabel = [];
        
        foreach ($alternatif as $alt) {
            $row = ['alternatif' => $alt];
            
            foreach ($kriteria as $krit) {
                $nilai = $penilaian->where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();
                
                $row[$krit->kode] = $nilai ? $nilai->nilai_parameter : 0;
            }
            
            $tabel[] = $row;
        }
        
        return $tabel;
    }

    /**
     * Mendapatkan nilai ekstrem (min-max) per kriteria
     */
    private function getNilaiEkstrem($kriteria, $penilaian)
    {
        $ekstrem = [];
        
        foreach ($kriteria as $krit) {
            $nilaiKriteria = $penilaian->where('kriteria_id', $krit->id)
                ->pluck('nilai_parameter');
            
            $ekstrem[$krit->kode] = [
                'min' => $nilaiKriteria->min() ?? 0,
                'max' => $nilaiKriteria->max() ?? 0,
            ];
        }
        
        return $ekstrem;
    }

    /**
     * Menghitung nilai utilitas (normalisasi)
     * 
     * Rumus SMART:
     * - Untuk kriteria BENEFIT (semakin besar semakin baik):
     *   U(a) = (Nilai - Min) / (Max - Min)
     * 
     * - Untuk kriteria COST (semakin kecil semakin baik):
     *   U(a) = (Max - Nilai) / (Max - Min)
     * 
     * Dimana:
     * - U(a) = Nilai utilitas alternatif
     * - Nilai = Nilai parameter alternatif untuk kriteria tertentu
     * - Min = Nilai minimum dari semua alternatif untuk kriteria tertentu
     * - Max = Nilai maksimum dari semua alternatif untuk kriteria tertentu
     */
    private function getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem)
    {
        $utilitas = [];
        
        foreach ($alternatif as $alt) {
            $row = ['alternatif' => $alt];
            
            foreach ($kriteria as $krit) {
                $nilai = $penilaian->where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();
                
                $nilaiParameter = $nilai ? $nilai->nilai_parameter : 0;
                $min = $nilaiEkstrem[$krit->kode]['min'];
                $max = $nilaiEkstrem[$krit->kode]['max'];
                
                // Hitung utilitas berdasarkan tipe kriteria
                if ($max - $min == 0) {
                    $nilaiUtilitas = 0;
                } else {
                    // Jika tipe COST, gunakan rumus terbalik (semakin kecil semakin baik)
                    if ($krit->tipe == 'cost') {
                        $nilaiUtilitas = ($max - $nilaiParameter) / ($max - $min);
                    } else {
                        // Tipe BENEFIT (semakin besar semakin baik)
                        $nilaiUtilitas = ($nilaiParameter - $min) / ($max - $min);
                    }
                }
                
                $row[$krit->kode] = round($nilaiUtilitas, 4);
            }
            
            $utilitas[] = $row;
        }
        
        return $utilitas;
    }

    /**
     * Menghitung nilai preferensi (V)
     * 
     * Rumus SMART:
     * V(a) = Σ (bobot × utilitas)
     * 
     * Dimana:
     * - V(a) = Nilai preferensi alternatif
     * - bobot = Bobot kriteria
     * - utilitas = Nilai utilitas yang telah dinormalisasi
     * 
     * Nilai V yang lebih tinggi menunjukkan alternatif yang lebih baik
     */
    private function getNilaiPreferensi($alternatif, $kriteria, $nilaiUtilitas)
    {
        $preferensi = [];
        
        foreach ($nilaiUtilitas as $row) {
            $alt = $row['alternatif'];
            $nilaiV = 0;
            
            foreach ($kriteria as $krit) {
                // V = Σ (bobot × utilitas)
                $nilaiV += $krit->bobot * $row[$krit->kode];
            }
            
            $preferensi[] = [
                'alternatif' => $alt,
                'nilai_v' => round($nilaiV, 4),
                'utilitas' => $row,
            ];
        }
        
        return $preferensi;
    }

    /**
     * Mendapatkan ranking berdasarkan nilai preferensi (V)
     * Urutan dari nilai V terbesar ke terkecil
     */
    private function getRanking($nilaiPreferensi)
    {
        // Urutkan berdasarkan nilai V (descending)
        usort($nilaiPreferensi, function ($a, $b) {
            return $b['nilai_v'] <=> $a['nilai_v'];
        });
        
        // Tambahkan nomor ranking
        $ranking = [];
        $no = 1;
        foreach ($nilaiPreferensi as $item) {
            $ranking[] = [
                'rank' => $no++,
                'alternatif' => $item['alternatif'],
                'nilai_v' => $item['nilai_v'],
            ];
        }
        
        return $ranking;
    }
}
