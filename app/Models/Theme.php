<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['theme_color', 'universitas_id'];

    public function universitas()
    {
        return $this->belongsTo(Universitas::class, 'universitas_id', 'id');
    }
}
