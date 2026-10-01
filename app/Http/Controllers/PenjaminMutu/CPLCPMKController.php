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

        $userProdiId = auth()->user()->id_prodiUser;
        $mks = MK::query()
            ->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            })
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
        
        $userProdiId = auth()->user()->id_prodiUser;
        $mks = MK::query()
            ->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            })
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

        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $queryMks = MK::query()->with(['cpmks.cpl', 'cpl', 'kurikulum']);

        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', $user->id_universitasUser);
            $univId = $user->id_universitasUser;
            $queryMks->where(function($q) use ($univId) {
                $q->where('mks.id_universitas', $univId)
                  ->orWhereHas('prodi.fakultas', function($f) use ($univId) {
                      $f->where('id_universitas', $univId);
                  })->orWhereExists(function($sub) use ($univId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                          ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('fakultas.id_universitas', $univId);
                  });
            });
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', $user->id_fakultasUser);
            $fakId = $user->id_fakultasUser;
            $queryMks->where(function($q) use ($fakId) {
                $q->whereHas('prodi', function($p) use ($fakId) {
                    $p->where('id_fakultas', $fakId);
                })->orWhereExists(function($sub) use ($fakId) {
                    $sub->select(DB::raw(1))
                        ->from('mk_kurikulum')
                        ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                        ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                        ->where('prodi.id_fakultas', $fakId);
                });
            });
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', $userProdiId);
            $queryMks->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            });
        }

        if ($request->filled('kurikulum_id')) {
            $kurId = $request->kurikulum_id;
            $queryCpl->where('cpls.id_kurikulum', $kurId);
            $queryMks->where(function($q) use ($kurId) {
                $q->where('mks.id_kurikulum', $kurId)
                  ->orWhere('mks.kurikulum', $kurId)
                  ->orWhereExists(function($sub) use ($kurId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_kurikulum', $kurId);
                  });
            });
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
        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $queryMks = MK::query();
        if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMks->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            });
        }
        if ($kurikulumId) {
            $queryMks->where(function($q) use ($kurikulumId) {
                $q->where('mks.id_kurikulum', $kurikulumId)
                  ->orWhere('mks.kurikulum', $kurikulumId)
                  ->orWhereExists(function($sub) use ($kurikulumId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_kurikulum', $kurikulumId);
                  });
            });
        }
        $mks = $queryMks->get();

        DB::transaction(function () use ($mks, $matrix, $userProdiId) {
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
                    $cplSyncData[$cplId] = ['id_prodi' => $mk->id_prodi ?: $userProdiId];
                }
                $mk->cpl()->sync($cplSyncData);
            }
        });

        return redirect()->back()->with('success', 'Matriks Pemetaan CPL - MK - CPMK berhasil diperbarui.');
    }

    public function indexMKCPMKSubCPMK(Request $request)
    {
        $kurikulums = $this->getKurikulumsForUser();
        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $query = MK::with(['cpmks.subCpmks.mks', 'cpmks.cpl', 'cpl', 'sub_cpmk.cpmk.cpl']);

        // Filtering berdasarkan otoritas pengguna
        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $univId = $user->id_universitasUser;
            $query->where(function($q) use ($univId) {
                $q->where('mks.id_universitas', $univId)
                  ->orWhereHas('prodi.fakultas', function($f) use ($univId) {
                      $f->where('id_universitas', $univId);
                  })->orWhereExists(function($sub) use ($univId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                          ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('fakultas.id_universitas', $univId);
                  });
            });
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $fakId = $user->id_fakultasUser;
            $query->where(function($q) use ($fakId) {
                $q->whereHas('prodi', function($p) use ($fakId) {
                    $p->where('id_fakultas', $fakId);
                })->orWhereExists(function($sub) use ($fakId) {
                    $sub->select(DB::raw(1))
                        ->from('mk_kurikulum')
                        ->join('prodi', 'mk_kurikulum.id_prodi', '=', 'prodi.id')
                        ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                        ->where('prodi.id_fakultas', $fakId);
                });
            });
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            });
        }

        if ($request->filled('kurikulum_id')) {
            $kurId = $request->kurikulum_id;
            $query->where(function($q) use ($kurId) {
                $q->where('mks.id_kurikulum', $kurId)
                  ->orWhere('mks.kurikulum', $kurId)
                  ->orWhereExists(function($sub) use ($kurId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_kurikulum', $kurId);
                  });
            });
        }

        $mks = $query->get();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_mk_cpmk_subcpmk', compact('mks','kurikulums'));
    }

    public function updateMatrixMKCPMKSubCPMK(Request $request)
    {
        $matrix = $request->input('matrix', []); // matrix[mk_kode] = [sub_cpmk_id1, sub_cpmk_id2, ...]
        $uraianInputs = $request->input('uraian', []); // uraian[sub_cpmk_id] = "new description"
        $kodeInputs = $request->input('kode', []); // kode[sub_cpmk_id] = "new code"
        $kurikulumId = $request->input('kurikulum_id');
        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $queryMks = MK::query();
        if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMks->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            });
        }
        if ($kurikulumId) {
            $queryMks->where(function($q) use ($kurikulumId) {
                $q->where('mks.id_kurikulum', $kurikulumId)
                  ->orWhere('mks.kurikulum', $kurikulumId)
                  ->orWhereExists(function($sub) use ($kurikulumId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_kurikulum', $kurikulumId);
                  });
            });
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
        $userProdiId = auth()->user()->id_prodiUser;
        $mks = MK::query()
            ->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            })
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

        $userProdiId = auth()->user()->id_prodiUser;
        $query = MK::with(['cpmks.cpl'])
            ->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            })
            ->orderBy('mks.nama', 'asc');

        if ($request->filled('kurikulum_id')) {
            $kurId = $request->kurikulum_id;
            $query->where(function($q) use ($kurId) {
                $q->where('mks.id_kurikulum', $kurId)
                  ->orWhere('mks.kurikulum', $kurId)
                  ->orWhereExists(function($sub) use ($kurId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_kurikulum', $kurId);
                  });
            });
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
        $userProdiId = auth()->user()->id_prodiUser;
        $mks = MK::query()
            ->where(function($q) use ($userProdiId) {
                $q->where('mks.id_prodi', $userProdiId)
                  ->orWhereExists(function($sub) use ($userProdiId) {
                      $sub->select(DB::raw(1))
                          ->from('mk_kurikulum')
                          ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                          ->where('mk_kurikulum.id_prodi', $userProdiId);
                  });
            })
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
    $cpls = $cplQuery->withCount('subCpmks')->orderBy('kode', 'asc')->get();

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

    public function downloadTemplateSubCpmk()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Sub CPMK');

        // Header
        $headers = [
            'A1' => 'Tahun Kurikulum',
            'B1' => 'Kode MK (Opsional)',
            'C1' => 'Kode CPMK',
            'D1' => 'Uraian Sub CPMK',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style Header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Sample Data Row 1
        $sheet->setCellValue('A2', '2024');
        $sheet->setCellValue('B2', 'INF101');
        $sheet->setCellValue('C2', 'CPMK01');
        $sheet->setCellValue('D2', 'Mampu menjelaskan konsep dasar pemrograman berorientasi objek.');

        // Sample Data Row 2
        $sheet->setCellValue('A3', '2024');
        $sheet->setCellValue('B3', 'INF101');
        $sheet->setCellValue('C3', 'CPMK01');
        $sheet->setCellValue('D3', 'Mampu menerapkan prinsip enkapsulasi dan pewarisan.');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Template_Import_Sub_CPMK.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function importExcelSubCpmk(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $user = auth()->user();
        $id_prodi_user = $user->id_prodiUser ?? ($user->prodi ? $user->prodi->id : null);

        $file = $request->file('excel_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('failed', 'File Excel tidak valid.');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $dataRows = $worksheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        if (count($dataRows) <= 1) {
            return redirect()->back()->with('failed', 'File Excel kosong atau hanya berisi header.');
        }

        $createdCount = 0;
        $updatedCount = 0;
        $failedCount = 0;

        DB::beginTransaction();
        try {
            for ($i = 1; $i < count($dataRows); $i++) {
                $row = $dataRows[$i];
                $tahunKurikulum = trim($row[0] ?? '');
                $kodeMk = trim($row[1] ?? '');
                $kodeCpmk = trim($row[2] ?? '');
                $uraian = trim($row[3] ?? '');

                // Minimal butuh Kode CPMK dan Uraian
                if (empty($kodeCpmk) || empty($uraian)) {
                    continue;
                }

                // Cari Kurikulum jika diisi
                $kurikulumId = null;
                if (!empty($tahunKurikulum)) {
                    $kurQuery = Kurikulum::where('tahun', $tahunKurikulum);
                    if ($id_prodi_user) {
                        $kurQuery->where('id_prodi', $id_prodi_user);
                    }
                    $kurikulum = $kurQuery->first();
                    if ($kurikulum) {
                        $kurikulumId = $kurikulum->id;
                    }
                }

                // Cari CPMK berdasarkan kode
                $cpmkQuery = CPMK::query();
                if ($id_prodi_user) {
                    $cpmkQuery->where('id_prodi', $id_prodi_user);
                }
                $cpmkQuery->where('kode', $kodeCpmk);
                if ($kurikulumId) {
                    $cpmkQuery->whereHas('cpl', function ($q) use ($kurikulumId) {
                        $q->where('id_kurikulum', $kurikulumId);
                    });
                }
                $cpmk = $cpmkQuery->first();

                // Fallback pencarian CPMK jika kurikulum mismatch
                if (!$cpmk) {
                    $cpmk = CPMK::when($id_prodi_user, function ($q) use ($id_prodi_user) {
                        $q->where('id_prodi', $id_prodi_user);
                    })->where('kode', $kodeCpmk)->first();
                }

                if (!$cpmk) {
                    $failedCount++;
                    continue;
                }

                // Cari apakah Sub CPMK dengan Uraian persis ini sudah ada untuk CPMK tersebut
                $existingSub = SubCpmk::where('cpmk_id', $cpmk->id)
                    ->where('uraian', $uraian)
                    ->first();

                if ($existingSub) {
                    $existingSub->update([
                        'uraian' => $uraian,
                        'id_prodi' => $id_prodi_user ?? $existingSub->id_prodi,
                    ]);
                    $subCpmkObj = $existingSub;
                    $updatedCount++;
                } else {
                    // Auto-generate kode unik Sub CPMK
                    $subCount = SubCpmk::where('cpmk_id', $cpmk->id)->count() + 1;
                    do {
                        $genKode = 'Sub-' . $cpmk->kode . $subCount;
                        $existsKode = SubCpmk::where('cpmk_id', $cpmk->id)->where('kode', $genKode)->exists();
                        if ($existsKode) {
                            $subCount++;
                        }
                    } while ($existsKode);

                    $subCpmkObj = SubCpmk::create([
                        'kode' => $genKode,
                        'uraian' => $uraian,
                        'cpmk_id' => $cpmk->id,
                        'id_prodi' => $id_prodi_user,
                    ]);
                    $createdCount++;
                }

                // Hubungkan ke MK jika kode_mk diisi
                if (!empty($kodeMk) && $subCpmkObj) {
                    $mkQuery = MK::where('kode', $kodeMk);
                    if ($id_prodi_user) {
                        $mkQuery->where('id_prodi', $id_prodi_user);
                    }
                    $mk = $mkQuery->first();
                    if ($mk) {
                        if (!$subCpmkObj->mks()->where('mks.kode', $mk->kode)->exists()) {
                            $subCpmkObj->mks()->attach($mk->kode);
                        }
                    }
                }
            }

            DB::commit();

            $msg = "Proses import selesai. $createdCount Sub CPMK baru ditambahkan, $updatedCount diperbarui.";
            if ($failedCount > 0) {
                $msg .= " ($failedCount baris dilewati/CPMK tidak ditemukan).";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error import Sub CPMK: ' . $e->getMessage());
            return redirect()->back()->with('failed', 'Gagal mengimpor Sub CPMK: ' . $e->getMessage());
        }
    }
}

