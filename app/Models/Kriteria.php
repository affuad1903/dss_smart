<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Kriteria
 * Merepresentasikan kriteria penilaian beserta bobotnya
 */
class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';

    protected $fillable = [
        'kode',
        'nama',
        'bobot',
        'tipe',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
    ];

    /**
     * Relasi ke parameter kriteria
     */
    public function parameters()
    {
        return $this->hasMany(ParameterKriteria::class);
    }

    /**
     * Relasi ke penilaian
     */
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }
}
