<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\profesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PemetaanCpmkProfesiController extends Controller
{
    private function resolvePemetaanData(): array
    {
        $user      = Auth::user();
        $mahasiswa = $this->guardMahasiswa($user);
        $npm       = $mahasiswa->NPM;

        $mahasiswaData = DB::table('mahasiswa')->where('NPM', $npm)->first();
        if (!$mahasiswaData) {
            $mahasiswaData = DB::table('mutus')->where('npm', $npm)->first();
        }

        $prodi = Prodi::find($user->id_prodiUser);

        $competencyData      = $mahasiswa->getCompetencyData();
        $allCpmks            = $competencyData['cpmks'];
        $takenCourses        = $mahasiswa->getTakenCourses();

        $profesiDenganDetail = Profesi::getPemetaanForMahasiswa($allCpmks, $takenCourses);

        return [$profesiDenganDetail, $allCpmks, $mahasiswaData, $prodi, $takenCourses, $npm];
    }

    public function index()
    {
        [$profesiDenganDetail, $allCpmks, $mahasiswaData, $prodi, $takenCourses] = $this->resolvePemetaanData();

        if (!$mahasiswaData) {
            abort(404, 'Data akademik tidak ditemukan.');
        }

        $chartProfesi = $profesiDenganDetail->take(5)->map(fn($p) => [
            'label'     => Str::limit($p['nama'], 25),
            'value'     => (float) $p['match_percentage'],
            'full_name' => $p['nama'],
        ]);

        return view('mahasiswa.pemetaan-cpmk-profesi', compact(
            'profesiDenganDetail',
            'allCpmks',
            'mahasiswaData',
            'chartProfesi',
            'takenCourses'
        ));
    }

    public function pdf()
    {
        [$profesiDenganDetail, $allCpmks, $mahasiswaData, $prodi, $takenCourses, $npm] = $this->resolvePemetaanData();

        $pdf = Pdf::loadView('mahasiswa.pemetaan-cpmk-profesi-pdf', compact(
            'profesiDenganDetail',
            'allCpmks',
            'mahasiswaData',
            'prodi'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Pemetaan-CPMK-Profesi-' . ($npm ?? 'mahasiswa') . '.pdf');
    }

    public function print()
    {
        [$profesiDenganDetail, $allCpmks, $mahasiswaData, $prodi, $takenCourses, $npm] = $this->resolvePemetaanData();

        return view('mahasiswa.pemetaan-cpmk-profesi-pdf', compact(
            'profesiDenganDetail',
            'allCpmks',
            'mahasiswaData',
            'prodi'
        ) + ['isPrint' => true]);
    }

    private function guardMahasiswa(\App\Models\User $user): Mahasiswa
    {
        $isMahasiswa = DB::table('user_otoritas')
            ->where('user_id', $user->id)
            ->where('otoritas', 'Mahasiswa')
            ->where('active', 1)
            ->exists();

        abort_unless($isMahasiswa, 403, 'Halaman ini hanya dapat diakses oleh Mahasiswa.');

        $mahasiswa = Mahasiswa::findForUser($user);

        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        return $mahasiswa;
    }
}