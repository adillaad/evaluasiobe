<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\BK;
use App\Models\CPL;
use App\Models\Kurikulum;
use App\Models\MK;
use App\Models\ProfilCpl;
use App\Models\ProfilLulusan;
use App\Traits\UniversityFilterTrait;
use Crypt;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CPLController extends Controller
{
    use UniversityFilterTrait;
    
    public function index(Request $request)
    {
        $query = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul', 'cpls.id_prodi');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('cpls.id_prodi', auth()->user()->id_prodiUser);
        }

        $query = $this->getFilteredQuery($query, $request);
        $cpls = $query->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('penjamin-mutu.cpl.index', array_merge(
            ['cpls' => $cpls],
            $filterData
        ));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Kaprodi hanya butuh data kurikulum dari prodinya sendiri.
        $kurikulums = Kurikulum::where('id_prodi', $user->id_prodiUser)
                            ->orderBy('tahun', 'asc')
                            ->get();
        
        $prodi = $user->prodi; // Ambil data prodi dari relasi

        return view('penjamin-mutu.cpl.add', compact('kurikulums', 'prodi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kurikulum.*' => 'required|integer',
            'aspek.*' => 'required|in:Sikap,Keterampilan Umum,Keterampilan Khusus,Pengetahuan,Pengetahuan & Keterampilan,Pengetahuan Interdisipliner,Keterampilan Umum & Khusus,Lainnya',
            'kode.*' => 'required|string|max:255',
            'judul.*' => 'required|string',
        ]);

        $id_prodi_user = auth()->user()->id_prodiUser;

        try {
            foreach ($request->kode as $key => $value) {
                // Keamanan: Paksa id_prodi dari user yang login, bukan dari inputan.
                CPL::create([
                    'aspek' => $request->aspek[$key],
                    'id_kurikulum' => $request->id_kurikulum[$key],
                    'kode' => 'CPL' . $value,
                    'nomor' => $value,
                    'judul' => $request->judul[$key],
                    'id_prodi' => $id_prodi_user,
                ]);
            }
            
            return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL berhasil ditambahkan!');

        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nomor CPL yang diinputkan ada yang duplikat untuk Prodi dan Kurikulum ini.');
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $ids = Crypt::decrypt($id);
        $cpl = CPL::findOrFail($ids);
        $user = auth()->user();

        // Keamanan: Pastikan CPL yang akan diedit adalah milik prodi user.
        if ($cpl->id_prodi !== $user->id_prodiUser) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit CPL ini.');
        }

        $kurikulums = Kurikulum::where('id_prodi', $user->id_prodiUser)
                            ->orderBy('tahun', 'desc')
                            ->get();

        return view('penjamin-mutu.cpl.edit', compact('cpl', 'kurikulums'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kurikulum' => 'required|integer',
            'aspek.*' => 'required|in:Sikap,Keterampilan Umum,Keterampilan Khusus,Pengetahuan,Pengetahuan & Keterampilan,Pengetahuan Interdisipliner,Keterampilan Umum & Khusus,Lainnya',
            'nomor' => 'required|string|max:255',
            'judul' => 'required|string',
        ]);
        
        $ids = Crypt::decrypt($id);
        $cpl = CPL::findOrFail($ids);
        $user = auth()->user();

        // Keamanan: Pastikan CPL yang akan diupdate adalah milik prodi user.
        if ($cpl->id_prodi !== $user->id_prodiUser) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate CPL ini.');
        }

        try {
            $cpl->update([
                'aspek' => $request->aspek,
                'id_kurikulum' => $request->id_kurikulum,
                'kode' => 'CPL' . $request->nomor,
                'nomor' => $request->nomor,
                'judul' => $request->judul,
            ]);
    
            return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL berhasil diubah!');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Nomor yang diinputkan sudah ada untuk Prodi ini.');
            }
            throw $e;
        }
    }

    public function delete($id)
    {
        $ids = Crypt::decrypt($id);
        $cpl = CPL::findOrFail($ids);

        // Keamanan: Pastikan CPL yang akan dihapus adalah milik prodi user.
        if ($cpl->id_prodi !== auth()->user()->id_prodiUser) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus CPL ini.');
        }

        $cpl->delete();
        return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL berhasil dihapus!');
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.cpl.index',
            'Kepala Program Studi' => 'kepala-program-studi.cpl.index',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }

    public function indexCPLPL()
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul')
            ->with('profilLulusan');

        $queryProfilLulusans = ProfilLulusan::query()
            ->join('prodi', 'profil_lulusan.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('profil_lulusan.*');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryProfilLulusans->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryProfilLulusans->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', auth()->user()->id_prodiUser);
            $queryProfilLulusans->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $cpls = $queryCpl->get();
        $profilLulusans = $queryProfilLulusans->get();

        return view('penjamin-mutu.cpl.pemetaan_cpl_pl', compact('cpls', 'profilLulusans'));
    }

    public function indexCPLBK()
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul');

        $queryBks = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->with('cpl')->select('bks.*');

        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryBks->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } else if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryBks->where('fakultas.id', auth()->user()->id_fakultasUser);
        } else if (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', auth()->user()->id_prodiUser);
            $queryBks->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $cpls = $queryCpl->get();
        $bks = $queryBks->get();

        return view('penjamin-mutu.cpl.pemetaan_cpl_bk', compact('cpls', 'bks'));
    }

    public function indexCPLMK()
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul');

        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')->with('cpl')
            ->select('mks.*');

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


        return view('penjamin-mutu.cpl.pemetaan_cpl_mk', compact('cpls', 'mks'));
    }

    public function indexCPLBKMK()
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul');

        // Query BK dengan relasi ke MK dan CPL
        $queryBk = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*')
            ->with(['mk.cpl', 'cpl']);

        // Filter berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryBk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryBk->where('prodi.id_fakultas', auth()->user()->id_fakultasUser);
        } elseif (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', auth()->user()->id_prodiUser);
            $queryBk->where('prodi.id', auth()->user()->id_prodiUser);
        }

        // Eksekusi query
        $cpls = $queryCpl->get();
        $bks = $queryBk->get();

        return view('penjamin-mutu.cpl.pemetaan_cpl_bk_mk', compact('cpls', 'bks'));
    }


    public function addCPLPL()
    {
        $idProdi = auth()->user()->id_prodiUser;

        $profilLulusans = ProfilLulusan::query()
            ->join('prodi', 'profil_lulusan.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('profil_lulusan.id_prodi', $idProdi)
            ->select('profil_lulusan.*')
            ->get();

        $cpls = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('cpls.id_prodi', $idProdi)
            ->select('cpls.*')
            ->get();

        return view('penjamin-mutu.cpl.add_cpl_pl', compact('profilLulusans', 'cpls'));
    }

    public function storeCPLPL(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profil_lulusan_id' => 'required|exists:profil_lulusan,id',
            'cpl_ids' => 'required|array|min:1',
            'cpl_ids.*' => 'exists:cpls,id',
            'bobot' => 'required|array',
            'bobot.*' => 'nullable|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->cpl_ids ?? [] as $cplId) {
                $bobot = $request->bobot[$cplId] ?? null;
                if ($bobot === null || $bobot === '') {
                    $validator->errors()->add('bobot.' . $cplId, 'Bobot wajib diisi untuk setiap CPL yang dipilih.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('failed', 'Gagal menyimpan data: ' . $validator->errors()->first());
        }

        try {
            $idProdi = auth()->user()->id_prodiUser;
            $savedCount = 0;

            foreach ($request->cpl_ids as $cplId) {
                $created = ProfilCpl::updateOrCreate(
                    [
                        'idProfil' => $request->profil_lulusan_id,
                        'idCpl' => $cplId,
                        'id_prodi' => $idProdi,
                    ],
                    [
                        'bobot' => $request->bobot[$cplId],
                    ]
                );

                if ($created->wasRecentlyCreated || $created->wasChanged()) {
                    $savedCount++;
                }
            }

            if ($savedCount === 0) {
                return redirect()
                    ->route($this->currentPrefix() . 'cpl.cpl-pl')
                    ->with('failed', 'Semua pemetaan yang dipilih sudah ada sebelumnya.');
            }

            return redirect()
                ->route($this->currentPrefix() . 'cpl.cpl-pl')
                ->with('success', $savedCount . ' pemetaan CPL-PL berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('failed', 'Gagal menyimpan data. Silakan coba lagi.');
        }
    }

    public function addCPLBK()
    {
        $bks = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->select('bks.*')->get();
        $cpls = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('prodi.id', auth()->user()->id_prodiUser)
            ->select('cpls.*')->get();

        return view('penjamin-mutu.cpl.add_cpl_bk', compact('bks', 'cpls'));
    }
    public function storeCPLBK(Request $request)
    {
        $validated = $request->validate([
            'cpl_id' => 'required|exists:cpls,id',
            'bk_ids' => 'required|array',
            'bk_ids.*' => 'exists:bks,id',
        ]);

        $cpl = CPL::findOrFail($validated['cpl_id']);
        $cpl->bk()->syncWithoutDetaching($validated['bk_ids']);

        return redirect()->back()->with('success', 'Pemetaan berhasil disimpan.');
    }

    public function addCPLMK()
    {
        $mks = MK::query()
        ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
        ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
        ->where('prodi.id', auth()->user()->id_prodiUser)
        ->select('mks.*')->get();
        $cpls = CPL::query()
        ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
        ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
        ->where('prodi.id', auth()->user()->id_prodiUser)
        ->select('cpls.*')->get();

        return view('penjamin-mutu.cpl.add_cpl_mk', compact('mks', 'cpls'));
    }

    private function currentPrefix(): string
    {
        $otoritas = auth()->user()->otoritas->otoritas;

        return match ($otoritas) {
            'Penjamin Mutu Universitas'   => 'penjamin-mutu.universitas.',
            'Penjamin Mutu Fakultas'      => 'penjamin-mutu.fakultas.',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
            'Kepala Program Studi'        => 'kepala-program-studi.',
            default                       => 'penjamin-mutu.program-studi.',
        };
    }

    public function storeCPLMK(Request $request)
    {
        $validated = $request->validate([
            'cpl_id' => 'required|exists:cpls,id',
            'mk_kodes' => 'required|array',
            'mk_kodes.*' => 'exists:mks,kode',
        ]);

        $id_prodi = auth()->user()->id_prodiUser;
        $cpl = CPL::findOrFail($validated['cpl_id']);

        $existingKodes = $cpl->mk()
            ->whereIn('mks.kode', $validated['mk_kodes'])
            ->pluck('mks.kode')
            ->all();

        $newKodes = array_values(array_diff($validated['mk_kodes'], $existingKodes));

        $syncData = [];
        foreach ($validated['mk_kodes'] as $kode) {
            $syncData[$kode] = ['id_prodi' => $id_prodi];
        }

        $cpl->mk()->syncWithoutDetaching($syncData);

        if (empty($newKodes)) {
            return redirect()
                ->route($this->currentPrefix() . 'cpl.cpl-mk')
                ->with('failed', 'Semua pemetaan yang dipilih sudah ada sebelumnya.');
        }

        return redirect()
            ->route($this->currentPrefix() . 'cpl.cpl-mk')
            ->with('success', count($newKodes) . ' pemetaan CPL-MK baru berhasil disimpan.');
    }
}