<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\MK;
use App\Models\RPS;
use App\Models\CPLMK;
use App\Models\MK_CPL;
use App\Traits\UniversityFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CplmkController extends Controller
{
    use UniversityFilterTrait;

    public function create(Request $request)
    {
        $rpss = RPS::query()
            ->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('fakultas.id_universitas', auth()->user()->id_universitasUser)->get();
        $mks = collect();
        foreach ($rpss as $rps) {
            $mk = MK::where('kode', $rps->kode_mk)->firstOrFail();
            $mks->push($mk);
        }

        $query = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id')
            ->select('cpls.*', 'kurikulums.tahun');

        // Gunakan method dari trait untuk filter
        
        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('universitas.id', auth()->user()->id_universitasUser);
        }
        
        $query = $this->getFilteredQuery($query, $request);
        $cpls = $query->orderBy('aspek', 'desc')->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.cplmk.add', array_merge(
            ['mks' => $mks],
            ['cpls' => $cpls],
            $filterData
        ));
    }

    public function list(Request $request)
    {
        // $rpss = RPS::query()
        //     ->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
        //     ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
        //     ->select('rpss.*');
        // $kode_mks = $rpss->pluck('kode_mk');
        // $mks = MK::whereIn('kode',$kode_mks)->get();
        // foreach ($rpss as $rps) {
        //     $mk = MK::where('kode', $rps->kode_mk)->firstOrFail();
        //     $mks->push($mk);
        // }
        $query = MK_CPL::query()
            ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('mk_cpl.*');
        // foreach ($mks as $mk) {
        //     $kode_mk = $mk->kode;
        //     $temp = CPLMK::where('kode_mk', $kode_mk)->get();
        //     foreach ($temp as $cplmk) {
        //         $cplmks->push($cplmk);
        //     }
        // }
        
        // $cpls = CPL::query()
        //     ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
        //     ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
        //     ->select('cpls.*');

        // $query = CPLMK::query()
        //     ->join('prodi', 'cplmks.id_prodi', '=', 'prodi.id')
        //     ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
        //     ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
        //     ->select('cplmks.*');

        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('universitas.id', auth()->user()->id_universitasUser);
        }

        // // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);
        $cplmks = $query->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.cplmk.list', array_merge(
            ['cplmks' => $cplmks],
            $filterData
        ));

        // return view('admin.cplmk.list', compact('cplmks'));


        // return view('admin.cplmk.list', compact('cplmks', 'mks', 'cpls'));
    }

    public function store(Request $request)
    {   
        $data = [];

        foreach ($request->input('id_cpl', []) as $id_cpl) { // Default `[]` agar tidak error jika `id_cpl` kosong
            $data[] = [
                'mk_kode' => $request->kode_mk,
                'cpl_id' => $id_cpl,
                'id_prodi' => auth()->user()->id_prodiUser,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('mk_cpl')->insertOrIgnore($data);

        return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL successfully added!');
    }

    public function delete($id)
    {
        DB::table('mk_cpl')->where('id', $id)->delete();
        return redirect()->route($this->getRouteByAuthority())->with('success', 'Kode CPL successfully removed!');
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-cplmk',
            'Admin Universitas' => 'admin-universitas.list-cplmk',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }
}
