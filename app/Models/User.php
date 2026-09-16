<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'img',
        'jabatan',
        'id_prodiUser',
        'id_fakultasUser',
        'id_universitasUser'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($user) {
            if ($user->id_prodiUser && !$user->prodis()->where('prodi_id', $user->id_prodiUser)->exists()) {
                $user->prodis()->attach($user->id_prodiUser, ['active' => true]);
            }
        });
    }

    public function otoritas()
    {
        return $this->hasMany(UserOtoritas::class, 'user_id');
    }

    public function getOtoritasAttribute()
    {
        $active = $this->otoritas()->where('active', true)->first();
        if ($active) {
            return $active;
        }

        $first = $this->otoritas()->first();
        if ($first) {
            return $first;
        }

        $rawOtoritas = $this->getRawOriginal('otoritas');
        if (!empty($rawOtoritas)) {
            return (object) [
                'id' => 0,
                'user_id' => $this->id,
                'otoritas' => $rawOtoritas,
                'active' => true,
            ];
        }

        return null;
    }

    // Helper method untuk cek otoritas
    public function hasOtoritas($otoritas)
    {
        return $this->otoritas()->where('otoritas', $otoritas)->where('active', true)->exists();
    }

    /**
     * Tampilkan string otoritas user berdasarkan konteks prodi yang sedang dilihat.
     * Jika prodi konteks bukan prodi utama user (user ditambahkan sebagai Dosen Pengampu),
     * maka otoritas yang ditampilkan HANYA 'Dosen'.
     * Jika di prodi utama, tampilkan seluruh otoritas lengkapnya.
     *
     * @param int|null $currentProdiId
     * @return string
     */
    public function getOtoritasDisplayForProdi(?int $currentProdiId = null): string
    {
        $primaryProdiId = $this->primary_prodi_id ?? $this->id_prodiUser;

        // Jika prodi konteks berbeda dari prodi utama user -> bertindak sebagai Dosen Pengampu
        if ($currentProdiId && $primaryProdiId && (int)$currentProdiId !== (int)$primaryProdiId) {
            return 'Dosen';
        }

        // Tampilkan seluruh otoritas di prodi utama
        $allOtoritas = $this->otoritas()->pluck('otoritas')->unique()->values();

        if ($allOtoritas->isEmpty()) {
            $raw = $this->getRawOriginal('otoritas');
            return $raw ?: 'Dosen';
        }

        return $allOtoritas->implode(', ');
    }

    public function prodis()
    {
        return $this->belongsToMany(Prodi::class, 'prodi_user', 'user_id', 'prodi_id')
                    ->withPivot('id', 'active') // Mengambil kolom 'active' dari pivot
                    ->withTimestamps();
    }

    public function ttds()
    {
        return $this->hasMany(UserTtd::class, 'user_id');
    }

    public function activeTtd()
    {
        return $this->hasOne(UserTtd::class, 'user_id')->where('is_active', 1);
    }
    
    public function rubrics()
    {
        return $this->hasMany(Rubric::class, 'user_id', 'id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodiUser', 'id');
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultasUser', 'id');
    }

    public function universitas()
    {
        return $this->belongsTo(Universitas::class, 'id_universitasUser', 'id');
    }
}
