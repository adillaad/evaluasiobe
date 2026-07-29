<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumenPenilaian extends Model
{
    use HasFactory;
    protected $table = 'instrumen_penilaian';

    protected $fillable = ['nama_kriteria', 'id_prodi'];
}
