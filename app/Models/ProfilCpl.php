<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CPL;
use App\Models\ProfilLulusan;
use App\Models\Prodi;

class ProfilCpl extends Model
{
    use HasFactory;

    protected $table = 'profil_cpl';
    public $timestamps = false;
    protected $fillable = ['idProfil', 'idCpl','bobot','id_prodi'];
    protected $guarded = ['id'];

    public function profilLulusan()
    {
        return $this->belongsTo(ProfilLulusan::class, 'idProfil');
    }

    public function cpl()
    {
        return $this->belongsTo(CPL::class, 'idCpl');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }
}
