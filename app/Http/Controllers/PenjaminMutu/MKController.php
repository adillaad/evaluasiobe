<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\Fakultas;
use App\Models\Kurikulum;
use App\Models\MK;
use App\Models\Prodi;
use App\Support\MkKodeUpdater;
use App\Traits\UniversityFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MKController extends Controller
{
    use UniversityFilterTrait;
    
    public function create()
    {
        $user = auth()->user();
        $isUniversityLevel = in_array($user->otoritas->otoritas, ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']);

        if ($isUniversityLevel) {
            $kurikulums = Kurikulum::whereHas('prodi.fakultas', function($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
            })->orderBy('tahun', 'desc')->get();
            $mks = MK::where(function($q) use ($user) {
                $q->whereHas('prodi.fakultas', function($sub) use ($user) {
                    $sub->where('id_universitas', $user->id_universitasUser);
                })->orWhereNull('id_prodi');
            })->get();
            $prodi = null;
        } else {
            $kurikulums = Kurikulum::where('id_prodi', $user->id_prodiUser)
                                ->orderBy('tahun', 'desc')
                                ->get();
            $mks = MK::where(function($q) use ($user) {
                $q->where('id_prodi', $user->id_prodiUser)->orWhereNull('id_prodi');
            })->get();
            $prodi = $user->prodi;
        }
        $mksUniv = MK::whereNull('id_prodi')->get();
        return view('penjamin-mutu.mk.add', compact('kurikulums', 'mks', 'prodi', 'mksUniv'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'alpha_num', 'min:9'],
            'nama' => ['required', 'string'],
            'nama_eng' => ['required', 'string'],
            'semester' => ['required'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string'],
            'id_kurikulum' => ['nullable'],
            'id_prodi' => ['nullable'],
            'deskripsi' => 'required',
            'batas_kelulusan_mhs' => ['required','numeric','min:0','max:100'], 
            'batas_kelulusan_mk'  => ['required','numeric','min:0','max:100'],
            'bobot_teori' => ['required', 'integer', 'digits:1'],
            'bobot_praktikum' => ['nullable', 'integer', 'digits:1'],
        ]);
        
        $prasyarat = $request->prasyarat ?? 'Tidak ada';
        $bobot_praktikum = $request->bobot_praktikum ?? 0;
        $semesterVal = is_array($request->semester) ? implode(',', $request->semester) : $request->semester;
        
        $user = auth()->user();
        $isUniversityLevel = in_array($user->otoritas->otoritas, ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']);
        $idProdi = $isUniversityLevel ? ($request->filled('id_prodi') ? $request->id_prodi : null) : $user->id_prodiUser;
        $idKurikulum = $request->filled('id_kurikulum') ? $request->id_kurikulum : null;

        try {
            $kode = strtoupper($request->kode);
            $existingMkUniv = MK::where('kode', $kode)->whereNull('id_prodi')->first();

            // Jika memilih MK Universitas yang sudah ada untuk di-assign ke Kurikulum Prodi
            if ($existingMkUniv && !is_null($idProdi)) {
                if ($idKurikulum) {
                    DB::table('mk_kurikulum')->updateOrInsert(
                        ['mk_kode' => $kode, 'id_kurikulum' => $idKurikulum],
                        ['id_prodi' => $idProdi, 'semester' => $semesterVal, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
                return redirect()->route($this->getRouteByAuthority())->with('success', 'MK Universitas berhasil ditambahkan ke Kurikulum Prodi!');
            }

            // Buat MK baru di tabel mks
            MK::create([
                'kode' => $kode,
                'nama' => $request->nama,
                'nama_eng' => $request->nama_eng,
                'semester' => $semesterVal,
                'rumpun' => $request->rumpun,
                'prasyarat' => $prasyarat,
                'id_kurikulum' => $idKurikulum,
                'id_prodi' => $idProdi,
                'deskripsi' => $request->deskripsi,
                'batas_kelulusan_mhs' => $request->batas_kelulusan_mhs,
                'batas_kelulusan_mk' => $request->batas_kelulusan_mk, 
                'bobot_teori' => $request->bobot_teori,
                'bobot_praktikum' => $bobot_praktikum,
            ]);

            // Hanya masukan ke mk_kurikulum jika ini MK Universitas murni (id_prodi NULL)
            if ($idKurikulum && is_null($idProdi)) {
                DB::table('mk_kurikulum')->updateOrInsert(
                    ['mk_kode' => $kode, 'id_kurikulum' => $idKurikulum],
                    ['id_prodi' => null, 'semester' => $semesterVal, 'updated_at' => now(), 'created_at' => now()]
                );
            }
            return redirect()->route($this->getRouteByAuthority())->with('success', 'MK successfully added!');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1] ?? 0;
            if ($errorCode == 1062)
                return redirect()->back()->withInput()->with('error', 'Kode mata kuliah ' . $request->kode . ' sudah ada');
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        }
    }

    public function edit($kode)
    {
        $user = auth()->user();
        $isUniversityLevel = in_array($user->otoritas->otoritas, ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']);

        if ($isUniversityLevel) {
            $mk = MK::where('kode', $kode)->firstOrFail();
            $kurikulums = Kurikulum::whereHas('prodi.fakultas', function($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
            })->orderBy('tahun', 'desc')->get();
            $prasyarats = MK::where('kode', '!=', $kode)->get();
        } else {
            $userProdiId = $user->id_prodiUser;
            $mk = MK::where('kode', $kode)
                    ->where(function($q) use ($userProdiId) {
                        $q->where('id_prodi', $userProdiId)
                          ->orWhere(function($sub) use ($userProdiId) {
                              $sub->whereNull('id_prodi')
                                  ->whereExists(function($mkKurQuery) use ($userProdiId) {
                                      $mkKurQuery->select(DB::raw(1))
                                                 ->from('mk_kurikulum')
                                                 ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                                                 ->where('mk_kurikulum.id_prodi', $userProdiId);
                                  });
                          });
                    })
                    ->firstOrFail();

            if (!$mk->id_kurikulum && $userProdiId) {
                $mkKurRecord = DB::table('mk_kurikulum')
                    ->where('mk_kode', $kode)
                    ->where('id_prodi', $userProdiId)
                    ->first();
                if ($mkKurRecord) {
                    $mk->id_kurikulum = $mkKurRecord->id_kurikulum;
                }
            }

            $kurikulums = Kurikulum::where('id_prodi', $userProdiId)
                                ->orderBy('tahun', 'desc')
                                ->get();

            $prasyarats = MK::where(function($q) use ($userProdiId) {
                                $q->where('id_prodi', $userProdiId)->orWhereNull('id_prodi');
                            })
                            ->where('kode', '!=', $kode)
                            ->get();
        }

        return view('penjamin-mutu.mk.edit', compact('mk', 'kurikulums', 'prasyarats'));
    }

    public function update(Request $request, $kode)
    {
        $request->validate([
            'id_kurikulum' => 'nullable',
            'id_prodi' => 'nullable',
            'kode' => ['required', 'alpha_num', 'min:9'],
            'nama' => ['required', 'string'],
            'nama_eng' => ['required', 'string'],
            'semester' => ['required'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string'],
            'deskripsi' => 'required',
            'batas_kelulusan_mhs' => ['required','numeric','min:0','max:100'],
            'batas_kelulusan_mk'  => ['required','numeric','min:0','max:100'],
            'bobot_teori' => ['required', 'integer', 'digits:1'],
            'bobot_praktikum' => ['nullable', 'integer', 'digits:1'],
        ]);

        $user = auth()->user();
        $isUniversityLevel = in_array($user->otoritas->otoritas, ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor']);

        if ($isUniversityLevel) {
            $mk = MK::where('kode', $kode)->firstOrFail();
            $isUniv = $request->input('mk_type_toggle') === 'univ';
            $idProdi = $isUniv ? null : ($request->filled('id_prodi') ? $request->id_prodi : null);
            $idKurikulum = $isUniv ? null : ($request->filled('id_kurikulum') ? $request->id_kurikulum : null);
        } else {
            $userProdiId = $user->id_prodiUser;
            $mk = MK::where('kode', $kode)
                ->where(function($q) use ($userProdiId) {
                    $q->where('id_prodi', $userProdiId)
                      ->orWhere(function($sub) use ($userProdiId) {
                          $sub->whereNull('id_prodi')
                              ->whereExists(function($mkKurQuery) use ($userProdiId) {
                                  $mkKurQuery->select(DB::raw(1))
                                             ->from('mk_kurikulum')
                                             ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                                             ->where('mk_kurikulum.id_prodi', $userProdiId);
                              });
                      });
                })
                ->firstOrFail();
            $idProdi = $mk->id_prodi; // Preserve NULL if it's MK Univ
            $idKurikulum = $request->filled('id_kurikulum') ? $request->id_kurikulum : $mk->id_kurikulum;
        }

        $newKode = strtoupper($request->kode);

        if ($newKode !== $mk->kode && MK::where('kode', $newKode)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Kode mata kuliah ' . $newKode . ' sudah ada.');
        }

        $prasyarat = $request->prasyarat ?? 'Tidak ada';
        $bobot_praktikum = $request->bobot_praktikum ?? 0;
        $semesterVal = is_array($request->semester) ? implode(',', $request->semester) : $request->semester;

        $attributes = [
            'nama' => $request->nama,
            'nama_eng' => $request->nama_eng,
            'semester' => $semesterVal,
            'rumpun' => $request->rumpun,
            'prasyarat' => $prasyarat,
            'id_kurikulum' => $idKurikulum,
            'id_prodi' => $idProdi,
            'deskripsi' => $request->deskripsi,
            'batas_kelulusan_mhs' => $request->batas_kelulusan_mhs,
            'batas_kelulusan_mk' => $request->batas_kelulusan_mk,
            'bobot_teori' => $request->bobot_teori,
            'bobot_praktikum' => $bobot_praktikum,
            'updated_at' => now(),
        ];

        try {
            MkKodeUpdater::rename($mk->kode, $newKode, $attributes);

            $targetProdiId = $user->id_prodiUser ?? $idProdi;
            if ($idKurikulum && $targetProdiId) {
                DB::table('mk_kurikulum')->updateOrInsert(
                    ['mk_kode' => $newKode, 'id_prodi' => $targetProdiId],
                    ['id_kurikulum' => $idKurikulum, 'semester' => $semesterVal, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            return redirect()->route($this->getRouteByAuthority())->with('success', 'MK berhasil diubah!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput()->with('error', MkKodeUpdater::databaseErrorMessage($e));
        }
    }

    public function delete($kode)
    {
        $user = auth()->user();
        $query = MK::where('kode', $kode);

        if (in_array($user->otoritas->otoritas, ['Admin Universitas', 'Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $query->where(function($q) use ($user) {
                $q->whereHas('prodi.fakultas', function($f) use ($user) {
                    $f->where('id_universitas', $user->id_universitasUser);
                })->orWhereNull('id_prodi');
            });
        } else {
            $query->where('id_prodi', $user->id_prodiUser);
        }

        $deleted = $query->delete();

        if ($deleted) {
            return redirect()->route($this->getRouteByAuthority())->with('success', 'MK successfully deleted!');
        }

        return redirect()->route($this->getRouteByAuthority())->with('error', 'MK not found or unauthorized!');
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.mk.susunan-mk',
            'Kepala Program Studi' => 'kepala-program-studi.mk.susunan-mk',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.mk.susunan-mk',
            'Admin Universitas' => 'admin-universitas.list-mk',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'penjamin-mutu.universitas.mk.susunan-mk';
    }
    
    public function susunanMK(Request $request)
    {
        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $query = MK::with(['kurikulum', 'prodi.fakultas.universitas'])
            ->leftJoin('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->leftJoin('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
            ->orderBy('mks.semester', 'asc')
            ->select('mks.*');

        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where(function($q) use ($user) {
                $q->where('fakultas.id_universitas', $user->id_universitasUser)
                  ->orWhereNull('mks.id_prodi');
            });
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where(function($q) use ($user) {
                $q->where('fakultas.id', $user->id_fakultasUser)
                  ->orWhereNull('mks.id_prodi');
            });
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where(function($q) use ($userProdiId) {
                $q->where('prodi.id', $userProdiId)
                  ->orWhere(function($sub) use ($userProdiId) {
                      $sub->whereNull('mks.id_prodi')
                          ->whereExists(function($mkKurQuery) use ($userProdiId) {
                              $mkKurQuery->select(DB::raw(1))
                                         ->from('mk_kurikulum')
                                         ->whereColumn('mk_kurikulum.mk_kode', 'mks.kode')
                                         ->where('mk_kurikulum.id_prodi', $userProdiId);
                          });
                  });
            });
        }
        $query = $this->getFilteredQuery($query, $request);
        $mks = $query->get();

        foreach ($mks as $mkItem) {
            if (!$mkItem->kurikulum && $userProdiId) {
                $mkKurRec = DB::table('mk_kurikulum')
                    ->where('mk_kode', $mkItem->kode)
                    ->where('id_prodi', $userProdiId)
                    ->first();
                if ($mkKurRec) {
                    $kurModel = Kurikulum::find($mkKurRec->id_kurikulum);
                    if ($kurModel) {
                        $mkItem->setRelation('kurikulum', $kurModel);
                        $mkItem->id_kurikulum = $kurModel->id;
                    }
                }
            }
        }

        $maxSemester = (int)(MK::max('semester') ?: 8);

        $filterData = $this->getFilterData($request);
        unset($filterData['mks']);

        return view('penjamin-mutu.mk.susunan_mk', array_merge(
            $filterData,
            [
                'mks' => $mks,
                'maxSemester' => $maxSemester
            ]
        ));
    }

    public function organisasiMK()
    {
        $query = DB::table('mks')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        // Filter berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $querySemester = DB::table('mks')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        // Filter berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $querySemester->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $querySemester->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $querySemester->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $semesters = $querySemester->select(
            'mks.semester',
            DB::raw('SUM(mks.bobot_teori + mks.bobot_praktikum) as total_sks'),
            DB::raw('COUNT(mks.kode) as jumlah_mk'),
            DB::raw('GROUP_CONCAT(CASE WHEN LOWER(mks.rumpun) = "wajib" THEN mks.kode END) as kode_wajib'),
            DB::raw('GROUP_CONCAT(CASE WHEN LOWER(mks.rumpun) = "peminatan" THEN mks.kode END) as kode_peminatan'),
            DB::raw('GROUP_CONCAT(CASE WHEN LOWER(mks.rumpun) IN ("wajib_kurikulum", "mkwk") THEN mks.kode END) as kode_wajib_kurikulum')
        )->groupBy('mks.semester')->orderBy('mks.semester')->get();

        $mks = $query->select('mks.kode', 'mks.nama', 'mks.rumpun')
            ->orderBy('mks.rumpun')
            ->get();
            
        $totals = $query->select(
            DB::raw('SUM(mks.bobot_teori + mks.bobot_praktikum) as total_sks'),
            DB::raw('COUNT(mks.kode) as jumlah_mk')
        )->first();

        return view('penjamin-mutu.mk.organisasi_mk', compact('semesters', 'totals', 'mks'));
    }

    public function pemenuhanCPL(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? '';

        $query = CPL::with(['mk' => function ($q) {
            $q->orderBy('semester');
        }])->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.*');

        if ($userOtoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryMks->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($userOtoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', $user->id_fakultasUser);
            $queryMks->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', $user->id_prodiUser);
            $queryMks->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $query->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryMks->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $cpls = $query->select('cpls.*')->with('kurikulum')->get();
        $allMks = $queryMks->with('kurikulum')->orderBy('semester')->orderBy('kode')->get();

        $maxSemester = 8;
        $mksBySemester = collect();
        foreach (range(1, $maxSemester) as $s) {
            $mksBySemester->put($s, $allMks->filter(function($mk) use ($s) {
                $sems = array_map('trim', explode(',', (string)$mk->semester));
                return in_array((string)$s, $sems);
            }));
        }

        $queryKur = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('kurikulums.*', 'prodi.nama as nama_prodi')
            ->orderBy('kurikulums.tahun', 'desc');

        if ($userOtoritas === 'Penjamin Mutu Universitas') {
            $queryKur->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($userOtoritas === 'Penjamin Mutu Fakultas') {
            $queryKur->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $queryKur->where('prodi.id', $user->id_prodiUser);
        }
        $kurikulums = $queryKur->get();

        return view('penjamin-mutu.mk.pemenuhan_cpl', compact('cpls', 'maxSemester', 'allMks', 'mksBySemester', 'kurikulums'));
    }

    public function updateMatrixPemenuhanCPL(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? '';

        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.*');

        if ($userOtoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($userOtoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
        }

        $cpls = $queryCpl->get();
        $matrix = $request->input('matrix', []); // Key: cpl_id, Value: array of mk_kodes

        DB::transaction(function () use ($cpls, $matrix) {
            foreach ($cpls as $cpl) {
                $selectedMkKodes = isset($matrix[$cpl->id]) ? (array) $matrix[$cpl->id] : [];
                $syncData = [];
                foreach ($selectedMkKodes as $mkKode) {
                    $syncData[$mkKode] = ['id_prodi' => $cpl->id_prodi];
                }
                $cpl->mk()->sync($syncData);
            }
        });

        return redirect()->back()->with('success', 'Pemenuhan CPL berhasil diperbarui.');
    }
}
