<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\HasilPerhitungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk Perhitungan dan Hasil Akhir Metode SMART
 */
class PerhitunganController extends Controller
{
    /**
     * Tampilkan halaman perhitungan
     */
    public function index(Request $request)
    {
        // Ambil semua alternatif untuk checklist
        $semuaAlternatif = Alternatif::get();
        $kriteria = Kriteria::get();
        
        // Cek apakah ada alternatif yang dipilih
        $alternatifIds = $request->input('alternatif_ids', []);
        
        // Jika tidak ada yang dipilih, tampilkan halaman checklist
        if (empty($alternatifIds)) {
            return view('perhitungan.checklist', compact('semuaAlternatif', 'kriteria'));
        }
        
        // Filter alternatif berdasarkan yang dipilih
        $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
        $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();

        // 1. TABEL PENILAIAN (Nilai Parameter)
        $tabelPenilaian = $this->getTabelPenilaian($alternatif, $kriteria, $penilaian);

        // 2. NILAI EKSTREM (Min-Max per kriteria) - hanya dari alternatif terpilih
        $nilaiEkstrem = $this->getNilaiEkstrem($kriteria, $penilaian);

        // 3. NILAI UTILITAS (Normalisasi)
        $nilaiUtilitas = $this->getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem);

        return view('perhitungan.index', compact(
            'alternatif',
            'kriteria',
            'tabelPenilaian',
            'nilaiEkstrem',
            'nilaiUtilitas',
            'alternatifIds'
        ));
    }

    /**
     * Tampilkan halaman hasil akhir dan perankingan
     */
    public function hasil(Request $request)
    {
        // Ambil alternatif yang dipilih dari session atau request
        $alternatifIds = $request->input('alternatif_ids', session('alternatif_ids', []));
        
        // Jika tidak ada yang dipilih, redirect ke halaman checklist
        if (empty($alternatifIds)) {
            return redirect()->route('perhitungan.index')
                ->with('error', 'Silakan pilih alternatif terlebih dahulu');
        }
        
        // Simpan ke session untuk digunakan di export dan simpan history
        session(['alternatif_ids' => $alternatifIds]);
        
        // Filter alternatif berdasarkan yang dipilih
        $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
        $kriteria = Kriteria::get();
        $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();

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
            'ranking',
            'alternatifIds'
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

    /**
     * Export hasil perankingan ke format CSV
     */
    public function exportCsv()
    {
        // Ambil alternatif yang dipilih dari session
        $alternatifIds = session('alternatif_ids', []);
        
        if (empty($alternatifIds)) {
            return redirect()->route('perhitungan.index')
                ->with('error', 'Silakan pilih alternatif terlebih dahulu');
        }
        
        $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
        $kriteria = Kriteria::get();
        $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();

        // Hitung nilai utilitas
        $nilaiEkstrem = $this->getNilaiEkstrem($kriteria, $penilaian);
        $nilaiUtilitas = $this->getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem);

        // Hitung nilai preferensi dan ranking
        $nilaiPreferensi = $this->getNilaiPreferensi($alternatif, $kriteria, $nilaiUtilitas);
        $ranking = $this->getRanking($nilaiPreferensi);

        // Nama file dengan timestamp
        $filename = 'hasil_perankingan_' . date('Y-m-d_His') . '.csv';

        // Header untuk download
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Callback untuk generate CSV
        $callback = function() use ($ranking, $kriteria, $nilaiPreferensi) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header CSV
            $header = ['Ranking', 'Kode', 'Nama Bank Sampah'];
            foreach ($kriteria as $krit) {
                $header[] = $krit->nama . ' (' . $krit->kode . ')';
            }
            $header[] = 'Nilai Preferensi (V)';
            $header[] = 'Status';
            
            fputcsv($file, $header);

            // Data ranking
            foreach ($ranking as $item) {
                $row = [
                    $item['rank'],
                    $item['alternatif']->kode,
                    $item['alternatif']->nama
                ];

                // Cari utilitas untuk alternatif ini
                $altUtilitas = collect($nilaiPreferensi)->firstWhere('alternatif.id', $item['alternatif']->id);
                
                // Tambahkan nilai utilitas x bobot untuk setiap kriteria
                foreach ($kriteria as $krit) {
                    $utilitas = $altUtilitas['utilitas'][$krit->kode];
                    $bobotUtilitas = $utilitas * $krit->bobot;
                    $row[] = number_format($bobotUtilitas, 4);
                }

                // Nilai preferensi
                $row[] = number_format($item['nilai_v'], 4);

                // Status
                if ($item['rank'] == 1) {
                    $row[] = 'PRIORITAS UTAMA';
                } elseif ($item['rank'] <= 3) {
                    $row[] = 'Prioritas Tinggi';
                } else {
                    $row[] = 'Prioritas Rendah';
                }

                fputcsv($file, $row);
            }

            // Tambahkan informasi tambahan
            fputcsv($file, []);
            fputcsv($file, ['INFORMASI PERHITUNGAN']);
            fputcsv($file, ['Metode', 'SMART (Simple Multi Attribute Rating Technique)']);
            fputcsv($file, ['Tanggal Export', date('d-m-Y H:i:s')]);
            fputcsv($file, ['Jumlah Alternatif', count($ranking)]);
            fputcsv($file, ['Jumlah Kriteria', $kriteria->count()]);
            
            fputcsv($file, []);
            fputcsv($file, ['BOBOT KRITERIA']);
            foreach ($kriteria as $krit) {
                fputcsv($file, [$krit->kode, $krit->nama, $krit->bobot, $krit->tipe]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simpan hasil perhitungan ke history
     */
    public function simpanHistory(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        // Ambil alternatif yang dipilih dari session
        $alternatifIds = session('alternatif_ids', []);
        
        if (empty($alternatifIds)) {
            return back()->with('error', 'Silakan pilih alternatif terlebih dahulu');
        }

        $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
        $kriteria = Kriteria::get();
        $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();

        // Validasi: Pastikan semua alternatif sudah dinilai
        $totalPenilaianRequired = $alternatif->count() * $kriteria->count();
        if ($penilaian->count() < $totalPenilaianRequired) {
            return back()->with('error', 'Tidak dapat menyimpan history. Pastikan semua alternatif sudah dinilai untuk semua kriteria!');
        }

        // Validasi: Pastikan total bobot = 1
        $totalBobot = $kriteria->sum('bobot');
        if (abs($totalBobot - 1) >= 0.01) {
            return back()->with('error', 'Tidak dapat menyimpan history. Total bobot kriteria harus sama dengan 1! Saat ini: ' . $totalBobot);
        }

        // Hitung semua hasil
        $nilaiEkstrem = $this->getNilaiEkstrem($kriteria, $penilaian);
        $nilaiUtilitas = $this->getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem);
        $nilaiPreferensi = $this->getNilaiPreferensi($alternatif, $kriteria, $nilaiUtilitas);
        $ranking = $this->getRanking($nilaiPreferensi);

        // Simpan ke database
        HasilPerhitungan::create([
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'data_alternatif' => $alternatif->toArray(),
            'data_kriteria' => $kriteria->toArray(),
            'data_penilaian' => $penilaian->toArray(),
            'hasil_normalisasi' => $nilaiEkstrem,
            'hasil_utility' => $nilaiUtilitas,
            'hasil_akhir' => $ranking,
            'total_bobot_kriteria' => $totalBobot,
            'jumlah_alternatif' => $alternatif->count(),
            'jumlah_kriteria' => $kriteria->count(),
            'user_name' => Auth::user()->name ?? 'System',
        ]);

        return redirect()->route('history.index')
            ->with('success', 'History perhitungan berhasil disimpan!');
    }

    /**
     * Tampilkan daftar history perhitungan
     */
    public function history()
    {
        $history = HasilPerhitungan::orderBy('created_at', 'desc')->paginate(10);
        return view('perhitungan.history', compact('history'));
    }

    /**
     * Tampilkan detail history perhitungan
     */
    public function showHistory($id)
    {
        $hasil = HasilPerhitungan::findOrFail($id);
        return view('perhitungan.detail-history', compact('hasil'));
    }

    /**
     * Hapus history perhitungan
     */
    public function deleteHistory($id)
    {
        $hasil = HasilPerhitungan::findOrFail($id);
        $hasil->delete();

        return redirect()->route('history.index')
            ->with('success', 'History perhitungan berhasil dihapus!');
    }
}
