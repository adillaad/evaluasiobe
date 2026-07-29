<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CPL;
use App\Models\MK;
use App\Models\RPS;
use App\Models\CPLMK;
use App\Support\DosenMkResolver;
use App\Traits\UniversityFilterTrait;
use Illuminate\Support\Facades\DB;

class CPLMKcontroller extends Controller
{
    use UniversityFilterTrait;

    private function applyRpsScopeForUser($queryRpss)
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        if ($otoritas === 'Wakil Rektor') {
            return $queryRpss->where('fakultas.id_universitas', $user->id_universitasUser);
        }

        if ($otoritas === 'Wakil Dekan') {
            return $queryRpss->where('fakultas.id', $user->id_fakultasUser);
        }

        if ($otoritas === 'Kepala Program Studi') {
            return $queryRpss->where('prodi.id', $user->id_prodiUser);
        }

        if ($otoritas === 'Dosen') {
            $userName = $user->name;

            return $queryRpss
                ->where('prodi.id', $user->id_prodiUser)
                ->where(function ($query) use ($userName) {
                    $query->where('rpss.pengembang', $userName)
                        ->orWhere('rpss.dosen', $userName)
                        ->orWhere('rpss.dosen_anggota1', $userName)
                        ->orWhere('rpss.dosen_anggota2', $userName)
                        ->orWhere('rpss.koordinator', $userName);
                });
        }

        return $queryRpss;
    }

    private function resolveMksAndCpls($rpss)
    {
        $user = auth()->user();
        $kodeMks = $rpss->pluck('kode_mk')->filter()->unique();

        $mks = MK::query()
            ->whereIn('kode', $kodeMks)
            ->where('id_prodi', $user->id_prodiUser)
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        if ($mks->isEmpty() && $user->otoritas->otoritas === 'Dosen') {
            $mks = DosenMkResolver::forUser($user);
        }

        $idKurikulums = $mks->pluck('id_kurikulum')->unique()->filter();

        $cplQuery = CPL::query()
            ->where('cpls.id_prodi', $user->id_prodiUser)
            ->with('kurikulum')
            ->orderBy('nomor', 'asc');

        if ($idKurikulums->isNotEmpty()) {
            $cplQuery->whereIn('cpls.id_kurikulum', $idKurikulums);
        }

        $cpls = $cplQuery->get();

        return [$mks, $cpls];
    }
    
    public function Add()
    {
        $queryRpss = RPS::query()
            ->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('mks')
                    ->whereColumn('mks.kode', 'rpss.kode_mk');
            })
            ->select('rpss.*');

        $queryRpss = $this->applyRpsScopeForUser($queryRpss);
        $rpss = $queryRpss->get();

        [$mks, $cpls] = $this->resolveMksAndCpls($rpss);
        
        return view('dosen.CPLMK.add', compact('mks', 'cpls'));
    }

    public function List(Request $request)
    {
        $queryRpss = RPS::query()
        ->with(['prodi', 'prodi.fakultas'])
            ->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('rpss.*');

        if (auth()->user()->otoritas->otoritas == 'Wakil Rektor') {
            $queryRpss->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else {
            $queryRpss = $this->applyRpsScopeForUser($queryRpss);
        }
        
        $queryRpss = $this->getFilteredQuery($queryRpss, $request);
        $rpss = $queryRpss->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        $kode_mks = $rpss->pluck('kode_mk');
        $mks = MK::whereIn('kode', $kode_mks)
            ->where('id_prodi', auth()->user()->id_prodiUser)
            ->get();

        if ($mks->isEmpty() && auth()->user()->otoritas->otoritas === 'Dosen') {
            $mks = DosenMkResolver::forUser(auth()->user());
            $kode_mks = $mks->pluck('kode');
        }
        // foreach ($rpss as $rps) {
        //     $mk = MK::where('kode', $rps->kode_mk)->firstOrFail();
        //     $mks->push($mk);
        // }
        $cplmks = DB::table('mk_cpl')->whereIn('mk_kode',$kode_mks)->get();
        // foreach ($mks as $mk) {
        //     $kode_mk = $mk->kode;
        //     $temp = CPLMK::where('kode_mk', $kode_mk)->get();
        //     foreach ($temp as $cplmk) {
        //         $cplmks->push($cplmk);
        //     }
        // }
        
        $cpls = CPL::all();
        return view('dosen.CPLMK.list', array_merge(
            ['mks' => $mks],
            ['cpls' => $cpls],
            ['cplmks' => $cplmks],
            ['rpss' => $rpss],
            $filterData
        ));
    }

    public function Store(Request $request)
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
        return redirect()->route('dosen.cplmk-list')->with('success', 'CPL successfully added!');
    }

    public function Delete($id)
    {
        DB::table('mk_cpl')->where('id', $id)->delete();
        return redirect()->route('dosen.cplmk-list')->with('success', 'Kode CPL successfully removed!');
    }

}