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
use Illuminate\Support\Facades\DB;
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
            'id_kurikulum' => 'required|array',
            'id_kurikulum.*' => 'required|integer',
            'aspek' => 'required|array',
            'aspek.*' => 'required|in:Sikap,Keterampilan Umum,Keterampilan Khusus,Pengetahuan,Pengetahuan & Keterampilan,Pengetahuan Interdisipliner,Keterampilan Umum & Khusus,Lainnya',
            'kode' => 'required|array',
            'kode.*' => 'required|string|max:255',
            'judul' => 'required|array',
            'judul.*' => 'required|string',
        ]);

        $id_prodi_user = auth()->user()->id_prodiUser;

        try {
            $kodes = $request->input('kode', []);
            $aspeks = $request->input('aspek', []);
            $idKurikulums = $request->input('id_kurikulum', []);
            $juduls = $request->input('judul', []);

            // 1. Cek duplikat di dalam inputan form itu sendiri
            $seenCombinations = [];
            foreach ($kodes as $key => $value) {
                $trimmedValue = trim($value);
                $kurikulumVal = $idKurikulums[$key] ?? null;
                $combinationKey = $kurikulumVal . '-' . strtolower($trimmedValue);

                if (in_array($combinationKey, $seenCombinations)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Terdapat nomor CPL yang sama (' . $trimmedValue . ') di dalam form input untuk kurikulum yang sama.');
                }
                $seenCombinations[] = $combinationKey;
            }

            // 2. Simpan setiap CPL
            foreach ($kodes as $key => $value) {
                $aspekVal = $aspeks[$key] ?? null;
                $kurikulumVal = $idKurikulums[$key] ?? null;
                $judulVal = $juduls[$key] ?? null;
                $trimmedValue = trim($value);

                if (!$aspekVal || !$kurikulumVal || !$judulVal || $trimmedValue === '') {
                    continue;
                }

                // Cek apakah kode/nomor CPL ini sudah ada di database untuk Kurikulum + Prodi ini
                $formattedKode = 'CPL' . $trimmedValue;
                $existsInDb = CPL::where('id_prodi', $id_prodi_user)
                    ->where('id_kurikulum', $kurikulumVal)
                    ->where(function($q) use ($formattedKode, $trimmedValue) {
                        $q->where('kode', $formattedKode)
                          ->orWhere('nomor', $trimmedValue);
                    })->exists();

                if ($existsInDb) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Nomor CPL "' . $trimmedValue . '" sudah terdaftar sebelumnya di database untuk Kurikulum dan Prodi ini.');
                }

                CPL::create([
                    'aspek' => $aspekVal,
                    'id_kurikulum' => $kurikulumVal,
                    'kode' => $formattedKode,
                    'nomor' => $trimmedValue,
                    'judul' => $judulVal,
                    'id_prodi' => $id_prodi_user,
                ]);
            }
            
            return redirect()->route($this->getRouteByAuthority())->with('success', 'CPL berhasil ditambahkan!');

        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1062')) {
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

    private function getKurikulumsForUser()
    {
        $user = auth()->user();
        $query = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('kurikulums.*', 'prodi.nama as nama_prodi')
            ->orderBy('kurikulums.tahun', 'desc');

        if ($user->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $query->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($user->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $query->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($user->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $query->where('prodi.id', $user->id_prodiUser);
        }

        return $query->get();
    }

    public function indexCPLPL(Request $request)
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul', 'cpls.id_kurikulum')
            ->with(['profilLulusan', 'kurikulum']);

        $queryProfilLulusans = ProfilLulusan::query()
            ->join('prodi', 'profil_lulusan.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('profil_lulusan.*')
            ->with('kurikulum');

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

        if ($request->filled('kurikulum_id')) {
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryProfilLulusans->where('profil_lulusan.kurikulum_id', $request->kurikulum_id);
        }

        $cpls = $queryCpl->get();
        $profilLulusans = $queryProfilLulusans->get();
        $kurikulums = $this->getKurikulumsForUser();

        return view('penjamin-mutu.cpl.pemetaan_cpl_pl', compact('cpls', 'profilLulusans', 'kurikulums'));
    }

    public function indexCPLBK(Request $request)
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul', 'cpls.id_kurikulum')
            ->with('kurikulum');

        $queryBks = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->with(['cpl', 'kurikulum'])->select('bks.*');

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

        if ($request->filled('kurikulum_id')) {
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryBks->where('bks.kurikulum_id', $request->kurikulum_id);
        }

        $cpls = $queryCpl->get();
        $bks = $queryBks->get();
        $kurikulums = $this->getKurikulumsForUser();

        return view('penjamin-mutu.cpl.pemetaan_cpl_bk', compact('cpls', 'bks', 'kurikulums'));
    }

    public function indexCPLMK(Request $request)
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul', 'cpls.id_kurikulum')
            ->with('kurikulum');

        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')->with(['cpl', 'kurikulum'])
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

        if ($request->filled('kurikulum_id')) {
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryMks->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $cpls = $queryCpl->get();
        $mks = $queryMks->get();
        $kurikulums = $this->getKurikulumsForUser();

        return view('penjamin-mutu.cpl.pemetaan_cpl_mk', compact('cpls', 'mks', 'kurikulums'));
    }

    public function indexCPLBKMK(Request $request)
    {
        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul', 'cpls.id_kurikulum')
            ->with('kurikulum');

        // Query BK dengan relasi ke MK dan CPL
        $queryBk = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*')
            ->with(['mk.cpl', 'cpl', 'kurikulum']);

        $queryMk = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.*')
            ->with(['cpl', 'kurikulum']);

        // Filter berdasarkan otoritas pengguna
        if (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryBk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $queryMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (auth()->user()->otoritas->otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', auth()->user()->id_fakultasUser);
            $queryBk->where('prodi.id_fakultas', auth()->user()->id_fakultasUser);
            $queryMk->where('prodi.id_fakultas', auth()->user()->id_fakultasUser);
        } elseif (in_array(auth()->user()->otoritas->otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', auth()->user()->id_prodiUser);
            $queryBk->where('prodi.id', auth()->user()->id_prodiUser);
            $queryMk->where('prodi.id', auth()->user()->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryBk->where('bks.kurikulum_id', $request->kurikulum_id);
            $queryMk->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        // Eksekusi query
        $cpls = $queryCpl->get();
        $bks = $queryBk->get();
        $mks = $queryMk->get();
        $kurikulums = $this->getKurikulumsForUser();

        return view('penjamin-mutu.cpl.pemetaan_cpl_bk_mk', compact('cpls', 'bks', 'mks', 'kurikulums'));
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
        $idProdi = auth()->user()->id_prodiUser;

        $mksQuery = MK::query();
        $cplsQuery = CPL::query();

        if ($idProdi) {
            $mksQuery->where('id_prodi', $idProdi);
            $cplsQuery->where('id_prodi', $idProdi);
        }

        $mks = $mksQuery->get();
        if ($mks->isEmpty()) {
            $mks = MK::all();
        }

        $cpls = $cplsQuery->get();
        if ($cpls->isEmpty()) {
            $cpls = CPL::all();
        }

        return view('penjamin-mutu.cpl.add_cpl_mk', compact('mks', 'cpls'));
    }

    public function getCplsByKurikulum($kurikulumId)
    {
        $user = auth()->user();
        $kurikulum = Kurikulum::find($kurikulumId);
        $idProdi = $kurikulum ? $kurikulum->id_prodi : ($user ? $user->id_prodiUser : null);

        // Fetch CPLs
        $cplQuery = CPL::where('id_kurikulum', $kurikulumId);
        if ($idProdi) {
            $cpls = (clone $cplQuery)->where('id_prodi', $idProdi)->select('id', 'kode', 'judul')->get();
            if ($cpls->isEmpty()) {
                $cpls = $cplQuery->select('id', 'kode', 'judul')->get();
            }
        } else {
            $cpls = $cplQuery->select('id', 'kode', 'judul')->get();
        }

        // Fetch MKs - check both id_kurikulum and kurikulum columns, select kode & nama
        $mkQuery = MK::where(function($q) use ($kurikulumId) {
            $q->where('id_kurikulum', $kurikulumId)
              ->orWhere('kurikulum', $kurikulumId);
        });

        if ($idProdi) {
            $mks = (clone $mkQuery)->where('id_prodi', $idProdi)->select('kode', 'nama')->get();
            if ($mks->isEmpty()) {
                $mks = $mkQuery->select('kode', 'nama')->get();
            }
        } else {
            $mks = $mkQuery->select('kode', 'nama')->get();
        }

        return response()->json([
            'cpls' => $cpls,
            'mks' => $mks,
        ]);
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

    public function updateMatrixCPLMK(Request $request)
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $queryMks = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.*');

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $queryMks->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($otoritas === 'Penjamin Mutu Fakultas') {
            $queryMks->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryMks->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $queryMks->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $mks = $queryMks->get();
        $matrix = $request->input('matrix', []);

        DB::transaction(function () use ($mks, $matrix) {
            foreach ($mks as $mk) {
                $selectedCplIds = isset($matrix[$mk->kode]) ? array_map('intval', (array)$matrix[$mk->kode]) : [];
                
                $syncData = [];
                foreach ($selectedCplIds as $cplId) {
                    $syncData[$cplId] = ['id_prodi' => $mk->id_prodi];
                }
                $mk->cpl()->sync($syncData);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Matriks Pemetaan CPL - MK berhasil diperbarui!'
            ]);
        }

        return redirect()->back()->with('success', 'Matriks Pemetaan CPL - MK berhasil diperbarui!');
    }

    public function updateMatrixCPLPL(Request $request)
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.*');

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $queryCpl->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($otoritas === 'Penjamin Mutu Fakultas') {
            $queryCpl->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryCpl->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
        }

        $cpls = $queryCpl->get();
        $matrix = $request->input('matrix', []);

        DB::transaction(function () use ($cpls, $matrix) {
            foreach ($cpls as $cpl) {
                $selectedProfilIds = isset($matrix[$cpl->id]) ? array_map('intval', (array)$matrix[$cpl->id]) : [];

                $existingBobotMap = ProfilCpl::where('idCpl', $cpl->id)->pluck('bobot', 'idProfil')->toArray();

                $syncData = [];
                foreach ($selectedProfilIds as $profilId) {
                    $syncData[$profilId] = [
                        'id_prodi' => $cpl->id_prodi,
                        'bobot' => $existingBobotMap[$profilId] ?? 1.0,
                    ];
                }

                $cpl->profilLulusan()->sync($syncData);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Matriks Pemetaan CPL - PL berhasil diperbarui!'
            ]);
        }

        return redirect()->back()->with('success', 'Matriks Pemetaan CPL - PL berhasil diperbarui!');
    }

    public function updateMatrixCPLBK(Request $request)
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $queryBks = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*');

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $queryBks->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($otoritas === 'Penjamin Mutu Fakultas') {
            $queryBks->where('fakultas.id', $user->id_fakultasUser);
        } else if (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryBks->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $queryBks->where('bks.kurikulum_id', $request->kurikulum_id);
        }

        $bks = $queryBks->get();
        $matrix = $request->input('matrix', []);

        DB::transaction(function () use ($bks, $matrix) {
            foreach ($bks as $bk) {
                $selectedCplIds = isset($matrix[$bk->id]) ? array_map('intval', (array)$matrix[$bk->id]) : [];
                $bk->cpl()->sync($selectedCplIds);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Matriks Pemetaan CPL - BK berhasil diperbarui!'
            ]);
        }

        return redirect()->back()->with('success', 'Matriks Pemetaan CPL - BK berhasil diperbarui!');
    }

    public function updateMatrixCPLBKMK(Request $request)
    {
        $user = auth()->user();
        $otoritas = $user->otoritas->otoritas;

        $queryBk = BK::query()
            ->join('prodi', 'bks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('bks.*');

        $queryCpl = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul');

        $queryMk = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mks.*');

        if ($otoritas === 'Penjamin Mutu Universitas') {
            $queryBk->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryCpl->where('fakultas.id_universitas', $user->id_universitasUser);
            $queryMk->where('fakultas.id_universitas', $user->id_universitasUser);
        } else if ($otoritas === 'Penjamin Mutu Fakultas') {
            $queryBk->where('prodi.id_fakultas', $user->id_fakultasUser);
            $queryCpl->where('fakultas.id', $user->id_fakultasUser);
            $queryMk->where('prodi.id_fakultas', $user->id_fakultasUser);
        } else if (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $queryBk->where('prodi.id', $user->id_prodiUser);
            $queryCpl->where('prodi.id', $user->id_prodiUser);
            $queryMk->where('prodi.id', $user->id_prodiUser);
        }

        if ($request->filled('kurikulum_id')) {
            $queryBk->where('bks.kurikulum_id', $request->kurikulum_id);
            $queryCpl->where('cpls.id_kurikulum', $request->kurikulum_id);
            $queryMk->where('mks.id_kurikulum', $request->kurikulum_id);
        }

        $bks = $queryBk->get();
        $cpls = $queryCpl->get();
        $mks = $queryMk->get();
        $matrix = $request->input('matrix', []);

        DB::transaction(function () use ($bks, $cpls, $mks, $matrix) {
            foreach ($bks as $bk) {
                $selectedCplIds = [];
                $selectedMkKodes = [];

                foreach ($cpls as $cpl) {
                    $mksInCell = isset($matrix[$bk->id][$cpl->id]) ? (array)$matrix[$bk->id][$cpl->id] : [];
                    if (!empty($mksInCell)) {
                        $selectedCplIds[] = $cpl->id;
                        foreach ($mksInCell as $mkKode) {
                            $selectedMkKodes[] = $mkKode;
                        }
                    }
                }

                $bk->cpl()->sync(array_unique($selectedCplIds));
                $bk->mk()->sync(array_unique($selectedMkKodes));
            }

            foreach ($mks as $mk) {
                $selectedCplIdsForMk = [];

                foreach ($bks as $bk) {
                    foreach ($cpls as $cpl) {
                        $mksInCell = isset($matrix[$bk->id][$cpl->id]) ? (array)$matrix[$bk->id][$cpl->id] : [];
                        if (in_array($mk->kode, $mksInCell)) {
                            $selectedCplIdsForMk[] = $cpl->id;
                        }
                    }
                }

                $selectedCplIdsForMk = array_unique($selectedCplIdsForMk);
                $syncData = [];
                foreach ($selectedCplIdsForMk as $cplId) {
                    $syncData[$cplId] = ['id_prodi' => $mk->id_prodi];
                }
                $mk->cpl()->sync($syncData);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Matriks Pemetaan CPL - BK - MK berhasil diperbarui!'
            ]);
        }

        return redirect()->back()->with('success', 'Matriks Pemetaan CPL - BK - MK berhasil diperbarui!');
    }
}