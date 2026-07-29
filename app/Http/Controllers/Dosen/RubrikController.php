<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Imports\ImportRubrik;
use App\Models\Rubric;
use App\Models\MK;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Universitas;
use App\Support\DosenMkResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class RubrikController extends Controller
{
    public function Add()
    {
        $user = auth()->user();

        $mks = DosenMkResolver::forUser($user);
        $mks->load(['kurikulum', 'prodi']);

        return view('dosen.rubrik.add', compact('mks'));
    }

    public function getJenisRubrikByMk($mkKode)
    {
        $mk = MK::with([
            'cplMkCpmkPenilaians.penilaianMetode.metode'
        ])->where('kode', $mkKode)->first();

        if (!$mk) {
            return response()->json([]);
        }

        $result = $mk->cplMkCpmkPenilaians
            ->flatMap(function ($item) {
                return $item->penilaianMetode;
            })
            ->map(function ($pm) {
                return optional($pm->metode)->nama;
            })
            ->filter()
            ->unique()
            ->values();

        return response()->json($result);
    }

    public function previewRubrik(Request $request)
    {
        $request->validate([
            'rubrik_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $import = new ImportRubrik();
        Excel::import($import, $request->file('rubrik_file'));

        if (!$import->type) {
            return response()->json([
                'success' => false,
                'message' => 'Template rubrik tidak dikenali. Pastikan file menggunakan salah satu dari 3 template yang disediakan.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'type' => $import->type,
            'headers' => $import->headers,
            'data' => $import->data,
        ]);
    }

    public function Store(Request $request)
    {
        $request->validate([
            'mk_kode'      => 'required|exists:mks,kode',
            'jenis_rubrik' => 'required|string|max:100',
            'rubrik_file'  => 'required|file|mimes:xlsx,xls'
        ]);

        // deteksi type dulu dari file yang diupload
        $import = new ImportRubrik();
        Excel::import($import, $request->file('rubrik_file'));

        if (!$import->type) {
            return redirect()
                ->back()
                ->withErrors(['rubrik_file' => 'Template rubrik tidak dikenali.'])
                ->withInput();
        }

        $user = auth()->user();

        $existing = Rubric::where('mk_kode', $request->mk_kode)
            ->where('user_id', $user->id)
            ->where('jenis_rubrik', $request->jenis_rubrik)
            ->first();

        $file = $request->file('rubrik_file');
        $path = $file->store('rubrics', 'public');

        if ($existing) {
            if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }

            $existing->update([
                'rubric_type' => $import->type,
                'file_path'   => $path,
            ]);

            return redirect()
                ->route('dosen.rubrik-list')
                ->with('success', 'Rubrik berhasil diperbarui.');
        }

        Rubric::create([
            'mk_kode'      => $request->mk_kode,
            'user_id'      => $user->id,
            'jenis_rubrik' => $request->jenis_rubrik,
            'rubric_type'  => $import->type,
            'file_path'    => $path,
        ]);

        return redirect()
            ->route('dosen.rubrik-list')
            ->with('success', 'Rubrik berhasil disimpan.');
    }

    public function List(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? '';
    
        $query = Rubric::with([
            'mk.prodi.fakultas.universitas',
            'user'
        ]);
    
        // =========================
        // BATASAN AKSES SESUAI ROLE
        // =========================
    
        if ($userOtoritas === 'Dosen') {
            $query->where('user_id', $user->id);
        }
    
        elseif ($userOtoritas === 'Wakil Dekan') {
            $fakultasIdUser = $user->prodi->fakultas->id ?? null;
    
            $query->whereHas('mk.prodi.fakultas', function ($q) use ($fakultasIdUser) {
                $q->where('id', $fakultasIdUser);
            });
        }
    
        elseif ($userOtoritas === 'Wakil Rektor') {
            $universitasIdUser = $user->prodi->fakultas->universitas->id ?? null;
    
            $query->whereHas('mk.prodi.fakultas.universitas', function ($q) use ($universitasIdUser) {
                $q->where('id', $universitasIdUser);
            });
        }
    
        // =========================
        // FILTER (sesuai UI baru)
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
    
        // =========================
        // DATA UNTUK FILTER COMPONENT
        // =========================
    
        $universities = collect();
        $faculties = collect();
        $programs = collect();
    
        if ($userOtoritas === 'Wakil Rektor') {
            $universitasIdUser = $user->prodi->fakultas->universitas->id ?? null;
    
            $universities = Universitas::where('id', $universitasIdUser)->get();
    
            $faculties = Fakultas::where('id_universitas', $universitasIdUser)->get();
    
            $programs = Prodi::when($request->fakultas_id, function ($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            })->get();
        }
    
        elseif ($userOtoritas === 'Wakil Dekan') {
            $fakultasIdUser = $user->prodi->fakultas->id ?? null;
    
            $faculties = Fakultas::where('id', $fakultasIdUser)->get();
    
            $programs = Prodi::where('id_fakultas', $fakultasIdUser)->get();
        }
    
        // Dosen tidak pakai filter → kosong aja
    
        return view('dosen.rubrik.list', compact(
            'rubriks',
            'universities',
            'faculties',
            'programs',
            'userOtoritas'
        ));
    }

    public function download($id)
    {
        $user = auth()->user();
        $rubric = Rubric::with('mk.prodi.fakultas.universitas')->findOrFail($id);

        $userOtoritas = $user->otoritas->otoritas ?? '';

        // ======================
        // CEK OTORITAS
        // ======================

        if ($userOtoritas === 'Dosen') {

            if ($rubric->user_id != $user->id) {
                abort(403);
            }

        } elseif ($userOtoritas === 'Wakil Dekan') {

            $userFakultas = $user->prodi->fakultas->id ?? null;
            $rubricFakultas = $rubric->mk->prodi->fakultas->id ?? null;

            if ($userFakultas != $rubricFakultas) {
                abort(403);
            }

        } elseif ($userOtoritas === 'Wakil Rektor') {

            $userUniversitas = $user->prodi->fakultas->universitas->id ?? null;
            $rubricUniversitas = $rubric->mk->prodi->fakultas->universitas->id ?? null;

            if ($userUniversitas != $rubricUniversitas) {
                abort(403);
            }

        }

        // ======================
        // DOWNLOAD FILE
        // ======================

        if (!Storage::disk('public')->exists($rubric->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $rubric->file_path,
            'Rubrik_'.$rubric->mk_kode.'_'.$rubric->jenis_rubrik.'.xlsx'
        );
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $rubric = Rubric::findOrFail($id);

        // hanya dosen pemilik rubrik yang boleh hapus
        if ($rubric->user_id != $user->id) {
            abort(403, 'Anda tidak memiliki izin menghapus rubrik ini.');
        }

        // hapus file dari storage
        if ($rubric->file_path && Storage::disk('public')->exists($rubric->file_path)) {
            Storage::disk('public')->delete($rubric->file_path);
        }

        // hapus database
        $rubric->delete();

        return redirect()
            ->route('dosen.rubrik-list')
            ->with('success', 'Rubrik berhasil dihapus.');
    }
}