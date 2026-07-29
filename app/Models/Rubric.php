<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    protected $fillable = [
        'mk_kode',
        'user_id',
        'jenis_rubrik',
        'file_path',
        'rubric_type'
    ];

    public function mk()
    {
        return $this->belongsTo(MK::class, 'mk_kode', 'kode');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}