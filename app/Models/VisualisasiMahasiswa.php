<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualisasiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'visualisasi_mahasiswas';

    protected $fillable = [
        'npm',
        'id_prodi',
        'angkatan',
        'tahun_filter',
        'semester_filter',
        'ipk_obe',
        'avg_cpl',
        'avg_cpmk',
        'total_sks',
        'total_mk',
        'rekap_cpl_json',
        'rekap_cpmk_json',
        'rekap_mk_json',
        'last_calculated_at',
    ];

    protected $casts = [
        'rekap_cpl_json' => 'array',
        'rekap_cpmk_json' => 'array',
        'rekap_mk_json' => 'array',
        'ipk_obe' => 'float',
        'avg_cpl' => 'float',
        'avg_cpmk' => 'float',
        'total_sks' => 'integer',
        'total_mk' => 'integer',
        'last_calculated_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'npm', 'NPM');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id');
    }
}
