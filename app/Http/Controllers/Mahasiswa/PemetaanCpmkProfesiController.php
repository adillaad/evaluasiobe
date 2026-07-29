<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\profesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;

class PemetaanCpmkProfesiController extends Controller
{

    public function index()
    {
        $user      = Auth::user();
        $mahasiswa = $this->guardMahasiswa($user);
        $npm       = $mahasiswa->NPM;

        $mahasiswaData   = DB::table('mutus')->where('npm', $npm)->first();

        if (!$mahasiswaData) {
            abort(404, 'Data akademik tidak ditemukan.');
        }

        $competencyData  = $mahasiswa->getCompetencyData();
        $allCpmks        = $competencyData['cpmks'];           // Collection
        $takenCourses    = $mahasiswa->getTakenCourses();

        $profesiDenganDetail = Profesi::getPemetaanForMahasiswa($allCpmks, $takenCourses);

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
        $user      = Auth::user();
        $mahasiswa = $this->guardMahasiswa($user);
        $npm       = $mahasiswa->NPM;

        $mahasiswaData       = DB::table('mahasiswa')->where('NPM', $npm)->first();
        $prodi               = DB::table('prodi')->where('id', $user->id_prodiUser)->first();

        $competencyData      = $mahasiswa->getCompetencyData();
        $allCpmks            = $competencyData['cpmks'];
        $takenCourses        = $mahasiswa->getTakenCourses();

        $profesiDenganDetail = Profesi::getPemetaanForMahasiswa($allCpmks, $takenCourses);

        $pdf = Pdf::loadView('mahasiswa.pemetaan-cpmk-profesi-pdf', compact(
            'profesiDenganDetail',
            'allCpmks',
            'mahasiswaData',
            'prodi'
        ))->setPaper('a4', 'portrait');

        return $pdf->inline('Pemetaan-CPMK-Profesi-' . ($npm ?? 'mahasiswa') . '.pdf');
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
