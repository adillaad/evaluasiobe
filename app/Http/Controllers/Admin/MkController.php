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

        // Kirim data fakultas dan collection kosong untuk dropdown lainnya
        return view('admin.mk.add', [
            'fakultas' => $fakultas,
            'prodi' => collect(),
            'kurikulums' => collect(),
            'mks' => collect(),
        ]);
    }

    public function list(Request $request)
    {
        $query = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->join('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
            ->select('mks.*');

        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('id_universitas', auth()->user()->id_universitasUser);
        }

        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);
        $mks = $query->orderBy('kurikulums.id', 'desc')->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.mk.list', array_merge(
            ['mks' => $mks],
            $filterData
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'alpha_num', 'min:9', 'max:50'],
            'nama' => ['required', 'string', 'regex:/^[\/a-zA-Z -]+$/', 'max:255'],
            'semester' => ['required', 'integer', 'between:1,8'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string', 'max:255'],
            'id_kurikulum' => ['required', 'integer'],
            'id_prodi' => ['required', 'integer'],
            'id_fakultas' => ['required', 'integer'],
            'deskripsi' => 'required',
            'bobot_teori' => ['required', 'integer', 'digits:1'],
            'bobot_praktikum' => ['nullable', 'integer', 'digits:1'],
        ]);
        
        $prasyarat = $request->prasyarat ?? 'Tidak ada';
        $bobot_praktikum = $request->bobot_praktikum ?? 0;

        try {
            MK::create([
                'kode' => strtoupper($request->kode),
                'nama' => $request->nama,
                'semester' => $request->semester,
                'rumpun' => $request->rumpun,
                'prasyarat' => $prasyarat,
                'id_kurikulum' => $request->id_kurikulum,
                'id_prodi' => $request->id_prodi,
                'deskripsi' => $request->deskripsi,
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
        // Temukan MK beserta relasi yang diperlukan (kurikulum -> prodi -> fakultas)
        $mk = MK::with('kurikulum.prodi.fakultas')
                ->where('kode', $kode)
                ->firstOrFail();

        // Pastikan MK yang diakses berada di universitas user yang login
        if ($mk->kurikulum->prodi->fakultas->id_universitas != auth()->user()->id_universitasUser) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil data terpilih dari MK yang ada
        $selectedKurikulum = $mk->kurikulum;
        $selectedProdi = $selectedKurikulum->prodi;
        $selectedFakultas = $selectedProdi->fakultas;

        $user = auth()->user();

        // Ambil semua data untuk pilihan dropdown
        $allFakultas = Fakultas::where('id_universitas', $user->id_universitasUser)->get();
        $allProdi = Prodi::where('id_fakultas', $selectedFakultas->id)->get();
        $allKurikulum = Kurikulum::where('id_prodi', $selectedProdi->id)->get();
        
        // Ambil daftar MK lain dari prodi yang sama untuk pilihan prasyarat
        $allMkPrasyarat = MK::where('id_prodi', $selectedProdi->id)
                            ->where('kode', '!=', $kode) // Kecualikan MK yang sedang diedit
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
            'id_fakultas' => 'required|integer',
            'id_prodi' => 'required|integer',
            'id_kurikulum' => 'required|integer',
            'kode' => ['required', 'alpha_num', 'min:9', 'max:50'],
            'nama' => ['required', 'string', 'regex:/^[\/a-zA-Z -]+$/', 'max:255'],
            'semester' => ['required', 'integer', 'between:1,8'],
            'rumpun' => 'required',
            'prasyarat' => ['nullable', 'string', 'max:255'],
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

        $attributes = [
            'nama' => $request->nama,
            'semester' => $request->semester,
            'rumpun' => $request->rumpun,
            'prasyarat' => $prasyarat,
            'id_kurikulum' => $request->id_kurikulum,
            'id_prodi' => $request->id_prodi,
            'deskripsi' => $request->deskripsi,
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

    public function getKurikulum($prodi_id)
    {
        $kurikulum = Kurikulum::where('id_prodi', $prodi_id)
            ->select('id', 'tahun')
            ->orderBy('tahun', 'desc')
            ->get();
        return response()->json($kurikulum);
    }

    public function getMkPrasyarat($prodi_id)
    {
        // Mengambil semua MK dari prodi yang dipilih untuk dijadikan pilihan prasyarat
        $mks = MK::where('id_prodi', $prodi_id)
            ->select('kode', 'nama')
            ->get();
        return response()->json($mks);
    }
}
