<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianInstrumen extends Model
{
    use HasFactory;

    protected $table = 'penilaian_instrumen';

    protected $fillable = [
        'cpl_mk_cpmk_penilaian_id',
        'kriteria_id',
        'bobot_metode'
    ];
    
    // FK -> cpl_mk_cpmk_penilaian.id
    public function cplMkCpmkPenilaian()
    {
        return $this->belongsTo(
            CplMkCpmkPenilaian::class,
            'cpl_mk_cpmk_penilaian_id'
        );
    }

    // FK -> instrumen_penilaian.id
    public function instrumenPenilaian()
    {
        return $this->belongsTo(
            InstrumenPenilaian::class,
            'kriteria_id'
        );
    }
}
