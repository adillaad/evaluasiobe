<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualisasiFakultas extends Model
{
    use HasFactory;

    protected $table = 'visualisasi_fakultas';

    protected $fillable = [
        'id_fakultas',
        'id_universitas',
        'tahun_ajaran',
        'semester_filter',
        'avg_cpl_fakultas',
        'rekap_prodi_json',
        'radar_cpl_json',
        'last_calculated_at',
    ];

    protected $casts = [
        'rekap_prodi_json' => 'array',
        'radar_cpl_json' => 'array',
        'avg_cpl_fakultas' => 'float',
        'last_calculated_at' => 'datetime',
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas', 'id');
    }

    public function universitas()
    {
        return $this->belongsTo(Universitas::class, 'id_universitas', 'id');
    }
}
