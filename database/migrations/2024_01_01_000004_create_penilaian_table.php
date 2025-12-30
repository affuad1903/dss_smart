<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel penilaian
 * Tabel ini menyimpan nilai aktual untuk setiap alternatif per kriteria
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternatif_id')->constrained('alternatif')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->string('nilai_aktual'); // Nilai aktual (bisa angka atau teks untuk C2)
            $table->integer('nilai_parameter'); // Nilai berdasarkan parameter (1-5)
            $table->timestamps();
            
            // Kombinasi alternatif dan kriteria harus unik
            $table->unique(['alternatif_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
