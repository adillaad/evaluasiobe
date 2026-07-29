<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Models\MK;
use App\Models\CPMK;
use App\Models\Soal;
use App\Models\CPMKSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\UniversityFilterTrait;
use Illuminate\Support\Facades\Crypt;

class SoalController extends Controller
{
    use UniversityFilterTrait;
    public function list(Request $request)
    {
        $query = Soal::query()
            ->join('mks', 'soals.kode_mk', '=', 'mks.kode')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->select('soals.*');

        $cpmkQuery = CPMK::query()
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        $cpmks = $cpmkQuery->orderBy('cpmks.id', 'asc')->get()->groupBy('kode_mk');

        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $cpmkQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        }

        // Gunakan method dari trait untuk filter
        $query = $this->getFilteredQuery($query, $request);

        $soals = Soal::select('*')
            ->fromSub(function ($subQuery) use ($query) {
                $subQuery->from($query)
                    ->select('*')
                    ->selectRaw('ROW_NUMBER() OVER (PARTITION BY kode_mk, jenis ORDER BY id) as row_num');
            }, 'ranked')
            ->where('row_num', 1)
            ->orderBy('kode_mk')
            ->orderBy('minggu')
            ->orderBy('jenis')
            ->get();

        // Gunakan method dari trait untuk data dropdown
        $filterData = $this->getFilterData($request);

        return view('admin.soal.list', array_merge(
            ['soals' => $soals],
            ['cpmks' => $cpmks],
            $filterData
        ));
    }

    public function summary(Request $request)
    {
        // Memulai kueri dari Model CPMK (Eloquent Builder)
        $query = CPMK::query()
            // Gabungkan tabel-tabel yang diperlukan
            ->join('cpmk_mk', 'cpmks.id', '=', 'cpmk_mk.cpmk_id')
            ->join('mks', 'cpmk_mk.mk_kode', '=', 'mks.kode')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
            ->leftJoin('cpmk_soals', 'cpmks.id', '=', 'cpmk_soals.id_cpmk') // Join untuk hitung soal
            
            // --- PERBAIKAN DI SINI ---
            ->select(
                'cpmks.id', 'cpmks.judul', // Ambil kolom spesifik dari cpmks
                'mks.kode AS mk_kode',     // <-- TAMBAHKAN KOLOM INI
                'mks.nama AS nama_mk',
                'prodi.nama AS nama_prodi',
                'fakultas.nama AS nama_fakultas',
                'universitas.nama AS nama_universitas',
                DB::raw('COUNT(cpmk_soals.id_soal) as soal_count') // Hitung jumlah soal
            )
            ->groupBy('cpmks.id', 'mks.kode'); // Group by ID unik

        // Filter berdasarkan hak akses (otoritas) pengguna
        if (auth()->user()->otoritas->otoritas === 'Admin Universitas') {
            $query->where('universitas.id', auth()->user()->id_universitasUser);
        }

        // Gunakan Trait, sekarang akan berjalan tanpa error
        $query = $this->getFilteredQuery($query, $request);
        
        // Eksekusi kueri
        $results = $query->orderBy('mks.kode', 'asc')->orderBy('cpmks.id', 'asc')->get();

        // Kelompokkan hasilnya berdasarkan kode mata kuliah
        $cpmks = $results->groupBy('mk_kode');

        // Dapatkan data untuk dropdown filter
        $filterData = $this->getFilterData($request);

        // Kirim data ke view
        return view('admin.soal.summary', array_merge(
            ['cpmks' => $cpmks],
            $filterData
        ));
    }

    public function chart_soal($kode_mk)
    {
        $cpmks = CPMK::orderBy('id', 'asc')->get()->groupBy('kode_mk');
        $i = 0;
        $sum = 0;
        foreach ($cpmks as $mk => $cpmk) {
            if ($mk == $kode_mk) {
                foreach ($cpmk as $cp) {
                    $sum += 1;
                    $cpp[$i] = $cp->soal->count();
                    $kodecpmks[$i] = 'CPMK - ' . $sum;
                    $i++;
                }
            }
        }

        $list_warna = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
        ];
        $tmp = -1;
        for ($i = 0; $i < $sum; $i++) {
            $warna[$i] = $list_warna[++$tmp];
            if ($tmp == 5) {
                $tmp = 0;
            }
        }

        $list_border = ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',];
        $tmp = -1;
        for ($i = 0; $i < $sum; $i++) {
            $border[$i] = $list_border[++$tmp];
            if ($tmp == 5) {
                $tmp = 0;
            }
        }

        $dt = ([
            'kode_cpmk' => $kodecpmks,
            'jumlah' => $cpp,
            'warna' => $warna,
            'border' => $border,
        ]);

        return response()->json($dt);
    }

    public function print($id)
    {
        $ids = Crypt::decrypt($id);
        $soal = soal::findOrFail($ids);
        $mks = MK::all();
        $cpmks = collect();
        $soals = collect();
        $cpmk_soals = collect();
        // $countsoals = collect();
        foreach ($mks as $mk) {
            if ($soal->kode_mk == $mk->kode) {
                $soalss = Soal::where('kode_mk', $mk->kode)->where('jenis', $soal->jenis)->orderBy('id', 'asc')->get();
            }
        }

        foreach ($soalss as $s) $soals->push($s);
        foreach ($soals as $sl) {
            $temp = DB::table('cpmk_soals')->select(DB::raw('id_cpmk, id_soal'))->groupBy('id_cpmk')->orderBy('id_cpmk', 'asc')->get();
            $cpmk_s = $temp->where('id_soal', $sl->id);
            // $count = DB::table('cpmk_soals')->select(DB::raw('id_soal,COUNT(*) as soal_count'))->where('id_soal', $sl->id)->groupBy('id_soal')->orderBy('id_cpmk', 'asc')->get();
            foreach ($cpmk_s as $cpmk) $cpmk_soals->push($cpmk);
            // foreach ($count as $cnt) $countsoals->push($cnt);
        }
        foreach ($cpmk_soals as $c_s) {
            $cpmkss = CPMK::where('id', $c_s->id_cpmk)->get();
            foreach ($cpmkss as $cp) $cpmks->push($cp);
        }
        $mk = MK::findorFail($soal->kode_mk);
        $data = compact(
            'mk',
            'soal',
            'mks',
            'cpmks',
            'cpmk_soals'
        );
        $pdf = PDF::loadView('admin.soal.print', $data);
        $pdf->setOption('enable-local-file-access', true);
        return $pdf->stream('soal.pdf');
    }
}
