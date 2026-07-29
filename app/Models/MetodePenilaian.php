<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodePenilaian extends Model
{
    use HasFactory;
    protected $table = 'metode_penilaian';

    protected $fillable = ['nama', 'id_prodi'];
    
    public function penilaianMetodes()
    {
        return $this->hasMany(PenilaianMetode::class, 'metode_id', 'id');
    }
}
