<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\Kurikulum;
use App\Models\MK;
use App\Models\SubCpmk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CPLCPMKController extends Controller
{
    private function getKurikulumsForUser()
    {
        $user = auth()->user();
        $query = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('kurikulums.*', 'prodi.nama as nama_prodi')
            ->orderBy('kurikulums.tahun', 'desc');

        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $query->where('prodi.id', $user->id_prodiUser);
        }

        return $query->get();
    }

    public function indexCPLCPMKMK(Request $request)
    {
        $query = CPL::with(['cpmk.mks', 'mk'])
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.*');

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $query->where('cpls.id_kurikulum', $request->kurikulum_id);
        }

        $cpls = $query->get();
        $kurikulums = $this->getKurikulumsForUser();

        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->orderBy('mks.nama', 'asc')
            ->select('mks.*')->get();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_cpl_cpmk_mk', compact('cpls', 'kurikulums', 'mks'));
    }

    public function indexCPLCPMKMKSMT(Request $request)
    {
        $query = CPL::with(['cpmk.mks', 'mk'])
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $query->where('cpls.id_kurikulum', $request->kurikulum_id);
        }

        $cpls = $query->select('cpls.*')->get();
        $semesters = MK::select('semester')->whereNotNull('semester')->where('semester', '!=', 0)->distinct()->orderBy('semester')->get();
        
        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->whereNotNull('mks.semester')
            ->where('mks.semester', '!=', 0)
            ->orderBy('mks.semester', 'asc')
            ->orderBy('mks.kode', 'asc')
            ->select('mks.*')
            ->get();

        $kurikulums = $this->getKurikulumsForUser();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_cpl_cpmk_mk_semester', compact('semesters', 'cpls', 'mks', 'kurikulums'));
    }

    public function indexCPLMKCPMK(Request $request)
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.*')
            ->with(['cpmk', 'kurikulum']);

        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.*')
            ->with(['cpmks.cpl', 'cpl', 'kurikulum']);

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryMks->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryMks->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', auth()->user()->id_prodiUser);
            $queryMks->where('prodi.id', auth()->user()->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryMks->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $cpls = $queryCpl->get();
        $mks = $queryMks->get();
        $kurikulums = $this->getKurikulumsForUser();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_cpl_mk_cpmk', compact('cpls', 'mks', 'kurikulums'));
    }

    public function updateMatrixCPLMKCPMK(Request $request)
    {
        $matrix = $request->input('matrix', []); // Key 1: mk_kode, Key 2: cpl_id, Value: array of cpmk_ids
        $kurikulumId = $request->input('kurikulum_id');

        $queryMks = MK::query();
        if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMks->where('id_prodi', auth()->user()->id_prodiUser);
        }
        if ($kurikulumId) {
            $queryMks->where('id_kurikulum', $kurikulumId);
        }
        $mks = $queryMks->get();

        DB::transaction(function () use ($mks, $matrix) {
            foreach ($mks as $mk) {
                $allSelectedCpmkIds = [];
                $allSelectedCplIds = [];

                if (isset($matrix[$mk->kode]) && is_array($matrix[$mk->kode])) {
                    foreach ($matrix[$mk->kode] as $cplId => $cpmkIds) {
                        $cpmkArray = (array) $cpmkIds;
                        if (!empty($cpmkArray)) {
                            $allSelectedCplIds[] = (int) $cplId;
                            foreach ($cpmkArray as $cpmkId) {
                                $allSelectedCpmkIds[] = (int) $cpmkId;
                            }
                        }
                    }
                }

                $allSelectedCpmkIds = array_unique(array_filter($allSelectedCpmkIds));
                $allSelectedCplIds = array_unique(array_filter($allSelectedCplIds));

                // Sync CPMK-MK relationship
                $mk->cpmks()->sync($allSelectedCpmkIds);

                // Sync CPL-MK relationship
                $cplSyncData = [];
                foreach ($allSelectedCplIds as $cplId) {
                    $cplSyncData[$cplId] = ['id_prodi' => $mk->id_prodi];
                }
                $mk->cpl()->sync($cplSyncData);
            }
        });

        return redirect()->back()->with('success', 'Matriks Pemetaan CPL - MK - CPMK berhasil diperbarui.');
    }

    public function indexMKCPMKSubCPMK(Request $request)
    {
        $kurikulums = $this->getKurikulumsForUser();

        $query = MK::with(['cpmks.subCpmks.mks', 'cpmks.cpl', 'cpl', 'sub_cpmk.cpmk.cpl'])
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $query->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $mks = $query->select('mks.*')->get();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_mk_cpmk_subcpmk', compact('mks','kurikulums'));
    }

    public function updateMatrixMKCPMKSubCPMK(Request $request)
    {
        $matrix = $request->input('matrix', []); // matrix[mk_kode] = [sub_cpmk_id1, sub_cpmk_id2, ...]
        $uraianInputs = $request->input('uraian', []); // uraian[sub_cpmk_id] = "new description"
        $kodeInputs = $request->input('kode', []); // kode[sub_cpmk_id] = "new code"
        $kurikulumId = $request->input('kurikulum_id');

        $queryMks = MK::query();
        if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMks->where('id_prodi', auth()->user()->id_prodiUser);
        }
        if ($kurikulumId) {
            $queryMks->where('id_kurikulum', $kurikulumId);
        }
        $mks = $queryMks->get();

        DB::transaction(function () use ($mks, $matrix, $uraianInputs, $kodeInputs) {
            // Update Sub CPMK code (kode) and descriptions (uraian)
            $allSubIds = array_unique(array_merge(
                is_array($uraianInputs) ? array_keys($uraianInputs) : [],
                is_array($kodeInputs) ? array_keys($kodeInputs) : []
            ));

            foreach ($allSubIds as $subId) {
                $updateData = [];
                if (isset($kodeInputs[$subId]) && trim($kodeInputs[$subId]) !== '') {
                    $updateData['kode'] = trim($kodeInputs[$subId]);
                }
                if (isset($uraianInputs[$subId]) && trim($uraianInputs[$subId]) !== '') {
                    $updateData['uraian'] = trim($uraianInputs[$subId]);
                }
                if (!empty($updateData)) {
                    SubCpmk::where('id', $subId)->update($updateData);
                }
            }

            foreach ($mks as $mk) {
                $allSelectedSubCpmkIds = [];

                if (isset($matrix[$mk->kode]) && is_array($matrix[$mk->kode])) {
                    foreach ($matrix[$mk->kode] as $subId) {
                        if (!empty($subId)) {
                            $allSelectedSubCpmkIds[] = (int) $subId;
                        }
                    }
                }

                $allSelectedSubCpmkIds = array_unique(array_filter($allSelectedSubCpmkIds));
                $mk->sub_cpmk()->sync($allSelectedSubCpmkIds);

                // Auto-sync parent CPMKs and CPLs to MK
                if (!empty($allSelectedSubCpmkIds)) {
                    $subCpmks = SubCpmk::with('cpmk.cpl')->whereIn('id', $allSelectedSubCpmkIds)->get();
                    $cpmkIds = [];
                    $cplIds = [];
                    foreach ($subCpmks as $sub) {
                        if ($sub->cpmk_id) {
                            $cpmkIds[] = $sub->cpmk_id;
                            if ($sub->cpmk && $sub->cpmk->cpl_id) {
                                $cplIds[] = $sub->cpmk->cpl_id;
                            }
                        }
                    }
                    if (!empty($cpmkIds)) {
                        $mk->cpmks()->syncWithoutDetaching(array_unique($cpmkIds));
                    }
                    if (!empty($cplIds)) {
                        $cplSync = [];
                        foreach (array_unique($cplIds) as $cplId) {
                            $cplSync[$cplId] = ['id_prodi' => $mk->id_prodi];
                        }
                        $mk->cpl()->syncWithoutDetaching($cplSync);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Pemetaan & Deskripsi Sub CPMK berhasil diperbarui.');
    }

    public function addCPLCPMKMK()
    {
        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->orderBy('mks.nama', 'asc')
            ->select('mks.*')->get();

        return view('penjamin-mutu.cpl-cpmk.add_cpl_cpmk_mk', compact('mks'));
    }

    public function getCPMKByMK($mkId)
    {
        // Temukan BK yang dipilih
        $mk = MK::findOrFail($mkId);

        // Ambil semua MK yang terkait melalui CPL
        $cplIds = $mk->cpl->pluck('id'); // Semua CPL yang terkait dengan MK ini
        $cpmks = CPMK::where('id_prodi',auth()->user()->id_prodiUser)
            ->whereIn('cpl_id', $cplIds)
            ->orderBy('kode', 'asc')
            ->get(); // CPMK yang terkait dengan CPL tersebut

        return response()->json(['cpmks' => $cpmks]);
    }

    public function indexMKCPMK(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('cpmk_mk', 'bobot')) {
            \Illuminate\Support\Facades\Schema::table('cpmk_mk', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->float('bobot')->nullable()->default(0);
            });
        }

        $query = MK::with(['cpmks.cpl'])
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->orderBy('mks.nama', 'asc')
            ->select('mks.*');

        if ($request->filled('kurikulum_id')) {
            $query->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $mks = $query->get();
        $semesters = MK::select('semester')->whereNotNull('semester')->where('semester', '!=', 0)->distinct()->orderBy('semester')->get();
        $kurikulums = $this->getKurikulumsForUser();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_mk_cpmk', compact('mks', 'semesters', 'kurikulums'));
    }

    public function storeMKCPMK(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('cpmk_mk', 'bobot')) {
            \Illuminate\Support\Facades\Schema::table('cpmk_mk', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->float('bobot')->nullable()->default(0);
            });
        }

        $validated = $request->validate([
            'mk_kode' => 'required|exists:mks,kode',
            'cpmk_ids' => 'required|array|min:1',
            'cpmk_ids.*' => 'exists:cpmks,id',
            'bobot' => 'nullable|array',
        ]);

        $mk = MK::findOrFail($validated['mk_kode']);

        $totalBobot = 0;
        $hasAnyBobotInput = false;
        $syncData = [];
        foreach ($validated['cpmk_ids'] as $cpmkId) {
            $hasInput = isset($validated['bobot'][$cpmkId]) && $validated['bobot'][$cpmkId] !== null && $validated['bobot'][$cpmkId] !== '';
            $bVal = $hasInput ? (float)$validated['bobot'][$cpmkId] : 0;
            if ($hasInput && $bVal > 0) {
                $hasAnyBobotInput = true;
            }
            $totalBobot += $bVal;
            $syncData[$cpmkId] = ['bobot' => $bVal];
        }

        // If user filled in at least 1 bobot, the total MUST equal 100%
        if ($hasAnyBobotInput && abs($totalBobot - 100) > 0.01) {
            return redirect()->back()->with('error', 'Jika mengisi pembobotan, total bobot kontribusi CPMK harus bernilai tepat 100%. Total saat ini: ' . (float)$totalBobot . '%');
        }

        $mk->cpmks()->syncWithoutDetaching($syncData);

        return redirect()->back()->with('success', 'Pemetaan MK ke CPMK beserta bobot berhasil disimpan.');
    }

    public function updateMKCPMK(Request $request, $mk_kode)
    {
        $validated = $request->validate([
            'cpmk_ids' => 'nullable|array',
            'cpmk_ids.*' => 'exists:cpmks,id',
        ]);

        $mk = MK::findOrFail($mk_kode);
        $syncData = [];
        $totalBobot = 0;
        $hasAnyBobotInput = false;

        $bobotInputs = $request->input('bobot', []);

        if (!empty($validated['cpmk_ids'])) {
            foreach ($validated['cpmk_ids'] as $cpmkId) {
                $hasInput = isset($bobotInputs[$cpmkId]) && $bobotInputs[$cpmkId] !== null && $bobotInputs[$cpmkId] !== '';
                $bVal = $hasInput ? (float)$bobotInputs[$cpmkId] : 0;
                if ($hasInput && $bVal > 0) {
                    $hasAnyBobotInput = true;
                }
                $totalBobot += $bVal;
                $syncData[$cpmkId] = ['bobot' => $bVal];
            }

            // If user filled in at least 1 bobot, total MUST equal 100%
            if ($hasAnyBobotInput && abs($totalBobot - 100) > 0.01) {
                return redirect()->back()->with('error', 'Jika mengisi pembobotan, total bobot kontribusi CPMK harus bernilai tepat 100%. Total saat ini: ' . (float)$totalBobot . '%');
            }
        }

        $mk->cpmks()->sync($syncData);

        return redirect()->back()->with('success', 'Pemetaan MK ke CPMK berhasil diperbarui.');
    }

    public function updateSingleMKCPMK(Request $request, $mk_kode, $cpmk_id)
    {
        $validated = $request->validate([
            'cpmk_id' => 'required|exists:cpmks,id',
            'bobot' => 'nullable|numeric|min:0',
        ]);

        $mk = MK::findOrFail($mk_kode);
        $newCpmkId = (int)$validated['cpmk_id'];
        $bVal = isset($validated['bobot']) && $validated['bobot'] !== null && $validated['bobot'] !== '' ? (float)$validated['bobot'] : 0;

        if ($newCpmkId != (int)$cpmk_id) {
            $mk->cpmks()->detach($cpmk_id);
        }

        $mk->cpmks()->syncWithoutDetaching([
            $newCpmkId => ['bobot' => $bVal]
        ]);

        return redirect()->back()->with('success', 'Pemetaan & Bobot CPMK berhasil diperbarui.');
    }

    public function destroyMKCPMK($mk_kode, $cpmk_id)
    {
        $mk = MK::findOrFail($mk_kode);
        $mk->cpmks()->detach($cpmk_id);

        return redirect()->back()->with('success', 'CPMK berhasil dihapus dari pemetaan MK.');
    }

    public function storeCPLCPMKMK(Request $request)
    {

        $validated = $request->validate([
            'mk_kode' => 'required|exists:mks,kode',
            'cpmk_ids' => 'required|array',
            'cpmk_ids.*' => 'exists:cpmks,id',
        ]);

        $mk = MK::findOrFail($validated['mk_kode']);

        $mk->cpmks()->syncWithoutDetaching($validated['cpmk_ids']);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan.');
    }

    public function addCPMKMKSUBCPMK()
    {
        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->select('mks.*')->get();
        return view('penjamin-mutu.cpl-cpmk.add_cpmk_mk_subcpmk', compact('mks'));
    }

    public function getSUBCPMKByMK($mkId)
    {
        $mk = MK::find($mkId);
        $idProdi = auth()->user()->id_prodiUser;

        // Ambil seluruh Sub CPMK milik Program Studi agar Sub CPMK yang baru dibuat selalu muncul di opsi pilihan
        $subCpmks = SubCpmk::where('id_prodi', $idProdi)->get();

        if ($subCpmks->isEmpty() && $mk) {
            $cpmkIds = $mk->cpmks->pluck('id');
            $subCpmks = SubCpmk::whereIn('cpmk_id', $cpmkIds)->get();
        }

        return response()->json(['subcpmks' => $subCpmks]);
    }

    public function storeCPMKMKSUBCPMK(Request $request)
    {
        try {
            $validated = $request->validate([
                'mk_kode' => 'required|exists:mks,kode',
                'sub_cpmk_ids' => 'required|array',
                'sub_cpmk_ids.*' => 'exists:sub_cpmk,id',
            ]);

            $mk = MK::findOrFail($validated['mk_kode']);
            $mk->sub_cpmk()->syncWithoutDetaching($validated['sub_cpmk_ids']);

            $routePrefix = [
                'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
                'Kepala Program Studi' => 'kepala-program-studi.',
                'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.',
                'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.',
                'Dosen' => 'dosen.',
            ];
            $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
            $prefix = $routePrefix[$userOtoritas] ?? 'penjamin-mutu.program-studi.';

            return redirect()->route($prefix . 'cpl-cpmk.mk-cpmk-subcpmk')->with('success', 'Pemetaan MK - Sub CPMK berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('failed', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

public function addSubCpmk()
{
    $kurikulums = Kurikulum::where('id_prodi',auth()->user()->id_prodiUser)->get();

    return view('penjamin-mutu.bk.addSubCpmk', compact('kurikulums'));
}

public function getCpmkByKurikulum($id_kurikulum)
{
    $cpmks = Cpmk::whereHas('cpl', function ($query) use ($id_kurikulum) {
        $query->where('id_kurikulum', $id_kurikulum);
        $query->where('id_prodi', auth()->user()->id_prodiUser);
    })->with('cpl')->get();

    return response()->json([
        'cpmks' => $cpmks,
        'kurikulum_id' => $id_kurikulum,
        'prodi_id' => auth()->user()->id_prodiUser
    ]);
}

public function getCplByKurikulum($id_kurikulum)
{
    $cpls = CPL::where('id_kurikulum', $id_kurikulum)
        ->where('id_prodi', auth()->user()->id_prodiUser)
        ->orderBy('kode', 'asc')
        ->get();

    return response()->json([
        'cpls' => $cpls
    ]);
}

public function getCpmkByCpl($cpl_id)
{
    $cpmks = CPMK::where('cpl_id', $cpl_id)
        ->where('id_prodi', auth()->user()->id_prodiUser)
        ->orderBy('kode', 'asc')
        ->get();

    return response()->json([
        'cpmks' => $cpmks
    ]);
}

public function storeSubCpmk(Request $request)
{
    // Validasi
    // $validator = Validator::make($request->all(), [
    //     'kurikulum_id' => 'required',
    //     'cpmk_id' => 'required|string',
    //     'uraian' => 'required|string',
    //     'cpmk_kode'=> ' required',
    // ]);

    // if ($validator->fails()) {
    //     return redirect()->back()->with('failed', 'Data tidak dapat di simpan.'.$validator->errors());
    // }
    // try {
    //     $data = $request->all();
        
    //     $jumlahSubCpmk = SubCpmk::where('cpmk_id',$request->cpmk_id)->count();
    //     $kodeSubCpmkforInput = 'Sub-'.$request->cpmk_kode.$jumlahSubCpmk+1;

    //     $data['kode'] = $kodeSubCpmkforInput;

    //     SubCpmk::create($data);
    //     return redirect()->back()->with('success', 'Data berhasil disimpan.');
    // } catch (\Exception $e) {
    //     return redirect()->back()->with('failed', 'Data tidak dapat di simpan.');
    // }
    $validated = $request->validate([
        'cpmk_id' => 'required|integer|exists:cpmks,id',
        'uraian' => 'required|string',
        // kurikulum_id dan cpmk_kode tidak perlu divalidasi karena tidak disimpan langsung
    ]);

    try {
        $id_prodi = auth()->user()->id_prodiUser;
        $parentCpmk = CPMK::findOrFail($validated['cpmk_id']);
        $subCpmkCount = SubCpmk::where('cpmk_id', $validated['cpmk_id'])->count();
        $newKode = 'Sub-' . $parentCpmk->kode . ($subCpmkCount + 1);

        // 2. Siapkan data yang bersih untuk disimpan
        $dataToCreate = [
            'kode' => $newKode,
            'uraian' => $validated['uraian'],
            'cpmk_id' => $validated['cpmk_id'],
            'id_prodi' => $id_prodi, // Menyertakan ID Prodi
        ];

        SubCpmk::create($dataToCreate);
        return redirect()->back()->with('success', 'Data Sub CPMK berhasil disimpan.');

    } catch (\Exception $e) {
        Log::error('Error saat menyimpan Sub CPMK baru: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('failed', 'Data tidak dapat disimpan karena terjadi kesalahan.');
    }
}

public function indexKelolaSubCpmk(Request $request)
{
    $kurikulums = $this->getKurikulumsForUser();

    // Ambil daftar CPL milik prodi untuk filter tab CPL
    $cplQuery = CPL::query();
    if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
        $cplQuery->where('id_prodi', auth()->user()->id_prodiUser);
    }
    if ($request->filled('kurikulum_id')) {
        $cplQuery->where('id_kurikulum', $request->kurikulum_id);
    }
    $cpls = $cplQuery->orderBy('kode', 'asc')->get();

    $query = SubCpmk::with(['cpmk.cpl', 'mks'])
        ->join('cpmks', 'sub_cpmk.cpmk_id', '=', 'cpmks.id')
        ->join('cpls', 'cpmks.cpl_id', '=', 'cpls.id');

    if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
        $query->where('sub_cpmk.id_prodi', auth()->user()->id_prodiUser);
    }

    if ($request->filled('kurikulum_id')) {
        $query->where('cpls.id_kurikulum', $request->kurikulum_id);
    }

    if ($request->filled('cpl_id')) {
        $query->where('cpls.id', $request->cpl_id);
    }

    $subCpmks = $query->select('sub_cpmk.*')
        ->orderBy('cpls.kode', 'asc')
        ->orderBy('cpmks.kode', 'asc')
        ->orderBy('sub_cpmk.kode', 'asc')
        ->paginate(15)
        ->withQueryString();

    return view('penjamin-mutu.cpl-cpmk.kelola_subcpmk', compact('subCpmks', 'kurikulums', 'cpls'));
}

public function updateSubCpmk(Request $request, $id)
{
    $validated = $request->validate([
        'kode' => 'nullable|string|max:255',
        'uraian' => 'required|string',
    ]);

    try {
        $subCpmk = SubCpmk::findOrFail($id);
        $data = ['uraian' => $validated['uraian']];
        if (!empty($validated['kode'])) {
            $data['kode'] = $validated['kode'];
        }
        $subCpmk->update($data);

        return redirect()->back()->with('success', 'Data Sub CPMK berhasil diperbarui.');
    } catch (\Exception $e) {
        Log::error('Error update Sub CPMK: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('failed', 'Gagal memperbarui Sub CPMK.');
    }
}

public function destroySubCpmk($id)
{
    try {
        $subCpmk = SubCpmk::findOrFail($id);
        $kodeSub = $subCpmk->kode;
        if (method_exists($subCpmk, 'mks')) {
            $subCpmk->mks()->detach();
        }
        $subCpmk->delete();

        return redirect()->back()->with('success', "Sub CPMK $kodeSub berhasil dihapus.");
    } catch (\Exception $e) {
        Log::error('Error delete Sub CPMK: ' . $e->getMessage());
        return redirect()->back()->with('failed', 'Gagal menghapus Sub CPMK.');
    }
}

}

