<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ProfilLulusan extends Model
{
    use HasFactory;
    protected $table = 'profil_lulusan';
    public $timestamps = false;
    protected $fillable = ['namaProfil', 'kode', 'deskripsi', 'id_prodi', 'kurikulum_id', 'acuan', 'jenis'];

    protected $guarded = ['id'];

    public function cpls()
    {
        return $this->belongsToMany(CPL::class, 'profil_cpl', 'idProfil', 'idCpl');
    }
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }
    
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }
    
    public function scopeFilterOtoritas($query, $user, $idKurikulum = null)
    {
        if ($idKurikulum) {
            $query->where('kurikulum_id', $idKurikulum);
        }

        $namaOtoritas = $user->otoritas->otoritas;

        if (in_array($namaOtoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            return $query->whereHas('prodi.fakultas', function ($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
            });
        }

        if (in_array($namaOtoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            return $query->whereHas('prodi', function ($q) use ($user) {
                $q->where('id_fakultas', $user->id_fakultasUser);
            });
        }

        return $query->where('id_prodi', $user->id_prodiUser);
    }

    public static function queryProfilMK($user, $request = null)
    {
        $otoritas    = $user->otoritas->otoritas ?? '';
        $universityId = $request?->universitas_id;
        $facultyId    = $request?->fakultas_id;
        $programId    = $request?->prodi_id;
        $kurikulumId  = $request?->kurikulum_id;

        $query = DB::table('profil_lulusan')
            ->join('profil_cpl', 'profil_lulusan.id', '=', 'profil_cpl.idProfil')
            ->join('cpls', 'profil_cpl.idCpl', '=', 'cpls.id')
            ->leftJoin('mk_cpl', 'cpls.id', '=', 'mk_cpl.cpl_id')
            ->leftJoin('mks', 'mk_cpl.mk_kode', '=', 'mks.kode')
            ->join('prodi', 'profil_lulusan.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->leftJoin('kurikulums', 'profil_lulusan.kurikulum_id', '=', 'kurikulums.id')
            ->select(
                'profil_lulusan.id as profil_id',
                'profil_lulusan.kode as profil_kode',
                'profil_lulusan.deskripsi as profil_nama',
                'profil_lulusan.kurikulum_id',
                'kurikulums.tahun as kurikulum_tahun',
                'universitas.nama as universitas_nama',
                'fakultas.nama as fakultas_nama',
                'prodi.nama as prodi_nama',
                'mk_cpl.mk_kode',
                'mks.nama as mk_nama',
                'mks.semester as mk_semester',
                'mks.bobot_teori',
                'mks.bobot_praktikum',
                'mks.rumpun'
            );

        // --- filter otoritas ---
        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $query->where('fakultas.id_universitas', $universityId ?? $user->id_universitasUser);
            if ($facultyId) $query->where('fakultas.id', $facultyId);
            if ($programId) $query->where('prodi.id', $programId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $query->where('fakultas.id', $facultyId ?? $user->id_fakultasUser);
            if ($programId) $query->where('prodi.id', $programId);
        } else {
            $query->where('prodi.id', $programId ?? $user->id_prodiUser);
        }

        if ($kurikulumId) {
            $query->where('profil_lulusan.kurikulum_id', $kurikulumId);
        }

        return $query;
    }

    public static function generateKode(int $prodiId, int $kurikulumId): string
    {
        $jumlah = static::where('id_prodi', $prodiId)
            ->where('kurikulum_id', $kurikulumId)
            ->count();

        return 'PL0' . ($jumlah + 1);
    }
}

