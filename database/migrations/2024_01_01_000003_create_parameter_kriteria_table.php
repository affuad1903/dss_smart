<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel parameter_kriteria
 * Tabel ini menyimpan range nilai dan skor untuk setiap kriteria
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parameter_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->string('deskripsi'); // Deskripsi parameter (misal: < 100 kg)
            $table->integer('nilai'); // Skor parameter (1-5)
            $table->decimal('batas_bawah', 10, 2)->nullable(); // Batas bawah range
            $table->decimal('batas_atas', 10, 2)->nullable(); // Batas atas range
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameter_kriteria');
    }
};
