<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BK extends Model
{
    use HasFactory;

    protected $table='bks';
    protected $fillable= [
       'kode','nama' ,'id_prodi','rumpun','kurikulum_id'
    ];

    public function cpl(){
        return $this->belongsToMany(CPL::class,'bk_cpl','bk_id','cpl_id');
    }

    public function mk(){
        return $this->belongsToMany(MK::class,'bk_mk','bk_id','mk_kode');
    }
}
