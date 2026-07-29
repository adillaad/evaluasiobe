<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'activities';

    protected $fillable = [
        'minggu', 
        'id_cpmk',
        'sub_cpmk', 
        'indikator', 
        'bentuk_asesmen',
        'materi',
        'metode',
        'kriteria', 
        'kegiatan_luring', 
        'kegiatan_daring', 
        'bobot', 
        'id_rps',
        'id_prodi'
    ];
   
    protected $casts = [
        'id_cpmk' => 'array',
        'sub_cpmk' => 'array',
        'materi' => 'array',
        'kegiatan_luring' => 'array',
        'kegiatan_daring' => 'array',
        'indikator'       => 'array',
        'metode' => 'array',
        'bentuk_asesmen' => 'array'
    ];

    public function rps()
    {
        return $this->belongsTo(RPS::class, 'id_rps');
    }
 
    public function getCpmkDetailsAttribute()
    {
        $cpmk_ids = $this->id_cpmk; 

        // 1. Jika datanya null (kosong), kembalikan collection kosong
        if (is_null($cpmk_ids)) {
            return collect(); // collect() adalah array "pintar" Laravel
        }
        
        // 2. Jika datanya BUKAN array (karena casting gagal),
        //    bungkus dia ke dalam array.
        if (!is_array($cpmk_ids)) {
            $cpmk_ids = [$cpmk_ids]; 
        }
        
        // 3. Jika array-nya kosong, kembalikan collection kosong
        if (empty($cpmk_ids)) {
            return collect();
        }
        
        // 4. Baru jalankan query yang aman
        return CPMK::whereIn('id', $cpmk_ids)->get(); 
    }
    
    public function cpmk()
    {
        // return $this->belongsTo(CPMK::class, 'id_cpmk', 'id');
        return $this->belongsTo(CPMK::class, 'id_cpmk');
    }

}
