<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use App\Models\CPL;
use App\Models\Kurikulum;
use App\Traits\UniversityFilterTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Crypt;

class CplController extends Controller
{
    use UniversityFilterTrait;

    public function create()
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas;

        // Data awal untuk dropdown
        $data = [
            'fakultas' => collect(),
            'prodi' => collect(),
            'kurikulums' => collect(),
        ];
        
        // Logika untuk mengisi dropdown berdasarkan otoritas user
        if ($userOtoritas === 'Admin Universitas' || $userOtoritas === 'Penjamin Mutu Universitas') {
            // Admin Univ bisa memilih fakultas mana saja di universitasnya
            $data['fakultas'] = Fakultas::where('id_universitas', $user->id_universitasUser)->get();
        } else {
            $data['fakultas'] = Fakultas::where('id', $user->id_fakultasUser)->get();
            // Jika user level fakultas, kita bisa langsung load prodi di fakultas tsb
            if ($user->id_fakultasUser) {
                $data['prodi'] = Prodi::where('id_fakultas', $user->id_fakultasUser)->get();
            }
        }

        return view('admin.cpl.add', $data);
    }

    public function list(Request $request)
    {
        $query = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id')
            ->select('cpls.*', 'kurikulums.tahun');

        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('universitas.id', auth()->user()->id_universitasUser);
        }

        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);

        
        $cpls = $query->orderBy('kurikulums.tahun', 'desc')
            ->orderByRaw("
                CASE aspek
                    WHEN 'Sikap' THEN 1
                    WHEN 'Keterampilan Umum' THEN 2
                    WHEN 'Keterampilan Khusus' THEN 3
                    WHEN 'Pengetahuan' THEN 4
                    WHEN 'Lainnya' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('cpls.kode', 'asc')
            ->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        // Kirim data CPL dalam satu variabel 'cpls'
        return view('admin.cpl.list', array_merge(
            ['cpls' => $cpls],
            $filterData
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_fakultas' => 'required|integer',
            'id_prodi' => 'required|integer',
            'id_kurikulum.*' => 'required',
            'aspek.*' => 'required|in:Sikap,Umum,Pengetahuan,Keterampilan',
            'kode.*' => 'required',
            'judul.*' => 'required',
        ]);

        try {
            foreach ($request->kode as $key => $value) {
                CPL::create([
                    'aspek' => $request->aspek[$key],
                    'id_kurikulum' => $request->id_kurikulum[$key],
                    'kode' => 'CPL' . $value,
                    'nomor' => $value,
                    'judul' => $request->judul[$key],
                    'id_prodi' => $request->id_prodi,
                ]);
            }
            
            return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL berhasil ditambahkan!');

        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nomor CPL yang diinputkan ada yang duplikat untuk Prodi ini.');
            }

            return redirect()->back()->withInput()->with('error', "Terjadi kesalahan, silahkan periksa kembali data yang diinputkan!");
        }
    }

    public function edit($id)
    {
        $ids = Crypt::decrypt($id);
        $cpl = CPL::with('kurikulum.prodi.fakultas')->findOrFail($ids);

        $selectedKurikulum = $cpl->kurikulum;
        $selectedProdi = $selectedKurikulum->prodi;
        $selectedFakultas = $selectedProdi->fakultas;

        $user = auth()->user();
        $allFakultas = Fakultas::where('id_universitas', $user->id_universitasUser)->get();
        $allProdi = Prodi::where('id_fakultas', $selectedFakultas->id)->get();
        $allKurikulum = Kurikulum::where('id_prodi', $selectedProdi->id)->get();

        $data = [
            'cpl' => $cpl,
            'selectedFakultas' => $selectedFakultas,
            'selectedProdi' => $selectedProdi,
            'selectedKurikulum' => $selectedKurikulum,
            'allFakultas' => $allFakultas,
            'allProdi' => $allProdi,
            'allKurikulum' => $allKurikulum,
        ];
            
        return view('admin.cpl.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_fakultas' => 'required|integer',
            'id_prodi' => 'required|integer',
            'aspek' => 'required',
            'id_kurikulum' => ['required', 'integer'],
            'nomor' => 'required',
            'judul' => 'required',
        ]);

        $ids = Crypt::decrypt($id);
        $cpl = CPL::findOrFail($ids);

        try {
            $cpl->update([
                'aspek' => $request->aspek,
                'id_kurikulum' => $request->id_kurikulum,
                'kode' => 'CPL' . $request->nomor,
                'nomor' => $request->nomor,
                'judul' => $request->judul,
                'id_prodi' => $request->id_prodi,
            ]);
    
            return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL berhasil diubah!');
        } catch (QueryException $e) {
            // Periksa apakah error adalah duplikat entri
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nomor yang diinputkan sudah ada untuk Prodi ini.');
            }
    
            // Jika error lain, lempar ulang exception
            throw $e;
        }
    }

    public function delete($id)
    {
        $ids = Crypt::decrypt($id);
        CPL::where('id', $ids)->delete();
        return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL berhasil dihapus!');
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-cpl',
            'Admin Universitas' => 'admin-universitas.list-cpl',
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
}
