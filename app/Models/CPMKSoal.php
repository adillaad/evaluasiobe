<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPMKSoal extends Model
{
    use HasFactory;
    protected $table = 'cpmk_soals';

    protected $fillable = [
        'id_cpmk', 'id_soal'
    ];
}
