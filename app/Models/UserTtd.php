<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTtd extends Model
{
    use HasFactory;

    protected $table = 'user_ttds';

    protected $fillable = [
        'user_id',
        'file_ttd',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
