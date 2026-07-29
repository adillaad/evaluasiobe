<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPMK extends Model
{
    use HasFactory;
    protected $table = 'cpmks';
    public $timestamps = false;

    protected $fillable = [
        'kode_mk', 'judul','id_prodi','kode','cpl_id'
    ];

    public function mk()
    {
        return $this->belongsTo(MK::class, 'kode_mk', 'kode');
    }

    public function soal()
    {
        return $this->belongsToMany(Soal::class, 'cpmk_soals', 'id_cpmk', 'id_soal')->withTimestamps();
    }

    public function mutus()
    {
        return $this->hasMany(Mutu::class);
    }

    public function cpl(){
        return $this->belongsTo(CPL::class,'cpl_id','id');
    }

    public function mks(){
        return $this->belongsToMany(MK::class,'cpmk_mk','cpmk_id','mk_kode');
    }

    public function subCpmks(){
        return $this->hasMany(SubCpmk::class,'cpmk_id','id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function profesis()
    {
        return $this->belongsToMany(profesi::class, 'profesi_cpmk', 'cpmk_id', 'profesi_id');
    }
    
    public function scopeForUser($query, $user)
    {
        $query->with(['profesis', 'prodi.fakultas'])
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpmks.*');

        return match ($user->otoritas->otoritas) {
            'Penjamin Mutu Universitas'
            => $query->where('fakultas.id_universitas', $user->id_universitasUser),

            'Penjamin Mutu Fakultas'
            => $query->where('fakultas.id', $user->id_fakultasUser),

            default  // Penjamin Mutu Program Studi, Kepala Program Studi
            => $query->where('prodi.id', $user->id_prodiUser),
        };
    }
}
