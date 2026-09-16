<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'tahun',
        'jenis_semester',
    ];

    public function mutus()
    {
        return $this->hasMany(Mutu::class, 'tahun_ajaran_id');
    }

    public function rpss()
    {
        return $this->hasMany(RPS::class, 'tahun_ajaran_id');
    }

    public function penilaianKonversi()
    {
        return $this->hasMany(PenilaianKonversi::class, 'tahun_ajaran_id');
    }

    public function getLabelAttribute(): string
    {
        $tahunLanjut = (int) $this->tahun + 1;
        return "{$this->tahun}/{$tahunLanjut} - {$this->jenis_semester}";
    }
}
