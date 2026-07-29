<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\Kurikulum;
use App\Models\MK;
use App\Models\SubCpmk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CPLCPMKController extends Controller
{
    public function indexCPLCPMKMK()
    {
        $query = CPL::with(['cpmk.mks', 'mk'])
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.*');

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $cpls = $query->get();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_cpl_cpmk_mk', compact('cpls'));
    }

    public function indexCPLCPMKMKSMT()
    {
        $query = CPL::with(['cpmk.mks', 'mk'])
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $cpls = $query->select('cpls.*')->get();
        $semesters = MK::select('semester')->whereNotNull('semester')->where('semester', '!=', 0)->distinct()->orderBy('semester')->get();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_cpl_cpmk_mk_semester', compact('semesters', 'cpls'));
    }

    public function indexCPLMKCPMK()
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.*');

        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.*')
            ->with(['cpmks.cpl', 'cpl']);

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryMks->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryMks->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', auth()->user()->id_prodiUser);
            $queryMks->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $cpls = $queryCpl->get();
        $mks = $queryMks->get();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_cpl_mk_cpmk', compact('cpls', 'mks'));
    }

    public function indexMKCPMKSubCPMK()
    {
        $kurikulums = Kurikulum::where('id_prodi',auth()->user()->id_prodiUser)->get();

        $query = MK::with(['cpmks.subCpmks', 'cpmks.cpl', 'cpl', 'sub_cpmk'])
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->whereHas('cpl', function ($query) {
                // Hanya MK yang memiliki hubungan CPL di mk_cpl
            })
            ->with([
                'cpmks' => function ($query) {
                    $query->whereHas('cpl.mk'); // pastikan CPL terhubung ke MK
                },
                'cpmks.subCpmks' => function ($query) {
                    $query->whereHas('mks'); // SubCPMK harus ada di hubungan mk_sub_cpmk
                },
            ]);

        // Filtering berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $mks = $query->select('mks.*')->get();

        return view('penjamin-mutu.cpl-cpmk.pemetaan_mk_cpmk_subcpmk', compact('mks','kurikulums'));
    }

    public function addCPLCPMKMK()
    {
        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->orderBy('mks.nama', 'asc')
            ->select('mks.*')->get();

        return view('penjamin-mutu.cpl-cpmk.add_cpl_cpmk_mk', compact('mks'));
    }

    public function getCPMKByMK($mkId)
    {
        // Temukan BK yang dipilih
        $mk = MK::findOrFail($mkId);

        // Ambil semua MK yang terkait melalui CPL
        $cplIds = $mk->cpl->pluck('id'); // Semua CPL yang terkait dengan MK ini
        $cpmks = CPMK::where('id_prodi',auth()->user()->id_prodiUser)
            ->whereIn('cpl_id', $cplIds)
            ->orderBy('kode', 'asc')
            ->get(); // CPMK yang terkait dengan CPL tersebut

        return response()->json(['cpmks' => $cpmks]);
    }

    public function storeCPLCPMKMK(Request $request)
    {

        $validated = $request->validate([
            'mk_kode' => 'required|exists:mks,kode',
            'cpmk_ids' => 'required|array',
            'cpmk_ids.*' => 'exists:cpmks,id',
        ]);

        $mk = MK::findOrFail($validated['mk_kode']);

        $mk->cpmks()->syncWithoutDetaching($validated['cpmk_ids']);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan.');
    }

    public function addCPMKMKSUBCPMK()
    {
        $mks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->select('mks.*')->get();
        return view('penjamin-mutu.cpl-cpmk.add_cpmk_mk_subcpmk', compact('mks'));
    }

    public function getSUBCPMKByMK($mkId)
    {
        $mk = MK::findOrFail($mkId);

        $cpmkIds = $mk->cpmks->pluck('id');
        $subCpmks = SubCpmk::whereIn('cpmk_id', $cpmkIds)
            ->whereHas('cpmk', function ($query) use ($mkId) {
                $query->whereHas('cpl', function ($query) use ($mkId) {
                    $query->whereHas('mk', function ($query) use ($mkId) {
                        $query->where('mk_kode', $mkId);
                    });
                });
            })
            ->get();
        return response()->json(['subcpmks' => $subCpmks]);
    }

    public function storeCPMKMKSUBCPMK(Request $request)
{
    try {
        $validated = $request->validate([
            'mk_kode' => 'required|exists:mks,kode',
            'sub_cpmk_ids' => 'required|array',
            'sub_cpmk_ids.*' => 'exists:sub_cpmk,id',
        ]);

        $mk = MK::findOrFail($validated['mk_kode']);
        $mk->sub_cpmk()->syncWithoutDetaching($validated['sub_cpmk_ids']);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->validator);
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('failed', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function addSubCpmk()
{
    $kurikulums = Kurikulum::where('id_prodi',auth()->user()->id_prodiUser)->get();

    return view('penjamin-mutu.bk.addSubCpmk', compact('kurikulums'));
}

public function getCpmkByKurikulum($id_kurikulum)
{
    $cpmks = Cpmk::whereHas('cpl', function ($query) use ($id_kurikulum) {
        $query->where('id_kurikulum', $id_kurikulum);
        $query->where('id_prodi', auth()->user()->id_prodiUser);
    })->with('cpl')->get();

    return response()->json([
        'cpmks' => $cpmks,
        'kurikulum_id' => $id_kurikulum,
        'prodi_id' => auth()->user()->id_prodiUser
    ]);
}

public function storeSubCpmk(Request $request)
{
    // Validasi
    // $validator = Validator::make($request->all(), [
    //     'kurikulum_id' => 'required',
    //     'cpmk_id' => 'required|string',
    //     'uraian' => 'required|string',
    //     'cpmk_kode'=> ' required',
    // ]);

    // if ($validator->fails()) {
    //     return redirect()->back()->with('failed', 'Data tidak dapat di simpan.'.$validator->errors());
    // }
    // try {
    //     $data = $request->all();
        
    //     $jumlahSubCpmk = SubCpmk::where('cpmk_id',$request->cpmk_id)->count();
    //     $kodeSubCpmkforInput = 'Sub-'.$request->cpmk_kode.$jumlahSubCpmk+1;

    //     $data['kode'] = $kodeSubCpmkforInput;

    //     SubCpmk::create($data);
    //     return redirect()->back()->with('success', 'Data berhasil disimpan.');
    // } catch (\Exception $e) {
    //     return redirect()->back()->with('failed', 'Data tidak dapat di simpan.');
    // }
    $validated = $request->validate([
        'cpmk_id' => 'required|integer|exists:cpmks,id',
        'uraian' => 'required|string',
        // kurikulum_id dan cpmk_kode tidak perlu divalidasi karena tidak disimpan langsung
    ]);

    try {
        $id_prodi = auth()->user()->id_prodiUser;
        $parentCpmk = CPMK::findOrFail($validated['cpmk_id']);
        $subCpmkCount = SubCpmk::where('cpmk_id', $validated['cpmk_id'])->count();
        $newKode = 'Sub-' . $parentCpmk->kode . ($subCpmkCount + 1);

        // 2. Siapkan data yang bersih untuk disimpan
        $dataToCreate = [
            'kode' => $newKode,
            'uraian' => $validated['uraian'],
            'cpmk_id' => $validated['cpmk_id'],
            'id_prodi' => $id_prodi, // Menyertakan ID Prodi
        ];

        SubCpmk::create($dataToCreate);
        return redirect()->back()->with('success', 'Data Sub CPMK berhasil disimpan.');

    } catch (\Exception $e) {
        Log::error('Error saat menyimpan Sub CPMK baru: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('failed', 'Data tidak dapat disimpan karena terjadi kesalahan.');
    }
}

}
