<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonversiCpmkMetode extends Model
{
    use HasFactory;

    protected $table = 'konversi_cpmk_metode';

    protected $fillable = [
        'konversi_metode_id',
        'cpmk_id',
        'sub_cpmk_id',
        'nama_soal',
        'bobot_soal',
    ];

    public function konversiMetode()
    {
        return $this->belongsTo(KonversiMetode::class, 'konversi_metode_id');
    }

    public function cpmk()
    {
        return $this->belongsTo(CPMK::class, 'cpmk_id');
    }

    public function subCpmk()
    {
        return $this->belongsTo(SubCpmk::class, 'sub_cpmk_id');
    }
}
