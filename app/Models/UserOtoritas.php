<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOtoritas extends Model
{
    use HasFactory;
    protected $table = 'user_otoritas';

    protected $fillable = [
        'user_id',
        'otoritas',
        'nama_otoritas',
        'active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
