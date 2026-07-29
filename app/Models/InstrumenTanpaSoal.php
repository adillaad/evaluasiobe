<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstrumenTanpaSoal extends Model
{
    protected $table = 'instrumen_Tanpasoal'; // ← Wajib ada!
    protected $fillable = [
        'kode_mk', 'dosen_id', 'nama_instrumen', 
        'cpl_id', 'cpmk_id', 'bobot', 'status', 'komentar_kaprodi'
    ];

    // Relasi ke tabel lain
    public function mk() { return $this->belongsTo(\App\Models\MK::class, 'kode_mk', 'kode'); }
    public function dosen() { return $this->belongsTo(\App\Models\User::class, 'dosen_id'); }
    public function cpl() { return $this->belongsTo(\App\Models\CPL::class, 'cpl_id'); }
    public function cpmk() { return $this->belongsTo(\App\Models\CPMK::class, 'cpmk_id'); }
}