<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk parameter kriteria
 * Mengisi data parameter nilai untuk setiap kriteria
 */
class ParameterKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID kriteria
        $c1 = DB::table('kriteria')->where('kode', 'C1')->first()->id;
        $c2 = DB::table('kriteria')->where('kode', 'C2')->first()->id;
        $c3 = DB::table('kriteria')->where('kode', 'C3')->first()->id;
        $c4 = DB::table('kriteria')->where('kode', 'C4')->first()->id;

        $parameters = [
            // C1: Volume Timbulan Sampah Wilayah (kg)
            ['kriteria_id' => $c1, 'deskripsi' => '< 100 kg', 'nilai' => 1, 'batas_bawah' => null, 'batas_atas' => 100],
            ['kriteria_id' => $c1, 'deskripsi' => '100 ≤ s/d < 300 kg', 'nilai' => 2, 'batas_bawah' => 100, 'batas_atas' => 300],
            ['kriteria_id' => $c1, 'deskripsi' => '300 ≤ s/d < 500 kg', 'nilai' => 3, 'batas_bawah' => 300, 'batas_atas' => 500],
            ['kriteria_id' => $c1, 'deskripsi' => '500 ≤ s/d < 700 kg', 'nilai' => 4, 'batas_bawah' => 500, 'batas_atas' => 700],
            ['kriteria_id' => $c1, 'deskripsi' => '> 700 kg', 'nilai' => 5, 'batas_bawah' => 700, 'batas_atas' => null],

            // C2: Aksesibilitas Transportasi (kategorikal)
            ['kriteria_id' => $c2, 'deskripsi' => 'Motor', 'nilai' => 1, 'batas_bawah' => null, 'batas_atas' => null],
            ['kriteria_id' => $c2, 'deskripsi' => 'Pick Up', 'nilai' => 2, 'batas_bawah' => null, 'batas_atas' => null],
            ['kriteria_id' => $c2, 'deskripsi' => 'Truk Kecil', 'nilai' => 3, 'batas_bawah' => null, 'batas_atas' => null],
            ['kriteria_id' => $c2, 'deskripsi' => 'Truk Besar', 'nilai' => 4, 'batas_bawah' => null, 'batas_atas' => null],

            // C3: Kepadatan Penduduk (Jiwa/Km²)
            ['kriteria_id' => $c3, 'deskripsi' => '< 1000 Jiwa/Km²', 'nilai' => 1, 'batas_bawah' => null, 'batas_atas' => 1000],
            ['kriteria_id' => $c3, 'deskripsi' => '1000 ≤ s/d < 3000 Jiwa/Km²', 'nilai' => 2, 'batas_bawah' => 1000, 'batas_atas' => 3000],
            ['kriteria_id' => $c3, 'deskripsi' => '3000 ≤ s/d < 5000 Jiwa/Km²', 'nilai' => 3, 'batas_bawah' => 3000, 'batas_atas' => 5000],
            ['kriteria_id' => $c3, 'deskripsi' => '5000 ≤ s/d < 10000 Jiwa/Km²', 'nilai' => 4, 'batas_bawah' => 5000, 'batas_atas' => 10000],
            ['kriteria_id' => $c3, 'deskripsi' => '> 10000 Jiwa/Km²', 'nilai' => 5, 'batas_bawah' => 10000, 'batas_atas' => null],

            // C4: Jarak ke TPA (Km) - semakin dekat semakin baik
            ['kriteria_id' => $c4, 'deskripsi' => '> 15 Km', 'nilai' => 1, 'batas_bawah' => 15, 'batas_atas' => null],
            ['kriteria_id' => $c4, 'deskripsi' => '12.5 ≤ s/d < 15 Km', 'nilai' => 2, 'batas_bawah' => 12.5, 'batas_atas' => 15],
            ['kriteria_id' => $c4, 'deskripsi' => '10 ≤ s/d < 12.5 Km', 'nilai' => 3, 'batas_bawah' => 10, 'batas_atas' => 12.5],
            ['kriteria_id' => $c4, 'deskripsi' => '8 ≤ s/d < 10 Km', 'nilai' => 4, 'batas_bawah' => 8, 'batas_atas' => 10],
            ['kriteria_id' => $c4, 'deskripsi' => '< 8 Km', 'nilai' => 5, 'batas_bawah' => null, 'batas_atas' => 8],
        ];

        foreach ($parameters as &$param) {
            $param['created_at'] = now();
            $param['updated_at'] = now();
        }

        DB::table('parameter_kriteria')->insert($parameters);
    }
}
