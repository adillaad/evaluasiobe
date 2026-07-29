<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\Rubric;
use App\Models\MK;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Universitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RubrikController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? '';

        $query = Rubric::with([
            'mk.prodi.fakultas.universitas',
            'user'
        ]);

        $universities = collect();
        $faculties = collect();
        $programs = collect();

        // =========================
        // BATASAN AKSES SESUAI ROLE
        // =========================

        if ($userOtoritas === 'Penjamin Mutu Universitas') {
            $universitasIdUser = $user->id_universitasUser ?? null;

            $query->whereHas('mk.prodi.fakultas.universitas', function ($q) use ($universitasIdUser) {
                $q->where('id', $universitasIdUser);
            });

            $universities = Universitas::where('id', $universitasIdUser)->get();
            $faculties = Fakultas::where('id_universitas', $universitasIdUser)->get();

            $programs = Prodi::when($request->fakultas_id, function ($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            })->get();
        }

        elseif ($userOtoritas === 'Penjamin Mutu Fakultas') {
            $fakultasIdUser = $user->id_fakultasUser ?? null;

            $query->whereHas('mk.prodi.fakultas', function ($q) use ($fakultasIdUser) {
                $q->where('id', $fakultasIdUser);
            });

            $faculties = Fakultas::where('id', $fakultasIdUser)->get();
            $programs = Prodi::where('id_fakultas', $fakultasIdUser)->get();
        }

        elseif (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $prodiIdUser = $user->id_prodiUser ?? $user->id_prodi ?? null;

            $query->whereHas('mk.prodi', function ($q) use ($prodiIdUser) {
                $q->where('id', $prodiIdUser);
            });
        }

        // =========================
        // FILTER
        // =========================

        if ($request->filled('fakultas_id')) {
            $query->whereHas('mk.prodi.fakultas', function ($q) use ($request) {
                $q->where('id', $request->fakultas_id);
            });
        }

        if ($request->filled('prodi_id')) {
            $query->whereHas('mk.prodi', function ($q) use ($request) {
                $q->where('id', $request->prodi_id);
            });
        }

        $rubriks = $query->latest()->get();

        return view('penjamin-mutu.rubrik.list', compact(
            'rubriks',
            'universities',
            'faculties',
            'programs',
            'userOtoritas'
        ));
    }

    public function download($id)
    {
        $rubric = Rubric::findOrFail($id);

        if (!Storage::disk('public')->exists($rubric->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $rubric->file_path,
            'Rubrik_'.$rubric->mk_kode.'_'.$rubric->jenis_rubrik.'.xlsx'
        );
    }
}