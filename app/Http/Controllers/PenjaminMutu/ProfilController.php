<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Models\CPL;
use App\Models\CPMK;
use App\Models\Kurikulum;
use App\Models\profesi;
use App\Models\ProfilLulusan;
use App\Models\Prodi;
use App\Traits\UniversityFilterTrait;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;

class ProfilController extends Controller
{
    use UniversityFilterTrait;

    public function indexListProfil(Request $request)
    {
        return view('penjamin-mutu.profil.listProfilLulusan', $this->getFilterData($request));
    }

    public function indexProfilProfesi(Request $request)
    {
        $user        = auth()->user();
        $kurikulums  = Kurikulum::where('id_prodi', $user->id_prodiUser)->get();
        $listProfesi = Profesi::forUser($user)->get();

        return view('penjamin-mutu.profil.listProfilLulusanprof', array_merge(
            $this->getFilterData($request),
            compact('kurikulums', 'listProfesi')
        ));
    }

    public function indexListProfesi(Request $request)
    {
        $profesi = Profesi::forUser(auth()->user())
            ->when($request->kurikulum_id, fn($q) => $q->where('kurikulum_id', $request->kurikulum_id))
            ->get();

        return view('penjamin-mutu.profil.listProfesi', array_merge(
            $this->getFilterData($request),
            ['profesi' => $profesi]
        ));
    }

    public function indexPemetaanCPMKProf(Request $request)
    {
        $user = auth()->user();
        $query = CPMK::forUser($user);
        if ($request->filled('kurikulum_id')) {
            $query->where('id_kurikulum', $request->kurikulum_id);
        }
        $cpmks = $query->get();
        $kurikulums = $this->kurikulumsForUser();

        return view('penjamin-mutu.profil.listProfesiCpmk', compact('cpmks', 'kurikulums'));
    }

    public function indexProfilMK(Request $request)
    {
        $raw = ProfilLulusan::queryProfilMK(auth()->user(), $request)
            ->orderBy('kurikulums.tahun', 'desc')
            ->orderBy('profil_lulusan.kode')
            ->get();

        $groupedByKurikulum = $raw->groupBy(function ($item) {
            return $item->kurikulum_tahun ? 'Kurikulum ' . $item->kurikulum_tahun : 'Tanpa Kurikulum';
        })->map(function ($itemsInKurikulum) {
            return $itemsInKurikulum->groupBy('profil_kode');
        });

        return view('penjamin-mutu.profil.profil-MK', array_merge(
            $this->getFilterData($request),
            ['groupedByKurikulum' => $groupedByKurikulum]
        ));
    }
    public function readListProfil(Request $request)
    {
        $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
        $listProfil = ProfilLulusan::filterOtoritas(auth()->user(), $request->kurikulum_id)->get();
        return view('penjamin-mutu.profil.readListProfil', compact('listProfil', 'userOtoritas'));
    }

    public function readListProfilProf(Request $request)
    {
        $user        = auth()->user();
        $userOtoritas = $user->otoritas->otoritas ?? '';
        $kurikulums = Kurikulum::where('id_prodi', $user->id_prodiUser)->get();

        $query = ProfilLulusan::query()
            ->join('kurikulums', 'profil_lulusan.kurikulum_id', '=', 'kurikulums.id')
            ->join('prodi', 'profil_lulusan.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('profil_lulusan.*');
        $otoritas = $user->otoritas->otoritas;

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $query->where('universitas.id', $user->id_universitasUser);
        } elseif ($otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', $user->id_fakultasUser);
        } else {
            $query->where('profil_lulusan.id_prodi', $user->id_prodiUser);
        }
        if ($request->filled('kurikulum_id')) {
            $query->where('profil_lulusan.kurikulum_id', $request->kurikulum_id);
        }

        $listProfil  = $query->get();
        $listProfesi = Profesi::forUser($user)
            ->when($request->filled('kurikulum_id'), fn($q) => $q->where('profesi.kurikulum_id', $request->kurikulum_id))
            ->get();

        $view = $user->prodi->is_aptikom
            ? 'penjamin-mutu.profil.readListProfilProfAptikom'
            : 'penjamin-mutu.profil.readListProfilProf';

        return view($view, compact('listProfil', 'listProfesi', 'kurikulums', 'userOtoritas'));
    }

    public function readListProfesi(Request $request)
    {
        $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
        $profesi = Profesi::with('kurikulum')
            ->forUser(auth()->user())
            ->when($request->kurikulum_id, fn($q) => $q->where('kurikulum_id', $request->kurikulum_id))
            ->get();

        return view('penjamin-mutu.profil.readListProfesi', compact('profesi', 'userOtoritas'));
    }
    public function createListProfil()
    {
        $kurikulums = $this->kurikulumsForUser();
        $view = auth()->user()->prodi->is_aptikom
            ? 'penjamin-mutu.profil.createListProfilAptikom'
            : 'penjamin-mutu.profil.createListProfil';

        return view($view, compact('kurikulums'));
    }

    public function createListProfesi(Request $request)
    {
        $kurikulums = $this->kurikulumsForUser();
        $view = $request->ajax() ? 'penjamin-mutu.profil.formProfesi' : 'penjamin-mutu.profil.createlistProfesi';

        return $request->ajax()
            ? view($view, compact('kurikulums'))->render()
            : view($view, compact('kurikulums'));
    }
    public function storeListProfil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'namaProfil'   => 'required|string|unique:profil_lulusan',
            'deskripsi'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::create(array_merge($request->all(), [
            'id_prodi' => auth()->user()->id_prodiUser,
            'kode'     => ProfilLulusan::generateKode(auth()->user()->id_prodiUser, $request->kurikulum_id),
        ]));

        return response()->json(['message' => 'Data berhasil disimpan'], 201);
    }

    public function storeListProfilAptikom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'jenis'        => 'required|string',
            'deskripsi'    => 'required|string',
            'status'       => 'required|in:wajib,pilihan',
            'acuan'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::create(array_merge($request->all(), [
            'id_prodi' => auth()->user()->id_prodiUser,
            'kode'     => ProfilLulusan::generateKode(auth()->user()->id_prodiUser, $request->kurikulum_id),
        ]));

        return response()->json(['message' => 'Data berhasil disimpan'], 201);
    }

    public function storeListProfesi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'nama'         => 'required|string|unique:profesi',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? response()->json(['errors' => $validator->errors()], 422)
                : redirect()->back()->withInput()->with('error', implode(', ', $validator->errors()->all()));
        }

        Profesi::create(array_merge($request->all(), [
            'id_prodi' => auth()->user()->id_prodiUser,
        ]));

        return $request->ajax()
            ? response()->json(['message' => 'Profesi berhasil ditambahkan'], 201)
            : redirect()->back()->with('success', 'Profesi berhasil ditambahkan!');
    }

    public function storeProfesiCPMK(Request $request)
    {
        $request->validate([
            'cpmk_id'    => 'required|exists:cpmks,id',
            'profesis'   => 'required|array|min:1',
            'profesis.*' => 'exists:profesi,id',
        ]);

        CPMK::findOrFail($request->cpmk_id)
            ->profesis()
            ->syncWithoutDetaching($request->profesis);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan');
    }
    public function showProfil($id)
    {
        $profil     = ProfilLulusan::findOrFail($id);
        $kurikulums = $this->kurikulumsForUser();
        $view = auth()->user()->prodi->is_aptikom
            ? 'penjamin-mutu.profil.editProfilAptikom'
            : 'penjamin-mutu.profil.editProfil';

        return view($view, compact('profil', 'kurikulums'));
    }

    public function showProfesi($id)
    {
        $profesi    = Profesi::findOrFail($id);
        $kurikulums = $this->kurikulumsForUser();
        return view('penjamin-mutu.profil.formProfesi', compact('profesi', 'kurikulums'));
    }

    public function addProfesiCpmk(Request $request)
    {
        $user     = auth()->user();
        $profesis = Profesi::forUser($user)->get();
        $cpmks    = CPMK::forUser($user)->get();
        $selected = $request->cpmk_id
            ? DB::table('profesi_cpmk')->where('cpmk_id', $request->cpmk_id)->pluck('profesi_id')->toArray()
            : [];

        return view('penjamin-mutu.profil.add_profesi_cpmk', compact('profesis', 'cpmks', 'selected'));
    }

    public function editProfesiCpmk($id)
    {
        $cpmk     = CPMK::with('profesis')->findOrFail($id);
        $profesis = Profesi::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $selected = $cpmk->profesis->pluck('id')->toArray();

        return view('penjamin-mutu.profil.edit_profesi_cpmk', [
            'cpmk'         => $cpmk,
            'profesis'     => $profesis,
            'selected'     => $selected,
            'userOtoritas' => auth()->user()->otoritas->otoritas,
        ]);
    }
    public function updateProfil(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'namaProfil' => 'required|string|unique:profil_lulusan,namaProfil,' . $id,
            'deskripsi'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::findOrFail($id)->update($request->only('namaProfil', 'deskripsi'));
        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function updateProfilAptikom(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kurikulum_id' => 'required',
            'jenis'        => 'required|string',
            'deskripsi'    => 'required|string',
            'status'       => 'required|in:wajib,pilihan',
            'acuan'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Gagal menyimpan data', 'validation_errors' => $validator->errors()], 422);
        }

        ProfilLulusan::findOrFail($id)->update(
            $request->only('kurikulum_id', 'jenis', 'deskripsi', 'status', 'acuan')
        );

        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    public function updateProfesi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama'         => 'required|string|unique:profesi,nama,' . $id,
            'kurikulum_id' => 'required|exists:kurikulums,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Profesi::findOrFail($id)->update($request->only('nama', 'kurikulum_id'));
        return response()->json(['message' => 'Profesi berhasil diperbarui']);
    }

    public function updateProfesiCpmk(Request $request, $id)
    {
        $request->validate([
            'profesis'   => 'required|array',
            'profesis.*' => 'exists:profesi,id',
        ]);

        CPMK::findOrFail($id)->profesis()->sync($request->profesis);
        return redirect()->back()->with('success', 'Pemetaan berhasil diupdate');
    }
    public function deleteProfil($id)
    {
        ProfilLulusan::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function deleteProfesi($id)
    {
        Profesi::findOrFail($id)->delete();
        return response()->json(['message' => 'Profesi berhasil dihapus']);
    }

    public function deleteProfesiCpmk($cpmk_id, $profesi_id)
    {
        CPMK::findOrFail($cpmk_id)->profesis()->detach($profesi_id);
        return redirect()->back()->with('success', 'Data pemetaan CPMK-Profesi berhasil dihapus');
    }
    public function evaluasiCpmk()
    {
        $user     = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $mks = DB::table('mks')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->when(
                $otoritas === 'Penjamin Mutu Universitas',
                fn($q) => $q->where('fakultas.id_universitas', $user->id_universitasUser)
            )
            ->when(
                $otoritas === 'Penjamin Mutu Fakultas',
                fn($q) => $q->where('fakultas.id', $user->id_fakultasUser)
            )
            ->when(
                in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi']),
                fn($q) => $q->where('prodi.id', $user->id_prodiUser)
            )
            ->select('mks.kode', 'mks.nama')
            ->orderBy('kode')
            ->get();

        $cplMk = DB::table('mk_cpl')
            ->join('cpls', 'mk_cpl.cpl_id', '=', 'cpls.id')
            ->whereIn('mk_kode', $mks->pluck('kode'))
            ->select('mk_kode', 'cpls.kode as cpl_kode')
            ->get()
            ->groupBy('mk_kode');

        $cpmkRelations = DB::table('cpmks')
            ->join('cpl_mk_cpmk_penilaian', 'cpmks.id', '=', 'cpl_mk_cpmk_penilaian.cpmk_id')
            ->whereIn('cpl_mk_cpmk_penilaian.mk_kode', $mks->pluck('kode'))
            ->select('cpl_mk_cpmk_penilaian.mk_kode', 'cpmks.kode as cpmk_kode', 'cpmks.cpl_id')
            ->get()
            ->groupBy('mk_kode');

        $mahasiswas = DB::table('mutus')
            ->where('id_prodi', $user->id_prodiUser)
            ->select('NPM', 'nama_mhs')
            ->distinct()
            ->orderBy('nama_mhs')
            ->get();

        $nilaiCpmk = DB::table('mutus')
            ->where('id_prodi', $user->id_prodiUser)
            ->select('NPM', 'cpmk', DB::raw('ROUND(SUM(examWeight / 100 * Nilai), 2) as nilai'))
            ->groupBy('NPM', 'cpmk')
            ->get()
            ->groupBy('NPM');

        $nilaiMkMahasiswa = [];
        foreach ($mahasiswas as $mhs) {
            foreach ($mks as $mk) {
                $total = 0;
                foreach ($cpmkRelations[$mk->kode] ?? collect() as $cpmkItem) {
                    $record = $nilaiCpmk[$mhs->NPM]?->firstWhere('cpmk', $cpmkItem->cpmk_kode);
                    $total += $record->nilai ?? 0;
                }
                $nilaiMkMahasiswa[$mhs->NPM][$mk->kode] = round($total, 2);
            }
        }

        return view('penjamin-mutu.penilaian-evaluasi-cpmk', compact(
            'mahasiswas',
            'mks',
            'cplMk',
            'cpmkRelations',
            'nilaiCpmk',
            'nilaiMkMahasiswa'
        ));
    }
    private function kurikulumsForUser()
    {
        return Kurikulum::where('id_prodi', auth()->user()->id_prodiUser)->get();
    }
}
