<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianKonversi extends Model
{
    use HasFactory;

    protected $table = 'penilaian_konversi';

    protected $fillable = [
        'dosen_id',
        'mk_kode',
        'tahun_ajaran_id',
        'kurikulum_id',
    ];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mk()
    {
        return $this->belongsTo(MK::class, 'mk_kode', 'kode');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function konversiMetode()
    {
        return $this->hasMany(KonversiMetode::class, 'penilaian_konversi_id');
    }
}
