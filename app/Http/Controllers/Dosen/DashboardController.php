<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\RPS;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DashboardController extends Controller
{
    public function list()
    {
        $rpss = $this->getRpsByOtoritas(); // Menggunakan helper method
        $kode_mks = $rpss->pluck('kode_mk');

        $cpmks = DB::table('cpmk_mk')->whereIn('mk_kode', $kode_mks)->get();
        $cplmks = DB::table('mk_cpl')->whereIn('mk_kode', $kode_mks)->get();

        return view('dosen.dashboard', compact('rpss', 'cplmks', 'cpmks'));
    }

    public function chart()
    {
        $rpss = $this->getRpsByOtoritas(); // Menggunakan helper method
        $kode_mks = $rpss->pluck('kode_mk');

        // 1. Ambil data CPL dan kelompokkan jumlah MK yang terhubung dalam satu query
        $cplCounts = collect(DB::table('mk_cpl')->whereIn('mk_kode', $kode_mks)->get())
            ->groupBy('cpl_id')
            ->map(function ($group) {
                return $group->count();
            });

        // 2. Ambil detail CPL yang relevan
        $cpls = CPL::whereIn('id', $cplCounts->keys())->get();

        // 3. Inisialisasi array untuk hasil akhir
        $data = [
            'pengetahuan' => [], 'keterampilan' => [],
            'jumlahp' => [], 'jumlahk' => [],
            'warnap' => [], 'borderp' => [],
            'warnak' => [], 'borderk' => [],
        ];

        $list_warna = [
            'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)',
        ];
        $list_border = [
            'rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
        ];

        // 4. Proses semua data dalam satu perulangan
        foreach ($cpls as $cpl) {
            $count = $cplCounts->get($cpl->id, 0);

            if ($cpl->aspek == 'Pengetahuan') {
                $data['pengetahuan'][] = $cpl->kode;
                $data['jumlahp'][] = $count;
                // Ambil warna berdasarkan jumlah data yang sudah ada
                $colorIndex = count($data['warnap']) % count($list_warna);
                $data['warnap'][] = $list_warna[$colorIndex];
                $data['borderp'][] = $list_border[$colorIndex];
            } elseif ($cpl->aspek == 'Keterampilan') {
                $data['keterampilan'][] = $cpl->kode;
                $data['jumlahk'][] = $count;
                $colorIndex = count($data['warnak']) % count($list_warna);
                $data['warnak'][] = $list_warna[$colorIndex];
                $data['borderk'][] = $list_border[$colorIndex];
            }
        }

        return response()->json($data);
    }

    private function getRpsByOtoritas(): Collection
    {
        $user = auth()->user();
        $query = RPS::query();

        switch ($user->otoritas->otoritas) {
            case 'Wakil Rektor':
                return $query->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
                    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                    ->where('fakultas.id_universitas', $user->id_universitasUser)
                    ->get();
            case 'Wakil Dekan':
                return $query->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
                    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                    ->where('fakultas.id', $user->id_fakultasUser)
                    ->where('prodi.id', $user->id_prodiUser)
                    ->get();
            case 'Kepala Program Studi':
                return $query->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
                    ->where('prodi.id', $user->id_prodiUser)
                    ->get();
            case 'Dosen':
                return $query->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
                    ->where('pengembang', $user->name)
                    ->where('prodi.id', $user->id_prodiUser)
                    ->get();
            default:
                return collect(); // Return koleksi kosong jika tidak ada otoritas yang cocok
        }
    }
}
