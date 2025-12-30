<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Parameter Kriteria
 * Merepresentasikan range nilai dan skor untuk setiap kriteria
 */
class ParameterKriteria extends Model
{
    use HasFactory;

    protected $table = 'parameter_kriteria';

    protected $fillable = [
        'kriteria_id',
        'deskripsi',
        'nilai',
        'batas_bawah',
        'batas_atas',
    ];

    protected $casts = [
        'nilai' => 'integer',
        'batas_bawah' => 'decimal:2',
        'batas_atas' => 'decimal:2',
    ];

    /**
     * Relasi ke kriteria
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    /**
     * Cek apakah nilai aktual masuk dalam range parameter ini
     */
    public function isInRange($nilaiAktual)
    {
        // Jika tidak ada batas (kategorikal), return false
        if ($this->batas_bawah === null && $this->batas_atas === null) {
            return false;
        }

        // Jika hanya ada batas atas (< x)
        if ($this->batas_bawah === null) {
            return $nilaiAktual < $this->batas_atas;
        }

        // Jika hanya ada batas bawah (> x)
        if ($this->batas_atas === null) {
            return $nilaiAktual >= $this->batas_bawah;
        }

        // Jika ada kedua batas (x <= y < z)
        return $nilaiAktual >= $this->batas_bawah && $nilaiAktual < $this->batas_atas;
    }
}
