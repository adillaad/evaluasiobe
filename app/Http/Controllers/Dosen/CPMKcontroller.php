<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\SubCpmk; // <--- Jangan lupa import ini
use Illuminate\Http\Request;
use App\Traits\UniversityFilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CPMKcontroller extends Controller
{
    use UniversityFilterTrait;

    public function Add()
    {
        $idProdi = auth()->user()->id_prodiUser;
        $cpls = CPL::query()
            ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id')
            ->where('cpls.id_prodi', $idProdi)
            ->select(
                'cpls.id',
                'cpls.kode',
                'cpls.judul',
                'kurikulums.tahun as tahun_kurikulum'
            )
            ->orderBy('kurikulums.tahun', 'asc')
            ->orderBy('cpls.kode', 'asc')
            ->get();

        $existingCpmks = CPMK::query()
            ->where('id_prodi', $idProdi)
            ->select('id', 'kode', 'judul', 'cpl_id') // <--- WAJIB ADA cpl_id
            ->orderBy('kode')
            ->get();

        return view('dosen.CPMK.add', compact('cpls', 'existingCpmks'));
    }

    public function Store(Request $request)
    {
        $request->validate([
            'cpl' => 'required',
            'cpmk_selection' => 'required',
            'uraian_sub.*' => 'required|string',
        ]);

        if ($request->cpmk_selection === 'new') {
            $request->validate([
                'judul_cpmk_baru' => 'required|string',
            ]);
        }

        DB::beginTransaction();

        try {
            $cpmkToUse = null;
            $kodeCpmkInduk = '';
            $cplId = $request->cpl;

            // =========================
            // BUAT CPMK BARU
            // =========================
            if ($request->cpmk_selection === 'new') {

                $cpl = DB::table('cpls')->where('id', $cplId)->first();
                if (!$cpl) {
                    throw new \Exception('CPL tidak ditemukan');
                }

                // Ambil angka CPL saja
                $cplKode = preg_replace('/\D/', '', $cpl->kode);
                if (!$cplKode) {
                    throw new \Exception('Format kode CPL tidak valid');
                }

                /**
                 * 🔒 Ambil CPMK TERAKHIR + LOCK
                 */
                $lastCpmk = CPMK::where('cpl_id', $cplId)
                    ->where('kode', 'like', 'CPMK' . $cplKode . '%')
                    ->orderByRaw(
                        "CAST(SUBSTRING(kode, LENGTH('CPMK{$cplKode}') + 1) AS UNSIGNED) DESC"
                    )
                    ->lockForUpdate()
                    ->first();

                $nextCpmkNumber = 1;

                if ($lastCpmk) {
                    $lastNumber = (int) preg_replace(
                        '/\D/',
                        '',
                        substr($lastCpmk->kode, strlen('CPMK' . $cplKode))
                    );
                    $nextCpmkNumber = $lastNumber + 1;
                }

                $cpmkKode = 'CPMK' . $cplKode . $nextCpmkNumber;

                $cpmkToUse = CPMK::create([
                    'cpl_id'   => $cplId,
                    'kode'     => $cpmkKode,
                    'id_prodi' => auth()->user()->id_prodiUser,
                    'judul'    => $request->judul_cpmk_baru,
                ]);

                $kodeCpmkInduk = $cpmkKode;
            }

            // =========================
            // PAKAI CPMK YANG ADA
            // =========================
            else {
                $cpmkToUse = CPMK::lockForUpdate()->findOrFail($request->cpmk_selection);

                if ($cpmkToUse->cpl_id != $cplId) {
                    throw new \Exception('CPMK tidak sesuai dengan CPL yang dipilih');
                }

                $kodeCpmkInduk = $cpmkToUse->kode;
            }

            // =========================
            // BUAT SUB CPMK
            // =========================
            if ($request->has('uraian_sub')) {

                /**
                 * 🔒 Ambil SUB CPMK TERAKHIR + LOCK
                 */
                $lastSub = SubCpmk::where('cpmk_id', $cpmkToUse->id)
                    ->where('kode', 'like', 'Sub-' . $kodeCpmkInduk . '%')
                    ->orderByRaw(
                        "CAST(SUBSTRING(kode, LENGTH('Sub-{$kodeCpmkInduk}') + 1) AS UNSIGNED) DESC"
                    )
                    ->lockForUpdate()
                    ->first();

                $startNumber = 1;

                if ($lastSub) {
                    $lastSubNumber = (int) preg_replace(
                        '/\D/',
                        '',
                        substr($lastSub->kode, strlen('Sub-' . $kodeCpmkInduk))
                    );
                    $startNumber = $lastSubNumber + 1;
                }

                foreach ($request->uraian_sub as $index => $uraian) {
                    if (!empty($uraian)) {
                        $subKode = 'Sub-' . $kodeCpmkInduk . ($startNumber + $index);

                        SubCpmk::create([
                            'cpmk_id'  => $cpmkToUse->id,
                            'id_prodi' => auth()->user()->id_prodiUser,
                            'kode'     => $subKode,
                            'uraian'   => $uraian,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('dosen.cpmk-list')
                ->with('success', 'Data berhasil disimpan!');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function List(Request $request)
    { 
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

        } elseif (in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi', 'Penjamin Mutu Program Studi', 'Dosen'])) {
            // Kaprodi dan Penjamin Mutu Prodi melihat CPMK di prodinya saja
            $cpmks->where('cpmks.id_prodi', auth()->user()->id_prodiUser);
        }
        // Untuk 'Admin' (Super Admin), tidak ada filter yang diterapkan, sehingga bisa melihat semuanya.

        // Gunakan method dari trait untuk filter tambahan dari request (jika ada)
        $cpmks = $this->getFilteredQuery($cpmks, $request);
        $cpmks = $cpmks
            ->orderBy('cpls.kode', 'asc')
            ->orderBy('cpmks.kode', 'asc')
            ->get();

        // Gunakan method dari trait untuk data dropdown filter
        $filterData = $this->getFilterData($request);

        return view('dosen.CPMK.list', array_merge(
            ['cpmks' => $cpmks],
            $filterData, ['userOtoritas' => $userOtoritas]
        ));
    }

    public function Edit($id)
    {
        $idProdi = auth()->user()->id_prodiUser;
        $cpmk = CPMK::with('cpl')->findOrFail($id);
        $cpls = CPL::query()
            ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id')
            ->where('cpls.id_prodi', $idProdi)
            ->select('cpls.*', 'kurikulums.tahun as tahun_kurikulum')
            ->orderBy('kurikulums.tahun', 'asc')
            ->orderBy('cpls.kode', 'asc')
            ->get();

        return view('dosen.CPMK.edit', compact('cpmk', 'cpls'));
    }

    public function Update(Request $request, $id)
    {
        $request->validate([
            'cpl' => 'required',
            'judul' =>
            ['required', 'string', 'regex:/^[a-zA-Z0-9., \/()&*%-=+_:;]+$/', 'max:255'],
        ]);
        $cpmk = CPMK::findOrFail($id);
        $cpmk->update([
            'cpl_id' => $request->cpl,
            'judul' => $request->judul,
        ]);
        return redirect()->route('dosen.cpmk-list')->with('success', 'CPMK successfully updated!');
    }
    public function Delete($id)
    {
        DB::beginTransaction();

        try {
            $cpmk = CPMK::findOrFail($id);
            $kodeCpmk = $cpmk->kode;

            // 1. Ambil semua ID Sub-CPMK terkait menggunakan Model SubCpmk (tabel: sub_cpmk)
            $subCpmkIds = SubCpmk::where('cpmk_id', $id)->pluck('id');

            // 2. Hapus data di tabel turunan Sub-CPMK (jika ada)
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

            // 3. Hapus data di tabel yang mereferensikan CPMK (foreign keys) dengan verifikasi keberadaan tabel & kolom
            if (Schema::hasTable('mutus') && Schema::hasColumn('mutus', 'Cpmk')) {
                DB::table('mutus')->where('Cpmk', $id)->delete();
            }
            if (Schema::hasTable('cpmk_soals') && Schema::hasColumn('cpmk_soals', 'id_cpmk')) {
                DB::table('cpmk_soals')->where('id_cpmk', $id)->delete();
            }
            if (Schema::hasTable('cpl_cpmk') && Schema::hasColumn('cpl_cpmk', 'cpmk_id')) {
                DB::table('cpl_cpmk')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('cpmk_mk') && Schema::hasColumn('cpmk_mk', 'cpmk_id')) {
                DB::table('cpmk_mk')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('cpl_mk_cpmk_penilaian') && Schema::hasColumn('cpl_mk_cpmk_penilaian', 'cpmk_id')) {
                DB::table('cpl_mk_cpmk_penilaian')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('profesi_cpmk') && Schema::hasColumn('profesi_cpmk', 'cpmk_id')) {
                DB::table('profesi_cpmk')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'cpmk_id')) {
                DB::table('activities')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('soals') && Schema::hasColumn('soals', 'cpmk_id')) {
                DB::table('soals')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('tanpa_soals') && Schema::hasColumn('tanpa_soals', 'cpmk_id')) {
                DB::table('tanpa_soals')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('tanpa_soal') && Schema::hasColumn('tanpa_soal', 'cpmk_id')) {
                DB::table('tanpa_soal')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('konversi_cpmk_metode') && Schema::hasColumn('konversi_cpmk_metode', 'cpmk_id')) {
                DB::table('konversi_cpmk_metode')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('evaluasi_cpmk_mahasiswas') && Schema::hasColumn('evaluasi_cpmk_mahasiswas', 'cpmk_id')) {
                DB::table('evaluasi_cpmk_mahasiswas')->where('cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('evaluasi_mk_cpmk_angkatans') && Schema::hasColumn('evaluasi_mk_cpmk_angkatans', 'cpmk_id')) {
                DB::table('evaluasi_mk_cpmk_angkatans')->where('cpmk_id', $id)->delete();
            }

            // 4. Hapus CPMK utama
            $cpmk->delete();

            DB::commit();

            return redirect()
                ->route('dosen.cpmk-list')
                ->with('success', "CPMK $kodeCpmk & seluruh data terkait berhasil dihapus!");

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus CPMK: ' . $e->getMessage());
        }
    }

    public function hapusSubCpmk($id)
    {
        DB::beginTransaction();

        try { 
            $subCpmk = SubCpmk::findOrFail($id);
            $kodeSub = $subCpmk->kode; 

            if (Schema::hasTable('mk_sub_cpmk') && Schema::hasColumn('mk_sub_cpmk', 'sub_cpmk_id')) {
                DB::table('mk_sub_cpmk')->where('sub_cpmk_id', $id)->delete();
            }
            if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'sub_cpmk_id')) {
                DB::table('activities')->where('sub_cpmk_id', $id)->delete();
            }

            $subCpmk->delete(); 
            DB::commit();

            return redirect()->back()->with('success', "$kodeSub berhasil dihapus.");

        } catch (\Throwable $e) { 
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Sub-CPMK: ' . $e->getMessage());
        }
    }
}
