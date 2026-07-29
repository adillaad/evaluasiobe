<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\Kurikulum;
use App\Traits\UniversityFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
        } elseif ($userOtoritas === 'Kepala Program Studi' || $userOtoritas === 'Penjamin Mutu Program Studi') {
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
    } elseif ($userOtoritas === 'Kepala Program Studi' || $userOtoritas === 'Penjamin Mutu Program Studi') {
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
            ->select('cpmks.*');

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

        } elseif ($userOtoritas === 'Kepala Program Studi' || $userOtoritas === 'Penjamin Mutu Program Studi') {
            // Kaprodi dan Penjamin Mutu Prodi melihat CPMK di prodinya saja
            $cpmks->where('prodi.id', auth()->user()->id_prodiUser);
        }
        // Untuk 'Admin' (Super Admin), tidak ada filter yang diterapkan, sehingga bisa melihat semuanya.

        // Gunakan method dari trait untuk filter tambahan dari request (jika ada)
        $cpmks = $this->getFilteredQuery($cpmks, $request);
        $cpmks = $cpmks->orderBy('kode', 'asc')->get();

        // Gunakan method dari trait untuk data dropdown filter
        $filterData = $this->getFilterData($request);

        return view('admin.cpmk.list', array_merge(
            ['cpmks' => $cpmks],
            $filterData
        ));
    }

    public function edit($id)
    {
        $ids = Crypt::decrypt($id);
        $cpmk = CPMK::find($ids);
        $cpl = CPL::firstWhere('id',$cpmk->cpl_id);
        $cpmk->cpl = $cpl;

        return view('admin.cpmk.edit', compact('cpmk'));
    }

    public function update(Request $request, $id)
    {
        $ids = Crypt::decrypt($id);
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
        $ids = Crypt::decrypt($id);
        CPMK::where('id', $ids)->delete();
        return redirect()->route($this->getRouteByAuthority())->with('success', 'CPMK berhasil dihapus!');
    }

    private function getRouteByAuthority(): string
    {
        $routes = [
            'Admin' => 'admin.list-cpmk',
            'Admin Universitas' => 'admin-universitas.list-cpmk',
            'Kepala Program Studi' => 'kepala-program-studi.list-cpmk',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.list-cpmk',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.list-cpmk',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.list-cpmk',
        ];

        return $routes[auth()->user()->otoritas->otoritas] ?? 'default.route';
    }
}
