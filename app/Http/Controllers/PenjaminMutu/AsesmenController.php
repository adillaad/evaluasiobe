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

use App\Traits\UniversityFilterTrait;

class AsesmenController extends Controller
{
    use UniversityFilterTrait;
    
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
            ->select('cpls.id', 'cpls.kode')
            ->orderBy('cpls.kode', 'asc');
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
    
    public function NA_MK(Request $request)
    {
        $filterData = $this->getFilterData($request);

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $search = $request->input('search');

        // Query MKs yang terdaftar dalam asesmen/penilaian
        $queryMKs = DB::table('cpl_mk_cpmk_penilaian')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('cpmks', 'cpl_mk_cpmk_penilaian.cpmk_id', '=', 'cpmks.id')
            ->join('mks', 'cpl_mk_cpmk_penilaian.mk_kode', '=', 'mks.kode')
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.kode as mk_kode', 'mks.nama as mk_nama')
            ->distinct();

        // Filtering berdasarkan otoritas
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryMKs->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryMKs->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMKs->where('prodi.id', auth()->user()->id_prodiUser);
        }

        // Filtering request
        if ($request->filled('universitas_id')) {
            $queryMKs->where('fakultas.id_universitas', $request->universitas_id);
        }
        if ($request->filled('fakultas_id')) {
            $queryMKs->where('fakultas.id', $request->fakultas_id);
        }
        if ($request->filled('prodi_id')) {
            $queryMKs->where('prodi.id', $request->prodi_id);
        }
        if ($request->filled('kurikulum_id')) {
            $queryMKs->where('cpls.id_kurikulum', $request->kurikulum_id);
        }

        // Filter pencarian MK (kode / nama)
        if (!empty($search)) {
            $queryMKs->where(function ($q) use ($search) {
                $q->where('mks.kode', 'like', "%{$search}%")
                  ->orWhere('mks.nama', 'like', "%{$search}%");
            });
        }

        $paginatedMKs = $queryMKs->paginate($perPage)->appends($request->all());
        $mkCodesOnPage = collect($paginatedMKs->items())->pluck('mk_kode')->toArray();

        // Ambil data detail penilaian & bobot metode hanya untuk MK yang ada pada halaman aktif
        $penilaian = collect();
        $instrumens = collect();

        if (!empty($mkCodesOnPage)) {
            $queryPenilaian = DB::table('cpl_mk_cpmk_penilaian')
                ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
                ->join('cpmks', 'cpl_mk_cpmk_penilaian.cpmk_id', '=', 'cpmks.id')
                ->join('mks', 'cpl_mk_cpmk_penilaian.mk_kode', '=', 'mks.kode')
                ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->whereIn('mks.kode', $mkCodesOnPage)
                ->select(
                    'cpls.kode as cpl_kode',
                    'mks.kode as mk_kode',
                    'mks.nama as mk_nama',
                    'cpmks.kode as cpmk_kode',
                    'cpl_mk_cpmk_penilaian.id'
                );

            if ($request->filled('kurikulum_id')) {
                $queryPenilaian->where('cpls.id_kurikulum', $request->kurikulum_id);
            }

            $penilaian = $queryPenilaian->get()->collect();

            $penilaianIds = $penilaian->pluck('id')->toArray();
            if (!empty($penilaianIds)) {
                $instrumens = DB::table('penilaian_metode')
                    ->whereIn('cpl_mk_cpmk_penilaian_id', $penilaianIds)
                    ->select('bobot as bobot_metode', 'cpl_mk_cpmk_penilaian_id as id')
                    ->get()->collect();
            }
        }

        return view('penjamin-mutu.TPMPS.asesmen.NA_MK', array_merge(
            compact('paginatedMKs', 'penilaian', 'instrumens', 'perPage', 'search'),
            $filterData
        ));
    }


    public function NA_CPL(Request $request)
    {
        $filterData = $this->getFilterData($request);

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

        $queryInstrumens = DB::table('penilaian_metode')
            ->join('cpl_mk_cpmk_penilaian', 'penilaian_metode.cpl_mk_cpmk_penilaian_id', '=', 'cpl_mk_cpmk_penilaian.id')
            ->join('cpls', 'cpl_mk_cpmk_penilaian.cpl_id', '=', 'cpls.id')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('penilaian_metode.bobot as bobot_metode', 'penilaian_metode.cpl_mk_cpmk_penilaian_id as id');

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

        // Filtering request (Universitas, Fakultas, Prodi, Kurikulum)
        if ($request->filled('universitas_id')) {
            $queryPenilaian->where('fakultas.id_universitas', $request->universitas_id);
            $queryInstrumens->where('fakultas.id_universitas', $request->universitas_id);
        }
        if ($request->filled('fakultas_id')) {
            $queryPenilaian->where('fakultas.id', $request->fakultas_id);
            $queryInstrumens->where('fakultas.id', $request->fakultas_id);
        }
        if ($request->filled('prodi_id')) {
            $queryPenilaian->where('prodi.id', $request->prodi_id);
            $queryInstrumens->where('prodi.id', $request->prodi_id);
        }
        if ($request->filled('kurikulum_id')) {
            $queryPenilaian->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryInstrumens->where('cpls.id_kurikulum', $request->kurikulum_id);
        }

        $penilaian = $queryPenilaian->get()->collect();
        $instrumens = $queryInstrumens->get()->collect();

        return view('penjamin-mutu.TPMPS.asesmen.NA_CPL', array_merge(
            compact('penilaian', 'instrumens'),
            $filterData
        ));
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
            'instrumen' => 'required|string|in:Rubrik,Panduan Proyek Akhir',
            'blocks' => 'nullable|array',
            'metode_penilaian' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Menyimpan atau mengambil data CplMkCpmkPenilaian
            $cplMkCpmkPenilaian = CplMkCpmkPenilaian::updateOrCreate(
                [
                    'mk_kode' => $validate['mk_kode'],
                    'cpmk_id' => $validate['cpmk_id'],
                    'cpl_id' => $validate['cpl_id'],
                ],
                [
                    'tahap_penilaian' => '-',
                    'instrumen' => $validate['instrumen'],
                ]
            );

            $blocks = $request->input('blocks', []);

            // Fallback jika dikirim versi single lama
            if (empty($blocks) && !empty($request->input('metode_penilaian'))) {
                $blocks = [
                    [
                        'metode_id' => $request->input('metode_penilaian')[0] ?? null,
                        'bobot_metode' => isset($request->bobot_metode) && is_array($request->bobot_metode) ? reset($request->bobot_metode) : null,
                        'kriteria' => $request->input('kriteria_penilaian', []),
                        'bobot_kriteria' => $request->input('bobot_kriteria', []),
                    ]
                ];
            }

            foreach ($blocks as $block) {
                $metodeId = $block['metode_id'] ?? null;
                if (!$metodeId) continue;

                $metodeBobot = isset($block['bobot_metode']) && $block['bobot_metode'] !== ''
                    ? (float) $block['bobot_metode']
                    : 0;

                $pm = PenilaianMetode::create([
                    'cpl_mk_cpmk_penilaian_id' => $cplMkCpmkPenilaian->id,
                    'metode_id' => $metodeId,
                    'bobot' => $metodeBobot,
                ]);

                $kriteriaIds = $block['kriteria'] ?? [];
                $bobotKriteriaMap = $block['bobot_kriteria'] ?? [];
                $countKriteria = count($kriteriaIds);
                $fallbackBobotPerKriteria = $countKriteria > 0 ? ($metodeBobot / $countKriteria) : 0;

                foreach ($kriteriaIds as $kId) {
                    $userVal = isset($bobotKriteriaMap[$kId]) && $bobotKriteriaMap[$kId] !== ''
                        ? (float) $bobotKriteriaMap[$kId]
                        : null;

                    $finalKBobot = ($userVal !== null && $userVal > 0)
                        ? $userVal
                        : round($fallbackBobotPerKriteria, 2);

                    PenilaianInstrumen::create([
                        'cpl_mk_cpmk_penilaian_id' => $cplMkCpmkPenilaian->id,
                        'penilaian_metode_id' => $pm->id,
                        'kriteria_id' => $kId,
                        'bobot_metode' => $finalKBobot,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data asesmen berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('failed', 'Gagal menyimpan data asesmen: ' . $e->getMessage());
        }
    }

    // ====== PENGELOLAAN METODE PENILAIAN ======
    public function indexKelolaMetode(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $query = MetodePenilaian::query();

        if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('id_prodi', $user->id_prodiUser);
        }

        $metodes = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();

        // Data Matriks Pemetaan Metode Penilaian
        $queryMetodesList = DB::table('metode_penilaian')
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
            ->select('cpls.id', 'cpls.kode')
            ->orderBy('cpls.kode', 'asc');

        $queryMks = MK::join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->with('cpmks');

        $queryCpmks = CPMK::join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpmks.id', 'cpmks.kode', 'cpmks.cpl_id');

        if ($userOtoritas === 'Penjamin Mutu Universitas') {
            $queryMetodesList->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryPenilaian->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryCpls->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryMks->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryCpmks->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($userOtoritas === 'Penjamin Mutu Fakultas') {
            $queryMetodesList->where('fakultas.id', $user->id_fakultasUser);
            $queryPenilaian->where('fakultas.id', $user->id_fakultasUser);
            $queryCpls->where('fakultas.id', $user->id_fakultasUser);
            $queryMks->where('fakultas.id', $user->id_fakultasUser);
            $queryCpmks->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMetodesList->where('prodi.id', $user->id_prodiUser);
            $queryPenilaian->where('prodi.id', $user->id_prodiUser);
            $queryCpls->where('prodi.id', $user->id_prodiUser);
            $queryMks->where('prodi.id', $user->id_prodiUser);
            $queryCpmks->where('prodi.id', $user->id_prodiUser);
        }

        $metodesList = $queryMetodesList->pluck('metode_penilaian.nama', 'metode_penilaian.id');
        $penilaian = $queryPenilaian->get()->groupBy('id');
        $cpls = $queryCpls->get()->keyBy('id');
        $mks = $queryMks->get()->keyBy('kode');
        $cpmks = $queryCpmks->get()->keyBy('id');

        return view('penjamin-mutu.TPMPS.asesmen.kelola_metode_penilaian', compact(
            'metodes', 'userOtoritas', 'penilaian', 'metodesList', 'cpls', 'mks', 'cpmks'
        ));
    }

    public function updateKelolaMetode(Request $request, $id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if (!in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $metode = MetodePenilaian::where('id_prodi', $user->id_prodiUser)->findOrFail($id);

        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('metode_penilaian', 'nama')->where('id_prodi', $user->id_prodiUser)->ignore($metode->id)
            ],
        ]);

        $metode->update(['nama' => $request->nama]);

        return redirect()->back()->with('success', 'Metode Penilaian berhasil diperbarui.');
    }

    public function destroyKelolaMetode(Request $request, $id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if (!in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $metode = MetodePenilaian::where('id_prodi', $user->id_prodiUser)->findOrFail($id);

        if ($metode->penilaianMetodes()->count() > 0) {
            return redirect()->back()->with('failed', 'Metode penilaian tidak dapat dihapus karena sudah digunakan dalam asesmen.');
        }

        $metode->delete();

        return redirect()->back()->with('success', 'Metode Penilaian berhasil dihapus.');
    }

    // ====== PENGELOLAAN KRITERIA PENILAIAN ======
    public function indexKelolaKriteria(Request $request)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        $query = InstrumenPenilaian::query();

        if (in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('id_prodi', $user->id_prodiUser);
        }

        $kriterias = $query->orderBy('nama_kriteria', 'asc')->paginate(10)->withQueryString();

        return view('penjamin-mutu.TPMPS.asesmen.kelola_kriteria_penilaian', compact('kriterias', 'userOtoritas'));
    }

    public function updateKelolaKriteria(Request $request, $id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if (!in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $kriteria = InstrumenPenilaian::where('id_prodi', $user->id_prodiUser)->findOrFail($id);

        $request->validate([
            'nama_kriteria' => [
                'required',
                'string',
                'max:255',
                Rule::unique('instrumen_penilaian', 'nama_kriteria')->where('id_prodi', $user->id_prodiUser)->ignore($kriteria->id)
            ],
        ]);

        $kriteria->update(['nama_kriteria' => $request->nama_kriteria]);

        return redirect()->back()->with('success', 'Kriteria Penilaian berhasil diperbarui.');
    }

    public function destroyKelolaKriteria(Request $request, $id)
    {
        $user = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? 'Dosen';

        if (!in_array($userOtoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $kriteria = InstrumenPenilaian::where('id_prodi', $user->id_prodiUser)->findOrFail($id);

        $isUsed = PenilaianInstrumen::where('kriteria_id', $kriteria->id)->exists();
        if ($isUsed) {
            return redirect()->back()->with('failed', 'Kriteria penilaian tidak dapat dihapus karena sudah digunakan dalam asesmen.');
        }

        $kriteria->delete();

        return redirect()->back()->with('success', 'Kriteria Penilaian berhasil dihapus.');
    }
}
