<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCpmk extends Model
{
    use HasFactory;

    protected $table='sub_cpmk';
    // protected $primaryKey='kode';
    // public $incrementing=false;
    protected $fillable = [
        'kode',
        'uraian',
        'cpmk_id',
        'id_prodi',
    ];

    public function cpmk(){
        return $this->belongsTo(CPMK::class,'cpmk_id','id');
    }
    public function mks(){
        return $this->belongsToMany(MK::class,'mk_sub_cpmk','sub_cpmk_id','mk_kode');
    }
}
