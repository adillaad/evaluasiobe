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

        $filterKurId = $request->input('kurikulum_id') ?: $request->input('id_kurikulum');

        foreach ($mks as $mkItem) {
            if (!$mkItem->kurikulum && $userProdiId) {
                $mkKurQuery = DB::table('mk_kurikulum')
                    ->where('mk_kode', $mkItem->kode);

                if ($filterKurId) {
                    $mkKurQuery->where('id_kurikulum', $filterKurId);
                } else {
                    $mkKurQuery->where('id_prodi', $userProdiId);
                }

                $mkKurRec = $mkKurQuery->first();
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

    public function organisasiMK(Request $request)
    {
        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $query = MK::with(['kurikulum', 'prodi.fakultas.universitas'])
            ->leftJoin('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('mk_kurikulum', function($join) use ($userProdiId) {
                $join->on('mks.kode', '=', 'mk_kurikulum.mk_kode');
                if ($userProdiId) {
                    $join->where('mk_kurikulum.id_prodi', '=', $userProdiId);
                }
            });

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

        $mksList = $query->select(
            'mks.kode',
            'mks.nama',
            'mks.rumpun',
            'mks.bobot_teori',
            'mks.bobot_praktikum',
            DB::raw('COALESCE(mk_kurikulum.semester, mks.semester) as semester')
        )->get();

        $grouped = $mksList->groupBy('semester')->sortKeys();

        $semesters = collect();
        foreach ($grouped as $semNum => $items) {
            $wajibKodes = $items->filter(fn($i) => strtolower($i->rumpun) === 'wajib')->pluck('kode')->implode(', ');
            $peminatanKodes = $items->filter(fn($i) => strtolower($i->rumpun) === 'peminatan')->pluck('kode')->implode(', ');
            $wajibKurKodes = $items->filter(fn($i) => in_array(strtolower($i->rumpun), ['wajib_kurikulum', 'mkwk', 'wajib kurikulum']))->pluck('kode')->implode(', ');

            $semesters->push((object)[
                'semester' => $semNum,
                'total_sks' => $items->sum(fn($i) => (int)$i->bobot_teori + (int)$i->bobot_praktikum),
                'jumlah_mk' => $items->count(),
                'kode_wajib' => $wajibKodes,
                'kode_peminatan' => $peminatanKodes,
                'kode_wajib_kurikulum' => $wajibKurKodes,
            ]);
        }

        $totals = (object)[
            'total_sks' => $mksList->sum(fn($i) => (int)$i->bobot_teori + (int)$i->bobot_praktikum),
            'jumlah_mk' => $mksList->count(),
        ];

        $mks = $mksList;

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

    // Download Template Excel Mata Kuliah
    public function downloadTemplateMK()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import MK');

        // Header
        $headers = [
            'A1' => 'Tahun Kurikulum',
            'B1' => 'Kode MK',
            'C1' => 'Nama MK',
            'D1' => 'Nama MK (English)',
            'E1' => 'Semester',
            'F1' => 'Rumpun',
            'G1' => 'SKS Teori',
            'H1' => 'SKS Praktikum',
            'I1' => 'Batas Kelulusan Mhs (%)',
            'J1' => 'Batas Kelulusan MK (%)',
            'K1' => 'Prasyarat',
            'L1' => 'Deskripsi',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style Header
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Sample Data Row 1
        $sheet->setCellValue('A2', '2025');
        $sheet->setCellValue('B2', 'INF101001');
        $sheet->setCellValue('C2', 'Pemrograman Web');
        $sheet->setCellValue('D2', 'Web Programming');
        $sheet->setCellValue('E2', '1');
        $sheet->setCellValue('F2', 'Wajib');
        $sheet->setCellValue('G2', '2');
        $sheet->setCellValue('H2', '1');
        $sheet->setCellValue('I2', '60');
        $sheet->setCellValue('J2', '70');
        $sheet->setCellValue('K2', 'Tidak ada');
        $sheet->setCellValue('L2', 'Mata kuliah ini membahas dasar-dasar pengembangan web modern.');

        // Sample Data Row 2
        $sheet->setCellValue('A3', '2025');
        $sheet->setCellValue('B3', 'INF101002');
        $sheet->setCellValue('C3', 'Basis Data');
        $sheet->setCellValue('D3', 'Database Systems');
        $sheet->setCellValue('E3', '2');
        $sheet->setCellValue('F3', 'Wajib');
        $sheet->setCellValue('G3', '3');
        $sheet->setCellValue('H3', '0');
        $sheet->setCellValue('I3', '60');
        $sheet->setCellValue('J3', '70');
        $sheet->setCellValue('K3', 'INF101001');
        $sheet->setCellValue('L3', 'Mata kuliah ini membahas pemodelan dan pengelolaan basis data relasional.');

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Template_Import_Mata_Kuliah.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Import Excel Mata Kuliah
    public function importExcelMK(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'id_kurikulum' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $id_prodi_user = $user->id_prodiUser;

        $file = $request->file('excel_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File Excel tidak valid.');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $dataRows = $worksheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        if (count($dataRows) <= 1) {
            return redirect()->back()->with('error', 'File Excel kosong atau hanya berisi header.');
        }

        // Drop header (baris 1)
        unset($dataRows[0]);

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($dataRows as $row) {
                $rawTahunKur = trim((string)($row[0] ?? ''));
                $rawKode = trim((string)($row[1] ?? ''));
                $nama = trim((string)($row[2] ?? ''));
                $nama_eng = trim((string)($row[3] ?? ''));
                $semester = trim((string)($row[4] ?? '1'));
                $rumpun = trim((string)($row[5] ?? 'Wajib'));
                $bobot_teori = (int)($row[6] ?? 2);
                $bobot_praktikum = (int)($row[7] ?? 0);
                $batas_mhs = is_numeric($row[8] ?? null) ? (float)$row[8] : 60;
                $batas_mk = is_numeric($row[9] ?? null) ? (float)$row[9] : 70;
                $prasyarat = trim((string)($row[10] ?? 'Tidak ada'));
                $deskripsi = trim((string)($row[11] ?? 'Deskripsi mata kuliah'));

                if (empty($rawKode) || empty($nama)) {
                    continue;
                }

                $kode = strtoupper($rawKode);

                // Tentukan Kurikulum
                $idKurikulum = $request->id_kurikulum;
                $tahunClean = preg_replace('/[^\d]/', '', $rawTahunKur);

                if (!empty($tahunClean) && $id_prodi_user) {
                    $kurObj = Kurikulum::where('id_prodi', $id_prodi_user)
                        ->where('tahun', $tahunClean)
                        ->first();
                    if ($kurObj) {
                        $idKurikulum = $kurObj->id;
                    }
                }

                if (!$idKurikulum && $id_prodi_user) {
                    $kurObj = Kurikulum::where('id_prodi', $id_prodi_user)
                        ->orderBy('tahun', 'desc')
                        ->first();
                    if ($kurObj) {
                        $idKurikulum = $kurObj->id;
                    }
                }

                $existingMk = MK::where('kode', $kode)->first();

                if ($existingMk) {
                    $existingMk->update([
                        'nama' => $nama,
                        'nama_eng' => !empty($nama_eng) ? $nama_eng : $nama,
                        'semester' => $semester,
                        'rumpun' => !empty($rumpun) ? $rumpun : 'Wajib',
                        'prasyarat' => !empty($prasyarat) ? $prasyarat : 'Tidak ada',
                        'id_kurikulum' => $idKurikulum ?: $existingMk->id_kurikulum,
                        'id_prodi' => $id_prodi_user ?: $existingMk->id_prodi,
                        'deskripsi' => !empty($deskripsi) ? $deskripsi : $existingMk->deskripsi,
                        'batas_kelulusan_mhs' => $batas_mhs,
                        'batas_kelulusan_mk' => $batas_mk,
                        'bobot_teori' => $bobot_teori,
                        'bobot_praktikum' => $bobot_praktikum,
                        'updated_at' => now(),
                    ]);
                    $updatedCount++;
                } else {
                    MK::create([
                        'kode' => $kode,
                        'nama' => $nama,
                        'nama_eng' => !empty($nama_eng) ? $nama_eng : $nama,
                        'semester' => $semester,
                        'rumpun' => !empty($rumpun) ? $rumpun : 'Wajib',
                        'prasyarat' => !empty($prasyarat) ? $prasyarat : 'Tidak ada',
                        'id_kurikulum' => $idKurikulum,
                        'id_prodi' => $id_prodi_user,
                        'deskripsi' => !empty($deskripsi) ? $deskripsi : 'Deskripsi mata kuliah ' . $nama,
                        'batas_kelulusan_mhs' => $batas_mhs,
                        'batas_kelulusan_mk' => $batas_mk,
                        'bobot_teori' => $bobot_teori,
                        'bobot_praktikum' => $bobot_praktikum,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $importedCount++;
                }

                if ($idKurikulum && $id_prodi_user) {
                    DB::table('mk_kurikulum')->updateOrInsert(
                        ['mk_kode' => $kode, 'id_prodi' => $id_prodi_user],
                        ['id_kurikulum' => $idKurikulum, 'semester' => $semester, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }

            DB::commit();

            $msg = "Berhasil memproses impor Mata Kuliah: {$importedCount} data baru ditambahkan";
            if ($updatedCount > 0) {
                $msg .= ", {$updatedCount} data diperbarui.";
            } else {
                $msg .= ".";
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengimpor data Mata Kuliah: ' . $e->getMessage());
        }
    }
}
