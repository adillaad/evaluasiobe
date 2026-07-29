<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MK_CPL extends Model
{
    use HasFactory;
    protected $table = 'mk_cpl';
    protected $fillable = ['cpl_id','mk_kode','id_prodi'];
    public function cpl()
    {
        return $this->belongsTo(CPL::class,'cpl_id','id');
    }
    public function mk()
    {
        return $this->belongsTo(MK::class,'mk_kode','kode');
    }
    public function prodi()
    {
        return $this->belongsTo(Prodi::class,'id_prodi','id');
    }
}
