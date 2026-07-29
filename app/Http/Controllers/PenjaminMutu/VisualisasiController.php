<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\Mutu;
use App\Models\Prodi;
use App\Models\Universitas;
// use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisualisasiController extends Controller
{
    public function index()
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $userUniversitasId = auth()->user()->id_universitasUser;

        $universitas = Universitas::find($userUniversitasId);

        $prodiQuery = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('prodi.id', 'prodi.nama');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor', 'Dosen'])) {
            $prodiQuery->where('fakultas.id_universitas', $userUniversitasId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $prodiQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $prodiQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $prodi = $prodiQuery->get();

        return view('penjamin-mutu.visualisasi.indexVisualisasi', compact('universitas', 'prodi'));
    }

    // public function getProdiByUniversitas(Request $request)
    // {
    //     $otoritas = auth()->user()->otoritas->otoritas;
    //     // return $request;
    //     $universitas = $request->input('universitas');

    //     // ambil npm berdasarkan angkatan dari ajax
    //     $universitasData = DB::table('mutus')
    //         ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    //         ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    //         ->select('prodi.nama as prodi')
    //         ->where('universitas_id', $universitas)
    //         ->distinct();

    //     if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
    //         $universitasData->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
    //     } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
    //         $universitasData->where('fakultas.id', auth()->user()->id_fakultasUser);
    //     } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
    //         $universitasData->where('prodi.id', auth()->user()->id_prodiUser);
    //     }
    //     $universitasData = $universitasData->get();
    //     // return $universitasData;
    //     // balikin npm ke dropdown
    //     $options = '<option value="">Pilih Prodi</option>';
    //     foreach ($universitasData as $a) {
    //         $options .= '<option value="' . $a->prodi . '">' . $a->prodi . '</option>';
    //     }

    //     return $options;
    // }

    // public function getAngkatanByUniversitas(Request $request)
    // {
    //     $otoritas = auth()->user()->otoritas->otoritas;
    //     $universitas = $request->input('universitas');
    //     // ambil npm berdasarkan angkatan dari ajax
    //     $angkatanData = DB::table('mutus')
    //         ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    //         ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    //         ->select('angkatan')
    //         ->where('universitas_id', $universitas)
    //         ->distinct()
    //         ->orderBy('angkatan', 'desc');

    //     if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
    //         $angkatanData->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
    //     } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
    //         $angkatanData->where('fakultas.id', auth()->user()->id_fakultasUser);
    //     } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
    //         $angkatanData->where('prodi.id', auth()->user()->id_prodiUser);
    //     }
    //     $angkatanData = $angkatanData->get();
    //     // return $angkatanData;
    //     // balikin npm ke dropdown
    //     $options = '<option value="">Pilih Angkatan</option>';
    //     foreach ($angkatanData as $a) {
    //         $options .= '<option value="' . $a->angkatan . '">' . $a->angkatan . '</option>';
    //     }

    //     return $options;
    // }

    public function getNpmByAngkatan(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');
        // ambil npm berdasarkan angkatan dari ajax
        $npm = DB::table('mutus as m1')
            ->join('prodi', 'm1.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('npm', 'nama_mhs')
            ->where('angkatan', $angkatan)
            ->where('universitas_id', $universitas)
            ->groupBy('npm')
            ->orderBy('npm', 'asc');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $npm->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $npm->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $npm->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $npm = $npm->get();
        // return $npm;
        // balikin npm ke dropdown
        $options = '<option value="">Pilih Npm</option>';
        foreach ($npm as $npm) {
            $options .= '<option value="' . $npm->npm . '">' . $npm->npm . '-' . $npm->nama_mhs . '</option>';
        }

        return $options;
    }

    public function getPemetaanCpl(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $idCpl = $request->input('idCpl');
        $cpl = DB::table('mk_cpl')
            // ->select()
            ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mk_cpl.mk_kode', '=', 'mks.kode', $type = 'left')
            ->where('cpl_id', $idCpl);

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cpl->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cpl->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cpl->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $cpl = $cpl->get(['mk_kode', 'mks.nama']);
        return $cpl;
    }

    public function hasilVisualMahasiswa(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $npm = $request->input('npm');
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');
        $imgSrc = $request->input('imgSrc');

        $universitasNama = Universitas::where('id', $universitas)->value('nama');

        // return $imgSrc;
        // TODO:REVISI SEMHAS : KETERCAPAIAN CPL
        // Subquery to get the max year and semester for each course
        $subQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('Course', DB::raw("MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester"))
            ->where('mutus.NPM', $npm);

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $subQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $subQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $subQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $subQuery->groupBy('mutus.Course');

        // Main query to get the records with the max year and semester
        $subQueryMutus = DB::table('mutus as m')
            ->joinSub($subQuery, 't', function ($join) {
                $join->on('m.Course', '=', 't.Course')
                    ->on(DB::raw("CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)"), '=', 't.max_year_semester');
            })
            ->where('m.NPM', $npm)
            ->groupBy('m.npm', 'm.Course', 'm.jenis')
            ->select('m.npm', 'm.Course', 'm.jenis', "m.id");

        // Gabungkan subquery dengan tabel mutus untuk menghitung total nilai berdasarkan bobot ujian dan nilai
        $nilaiMk = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->joinSub($subQueryMutus, 'sub', function ($join) {
                $join->on('mutus.id', '=', 'sub.id');
            })
            ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'))
            ->groupBy('mutus.Course');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $nilaiMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $nilaiMk->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $nilaiMk->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $nilaiMk = $nilaiMk->get();
        // return $nilaiMk;
        // Ambil semua nama MK dalam satu query untuk efisiensi
        $namaMkList = DB::table('mks')
            ->pluck('nama', 'kode'); // Menghasilkan array asosiatif [kode => nama]
        $nilaiMkLulus = [];
        $nilaiMkTidakLulus = [];

        // return $nilaiMkLulus;
        // buat masukkin mk yg lulus >50
        foreach ($nilaiMk as $nilai) {
            $namaMk = $namaMkList[$nilai->Course] ?? 'N/A';
            if ($nilai->total_nilai >= 50) {
                $nilaiMkLulus[$nilai->Course] = [$nilai->total_nilai, $namaMk];
            } else {
                $nilaiMkTidakLulus[$nilai->Course] = [$nilai->total_nilai, $namaMk];
            }
        }
        // Ambil list key Course dari $nilaiMkLulus yg dah lulus
        // $coursesLulus = array_keys($nilaiMkLulus);
        // ambil cpl perhitungannya
        $cplNilaiMk = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('Course', 'Cpl', DB::raw('SUM(examWeight / 100 * Nilai) as total_nilaiCek'))
            ->where('npm', $npm)
            ->whereIn('Course', array_keys($nilaiMkLulus))
            ->groupBy('Course', 'Cpl');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cplNilaiMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cplNilaiMk->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cplNilaiMk->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $cplNilaiMk = $cplNilaiMk->get();

        // return $nilaiMk;
        $cplNilaiMkUnique = $cplNilaiMk->pluck('Cpl')->unique()->toArray();

        // Query ke cplmk
        $cplResultsQuery = DB::table('mk_cpl')
            ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->whereIn('cpl_id', $cplNilaiMkUnique);

        // return $cplResults;
        //Buat dapetin semua cpl untuk pemilihan pemmetaan 
        $cplResultsAllQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cplResultsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $cplResultsAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cplResultsQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            $cplResultsAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cplResultsQuery->where('prodi.id', auth()->user()->id_prodiUser);
            $cplResultsAllQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $cplResults = $cplResultsQuery->get()->groupBy('cpl_id');
        $cplResultsAll = $cplResultsAllQuery->get(['cpls.id', 'cpls.kode']);

        // return $cplResultsAll;
        // Melihat keseluruhan total cpl perhitungan
        $persentaseTotalCplCapaian = [];

        // Membuat array hasil akhir
        foreach ($cplResults as $id_cpl => $items) {
            $kode_mk_list = $items->pluck('mk_kode')->toArray();
            $kodeMkArray = explode(',', implode(',', $kode_mk_list));
            $countKesamaan = count(array_intersect($kodeMkArray, array_keys($nilaiMkLulus)));
            $persentase = ($countKesamaan / count($items)) * 100;

            $cplCode = DB::table('cpls')
                ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->where('cpls.id', $id_cpl);

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplCode->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplCode->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $cplCode->where('prodi.id', auth()->user()->id_prodiUser);
            }

            // Ambil nilai kode CPL setelah filter diterapkan
            $cplCode = $cplCode->value('cpls.kode');

            $persentaseTotalCplCapaian[] = [
                'cpl' => $id_cpl,
                'kode_mk' => implode(',', $kode_mk_list),
                'count_mk' => count($items),
                'persentase' => round($persentase, 2),
                'kode_cpl' => $cplCode,
            ];
        }

        // TODO:CPL per soal
        $query = "
                SELECT cpl, cpls.kode as kode, AVG(hasil) as hasil FROM (
                    SELECT Cpl, Course, SUM(hasil) as hasil 
                        FROM (
                            SELECT q1.tahun, q1.Cpl, q1.Jenis, q1.Course, 
                               (q1.nilaiSoal * q1.BobotSoal / q2.BobotJenis) * q1.examWeight / q3.sumExamWeight as hasil 
                            FROM (
                                SELECT m.tahun, m.Cpl, m.Jenis, m.Course, m.nilaiSoal, m.examWeight, m.BobotSoal 
                                FROM mutus m 
                                JOIN (
                                    SELECT Course, MAX(CONCAT(
                                        SUBSTRING_INDEX(tahun, ' ', -1), 
                                        CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END 
                                    )) AS max_year_semester 
                                    FROM mutus 
                                    WHERE NPM = :npm1 
                                    GROUP BY Course 
                                ) t 
                                ON m.Course = t.Course AND CONCAT(
                                    SUBSTRING_INDEX(m.tahun, ' ', -1), 
                                    CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END 
                                ) = t.max_year_semester 
                                WHERE m.NPM = :npm2
                            ) q1 
                            JOIN (
                                SELECT Cpl, Jenis, Course, tahun, SUM(BobotSoal) as BobotJenis 
                                FROM mutus 
                                WHERE NPM = :npm3 
                                GROUP BY Cpl, Jenis, Course, tahun 
                            ) q2 
                            ON q1.tahun = q2.tahun AND q1.Cpl = q2.Cpl AND q1.Course = q2.Course AND q1.Jenis = q2.Jenis 
                            JOIN (
                                SELECT tahun, Cpl, Course, SUM(examWeight) AS sumExamWeight 
                                FROM (
                                    SELECT DISTINCT tahun, Cpl, Course, Jenis, examWeight 
                                    FROM mutus 
                                    WHERE NPM = :npm4
                                ) AS subquery 
                                GROUP BY tahun, Cpl, Course
                            ) q3 
                            ON q1.tahun = q3.tahun AND q1.Cpl = q3.Cpl AND q1.Course = q3.Course
                        ) q4 
                        GROUP BY Cpl, Course
                    ) q5 
                    JOIN cpls ON cpls.id = cpl
                    JOIN prodi ON cpls.id_prodi = prodi.id
                    JOIN fakultas ON prodi.id_fakultas = fakultas.id
                ";

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $query .= " WHERE fakultas.id_universitas = :id_universitas ";
            $bindings['id_universitas'] = auth()->user()->id_universitasUser;
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $query .= " WHERE fakultas.id = :id_fakultas ";
            $bindings['id_fakultas'] = auth()->user()->id_fakultasUser;
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $query .= " WHERE prodi.id = :id_prodi ";
            $bindings['id_prodi'] = auth()->user()->id_prodiUser;
        }

        $query .= " GROUP BY Cpl; ";

        $cplPerSoalWithKode = DB::select($query, array_merge([
            'npm1' => $npm,
            'npm2' => $npm,
            'npm3' => $npm,
            'npm4' => $npm
        ], $bindings));

        $dictionaryCpl = [];

        foreach ($cplPerSoalWithKode as $row) {
            $dictionaryCpl[$row->cpl] = $row->hasil;
        }
        // return $dictionaryCpl;

        // BUAT NAMPILIN KALO ADA DATA DAN KOSONG
        $cplPerSoalWithKodeAll = [];
        foreach ($cplResultsAll as $item) {
            $cplPerSoalWithKodeAll[$item->id] = ['kode' => $item->kode, 'cpl' => $item->id, 'hasil' => 0];
        }

        foreach ($cplPerSoalWithKode as $item) {
            $cplPerSoalWithKodeAll[$item->cpl] = $item;
        }

        // return $cplPerSoalWithKodeAll;

        // TODO:REVISI SEMHAS : KETERCAPAIAN CPL Angkatan
        $dataQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('nama_mhs', 'prodi.nama as prodiNama', 'prodi.id as prodiId')
            ->where('npm', $npm)
            ->where('universitas_id', $universitas);

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $dataQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $dataQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $dataQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $data = $dataQuery->distinct()->first();
        $nama = $data->nama_mhs;
        $prodiNama = $data->prodiNama;
        $prodiId = $data->prodiId;

        $allNpmQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('npm')
            ->where('angkatan', $angkatan)
            ->where('id_prodi', $prodiId);
        // return $npm;
        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $allNpmQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $allNpmQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $allNpmQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $allNpm = $allNpmQuery->distinct()->pluck('npm');
        $persentaseTotalCplCapaianAngkatan = [];

        $allCplPerAngkatanQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $allCplPerAngkatanQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $allCplPerAngkatanQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $allCplPerAngkatanQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $allCplPerAngkatanQuery = $allCplPerAngkatanQuery->get();
        $allCplPerAngkatan = [
            'total' => [],
            'min' => [],
            'max' => []
        ];
        // $allCplPerAngkatan = [];
        // $allCplPerAngkatan['total'] = [];
        foreach ($allCplPerAngkatanQuery as $cpl) {
            $allCplPerAngkatan['total'][$cpl->kode] = 0;
            $allCplPerAngkatan['min'][$cpl->kode] = 101;
            $allCplPerAngkatan['max'][$cpl->kode] = 0;
        }

        $allCplPerAngkatan['count'] = 0;
        $gabunganMkAngkatan = [];
        foreach ($allNpm as $npmItem) {
            // Sama seperti perhitungan persentaseTotalCplCapaian sebelumnya untuk setiap npm
            $subQuery = DB::table('mutus')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->select('Course', DB::raw("MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester"))
                ->where('mutus.NPM', $npmItem);

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $subQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $subQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $subQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $subQuery->groupBy('mutus.Course');

            // Main query to get the records with the max year and semester
            $subQueryMutus = DB::table('mutus as m')
                ->joinSub($subQuery, 't', function ($join) {
                    $join->on('m.Course', '=', 't.Course')
                        ->on(DB::raw("CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)"), '=', 't.max_year_semester');
                })
                ->where('m.NPM', $npmItem)
                ->groupBy('m.npm', 'm.Course', 'm.jenis')
                ->select(
                    'm.npm',
                    'm.Course',
                    'm.jenis',
                    "m.id"
                );

            // Gabungkan subquery dengan tabel mutus untuk menghitung total nilai berdasarkan bobot ujian dan nilai
            $nilaiMk = DB::table('mutus')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->joinSub($subQueryMutus, 'sub', function ($join) {
                    $join->on('mutus.id', '=', 'sub.id');
                })
                ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'));

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $nilaiMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $nilaiMk->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $nilaiMk->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $nilaiMk = $nilaiMk->groupBy('mutus.Course')->get();
            // Ambil semua nama MK dalam satu query untuk efisiensi
            $namaMkList = DB::table('mks')
                ->pluck('nama', 'kode'); // Menghasilkan array asosiatif [kode => nama]
            $nilaiMkLulusAngkatan = [];
            $nilaiMkTidakLulusAngkatan = [];

            foreach ($nilaiMk as $nilai) {
                $namaMk = $namaMkList[$nilai->Course] ?? 'N/A';
                if ($nilai->total_nilai >= 50) {
                    $nilaiMkLulusAngkatan[$nilai->Course] = [$nilai->total_nilai, $namaMk];
                } else {
                    $nilaiMkTidakLulusAngkatan[$nilai->Course] = [$nilai->total_nilai, $namaMk];
                }
            }
            $namaMkLulus = array_column($nilaiMkLulusAngkatan, 1);
            $namaMkTidakLulus = array_column($nilaiMkTidakLulusAngkatan, 1);
            $gabunganMkAngkatan = array_merge($gabunganMkAngkatan, $namaMkLulus, $namaMkTidakLulus);

            $cplNilaiMkAngkatanQuery = DB::table('mutus')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->select('Course', 'Cpl', DB::raw('SUM(examWeight / 100 * Nilai) as total_nilaiCek'))
                ->where('npm', $npmItem)
                ->whereIn('Course', array_keys($nilaiMkLulusAngkatan));

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $cplNilaiMkAngkatanQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $cplNilaiMkAngkatan = $cplNilaiMkAngkatanQuery->groupBy('mutus.Course', 'mutus.Cpl')->get();

            $cplNilaiMkUnique = $cplNilaiMkAngkatan->pluck('Cpl')->unique()->toArray();

            $cplResultsQuery = DB::table('mk_cpl')
                ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->whereIn('mk_cpl.cpl_id', $cplNilaiMkUnique);

            $cplResultsAllQuery = DB::table('cpls')
                ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplResultsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                $cplResultsAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplResultsQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
                $cplResultsAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $cplResultsQuery->where('prodi.id', auth()->user()->id_prodiUser);
                $cplResultsAllQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $cplResults = $cplResultsQuery->get()->groupBy('cpl_id');
            $cplResultsAll = $cplResultsAllQuery->get(['cpls.id', 'kode']);

            $persentaseTotalCplCapaianIndividu = [];

            foreach ($cplResults as $id_cpl => $items) {
                $kode_mk_list = $items->pluck('mk_kode')->toArray();
                $kodeMkArray = explode(',', implode(',', $kode_mk_list));
                $countKesamaan = count(array_intersect($kodeMkArray, array_keys($nilaiMkLulusAngkatan)));
                $persentase = ($countKesamaan / count($items)) * 100;

                $cplCode = DB::table('cpls')
                    ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                    ->where('cpls.id', $id_cpl);

                if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                    $cplCode->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                    $cplCode->where('fakultas.id', auth()->user()->id_fakultasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                    $cplCode->where('prodi.id', auth()->user()->id_prodiUser);
                }

                // Ambil nilai kode CPL setelah filter diterapkan
                $cplCode = $cplCode->value('cpls.kode');

                $persentaseTotalCplCapaianIndividu[] = [
                    'cpl' => $id_cpl,
                    'kode_mk' => implode(',', $kode_mk_list),
                    'count_mk' => count($items),
                    'persentase' => round($persentase, 2),
                    'kode_cpl' => $cplCode,
                ];

                $allCplPerAngkatan['total'][$cplCode] += $persentase;

                // MIN MAX
                if ($allCplPerAngkatan['min'][$cplCode] >= round($persentase, 2)) {
                    $allCplPerAngkatan['min'][$cplCode] = round($persentase, 2);
                }
                if ($allCplPerAngkatan['max'][$cplCode] <= round($persentase, 2)) {
                    $allCplPerAngkatan['max'][$cplCode] = round($persentase, 2);
                }
            }

            $allCplPerAngkatan['count'] += 1;

            $persentaseTotalCplCapaianAngkatan[] = $persentaseTotalCplCapaianIndividu;
        }
        $keyKodeCplAvgAngkatanMin = '';
        $cplAvgAngkatanMin = 101;
        $keyKodeCplAvgAngkatanMax = '';
        $cplAvgAngkatanMax = 0;
        foreach ($allCplPerAngkatan['total'] as $cpl => $total) {
            $allCplPerAngkatan['avg_cpl'][$cpl] = round($total / $allCplPerAngkatan['count'], 2);
            if ($allCplPerAngkatan['avg_cpl'][$cpl] < $cplAvgAngkatanMin && $allCplPerAngkatan['avg_cpl'][$cpl] > 0) {
                $keyKodeCplAvgAngkatanMin = $cpl;
                $cplAvgAngkatanMin = $allCplPerAngkatan['avg_cpl'][$cpl];
            }

            if ($allCplPerAngkatan['avg_cpl'][$cpl] > $cplAvgAngkatanMax) {
                $keyKodeCplAvgAngkatanMax = $cpl;
                $cplAvgAngkatanMax = $allCplPerAngkatan['avg_cpl'][$cpl];
            }
            if ($allCplPerAngkatan['min'][$cpl] > 100) {
                $allCplPerAngkatan['min'][$cpl] = 0;
            }
        }
        // return $keyKodeCplAvgAngkatanMax;
        $gabunganMkAngkatan = array_unique($gabunganMkAngkatan);
        // return $gabunganMkAngkatan;
        // return $nilaiMkLulusAngkatan;
        // Ngisi radar sementara pencapaian cpl yg blm diiinput
        $cplAllTmp = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cplAllTmp->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cplAllTmp->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cplAllTmp->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $cplAllTmp = $cplAllTmp->get();
        $cplTmp = [];
        foreach ($cplAllTmp  as $cpl) {
            $cplTmp[$cpl->id] = [0, $cpl->kode];
        }

        foreach ($persentaseTotalCplCapaian as $hasilCapaianCpl) {
            $cplTmp[$hasilCapaianCpl['cpl']] = [$hasilCapaianCpl['persentase'], $hasilCapaianCpl['kode_cpl']];
        }

        //TODO: Profil CPL
        $profilCpl = [];
        // Ngambil dari persentase capaian
        foreach ($persentaseTotalCplCapaian as $item) {
            $profilCpl[$item['cpl']] = $item['persentase'];
        }
        $profilCplKeys = array_keys($profilCpl);
        // return $nilaiMkTidakLulus;
        // TODO:Itung
        // List MK yg dah di ambil
        $keysLulus = array_keys($nilaiMkLulus);
        $keysTidakLulus = array_keys($nilaiMkTidakLulus);

        // Merge the keys and ensure uniqueness
        $gabunganMk = array_unique(array_merge($keysLulus, $keysTidakLulus));
        $courseArray = [];
        foreach ($gabunganMk as $kode) {
            $namaMkQuery = DB::table('mks')
                ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->where('mks.kode', $kode);

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $namaMkQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $namaMkQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $namaMkQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }
            $namaMk = $namaMkQuery->value('mks.nama');
            $courseArray[$kode] = $namaMk;
        }
        // return $courseArray;
        // return $courseArray;

        // Ambil semua label cpl
        $labelCplQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.kode', 'cpls.judul');
        // return $labelCpl;

        $profilCplInfoQuery = DB::table('profil_cpl')
            ->join('prodi', 'profil_cpl.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            //    ->join('profil_lulusan', 'profil_cpl.idProfil', '=', 'profil_lulusan.id')
            ->join('profil_lulusan', 'profil_cpl.idProfil', '=', 'profil_lulusan.id')
            ->whereIn('profil_cpl.idCpl', $profilCplKeys)
            ->select(
                'profil_cpl.id',
                'profil_cpl.idProfil',
                'profil_cpl.idCpl',
                'profil_cpl.bobot',
                'profil_lulusan.namaProfil',
                'profil_lulusan.deskripsi'
            );

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $labelCplQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $profilCplInfoQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $labelCplQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            $profilCplInfoQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $labelCplQuery->where('prodi.id', auth()->user()->id_prodiUser);
            $profilCplInfoQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $labelCpl = $labelCplQuery->get();
        $profilCplInfo = $profilCplInfoQuery->get();
        // return $profilCplInfo;
        $hasilFinalProfil = [];

        foreach ($profilCpl as $cpl => $hasilCpl) {
            $profilCplData = $profilCplInfo->where('idCpl', $cpl)->all();

            // Iterasi koleksi
            foreach ($profilCplData as $singleProfilCplData) {
                $idProfil = $singleProfilCplData->idProfil;

                // Inisialisasi array jika belum ada
                if (!isset($hasilFinalProfil[$idProfil])) {
                    $hasilFinalProfil[$idProfil] = [
                        'NamaProfil' => $singleProfilCplData->namaProfil,
                        'Deskripsi' => $singleProfilCplData->deskripsi,
                        'TotalAkhir' => 0,
                        'CPLs' => []
                    ];
                }

                // Ambil data dari tabel cpls pake foreign key idCpl
                $cplInfo = DB::table('cpls')
                    ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                    ->where('cpls.id', $cpl);

                if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                    $cplInfo->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                    $cplInfo->where('fakultas.id', auth()->user()->id_fakultasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                    $cplInfo->where('prodi.id', auth()->user()->id_prodiUser);
                }
                $cplInfo = $cplInfo->first();
                $kode = $cplInfo->kode;

                // Mengalikan HasilCpl dengan bobot
                $bobot = $singleProfilCplData->bobot;
                $total = $hasilCpl * $bobot;

                // Menambahkan hasil ke dalam array
                $hasilFinalProfil[$idProfil]['CPLs'][] = [
                    'CPL' => $kode,
                    'Bobot' => $bobot,
                    'HasilCPL' => $hasilCpl,
                    'Total' => round($total, 2), // Membulatkan total menjadi dua angka di belakang koma
                ];

                // Menambahkan total ke dalam TotalAkhir
                $hasilFinalProfil[$idProfil]['TotalAkhir'] = round($hasilFinalProfil[$idProfil]['TotalAkhir'] + $total, 2);
            }
        }

        // return $hasilFinalProfil;
        usort($hasilFinalProfil, function ($a, $b) {
            return strcmp($a['NamaProfil'], $b['NamaProfil']);
        });

        // BUAT BAR CHART DATA
        $chartDataProfil = collect($hasilFinalProfil)->map(function ($profil) {
            return [
                'label' => $profil['NamaProfil'],
                'data' => number_format($profil['TotalAkhir'], 2, '.', ''),
            ];
        })->sortBy('label')->values()->all();
        // return $chartDataProfil;

        $subQueryMaxCPMK = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('CPMK', DB::raw("MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester"))
            ->where('mutus.NPM', $npm);

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $subQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $subQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $subQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $subQueryMaxCPMK->groupBy('mutus.CPMK');
        $nilaiCpmk = DB::table('mutus as m')
            ->joinSub($subQueryMaxCPMK, 't', function ($join) {
                $join->on('m.CPMK', '=', 't.CPMK')
                    ->on(DB::raw("CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)"), '=', 't.max_year_semester');
            })
            ->where('m.NPM', $npm)
            ->select('m.CPMK', DB::raw('ROUND(SUM(m.examWeight / 100 * m.Nilai), 2) as total_nilai'))
            ->groupBy('m.CPMK')
            ->get();

        $relasiCpmkProfesi = DB::table('profesi_cpmk')
            ->join('profesi', 'profesi_cpmk.profesi_id', '=', 'profesi.id')
            ->join('cpmks', 'profesi_cpmk.cpmk_id', '=', 'cpmks.id')
            ->select('profesi_cpmk.cpmk_id', 'profesi_cpmk.profesi_id', 'profesi.nama','cpmks.judul','cpmks.kode')
            ->get();

        $hasilProfesi = [];
        
        foreach ($relasiCpmkProfesi->groupBy('profesi_id') as $profesi_id => $items) {
            $totalNilai = 0;
            $jumlahCpmk = 0;
            $cpmkList = [];

            foreach ($items as $item) {
                $cpmkId = $item->cpmk_id;
                $cpmkValue = $nilaiCpmk->firstWhere('CPMK', $cpmkId);
                if ($cpmkValue) {
                    $totalNilai += $cpmkValue->total_nilai;
                    $jumlahCpmk++;
                    $cpmkList[] = [
                        'CPMK' => $item->kode,
                        'deskripsi' => $item->judul,
                        'HasilCPMK' => $cpmkValue->total_nilai
                    ];
                }
            }

            if ($jumlahCpmk > 0) {
                $persentase = ($totalNilai / $jumlahCpmk);
                $hasilProfesi[] = [
                    'id' => $profesi_id,
                    'nama' => $items->first()->nama,
                    'persentase' => round($persentase, 2),
                    'CPMKs' => $cpmkList,
                    'TotalAkhir' => round($persentase, 3)
                ];
            }
        }

        $top4 = collect($hasilProfesi)
            ->sortByDesc('persentase')
            ->take(4)
            ->values();

        $chartProfesi = $top4->map(function ($item) {
            return [
                'label' => $item['nama'],
                'data' => $item['persentase']
            ];
        });

        // Course untuk lihat cpmk berdasarkan matkul yg diampuh dosen kalo kaprodi bebas
        $userNameLogin = auth()->user()->name;
        $userJabatanLogin = auth()->user()->jabatan;

        $courseCpmkRestrict = $courseArray;
        // klo jdi restrik
        // if ($userJabatanLogin == "Kaprodi") {
        //     $courseCpmkRestrict = $courseArray;
        // } else {
        //     $courseCpmkPj       = DB::table('rpss')
        //         ->select('kode_mk', 'pengembang')
        //         ->where('pengembang', $userNameLogin)
        //         ->get();
        //     $courseCpmkRestrict = [];

        //     foreach ($courseCpmkPj as $item) {
        //         // Periksa apakah kode_mk ada dalam $courseArray
        //         if (isset($courseArray[$item->kode_mk])) {
        //             // Jika ada, tambahkan ke $courseCpmkRestrict dengan kunci dan nilai yang sama
        //             $courseCpmkRestrict[$item->kode_mk] = $courseArray[$item->kode_mk];
        //         }
        //     }
        // }

        // TODO:Soal terendah
        $minCpl = collect($dictionaryCpl)->min();
        $minCplKey = collect($dictionaryCpl)->search($minCpl);
        // return $minCplKey;

        // Joinkan tabel bila soal deskripsi tidak ada ambil dari idsoal di tabel soal
        $soalDescQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->select('mutus.id', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
            ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
            ->where('mutus.npm', $npm)
            ->where('mutus.cpl', $minCplKey)
            ->distinct();

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $soalDescQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $soalDescQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $soalDescQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $soalDesc = $soalDescQuery->get();
        // return $soalDesc;    
        //kelompokin 1 array membuang soal null
        $uniqueData = [];

        foreach ($soalDesc as $result) {
            // cek untuk isi atribut soal, buang yang kosong
            $soal = !empty($result->soal) ? $result->soal : $result->soalFromId;

            // kunci unik(namaCourse, jenis, soal)
            $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;

            // Tambahkan elemen ke array asosiatif jika belum ada
            if (!isset($uniqueData[$uniqueKey])) {
                $uniqueData[$uniqueKey] = [
                    'soal' => $soal,
                    'id' => $result->id,
                    'Jenis' => $result->Jenis,
                    'namaCourse' => $result->namaCourse,
                    'idSoal' => $result->idSoal,
                ];
            }
        }
        $soalTerendah = array_values($uniqueData);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diolah',
            'result' => [
                'angkatan' => $angkatan,
                'npm' => $npm,
                'labelCpl' => $labelCpl,
                'nama' => $nama,
                'prodi' => $prodiNama,
                'is_aptikom' => auth()->user()->prodi->is_aptikom,
                'courseArray' => $courseArray,
                'soalTerendah' => $soalTerendah,
                'hasilFinalProfil' => $hasilFinalProfil,
                'chartDataProfil' => $chartDataProfil,
                'hasilFinalProfesi' => $hasilProfesi,
                'chartDataProfesi' => $chartProfesi,
                'courseCpmkRestrict' => $courseCpmkRestrict,
                'universitas' => $universitasNama,
                'imgSrc' => $imgSrc,
                // REVISI
                'nilaiMkLulus' => $nilaiMkLulus,
                'nilaiMkTidakLulus' => $nilaiMkTidakLulus,
                'persentaseTotalCplCapaian' => $persentaseTotalCplCapaian,
                'cplResultsAll' => $cplResultsAll,
                'cplTmp' => $cplTmp,
                'allCplPerAngkatan' => $allCplPerAngkatan,
                'cplPerSoalWithKodeAll' => $cplPerSoalWithKodeAll
            ],
            'showVisualContainer' => true
        ]);
    }

    public function generatePDFhasilVisualMahasiswa(Request $request)
    {
        $data = [
            'nama' => $request->nama,
            'npm' => $request->npm,
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
            'courseList' => $request->courseList,
            'radarChartCapaianCplImg' => $request->radarChartCapaianCplImg,
            'radarChartImg' => $request->radarChartImg,
            'profilChartImg' => $request->profilChartImg,
            'soalTerendah' => $request->soalTerendah,
            'hasilProfil' => $request->hasilProfil,
            'mataKuliahLulus' => $request->mataKuliahLulus ?? [],
            'mataKuliahTidakLulus' => $request->mataKuliahTidakLulus ?? [],
            'descriptions' => $request->descriptions ?? []
        ];

        $pdf = SnappyPdf::loadView('pdf.reportVisualisasiMahasiswa', $data)
            ->setPaper('a4')
            ->setOrientation('portrait')
            ->setOption('margin-left', 12.7)
            ->setOption('margin-right', 12.7)
            ->setOption('margin-top', 12.7)
            ->setOption('margin-bottom', 12.7)
            ->setOption('title', "Laporan Visualisasi Mahasiswa - {$request->nama}")
            ->setOption('dpi', 300);

        return $pdf->download("Laporan Visualisasi Mahasiswa - {$request->nama}.pdf");
    }

    public function generatePDFhasilVisualAngkatan(Request $request)
    {
        $data = [
            'radarChartAngkatanImg' => $request->radarChartAngkatanImg,
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah,
            'mahasiswaAngkatan' => $request->mahasiswaAngkatan,
            'cplCodes' => $request->cplCodes,
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
            'courseList' => $request->courseList,
        ];

        $pdf = SnappyPdf::loadView('pdf.reportVisualisasiAngkatan', $data)
            ->setPaper('A4', 'portrait')
            ->setOption('margin-left', 12.7)
            ->setOption('margin-right', 12.7)
            ->setOption('margin-top', 12.7)
            ->setOption('margin-bottom', 12.7)
            ->setOption('title', "Laporan Visualisasi Angkatan - {$request->angkatan}")
            ->setOption('dpi', 300);

        return $pdf->download("Laporan Visualisasi Angkatan - {$request->angkatan}.pdf");
    }

    public function generatePDFhasilVisualMataKuliah(Request $request)
    {
        $data = [
            'radarChartAngkatanImg' => $request->radarChartAngkatanImg,
            'summary' => $request->summary,
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah,
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
            'course' => $request->course,
        ];

        $pdf = SnappyPdf::loadView('pdf.reportVisualisasiMataKuliah', $data)
            ->setPaper('A4', 'portrait')
            ->setOption('margin-left', 12.7)
            ->setOption('margin-right', 12.7)
            ->setOption('margin-top', 12.7)
            ->setOption('margin-bottom', 12.7)
            ->setOption('title', "Laporan Visualisasi Mata Kuliah - {$request->course}")
            ->setOption('dpi', 300);

        return $pdf->download("Laporan Visualisasi Mata Kuliah - {$request->course}.pdf");
    }

    public function generatePDFhasilVisualCpmkMahasiswa(Request $request)
    {
        $data = [
            'course' => $request->course,
            'radarChartImg' => $request->radarChartImg,
            'summary' => $request->summary,
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah,
            'nama' => $request->nama,
            'npm' => $request->npm,
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
        ];

        $pdf = SnappyPdf::loadView('pdf.reportVisualisasiCPMKMahasiswa', $data)
            ->setPaper('A4', 'portrait')
            ->setOption('margin-left', 12.7)
            ->setOption('margin-right', 12.7)
            ->setOption('margin-top', 12.7)
            ->setOption('margin-bottom', 12.7)
            ->setOption('title', "Laporan Visualisasi CPMK {$request->course} Mahasiswa - {$request->nama}")
            ->setOption('dpi', 300);

        return $pdf->download("Laporan Visualisasi CPMK {$request->course} Mahasiswa - {$request->nama}.pdf");
    }

    public function generatePDFhasilVisualCpmkAngkatan(Request $request)
    {
        $data = [
            'course' => $request->course,
            'radarChartAngkatanImg' => $request->radarChartAngkatanImg,
            'summary' => $request->summary,
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah,
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
        ];

        $pdf = SnappyPdf::loadView('pdf.reportVisualisasiCPMKAngkatan', $data)
            ->setPaper('A4', 'portrait')
            ->setOption('margin-left', '12.7mm')
            ->setOption('margin-right', '12.7mm')
            ->setOption('margin-top', '12.7mm')
            ->setOption('margin-bottom', '12.7mm')
            ->setOption('title', "Laporan Visualisasi CPMK {$request->course} Angkatan - {$request->nama}")
            ->setOption('dpi', 300);

        return $pdf->download("Laporan Visualisasi CPMK {$request->course} Angkatan - {$request->angkatan}.pdf");
    }

    public function hasilVisualCpmkMahasiswa(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        // dd($request);
        $universitas = $request->universitasCPMK;
        $universitasImg = $request->universitasImg;

        // dd($allNamaNpmData);
        $npm = $request->npm;
        $nama = $request->nama;
        $prodi = $request->prodi;
        $angkatan = $request->angkatan;
        $originalReqCourse = $request->course;
        list($course, $namaCourse) = explode('-', $originalReqCourse);
        // dd($course);
        $prodiId = Prodi::where('nama', $prodi)->value('id');
        $universitasId = Universitas::where('nama', $universitas)->value('id');

        $allNpm = DB::table('mutus')
            ->select('npm')
            ->where('angkatan', $angkatan)
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitasId)
            ->distinct()
            ->pluck('npm');

        $allNamaNpmData = DB::table('mutus')
            ->whereIn('Npm', $allNpm)
            ->where('universitas_id', $universitasId)
            ->select('nama_mhs', 'NPM')
            ->distinct()
            ->get();

        // CPMK PER SOAL MAHASISWA
        $query = "
                SELECT m.cpmk, 
                    SUM((m.BobotSoal * m.nilaiSoal) / q1.BobotJenisCPMK * m.examWeight / q2.sumExamWeight) AS r3, 
                    cpmks.kode
                FROM mutus m
                INNER JOIN (
                    SELECT Course, 
                        MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), 
                                CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester
                    FROM mutus
                    WHERE NPM = :npm1 AND Course = :course1
                ) t ON m.Course = t.Course 
                AND CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), 
                    CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END) = t.max_year_semester
                JOIN (
                    SELECT Cpmk, Jenis, tahun, SUM(BobotSoal) AS BobotJenisCPMK
                    FROM mutus
                    WHERE NPM = :npm2 AND Course = :course2
                    GROUP BY Cpmk, Jenis, tahun
                ) q1 ON m.cpmk = q1.Cpmk AND m.Jenis = q1.Jenis AND m.tahun = q1.tahun
                JOIN (
                    SELECT tahun, Cpmk, SUM(examWeight) AS sumExamWeight
                    FROM (
                        SELECT DISTINCT tahun, Cpmk, Jenis, examWeight
                        FROM mutus
                        WHERE NPM = :npm3 AND Course = :course3
                    ) AS subquery
                    GROUP BY tahun, Cpmk
                ) q2 ON q1.cpmk = q2.Cpmk AND q1.tahun = q2.tahun
                JOIN cpmks ON m.cpmk = cpmks.id
                WHERE m.NPM = :npm4 AND m.Course = :course4
                GROUP BY m.cpmk, cpmks.kode;
            ";

        $cpmkPerSoalWithKode = DB::select($query, [
            'npm1' => $npm,
            'course1' => $course,
            'npm2' => $npm,
            'course2' => $course,
            'npm3' => $npm,
            'course3' => $course,
            'npm4' => $npm,
            'course4' => $course
        ]);

        // CPMK PER SOAL ANGKATAN
        $queryCpmkAngkatan = "
                SELECT 
                    sub.cpmk, 
                    cpmks.kode,
                    MIN(sub.r3) AS min_r3,
                    MAX(sub.r3) AS max_r3,
                    AVG(sub.r3) AS avg_r3
                FROM (
                    SELECT 
                        m.NPM,
                        m.cpmk, 
                        SUM((m.BobotSoal * m.nilaiSoal) / q1.BobotJenisCPMK * m.examWeight / q2.sumExamWeight) AS r3
                    FROM 
                        mutus m 
                    JOIN 
                        (SELECT 
                            NPM, 
                            MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), 
                                CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester
                        FROM 
                            mutus
                        WHERE 
                            Course = :course1
                        GROUP BY 
                            NPM
                        ) AS ly
                        ON m.NPM = ly.NPM
                        AND CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), 
                            CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END) = ly.max_year_semester
                    JOIN 
                        (SELECT 
                            NPM, 
                            Cpmk, 
                            Jenis, 
                            tahun, 
                            SUM(BobotSoal) AS BobotJenisCPMK 
                        FROM 
                            mutus 
                        WHERE 
                            Course = :course2 
                        GROUP BY 
                            NPM, Cpmk, Jenis, tahun
                        ) AS q1
                        ON m.NPM = q1.NPM 
                        AND m.cpmk = q1.Cpmk 
                        AND m.Jenis = q1.Jenis 
                        AND m.tahun = q1.tahun
                    JOIN 
                        (SELECT 
                            NPM, 
                            tahun, 
                            Cpmk, 
                            SUM(examWeight) AS sumExamWeight 
                        FROM 
                            (SELECT DISTINCT 
                                NPM, 
                                tahun, 
                                Cpmk, 
                                Jenis, 
                                examWeight 
                            FROM 
                                mutus 
                            WHERE  
                                Course = :course3
                            ) AS subquery 
                        GROUP BY 
                            NPM, tahun, Cpmk
                        ) AS q2 
                        ON q1.NPM = q2.NPM 
                        AND q1.cpmk = q2.Cpmk 
                        AND q1.tahun = q2.tahun
                    WHERE 
                        m.Course = :course4
                    GROUP BY 
                        m.NPM, m.cpmk
                ) AS sub 
                JOIN 
                    cpmks 
                ON 
                    sub.cpmk = cpmks.id
                GROUP BY 
                    sub.cpmk, cpmks.kode
            ";

        $cpmkPerSoalAngkatanWithKode = DB::select($queryCpmkAngkatan, [
            'course1' => $course,
            'course2' => $course,
            'course3' => $course,
            'course4' => $course
        ]);
        // dd($cpmkPerSoalAngkatanWithKode);

        // Ngisi CPMK SEMENTARA
        $cpmkResultAllQuery = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('mk_kode', $course)
            ->select('cpmk_mk.cpmk_id as id', 'cpmks.judul', 'cpmks.kode');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cpmkResultAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cpmkResultAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cpmkResultAllQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $cpmkResultAll = $cpmkResultAllQuery->get();
        // dd($cpmkResultAll);
        $cpmkTmp = [];
        foreach ($cpmkResultAll as $itemCpmk) {
            $cpmkTmp[$itemCpmk->id] = [0, 0, 0, 0, $itemCpmk->kode];
        }
        // dd($cpmkTmp);
        // dd($cpmkPerSoalAngkatanWithKode);
        // Buat summary
        $minCpmk = 101;
        $keyMinCpl = 0;
        $kodeMinCpmk = '';
        $maxCpmk = 0;
        $kodeMaxCpmk = '';
        foreach ($cpmkPerSoalWithKode  as $itemCpmk) {
            if ($itemCpmk->r3 < $minCpmk) {
                $minCpmk = number_format($itemCpmk->r3, 2);
                $keyMinCpl = $itemCpmk->cpmk;
                $kodeMinCpmk = $itemCpmk->kode;
            }
            if ($itemCpmk->r3 > $maxCpmk) {
                $maxCpmk = number_format($itemCpmk->r3, 2);
                $keyMaxCpl = $itemCpmk->cpmk;
                $kodeMaxCpmk = $itemCpmk->kode;
            }
            $cpmkTmp[$itemCpmk->cpmk][0] = $itemCpmk->r3;
        }

        $minAvg = 101;
        $keyMinAvg = 0;
        foreach ($cpmkPerSoalAngkatanWithKode  as $itemCpmk) {
            if ($itemCpmk->avg_r3 < $minAvg) {
                $minAvg = $itemCpmk->avg_r3;
                $keyMinAvg = $itemCpmk->cpmk;
            }
            $cpmkTmp[$itemCpmk->cpmk][1] = $itemCpmk->avg_r3;
            $cpmkTmp[$itemCpmk->cpmk][2] = $itemCpmk->min_r3;
            $cpmkTmp[$itemCpmk->cpmk][3] = $itemCpmk->max_r3;
        }
        // dd($keyMinAvg);


        // return $cpmkPerSoalAngkatanWithKode;

        // Joinkan tabel bila soal deskripsi tidak ada ambil dari idsoal di tabel soal
        $soalDescQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->select('mutus.id', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId', 'mutus.cpmk')
            ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
            ->where('mutus.npm', $npm)
            ->where('mutus.cpmk', $keyMinCpl)
            ->where('mutus.Course', $course)
            ->where('mutus.universitas_id', $universitasId)
            ->distinct();

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $soalDescQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $soalDescQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $soalDescQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $soalDesc = $soalDescQuery->get();
        // dd($soalDesc);    
        //kelompokin 1 array membuang soal null
        $uniqueData = [];
        foreach ($soalDesc as $result) {
            // cek untuk isi atribut soal, buang yang kosong
            $soal = !empty($result->soal) ? $result->soal : $result->soalFromId;
            // kunci unik(namaCourse, jenis, soal)
            $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;

            // Tambahkan elemen ke array asosiatif jika belum ada
            if (!isset($uniqueData[$uniqueKey])) {
                $uniqueData[$uniqueKey] = [
                    'soal' => $soal,
                    'id' => $result->id,
                    'Jenis' => $result->Jenis,
                    'namaCourse' => $result->namaCourse,
                    'idSoal' => $result->idSoal,
                ];
            }
        }
        $soalTerendah = array_values($uniqueData);
        // dd($cpmkTmp);
        return view('penjamin-mutu.visualisasi.hasilVisualisasiCpmkMahasiswa', [
            'nama' => $nama,
            'npm' => $npm,
            'prodi' => $prodi,
            'angkatan' => $angkatan,
            'completeCourseFormat' => $originalReqCourse,
            'allNamaNpmData' => $allNamaNpmData,
            'allNpm' => $allNpm,
            'universitas' => $universitas,
            'universitasImg' => $universitasImg,
            // ABis Semhas
            'cpmkTmp' => $cpmkTmp,
            'cpmkResultAll' => $cpmkResultAll,
            'soalTerendah' => $soalTerendah,
            'kodeMaxCpmk' => $kodeMaxCpmk,
            'maxCpmk' => $maxCpmk,
            'kodeMinCpmk' => $kodeMinCpmk,
            'minCpmk' => $minCpmk,

        ]);
    }

    // TODO: SIDEBAR ANGKATAN
    public function indexAngkatan()
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $userUniversitasId = auth()->user()->id_universitasUser;

        $universitas = Universitas::find($userUniversitasId);

        $prodiQuery = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('prodi.id', 'prodi.nama');

        // Apply access restrictions based on user role
        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $prodiQuery->where('fakultas.id_universitas', $userUniversitasId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $prodiQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $prodiQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $prodi = $prodiQuery->get();

        return view('penjamin-mutu.visualisasi.indexVisualisasiAngkatan', compact('universitas', 'prodi'));
    }

    public function hasilVisualMahasiswaAngkatan(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        // return $request;
        $prodiId = $request->input('prodi');
        $prodi = Prodi::where('id', $prodiId)->get();
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');
        $univNama = Universitas::where('id', $universitas)->value('nama');
        $imgSrc = $request->input('imgSrc');

        // CPL ANGKATAN YANG INI YG BENER
        $allNpm = DB::table('mutus')
            ->select('npm')
            ->where('angkatan', $angkatan)
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->distinct()
            ->pluck('npm');

        $persentaseTotalCplCapaianAngkatan = [];

        $allCplPerAngkatanQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $allCplPerAngkatanQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $allCplPerAngkatanQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $allCplPerAngkatanQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $allCplPerAngkatanQuery = $allCplPerAngkatanQuery->get();
        $allCplPerAngkatan = [
            'total' => [],
            'min' => [],
            'max' => []
        ];
        // $allCplPerAngkatan = [];
        // $allCplPerAngkatan['total'] = [];
        foreach ($allCplPerAngkatanQuery as $cpl) {
            $allCplPerAngkatan['total'][$cpl->kode] = 0;
            $allCplPerAngkatan['min'][$cpl->kode] = 101;
            $allCplPerAngkatan['max'][$cpl->kode] = 0;
        }

        $allCplPerAngkatan['count'] = 0;
        $gabunganMkAngkatan = [];

        // LOOPING CAPAIAN CPL PER ANGKATAN
        foreach ($allNpm as $npm) {
            // Sama seperti perhitungan persentaseTotalCplCapaian sebelumnya untuk setiap npm
            $subQuery = DB::table('mutus')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->select('Course', DB::raw("MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester"))
                ->where('mutus.NPM', $npm);

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $subQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $subQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $subQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $subQuery = $subQuery->groupBy('mutus.Course');

            // Main query to get the records with the max year and semester
            $subQueryMutus = DB::table('mutus as m')
                ->joinSub(
                    $subQuery,
                    't',
                    function ($join) {
                        $join->on(
                            'm.Course',
                            '=',
                            't.Course'
                        )
                            ->on(DB::raw("CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)"), '=', 't.max_year_semester');
                    }
                )
                ->where('m.NPM', $npm)
                ->groupBy('m.npm', 'm.Course', 'm.jenis')
                ->select(
                    'm.npm',
                    'm.Course',
                    'm.jenis',
                    "m.id"
                );

            // Gabungkan subquery dengan tabel mutus untuk menghitung total nilai berdasarkan bobot ujian dan nilai
            $nilaiMk = DB::table('mutus')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->joinSub($subQueryMutus, 'sub', function ($join) {
                    $join->on('mutus.id', '=', 'sub.id');
                })
                ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'));

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $nilaiMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $nilaiMk->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $nilaiMk->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $nilaiMk = $nilaiMk->groupBy('mutus.Course')->get();
            // Ambil semua nama MK dalam satu query untuk efisiensi
            $namaMkList = DB::table('mks')
                ->pluck('nama', 'kode'); // Menghasilkan array asosiatif [kode => nama]
            $nilaiMkLulusAngkatan = [];
            $nilaiMkTidakLulusAngkatan = [];

            foreach ($nilaiMk as $nilai) {
                $namaMk = $namaMkList[$nilai->Course] ?? 'N/A';
                if ($nilai->total_nilai >= 50) {
                    $nilaiMkLulusAngkatan[$nilai->Course] = [$nilai->total_nilai, $namaMk];
                } else {
                    $nilaiMkTidakLulusAngkatan[$nilai->Course] = [$nilai->total_nilai, $namaMk];
                }
            }
            $namaMkLulus = array_keys($nilaiMkLulusAngkatan);
            $namaMkTidakLulus = array_keys($nilaiMkTidakLulusAngkatan);
            $gabunganMkAngkatan = array_merge($gabunganMkAngkatan, $namaMkLulus, $namaMkTidakLulus);

            $cplNilaiMkAngkatanQuery = DB::table('mutus')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->select('Course', 'Cpl', DB::raw('SUM(examWeight / 100 * Nilai) as total_nilaiCek'))
                ->where('npm', $npm)
                ->whereIn('Course', array_keys($nilaiMkLulusAngkatan));

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $cplNilaiMkAngkatanQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $cplNilaiMkAngkatan = $cplNilaiMkAngkatanQuery->groupBy('mutus.Course', 'mutus.Cpl')->get();

            $cplNilaiMkUnique = $cplNilaiMkAngkatan->pluck('Cpl')->unique()->toArray();

            $cplResultsQuery = DB::table('mk_cpl')
                ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->whereIn('mk_cpl.cpl_id', $cplNilaiMkUnique);

            $cplResultsAllQuery = DB::table('cpls')
                ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplResultsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                $cplResultsAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplResultsQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
                $cplResultsAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $cplResultsQuery->where('prodi.id', auth()->user()->id_prodiUser);
                $cplResultsAllQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $cplResults = $cplResultsQuery->get()->groupBy('cpl_id');
            $cplResultsAll = $cplResultsAllQuery->get(['cpls.id', 'kode']);

            $persentaseTotalCplCapaianIndividu = [];

            foreach ($cplResults as $id_cpl => $items) {
                $kode_mk_list = $items->pluck('mk_kode')->toArray();
                $kodeMkArray = explode(',', implode(',', $kode_mk_list));
                $countKesamaan = count(array_intersect($kodeMkArray, array_keys($nilaiMkLulusAngkatan)));
                $persentase = ($countKesamaan / count($items)) * 100;

                $cplCode = DB::table('cpls')
                    ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                    ->where('cpls.id', $id_cpl);

                if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                    $cplCode->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                    $cplCode->where('fakultas.id', auth()->user()->id_fakultasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                    $cplCode->where('prodi.id', auth()->user()->id_prodiUser);
                }

                // Ambil nilai kode CPL setelah filter diterapkan
                $cplCode = $cplCode->value('cpls.kode');

                $persentaseTotalCplCapaianIndividu[] = [
                    'cpl' => $id_cpl,
                    'kode_mk' => implode(',', $kode_mk_list),
                    'count_mk' => count($items),
                    'persentase' => round($persentase, 2),
                    'kode_cpl' => $cplCode,
                ];

                $allCplPerAngkatan['total'][$cplCode] += $persentase;

                // MIN MAX
                if ($allCplPerAngkatan['min'][$cplCode] >= round($persentase, 2)) {
                    $allCplPerAngkatan['min'][$cplCode] = round($persentase, 2);
                }
                if ($allCplPerAngkatan['max'][$cplCode] <= round($persentase, 2)) {
                    $allCplPerAngkatan['max'][$cplCode] = round($persentase, 2);
                }
            }

            $allCplPerAngkatan['count'] += 1;

            $persentaseTotalCplCapaianAngkatan[] = $persentaseTotalCplCapaianIndividu;
        }
        $keyKodeCplAvgAngkatanMin = '';
        $cplAvgAngkatanMin = 101;
        $keyKodeCplAvgAngkatanMax = '';
        $cplAvgAngkatanMax = 0;
        foreach ($allCplPerAngkatan['total'] as $cpl => $total) {
            $allCplPerAngkatan['avg_cpl'][$cpl] = round($total / $allCplPerAngkatan['count'], 2);
            if ($allCplPerAngkatan['avg_cpl'][$cpl] < $cplAvgAngkatanMin && $allCplPerAngkatan['avg_cpl'][$cpl] > 0) {
                $keyKodeCplAvgAngkatanMin = $cpl;
                $cplAvgAngkatanMin = $allCplPerAngkatan['avg_cpl'][$cpl];
            }
            if ($allCplPerAngkatan['avg_cpl'][$cpl] > $cplAvgAngkatanMax) {
                $keyKodeCplAvgAngkatanMax = $cpl;
                $cplAvgAngkatanMax = $allCplPerAngkatan['avg_cpl'][$cpl];
            }
            if ($allCplPerAngkatan['min'][$cpl] > 100) {
                $allCplPerAngkatan['min'][$cpl] = 0;
            }
        }
        // return $keyKodeCplAvgAngkatanMax;
        // Buat ditampilin course perhitungan dan sumbit untuuk liat cpmk angkatan
        $gabunganMkAngkatan = array_unique($gabunganMkAngkatan);

        $gabunganAkhirMkQuery = DB::table('mks')
            ->join('prodi', 'mks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->whereIn('kode', $gabunganMkAngkatan);
        // return $gabunganAkhirMk;
        // Semua label cpl
        $labelCplQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.kode', 'cpls.judul');
        // Buat milih cpl terkait sama mk apa
        $cplResultsAllQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');
        // return $minCplAngkatan;
        // BUAT RATA-RATA CPL TERENDAH
        $idCplAvgMinQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id')
            ->where('cpls.kode', '=', $keyKodeCplAvgAngkatanMin);

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $gabunganAkhirMkQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $labelCplQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $cplResultsAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            $idCplAvgMinQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $gabunganAkhirMkQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            $labelCplQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            $cplResultsAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            $idCplAvgMinQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $gabunganAkhirMkQuery->where('prodi.id', auth()->user()->id_prodiUser);
            $labelCplQuery->where('prodi.id', auth()->user()->id_prodiUser);
            $cplResultsAllQuery->where('prodi.id', auth()->user()->id_prodiUser);
            $idCplAvgMinQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $gabunganAkhirMk = $gabunganAkhirMkQuery->pluck('mks.nama', 'mks.kode')->toArray();
        $labelCpl = $labelCplQuery->get();
        $cplResultsAll = $cplResultsAllQuery->get(['cpls.id', 'cpls.kode']);
        $idCplAvgMin = $idCplAvgMinQuery->first();

        $idCplAvgMinInt = $idCplAvgMin ? (int)$idCplAvgMin->id : null;
        $soalDescQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->select('mutus.id', 'mutus.cpl', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
            ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
            ->where('mutus.angkatan', $angkatan)
            ->where('mutus.id_prodi', $prodiId)
            ->where('mutus.cpl', $idCplAvgMinInt)
            ->where('universitas_id', $universitas)
            ->distinct();
        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $soalDescQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $soalDescQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $soalDescQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $soalDesc = $soalDescQuery->get();
        // return $soalDesc;
        $uniqueData = [];

        foreach ($soalDesc as $result) {
            // cek untuk isi atribut soal, buang yang kosong
            $soal = !empty($result->soal) ? $result->soal : $result->soalFromId;
            // kunci unik(namaCourse, jenis, soal)
            $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;
            // Tambahkan elemen ke array asosiatif jika belum ada
            if (!isset($uniqueData[$uniqueKey])) {
                $uniqueData[$uniqueKey] = [
                    'soal' => $soal,
                    'id' => $result->id,
                    'Jenis' => $result->Jenis,
                    'namaCourse' => $result->namaCourse,
                    'idSoal' => $result->idSoal,
                ];
            }
        }

        $soalTerendah = array_values($uniqueData);

        // return $allCplPerAngkatan;
        // MAHASISWA ANGKATAN START
        // Mendapatkan daftar mahasiswa dengan NPM dan nama terlebih dahulu
        $mahasiswaList = Mutu::query()
            ->select('npm', 'nama_mhs')
            ->where('angkatan', $angkatan)
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->groupBy('npm', 'nama_mhs')
            ->orderby('npm')
            ->get();

        // Fetch all CPLs for this program
        $allCpls = DB::table('cpls')
            ->where('id_prodi', $prodiId)
            ->orderBy('id')
            ->get();

        // Membuat array untuk menyimpan hasil akhir
        $mahasiswaAngkatan = [];

        // Untuk setiap mahasiswa, hitung skor CPL
        foreach ($mahasiswaList as $mahasiswa) {
            $npm = $mahasiswa->npm;

            // Query untuk mendapatkan CPL dan skornya untuk mahasiswa ini
            $query = "
            SELECT cpl, cpls.kode as kode, AVG(hasil) as hasil FROM (
                SELECT Cpl, Course, SUM(hasil) as hasil 
                FROM (
                    SELECT q1.tahun, q1.Cpl, q1.Jenis, q1.Course, 
                        (q1.nilaiSoal * q1.BobotSoal / q2.BobotJenis) * q1.examWeight / q3.sumExamWeight as hasil 
                    FROM (
                        SELECT m.tahun, m.Cpl, m.Jenis, m.Course, m.nilaiSoal, m.examWeight, m.BobotSoal 
                        FROM mutus m 
                        JOIN (
                            SELECT Course, MAX(CONCAT(
                                SUBSTRING_INDEX(tahun, ' ', -1), 
                                CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END 
                            )) AS max_year_semester 
                            FROM mutus 
                            WHERE NPM = :npm1 
                            GROUP BY Course 
                        ) t 
                        ON m.Course = t.Course AND CONCAT(
                            SUBSTRING_INDEX(m.tahun, ' ', -1), 
                            CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END 
                        ) = t.max_year_semester 
                        WHERE m.NPM = :npm2
                    ) q1 
                    JOIN (
                        SELECT Cpl, Jenis, Course, tahun, SUM(BobotSoal) as BobotJenis 
                        FROM mutus 
                        WHERE NPM = :npm3 
                        GROUP BY Cpl, Jenis, Course, tahun 
                    ) q2 
                    ON q1.tahun = q2.tahun AND q1.Cpl = q2.Cpl AND q1.Course = q2.Course AND q1.Jenis = q2.Jenis 
                    JOIN (
                        SELECT tahun, Cpl, Course, SUM(examWeight) AS sumExamWeight 
                        FROM (
                            SELECT DISTINCT tahun, Cpl, Course, Jenis, examWeight 
                            FROM mutus 
                            WHERE NPM = :npm4
                        ) AS subquery 
                        GROUP BY tahun, Cpl, Course
                    ) q3 
                    ON q1.tahun = q3.tahun AND q1.Cpl = q3.Cpl AND q1.Course = q3.Course
                ) q4 
                GROUP BY Cpl, Course
            ) q5 
            JOIN cpls ON cpls.id = cpl
            JOIN prodi ON cpls.id_prodi = prodi.id
            JOIN fakultas ON prodi.id_fakultas = fakultas.id
            WHERE prodi.id = :id_prodi 
            GROUP BY Cpl
            ORDER BY Cpl
        ";

            $cplResults = DB::select($query, [
                'npm1' => $npm,
                'npm2' => $npm,
                'npm3' => $npm,
                'npm4' => $npm,
                'id_prodi' => $prodiId,
            ]);

            // Create array to store CPL results indexed by CPL ID
            $cplScores = [];
            foreach ($cplResults as $cplResult) {
                $cplScores[$cplResult->cpl] = $cplResult;
            }

            // For each CPL, create entry for this student (with score 0 if no score exists)
            foreach ($allCpls as $cpl) {
                $cplId = $cpl->id;
                $mahasiswaAngkatan[] = [
                    'npm' => $npm,
                    'nama_mhs' => $mahasiswa->nama_mhs,
                    'kode' => $cpl->kode,
                    'hasil' => isset($cplScores[$cplId]) ? $cplScores[$cplId]->hasil : 0,
                ];
            }
        }

        // Konversi array ke collection jika perlu
        $mahasiswaAngkatan = collect($mahasiswaAngkatan);
        // MAHASISWA ANGKATAN END

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diolah',
            'result' => [
                'angkatan' => $angkatan,
                'prodi' => $prodi,
                'imgSrc' => $imgSrc,
                'universitas' => $univNama,
                'mahasiswaAngkatan' => $mahasiswaAngkatan,
                'labelCpl' => $labelCpl,
                'soalTerendah' => $soalTerendah,
                'allCplPerAngkatan' => $allCplPerAngkatan,
                'gabunganAkhirMk' => $gabunganAkhirMk,
                'cplResultsAll' => $cplResultsAll
            ],
            'showVisualContainer' => true
        ]);
    }

    public function getAngkatanByProdiUniversitas(Request $request)
    {
        $prodiId = $request->input('prodi');
        $universitas = $request->input('universitas');
        // ambil npm berdasarkan angkatan dari ajax
        $angkatanData = DB::table('mutus')
            ->select('angkatan')
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->orderBy('angkatan', 'desc')
            ->distinct()
            ->get();
        // return $prodiData;
        // balikin npm ke dropdown
        $options = '<option value="">Pilih Angkatan</option>';
        foreach ($angkatanData as $p) {
            $options .= '<option value="' . $p->angkatan . '">' . $p->angkatan . '</option>';
        }

        return $options;
    }

    public function hasilVisualCpmkAngkatan(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        // return $request;
        // dd($request);
        $angkatan = $request->angkatan;
        $prodi = $request->prodi;
        $prodiId = Prodi::where('nama', $prodi)->value('id');
        $universitas = $request->universitasCPMK;
        $universitasId = Universitas::where('nama', $universitas)->value('id');
        $universitasImg = $request->universitasImg;
        $course = $request->course;

        $mk = DB::table('mks')
            ->where('kode', $course)
            ->select('kode', 'nama')
            ->first();
        // dd($mk);
        $completeCourseFormat = $mk->kode . '-' . $mk->nama;
        // dd($completeCourseFormat);

        $allAngkatan = DB::table('mutus')
            ->select('angkatan')
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitasId)
            ->where('Course', $course)
            ->orderBy('angkatan', 'desc')
            ->distinct()
            ->get();

        $queryCpmkAngkatan = "
                SELECT 
                    sub.cpmk, 
                    cpmks.kode,
                    MIN(sub.r3) AS min_r3,
                    MAX(sub.r3) AS max_r3,
                    AVG(sub.r3) AS avg_r3
                FROM (
                    SELECT 
                        m.NPM,
                        m.cpmk, 
                        SUM((m.BobotSoal * m.nilaiSoal) / q1.BobotJenisCPMK * m.examWeight / q2.sumExamWeight) AS r3
                    FROM 
                        mutus m 
                    JOIN 
                        (SELECT 
                            NPM, 
                            MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), 
                                    CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester
                        FROM 
                            mutus
                        WHERE 
                            Course = :course1 AND angkatan = :angkatan1
                        GROUP BY 
                            NPM
                        ) AS ly
                        ON m.NPM = ly.NPM
                        AND CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), 
                                CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END) = ly.max_year_semester
                    JOIN 
                        (SELECT 
                            NPM, 
                            Cpmk, 
                            Jenis, 
                            tahun, 
                            SUM(BobotSoal) AS BobotJenisCPMK 
                        FROM 
                            mutus 
                        WHERE 
                            Course = :course2 AND angkatan = :angkatan2
                        GROUP BY 
                            NPM, Cpmk, Jenis, tahun
                        ) AS q1
                        ON m.NPM = q1.NPM 
                        AND m.cpmk = q1.Cpmk 
                        AND m.Jenis = q1.Jenis 
                        AND m.tahun = q1.tahun
                    JOIN 
                        (SELECT 
                            NPM, 
                            tahun, 
                            Cpmk, 
                            SUM(examWeight) AS sumExamWeight 
                        FROM 
                            (SELECT DISTINCT 
                                NPM, 
                                tahun, 
                                Cpmk, 
                                Jenis, 
                                examWeight 
                            FROM 
                                mutus 
                            WHERE  
                                Course = :course3 AND angkatan = :angkatan3
                            ) AS subquery 
                        GROUP BY 
                            NPM, tahun, Cpmk
                        ) AS q2 
                        ON q1.NPM = q2.NPM 
                        AND q1.cpmk = q2.Cpmk 
                        AND q1.tahun = q2.tahun
                    WHERE 
                        m.Course = :course4 AND m.angkatan = :angkatan4
                    GROUP BY 
                        m.NPM, m.cpmk
                ) AS sub 
                JOIN 
                    cpmks 
                ON 
                    sub.cpmk = cpmks.id
                GROUP BY 
                    sub.cpmk, cpmks.kode
            ";

        $cpmkPerSoalAngkatanWithKode = DB::select($queryCpmkAngkatan, [
            'course1' => $course,
            'course2' => $course,
            'course3' => $course,
            'course4' => $course,
            'angkatan1' => $angkatan,
            'angkatan2' => $angkatan,
            'angkatan3' => $angkatan,
            'angkatan4' => $angkatan,
        ]);
        // dd($cpmkPerSoalAngkatanWithKode);
        $cpmkResultAllQuery = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('mk_kode', $course)->select('cpmk_mk.cpmk_id as id', 'cpmks.judul', 'cpmks.kode');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cpmkResultAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cpmkResultAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cpmkResultAllQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $cpmkResultAll = $cpmkResultAllQuery->get();
        // dd($cpmkResultAll);

        // Isi kode dulu
        $cpmkTmp = [];
        foreach ($cpmkResultAll  as $itemCpmk) {
            $cpmkTmp[$itemCpmk->id] = [0, 0, 0, $itemCpmk->kode];
        }
        // cari ratar-rata tertinggi dan terendah cpmk
        $minAvg = 101;
        $keyMinAvg = 0;
        $kodeMinAvg = '';
        $maxAvg = 0;
        $keyMaxAvg = 0;
        $kodeMaxAvg = '';
        // isi avg , min , max
        foreach ($cpmkPerSoalAngkatanWithKode  as $itemCpmk) {
            if ($itemCpmk->avg_r3 < $minAvg) {
                $minAvg = number_format($itemCpmk->avg_r3, 2);
                $keyMinAvg = $itemCpmk->cpmk;
                $kodeMinAvg = $itemCpmk->kode;
            }
            if ($itemCpmk->avg_r3 > $maxAvg) {
                $maxAvg = number_format($itemCpmk->avg_r3, 2);
                $keyMaxAvg = $itemCpmk->cpmk;
                $kodeMaxAvg = $itemCpmk->kode;
            }
            $cpmkTmp[$itemCpmk->cpmk][0] = $itemCpmk->avg_r3;
            $cpmkTmp[$itemCpmk->cpmk][1] = $itemCpmk->min_r3;
            $cpmkTmp[$itemCpmk->cpmk][2] = $itemCpmk->max_r3;
        }
        // dd($maxAvg);
        // return $prodiDa

        // Hitung cpmk angkatan

        // Ambil key foreignnya
        // dd($minCpmkKey);
        $soalDescQuery = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mutus.id', 'mutus.cpmk', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
            ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
            ->where('mutus.angkatan', $angkatan)
            ->where('mutus.id_prodi', $prodiId)
            ->where('mutus.course', $course)
            ->where('mutus.universitas_id', $universitasId)
            ->where('mutus.cpmk', $keyMinAvg)
            ->distinct();

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $soalDescQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $soalDescQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $soalDescQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $soalDesc = $soalDescQuery->get();
        $uniqueData = [];
        // dd($soalDesc);
        foreach ($soalDesc as $result) {
            // cek untuk isi atribut soal, buang yang kosong
            $soal = !empty($result->soal) ? $result->soal : $result->soalFromId;
            // kunci unik(namaCourse, jenis, soal)
            $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;
            // Tambahkan elemen ke array asosiatif jika belum ada
            if (!isset($uniqueData[$uniqueKey])) {
                $uniqueData[$uniqueKey] = [
                    'soal' => $soal,
                    'id' => $result->id,
                    'idSoal' => $result->idSoal,
                    'Jenis' => $result->Jenis,
                    'namaCourse' => $result->namaCourse,
                ];
            }
        }

        $soalTerendah = array_values($uniqueData);
        // dd($cpmkTmp);

        return view('penjamin-mutu.visualisasi.hasilVisualisasiCpmkAngkatan', [
            'prodi' => $prodi,
            'angkatan' => $angkatan,
            'universitas' => $universitas,
            'universitasImg' => $universitasImg,
            'completeCourseFormat' => $completeCourseFormat,
            'soalTerendah' => $soalTerendah,
            'course' => $course,
            'allAngkatan' => $allAngkatan,
            'cpmkTmp' => $cpmkTmp,
            'cpmkResultAll' => $cpmkResultAll,

            'minAvg' => $minAvg,
            'kodeMinAvg' => $kodeMinAvg,
            'maxAvg' => $maxAvg,
            'kodeMaxAvg' => $kodeMaxAvg
        ]);
    }

    public function getAllAngkatanCpmk(Request $request)
    {

        $courseRequest = $request->input('course');
        $prodi = $request->input('prodi');
        $universitas = $request->input('universitas');
        list($course, $namaCourse) = explode('-', $courseRequest);
        // return $course;
        $allAngkatan =  DB::table('mutus')
            ->select('angkatan')
            ->where('prodi', $prodi)
            ->where('course', $course)
            ->where('universitas', $universitas)
            ->orderBy('angkatan', 'desc')
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diolah',
            'result' => ['allAngkatan' => $allAngkatan],
        ]);
    }

    public function getAllNpmByAngkatan(Request $request)
    {
        $angkatan = $request->input('angkatan');
        $prodi = $request->input('prodi');

        $allNpm =  DB::table('mutus')
            ->select('npm')
            ->where('angkatan', $angkatan)
            ->where('prodi', $prodi)
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diolah',
            'result' => ['allNpm' => $allNpm],
        ]);
    }

    public function indexMataKuliah()
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $userUniversitasId = auth()->user()->id_universitasUser;

        $universitas = Universitas::find($userUniversitasId);

        $prodiQuery = Prodi::query()
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('prodi.id', 'prodi.nama');

        // Apply access restrictions based on user role
        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $prodiQuery->where('fakultas.id_universitas', $userUniversitasId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $prodiQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $prodiQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $prodi = $prodiQuery->get();

        return view('penjamin-mutu.visualisasi.indexVisualisasiMataKuliah', compact('universitas', 'prodi'));
    }

    public function getCourseByProdi(Request $request)
    {
        // return $request;
        // $prodi = $request->input('prodi');
        $prodiId = $request->input('prodi');
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');
        // return $universitas;
        $course = DB::table('mutus')->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->select('course', 'mks.nama as namaCourse')
            ->where('angkatan', $angkatan)
            ->where('mutus.id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->distinct()
            ->get();
        // return $courseArray;
        // TODO: next abis makan
        // $userNameLogin = auth()->user()->name;
        // $userJabatanLogin = auth()->user()->jabatan;
        // if ($userJabatanLogin == "Kaprodi") {
        //     $course = $courseArray->map(function ($item) {
        //         return [
        //             'course' => $item->course,
        //             'namaCourse' => $item->namaCourse
        //         ];
        //     })->toArray();
        // } else {
        //     $courseCpmkPj    = DB::table('rpss')
        //         ->select('kode_mk', 'pengembang')
        //         ->where('pengembang', $userNameLogin)
        //         ->get();

        //     $course = [];

        //     $courseArrayAssoc = [];
        //     foreach ($courseArray as $courseItem) {
        //         $courseArrayAssoc[$courseItem->course] = $courseItem->namaCourse;
        //     }

        //     foreach ($courseCpmkPj as $item) {
        //         // Periksa apakah kode_mk ada dalam $courseArrayAssoc
        //         if (isset($courseArrayAssoc[$item->kode_mk])) {
        //             // kalo ada, tambahkan ke $course 
        //             $course[] = [
        //                 'course' => $item->kode_mk,
        //                 'namaCourse' => $courseArrayAssoc[$item->kode_mk],
        //             ];
        //         }
        //     }
        // }

        // balikin npm ke dropdown
        $options = '<option value="">Pilih Course</option>';
        foreach ($course as $c) {
            $options .= '<option value="' . $c->course . '-' . $c->namaCourse . '">' . $c->course . '-' . $c->namaCourse . '</option>';
        }

        return $options;
    }

    public function hasilVisualMahasiswaMataKuliah(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        // return $request;
        $prodiId = $request->input('prodi');
        $prodi = Prodi::where('id', $prodiId)->value('nama');
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');
        $univNama = Universitas::where('id', $universitas)->value('nama');
        $imgSrc = $request->input('imgSrc');
        $originalReqCourse = $request->course;
        list($course, $namaCourse) = explode('-', $originalReqCourse);

        $allNpm =  DB::table('mutus')
            ->select('npm', 'nama_mhs')
            ->where('angkatan', $angkatan)
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->distinct()
            ->get();

        // Hitung cpmk angkatan
        // CPMK PER SOAL ANGKATAN
        $queryCpmkAngkatan = "
                SELECT 
                    sub.cpmk, 
                    cpmks.kode,
                    MIN(sub.r3) AS min_r3,
                    MAX(sub.r3) AS max_r3,
                    AVG(sub.r3) AS avg_r3
                FROM (
                    SELECT 
                        m.NPM,
                        m.cpmk, 
                        SUM((m.BobotSoal * m.nilaiSoal) / q1.BobotJenisCPMK * m.examWeight / q2.sumExamWeight) AS r3
                    FROM 
                        mutus m 
                    JOIN 
                        (SELECT 
                            NPM, 
                            MAX(CONCAT(SUBSTRING_INDEX(tahun, ' ', -1), 
                                    CASE WHEN SUBSTRING_INDEX(tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END)) AS max_year_semester
                        FROM 
                            mutus
                        WHERE 
                            Course = :course1 AND angkatan = :angkatan1
                        GROUP BY 
                            NPM
                        ) AS ly
                        ON m.NPM = ly.NPM
                        AND CONCAT(SUBSTRING_INDEX(m.tahun, ' ', -1), 
                                CASE WHEN SUBSTRING_INDEX(m.tahun, ' ', 1) = 'Genap' THEN '1' ELSE '0' END) = ly.max_year_semester
                    JOIN 
                        (SELECT 
                            NPM, 
                            Cpmk, 
                            Jenis, 
                            tahun, 
                            SUM(BobotSoal) AS BobotJenisCPMK 
                        FROM 
                            mutus 
                        WHERE 
                            Course = :course2 AND angkatan = :angkatan2
                        GROUP BY 
                            NPM, Cpmk, Jenis, tahun
                        ) AS q1
                        ON m.NPM = q1.NPM 
                        AND m.cpmk = q1.Cpmk 
                        AND m.Jenis = q1.Jenis 
                        AND m.tahun = q1.tahun
                    JOIN 
                        (SELECT 
                            NPM, 
                            tahun, 
                            Cpmk, 
                            SUM(examWeight) AS sumExamWeight 
                        FROM 
                            (SELECT DISTINCT 
                                NPM, 
                                tahun, 
                                Cpmk, 
                                Jenis, 
                                examWeight 
                            FROM 
                                mutus 
                            WHERE  
                                Course = :course3 AND angkatan = :angkatan3
                            ) AS subquery 
                        GROUP BY 
                            NPM, tahun, Cpmk
                        ) AS q2 
                        ON q1.NPM = q2.NPM 
                        AND q1.cpmk = q2.Cpmk 
                        AND q1.tahun = q2.tahun
                    WHERE 
                        m.Course = :course4 AND m.angkatan = :angkatan4
                    GROUP BY 
                        m.NPM, m.cpmk
                ) AS sub 
                JOIN 
                    cpmks 
                ON 
                    sub.cpmk = cpmks.id
                GROUP BY 
                    sub.cpmk, cpmks.kode
            ";

        $cpmkPerSoalAngkatanWithKode = DB::select($queryCpmkAngkatan, [
            'course1' => $course,
            'course2' => $course,
            'course3' => $course,
            'course4' => $course,
            'angkatan1' => $angkatan,
            'angkatan2' => $angkatan,
            'angkatan3' => $angkatan,
            'angkatan4' => $angkatan,
        ]);
        // return $cpmkPerSoalAngkatanWithKode;

        // Ngisi CPMK SEMENTARA
        $cpmkResultAllQuery = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('mk_kode', $course)
            ->select('cpmk_mk.cpmk_id as id', 'cpmks.judul', 'cpmks.kode');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cpmkResultAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cpmkResultAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cpmkResultAllQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $cpmkResultAll = $cpmkResultAllQuery->get();
        // Ngisi ke yg kosong
        $cpmkTmp = [];
        foreach ($cpmkResultAll  as $itemCpmk) {
            $cpmkTmp[$itemCpmk->id] = [0, 0, 0, $itemCpmk->kode];
        }
        // Buat summary
        $minAvgAngkatan = 100;
        $maxAvgAngkatan = 0;
        $keyMinAvgAngkatan = 0;
        $keyMaxAvgAngkatan = 0;
        $kodeMinAvgAngkatan = null;
        $kodeMaxAvgAngkatan = null;
        $soalTerendah = [];

        //    foreach ($cpmkPerSoalAngkatanWithKode  as $itemCpmk) {
        //        if ($itemCpmk->avg_r3 < $minAvgAngkatan) {
        //            $minAvgAngkatan = $itemCpmk->avg_r3;
        //            $keyMinAvgAngkatan = $itemCpmk->cpmk;
        //        }
        //        if ($itemCpmk->avg_r3 > $maxAvgAngkatan) {
        //            $maxAvgAngkatan = $itemCpmk->avg_r3;
        //            $keyMaxAvgAngkatan = $itemCpmk->cpmk;
        //        }
        //        // return $itemCpmk->kode;
        //        $cpmkTmp[$itemCpmk->cpmk][0] = $itemCpmk->avg_r3;
        //        $cpmkTmp[$itemCpmk->cpmk][1] = $itemCpmk->min_r3;
        //        $cpmkTmp[$itemCpmk->cpmk][2] = $itemCpmk->max_r3;
        //    }
        //    $kodeMinAvgAngkatan = $cpmkTmp[$keyMinAvgAngkatan];
        //    $kodeMaxAvgAngkatan = $cpmkTmp[$keyMaxAvgAngkatan];
        //    // return $kodeMaxAvgAngkatan;
        //    // Buat soal rata rata cpmk angkatan terendah 

        //    // // dd($minCpmkKey);
        //    $soalDesc = DB::table('mutus')->join('mks','mutus.course','=','mks.kode')
        //        ->select('mutus.id', 'mutus.cpmk', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
        //        ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
        //        ->where('mutus.angkatan', $angkatan)
        //        ->where('mutus.id_prodi', $prodiId)
        //        ->where('mutus.course', $course)
        //        ->where('mutus.universitas_id', $universitas)
        //        ->where('mutus.cpmk', $keyMinAvgAngkatan)
        //        ->distinct()
        //        ->get();

        //    $uniqueData = [];

        //    foreach ($soalDesc as $result) {
        //        // cek untuk isi atribut soal, buang yang kosong
        //        $soal = !empty($result->soal) ? $result->soal : $result->soalFromId;
        //        // kunci unik(namaCourse, jenis, soal)
        //        $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;
        //        // Tambahkan elemen ke array asosiatif jika belum ada
        //        if (!isset($uniqueData[$uniqueKey])) {
        //            $uniqueData[$uniqueKey] = [
        //                'soal' => $soal,
        //                'id' => $result->id,
        //                'idSoal' => $result->idSoal,
        //                'Jenis' => $result->Jenis,
        //                'namaCourse' => $result->namaCourse,
        //            ];
        //        }
        //    }

        //    $soalTerendah = array_values($uniqueData);
        //    // return $allNpm;


        //    return response()->json([
        //        'success' => true,
        //        'message' => 'Data berhasil diolah',
        //        'result' => [
        //            'prodi' => $prodi, 'angkatan' => $angkatan,
        //            'completeCourseFormat' => $originalReqCourse,
        //            'soalTerendah' => $soalTerendah,
        //            'allNpm' => $allNpm,
        //            'universitas' => $univNama,
        //            'imgSrc' => $imgSrc,
        //            'cpmkTmp' => $cpmkTmp,
        //            'cpmkResultAll' => $cpmkResultAll,
        //            'kodeMinAvgAngkatan' => $kodeMinAvgAngkatan,
        //            'kodeMaxAvgAngkatan' => $kodeMaxAvgAngkatan
        //        ],
        //        'showVisualContainer' => true
        //    ]);
        // Buat summary - pastikan ada data sebelum memproses
        if (!empty($cpmkPerSoalAngkatanWithKode)) {
            foreach ($cpmkPerSoalAngkatanWithKode as $itemCpmk) {
                // Pastikan cpmk ada dalam $cpmkTmp
                if (!isset($cpmkTmp[$itemCpmk->cpmk])) {
                    $cpmkTmp[$itemCpmk->cpmk] = [0, 0, 0, $itemCpmk->kode];
                }

                if ($itemCpmk->avg_r3 < $minAvgAngkatan) {
                    $minAvgAngkatan = $itemCpmk->avg_r3;
                    $keyMinAvgAngkatan = $itemCpmk->cpmk;
                }
                if ($itemCpmk->avg_r3 > $maxAvgAngkatan) {
                    $maxAvgAngkatan = $itemCpmk->avg_r3;
                    $keyMaxAvgAngkatan = $itemCpmk->cpmk;
                }
                // return $itemCpmk->kode;
                $cpmkTmp[$itemCpmk->cpmk][0] = $itemCpmk->avg_r3;
                $cpmkTmp[$itemCpmk->cpmk][1] = $itemCpmk->min_r3;
                $cpmkTmp[$itemCpmk->cpmk][2] = $itemCpmk->max_r3;
            }

            // Pastikan key ada dalam array sebelum mengaksesnya
            if (isset($cpmkTmp[$keyMinAvgAngkatan])) {
                $kodeMinAvgAngkatan = $cpmkTmp[$keyMinAvgAngkatan];
            }

            if (isset($cpmkTmp[$keyMaxAvgAngkatan])) {
                $kodeMaxAvgAngkatan = $cpmkTmp[$keyMaxAvgAngkatan];
            }

            // Ambil soal terendah jika ada CPMK terendah yang valid
            if ($keyMinAvgAngkatan != 0) {
                // Buat soal rata rata cpmk angkatan terendah
                $soalDescQuery = DB::table('mutus')
                    ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                    ->join('mks', 'mutus.course', '=', 'mks.kode')
                    ->select('mutus.id', 'mutus.cpmk', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
                    ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
                    ->where('mutus.angkatan', $angkatan)
                    ->where('mutus.id_prodi', $prodiId)
                    ->where('mutus.course', $course)
                    ->where('mutus.universitas_id', $universitas)
                    ->where('mutus.cpmk', $keyMinAvgAngkatan)
                    ->distinct();

                if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                    $soalDescQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                    $soalDescQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                    $soalDescQuery->where('prodi.id', auth()->user()->id_prodiUser);
                }

                $soalDesc = $soalDescQuery->get();

                $uniqueData = [];

                foreach ($soalDesc as $result) {
                    // cek untuk isi atribut soal, buang yang kosong
                    $soal = !empty($result->soal) ? $result->soal : $result->soalFromId;
                    // kunci unik(namaCourse, jenis, soal)
                    $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;
                    // Tambahkan elemen ke array asosiatif jika belum ada
                    if (!isset($uniqueData[$uniqueKey])) {
                        $uniqueData[$uniqueKey] = [
                            'soal' => $soal,
                            'id' => $result->id,
                            'idSoal' => $result->idSoal,
                            'Jenis' => $result->Jenis,
                            'namaCourse' => $result->namaCourse,
                        ];
                    }
                }

                $soalTerendah = array_values($uniqueData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => !empty($cpmkPerSoalAngkatanWithKode) ? 'Data berhasil diolah' : 'Tidak ada data yang ditemukan',
            'result' => [
                'prodi' => $prodi,
                'angkatan' => $angkatan,
                'completeCourseFormat' => $originalReqCourse,
                'soalTerendah' => $soalTerendah,
                'allNpm' => $allNpm,
                'universitas' => $univNama,
                'imgSrc' => $imgSrc,
                'cpmkTmp' => $cpmkTmp,
                'cpmkResultAll' => $cpmkResultAll,
                'kodeMinAvgAngkatan' => $kodeMinAvgAngkatan,
                'kodeMaxAvgAngkatan' => $kodeMaxAvgAngkatan
            ],
            'showVisualContainer' => !empty($cpmkPerSoalAngkatanWithKode)
        ]);
    }

    public function getNamaByNpm(Request $request)
    {

        $npm = $request->input('npm');

        // ambil npm berdasarkan angkatan dari ajax
        $namaData = DB::table('mutus')
            ->where('npm', $npm)
            ->distinct()
            ->pluck('nama_mhs')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diolah',
            'result' => ['namaData' => $namaData],
        ]);
    }
}
