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
        $kurikulums = Kurikulum::where('id_prodi', $user->id_prodiUser)
                            ->orderBy('tahun', 'desc')
                            ->get();
        $mks = MK::where('id_prodi', $user->id_prodiUser)->get();
        $prodi = $user->prodi;
        return view('penjamin-mutu.mk.add', compact('kurikulums', 'mks', 'prodi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'alpha_num', 'min:9', 'max:50'],
            'nama' => ['required', 'string', 'regex:/^[\/a-zA-Z -]+$/', 'max:255'],
            'nama_eng' => ['required', 'string', 'regex:/^[\/a-zA-Z -]+$/', 'max:50'],
            'semester' => ['required', 'integer', 'between:1,8'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string', 'max:255'],
            'id_kurikulum' => ['required', 'integer'],
            'deskripsi' => 'required',
            'batas_kelulusan_mhs' => ['required','numeric','min:0','max:100'], 
            'batas_kelulusan_mk'  => ['required','numeric','min:0','max:100'],
            'bobot_teori' => ['required', 'integer', 'digits:1'],
            'bobot_praktikum' => ['nullable', 'integer', 'digits:1'],
        ]);
        
        $prasyarat = $request->prasyarat ?? 'Tidak ada';
        $bobot_praktikum = $request->bobot_praktikum ?? 0;
        try {
            MK::create([
                'kode' => strtoupper($request->kode),
                'nama' => $request->nama,
                'nama_eng' => $request->nama_eng,
                'semester' => $request->semester,
                'rumpun' => $request->rumpun,
                'prasyarat' => $prasyarat,
                'id_kurikulum' => $request->id_kurikulum,
                'id_prodi' => auth()->user()->id_prodiUser,
                'deskripsi' => $request->deskripsi,
                'batas_kelulusan_mhs' => $request->batas_kelulusan_mhs,
                'batas_kelulusan_mk' => $request->batas_kelulusan_mk, 
                'bobot_teori' => $request->bobot_teori,
                'bobot_praktikum' => $bobot_praktikum,
            ]);
            return redirect()->route($this->getRouteByAuthority())->with('success', 'MK successfully added!');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062)
                return redirect()->back()->withInput($request->all)->with('error', 'Kode mata kuliah ' . $request->kode . ' sudah ada');
        }
    }

    public function edit($kode)
    {
        $user = auth()->user();
        $userProdiId = $user->id_prodiUser;

        $mk = MK::where('kode', $kode)
                ->where('id_prodi', $userProdiId)
                ->firstOrFail();

        $kurikulums = Kurikulum::where('id_prodi', $userProdiId)
                            ->orderBy('tahun', 'desc')
                            ->get();

        $prasyarats = MK::where('id_prodi', $userProdiId)
                        ->where('kode', '!=', $kode)
                        ->get();

        return view('penjamin-mutu.mk.edit', compact('mk', 'kurikulums', 'prasyarats'));
    }

    public function update(Request $request, $kode)
    {
        $request->validate([
            'id_kurikulum' => 'required|integer',
            'kode' => ['required', 'alpha_num', 'min:9', 'max:50'],
            'nama' => ['required', 'string', 'regex:/^[\/a-zA-Z -]+$/', 'max:255'],
            'nama_eng' => ['required', 'string', 'max:50'],
            'semester' => ['required', 'integer', 'between:1,8'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string', 'max:255'],
            'deskripsi' => 'required',
            'batas_kelulusan_mhs' => ['required','numeric','min:0','max:100'],
            'batas_kelulusan_mk'  => ['required','numeric','min:0','max:100'],
            'bobot_teori' => ['required', 'integer', 'digits:1'],
            'bobot_praktikum' => ['nullable', 'integer', 'digits:1'],
        ]);

        $mk = MK::where('kode', $kode)
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->firstOrFail();

        $newKode = strtoupper($request->kode);

        if ($newKode !== $mk->kode && MK::where('kode', $newKode)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Kode mata kuliah ' . $newKode . ' sudah ada.');
        }

        $prasyarat = $request->prasyarat ?? 'Tidak ada';
        $bobot_praktikum = $request->bobot_praktikum ?? 0;

        $attributes = [
            'nama' => $request->nama,
            'nama_eng' => $request->nama_eng,
            'semester' => $request->semester,
            'rumpun' => $request->rumpun,
            'prasyarat' => $prasyarat,
            'id_kurikulum' => $request->id_kurikulum,
            'id_prodi' => auth()->user()->id_prodiUser,
            'deskripsi' => $request->deskripsi,
            'batas_kelulusan_mhs' => $request->batas_kelulusan_mhs,
            'batas_kelulusan_mk' => $request->batas_kelulusan_mk,
            'bobot_teori' => $request->bobot_teori,
            'bobot_praktikum' => $bobot_praktikum,
            'updated_at' => now(),
        ];

        try {
            MkKodeUpdater::rename($mk->kode, $newKode, $attributes);

            return redirect()->route($this->getRouteByAuthority())->with('success', 'MK berhasil diubah!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput()->with('error', MkKodeUpdater::databaseErrorMessage($e));
        }
    }

    public function delete($kode)
    {
        $deleted = MK::where('kode', $kode)
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->delete();

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
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }
    
    public function susunanMK(Request $request)
    {
        $query = MK::with('kurikulum')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->leftJoin('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
            ->orderBy('mks.semester', 'asc')
            ->select('mks.*');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $query = $this->getFilteredQuery($query, $request);
        $mks = $query->get();
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
            DB::raw('GROUP_CONCAT(CASE WHEN mks.rumpun = "wajib" THEN mks.kode END) as kode_wajib'),
            DB::raw('GROUP_CONCAT(CASE WHEN mks.rumpun = "peminatan" THEN mks.kode END) as kode_peminatan'),
            DB::raw('GROUP_CONCAT(CASE WHEN mks.rumpun = "wajib_kurikulum" THEN mks.kode END) as kode_wajib_kurikulum')
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

        $maxSemester = max((int) $allMks->max('semester'), (int) MK::max('semester'), 8);
        $mksBySemester = $allMks->groupBy('semester');

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
