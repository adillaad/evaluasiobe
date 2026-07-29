<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\BK;
use App\Models\Kurikulum;
use App\Models\MK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BKController extends Controller
{
    public function index(Request $request)
    {
        $kurikulums = Kurikulum::where('id_prodi',auth()->user()->id_prodiUser)->get();
        $query = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $query->where('kurikulum_id', $request->kurikulum_id);
        }

        $bks = $query->get()->groupBy('rumpun');

        return view('penjamin-mutu.bk.index', compact('bks','kurikulums'));
    }
    
    public function addBK()
    {
        $kurikulums = Kurikulum::where('id_prodi',auth()->user()->id_prodiUser)->get();

        return view('penjamin-mutu.bk.addBK', compact('kurikulums'));
    }

    public function storeBK(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'nama' => 'required|string',
            'rumpun' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', 'Data tidak dapat dai simpan.'.$validator->errors());
        }
        try {
           // Tambahkan id_prodiUser ke dalam data yang akan disimpan
            $data = $request->all();
            $data['id_prodi'] = auth()->user()->id_prodiUser;

            //mendapatkan kode pl, dengan menghitung banyak nya PL yang sudah ada dalam sebuah prodi
            $jumlahBk = BK::where('id_prodi',auth()->user()->id_prodiUser)->where('kurikulum_id',$request->kurikulum_id)->count();
            $kodeBKforInput = 'BK0'.$jumlahBk+1;

            $data['kode'] = $kodeBKforInput;

            BK::create($data);
            return redirect()->back()->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Data tidak dapat di simpan.');
        }
    }

    public function indexBKMK()
    {
        $queryBks = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*');

        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.*');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryBks->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryMks->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryBks->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryMks->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryBks->where('prodi.id', auth()->user()->id_prodiUser);
            $queryMks->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $bks = $queryBks->get();
        $mks =$queryMks->with('bk')->get();

        return view('penjamin-mutu.bk.pemetaan_bk_mk', compact('bks', 'mks'));
    }

    public function addBKMK()
    {
        $bks = BK::where('id_prodi', auth()->user()->id_prodiUser)
            ->select('bks.*')->get();

        return view('penjamin-mutu.bk.add_bk_mk', compact('bks'));
    }

    public function getMkByBk($bkId)
    {
        // Temukan BK yang dipilih
        $bk = BK::where('id_prodi', auth()->user()->id_prodiUser)
            ->with('cpl.mk')->findOrFail($bkId);

        // Ambil semua MK yang terkait melalui CPL
        $mkKodes = $bk->cpl->flatMap(function ($cpl) {
            return $cpl->mk->pluck('kode');
        })->unique();

        // Temukan MK berdasarkan ID yang dikumpulkan
        $mks = MK::where('id_prodi', auth()->user()->id_prodiUser)
            ->whereIn('kode', $mkKodes)->get();

        return response()->json(['mks' => $mks]);
    }

    public function storeBKMK(Request $request)
    {
        $validated = $request->validate([
            'bk_id' => 'required|exists:bks,id',
            'mk_kodes' => 'required|array',
            'mk_kodes.*' => 'exists:mks,kode',
        ]);

        $bk = BK::with('cpl.mk')->findOrFail($validated['bk_id']);
        $mkKodes = $validated['mk_kodes'];

        $validMkKodes = $bk->cpl->flatMap(function ($cpl) {
            return $cpl->mk->pluck('kode');
        })->unique();

        foreach ($mkKodes as $mkKode) {
            if (!$validMkKodes->contains($mkKode)) {
                return redirect()->back()->with('failed', 'MK yang dipilih tidak valid untuk BK yang dipilih.');
            }
        }

        $bk->mk()->syncWithoutDetaching($validated['mk_kodes']);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan.');
    }
}
