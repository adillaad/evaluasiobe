<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanpaSoal extends Model
{
    use HasFactory;

    protected $table = 'tanpa_soal';

    protected $fillable = [
        'kode_mk',
        'metode_id',
        'nama_instrumen',
        'cpl_id',
        'cpmk_id',
        'sub_cpmk_id',
        'persentase_cpmk',
        'bobot_TS',
        'dosen_id',
        'kurikulum_id',
        'status',
        'komentar_kaprodi'
    ];

    public function subCpmk()
    {
        return $this->belongsTo(SubCpmk::class, 'sub_cpmk_id');
    }

    public function cpl()
    {
        return $this->belongsTo(CPL::class, 'cpl_id');
    }

    public function cpmk()
    {
        return $this->belongsTo(CPMK::class, 'cpmk_id');
    }

    public function mk()
    {
        return $this->belongsTo(MK::class, 'kode_mk', 'kode');
    }

    public function metode()
    {
        return $this->belongsTo(\App\Models\MetodePenilaian::class, 'metode_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
