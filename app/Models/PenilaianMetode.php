<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianMetode extends Model
{
    use HasFactory;

    protected $table = 'penilaian_metode';

    protected $fillable = [
        'cpl_mk_cpmk_penilaian_id',
        'bobot',
        'metode_id'
    ]; 
    
    public function cplMkCpmkPenilaian()
    {
        return $this->belongsTo(
            CplMkCpmkPenilaian::class,
            'cpl_mk_cpmk_penilaian_id','id'
        );
    }

    public function metode()
    {
        return $this->belongsTo(
            MetodePenilaian::class,
            'metode_id','id'
        );
    }
}
