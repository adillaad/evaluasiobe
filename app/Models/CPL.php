<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPL extends Model
{
    use HasFactory;
    protected $table = 'cpls';

    protected $fillable = [
        'aspek', 'kode', 'nomor', 'judul', 'id_kurikulum', 'id_prodi'
    ];

    public function mutus()
    {
        return $this->hasMany(Mutu::class);
    }

    public function profilLulusan(){
        return $this->belongsToMany(ProfilLulusan::class,'profil_cpl','idCpl','idProfil');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi','id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum','id');
    }

    public function cpmk(){
        return $this->hasMany(CPMK::class,'cpl_id','id');
    }

    public function mk(){
        return $this->belongsToMany(MK::class,'mk_cpl','cpl_id','mk_kode')
        ->withPivot('id_prodi')
        ->withTimestamps();
    }
    public function bk(){
        return $this->belongsToMany(BK::class,'bk_cpl','cpl_id','bk_id');
    }
}
