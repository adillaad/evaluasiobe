<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RPS extends Model
{
    use HasFactory;
    protected $table = 'rpss';

    protected $fillable = [
        'kode_mk', 'nomor', 'dosen','versi','status', 'submitted_at','dosen_anggota1','dosen_anggota2', 'pengembang', 'koordinator', 'kaprodi', 'id_kurikulum', 'semester', 'materi_mk', 'batas_kelulusan_mhs', 'batas_kelulusan_mk','tipe', 'waktu', 'syarat_ujian', 'syarat_studi', 'media_software','media_hardware', 'kontrak', 'id_prodi',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];


    public function pustakas()
    {
        return $this->belongsToMany(Pustaka::class, 'rps_pustaka', 'rps_id', 'pustaka_id')
                    ->withPivot('sifat')
                    ->withTimestamps();
    }

    public function pustakaUtama()
    {
        return $this->belongsToMany(Pustaka::class, 'rps_pustaka', 'rps_id', 'pustaka_id')
                    ->wherePivot('sifat', 'utama');
    }

    // 3. Helper untuk mengambil khusus Pustaka Pendukung
    public function pustakaPendukung()
    {
        return $this->belongsToMany(Pustaka::class, 'rps_pustaka', 'rps_id', 'pustaka_id')
                    ->wherePivot('sifat', 'pendukung');
    }

    public function mk()
    {
        return $this->belongsTo(MK::class, 'kode_mk','kode');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi','id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'id_rps');
    }

    public function koordinatorUser()
    {
        return $this->belongsTo(User::class, 'koordinator', 'name');
    }

    public function pengembangUser()
    {
        return $this->belongsTo(User::class, 'pengembang', 'name');
    }

    public function dosenUser() 
    { 
        return $this->belongsTo(User::class, 'dosen', 'name'); 
    }

    public function dosenAnggota1User()
    {
        return $this->belongsTo(User::class, 'dosen_anggota1', 'name');
    }

    public function dosenAnggota2User()
    {
        return $this->belongsTo(User::class, 'dosen_anggota2', 'name');
    }

    public function kaprodiUser()
    {
        return $this->belongsTo(User::class, 'kaprodi', 'name');
    }

    public function validations()
    {
        return $this->hasMany(RpsValidation::class, 'rps_id');
    }

    public function latestValidation()
    {
        return $this->hasOne(RpsValidation::class, 'rps_id')->latestOfMany();
    }
}
