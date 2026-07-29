<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CplMkCpmkPenilaian extends Model
{
    use HasFactory;

    protected $table = 'cpl_mk_cpmk_penilaian';
    
    protected $fillable = [
        'mk_kode',
        'cpl_id',
        'cpmk_id',
        'tahap_penilaian',
        'instrumen',
    ];

    // FK: cpl_mk_cpmk_penilaian.mk_kode -> mk.kode (PK)
    public function mk()
    {
        return $this->belongsTo(Mk::class, 'mk_kode', 'kode');
    }

    // FK: cpl_mk_cpmk_penilaian.cpl_id -> cpls.id
    public function cpl()
    {
        return $this->belongsTo(Cpl::class, 'cpl_id');
    }

    // FK: cpl_mk_cpmk_penilaian.cpmk_id -> cpmks.id
    public function cpmk()
    {
        return $this->belongsTo(CPMK::class, 'cpmk_id');
    }

    // Jika ada tabel detail penilaian_metode dengan FK cpl_mk_cpmk_penilaian_id
    public function penilaianMetode()
    {
        return $this->hasMany(PenilaianMetode::class, 'cpl_mk_cpmk_penilaian_id','id');
    }

}
