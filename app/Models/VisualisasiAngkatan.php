<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualisasiAngkatan extends Model
{
    use HasFactory;

    protected $table = 'visualisasi_angkatans';

    protected $fillable = [
        'id_prodi',
        'angkatan',
        'tahun_filter',
        'semester_filter',
        'avg_skor_cpl',
        'rekap_cpl_json',
        'rekap_cpmk_json',
        'distribusi_kelulusan_json',
        'last_calculated_at',
    ];

    protected $casts = [
        'rekap_cpl_json' => 'array',
        'rekap_cpmk_json' => 'array',
        'distribusi_kelulusan_json' => 'array',
        'avg_skor_cpl' => 'float',
        'angkatan' => 'integer',
        'last_calculated_at' => 'datetime',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id');
    }
}
