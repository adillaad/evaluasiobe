<?php

namespace App\Http\Controllers\Admin;

use App\Models\MK;
use App\Models\CPL;
use App\Models\RPS;
use App\Models\Soal;
use App\Models\User;
use App\Models\Kurikulum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        $authUser = auth()->user(); // Mendapatkan user yang sedang login
        $userOtoritas = optional($authUser->otoritas)->otoritas;

        // Cek otoritas pengguna
        if ($userOtoritas == "Admin Universitas") {
            // Hitung userCount hanya untuk universitas tertentu
            $userCount = User::query()
                ->leftJoin('user_otoritas', 'users.id', '=', 'user_otoritas.user_id')
                ->leftJoin('prodi', 'users.id_prodiUser', '=', 'prodi.id')
                ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->leftJoin('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
                ->whereNotNull(['name', 'email', 'id_universitasUser'])
                ->where('users.id', '!=', auth()->id())
                ->where('fakultas.id_universitas', $authUser->id_universitasUser)
                ->distinct()
                ->count('users.id');

            // Query kurikulum hanya untuk universitas tertentu
            $kurikulums = Kurikulum::query()
                ->select('kurikulums.*')
                ->join('prodi', 'kurikulums.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->where('fakultas.id_universitas', $authUser->id_universitasUser)
                ->orderBy('tahun', 'asc')
                ->get();

            // Query prodis untuk universitas tertentu
            $prodis = \App\Models\Prodi::query()
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->where('fakultas.id_universitas', $authUser->id_universitasUser)
                ->select('prodi.*')
                ->with(['fakultas', 'fakultas.universitas'])
                ->withCount('users')
                ->get();
        } else {
            // Hitung userCount untuk semua data
            $userCount = User::query()
                ->leftJoin('user_otoritas', 'users.id', '=', 'user_otoritas.user_id')
                ->leftJoin('prodi', 'users.id_prodiUser', '=', 'prodi.id')
                ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->leftJoin('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
                ->whereNotNull(['name', 'email', 'id_universitasUser'])
                ->where('users.id', '!=', auth()->id())
                ->distinct()
                ->count('users.id');

            // Query semua kurikulum tanpa filter universitas
            $kurikulums = Kurikulum::orderBy('tahun', 'desc')->get();

            $prodis = \App\Models\Prodi::with(['fakultas', 'fakultas.universitas'])->withCount('users')->get();
        }

        return view('admin.dashboard', compact('userCount', 'kurikulums', 'prodis'));
    }

    public function card($filter)
    {
        $authUser = auth()->user(); // Mendapatkan user yang sedang login

        // Inisialisasi query untuk masing-masing model
        $cplQuery = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');
        $mkQuery = MK::query()
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');
        $rpsQuery = RPS::query()
            ->join('prodi', 'rpss.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');
        $soalQuery = Soal::query()
            ->join('prodi', 'soals.prodiId', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        // Jika otoritas adalah "Admin Universitas", tambahkan filter berdasarkan id_universitasUser
        if ($authUser->otoritas->otoritas === 'Admin Universitas') {
            $cplQuery->where('fakultas.id_universitas', $authUser->id_universitasUser);
            $mkQuery->where('fakultas.id_universitas', $authUser->id_universitasUser);
            $rpsQuery->where('fakultas.id_universitas', $authUser->id_universitasUser);
            $soalQuery->where('fakultas.id_universitas', $authUser->id_universitasUser);
        }

        // Filter berdasarkan tahun kurikulum jika ada filter
        if ($filter !== 'all') {
            $cplQuery->where('id_kurikulum', $filter);
            $mkQuery->where('id_kurikulum', $filter);
            $rpsQuery->where('id_kurikulum', $filter);
            $soalQuery->where('kurikulumId', $filter);
        }

        // Ambil data berdasarkan query yang sudah difilter
        $cpls = $cplQuery->get();
        $mks = $mkQuery->get();
        $rpss = $rpsQuery->get();
        $soals = $soalQuery->get();

        // Hitung jumlah data
        $data = [
            'sum_mk' => $mks->count(),
            'sum_cpl' => $cpls->count(),
            'sum_rps' => $rpss->count(),
            'sum_soal' => $soals->count()
        ];

        return response()->json($data);
    }

    public function chart($filter)
    {
        $authUser = auth()->user();

        // 1. Ambil data CPL berdasarkan filter dan otoritas
        $cplQuery = CPL::query()
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('kurikulums', 'cpls.id_kurikulum', '=', 'kurikulums.id');

        if ($authUser->otoritas->otoritas === 'Admin Universitas') {
            $cplQuery->where('fakultas.id_universitas', $authUser->id_universitasUser);
        }

        if ($filter !== 'all') {
            $cplQuery->where('cpls.id_kurikulum', $filter);
        }
        
        $cpls = $cplQuery->select('cpls.*', 'kurikulums.tahun as kurikulum_tahun')->get();

        // 2. Kelompokkan CPL berdasarkan aspek yang baru
        $cplGroups = $cpls->groupBy('aspek');

        $pengetahuan_labels = $cplGroups->get('Pengetahuan', collect())->map(function ($cpl) use ($filter) {
            return $filter === 'all' ? $cpl->kurikulum_tahun . ' - ' . $cpl->kode : $cpl->kode;
        })->values()->all();

        $keterampilan_khusus_labels = $cplGroups->get('Keterampilan Khusus', collect())->map(function ($cpl) use ($filter) {
            return $filter === 'all' ? $cpl->kurikulum_tahun . ' - ' . $cpl->kode : $cpl->kode;
        })->values()->all();

        $keterampilan_umum_labels = $cplGroups->get('Keterampilan Umum', collect())->map(function ($cpl) use ($filter) {
            return $filter === 'all' ? $cpl->kurikulum_tahun . ' - ' . $cpl->kode : $cpl->kode;
        })->values()->all();

        // 3. Hitung jumlah MK yang terhubung ke setiap CPL
        $cplMkCounts = DB::table('mk_cpl')
            ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->when($authUser->otoritas->otoritas === 'Admin Universitas', function ($query) use ($authUser) {
                $query->where('fakultas.id_universitas', $authUser->id_universitasUser);
            })
            ->select('cpl_id', DB::raw('count(mk_kode) as mk_count'))
            ->groupBy('cpl_id')
            ->pluck('mk_count', 'cpl_id');

        // Fungsi untuk mendapatkan jumlah MK untuk setiap CPL
        $getCounts = function ($cplGroup) use ($cplMkCounts) {
            return $cplGroup->map(function ($cpl) use ($cplMkCounts) {
                return $cplMkCounts->get($cpl->id, 0);
            })->values()->all();
        };

        $pengetahuan_counts = $getCounts($cplGroups->get('Pengetahuan', collect()));
        $keterampilan_khusus_counts = $getCounts($cplGroups->get('Keterampilan Khusus', collect()));
        $keterampilan_umum_counts = $getCounts($cplGroups->get('Keterampilan Umum', collect()));

        // 4. Siapkan data warna dan border untuk chart
        $list_warna = [
            'rgba(255, 99, 132, 0.2)', 
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)', 
            'rgba(75, 192, 192, 0.2)', 
            'rgba(153, 102, 255, 0.2)', 
            'rgba(255, 159, 64, 0.2)'
        ];
        $list_border = [
            'rgba(255,99,132,1)', 
            'rgba(54, 162, 235, 1)', 
            'rgba(255, 206, 86, 1)', 
            'rgba(75, 192, 192, 1)', 
            'rgba(153, 102, 255, 1)', 
            'rgba(255, 159, 64, 1)'
        ];

        $generateColors = function ($count) use ($list_warna) {
            $colors = [];
            for ($i = 0; $i < $count; $i++) { $colors[] = $list_warna[$i % count($list_warna)]; }
            return $colors;
        };
        $generateBorders = function ($count) use ($list_border) {
            $borders = [];
            for ($i = 0; $i < $count; $i++) { $borders[] = $list_border[$i % count($list_border)]; }
            return $borders;
        };

        // 5. Kirim data dalam format JSON
        $data = [
            'pengetahuan' => [
                'labels' => !empty($pengetahuan_labels) ? $pengetahuan_labels : ['Tidak ada data'],
                'counts' => !empty($pengetahuan_counts) ? $pengetahuan_counts : [0],
                'colors' => $generateColors(count($pengetahuan_labels)),
                'borders' => $generateBorders(count($pengetahuan_labels)),
            ],
            'keterampilan_khusus' => [
                'labels' => !empty($keterampilan_khusus_labels) ? $keterampilan_khusus_labels : ['Tidak ada data'],
                'counts' => !empty($keterampilan_khusus_counts) ? $keterampilan_khusus_counts : [0],
                'colors' => $generateColors(count($keterampilan_khusus_labels)),
                'borders' => $generateBorders(count($keterampilan_khusus_labels)),
            ],
            'keterampilan_umum' => [
                'labels' => !empty($keterampilan_umum_labels) ? $keterampilan_umum_labels : ['Tidak ada data'],
                'counts' => !empty($keterampilan_umum_counts) ? $keterampilan_umum_counts : [0],
                'colors' => $generateColors(count($keterampilan_umum_labels)),
                'borders' => $generateBorders(count($keterampilan_umum_labels)),
            ],
        ];

        return response()->json($data);
    }
}
