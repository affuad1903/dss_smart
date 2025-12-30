<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Alternatif (Bank Sampah)
 * Merepresentasikan data bank sampah yang akan dinilai
 */
class Alternatif extends Model
{
    use HasFactory;

    protected $table = 'alternatif';

    protected $fillable = [
        'kode',
        'nama',
    ];

    /**
     * Relasi ke penilaian
     */
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }

    /**
     * Generate kode alternatif berikutnya (A1, A2, A3, ...)
     */
    public static function generateKode()
    {
        $lastAlternatif = self::orderBy('id', 'desc')->first();
        
        if (!$lastAlternatif) {
            return 'A1';
        }

        $lastNumber = (int) substr($lastAlternatif->kode, 1);
        return 'A' . ($lastNumber + 1);
    }
}
