<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $mahasiswa = Mahasiswa::with('prodi')
            ->where('Nama', $user->name)
            ->where('id_prodi', $user->id_prodiUser)
            ->first();

        if (!$mahasiswa) {
            return view('mahasiswa.dashboard', [
                'mahasiswa'  => null,
                'sksLulus'   => 0,
                'ipk'        => 0,
                'avgCpmk'    => 0,
                'avgCpl'     => 0,
                'topCpmks'   => [],
                'topProfesi' => [],
            ]);
        }

        $competencyData = $mahasiswa->getCompetencyData();
        $allCpmks = collect($competencyData['cpmks']);
        $allCpls  = collect($competencyData['cpls']);

        return view('mahasiswa.dashboard', [
            'mahasiswa'  => $mahasiswa,
            'sksLulus'   => $mahasiswa->calculateSksLulus(),
            'ipk'        => $mahasiswa->calculateIPK(),
            'avgCpmk'    => round($allCpmks->avg('nilai') ?? 0, 1),
            'avgCpl'     => round($allCpls->avg('nilai') ?? 0, 1),
            'topCpmks'   => $allCpmks->sortByDesc('nilai')->take(5)->values(),
            'topProfesi' => $mahasiswa->getTopProfesi($allCpmks),
        ]);
    }
}
