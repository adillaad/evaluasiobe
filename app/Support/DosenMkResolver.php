<?php

namespace App\Support;

use App\Models\MK;
use App\Models\RPS;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DosenMkResolver
{
    public static function forUser(User $user): Collection
    {
        $prodiId = $user->id_prodiUser;

        if (! $prodiId) {
            return collect();
        }

        $userName = $user->name;

        $kodeMks = RPS::query()
            ->where('id_prodi', $prodiId)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('mks')
                    ->whereColumn('mks.kode', 'rpss.kode_mk');
            })
            ->where(function ($query) use ($userName) {
                $query->where('pengembang', $userName)
                    ->orWhere('dosen', $userName)
                    ->orWhere('dosen_anggota1', $userName)
                    ->orWhere('dosen_anggota2', $userName)
                    ->orWhere('koordinator', $userName);
            })
            ->pluck('kode_mk')
            ->filter()
            ->unique();

        $mks = MK::query()
            ->whereIn('kode', $kodeMks)
            ->where('id_prodi', $prodiId)
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        if ($mks->isNotEmpty()) {
            return $mks;
        }

        return MK::where('id_prodi', $prodiId)
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();
    }
}
