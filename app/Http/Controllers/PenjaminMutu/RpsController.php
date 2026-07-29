<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\RPS;
use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\RpsValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RpsController extends Controller
{  
    // public function index(Request $request)
    // {
    //     $user = Auth::user();
    //     $otoritas = $user->otoritas->otoritas;

    //     // =========================
    //     // 0) BASE QUERY
    //     // =========================
    //     $query = Rps::query()
    //         ->where('status', 'published')
    //         ->with(['mk.prodi.fakultas', 'latestValidation']);

    //     // =========================
    //     // 1) SCOPE SESUAI OTORITAS
    //     // =========================
    //     if (in_array($otoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {
    //         $query->whereHas('mk', fn ($q) => $q->where('id_prodi', $user->id_prodiUser));
    //     } elseif ($otoritas === 'Penjamin Mutu Fakultas') {
    //         $query->whereHas('mk.prodi', fn ($q) => $q->where('id_fakultas', $user->id_fakultasUser));
    //     } elseif ($otoritas === 'Penjamin Mutu Universitas') {
    //         $query->whereHas('mk.prodi.fakultas', fn ($q) => $q->where('id_universitas', $user->id_universitasUser));
    //     }

    //     // =========================
    //     // 2) FILTER DARI COMPONENT
    //     // =========================
    //     if ($request->filled('universitas_id')) {
    //         $query->whereHas('mk.prodi.fakultas', fn ($q) => $q->where('id_universitas', $request->universitas_id));
    //     }

    //     if ($request->filled('fakultas_id')) {
    //         $query->whereHas('mk.prodi', fn ($q) => $q->where('id_fakultas', $request->fakultas_id));
    //     }

    //     if ($request->filled('prodi_id')) {
    //         $query->whereHas('mk', fn ($q) => $q->where('id_prodi', $request->prodi_id));
    //     }

    //     // FILTER KURIKULUM
    //     if ($request->filled('kurikulum_id')) {
    //         // Asumsi: kolom di tabel mks adalah kurikulum_id
    //         $query->whereHas('mk', fn ($q) => $q->where('id_kurikulum', $request->kurikulum_id));
    //     }

    //     $rpss = $query->latest()->get();

    //     // =========================
    //     // 3) DATA UNTUK DROPDOWN FILTER
    //     // =========================
    //     $universities = Universitas::query()
    //         ->when(!empty($user->id_universitasUser), fn($q) => $q->where('id', $user->id_universitasUser))
    //         ->orderBy('nama')
    //         ->get();

    //     $faculties = Fakultas::query()
    //         ->when($request->filled('universitas_id'), fn($q) => $q->where('id_universitas', $request->universitas_id))
    //         ->when(!$request->filled('universitas_id') && !empty($user->id_universitasUser), fn($q) => $q->where('id_universitas', $user->id_universitasUser))
    //         ->when(!empty($user->id_fakultasUser), fn($q) => $q->where('id', $user->id_fakultasUser))
    //         ->orderBy('nama')
    //         ->get();

    //     $programs = Prodi::query()
    //         ->when($request->filled('fakultas_id'), fn($q) => $q->where('id_fakultas', $request->fakultas_id))
    //         ->when(!$request->filled('fakultas_id') && !empty($user->id_fakultasUser), fn($q) => $q->where('id_fakultas', $user->id_fakultasUser))
    //         ->when(!empty($user->id_prodiUser), fn($q) => $q->where('id', $user->id_prodiUser))
    //         ->orderBy('nama')
    //         ->get();

    //     $kurikulums = Kurikulum::query()
    //         // kalau tabel kurikulums kolomnya id_prodi, ganti prodi_id -> id_prodi
    //         ->when($request->filled('prodi_id'), fn($q) => $q->where('id_prodi', $request->prodi_id))
    //         ->when(!$request->filled('prodi_id') && !empty($user->id_prodiUser), fn($q) => $q->where('id_prodi', $user->id_prodiUser))
    //         ->orderByDesc('tahun')
    //         ->get();

    //     // =========================
    //     // 4) FLAG TAMPIL FILTER
    //     // =========================
    //     $showUniversitas = false;
    //     $showFakultas = ($otoritas === 'Penjamin Mutu Universitas');
    //     $showProdi = in_array($otoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']);

    //     return view('penjamin-mutu.rps.rps_list', compact(
    //         'rpss',
    //         'universities',
    //         'faculties',
    //         'programs',
    //         'kurikulums',
    //         'otoritas',
    //         'showUniversitas',
    //         'showFakultas',
    //         'showProdi'
    //     ));
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $otoritas = $user->otoritas->otoritas;

        $query = Rps::with(['mk.prodi.fakultas', 'latestValidation'])
            ->where('status', 'published');

        // Scope akses sesuai otoritas
        if (in_array($otoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {
            $query->whereHas('mk', function ($q) use ($user) {
                $q->where('id_prodi', $user->id_prodiUser);
            });
        } elseif ($otoritas === 'Penjamin Mutu Fakultas') {
            $query->whereHas('mk.prodi', function ($q) use ($user) {
                $q->where('id_fakultas', $user->id_fakultasUser);
            });
        } elseif ($otoritas === 'Penjamin Mutu Universitas') {
            $query->whereHas('mk.prodi.fakultas', function ($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
            });
        }

        // Filter request
        if ($request->filled('universitas_id')) {
            $query->whereHas('mk.prodi.fakultas', function ($q) use ($request) {
                $q->where('id_universitas', $request->universitas_id);
            });
        }

        if ($request->filled('fakultas_id')) {
            $query->whereHas('mk.prodi', function ($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            });
        }

        if ($request->filled('prodi_id')) {
            $query->whereHas('mk', function ($q) use ($request) {
                $q->where('id_prodi', $request->prodi_id);
            });
        }

        if ($request->filled('kurikulum_id')) {
            $query->whereHas('mk', function ($q) use ($request) {
                $q->where('id_kurikulum', $request->kurikulum_id);
            });
        }

        if ($request->filled('mk_kode')) {
            $query->whereHas('mk', function ($q) use ($request) {
                $q->where('kode', $request->mk_kode);
            });
        }

        $rpss = $query->latest()->get();

        // Data dropdown
        $universities = Universitas::orderBy('nama')->get();

        $faculties = Fakultas::query()
            ->when($request->filled('universitas_id'), function ($q) use ($request) {
                $q->where('id_universitas', $request->universitas_id);
            })
            ->when($otoritas === 'Penjamin Mutu Universitas', function ($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
            })
            ->when($otoritas === 'Penjamin Mutu Fakultas', function ($q) use ($user) {
                $q->where('id', $user->id_fakultasUser);
            })
            ->orderBy('nama')
            ->get();

        $programs = Prodi::query()
            ->when($request->filled('fakultas_id'), function ($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            })
            ->when($otoritas === 'Penjamin Mutu Fakultas', function ($q) use ($user) {
                $q->where('id_fakultas', $user->id_fakultasUser);
            })
            ->when(in_array($otoritas, ['Kepala Program Studi', 'Penjamin Mutu Program Studi']), function ($q) use ($user) {
                $q->where('id', $user->id_prodiUser);
            })
            ->orderBy('nama')
            ->get();

        return view('penjamin-mutu.rps.rps_list', [
            'rpss' => $rpss,
            'universities' => $universities,
            'faculties' => $faculties,
            'programs' => $programs,
            'otoritas' => $otoritas,

            // cukup kirim yang memang dipakai component
            'showUniversitas' => false,
            'showFakultas' => $otoritas === 'Penjamin Mutu Universitas',
            'showProdi' => in_array($otoritas, ['Penjamin Mutu Universitas', 'Penjamin Mutu Fakultas']),
        ]);
    }
 
    public function validationList()
    {
        $user = Auth::user();
        $rpss = Rps::where('status', 'pending')
            ->whereHas('mk', fn($q) => $q->where('id_prodi', $user->id_prodiUser))  
            ->with('mk.prodi')
            ->latest()
            ->get();
            
        return view('penjamin-mutu.rps.rps_validation_list', compact('rpss'));
    }
    private function currentPrefix(): string
    {
        $otoritas = auth()->user()->otoritas->otoritas;
    
        return match ($otoritas) {
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
            'Kepala Program Studi' => 'kepala-program-studi.',
            default => 'penjamin-mutu.program-studi.',
        };
    }
 
    public function approve(Request $request, $id)
    {
        $rps = Rps::where('status', 'pending')->findOrFail($id);

        DB::transaction(function () use ($rps, $request) {
            $rps->status = 'published';
            $rps->save();

            $rps->validations()->create([
                'validator_id' => auth()->id(),
                'status'       => 'published',
                'catatan'      => 'RPS telah disetujui.',
            ]);
        });
        return redirect()
            ->route($this->currentPrefix() . 'rps.validation.list')
            ->with('success', 'RPS berhasil dipublikasikan.');
    }
 
    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:10']);
        $rps = Rps::where('status', 'pending')->findOrFail($id);

        DB::transaction(function () use ($rps, $request) {
            $rps->status = 'rejected';
            $rps->save();

            $rps->validations()->create([
                'validator_id' => auth()->id(),
                'status'       => 'rejected',
                'catatan'      => $request->catatan,
            ]);
        });
        return redirect()
            ->route($this->currentPrefix() . 'rps.validation.list')
            ->with('error', 'RPS telah ditolak dan dikembalikan ke dosen.');
    }
}