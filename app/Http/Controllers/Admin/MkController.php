<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use App\Models\MK;
use App\Models\Kurikulum;
use App\Support\MkKodeUpdater;
use App\Traits\UniversityFilterTrait;

class MkController extends Controller
{
    use UniversityFilterTrait;

    public function create()
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $fakultas = collect();

        // Admin Universitas bisa memilih semua fakultas di universitasnya
        if (in_array($otoritas, ['Admin Universitas', 'Penjamin Mutu Universitas'])) {
            $fakultas = Fakultas::where('id_universitas', $user->id_universitasUser)->get();
        } else {
            // User level lain hanya bisa mengakses fakultasnya sendiri
            $fakultas = Fakultas::where('id', $user->id_fakultasUser)->get();
        }

        $mksUniv = MK::whereNull('id_prodi')->get();

        // Kirim data fakultas dan collection kosong untuk dropdown lainnya
        return view('admin.mk.add', [
            'fakultas' => $fakultas,
            'prodi' => collect(),
            'kurikulums' => collect(),
            'mks' => collect(),
            'mksUniv' => $mksUniv,
        ]);
    }

    public function list(Request $request)
    {
        $user = auth()->user();
        $query = MK::with(['kurikulum', 'prodi.fakultas.universitas'])
            ->leftJoin('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->leftJoin('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
            ->select('mks.*');

        if ($user->otoritas->otoritas === 'Admin Universitas') {
            $query->where(function($q) use ($user) {
                $q->where('fakultas.id_universitas', $user->id_universitasUser)
                  ->orWhereNull('mks.id_prodi');
            });
        }

        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);
        $mks = $query->orderBy('mks.created_at', 'desc')->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.mk.list', array_merge(
            $filterData,
            ['mks' => $mks]
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'alpha_num', 'min:9'],
            'nama' => ['required', 'string'],
            'semester' => ['required'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string'],
            'id_kurikulum' => ['nullable'],
            'id_prodi' => ['nullable'],
            'id_fakultas' => ['nullable'],
            'deskripsi' => 'required',
            'bobot_teori' => ['required', 'integer', 'digits:1'],
            'bobot_praktikum' => ['nullable', 'integer', 'digits:1'],
        ]);
        
        $prasyarat = $request->prasyarat ?? 'Tidak ada';
        $bobot_praktikum = $request->bobot_praktikum ?? 0;
        $semesterVal = is_array($request->semester) ? implode(',', $request->semester) : $request->semester;
        $idProdi = $request->filled('id_prodi') ? $request->id_prodi : null;
        $idKurikulum = $request->filled('id_kurikulum') ? $request->id_kurikulum : null;

        try {
            MK::create([
                'kode' => strtoupper($request->kode),
                'nama' => $request->nama,
                'semester' => $semesterVal,
                'rumpun' => $request->rumpun,
                'prasyarat' => $prasyarat,
                'id_kurikulum' => $idKurikulum,
                'id_prodi' => $idProdi,
                'deskripsi' => $request->deskripsi,
                'batas_kelulusan_mhs' => $request->input('batas_kelulusan_mhs', 50.00),
                'batas_kelulusan_mk' => $request->input('batas_kelulusan_mk', 75.00),
                'bobot_teori' => $request->bobot_teori,
                'bobot_praktikum' => $bobot_praktikum,
            ]);

            if ($idKurikulum && is_null($idProdi)) {
                \Illuminate\Support\Facades\DB::table('mk_kurikulum')->updateOrInsert(
                    ['mk_kode' => strtoupper($request->kode), 'id_kurikulum' => $idKurikulum],
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

        // Temukan MK beserta relasi yang diperlukan (kurikulum -> prodi -> fakultas)
        $mk = MK::with(['kurikulum', 'prodi.fakultas'])
                ->where('kode', $kode)
                ->firstOrFail();

        // Pastikan MK yang diakses berada di universitas user yang login (jika ada prodi)
        if ($mk->prodi && $mk->prodi->fakultas && $mk->prodi->fakultas->id_universitas != $user->id_universitasUser) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil data terpilih dari MK yang ada
        $selectedKurikulum = $mk->kurikulum;
        $selectedProdi = $mk->prodi;
        $selectedFakultas = $selectedProdi ? $selectedProdi->fakultas : null;

        // Ambil semua data untuk pilihan dropdown
        $allFakultas = Fakultas::where('id_universitas', $user->id_universitasUser)->get();
        $allProdi = $selectedFakultas ? Prodi::where('id_fakultas', $selectedFakultas->id)->get() : collect();
        $allKurikulum = $selectedProdi 
            ? Kurikulum::where('id_prodi', $selectedProdi->id)->get() 
            : Kurikulum::whereHas('prodi.fakultas', function($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
              })->orderBy('tahun', 'desc')->get();
        
        // Ambil daftar MK lain untuk pilihan prasyarat
        $allMkPrasyarat = MK::where('kode', '!=', $kode)
                            ->where(function($q) use ($user, $selectedProdi) {
                                if ($selectedProdi) {
                                    $q->where('id_prodi', $selectedProdi->id)->orWhereNull('id_prodi');
                                } else {
                                    $q->whereHas('prodi.fakultas', function($sub) use ($user) {
                                        $sub->where('id_universitas', $user->id_universitasUser);
                                    })->orWhereNull('id_prodi');
                                }
                            })
                            ->get();

        // Kumpulkan semua data untuk dikirim ke view
        $data = [
            'mk' => $mk,
            'selectedFakultas' => $selectedFakultas,
            'selectedProdi' => $selectedProdi,
            'selectedKurikulum' => $selectedKurikulum,
            'allFakultas' => $allFakultas,
            'allProdi' => $allProdi,
            'allKurikulum' => $allKurikulum,
            'allMkPrasyarat' => $allMkPrasyarat,
        ];
            
        return view('admin.mk.edit', $data);
    }

    public function update(Request $request, $kode)
    {
        $request->validate([
            'id_fakultas' => 'nullable',
            'id_prodi' => 'nullable',
            'id_kurikulum' => 'nullable',
            'kode' => ['required', 'alpha_num', 'min:9'],
            'nama' => ['required', 'string'],
            'semester' => ['required'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string'],
            'deskripsi' => 'required',
            'bobot_teori' => ['required', 'integer', 'digits:1'],
            'bobot_praktikum' => ['nullable', 'integer', 'digits:1'],
        ]);

        $mk = MK::where('kode', $kode)->firstOrFail();

        $newKode = strtoupper($request->kode);

        if ($newKode !== $mk->kode && MK::where('kode', $newKode)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Kode mata kuliah ' . $newKode . ' sudah ada.');
        }

        $prasyarat = $request->prasyarat ?? 'Tidak ada';
        $bobot_praktikum = $request->bobot_praktikum ?? 0;
        $semesterVal = is_array($request->semester) ? implode(',', $request->semester) : $request->semester;
        $isUniv = $request->input('mk_type_toggle') === 'univ';
        $idProdi = $isUniv ? null : ($request->filled('id_prodi') ? $request->id_prodi : null);
        $idKurikulum = $isUniv ? null : ($request->filled('id_kurikulum') ? $request->id_kurikulum : null);

        $attributes = [
            'nama' => $request->nama,
            'semester' => $semesterVal,
            'rumpun' => $request->rumpun,
            'prasyarat' => $prasyarat,
            'id_kurikulum' => $idKurikulum,
            'id_prodi' => $idProdi,
            'deskripsi' => $request->deskripsi,
            'batas_kelulusan_mhs' => $request->input('batas_kelulusan_mhs', $mk->batas_kelulusan_mhs ?? 50.00),
            'batas_kelulusan_mk' => $request->input('batas_kelulusan_mk', $mk->batas_kelulusan_mk ?? 75.00),
            'bobot_teori' => $request->bobot_teori,
            'bobot_praktikum' => $bobot_praktikum,
            'updated_at' => now(),
        ];

        try {
            MkKodeUpdater::rename($mk->kode, $newKode, $attributes);

            $targetProdiId = $user->id_prodiUser ?? $idProdi;
            if ($idKurikulum && $targetProdiId) {
                \Illuminate\Support\Facades\DB::table('mk_kurikulum')->updateOrInsert(
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
        if ($user->otoritas->otoritas === 'Admin Universitas' || $user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where(function($q) use ($user) {
                $q->whereHas('prodi.fakultas', function($f) use ($user) {
                    $f->where('id_universitas', $user->id_universitasUser);
                })->orWhereNull('id_prodi');
            });
        } else if ($user->id_prodiUser) {
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
            'Admin' => 'admin.list-mk',
            'Admin Universitas' => 'admin-universitas.list-mk',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }

    public function getProdi($fakultas_id)
    {
        $prodi = Prodi::where('id_fakultas', $fakultas_id)
            ->select('id', 'nama')
            ->get();
        return response()->json($prodi);
    }

    public function getKurikulum($prodi_id = null)
    {
        $user = auth()->user();
        if (!$prodi_id || $prodi_id === 'all' || $prodi_id === '0') {
            $kurikulum = Kurikulum::whereHas('prodi.fakultas', function($q) use ($user) {
                $q->where('id_universitas', $user->id_universitasUser);
            })
            ->select('id', 'tahun')
            ->orderBy('tahun', 'desc')
            ->get();
        } else {
            $kurikulum = Kurikulum::where('id_prodi', $prodi_id)
                ->select('id', 'tahun')
                ->orderBy('tahun', 'desc')
                ->get();
        }
        return response()->json($kurikulum);
    }

    public function getMkPrasyarat($prodi_id = null)
    {
        $user = auth()->user();
        if (!$prodi_id || $prodi_id === 'all' || $prodi_id === '0') {
            $mks = MK::where(function($q) use ($user) {
                $q->whereHas('prodi.fakultas', function($sub) use ($user) {
                    $sub->where('id_universitas', $user->id_universitasUser);
                })->orWhereNull('id_prodi');
            })
            ->select('kode', 'nama')
            ->get();
        } else {
            $mks = MK::where(function($q) use ($prodi_id) {
                $q->where('id_prodi', $prodi_id)->orWhereNull('id_prodi');
            })
            ->select('kode', 'nama')
            ->get();
        }
        return response()->json($mks);
    }
}
