<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonversiMetode extends Model
{
    use HasFactory;

    protected $table = 'konversi_metode';

    protected $fillable = [
        'penilaian_konversi_id',
        'metode_id',
        'bobot',
    ];

    public function penilaianKonversi()
    {
        return $this->belongsTo(PenilaianKonversi::class, 'penilaian_konversi_id');
    }

    public function metodePenilaian()
    {
        return $this->belongsTo(MetodePenilaian::class, 'metode_id');
    }

    public function cpmkMetode()
    {
        return $this->hasMany(KonversiCpmkMetode::class, 'konversi_metode_id');
    }

    public function mutus()
    {
        return $this->hasMany(Mutu::class, 'konversi_metode_id');
    }
}
