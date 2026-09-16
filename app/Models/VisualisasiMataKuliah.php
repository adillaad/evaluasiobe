<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualisasiMataKuliah extends Model
{
    use HasFactory;

    protected $table = 'visualisasi_mata_kuliahs';

    protected $fillable = [
        'id_prodi',
        'angkatan',
        'kode_mk',
        'tahun_filter',
        'semester_filter',
        'avg_skor_mk',
        'rekap_cpmk_json',
        'rekap_cpl_json',
        'distribusi_nilai_json',
        'last_calculated_at',
    ];

    protected $casts = [
        'rekap_cpmk_json' => 'array',
        'rekap_cpl_json' => 'array',
        'distribusi_nilai_json' => 'array',
        'avg_skor_mk' => 'float',
        'angkatan' => 'integer',
        'last_calculated_at' => 'datetime',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id');
    }

    public function mk()
    {
        return $this->belongsTo(MK::class, 'kode_mk', 'kode');
    }
}
