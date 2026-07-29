<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RekomendasiMkController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $mahasiswa = $this->guardMahasiswa($user);

        $competencyData = $mahasiswa->getCompetencyData();
        $cpmkMap        = $competencyData['cpmks']->keyBy('id');
        $takenCourses   = $mahasiswa->getTakenCourses();
        $sksLulus       = $mahasiswa->calculateSksLulus();
        $avgCpmkKeseluruhan = round($competencyData['cpmks']->avg('nilai') ?? 0, 2);

        $mkList = DB::table('cpmk_mk')
            ->join('mks', 'cpmk_mk.mk_kode', '=', 'mks.kode')
            ->whereIn('cpmk_mk.cpmk_id', $cpmkMap->keys())
            ->where('mks.id_prodi', $user->id_prodiUser)
            ->whereNotIn('mks.kode', $takenCourses)
            ->select(
                'mks.kode',
                'mks.nama',
                'mks.semester',
                'mks.bobot_teori',
                'mks.bobot_praktikum',
                'mks.rumpun',
                'mks.prasyarat',
                'mks.deskripsi'
            )
            ->distinct()
            ->get();

        $rekomendasiMk = $mkList
            ->map(fn($mk) => $this->buildRekomendasiItem($mk, $cpmkMap, $takenCourses))
            ->sortBy(fn($mk) => [$mk['prasyarat_terpenuhi'] ? 0 : 1, $mk['avg_cpmk']])
            ->values();

        $stats = [
            'total_mk_rekomendasi' => $rekomendasiMk->count(),
            'avg_keseluruhan'      => $avgCpmkKeseluruhan,
            'mk_prioritas'         => $rekomendasiMk->filter(fn($m) => $m['avg_cpmk'] < 70)->count(),
        ];

        return view('mahasiswa.rekomendasi-mk', compact('rekomendasiMk', 'stats', 'sksLulus'));
    }

    private function buildRekomendasiItem(
        object $mk,
        \Illuminate\Support\Collection $cpmkMap,
        array $takenCourses
    ): array {
        $cpmkTerkait = DB::table('cpmk_mk')
            ->where('cpmk_mk.mk_kode', $mk->kode)
            ->whereIn('cpmk_mk.cpmk_id', $cpmkMap->keys())
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->select('cpmks.id', 'cpmks.kode as cpmk_kode', 'cpmks.judul as cpmk_deskripsi')
            ->get()
            ->map(function ($item) use ($cpmkMap) {
                $data = $cpmkMap->get($item->id);
                return [
                    'kode'        => $item->cpmk_kode,
                    'deskripsi'   => Str::limit($item->cpmk_deskripsi, 100),
                    'persentase'  => $data['nilai']  ?? 0,
                    'status'      => $data['status'] ?? 'Tidak diketahui',
                    'badge_class' => Prodi::badgeClassKompetensi($data['status'] ?? 'Kurang'),
                ];
            });

        $avg = $cpmkTerkait->avg('persentase') ?? 0;

        $prasyaratTerpenuhi = true;
        if ($mk->prasyarat && $mk->prasyarat !== '-') {
            foreach (array_map('trim', explode(',', $mk->prasyarat)) as $kode) {
                if ($kode && !in_array($kode, $takenCourses)) {
                    $prasyaratTerpenuhi = false;
                    break;
                }
            }
        }

        return [
            'kode'                => $mk->kode,
            'nama'                => $mk->nama,
            'semester'            => $mk->semester,
            'sks'                 => ($mk->bobot_teori ?? 0) + ($mk->bobot_praktikum ?? 0),
            'rumpun'              => $mk->rumpun,
            'jenis'               => $mk->rumpun ?? null,
            'prasyarat'           => $mk->prasyarat,
            'prasyarat_terpenuhi' => $prasyaratTerpenuhi,
            'deskripsi'           => Str::limit($mk->deskripsi, 150),
            'cpmk_terkait'        => $cpmkTerkait,
            'avg_cpmk'            => round($avg, 2),
            'priority_class'      => $this->priorityClass($avg),
        ];
    }

    private function guardMahasiswa(\App\Models\User $user): Mahasiswa
    {
        $isMahasiswa = DB::table('user_otoritas')
            ->where('user_id', $user->id)
            ->where('otoritas', 'Mahasiswa')
            ->where('active', 1)
            ->exists();

        abort_unless($isMahasiswa, 403, 'Halaman hanya untuk Mahasiswa.');

        $mahasiswa = Mahasiswa::findForUser($user);

        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        return $mahasiswa;
    }

    private function priorityClass(float $avg): string
    {
        if ($avg < 60) return 'danger';
        if ($avg < 75) return 'warning';
        return 'success';
    }
}
