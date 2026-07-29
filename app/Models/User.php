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

    public function otoritas()
    {
        return $this->hasMany(UserOtoritas::class, 'user_id');
    }

    public function getOtoritasAttribute()
    {
        return $this->otoritas()->where('active', true)->first();
    }

    // Helper method untuk cek otoritas
    public function hasOtoritas($otoritas)
    {
        return $this->otoritas()->where('otoritas', $otoritas)->where('active', true)->exists();
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
