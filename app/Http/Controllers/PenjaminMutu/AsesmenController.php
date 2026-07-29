<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CplMkCpmkPenilaian;
use App\Models\CPMK;
use App\Models\InstrumenPenilaian;
use App\Models\MetodePenilaian;
use App\Models\MK;
use App\Models\PenilaianInstrumen;
use App\Models\PenilaianMetode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AsesmenController extends Controller
{
    public function metodePenilaian()
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $user  = auth()->user();

        $queryMetodes = DB::table('metode_penilaian')
            ->join('prodi', 'metode_penilaian.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        $queryPenilaian = DB::table('cpl_mk_cpmk_penilaian')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('penilaian_metode', 'cpl_mk_cpmk_penilaian.id', '=', 'penilaian_metode.cpl_mk_cpmk_penilaian_id')
            ->select('cpl_mk_cpmk_penilaian.*', 'penilaian_metode.metode_id', 'penilaian_metode.bobot');

        $queryCpls = CPL::join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode');
        $queryMks = MK::join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->with('cpmks');
        $queryCpmks = CPMK::join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpmks.id', 'cpmks.kode', 'cpmks.cpl_id');

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $queryMetodes->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryPenilaian->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryCpls->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryMks->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryCpmks->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($otoritas === 'Penjamin Mutu Fakultas') {
            $queryMetodes->where('fakultas.id', $user->id_fakultasUser);
            $queryPenilaian->where('fakultas.id', $user->id_fakultasUser);
            $queryCpls->where('fakultas.id', $user->id_fakultasUser);
            $queryMks->where('fakultas.id', $user->id_fakultasUser);
            $queryCpmks->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMetodes->where('prodi.id', $user->id_prodiUser);
            $queryPenilaian->where('prodi.id', $user->id_prodiUser);
            $queryCpls->where('prodi.id', $user->id_prodiUser);
            $queryMks->where('prodi.id', $user->id_prodiUser);
            $queryCpmks->where('prodi.id', $user->id_prodiUser);
        }

        $metodes = $queryMetodes->pluck('metode_penilaian.nama', 'metode_penilaian.id');
        $penilaian = $queryPenilaian->get()->groupBy('id');
        $cpls = $queryCpls->get()->keyBy('id');
        $mks = $queryMks->get()->keyBy('kode');
        $cpmks = $queryCpmks->get()->keyBy('id');

        return view('penjamin-mutu.TPMPS.asesmen.metode_penilaian', compact('penilaian', 'metodes', 'cpls', 'mks', 'cpmks'));
    }

    public function addMetodePenilaian()
    {
        return view('penjamin-mutu.TPMPS.asesmen.add_metode_penilaian');
    }

    public function storeMetodePenilaian(Request $request)
    {

        $validate = $request->validate([
            'nama_metode' => [
                'required',
                'string',
                Rule::unique('metode_penilaian', 'nama')->where('id_prodi', auth()->user()->id_prodiUser)
            ],
        ]);

        try {
            MetodePenilaian::create([
                'nama' => $validate['nama_metode'],
                'id_prodi' => auth()->user()->id_prodiUser
            ]);

            return redirect()->back()->with('success', 'Data Metode Penilaian berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('failed', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tahapPenilaian()
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $user = auth()->user();

        $queryPenilaian = DB::table('cpl_mk_cpmk_penilaian')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('cpmks', 'cpl_mk_cpmk_penilaian.cpmk_id', '=', 'cpmks.id')
            ->join('mks', 'cpl_mk_cpmk_penilaian.mk_kode', '=', 'mks.kode')
            ->select(
                'cpls.kode as cpl_kode',
                'mks.kode as mk_kode',
                'cpmks.kode as cpmk_kode',
                'cpl_mk_cpmk_penilaian.tahap_penilaian',
                'cpl_mk_cpmk_penilaian.instrumen',
                'cpl_mk_cpmk_penilaian.id',
            );

        $queryMetodes = DB::table('penilaian_metode')
            ->join('cpl_mk_cpmk_penilaian', 'penilaian_metode.cpl_mk_cpmk_penilaian_id', '=', 'cpl_mk_cpmk_penilaian.id')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('metode_penilaian', 'penilaian_metode.metode_id', '=', 'metode_penilaian.id')
            ->select('metode_penilaian.nama as metode', 'penilaian_metode.cpl_mk_cpmk_penilaian_id as id');

        $queryInstrumens = DB::table('penilaian_instrumen')
            ->join('cpl_mk_cpmk_penilaian', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id', '=', 'cpl_mk_cpmk_penilaian.id')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('instrumen_penilaian', 'penilaian_instrumen.kriteria_id', '=', 'instrumen_penilaian.id')
            ->select('instrumen_penilaian.nama_kriteria', 'penilaian_instrumen.bobot_metode', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id as id');

        // Filtering berdasarkan otoritas pengguna
        if ($otoritas === 'Penjamin Mutu Universitas') {
            $queryPenilaian->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryMetodes->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryInstrumens->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($otoritas === 'Penjamin Mutu Fakultas') {
            $queryPenilaian->where('fakultas.id', $user->id_fakultasUser);
            $queryMetodes->where('fakultas.id', $user->id_fakultasUser);
            $queryInstrumens->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryPenilaian->where('prodi.id', $user->id_prodiUser);
            $queryMetodes->where('prodi.id', $user->id_prodiUser);
            $queryInstrumens->where('prodi.id', $user->id_prodiUser);
        }

        $penilaian = $queryPenilaian->get();
        $metodes = $queryMetodes->get()->collect();
        $instrumens = $queryInstrumens->get();

        return view('penjamin-mutu.TPMPS.asesmen.tahap_penilaian', compact('penilaian', 'metodes', 'instrumens'));
    }

    public function addInstrumenPenilaian()
    {
        return view('penjamin-mutu.TPMPS.asesmen.add_instrumen_penilaian');
    }

    public function storeInstrumenPenilaian(Request $request)
    {
        // $validate = $request->validate(['nama_kriteria' => 'required|string|unique:instrumen_penilaian,nama_kriteria']);
        $validate = $request->validate([
            'nama_kriteria' => [
                'required',
                'string',
                Rule::unique('instrumen_penilaian', 'nama_kriteria')->where('id_prodi', auth()->user()->id_prodiUser)
            ],
        ]);

        try {
            InstrumenPenilaian::firstOrCreate([
                'nama_kriteria' => $validate['nama_kriteria'],
                'id_prodi' => auth()->user()->id_prodiUser
            ]);

            return redirect()->back()->with('success', 'Data Instrumen Penilaian berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('failed', 'Terjadi kesalahan :' . $e->getMessage());
        }
    }

    public function bobotPenilaian()
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $user = auth()->user();
        
        $queryMetodes = DB::table('metode_penilaian')
            ->join('prodi', 'metode_penilaian.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        $queryPenilaian = DB::table('cpl_mk_cpmk_penilaian')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->Join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('penilaian_metode', 'cpl_mk_cpmk_penilaian.id', '=', 'penilaian_metode.cpl_mk_cpmk_penilaian_id')
            ->select('cpl_mk_cpmk_penilaian.*', 'penilaian_metode.metode_id', 'penilaian_metode.bobot');

        $queryCpls = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'fakultas.id', '=', 'prodi.id_fakultas')
            ->select('cpls.id', 'cpls.kode');
            
        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'fakultas.id', '=', 'prodi.id_fakultas')
            ->select('mks.*')
            ->with('cpmks');

        $queryCpmks = CPMK::query()
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'fakultas.id', '=', 'prodi.id_fakultas')
            ->select('cpmks.id', 'cpmks.kode', 'cpmks.cpl_id');

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $queryMetodes->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryPenilaian->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryCpls->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryMks->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryCpmks->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($otoritas === 'Penjamin Mutu Fakultas') {
            $queryMetodes->where('fakultas.id', $user->id_fakultasUser);
            $queryPenilaian->where('fakultas.id', $user->id_fakultasUser);
            $queryCpls->where('fakultas.id', $user->id_fakultasUser);
            $queryMks->where('fakultas.id', $user->id_fakultasUser);
            $queryCpmks->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMetodes->where('prodi.id', $user->id_prodiUser);
            $queryPenilaian->where('prodi.id', $user->id_prodiUser);
            $queryCpls->where('prodi.id', $user->id_prodiUser);
            $queryMks->where('prodi.id', $user->id_prodiUser);
            $queryCpmks->where('prodi.id', $user->id_prodiUser);
        }

        $metodes = $queryMetodes->pluck('metode_penilaian.nama', 'metode_penilaian.id');
        $penilaian = $queryPenilaian->get()->groupBy('id');
        $cpls = $queryCpls->get()->keyBy('id');
        $mks = $queryMks->get()->keyBy('kode');
        $cpmks = $queryCpmks->get()->keyBy('id');

        return view('penjamin-mutu.TPMPS.asesmen.bobot_penilaian', compact('penilaian', 'metodes', 'cpls', 'mks', 'cpmks'));
    }
    
    public function NA_MK()
    {
        $queryPenilaian = DB::table('cpl_mk_cpmk_penilaian')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('cpmks', 'cpl_mk_cpmk_penilaian.cpmk_id', '=', 'cpmks.id')
            ->join('mks', 'cpl_mk_cpmk_penilaian.mk_kode', '=', 'mks.kode')
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id') // Join untuk filtering prodi
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select(
                'cpls.kode as cpl_kode',
                'mks.kode as mk_kode',
                'cpmks.kode as cpmk_kode',
                'cpl_mk_cpmk_penilaian.id',
            );

        // Filtering berdasarkan otoritas
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryPenilaian->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryPenilaian->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryPenilaian->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $penilaian = $queryPenilaian->get()->collect();

        $instrumens = DB::table('penilaian_instrumen')
            ->join('cpl_mk_cpmk_penilaian', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id', '=', 'cpl_mk_cpmk_penilaian.id')
            ->select('penilaian_instrumen.bobot_metode', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id as id')
            ->get()->collect();

        return view('penjamin-mutu.TPMPS.asesmen.NA_MK', compact('penilaian', 'instrumens'));
    }


    public function NA_CPL()
    {
        $queryPenilaian = DB::table('cpl_mk_cpmk_penilaian')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('cpmks', 'cpl_mk_cpmk_penilaian.cpmk_id', '=', 'cpmks.id')
            ->join('mks', 'cpl_mk_cpmk_penilaian.mk_kode', '=', 'mks.kode')
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id') // Join untuk filtering prodi
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select(
                'cpls.kode as cpl_kode',
                'mks.kode as mk_kode',
                'cpmks.kode as cpmk_kode',
                'cpl_mk_cpmk_penilaian.id',
            );

        $queryInstrumens = DB::table('penilaian_instrumen')
            ->join('cpl_mk_cpmk_penilaian', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id', '=', 'cpl_mk_cpmk_penilaian.id')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('instrumen_penilaian', 'penilaian_instrumen.kriteria_id', '=', 'instrumen_penilaian.id')
            ->select('instrumen_penilaian.nama_kriteria', 'penilaian_instrumen.bobot_metode', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id as id');

        // Filtering berdasarkan otoritas
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryPenilaian->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryInstrumens->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryPenilaian->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryInstrumens->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryPenilaian->where('prodi.id', auth()->user()->id_prodiUser);
            $queryInstrumens->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $penilaian = $queryPenilaian->get()->collect();
        $instrumens = $queryInstrumens->get()->collect();

        // $instrumens = DB::table('penilaian_instrumen')
        //     ->join('cpl_mk_cpmk_penilaian', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id', '=', 'cpl_mk_cpmk_penilaian.id')
        //     ->select('penilaian_instrumen.bobot_metode', 'penilaian_instrumen.cpl_mk_cpmk_penilaian_id as id')
        //     ->get()->collect();

        return view('penjamin-mutu.TPMPS.asesmen.NA_CPL', compact('penilaian', 'instrumens'));
    }


    public function addAsesmen()
    {
        // $cpls = CPL::all();
        $mks = MK::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $metodepenilaians = MetodePenilaian::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $instrumenPenilaians = InstrumenPenilaian::where('id_prodi', auth()->user()->id_prodiUser)->get();

        return view('penjamin-mutu.TPMPS.asesmen.add_asesmen', compact('mks', 'metodepenilaians', 'instrumenPenilaians'));
    }

    public function getCplByMk($mkKode)
    {
        $mk = MK::with('cpl')->findOrFail($mkKode);
        $cpls = $mk->cpl;

        return response()->json(['cpls' => $cpls]);
    }

    public function getCpmkByCpl($cplId)
    {
        $mkKode = request('mk_kode');

        $cpmks = CPMK::where('cpl_id', $cplId)
            ->whereHas('mks', function ($query) use ($mkKode) {
                $query->where('kode', $mkKode);
            })
            ->with('mk')
            ->get();

        return response()->json(['cpmks' => $cpmks]);
    }

    public function getMaxInputKriteriaMK($mkKode)
    {
        $MkPenilaianId = CplMkCpmkPenilaian::where('mk_kode', $mkKode)->pluck('id');

        $bobotMK = PenilaianInstrumen::whereIn('cpl_mk_cpmk_penilaian_id', $MkPenilaianId)->sum('bobot_metode');

        $maxBobotKriteriaInput = 100 - $bobotMK;

        if ($bobotMK >= 100) {
            return response()->json(['maxKriteriaBobotMK' => 0]);
        } else {
            return response()->json(['maxKriteriaBobotMK' => $maxBobotKriteriaInput]);
        }
    }

    public function storeAsesmen(Request $request)
    {
        $validate = $request->validate([
            'mk_kode' => 'required|string|exists:mks,kode',
            'cpmk_id' => 'required|integer|exists:cpmks,id',
            'cpl_id' => 'required|integer|exists:cpls,id',
            'tahap_penilaians' => 'required|array|min:1',
            'tahap_penilaians.*' => 'string|in:Akhir Semester,Tengah Semester,Perkuliahan',
            'instrumen' => 'required|string|in:Rubrik,Panduan Proyek Akhir',
            'metode_penilaian' => 'required|array|min:1',
            'metode_penilaian.*' => 'required|integer|exists:metode_penilaian,id',
            'bobot' => 'required|array|min:1',
            'bobot.*' => 'required|numeric|min:0|max:100',
            'kriteria_penilaian' => 'required|array|min:1',
            'kriteria_penilaian.*' => 'required|integer|exists:instrumen_penilaian,id',
            'bobot_kriteria' => 'required|array|min:1',
            'bobot_kriteria.*' => 'required|numeric|min:0|max:100',
        ]);

        // Menyimpan atau mengambil data CplMkCpmkPenilaian
        $cplMkCpmkPenilaian = CplMkCpmkPenilaian::updateOrCreate(
            [
                'mk_kode' => $validate['mk_kode'],
                'cpmk_id' => $validate['cpmk_id'],
                'cpl_id' => $validate['cpl_id'],
            ],
            [
                'tahap_penilaian' => implode(', ', $validate['tahap_penilaians']),
                'instrumen' => $validate['instrumen'],
            ]
        );

        // Menyimpan atau mengambil data PenilaianMetode untuk setiap metode_penilaian
        foreach ($validate['metode_penilaian'] as $metodePenilaian) {
            PenilaianMetode::updateOrCreate(
                [
                    'cpl_mk_cpmk_penilaian_id' => $cplMkCpmkPenilaian->id,
                    'metode_id' => $metodePenilaian,
                ],
                [
                    'bobot' => $validate['bobot'][$metodePenilaian],
                ]
            );
        }

        // Menyimpan atau mengambil data PenilaianInstrumen untuk setiap kriteria_penilaian
        foreach ($validate['kriteria_penilaian'] as $kriteriaPenilaian) {

            PenilaianInstrumen::updateOrCreate(
                [
                    'cpl_mk_cpmk_penilaian_id' => $cplMkCpmkPenilaian->id,
                    'kriteria_id' => $kriteriaPenilaian,
                ],
                [
                    'bobot_metode' => $validate['bobot_kriteria'][$kriteriaPenilaian],
                ]
            );
        }

        return redirect()->back()->with('success', 'Data asesmen berhasil disimpan.');
    }
}
