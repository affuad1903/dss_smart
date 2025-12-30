<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Penilaian
 * Merepresentasikan nilai aktual dan nilai parameter untuk setiap alternatif per kriteria
 */
class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'alternatif_id',
        'kriteria_id',
        'nilai_aktual',
        'nilai_parameter',
    ];

    protected $casts = [
        'nilai_parameter' => 'integer',
    ];

    /**
     * Relasi ke alternatif
     */
    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class);
    }

    /**
     * Relasi ke kriteria
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
