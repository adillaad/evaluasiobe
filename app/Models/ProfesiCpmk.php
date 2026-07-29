<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ProfesiCpmk extends Model
{
    use HasFactory;

    protected $table = 'profesi_cpmk';

    protected $fillable = [
        'profesi_id',
        'cpmk_id',
        'bobot' // jika ada bobot
    ];

    public function profesi()
    {
        return $this->belongsTo(Profesi::class, 'profesi_id');
    }

    public function cpmk()
    {
        return $this->belongsTo(CPMK::class, 'cpmk_id');
    }
}
