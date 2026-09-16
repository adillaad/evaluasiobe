<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MK extends Model
{
    use HasFactory;

    protected $table = 'mks';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'kode', 'nama', 'nama_eng', 'rumpun', 'semester', 'prasyarat', 'kurikulum', 'id_kurikulum',
        'deskripsi', 'bobot_teori', 'bobot_praktikum', 'dosen', 'id_prodi',
        'batas_kelulusan_mhs', 'batas_kelulusan_mk',
    ];

    protected $appends = ['total_sks'];

    public function getTotalSksAttribute()
    {
        return (int) $this->bobot_teori + $this->bobot_praktikum;
    }

    public function cpmk()
    {
        return $this->belongsToMany(CPMK::class, 'cpmk_mk', 'mk_kode', 'cpmk_id')->withPivot('bobot');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi','id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum','id');
    }

    public function cpl()
    {
        return $this->belongsToMany(CPL::class,'mk_cpl','mk_kode','cpl_id')
        ->withPivot('id_prodi');
    }

    public function bk()
    {
        return $this->belongsToMany(BK::class,'bk_mk','mk_kode','bk_id');
    }

    public function cpmks(){
        return $this->belongsToMany(CPMK::class,'cpmk_mk','mk_kode','cpmk_id')->withPivot('bobot');
    }

    public function sub_cpmk(){
        return $this->belongsToMany(SubCpmk::class,'mk_sub_cpmk','mk_kode','sub_cpmk_id');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'kode_mk', 'kode');
    }

    public function rubrics()
    {
        return $this->hasMany(Rubric::class, 'mk_kode', 'kode');
    }

    public function cplMkCpmkPenilaians()
    {
        return $this->hasMany(CplMkCpmkPenilaian::class, 'mk_kode', 'kode');
    }
    public function rps()
    {
        return $this->hasMany(RPS::class, 'kode_mk', 'kode');
    }
}
