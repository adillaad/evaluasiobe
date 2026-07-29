<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    use HasFactory;
    protected $table = 'fakultas';

    protected $fillable = ['nama', 'id_universitas'];

    public function users()
    {
        return $this->hasMany(User::class, 'id_fakultasUser');
    }

    public function universitas()
    {
        return $this->belongsTo(Universitas::class, 'id_universitas', 'id');
    }

    public function prodi()
    {
        return $this->hasMany(Prodi::class, 'id_fakultas');
    }
}
