<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pustaka extends Model
{
    use HasFactory;
    protected $table = 'pustakas';

    protected $fillable = [
        'id_prodi',
        'kode_mk',
        'judul',
        'penulis',
        'penerbit',
        'tahun',
        'kode_pustaka',
        'deskripsi_lengkap',
    ];

    public function mk()
    {
        return $this->belongsTo(MK::class, 'kode_mk', 'kode');
    }
}