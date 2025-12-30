<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk data kriteria
 * Mengisi data kriteria default sesuai spesifikasi
 */
class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriteria = [
            [
                'kode' => 'C1',
                'nama' => 'Volume Timbulan Sampah Wilayah',
                'bobot' => 0.40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'C2',
                'nama' => 'Aksesibilitas Transportasi DLH',
                'bobot' => 0.30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'C3',
                'nama' => 'Kepadatan Penduduk',
                'bobot' => 0.20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'C4',
                'nama' => 'Jarak Bank Sampah ke TPA',
                'bobot' => 0.10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('kriteria')->insert($kriteria);
    }
}
