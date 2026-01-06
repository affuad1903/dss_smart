<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_perhitungan', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable(); // Judul/nama perhitungan
            $table->text('keterangan')->nullable(); // Keterangan perhitungan
            $table->json('data_alternatif'); // Data alternatif saat perhitungan
            $table->json('data_kriteria'); // Data kriteria & bobot saat perhitungan
            $table->json('data_penilaian'); // Data penilaian saat perhitungan
            $table->json('hasil_normalisasi'); // Hasil normalisasi
            $table->json('hasil_utility'); // Hasil utility
            $table->json('hasil_akhir'); // Hasil akhir & ranking
            $table->decimal('total_bobot_kriteria', 5, 2); // Total bobot kriteria saat perhitungan
            $table->integer('jumlah_alternatif'); // Jumlah alternatif
            $table->integer('jumlah_kriteria'); // Jumlah kriteria
            $table->string('user_name')->nullable(); // Nama user yang melakukan perhitungan
            $table->timestamps();
            
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_perhitungan');
    }
};
