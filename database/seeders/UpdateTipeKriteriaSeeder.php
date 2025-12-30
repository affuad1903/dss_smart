<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateTipeKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update tipe untuk setiap kriteria
        // C1, C2, C3 = Benefit (semakin besar semakin baik)
        // C4 = Cost (semakin kecil semakin baik)
        
        DB::table('kriteria')->where('kode', 'C1')->update(['tipe' => 'benefit']);
        DB::table('kriteria')->where('kode', 'C2')->update(['tipe' => 'benefit']);
        DB::table('kriteria')->where('kode', 'C3')->update(['tipe' => 'benefit']);
        DB::table('kriteria')->where('kode', 'C4')->update(['tipe' => 'cost']);
    }
}
