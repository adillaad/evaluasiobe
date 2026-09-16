<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualisasiProdi extends Model
{
    use HasFactory;

    protected $table = 'visualisasi_prodis';

    protected $fillable = [
        'id_prodi',
        'tahun_ajaran',
        'semester_filter',
        'avg_cpl_keseluruhan',
        'radar_cpl_json',
        'rekap_angkatan_json',
        'last_calculated_at',
    ];

    protected $casts = [
        'radar_cpl_json' => 'array',
        'rekap_angkatan_json' => 'array',
        'avg_cpl_keseluruhan' => 'float',
        'last_calculated_at' => 'datetime',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id');
    }
}
