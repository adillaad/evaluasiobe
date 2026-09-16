<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\BK;
use App\Models\Kurikulum;
use App\Models\MK;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BKController extends Controller
{
    public function index(Request $request)
    {
        $idProdi = auth()->user()->id_prodiUser;
        $kurikulums = Kurikulum::where('id_prodi', $idProdi)->get();

        $query = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*')
            ->with('kurikulum');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', $idProdi);
        }

        if ($request->filled('kurikulum_id')) {
            $query->where('kurikulum_id', $request->kurikulum_id);
        }

        $bks = $query->get()->groupBy('rumpun');

        $existingRumpuns = BK::where('id_prodi', $idProdi)
            ->whereNotNull('rumpun')
            ->where('rumpun', '!=', '')
            ->distinct()
            ->pluck('rumpun');

        return view('penjamin-mutu.bk.index', compact('bks', 'kurikulums', 'existingRumpuns'));
    }
    
    public function addBK()
    {
        $kurikulums = Kurikulum::where('id_prodi', auth()->user()->id_prodiUser)->get();

        return view('penjamin-mutu.bk.addBK', compact('kurikulums'));
    }

    public function storeBK(Request $request)
    {
        $rumpunValue = $request->input('rumpun_select') === '__NEW__'
            ? trim($request->input('rumpun_new'))
            : trim($request->input('rumpun_select') ?: $request->input('rumpun'));

        $request->merge(['rumpun' => $rumpunValue]);

        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'nama' => 'required|string',
            'rumpun' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Gagal menyimpan: ' . $validator->errors()->first());
        }
        try {
            $idProdi = auth()->user()->id_prodiUser;
            $jumlahBk = BK::where('id_prodi', $idProdi)->where('kurikulum_id', $request->kurikulum_id)->count();
            $kodeBKforInput = 'BK0' . ($jumlahBk + 1);

            BK::create([
                'id_prodi' => $idProdi,
                'kurikulum_id' => $request->kurikulum_id,
                'nama' => $request->nama,
                'rumpun' => $rumpunValue,
                'kode' => $kodeBKforInput,
            ]);
            return redirect()->back()->with('success', 'Data Bahan Kajian berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('failed', 'Gagal menyimpan data Bahan Kajian.');
        }
    }

    public function updateBK(Request $request, $id)
    {
        $rumpunValue = $request->input('rumpun_select') === '__NEW__'
            ? trim($request->input('rumpun_new'))
            : trim($request->input('rumpun_select') ?: $request->input('rumpun'));

        if ($rumpunValue) {
            $request->merge(['rumpun' => $rumpunValue]);
        }

        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'nama' => 'required|string',
            'rumpun' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('failed', 'Gagal memperbarui: ' . $validator->errors()->first());
        }

        try {
            $idProdi = auth()->user()->id_prodiUser;
            $bk = BK::where('id_prodi', $idProdi)->findOrFail($id);

            $bk->update([
                'nama' => $request->nama,
                'kurikulum_id' => $request->kurikulum_id,
                'rumpun' => $rumpunValue ?: $bk->rumpun,
            ]);

            return redirect()->back()->with('success', 'Bahan Kajian berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('failed', 'Gagal memperbarui Bahan Kajian.');
        }
    }

    public function destroyBK($id)
    {
        try {
            $idProdi = auth()->user()->id_prodiUser;
            $bk = BK::where('id_prodi', $idProdi)->findOrFail($id);
            $bk->delete();

            return redirect()->back()->with('success', 'Bahan Kajian berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Gagal menghapus Bahan Kajian.');
        }
    }

    public function indexBKMK(Request $request)
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

        if ($request->filled('kurikulum_id')) {
            $queryBks->where('bks.kurikulum_id', $request->kurikulum_id);
            $queryMks->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $bks = $queryBks->with('kurikulum')->get();
        $mks = $queryMks->with(['bk', 'kurikulum'])->get();

        $user = auth()->user();
        $queryKur = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('kurikulums.*', 'prodi.nama as nama_prodi')
            ->orderBy('kurikulums.tahun', 'desc');

        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryKur->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryKur->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $queryKur->where('prodi.id', $user->id_prodiUser);
        }
        $kurikulums = $queryKur->get();

        return view('penjamin-mutu.bk.pemetaan_bk_mk', compact('bks', 'mks', 'kurikulums'));
    }

    public function updateMatrixBKMK(Request $request)
    {
        $matrix = $request->input('matrix', []); // Key: mk_kode, Value: array of bk_ids
        $kurikulumId = $request->input('kurikulum_id');

        $queryMks = MK::query();
        if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMks->where('id_prodi', auth()->user()->id_prodiUser);
        }
        if ($kurikulumId) {
            $queryMks->where('id_kurikulum', $kurikulumId);
        }
        $allMks = $queryMks->get();

        DB::transaction(function () use ($allMks, $matrix) {
            foreach ($allMks as $mk) {
                $selectedBkIds = isset($matrix[$mk->kode]) ? (array) $matrix[$mk->kode] : [];
                $mk->bk()->sync($selectedBkIds);
            }
        });

        return redirect()->back()->with('success', 'Matriks pemetaan BK - MK berhasil diperbarui.');
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
