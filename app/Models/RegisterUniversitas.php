<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterUniversitas extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'register_universitas';

    // Kolom yang dapat diisi
    protected $fillable = [
        'gelar_depan',
        'nama_lengkap',
        'gelar_belakang',
        'jenis_kelamin',
        'nomor_telepon',
        'nama_universitas',
        'nama_fakultas',
        'nama_prodi',
        'is_aptikom',
        'posisi',
        'nomor_telepon_universitas',
        'alamat_kontak_universitas',
        'website',
        'email',
        'password',
        'surat_tugas',
        'status',
        'registration_attempts',
        'rejected_reason',
        'rejected_at',
        'rejected_by',
        'user_id'
    ];

    protected $casts = [
        'is_aptikom' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
