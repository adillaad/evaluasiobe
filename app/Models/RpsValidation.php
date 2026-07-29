<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpsValidation extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     */
    protected $table = 'rps_validations';

    /**
     * Kolom yang bisa diisi secara massal.
     */
    protected $fillable = [
        'rps_id',
        'validator_id',
        'status',
        'catatan',
    ];

    /**
     * Relasi ke model RPS.
     */
    public function rps()
    {
        return $this->belongsTo(RPS::class, 'rps_id');
    }

    /**
     * Relasi ke model User (untuk validator/Kaprodi).
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
}