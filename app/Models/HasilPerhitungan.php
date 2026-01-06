<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk History Hasil Perhitungan SMART
 */
class HasilPerhitungan extends Model
{
    protected $table = 'hasil_perhitungan';

    protected $fillable = [
        'judul',
        'keterangan',
        'data_alternatif',
        'data_kriteria',
        'data_penilaian',
        'hasil_normalisasi',
        'hasil_utility',
        'hasil_akhir',
        'total_bobot_kriteria',
        'jumlah_alternatif',
        'jumlah_kriteria',
        'user_name',
    ];

    protected $casts = [
        'data_alternatif' => 'array',
        'data_kriteria' => 'array',
        'data_penilaian' => 'array',
        'hasil_normalisasi' => 'array',
        'hasil_utility' => 'array',
        'hasil_akhir' => 'array',
        'total_bobot_kriteria' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
