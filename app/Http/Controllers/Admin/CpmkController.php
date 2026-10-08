<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\Kurikulum;
use App\Traits\UniversityFilterTrait;
use App\Models\SubCpmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CpmkController extends Controller
{
    use UniversityFilterTrait;

    public function create()
    {
        $kurikulumsQuery = Kurikulum::query()
            ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('kurikulums.*');
        $userOtoritas = auth()->user()->otoritas->otoritas;
        $cplsQuery = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id','cpls.kode','cpls.judul');

        if ($userOtoritas === 'Admin Universitas') {
            $kurikulumsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $cplsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {
            $kurikulumsQuery->where('kurikulums.id_prodi', auth()->user()->id_prodiUser);
            $cplsQuery->where('cpls.id_prodi', auth()->user()->id_prodiUser);
        }
        $cpls = $cplsQuery->get();
        $kurikulums = $kurikulumsQuery->get();
        return view('admin.cpmk.add', compact('kurikulums'));
    }
    public function getCplbyKurkulum($kurikulum_id)
{
    $userOtoritas = auth()->user()->otoritas->otoritas;

    $cplsQuery = CPL::query()
        ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
        ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
        ->select('cpls.id','cpls.kode','cpls.judul');

    if ($userOtoritas === 'Admin Universitas') {
        $cplsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
    } elseif (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {
        $cplsQuery->where('cpls.id_prodi', auth()->user()->id_prodiUser);
    }

    $cpls = $cplsQuery->where('id_kurikulum', $kurikulum_id)->get();

    return response()->json($cpls);
}

    public function store(Request $request)
    {
        $request->validate([
            'cpl' => 'required',
            'judul' => 'required|array|min:1', // Pastikan judul adalah array dan tidak kosong
            'judul.*' => 'required|string', // Pastikan setiap item di dalam judul tidak kosong
        ], [
            'cpl.required' => 'CPL wajib dipilih.',
            'judul.*.required' => 'Judul rincian CPMK tidak boleh kosong.',
        ]);

        try {
            DB::beginTransaction(); // Gunakan transaksi untuk keamanan data

            $cplId = $request->cpl;
            $cpl = CPL::findOrFail($cplId);

            // 1. UBAH CARA MENGAMBIL KODE CPL
            // Ambil kode numerik dari CPL. Contoh: dari "CPL01" menjadi "01"
            $cplKodeNumeric = str_replace('CPL', '', $cpl->kode);

            // Dapatkan jumlah CPMK yang sudah ada untuk CPL ini
            $jumlahCpmkPadaCpl = CPMK::where('cpl_id', $cplId)->count();

            foreach ($request->judul as $index => $judul) {
                if (!empty($judul)) {
                    // 2. BUAT KODE CPMK YANG BENAR
                    // Gabungkan 'CPMK' + kode numerik CPL + (jumlah cpmk + index loop + 1)
                    // Contoh: 'CPMK' + '01' + (0 + 0 + 1) -> 'CPMK011'
                    // Contoh iterasi kedua: 'CPMK' + '01' + (0 + 1 + 1) -> 'CPMK012'
                    $cpmkKode = 'CPMK' . $cplKodeNumeric . ($jumlahCpmkPadaCpl + $index + 1);

                    CPMK::create([
                        'cpl_id' => $cplId,
                        'kode' => $cpmkKode,
                        'id_prodi' => $cpl->id_prodi,
                        'judul' => $judul,
                    ]);
                }
            }

            DB::commit(); // Simpan semua perubahan jika berhasil
            return redirect()->route($this->getRouteByAuthority())->with('success', 'CPMK berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            return redirect()->back()->withInput()->with('error', "Terjadi kesalahan: " . $e->getMessage());
        }
    }

    public function list(Request $request)
    {
        // Ambil peran pengguna sekali saja untuk efisiensi
        // Pastikan relasi 'otoritas' dan kolom 'otoritas' sudah benar
        $userOtoritas = auth()->user()->otoritas->otoritas;

        $cpmks = CPMK::query()
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->join('cpls', 'cpmks.cpl_id', '=', 'cpls.id')
            ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id')
            ->whereNotNull('cpmks.cpl_id')
            ->select('cpmks.*')
            ->with(['subCpmks', 'cpl.kurikulum', 'prodi.fakultas']);

        // Logika filter berdasarkan peran pengguna
        if ($userOtoritas === 'Admin Universitas') {
            // Admin Universitas melihat semua CPMK di universitasnya
            $cpmks->where('universitas.id', auth()->user()->id_universitasUser);

        } elseif ($userOtoritas === 'Penjamin Mutu Universitas') {
            // Penjamin Mutu Universitas melihat semua CPMK di universitasnya
            $cpmks->where('universitas.id', auth()->user()->id_universitasUser);

        } elseif ($userOtoritas === 'Penjamin Mutu Fakultas') {
            // Penjamin Mutu Fakultas melihat semua CPMK di fakultasnya
            $cpmks->where('fakultas.id', auth()->user()->id_fakultasUser);

        } elseif (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {
            // Kaprodi dan Penjamin Mutu Prodi melihat CPMK di prodinya saja
            $cpmks->where('prodi.id', auth()->user()->id_prodiUser);
        }
        // Untuk 'Admin' (Super Admin), tidak ada filter yang diterapkan, sehingga bisa melihat semuanya.

        // Gunakan method dari trait untuk filter tambahan dari request (jika ada)
        $cpmks = $this->getFilteredQuery($cpmks, $request);
        $cpmks = $cpmks->orderBy('cpls.kode', 'asc')->orderBy('cpmks.kode', 'asc')->get();

        // Gunakan method dari trait untuk data dropdown filter
        $filterData = $this->getFilterData($request);

        return view('admin.cpmk.list', array_merge(
            ['cpmks' => $cpmks, 'userOtoritas' => $userOtoritas],
            $filterData
        ));
    }

    public function edit($id)
    {
        try {
            $ids = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $ids = $id;
        }
        $cpmk = CPMK::with('cpl.kurikulum')->findOrFail($ids);
        
        $userOtoritas = auth()->user()->otoritas->otoritas ?? '';
        $cplsQuery = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id')
            ->select('cpls.id', 'cpls.kode', 'cpls.judul', 'kurikulums.tahun as tahun_kurikulum');

        if ($userOtoritas === 'Admin Universitas' || $userOtoritas === 'Penjamin Mutu Universitas') {
            $cplsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi'])) {
            $cplsQuery->where('cpls.id_prodi', auth()->user()->id_prodiUser);
        } elseif ($cpmk->id_prodi) {
            $cplsQuery->where('cpls.id_prodi', $cpmk->id_prodi);
        }
        $cpls = $cplsQuery->orderBy('kurikulums.tahun', 'asc')->orderBy('cpls.kode', 'asc')->get();

        if ($cpls->isEmpty()) {
            $cpls = CPL::query()
                ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id')
                ->select('cpls.id', 'cpls.kode', 'cpls.judul', 'kurikulums.tahun as tahun_kurikulum')
                ->where('cpls.id_prodi', $cpmk->id_prodi ?? (auth()->user()->id_prodiUser ?? null))
                ->orderBy('kurikulums.tahun', 'asc')
                ->orderBy('cpls.kode', 'asc')
                ->get();
        }

        return view('admin.cpmk.edit', compact('cpmk', 'cpls'));
    }

    public function update(Request $request, $id)
    {
        try {
            $ids = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $ids = $id;
        }
        $request->validate([
            'cpl' => 'required',
            'judul' =>
            ['required', 'string', 'regex:/^[a-zA-Z0-9., \/()&*%-=+_:;]+$/', 'max:255'],
        ]);
        $cpmk = CPMK::findOrFail($ids);
        $cpmk->update([
            'cpl_id' => $request->cpl,
            'judul' => $request->judul,
        ]);
        return redirect()->route($this->getRouteByAuthority())->with('success', 'CPMK berhasil diperbarui!');
    }

    public function delete($id)
    {
        try {
            $ids = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $ids = $id;
        }

        DB::beginTransaction();
        try {
            $cpmk = CPMK::findOrFail($ids);
            $kodeCpmk = $cpmk->kode;

            // 1. Ambil semua ID Sub-CPMK terkait
            $subCpmkIds = SubCpmk::where('cpmk_id', $ids)->pluck('id');

            // 2. Hapus data di tabel turunan Sub-CPMK
            if ($subCpmkIds->isNotEmpty()) {
                if (Schema::hasTable('mk_sub_cpmk') && Schema::hasColumn('mk_sub_cpmk', 'sub_cpmk_id')) {
                    DB::table('mk_sub_cpmk')->whereIn('sub_cpmk_id', $subCpmkIds)->delete();
                }
                if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'sub_cpmk_id')) {
                    DB::table('activities')->whereIn('sub_cpmk_id', $subCpmkIds)->delete();
                }
                if (Schema::hasTable('mutus') && Schema::hasColumn('mutus', 'sub_cpmk_id')) {
                    DB::table('mutus')->whereIn('sub_cpmk_id', $subCpmkIds)->delete();
                }
                if (Schema::hasTable('soals') && Schema::hasColumn('soals', 'sub_cpmk_id')) {
                    DB::table('soals')->whereIn('sub_cpmk_id', $subCpmkIds)->delete();
                }
                if (Schema::hasTable('tanpa_soal') && Schema::hasColumn('tanpa_soal', 'sub_cpmk_id')) {
                    DB::table('tanpa_soal')->whereIn('sub_cpmk_id', $subCpmkIds)->delete();
                }
                if (Schema::hasTable('konversi_cpmk_metode') && Schema::hasColumn('konversi_cpmk_metode', 'sub_cpmk_id')) {
                    DB::table('konversi_cpmk_metode')->whereIn('sub_cpmk_id', $subCpmkIds)->delete();
                }
                SubCpmk::whereIn('id', $subCpmkIds)->delete();
            }

            // 3. Hapus data di tabel yang mereferensikan CPMK (foreign keys)
            if (Schema::hasTable('mutus') && Schema::hasColumn('mutus', 'Cpmk')) {
                DB::table('mutus')->where('Cpmk', $ids)->delete();
            }
            if (Schema::hasTable('cpmk_soals') && Schema::hasColumn('cpmk_soals', 'id_cpmk')) {
                DB::table('cpmk_soals')->where('id_cpmk', $ids)->delete();
            }
            if (Schema::hasTable('cpl_cpmk') && Schema::hasColumn('cpl_cpmk', 'cpmk_id')) {
                DB::table('cpl_cpmk')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('cpmk_mk') && Schema::hasColumn('cpmk_mk', 'cpmk_id')) {
                DB::table('cpmk_mk')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('cpl_mk_cpmk_penilaian') && Schema::hasColumn('cpl_mk_cpmk_penilaian', 'cpmk_id')) {
                DB::table('cpl_mk_cpmk_penilaian')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('profesi_cpmk') && Schema::hasColumn('profesi_cpmk', 'cpmk_id')) {
                DB::table('profesi_cpmk')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'cpmk_id')) {
                DB::table('activities')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('soals') && Schema::hasColumn('soals', 'cpmk_id')) {
                DB::table('soals')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('tanpa_soals') && Schema::hasColumn('tanpa_soals', 'cpmk_id')) {
                DB::table('tanpa_soals')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('tanpa_soal') && Schema::hasColumn('tanpa_soal', 'cpmk_id')) {
                DB::table('tanpa_soal')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('konversi_cpmk_metode') && Schema::hasColumn('konversi_cpmk_metode', 'cpmk_id')) {
                DB::table('konversi_cpmk_metode')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('evaluasi_cpmk_mahasiswas') && Schema::hasColumn('evaluasi_cpmk_mahasiswas', 'cpmk_id')) {
                DB::table('evaluasi_cpmk_mahasiswas')->where('cpmk_id', $ids)->delete();
            }
            if (Schema::hasTable('evaluasi_mk_cpmk_angkatans') && Schema::hasColumn('evaluasi_mk_cpmk_angkatans', 'cpmk_id')) {
                DB::table('evaluasi_mk_cpmk_angkatans')->where('cpmk_id', $ids)->delete();
            }

            // 4. Hapus CPMK utama
            $cpmk->delete();

            DB::commit();
            return redirect()->route($this->getRouteByAuthority())->with('success', "CPMK {$kodeCpmk} & seluruh data terkait berhasil dihapus!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus CPMK: ' . $e->getMessage());
        }
    }

    private function getRouteByAuthority(): string
    {
        $otoritas = auth()->user()->otoritas->otoritas ?? '';
        $routes = [
            'Admin' => 'admin.list-cpmk',
            'Admin Universitas' => 'admin-universitas.list-cpmk',
            'Koordinator Program Studi' => 'koordinator-program-studi.cpmk-list',
            'Kepala Program Studi' => 'kepala-program-studi.cpmk-list',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.list-cpmk',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.list-cpmk',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.list-cpmk',
        ];

        $target = $routes[$otoritas] ?? 'dosen.cpmk-list';
        if (\Illuminate\Support\Facades\Route::has($target)) {
            return $target;
        }
        return 'dosen.cpmk-list';
    }
}
