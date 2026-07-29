<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class profesi extends Model
{
    use HasFactory;
    protected $table = 'profesi';

    protected $fillable = [
        'nama','kurikulum_id','id_prodi'
    ];
    
     public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function cpmks()
    {
        return $this->belongsToMany(CPMK::class, 'profesi_cpmk', 'profesi_id', 'cpmk_id');
    }
    
     public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }
    
     public function scopeForUser($query, $user)
    {
        $query->join('prodi', 'profesi.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('profesi.*');

        return match (true) {
            in_array($user->otoritas->otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])
            => $query->where('fakultas.id_universitas', $user->id_universitasUser),

            in_array($user->otoritas->otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])
            => $query->where('fakultas.id', $user->id_fakultasUser),

            default
            => $query->where('prodi.id', $user->id_prodiUser),
        };
    }

public static function getPemetaanForMahasiswa(
        \Illuminate\Support\Collection $allCpmks,
        array $takenCourses
    ): \Illuminate\Support\Collection {
        $cpmkMap = $allCpmks->filter(fn($c) => ($c['nilai'] ?? 0) > 0)->keyBy('id');

        if ($cpmkMap->isEmpty()) {
            return collect();
        }

        $profesiRaw = DB::table('profesi')
            ->join('profesi_cpmk', 'profesi.id', '=', 'profesi_cpmk.profesi_id')
            ->join('cpmks', 'profesi_cpmk.cpmk_id', '=', 'cpmks.id')
            ->leftJoin('cpmk_mk', 'cpmks.id', '=', 'cpmk_mk.cpmk_id')
            ->leftJoin('mks', 'cpmk_mk.mk_kode', '=', 'mks.kode')
            ->select(
                'profesi.id as profesi_id',
                'profesi.nama as profesi_nama',
                'cpmks.id as cpmk_id',
                'cpmks.kode as cpmk_kode',
                'cpmk_mk.mk_kode as mk_kode',
                'mks.nama as mk_nama'
            )
            ->get();

        return $profesiRaw
            ->groupBy('profesi_id')
            ->map(function ($items, $profesiId) use ($cpmkMap, $takenCourses, $profesiRaw) {
                $profesiNama     = $items->first()->profesi_nama;
                $allCpmkIds      = $items->pluck('cpmk_id')->unique();

                $dinilaiIds = $allCpmkIds->filter(fn($id) => $cpmkMap->has($id));

                if ($dinilaiIds->isEmpty()) {
                    return null;
                }

                $requiredMkKodes = $items->pluck('mk_kode')->filter()->unique()->toArray();

                $cpmksFormatted = static::formatCpmksForProfesi(
                    $allCpmkIds->toArray(),   // tetap tampilkan semua CPMK profesi
                    $cpmkMap,
                    $takenCourses,
                    $profesiId,
                    $profesiRaw
                );

                // Hitung match_percentage HANYA dari CPMK yang sudah dinilai
                $dinilaiCpmks    = $cpmksFormatted->filter(fn($c) => $c['nilai'] > 0);
                $totalScore      = $dinilaiCpmks->sum('nilai');
                $dinilaiCount    = $dinilaiCpmks->count();

                // Persentase = rata-rata nilai dari CPMK yang dinilai saja
                $totalCpmk       = $allCpmkIds->count();
                $matchPercentage = $totalCpmk > 0
                    ? round($totalScore / $totalCpmk, 2)
                    : 0;

                $statusLabel     = self::statusKompetensi($matchPercentage);
                $coursesInvolved = collect($requiredMkKodes)
                    ->filter(fn($mk) => in_array($mk, $takenCourses))
                    ->values()
                    ->toArray();

                return [
                    'id'                  => $profesiId,
                    'nama'                => $profesiNama,
                    'match_percentage'    => $matchPercentage,
                    'status'              => $statusLabel,
                    'badge_class'         => Prodi::badgeClassKompetensi($statusLabel),
                    'cpmks'               => $cpmksFormatted->sortByDesc('nilai')->values(),
                    'total_cpmk_matched'  => $dinilaiCount,
                    'total_cpmk_required' => $allCpmkIds->count(),
                    'courses_involved'    => $coursesInvolved,
                ];
            })
            ->filter()                        // buang profesi yang null (tidak ada CPMK dinilai)
            ->sortByDesc('match_percentage')
            ->values();
    }

    public static function getTopProfesi(
        \Illuminate\Support\Collection $allCpmks,
        int $take = 4
    ): \Illuminate\Support\Collection {
        // Hanya CPMK yang sudah dinilai
        $cpmkMap = $allCpmks->filter(fn($c) => ($c['nilai'] ?? 0) > 0)->keyBy('id');

        if ($cpmkMap->isEmpty()) {
            return collect();
        }

        return DB::table('profesi')
            ->join('profesi_cpmk', 'profesi.id', '=', 'profesi_cpmk.profesi_id')
            ->select('profesi.id as profesi_id', 'profesi.nama', 'profesi_cpmk.cpmk_id')
            ->get()
            ->groupBy('profesi_id')
            ->map(function ($items) use ($cpmkMap) {
                $allIds    = $items->pluck('cpmk_id')->unique();
                // Hanya yang dinilai
                $dinilaiIds = $allIds->filter(fn($id) => $cpmkMap->has($id));

                if ($dinilaiIds->isEmpty()) return null;

                $total = $dinilaiIds->sum(fn($id) => $cpmkMap->get($id)['nilai'] ?? 0);

                return [
                    'nama'             => $items->first()->nama,
                    'match_percentage' => round($total / $dinilaiIds->count(), 1),
                ];
            })
            ->filter()                        // buang profesi tanpa CPMK dinilai
            ->sortByDesc('match_percentage')
            ->take($take)
            ->values();
    }

    public static function statusKompetensi(float $score): string
    {
        if ($score >= 85) return 'Sangat Baik';
        if ($score >= 70) return 'Baik';
        if ($score >= 60) return 'Cukup';
        return 'Kurang';
    }

    private static function formatCpmksForProfesi(
        array $requiredCpmkIds,
        \Illuminate\Support\Collection $cpmkMap,
        array $takenCourses,
        int $profesiId,
        \Illuminate\Support\Collection $profesiRaw
    ): \Illuminate\Support\Collection {
        return DB::table('cpmks')
            ->whereIn('id', $requiredCpmkIds)
            ->get(['id', 'kode', 'judul as deskripsi'])
            ->map(function ($cpmk) use ($cpmkMap, $takenCourses, $profesiId, $profesiRaw) {
                $studentCpmk = $cpmkMap->get($cpmk->id);
                $nilai       = $studentCpmk ? $studentCpmk['nilai'] : 0;
                $status      = $studentCpmk
                    ? $studentCpmk['status']
                    : 'Belum Dinilai';
                $badgeClass  = $studentCpmk
                    ? Prodi::badgeClassKompetensi($status)
                    : 'secondary';

                $relatedMkKodes = $profesiRaw
                    ->where('profesi_id', $profesiId)
                    ->where('cpmk_id', $cpmk->id)
                    ->whereNotNull('mk_kode')
                    ->pluck('mk_kode')
                    ->unique()
                    ->toArray();

                $courses = collect($relatedMkKodes)->map(function ($mkKode) use ($takenCourses) {
                    $mk = DB::table('mks')->where('kode', $mkKode)->first();
                    return [
                        'kode'    => $mkKode,
                        'nama'    => $mk?->nama ?? $mkKode,
                        'diambil' => in_array($mkKode, $takenCourses),
                    ];
                })->toArray();

                return [
                    'id'         => $cpmk->id,
                    'kode'       => $cpmk->kode,
                    'deskripsi'  => Str::limit($cpmk->deskripsi ?? 'Tidak ada deskripsi', 100),
                    'nilai'      => $nilai,
                    'status'     => $status,
                    'badge_class' => $badgeClass,
                    'courses'    => $courses,
                ];
            });
    }
}
