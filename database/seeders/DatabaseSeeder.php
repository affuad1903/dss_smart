<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user default untuk login
        User::factory()->create([
            'name' => 'Affandi Putra Pradana',
            'email' => 'admin@spksmart.com',
            'password' => Hash::make('password'),
        ]);

        // Jalankan seeder untuk kriteria dan parameter
        $this->call([
            KriteriaSeeder::class,
            ParameterKriteriaSeeder::class,
        ]);
    }
}
