<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Universitas extends Model
{
    use HasFactory;
    protected $table = 'universitas';

    protected $fillable = [
        'nama', 'img'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_universitasUser');
    }

    public function fakultas()
    {
        return $this->hasMany(Fakultas::class, 'id_universitas');
    }
}