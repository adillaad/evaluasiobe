<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Mutu;
use App\Models\Prodi;
use App\Models\Universitas;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $prodiId = $request->input('prodi');

        // 1. Ambil mahasiswa dari tabel mahasiswa
        $mhsQuery = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mahasiswa.NPM as npm', 'mahasiswa.Nama as nama_mhs')
            ->where('mahasiswa.angkatan', $angkatan);

        if (!empty($prodiId)) {
            $mhsQuery->where('mahasiswa.id_prodi', $prodiId);
        }

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $mhsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $mhsQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $mhsQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $mhsList = $mhsQuery->get();

        // 2. Ambil mahasiswa dari tabel mutus
        $mutuQuery = DB::table('mutus as m1')
            ->join('prodi', 'm1.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('m1.npm as npm', 'm1.nama_mhs as nama_mhs')
            ->where('m1.angkatan', $angkatan)
            ->where('m1.universitas_id', $universitas);

        if (!empty($prodiId)) {
            $mutuQuery->where('m1.id_prodi', $prodiId);
        }

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $mutuQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $mutuQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $mutuQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $mutuList = $mutuQuery->distinct()->get();

        $studentMap = [];
        foreach ($mhsList as $m) {
            if (!empty($m->npm)) {
                $studentMap[$m->npm] = $m->nama_mhs ?? 'N/A';
            }
        }
        foreach ($mutuList as $m) {
            if (!empty($m->npm)) {
                if (!isset($studentMap[$m->npm]) || empty($studentMap[$m->npm]) || $studentMap[$m->npm] === 'N/A') {
                    $studentMap[$m->npm] = $m->nama_mhs ?? 'N/A';
                }
            }
        }

        ksort($studentMap);

        // balikin npm ke dropdown
        $options = '<option value="">Pilih NPM</option>';
        foreach ($studentMap as $npmKey => $namaMhs) {
            $options .= '<option value="' . $npmKey . '">' . $npmKey . '-' . $namaMhs . '</option>';
        }

        return $options;
    }

    public function getStudyPeriodConfigByProdi($prodi)
    {
        $jenjang = '';
        if (is_object($prodi)) {
            $jenjang = strtoupper(trim($prodi->jenjang ?? ''));
            if (empty($jenjang)) {
                $jenjang = strtoupper(trim($prodi->nama ?? ''));
            }
        } elseif (is_string($prodi)) {
            $jenjang = strtoupper(trim($prodi));
        }

        if (str_contains($jenjang, 'S2') || str_contains($jenjang, 'MAGISTER') || str_contains($jenjang, 'MASTER') || str_contains($jenjang, 'S-2')) {
            return [
                'jenjang' => 'S2',
                'normalYears' => 2,
                'normalSemesters' => 4,
                'normalYearsSpan' => 3,
                'maxYears' => 4,
                'maxSemesters' => 8,
            ];
        } elseif (str_contains($jenjang, 'S3') || str_contains($jenjang, 'DOKTOR') || str_contains($jenjang, 'DOCTOR') || str_contains($jenjang, 'S-3')) {
            return [
                'jenjang' => 'S3',
                'normalYears' => 3,
                'normalSemesters' => 6,
                'normalYearsSpan' => 4,
                'maxYears' => 3,
                'maxSemesters' => 6,
            ];
        } elseif (str_contains($jenjang, 'D3') || str_contains($jenjang, 'D-3') || str_contains($jenjang, 'D-III') || str_contains($jenjang, 'DIPLOMA 3') || str_contains($jenjang, 'DIPLOMA III')) {
            return [
                'jenjang' => 'D3',
                'normalYears' => 3,
                'normalSemesters' => 6,
                'normalYearsSpan' => 4,
                'maxYears' => 5,
                'maxSemesters' => 10,
            ];
        } elseif (str_contains($jenjang, 'D4') || str_contains($jenjang, 'D-4') || str_contains($jenjang, 'D-IV') || str_contains($jenjang, 'DIPLOMA 4') || str_contains($jenjang, 'DIPLOMA IV') || str_contains($jenjang, 'SARJANA TERAPAN')) {
            return [
                'jenjang' => 'D4',
                'normalYears' => 4,
                'normalSemesters' => 8,
                'normalYearsSpan' => 5,
                'maxYears' => 7,
                'maxSemesters' => 14,
            ];
        } else {
            // Default S1
            return [
                'jenjang' => 'S1',
                'normalYears' => 4,
                'normalSemesters' => 8,
                'normalYearsSpan' => 5,
                'maxYears' => 7,
                'maxSemesters' => 14,
            ];
        }
    }

    public function getMaxYearsByProdi($prodi)
    {
        $config = $this->getStudyPeriodConfigByProdi($prodi);
        return $config['maxYears'];
    }

    private function getYearSemSqlExpr($tableAlias = 'mutus', $taAlias = 'ta')
    {
        return "CONCAT(
            COALESCE(CONVERT($taAlias.tahun USING utf8mb4) COLLATE utf8mb4_unicode_ci, CONVERT(SUBSTRING_INDEX($tableAlias.tahun, ' ', -1) USING utf8mb4) COLLATE utf8mb4_unicode_ci),
            CASE WHEN COALESCE(CONVERT($taAlias.jenis_semester USING utf8mb4) COLLATE utf8mb4_unicode_ci, CONVERT(SUBSTRING_INDEX($tableAlias.tahun, ' ', 1) USING utf8mb4) COLLATE utf8mb4_unicode_ci) = 'Genap' THEN '1' ELSE '0' END
        )";
    }

    private function getAvailablePeriodsForStudent($npm, $angkatan, $prodiParam = null)
    {
        $baseAngkatan = (int)$angkatan > 1900 ? (int)$angkatan : (int)substr($npm, 0, 2) + 2000;
        if ($baseAngkatan < 2000) {
            $baseAngkatan = (int)date('Y') - 4;
        }

        $prodiObj = null;
        if ($prodiParam) {
            if (is_numeric($prodiParam)) {
                $prodiObj = DB::table('prodi')->where('id', $prodiParam)->first();
            } elseif (is_object($prodiParam)) {
                $prodiObj = $prodiParam;
            }
        }
        if (!$prodiObj) {
            $mhs = DB::table('mahasiswa')->where('NPM', $npm)->first();
            if ($mhs && !empty($mhs->id_prodi)) {
                $prodiObj = DB::table('prodi')->where('id', $mhs->id_prodi)->first();
            }
        }

        $periodConfig = $this->getStudyPeriodConfigByProdi($prodiObj);
        $maxYears = $periodConfig['maxYears'];
        $normalSemesters = $periodConfig['normalSemesters'];
        $normalYearsSpan = $periodConfig['normalYearsSpan'];

        $studentSemesters = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->where(function($q) use ($npm) {
                $q->where('mutus.npm', $npm)->orWhere('mutus.NPM', $npm);
            })
            ->pluck('mks.semester')
            ->unique()
            ->map(function($v) { return (int)$v; })
            ->toArray();

        $maxSemTaken = !empty($studentSemesters) ? max($studentSemesters) : 0;
        $extraYears = $maxSemTaken > $normalSemesters ? (int) ceil(($maxSemTaken - $normalSemesters) / 2) : 0;
        $totalYears = min($maxYears, $normalYearsSpan + $extraYears);

        $allSemestersList = [];
        $totalSemesters = max($normalSemesters, $maxSemTaken);

        for ($s = 1; $s <= $totalSemesters; $s++) {
            $semYear = $baseAngkatan + (int)floor($s / 2);
            $jenis = ($s % 2 == 1) ? 'Ganjil' : 'Genap';
            $hasSem = in_array($s, $studentSemesters);

            $allSemestersList[] = [
                'semNumber' => $s,
                'jenis' => $jenis,
                'tahun' => (string)$semYear,
                'academicYear' => (string)$semYear,
                'label' => "Semester {$s} ({$jenis} {$semYear})",
                'hasData' => $hasSem
            ];
        }

        $yearsList = [];
        for ($i = 0; $i < $totalYears; $i++) {
            $calYear = $baseAngkatan + $i;
            $semestersInYear = array_values(array_filter($allSemestersList, function($sem) use ($calYear) {
                return (int)$sem['tahun'] === $calYear;
            }));

            $hasYear = false;
            foreach ($semestersInYear as $sem) {
                if ($sem['hasData']) {
                    $hasYear = true;
                    break;
                }
            }

            $yearsList[] = [
                'tahun' => (string)$calYear,
                'academicYear' => (string)$calYear,
                'yearIndex' => $i + 1,
                'label' => (string)$calYear,
                'hasData' => $hasYear,
                'semesters' => $semestersInYear
            ];
        }

        return [
            'years' => $yearsList,
            'allSemesters' => $allSemestersList
        ];
    }

    public function getTahunSemesterByNpm(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $npm = $request->input('npm');
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');

        $availablePeriods = $this->getAvailablePeriodsForStudent($npm, $angkatan);

        return response()->json([
            'success' => true,
            'periods' => $availablePeriods
        ]);
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
        $rawTahun = $request->input('tahun'); // 'all', '2022', '2022/2023', '1'
        $rawSemester = $request->input('semester'); // 'all', '1'..'8'

        $baseAngkatan = (int)$angkatan > 1900 ? (int)$angkatan : (int)substr($npm, 0, 2) + 2000;
        if ($baseAngkatan < 2000) {
            $baseAngkatan = (int)date('Y') - 4;
        }

        $semestersToFilter = [];
        $activePeriodLabel = 'Kumulatif (Semua Semester)';

        if ($rawSemester && $rawSemester !== 'all' && is_numeric($rawSemester)) {
            $semNum = (int)$rawSemester;
            $semestersToFilter = [$semNum];
            $yearOffset = (int)floor(($semNum - 1) / 2);
            $calYear = $baseAngkatan + $yearOffset;
            $nextYear = $calYear + 1;
            $jenisSem = ($semNum % 2 === 1) ? 'Ganjil' : 'Genap';
            $activePeriodLabel = "Semester {$semNum} ({$jenisSem} {$calYear}/{$nextYear})";
        } elseif ($rawTahun && $rawTahun !== 'all') {
            $yearOffset = 0;
            $calYear = $baseAngkatan;
            if (is_numeric($rawTahun)) {
                $val = (int)$rawTahun;
                if ($val >= 2000) {
                    $yearOffset = $val - $baseAngkatan;
                    $calYear = $val;
                } else {
                    $yearOffset = $val - 1;
                    $calYear = $baseAngkatan + $yearOffset;
                }
            } elseif (strpos($rawTahun, '/') !== false) {
                $startYear = (int)explode('/', $rawTahun)[0];
                $yearOffset = $startYear - $baseAngkatan;
                $calYear = $startYear;
            }
            $nextYear = $calYear + 1;
            $sem1 = $yearOffset * 2 + 1;
            $sem2 = $yearOffset * 2 + 2;
            $semestersToFilter = [$sem1, $sem2];
            $activePeriodLabel = "Tahun {$calYear}/{$nextYear} (Semester {$sem1} & {$sem2})";
        }

        $universitasNama = null;
        if (!empty($universitas)) {
            if (is_numeric($universitas)) {
                $universitasNama = Universitas::where('id', $universitas)->value('nama');
            } else {
                $universitasNama = Universitas::where('nama', $universitas)->value('nama') ?? $universitas;
            }
        }
        if (empty($universitasNama)) {
            $universitasNama = auth()->user()->universitas->nama 
                ?? (auth()->user()->prodi ? auth()->user()->prodi->fakultas->universitas->nama ?? null : null) 
                ?? Universitas::first()->nama 
                ?? 'Universitas';
        }

        // Resolusi identitas mahasiswa dan prodi mahasiswa
        $mhsData = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('mahasiswa.Nama as nama_mhs', 'prodi.nama as prodiNama', 'prodi.id as prodiId')
            ->where('mahasiswa.NPM', $npm)
            ->first();

        $dataQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('nama_mhs', 'prodi.nama as prodiNama', 'prodi.id as prodiId')
            ->where('npm', $npm);
        if ($universitas) {
            $dataQuery->where('universitas_id', $universitas);
        }
        $dataMutus = $dataQuery->distinct()->first();

        $nama = $mhsData->nama_mhs ?? $dataMutus->nama_mhs ?? 'N/A';
        $prodiNama = $mhsData->prodiNama ?? $dataMutus->prodiNama ?? '';
        $prodiId = $request->input('prodi') ?? $mhsData->prodiId ?? $dataMutus->prodiId ?? auth()->user()->id_prodiUser;

        // Subquery to get the max year and semester for each course
        $subQuery = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
            ->select('mutus.Course', DB::raw("MAX(" . $this->getYearSemSqlExpr('mutus', 'ta') . ") AS max_year_semester"))
            ->where('mutus.NPM', $npm);

        if (!empty($semestersToFilter)) {
            $subQuery->whereIn('mks.semester', $semestersToFilter);
        }

        if (!empty($prodiId)) {
            $subQuery->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $subQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $subQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $subQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $subQuery->groupBy('mutus.Course');

        // Main query to get the records with the max year and semester
        $subQueryMutus = DB::table('mutus as m')
            ->join('mks', 'm.Course', '=', 'mks.kode')
            ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
            ->joinSub($subQuery, 't', function ($join) {
                $join->on('m.Course', '=', 't.Course')
                    ->on(DB::raw($this->getYearSemSqlExpr('m', 'ta')), '=', 't.max_year_semester');
            })
            ->where('m.NPM', $npm);

        if (!empty($semestersToFilter)) {
            $subQueryMutus->whereIn('mks.semester', $semestersToFilter);
        }

        $subQueryMutus->groupBy('m.npm', 'm.Course', 'm.jenis')
            ->select('m.npm', 'm.Course', 'm.jenis', "m.id");

        // Gabungkan subquery dengan tabel mutus untuk menghitung total nilai berdasarkan bobot ujian dan nilai
        $nilaiMk = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->joinSub($subQueryMutus, 'sub', function ($join) {
                $join->on('mutus.id', '=', 'sub.id');
            })
            ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'));

        if (!empty($semestersToFilter)) {
            $nilaiMk->whereIn('mks.semester', $semestersToFilter);
        }

        if (!empty($prodiId)) {
            $nilaiMk->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $nilaiMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $nilaiMk->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $nilaiMk->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $nilaiMk = $nilaiMk->groupBy('mutus.Course')->get();

        $namaMkList = DB::table('mks')
            ->pluck('nama', 'kode');
        $nilaiMkLulus = [];
        $nilaiMkTidakLulus = [];

        foreach ($nilaiMk as $nilai) {
            $namaMk = $namaMkList[$nilai->Course] ?? 'N/A';
            if ($nilai->total_nilai >= 50) {
                $nilaiMkLulus[$nilai->Course] = [$nilai->total_nilai, $namaMk];
            } else {
                $nilaiMkTidakLulus[$nilai->Course] = [$nilai->total_nilai, $namaMk];
            }
        }

        $cplNilaiMk = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
            ->select('mutus.Course', 'mutus.Cpl', DB::raw('SUM(mutus.examWeight / 100 * mutus.Nilai) as total_nilaiCek'))
            ->where('mutus.npm', $npm)
            ->whereIn('mutus.Course', array_keys($nilaiMkLulus));

        if (!empty($semestersToFilter)) {
            $cplNilaiMk->whereIn('mks.semester', $semestersToFilter);
        }

        if (!empty($prodiId)) {
            $cplNilaiMk->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $cplNilaiMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $cplNilaiMk->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $cplNilaiMk->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $cplNilaiMk = $cplNilaiMk->groupBy('mutus.Course', 'mutus.Cpl')->get();

        $cplNilaiMkUnique = $cplNilaiMk->pluck('Cpl')->unique()->toArray();

        // Query ke cplmk
        $cplResultsQuery = DB::table('mk_cpl')
            ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->whereIn('mk_cpl.cpl_id', $cplNilaiMkUnique);

        $cplResultsAllQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        if (!empty($prodiId)) {
            $cplResultsQuery->where('prodi.id', $prodiId);
            $cplResultsAllQuery->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
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

        $cplMapCombined = [];
        foreach ($cplResults as $id_cpl => $items) {
            $cplMapCombined[$id_cpl] = $items->pluck('mk_kode')->toArray();
        }

        foreach ($cplNilaiMk as $cn) {
            $id_cpl = $cn->Cpl;
            if ($id_cpl) {
                if (!isset($cplMapCombined[$id_cpl])) {
                    $cplMapCombined[$id_cpl] = [];
                }
                if (!in_array($cn->Course, $cplMapCombined[$id_cpl])) {
                    $cplMapCombined[$id_cpl][] = $cn->Course;
                }
            }
        }

        $persentaseTotalCplCapaian = [];

        foreach ($cplMapCombined as $id_cpl => $kode_mk_list) {
            $kodeMkArray = is_array($kode_mk_list) ? $kode_mk_list : explode(',', implode(',', (array)$kode_mk_list));
            $countKesamaan = count(array_intersect($kodeMkArray, array_keys($nilaiMkLulus)));
            $countTotal = count($kode_mk_list);
            $persentase = $countTotal > 0 ? ($countKesamaan / $countTotal) * 100 : 0;

            $cplCode = DB::table('cpls')->where('cpls.id', $id_cpl)->value('cpls.kode') ?? "CPL-{$id_cpl}";

            $persentaseTotalCplCapaian[] = [
                'cpl' => $id_cpl,
                'kode_mk' => implode(',', $kode_mk_list),
                'count_mk' => $countTotal,
                'persentase' => round($persentase, 2),
                'kode_cpl' => $cplCode,
            ];
        }

        // CPL per soal
        $cplConditionSub = "";
        $cplConditionMain = "";
        $bindings = [
            'npm1' => $npm,
            'npm2' => $npm,
            'npm3' => $npm,
            'npm4' => $npm,
        ];
        if (!empty($semestersToFilter)) {
            $semListStr = implode(',', array_map('intval', $semestersToFilter));
            $cplConditionSub .= " AND m2.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";
            $cplConditionMain .= " AND m.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";
        }

        $yearExprQ1 = $this->getYearSemSqlExpr('m', 'ta');
        $yearExprQ2 = $this->getYearSemSqlExpr('m2', 'ta2');

        $query = "
                SELECT cpl, cpls.kode as kode, AVG(hasil) as hasil FROM (
                    SELECT Cpl, Course, SUM(hasil) as hasil 
                    FROM (
                        SELECT q1.tahun, q1.Cpl, q1.Jenis, q1.Course, 
                            (CASE WHEN q1.nilaiSoal IS NOT NULL AND q1.BobotSoal > 0 AND q2.BobotJenis > 0 THEN (q1.nilaiSoal * q1.BobotSoal / q2.BobotJenis) * q1.examWeight / COALESCE(NULLIF(q3.sumExamWeight, 0), 100) ELSE COALESCE(q1.Nilai, 0) * q1.examWeight / COALESCE(NULLIF(q3.sumExamWeight, 0), 100) END) as hasil 
                        FROM (
                            SELECT m.tahun, m.Cpl, m.Jenis, m.Course, m.nilaiSoal, m.Nilai, m.examWeight, m.BobotSoal 
                            FROM mutus m 
                            LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
                            JOIN (
                                SELECT Course, MAX($yearExprQ2) AS max_year_semester 
                                FROM mutus m2
                                LEFT JOIN tahun_ajaran ta2 ON m2.tahun_ajaran_id = ta2.id
                                WHERE m2.NPM = :npm1 {$cplConditionSub}
                                GROUP BY Course 
                            ) t 
                            ON m.Course = t.Course AND $yearExprQ1 = t.max_year_semester 
                            WHERE m.NPM = :npm2 {$cplConditionMain}
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

        if (!empty($prodiId)) {
            $query .= " WHERE prodi.id = :id_prodi ";
            $bindings['id_prodi'] = $prodiId;
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
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

        $cplPerSoalWithKode = DB::select($query, $bindings);

        // Buat array asosiatif (dictionary) dari hasil query
        $dictionaryCpl = [];

        foreach ($cplPerSoalWithKode as $row) {
            $dictionaryCpl[$row->cpl] = $row->hasil;
        }

        // BUAT NAMPILIN KALO ADA DATA DAN KOSONG
        $cplPerSoalWithKodeAll = [];
        foreach ($cplResultsAll as $item) {
            $cplPerSoalWithKodeAll[$item->id] = ['kode' => $item->kode, 'cpl' => $item->id, 'hasil' => 0];
        }

        foreach ($cplPerSoalWithKode as $item) {
            $cplPerSoalWithKodeAll[$item->cpl] = $item;
        }

        // TODO:REVISI SEMHAS : KETERCAPAIAN CPL Angkatan
        $mhsNpms = DB::table('mahasiswa')
            ->where('id_prodi', $prodiId)
            ->where('angkatan', $angkatan)
            ->whereNotNull('NPM')
            ->pluck('NPM')
            ->toArray();

        $allNpmQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('npm')
            ->where('angkatan', $angkatan)
            ->where('mutus.id_prodi', $prodiId);

        if (!empty($prodiId)) {
            $allNpmQuery->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $allNpmQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $allNpmQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $allNpmQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $mutuNpms = $allNpmQuery->distinct()->pluck('npm')->toArray();
        $allNpm = array_values(array_unique(array_merge($mhsNpms, $mutuNpms)));
        $persentaseTotalCplCapaianAngkatan = [];

        $allCplPerAngkatanQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode');

        if (!empty($prodiId)) {
            $allCplPerAngkatanQuery->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
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
                ->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
                ->select('Course', DB::raw("MAX(" . $this->getYearSemSqlExpr('mutus', 'ta') . ") AS max_year_semester"))
                ->where('mutus.NPM', $npmItem);

            if (!empty($semestersToFilter)) {
                $subQuery->whereIn('mks.semester', $semestersToFilter);
            }

            if (!empty($prodiId)) {
                $subQuery->where('prodi.id', $prodiId);
            } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $subQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $subQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $subQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $subQuery->groupBy('mutus.Course');

            // Main query to get the records with the max year and semester
            $subQueryMutus = DB::table('mutus as m')
                ->join('mks', 'm.Course', '=', 'mks.kode')
                ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
                ->joinSub($subQuery, 't', function ($join) {
                    $join->on('m.Course', '=', 't.Course')
                        ->on(DB::raw($this->getYearSemSqlExpr('m', 'ta')), '=', 't.max_year_semester');
                })
                ->where('m.NPM', $npmItem);

            if (!empty($semestersToFilter)) {
                $subQueryMutus->whereIn('mks.semester', $semestersToFilter);
            }

            $subQueryMutus->groupBy('m.npm', 'm.Course', 'm.jenis')
                ->select(
                    'm.npm',
                    'm.Course',
                    'm.jenis',
                    "m.id"
                );

            // Gabungkan subquery dengan tabel mutus untuk menghitung total nilai berdasarkan bobot ujian dan nilai
            $nilaiMkAngkatanLoop = DB::table('mutus')
                ->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->joinSub($subQueryMutus, 'sub', function ($join) {
                    $join->on('mutus.id', '=', 'sub.id');
                })
                ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'));

            if (!empty($semestersToFilter)) {
                $nilaiMkAngkatanLoop->whereIn('mks.semester', $semestersToFilter);
            }

            if (!empty($prodiId)) {
                $nilaiMkAngkatanLoop->where('prodi.id', $prodiId);
            } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $nilaiMkAngkatanLoop->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $nilaiMkAngkatanLoop->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $nilaiMkAngkatanLoop->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $nilaiMkAngkatanLoop = $nilaiMkAngkatanLoop->groupBy('mutus.Course')->get();
            // Ambil semua nama MK dalam satu query untuk efisiensi
            $namaMkList = DB::table('mks')
                ->pluck('nama', 'kode'); // Menghasilkan array asosiatif [kode => nama]
            $nilaiMkLulusAngkatan = [];
            $nilaiMkTidakLulusAngkatan = [];

            foreach ($nilaiMkAngkatanLoop as $nilai) {
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
                ->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->leftJoin('cpmks', 'mutus.Cpmk', '=', 'cpmks.id')
                ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
                ->select('Course', DB::raw('COALESCE(mutus.Cpl, cpmks.cpl_id) as Cpl'), DB::raw('SUM(examWeight / 100 * Nilai) as total_nilaiCek'))
                ->where('npm', $npmItem)
                ->whereIn('Course', array_keys($nilaiMkLulusAngkatan));

            if (!empty($semestersToFilter)) {
                $cplNilaiMkAngkatanQuery->whereIn('mks.semester', $semestersToFilter);
            }

            if (!empty($prodiId)) {
                $cplNilaiMkAngkatanQuery->where('prodi.id', $prodiId);
            } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $cplNilaiMkAngkatanQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }

            $cplNilaiMkAngkatan = $cplNilaiMkAngkatanQuery->groupBy('mutus.Course', DB::raw('COALESCE(mutus.Cpl, cpmks.cpl_id)'))->get();

            $cplNilaiMkUnique = array_filter($cplNilaiMkAngkatan->pluck('Cpl')->unique()->toArray());

            $cplResultsQuery = DB::table('mk_cpl')
                ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->whereIn('mk_cpl.cpl_id', $cplNilaiMkUnique);

            $cplResultsAllQuery = DB::table('cpls')
                ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

            if (!empty($prodiId)) {
                $cplResultsQuery->where('prodi.id', $prodiId);
                $cplResultsAllQuery->where('prodi.id', $prodiId);
            } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
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

            $calculatedPersenPerCpl = [];
            foreach ($cplResults as $id_cpl => $items) {
                $kode_mk_list = $items->pluck('mk_kode')->toArray();
                $kodeMkArray = explode(',', implode(',', $kode_mk_list));
                $countKesamaan = count(array_intersect($kodeMkArray, array_keys($nilaiMkLulusAngkatan)));
                $persentase = count($items) > 0 ? ($countKesamaan / count($items)) * 100 : 0;
                $calculatedPersenPerCpl[$id_cpl] = [
                    'persentase' => $persentase,
                    'kode_mk' => implode(',', $kode_mk_list),
                    'count_mk' => count($items),
                ];
            }

            foreach ($allCplPerAngkatanQuery as $cpl) {
                $cplCode = $cpl->kode;
                $cplId = $cpl->id;
                $persentase = isset($calculatedPersenPerCpl[$cplId]) ? $calculatedPersenPerCpl[$cplId]['persentase'] : 0.0;

                if (isset($calculatedPersenPerCpl[$cplId])) {
                    $persentaseTotalCplCapaianIndividu[] = [
                        'cpl' => $cplId,
                        'kode_mk' => $calculatedPersenPerCpl[$cplId]['kode_mk'],
                        'count_mk' => $calculatedPersenPerCpl[$cplId]['count_mk'],
                        'persentase' => round($persentase, 2),
                        'kode_cpl' => $cplCode,
                    ];
                }

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
        $totalAngkatanCount = $allCplPerAngkatan['count'] > 0 ? $allCplPerAngkatan['count'] : 1;
        foreach ($allCplPerAngkatan['total'] as $cpl => $total) {
            $allCplPerAngkatan['avg_cpl'][$cpl] = round($total / $totalAngkatanCount, 2);
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
        $gabunganMkAngkatan = array_unique($gabunganMkAngkatan);

        // Ngisi radar sementara pencapaian cpl yg blm diiinput
        $cplAllTmp = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode');

        if (!empty($prodiId)) {
            $cplAllTmp->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
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

            if (!empty($prodiId)) {
                $namaMkQuery->where('prodi.id', $prodiId);
            } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $namaMkQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $namaMkQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                $namaMkQuery->where('prodi.id', auth()->user()->id_prodiUser);
            }
            $namaMk = $namaMkQuery->value('mks.nama');
            $courseArray[$kode] = $namaMk;
        }

        // Ambil semua label cpl
        $labelCplQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.kode', 'cpls.judul');

        if (!empty($prodiId)) {
            $labelCplQuery->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $labelCplQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $labelCplQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $labelCplQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $profilCplInfoQuery = DB::table('profil_cpl')
            ->join('prodi', 'profil_cpl.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
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

        if (!empty($prodiId)) {
            $profilCplInfoQuery->where('profil_cpl.id_prodi', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
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

                if (!empty($prodiId)) {
                    $cplInfo->where('prodi.id', $prodiId);
                } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                    $cplInfo->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                    $cplInfo->where('fakultas.id', auth()->user()->id_fakultasUser);
                } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
                    $cplInfo->where('prodi.id', auth()->user()->id_prodiUser);
                }
                $cplInfo = $cplInfo->first();
                $kode = $cplInfo->kode ?? '';

                // Mengalikan HasilCpl dengan bobot
                $bobot = $singleProfilCplData->bobot;
                $total = $hasilCpl * $bobot;

                // Menambahkan hasil ke dalam array
                $hasilFinalProfil[$idProfil]['CPLs'][] = [
                    'CPL' => $kode,
                    'Bobot' => $bobot,
                    'HasilCPL' => $hasilCpl,
                    'Total' => round($total, 2),
                ];

                // Menambahkan total ke dalam TotalAkhir
                $hasilFinalProfil[$idProfil]['TotalAkhir'] = round($hasilFinalProfil[$idProfil]['TotalAkhir'] + $total, 2);
            }
        }

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

        $subQueryMaxCPMK = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
            ->select('CPMK', DB::raw("MAX(" . $this->getYearSemSqlExpr('mutus', 'ta') . ") AS max_year_semester"))
            ->where('mutus.NPM', $npm);

        if (!empty($semestersToFilter)) {
            $subQueryMaxCPMK->whereIn('mks.semester', $semestersToFilter);
        }

        if (!empty($prodiId)) {
            $subQueryMaxCPMK->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $subQueryMaxCPMK->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $subQueryMaxCPMK->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $subQueryMaxCPMK->where('prodi.id', auth()->user()->id_prodiUser);
        }

        $subQueryMaxCPMK->groupBy('mutus.CPMK');

        $nilaiCpmk = DB::table('mutus as m')
            ->join('mks', 'm.Course', '=', 'mks.kode')
            ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
            ->joinSub($subQueryMaxCPMK, 't', function ($join) {
                $join->on('m.CPMK', '=', 't.CPMK')
                    ->on(DB::raw($this->getYearSemSqlExpr('m', 'ta')), '=', 't.max_year_semester');
            })
            ->where('m.NPM', $npm);

        if (!empty($semestersToFilter)) {
            $nilaiCpmk->whereIn('mks.semester', $semestersToFilter);
        }

        $nilaiCpmk = $nilaiCpmk->select('m.CPMK', DB::raw('ROUND(SUM(m.examWeight / 100 * m.Nilai), 2) as total_nilai'))
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

        // Soal terendah
        $minCpl = collect($dictionaryCpl)->min();
        $minCplKey = collect($dictionaryCpl)->search($minCpl);

        // Joinkan tabel bila soal deskripsi tidak ada ambil dari idsoal di tabel soal
        $soalDescQuery = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
            ->select('mutus.id', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
            ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
            ->where('mutus.npm', $npm)
            ->where('mutus.cpl', $minCplKey)
            ->distinct();

        if (!empty($semestersToFilter)) {
            $soalDescQuery->whereIn('mks.semester', $semestersToFilter);
        }

        if (!empty($prodiId)) {
            $soalDescQuery->where('prodi.id', $prodiId);
        } elseif (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $soalDescQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $soalDescQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            $soalDescQuery->where('prodi.id', auth()->user()->id_prodiUser);
        }
        $soalDesc = $soalDescQuery->get();

        $uniqueData = [];

        foreach ($soalDesc as $result) {
            $soal = !empty($result->soal) ? $result->soal : $result->soalFromId;
            $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;

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

        // Resolusi batasan tahun dan jenjang program studi
        $prodiObj = DB::table('prodi')->where('id', $prodiId)->first();
        $periodConfig = $this->getStudyPeriodConfigByProdi($prodiObj);
        $jenjangProdi = $periodConfig['jenjang'];
        $maxYears = $periodConfig['maxYears'];
        $normalDuration = $periodConfig['normalYears'];
        $normalSemesters = $periodConfig['normalSemesters'];
        $normalYearsSpan = $periodConfig['normalYearsSpan'];

        $disclaimerMasaStudi = "Batasan masa studi maksimal untuk program studi {$prodiNama} (Jenjang {$jenjangProdi}) adalah {$maxYears} tahun ({$periodConfig['maxSemesters']} semester).";

        // Hitung masa studi riil / standar untuk mahasiswa ini (Ide 1: Dynamic Study Period)
        $studentSemesters = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->where('mutus.NPM', $npm)
            ->pluck('mks.semester')
            ->map(fn($val) => (int)$val)
            ->unique()
            ->toArray();
        $actualSem = !empty($studentSemesters) ? max($studentSemesters) : 0;
        $extraYears = $actualSem > $normalSemesters ? (int) ceil(($actualSem - $normalSemesters) / 2) : 0;
        $displayYearsCount = min($maxYears, $normalYearsSpan + $extraYears);

        $yearsList = [];
        for ($i = 0; $i < $displayYearsCount; $i++) {
            $yearsList[] = $baseAngkatan + $i;
        }

        // Hitung tahun mana yang sudah ada data vs belum ada data
        $yearsWithData = [];
        $activeYearsList = [];
        for ($i = 0; $i < $displayYearsCount; $i++) {
            $currentYear = $baseAngkatan + $i;
            if ($i == 0) {
                $minSemInYr = 1;
                $semestersInYr = [1];
            } elseif ($i < $normalYearsSpan) {
                $minSemInYr = $i * 2;
                $semestersInYr = [$i * 2, $i * 2 + 1];
            } else {
                $offsetSem = $normalSemesters + (($i - $normalYearsSpan + 1) * 2);
                $minSemInYr = $offsetSem - 1;
                $semestersInYr = [$offsetSem - 1, $offsetSem];
            }
            
            $hasDataYr = ($actualSem >= $minSemInYr) || (count(array_intersect($semestersInYr, $studentSemesters)) > 0);
            $yearsWithData[$currentYear] = $hasDataYr;
            if ($hasDataYr) {
                $activeYearsList[] = $currentYear;
            }
        }

        // Hitung Ketercapaian CPL & Skor CPL Per Tahun Akademik
        $mkCplFromDb = DB::table('mk_cpl')->where('id_prodi', $prodiId)->get()->groupBy('cpl_id');
        $mutusCplFromDb = DB::table('mutus')->where('npm', $npm)->whereNotNull('Cpl')->select('Course as mk_kode', 'Cpl as cpl_id')->distinct()->get()->groupBy('cpl_id');

        $mkCplGrouped = [];
        foreach ($cplResultsAll as $cpl) {
            $cplId = $cpl->id;
            $mksFromMkCpl = isset($mkCplFromDb[$cplId]) ? $mkCplFromDb[$cplId]->pluck('mk_kode')->toArray() : [];
            $mksFromMutus = isset($mutusCplFromDb[$cplId]) ? $mutusCplFromDb[$cplId]->pluck('mk_kode')->toArray() : [];
            
            $combinedMks = array_unique(array_merge($mksFromMkCpl, $mksFromMutus));
            $mkCplGrouped[$cplId] = collect(array_map(function($mk) {
                return (object)['mk_kode' => $mk];
            }, $combinedMks));
        }
        $skorCplPerTahun = [];
        $ketercapaianCplPerTahun = [];

        foreach ($cplResultsAll as $cpl) {
            $skorCplPerTahun[$cpl->kode] = [];
            $ketercapaianCplPerTahun[$cpl->kode] = [];
        }

        for ($i = 0; $i < $displayYearsCount; $i++) {
            $currentYear = $baseAngkatan + $i;
            $hasDataYr = $yearsWithData[$currentYear] ?? true;

            if (!$hasDataYr) {
                foreach ($cplResultsAll as $cpl) {
                    $skorCplPerTahun[$cpl->kode][$currentYear] = null;
                    $ketercapaianCplPerTahun[$cpl->kode][$currentYear] = null;
                }
                continue;
            }

            if ($i == 0) {
                $maxSem = 1;
            } elseif ($i < $normalYearsSpan) {
                $maxSem = $i * 2;
            } else {
                $maxSem = ($normalDuration * 2) + (($i - $normalYearsSpan + 1) * 2);
            }
            $semestersUpToYear = range(1, min(14, $maxSem));
            $semListStr = implode(',', $semestersUpToYear);

            // A. Ketercapaian CPL (%) kumulatif hingga semester ini
            $subQueryYr = DB::table('mutus')
                ->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
                ->select('mutus.Course', DB::raw("MAX(" . $this->getYearSemSqlExpr('mutus', 'ta') . ") AS max_year_semester"))
                ->where('mutus.NPM', $npm)
                ->whereIn('mks.semester', $semestersUpToYear)
                ->where('prodi.id', $prodiId)
                ->groupBy('mutus.Course');

            $subQueryMutusYr = DB::table('mutus as m')
                ->join('mks', 'm.Course', '=', 'mks.kode')
                ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
                ->joinSub($subQueryYr, 't', function ($join) {
                    $join->on('m.Course', '=', 't.Course')
                        ->on(DB::raw($this->getYearSemSqlExpr('m', 'ta')), '=', 't.max_year_semester');
                })
                ->where('m.NPM', $npm)
                ->whereIn('mks.semester', $semestersUpToYear)
                ->groupBy('m.npm', 'm.Course', 'm.jenis')
                ->select('m.npm', 'm.Course', 'm.jenis', 'm.id');

            $nilaiMkYr = DB::table('mutus')
                ->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->joinSub($subQueryMutusYr, 'sub', function ($join) {
                    $join->on('mutus.id', '=', 'sub.id');
                })
                ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'))
                ->whereIn('mks.semester', $semestersUpToYear)
                ->where('prodi.id', $prodiId)
                ->groupBy('mutus.Course')
                ->get();

            $passedCoursesYr = [];
            foreach ($nilaiMkYr as $nm) {
                if ($nm->total_nilai >= 50) {
                    $passedCoursesYr[] = $nm->Course;
                }
            }

            foreach ($cplResultsAll as $cpl) {
                $items = $mkCplGrouped[$cpl->id] ?? collect([]);
                $kode_mk_list = $items->pluck('mk_kode')->toArray();
                $kodeMkArray = array_filter(explode(',', implode(',', $kode_mk_list)));
                $countKesamaan = count(array_intersect($kodeMkArray, $passedCoursesYr));
                $pct = count($items) > 0 ? round(($countKesamaan / count($items)) * 100, 2) : 0;

                // Monotonik non-decreasing kumulatif antar tahun
                if ($i > 0) {
                    $prevYear = $baseAngkatan + $i - 1;
                    $prevPct = $ketercapaianCplPerTahun[$cpl->kode][$prevYear] ?? 0;
                    if ($prevPct >= 100) {
                        $pct = 100.0;
                    } elseif ($prevPct !== null && $prevPct > 0) {
                        if ($pct < $prevPct) {
                            $pct = $prevPct;
                        }
                    }
                }
                $ketercapaianCplPerTahun[$cpl->kode][$currentYear] = $pct;
            }

            // B. Skor CPL (0-100) kumulatif hingga semester ini
            $cplConditionSubYr = " AND m2.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";
            $cplConditionMainYr = " AND m.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";

            $queryYr = "
                SELECT cpl, cpls.kode as kode, AVG(hasil) as hasil FROM (
                    SELECT Cpl, Course, SUM(hasil) as hasil 
                    FROM (
                        SELECT q1.tahun, q1.Cpl, q1.Jenis, q1.Course, 
                            (CASE WHEN q1.nilaiSoal IS NOT NULL AND q1.BobotSoal > 0 AND q2.BobotJenis > 0 THEN (q1.nilaiSoal * q1.BobotSoal / q2.BobotJenis) * q1.examWeight / COALESCE(NULLIF(q3.sumExamWeight, 0), 100) ELSE COALESCE(q1.Nilai, 0) * q1.examWeight / COALESCE(NULLIF(q3.sumExamWeight, 0), 100) END) as hasil 
                        FROM (
                            SELECT m.tahun, m.Cpl, m.Jenis, m.Course, m.nilaiSoal, m.Nilai, m.examWeight, m.BobotSoal 
                            FROM mutus m 
                            LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
                            JOIN (
                                SELECT Course, MAX($yearExprQ2) AS max_year_semester 
                                FROM mutus m2
                                LEFT JOIN tahun_ajaran ta2 ON m2.tahun_ajaran_id = ta2.id
                                WHERE m2.NPM = :npm1 {$cplConditionSubYr}
                                GROUP BY Course 
                            ) t 
                            ON m.Course = t.Course AND $yearExprQ1 = t.max_year_semester 
                            WHERE m.NPM = :npm2 {$cplConditionMainYr}
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
                WHERE prodi.id = :id_prodi
                GROUP BY Cpl;
            ";

            $cplScoresResultYr = DB::select($queryYr, [
                'npm1' => $npm,
                'npm2' => $npm,
                'npm3' => $npm,
                'npm4' => $npm,
                'id_prodi' => $prodiId
            ]);

            $cplScoreMapYr = [];
            foreach ($cplScoresResultYr as $res) {
                $cplScoreMapYr[$res->kode] = round($res->hasil, 2);
            }

            foreach ($cplResultsAll as $cpl) {
                $rawScore = $cplScoreMapYr[$cpl->kode] ?? 0;
                if ($i > 0) {
                    $prevYear = $baseAngkatan + $i - 1;
                    $prevScore = $skorCplPerTahun[$cpl->kode][$prevYear] ?? 0;
                    if ($prevScore !== null && $rawScore < $prevScore && $prevScore > 0) {
                        $rawScore = $prevScore;
                    }
                }
                $skorCplPerTahun[$cpl->kode][$currentYear] = $rawScore;
            }
        }

        // Hitung Overall per CPL & Rata-rata per Tahun (berdasarkan tahun aktif terakhir)
        $skorCplOverall = [];
        $ketercapaianCplOverall = [];
        $skorCplYearlyAvg = [];
        $ketercapaianCplYearlyAvg = [];
        $lastActiveYear = count($activeYearsList) > 0 ? end($activeYearsList) : $baseAngkatan;

        foreach ($cplResultsAll as $cpl) {
            $skorCplOverall[$cpl->kode] = $skorCplPerTahun[$cpl->kode][$lastActiveYear] ?? 0;
            $ketercapaianCplOverall[$cpl->kode] = $ketercapaianCplPerTahun[$cpl->kode][$lastActiveYear] ?? 0;
        }

        foreach ($yearsList as $yr) {
            if (!($yearsWithData[$yr] ?? true)) {
                $skorCplYearlyAvg[$yr] = null;
                $ketercapaianCplYearlyAvg[$yr] = null;
                continue;
            }
            $sumScore = 0; $cntScore = 0;
            $sumPct = 0; $cntPct = 0;
            foreach ($cplResultsAll as $cpl) {
                if (isset($skorCplPerTahun[$cpl->kode][$yr]) && $skorCplPerTahun[$cpl->kode][$yr] !== null) {
                    $sumScore += $skorCplPerTahun[$cpl->kode][$yr];
                    $cntScore++;
                }
                if (isset($ketercapaianCplPerTahun[$cpl->kode][$yr]) && $ketercapaianCplPerTahun[$cpl->kode][$yr] !== null) {
                    $sumPct += $ketercapaianCplPerTahun[$cpl->kode][$yr];
                    $cntPct++;
                }
            }
            $skorCplYearlyAvg[$yr] = $cntScore > 0 ? round($sumScore / $cntScore, 2) : 0;
            $ketercapaianCplYearlyAvg[$yr] = $cntPct > 0 ? round($sumPct / $cntPct, 2) : 0;
        }

        $skorCplGrandAvg = count($skorCplOverall) > 0 ? round(array_sum($skorCplOverall) / count($skorCplOverall), 2) : 0;
        $ketercapaianCplGrandAvg = count($ketercapaianCplOverall) > 0 ? round(array_sum($ketercapaianCplOverall) / count($ketercapaianCplOverall), 2) : 0;

        // Ambil data periode tahun dan semester untuk mahasiswa ini
        $availablePeriods = $this->getAvailablePeriodsForStudent($npm, $angkatan);
        $hasData = (count($nilaiMkLulus) > 0 || count($nilaiMkTidakLulus) > 0);

        // Format active period label
        if ($rawSemester && $rawSemester !== 'all') {
            $foundSem = collect($availablePeriods['allSemesters'])->firstWhere('semNumber', (int)$rawSemester);
            $activePeriodLabel = $foundSem['label'] ?? "Semester {$rawSemester}";
        } elseif ($rawTahun && $rawTahun !== 'all') {
            $foundYear = collect($availablePeriods['years'])->firstWhere('tahun', (string)$rawTahun);
            $activePeriodLabel = $foundYear ? "Tahun {$foundYear['label']}" : "Tahun {$rawTahun}";
        } else {
            $activePeriodLabel = 'Kumulatif (Semua Semester)';
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diolah',
            'result' => [
                'angkatan' => $angkatan,
                'npm' => $npm,
                'labelCpl' => $labelCpl,
                'nama' => $nama,
                'prodi' => $prodiNama,
                'is_aptikom' => auth()->user()->prodi->is_aptikom ?? false,
                'courseArray' => $courseArray,
                'soalTerendah' => $soalTerendah,
                'hasilFinalProfil' => $hasilFinalProfil,
                'chartDataProfil' => $chartDataProfil,
                'hasilFinalProfesi' => $hasilProfesi,
                'chartDataProfesi' => $chartProfesi,
                'courseCpmkRestrict' => $courseCpmkRestrict,
                'universitas' => $universitasNama,
                'imgSrc' => $imgSrc,
                // Batasan tahun & tabel tahunan CPL
                'yearsList' => $yearsList,
                'yearsWithData' => $yearsWithData,
                'lastActiveYear' => $lastActiveYear,
                'maxYears' => $maxYears,
                'jenjangProdi' => $jenjangProdi,
                'disclaimerMasaStudi' => $disclaimerMasaStudi,
                'skorCplPerTahun' => $skorCplPerTahun,
                'ketercapaianCplPerTahun' => $ketercapaianCplPerTahun,
                'skorCplOverall' => $skorCplOverall,
                'ketercapaianCplOverall' => $ketercapaianCplOverall,
                'skorCplYearlyAvg' => $skorCplYearlyAvg,
                'ketercapaianCplYearlyAvg' => $ketercapaianCplYearlyAvg,
                'skorCplGrandAvg' => $skorCplGrandAvg,
                'ketercapaianCplGrandAvg' => $ketercapaianCplGrandAvg,
                // REVISI TAHUN & SEMESTER
                'selectedTahun' => $rawTahun ?: 'all',
                'selectedSemester' => $rawSemester ?: 'all',
                'has_data' => $hasData,
                'activePeriodLabel' => $activePeriodLabel,
                'availablePeriods' => $availablePeriods,
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
        $decodeIfString = function ($val) {
            if (is_string($val)) {
                $decoded = json_decode($val, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $val;
            }
            return $val;
        };

        $yearsList = $decodeIfString($request->yearsList) ?? [];
        $yearsWithData = $decodeIfString($request->yearsWithData) ?? [];
        $skorCplPerTahun = $decodeIfString($request->skorCplPerTahun) ?? [];
        $ketercapaianCplPerTahun = $decodeIfString($request->ketercapaianCplPerTahun) ?? [];
        $skorCplOverall = $decodeIfString($request->skorCplOverall) ?? [];
        $ketercapaianCplOverall = $decodeIfString($request->ketercapaianCplOverall) ?? [];
        $skorCplYearlyAvg = $decodeIfString($request->skorCplYearlyAvg) ?? [];
        $ketercapaianCplYearlyAvg = $decodeIfString($request->ketercapaianCplYearlyAvg) ?? [];
        $courseList = $decodeIfString($request->courseList) ?? [];
        $soalTerendah = $decodeIfString($request->soalTerendah) ?? [];
        $hasilFinalProfil = $decodeIfString($request->hasilFinalProfil ?? $request->hasilProfil) ?? [];
        $mataKuliahLulus = $decodeIfString($request->mataKuliahLulus) ?? [];
        $mataKuliahTidakLulus = $decodeIfString($request->mataKuliahTidakLulus) ?? [];
        $descriptions = $decodeIfString($request->descriptions) ?? [];
        $rincianCplScores = $decodeIfString($request->rincianCplScores) ?? [];
        $komparasiCplAngkatan = $decodeIfString($request->komparasiCplAngkatan) ?? [];

        $data = [
            'nama' => $request->nama,
            'npm' => $request->npm,
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
            'tahun' => $request->tahun,
            'semester' => $request->semester,
            'jenjang' => $request->jenjang ?? 'S1',
            'maxYears' => $request->maxYears ?? 7,
            'disclaimerMasaStudi' => $request->disclaimerMasaStudi ?? '',
            'yearsList' => is_array($yearsList) ? $yearsList : [],
            'yearsWithData' => is_array($yearsWithData) ? $yearsWithData : [],
            'skorCplPerTahun' => is_array($skorCplPerTahun) ? $skorCplPerTahun : [],
            'ketercapaianCplPerTahun' => is_array($ketercapaianCplPerTahun) ? $ketercapaianCplPerTahun : [],
            'skorCplOverall' => is_array($skorCplOverall) ? $skorCplOverall : [],
            'ketercapaianCplOverall' => is_array($ketercapaianCplOverall) ? $ketercapaianCplOverall : [],
            'skorCplYearlyAvg' => is_array($skorCplYearlyAvg) ? $skorCplYearlyAvg : [],
            'ketercapaianCplYearlyAvg' => is_array($ketercapaianCplYearlyAvg) ? $ketercapaianCplYearlyAvg : [],
            'skorCplGrandAvg' => $request->skorCplGrandAvg ?? 0,
            'ketercapaianCplGrandAvg' => $request->ketercapaianCplGrandAvg ?? 0,
            'courseList' => is_array($courseList) ? $courseList : [],
            'radarChartCapaianCplImg' => $request->radarChartCapaianCplImg,
            'radarChartImg' => $request->radarChartImg,
            'profilChartImg' => $request->profilChartImg,
            'rincianCplScores' => is_array($rincianCplScores) ? $rincianCplScores : [],
            'komparasiCplAngkatan' => is_array($komparasiCplAngkatan) ? $komparasiCplAngkatan : [],
            'soalTerendah' => is_array($soalTerendah) ? $soalTerendah : [],
            'hasilFinalProfil' => is_array($hasilFinalProfil) ? $hasilFinalProfil : [],
            'hasilProfil' => is_array($hasilFinalProfil) ? $hasilFinalProfil : [],
            'mataKuliahLulus' => is_array($mataKuliahLulus) ? $mataKuliahLulus : [],
            'mataKuliahTidakLulus' => is_array($mataKuliahTidakLulus) ? $mataKuliahTidakLulus : [],
            'descriptions' => is_array($descriptions) ? $descriptions : []
        ];

        $pdf = Pdf::loadView('pdf.reportVisualisasiMahasiswa', $data);
        $pdf->setPaper('a4', 'portrait');

        $safeNama = str_replace(['/', '\\'], '-', $request->nama ?? 'Mahasiswa');
        return $pdf->download("Laporan Visualisasi Mahasiswa - {$safeNama}.pdf");
    }

    public function generatePDFhasilVisualAngkatan(Request $request)
    {
        $data = [
            'radarChartAngkatanImg' => $request->radarChartAngkatanImg,
            'summary' => $request->summary,
            'avgCpl' => $request->avgCpl,
            'maxCpl' => $request->maxCpl,
            'minCpl' => $request->minCpl,
            'yearsHeader' => $request->yearsHeader ?? [],
            'rincianCplPerTahun' => $request->rincianCplPerTahun ?? [],
            'yearlyAvg' => $request->yearlyAvg ?? [],
            'rincianCplScoresAngkatan' => $request->rincianCplScoresAngkatan ?? [],
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah ?? [],
            'mahasiswaAngkatan' => $request->mahasiswaAngkatan ?? [],
            'cplCodes' => $request->cplCodes ?? [],
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
            'courseList' => $request->courseList ?? [],
            'tahun' => $request->tahun,
            'semester' => $request->semester,
        ];

        $pdf = Pdf::loadView('pdf.reportVisualisasiAngkatan', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Laporan Visualisasi Angkatan - {$request->angkatan}.pdf");
    }

    public function generatePDFhasilVisualMataKuliah(Request $request)
    {
        $data = [
            'radarChartAngkatanImg' => $request->radarChartAngkatanImg,
            'summary' => $request->summary,
            'rincianCpmkAngkatan' => $request->rincianCpmkAngkatan ?? [],
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah ?? [],
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
            'course' => $request->course,
        ];

        $pdf = Pdf::loadView('pdf.reportVisualisasiMataKuliah', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Laporan Visualisasi Mata Kuliah - {$request->course}.pdf");
    }

    public function generatePDFhasilVisualCpmkMahasiswa(Request $request)
    {
        $data = [
            'course' => $request->course,
            'radarChartImg' => $request->radarChartImg,
            'cpmkScores' => $request->cpmkScores ?? [],
            'avgScore' => $request->avgScore ?? '-',
            'highestCpmk' => $request->highestCpmk ?? '-',
            'lowestCpmk' => $request->lowestCpmk ?? '-',
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah ?? [],
            'nama' => $request->nama,
            'npm' => $request->npm,
            'angkatan' => $request->angkatan,
            'prodi' => $request->prodi,
            'universitas' => $request->universitas,
        ];

        $pdf = Pdf::loadView('pdf.reportVisualisasiCPMKMahasiswa', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Laporan Visualisasi CPMK {$request->course} Mahasiswa - {$request->nama}.pdf");
    }

    public function generatePDFhasilVisualCpmkAngkatan(Request $request)
    {
        $course = $request->course ?? 'Mata Kuliah';
        $angkatan = $request->angkatan ?? '';
        $cleanCourse = preg_replace('~[\\\\/:*?"<>|]~', '-', $course);
        $cleanAngkatan = preg_replace('~[\\\\/:*?"<>|]~', '-', $angkatan);

        $data = [
            'course' => $course,
            'radarChartAngkatanImg' => $request->radarChartAngkatanImg,
            'summary' => $request->summary,
            'descriptions' => $request->descriptions ?? [],
            'soalTerendah' => $request->soalTerendah ?? [],
            'angkatan' => $angkatan,
            'prodi' => $request->prodi ?? '',
            'universitas' => $request->universitas ?? '',
        ];

        $pdf = Pdf::loadView('pdf.reportVisualisasiCPMKAngkatan', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Laporan Visualisasi CPMK {$cleanCourse} Angkatan - {$cleanAngkatan}.pdf");
    }

    public function hasilVisualCpmkMahasiswa(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $universitas = $request->universitasCPMK;
        $universitasImg = $request->universitasImg;

        $npm = $request->npm;
        $nama = $request->nama;
        $prodi = $request->prodi;
        $angkatan = $request->angkatan;
        $originalReqCourse = $request->course;
        $tahun = $request->tahun;
        $semester = $request->semester;

        $hasYearFilter = ($tahun && $tahun !== 'all');
        $hasSemFilter = ($semester && $semester !== 'all');

        list($course, $namaCourse) = explode('-', $originalReqCourse);
        
        $prodiId = Prodi::where('nama', $prodi)->value('id');
        if (!$prodiId && is_numeric($prodi)) {
            $prodiId = (int)$prodi;
            $prodi = Prodi::where('id', $prodiId)->value('nama') ?? $prodi;
        }
        $universitasId = Universitas::where('nama', $universitas)->value('id');
        if (!$universitasId && is_numeric($universitas)) {
            $universitasId = (int)$universitas;
            $universitas = Universitas::where('id', $universitasId)->value('nama') ?? $universitas;
        }
        if (empty($universitas) || $universitas === '-') {
            $userUniv = auth()->user()->universitas ?? (auth()->user()->prodi ? auth()->user()->prodi->fakultas->universitas ?? null : null);
            if ($userUniv) {
                $universitas = $userUniv->nama;
                $universitasId = $userUniv->id;
                if (empty($universitasImg) && !empty($userUniv->img)) {
                    $universitasImg = asset($userUniv->img);
                }
            } else {
                $defaultUniv = Universitas::first();
                if ($defaultUniv) {
                    $universitas = $defaultUniv->nama;
                    $universitasId = $defaultUniv->id;
                    if (empty($universitasImg) && !empty($defaultUniv->img)) {
                        $universitasImg = asset($defaultUniv->img);
                    }
                }
            }
        }

        $allNpmQuery = DB::table('mutus')
            ->select('npm')
            ->where('Course', $course)
            ->where('angkatan', $angkatan);

        if ($prodiId) {
            $allNpmQuery->where('id_prodi', $prodiId);
        }
        if ($universitasId) {
            $allNpmQuery->where('universitas_id', $universitasId);
        }

        $allNpm = $allNpmQuery->whereNotNull('npm')
            ->where('npm', '!=', '0')
            ->where('npm', '!=', '')
            ->distinct()
            ->orderBy('npm', 'asc')
            ->pluck('npm');

        if (!empty($npm) && !$allNpm->contains($npm)) {
            $allNpm->push($npm);
        }

        $mhsNamesFromTable = DB::table('mahasiswa')
            ->whereIn('NPM', $allNpm)
            ->pluck('Nama', 'NPM');

        $mutusNames = DB::table('mutus')
            ->whereIn('npm', $allNpm)
            ->whereNotNull('nama_mhs')
            ->where('nama_mhs', '!=', '')
            ->pluck('nama_mhs', 'npm');

        $allNamaNpmData = [];
        foreach ($allNpm as $oneNpm) {
            $mName = $mhsNamesFromTable[$oneNpm] ?? $mutusNames[$oneNpm] ?? ($oneNpm == $npm ? $nama : 'Mahasiswa ' . $oneNpm);
            $allNamaNpmData[] = (object)[
                'NPM' => $oneNpm,
                'nama_mhs' => $mName
            ];
        }

        $cpmkConditionSub = "";
        $cpmkConditionMain = "";
        $bindingsCpmk = [
            'npm1' => $npm,
            'course1' => $course,
            'npm2' => $npm,
            'course2' => $course,
            'npm3' => $npm,
            'course3' => $course,
            'npm4' => $npm,
            'course4' => $course,
        ];

        $yearExprCpmkM = $this->getYearSemSqlExpr('m', 'ta');
        $yearExprCpmkM2 = $this->getYearSemSqlExpr('m2', 'ta2');

        // CPMK PER SOAL MAHASISWA
        $query = "
                SELECT m.cpmk, 
                    SUM((m.BobotSoal * m.nilaiSoal) / q1.BobotJenisCPMK * m.examWeight / q2.sumExamWeight) AS r3, 
                    cpmks.kode
                FROM mutus m
                LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
                INNER JOIN (
                    SELECT Course, 
                        MAX($yearExprCpmkM2) AS max_year_semester
                    FROM mutus m2
                    LEFT JOIN tahun_ajaran ta2 ON m2.tahun_ajaran_id = ta2.id
                    WHERE m2.NPM = :npm1 AND m2.Course = :course1
                    GROUP BY Course
                ) t ON m.Course = t.Course 
                AND $yearExprCpmkM = t.max_year_semester
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

        $cpmkPerSoalWithKode = DB::select($query, $bindingsCpmk);

        $bindingsAngkatan = [
            'course1' => $course,
            'course2' => $course,
            'course3' => $course,
            'course4' => $course,
            'angkatan1' => $angkatan,
            'angkatan2' => $angkatan,
            'angkatan3' => $angkatan,
            'angkatan4' => $angkatan,
        ];

        $yearExprLy2 = $this->getYearSemSqlExpr('ly2', 'ta2');

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
                    LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
                    JOIN 
                        (SELECT 
                            ly2.NPM, 
                            MAX($yearExprLy2) AS max_year_semester
                        FROM 
                            mutus ly2
                        LEFT JOIN tahun_ajaran ta2 ON ly2.tahun_ajaran_id = ta2.id
                        WHERE 
                            ly2.Course = :course1 AND ly2.angkatan = :angkatan1
                        GROUP BY 
                            ly2.NPM
                        ) AS ly
                        ON m.NPM = ly.NPM
                        AND $yearExprCpmkM = ly.max_year_semester
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

        $cpmkPerSoalAngkatanWithKode = DB::select($queryCpmkAngkatan, $bindingsAngkatan);

        $hasBobotCol = \Illuminate\Support\Facades\Schema::hasColumn('cpmk_mk', 'bobot');
        $selectCols = ['cpmk_mk.cpmk_id as id', 'cpmks.judul', 'cpmks.kode'];
        if ($hasBobotCol) {
            $selectCols[] = 'cpmk_mk.bobot';
        }

        $cpmkResultAllQuery = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->leftJoin('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('mk_kode', $course)
            ->select($selectCols);

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            if (auth()->user()->id_universitasUser) {
                $cpmkResultAllQuery->where(function($q) {
                    $q->where('fakultas.id_universitas', auth()->user()->id_universitasUser)
                      ->orWhereNull('fakultas.id_universitas');
                });
            }
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            if (auth()->user()->id_fakultasUser) {
                $cpmkResultAllQuery->where(function($q) {
                    $q->where('fakultas.id', auth()->user()->id_fakultasUser)
                      ->orWhereNull('fakultas.id');
                });
            }
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi', 'Dosen'])) {
            if (auth()->user()->id_prodiUser) {
                $cpmkResultAllQuery->where(function($q) {
                    $q->where('prodi.id', auth()->user()->id_prodiUser)
                      ->orWhereNull('prodi.id');
                });
            }
        }
        $cpmkResultAll = $cpmkResultAllQuery->distinct()->get();

        $cpmkTmp = [];
        foreach ($cpmkResultAll as $itemCpmk) {
            $cpmkTmp[$itemCpmk->id] = [0, 0, 0, 0, $itemCpmk->kode];
        }

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

        // If keyMinCpl is not set by individual student score, use angkatan lowest CPMK or first available CPMK
        if (empty($keyMinCpl) || $keyMinCpl == 0) {
            $keyMinCpl = !empty($keyMinAvg) ? $keyMinAvg : ($cpmkResultAll->first()->id ?? 0);
        }

        // Ambil data soal asesmen CPMK terendah
        $soalDescQuery = DB::table('mutus')
            ->leftJoin('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->leftJoin('mks', 'mutus.Course', '=', 'mks.kode')
            ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
            ->select('mutus.id', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', DB::raw("COALESCE(mks.nama, mutus.Course) as namaCourse"), 'mutus.idSoal', 'soals.pertanyaan as soalFromId', 'mutus.cpmk')
            ->where('mutus.npm', $npm)
            ->where('mutus.Course', $course)
            ->distinct();

        if (!empty($keyMinCpl)) {
            $soalDescQuery->where('mutus.cpmk', $keyMinCpl);
        }

        $soalDesc = $soalDescQuery->get();

        $uniqueData = [];
        foreach ($soalDesc as $result) {
            $soal = !empty($result->soal) ? $result->soal : (!empty($result->soalFromId) ? $result->soalFromId : ($result->Jenis . ' - Soal Asesmen'));
            $uniqueKey = $result->namaCourse . '|' . $result->Jenis . '|' . $soal;

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

        // Menyiapkan data tabel rincian CPMK dan kalkulasi summary komprehensif
        $cpmkTableList = [];
        $totalScoresMhs = 0;
        $countScoresMhs = 0;
        $weightedScoresMhs = 0;
        $sumBobotMhs = 0;
        $countTercapai = 0;

        foreach ($cpmkResultAll as $itemCpmk) {
            $cpmkId = $itemCpmk->id;
            $mhsScore = isset($cpmkTmp[$cpmkId]) ? round((float)$cpmkTmp[$cpmkId][0], 2) : 0;
            $avgScore = isset($cpmkTmp[$cpmkId]) ? round((float)$cpmkTmp[$cpmkId][1], 2) : 0;
            $minScore = isset($cpmkTmp[$cpmkId]) ? round((float)$cpmkTmp[$cpmkId][2], 2) : 0;
            $maxScore = isset($cpmkTmp[$cpmkId]) ? round((float)$cpmkTmp[$cpmkId][3], 2) : 0;
            $bobotVal = (float)($itemCpmk->bobot ?? 0);

            $status = 'Belum Ada Data';
            if ($mhsScore >= 65) {
                $status = 'Tercapai';
                $countTercapai++;
            } elseif ($mhsScore > 0) {
                $status = 'Perlu Peningkatan';
            }

            $posisi = 'Belum Ada Data';
            if ($mhsScore > 0 || $avgScore > 0) {
                if ($mhsScore > $avgScore) {
                    $posisi = 'Di Atas Rata-rata';
                } elseif ($mhsScore < $avgScore) {
                    $posisi = 'Di Bawah Rata-rata';
                } else {
                    $posisi = 'Sama Rata-rata';
                }
            }

            if ($mhsScore > 0) {
                $totalScoresMhs += $mhsScore;
                $countScoresMhs++;

                if ($bobotVal > 0) {
                    $weightedScoresMhs += ($mhsScore * $bobotVal);
                    $sumBobotMhs += $bobotVal;
                }
            }

            $cpmkTableList[] = [
                'id' => $cpmkId,
                'kode' => $itemCpmk->kode,
                'judul' => $itemCpmk->judul,
                'bobot' => $bobotVal,
                'nilai_mhs' => $mhsScore,
                'avg_angkatan' => $avgScore,
                'min_angkatan' => $minScore,
                'max_angkatan' => $maxScore,
                'status' => $status,
                'posisi' => $posisi,
            ];
        }

        $totalCpmkCount = count($cpmkResultAll);
        if ($sumBobotMhs > 0) {
            $rataRataMhs = round($weightedScoresMhs / $sumBobotMhs, 2);
        } else {
            $rataRataMhs = $countScoresMhs > 0 ? round($totalScoresMhs / $countScoresMhs, 2) : 0;
        }

        return view('penjamin-mutu.visualisasi.hasilVisualisasiCpmkMahasiswa', [
            'nama' => $nama,
            'npm' => $npm,
            'prodi' => $prodi,
            'angkatan' => $angkatan,
            'courseKode' => $course,
            'courseNama' => $namaCourse,
            'completeCourseFormat' => $originalReqCourse,
            'allNamaNpmData' => $allNamaNpmData,
            'allNpm' => $allNpm,
            'universitas' => $universitas,
            'universitasImg' => $universitasImg,
            // ABis Semhas
            'cpmkTmp' => $cpmkTmp,
            'cpmkResultAll' => $cpmkResultAll,
            'cpmkTableList' => $cpmkTableList,
            'totalCpmkCount' => $totalCpmkCount,
            'rataRataMhs' => $rataRataMhs,
            'countTercapai' => $countTercapai,
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

    private function getAvailablePeriodsForAngkatan($angkatan, $prodiId, $universitas)
    {
        $baseAngkatan = (int)$angkatan > 1900 ? (int)$angkatan : (int)date('Y') - 4;
        if ($baseAngkatan < 2000) {
            $baseAngkatan = (int)date('Y') - 4;
        }

        $batchSemesters = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->where('mutus.angkatan', $angkatan)
            ->where('mutus.id_prodi', $prodiId)
            ->where('mutus.universitas_id', $universitas)
            ->pluck('mks.semester')
            ->unique()
            ->map(function($v) { return (int)$v; })
            ->toArray();

        $yearsList = [];
        $allSemestersList = [];

        for ($i = 0; $i < 4; $i++) {
            $calYear = $baseAngkatan + $i;
            $nextYear = $calYear + 1;
            $academicYearStr = "{$calYear}/{$nextYear}";

            $sem1Num = $i * 2 + 1;
            $sem2Num = $i * 2 + 2;

            $hasSem1 = in_array($sem1Num, $batchSemesters);
            $hasSem2 = in_array($sem2Num, $batchSemesters);
            $hasYear = $hasSem1 || $hasSem2;

            $semestersInYear = [
                [
                    'semNumber' => $sem1Num,
                    'jenis' => 'Ganjil',
                    'tahun' => (string)$calYear,
                    'academicYear' => $academicYearStr,
                    'label' => "Semester {$sem1Num} (Ganjil {$academicYearStr})",
                    'hasData' => $hasSem1
                ],
                [
                    'semNumber' => $sem2Num,
                    'jenis' => 'Genap',
                    'tahun' => (string)$calYear,
                    'academicYear' => $academicYearStr,
                    'label' => "Semester {$sem2Num} (Genap {$academicYearStr})",
                    'hasData' => $hasSem2
                ]
            ];

            $yearsList[] = [
                'tahun' => (string)$calYear,
                'academicYear' => $academicYearStr,
                'yearIndex' => $i + 1,
                'label' => $academicYearStr,
                'hasData' => $hasYear,
                'semesters' => $semestersInYear
            ];

            $allSemestersList[] = $semestersInYear[0];
            $allSemestersList[] = $semestersInYear[1];
        }

        return [
            'years' => $yearsList,
            'allSemesters' => $allSemestersList
        ];
    }

    public function hasilVisualMahasiswaAngkatan(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;
        $prodiId = $request->input('prodi') ?? auth()->user()->id_prodiUser;
        $prodi = Prodi::where('id', $prodiId)->get();
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');
        $univNama = Universitas::where('id', $universitas)->value('nama');
        $imgSrc = $request->input('imgSrc');
        $rawTahun = $request->input('tahun'); // 'all', '2022', '2022/2023', '1'
        $rawSemester = $request->input('semester'); // 'all', '1'..'8'

        $baseAngkatan = (int)$angkatan > 1900 ? (int)$angkatan : (int)date('Y') - 4;
        if ($baseAngkatan < 2000) {
            $baseAngkatan = (int)date('Y') - 4;
        }

        $availablePeriods = $this->getAvailablePeriodsForAngkatan($angkatan, $prodiId, $universitas);

        $semestersToFilter = [];
        $activePeriodLabel = 'Kumulatif (Semua Semester)';

        if ($rawSemester && $rawSemester !== 'all' && is_numeric($rawSemester)) {
            $semNum = (int)$rawSemester;
            $semestersToFilter = [$semNum];
            $foundSem = collect($availablePeriods['allSemesters'])->firstWhere('semNumber', $semNum);
            $activePeriodLabel = $foundSem['label'] ?? "Semester {$semNum}";
        } elseif ($rawTahun && $rawTahun !== 'all') {
            $yearOffset = 0;
            $calYear = $baseAngkatan;
            if (is_numeric($rawTahun)) {
                $val = (int)$rawTahun;
                if ($val >= 2000) {
                    $yearOffset = $val - $baseAngkatan;
                    $calYear = $val;
                } else {
                    $yearOffset = $val - 1;
                    $calYear = $baseAngkatan + $yearOffset;
                }
            } elseif (strpos($rawTahun, '/') !== false) {
                $startYear = (int)explode('/', $rawTahun)[0];
                $yearOffset = $startYear - $baseAngkatan;
                $calYear = $startYear;
            }
            $nextYear = $calYear + 1;
            $sem1 = $yearOffset * 2 + 1;
            $sem2 = $yearOffset * 2 + 2;
            $semestersToFilter = [$sem1, $sem2];
            $foundYear = collect($availablePeriods['years'])->firstWhere('tahun', (string)$rawTahun);
            $academicYearStr = $foundYear['academicYear'] ?? "{$calYear}/{$nextYear}";
            $activePeriodLabel = "Tahun {$academicYearStr} (Semester {$sem1} & {$sem2})";
        }

        // Fetch all students in angkatan from mahasiswa table
        $mhsRows = DB::table('mahasiswa')
            ->where('id_prodi', $prodiId)
            ->where('angkatan', $angkatan)
            ->select('NPM as npm', 'Nama as nama_mhs')
            ->get();

        // Fetch any additional students from mutus table
        $mutuRows = DB::table('mutus')
            ->where('angkatan', $angkatan)
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->select('npm', 'nama_mhs')
            ->distinct()
            ->get();

        $studentMap = [];
        foreach ($mhsRows as $m) {
            if (!empty($m->npm)) {
                $studentMap[$m->npm] = $m->nama_mhs ?? 'N/A';
            }
        }
        foreach ($mutuRows as $m) {
            if (!empty($m->npm)) {
                if (!isset($studentMap[$m->npm]) || empty($studentMap[$m->npm])) {
                    $studentMap[$m->npm] = $m->nama_mhs ?? 'N/A';
                }
            }
        }

        $allNpm = array_keys($studentMap);

        $persentaseTotalCplCapaianAngkatan = [];

        $allCplPerAngkatanQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id', 'cpls.kode')
            ->where('cpls.id_prodi', $prodiId);

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $allCplPerAngkatanQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $allCplPerAngkatanQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
        }

        $allCplPerAngkatanQuery = $allCplPerAngkatanQuery->get();
        $allCplPerAngkatan = [
            'total' => [],
            'min' => [],
            'max' => []
        ];

        foreach ($allCplPerAngkatanQuery as $cpl) {
            $allCplPerAngkatan['total'][$cpl->kode] = 0;
            $allCplPerAngkatan['min'][$cpl->kode] = 101;
            $allCplPerAngkatan['max'][$cpl->kode] = 0;
        }

        $allCplPerAngkatan['count'] = 0;
        $gabunganMkAngkatan = [];
        $persenPerMhs = [];

        // LOOPING CAPAIAN CPL PER ANGKATAN
        foreach ($allNpm as $npm) {
            $subQuery = DB::table('mutus')
                ->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
                ->select('mutus.Course', DB::raw("MAX(" . $this->getYearSemSqlExpr('mutus', 'ta') . ") AS max_year_semester"))
                ->where('mutus.NPM', $npm)
                ->where('mutus.id_prodi', $prodiId);

            if (!empty($semestersToFilter)) {
                $subQuery->whereIn('mks.semester', $semestersToFilter);
            }

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $subQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $subQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            }

            $subQuery = $subQuery->groupBy('mutus.Course');

            // Main query to get the records with the max year and semester
            $subQueryMutus = DB::table('mutus as m')
                ->join('mks', 'm.Course', '=', 'mks.kode')
                ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
                ->joinSub(
                    $subQuery,
                    't',
                    function ($join) {
                        $join->on('m.Course', '=', 't.Course')
                            ->on(DB::raw($this->getYearSemSqlExpr('m', 'ta')), '=', 't.max_year_semester');
                    }
                )
                ->where('m.NPM', $npm);

            if (!empty($semestersToFilter)) {
                $subQueryMutus->whereIn('mks.semester', $semestersToFilter);
            }

            $subQueryMutus = $subQueryMutus->groupBy('m.npm', 'm.Course', 'm.jenis')
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
                ->where('mutus.id_prodi', $prodiId)
                ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'));

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $nilaiMk->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $nilaiMk->where('fakultas.id', auth()->user()->id_fakultasUser);
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
                ->where('mutus.id_prodi', $prodiId)
                ->whereIn('Course', array_keys($nilaiMkLulusAngkatan));

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplNilaiMkAngkatanQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            }

            $cplNilaiMkAngkatan = $cplNilaiMkAngkatanQuery->groupBy('mutus.Course', 'mutus.Cpl')->get();

            $cplNilaiMkUnique = $cplNilaiMkAngkatan->pluck('Cpl')->unique()->toArray();

            $cplResultsQuery = DB::table('mk_cpl')
                ->join('prodi', 'mk_cpl.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->where('mk_cpl.id_prodi', $prodiId)
                ->whereIn('cpl_id', $cplNilaiMkUnique);

            $cplResultsAllQuery = DB::table('cpls')
                ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
                ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
                ->where('cpls.id_prodi', $prodiId);

            if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
                $cplResultsQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
                $cplResultsAllQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
            } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
                $cplResultsQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
                $cplResultsAllQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
            }

            $cplResults = $cplResultsQuery->get()->groupBy('cpl_id');
            $cplResultsAll = $cplResultsAllQuery->get(['cpls.id', 'kode']);

            $persentaseTotalCplCapaianIndividu = [];
            $persenPerMhs[$npm] = [];

            $calculatedPersenPerCpl = [];
            foreach ($cplResults as $id_cpl => $items) {
                $kode_mk_list = $items->pluck('mk_kode')->toArray();
                $kodeMkArray = explode(',', implode(',', $kode_mk_list));
                $countKesamaan = count(array_intersect($kodeMkArray, array_keys($nilaiMkLulusAngkatan)));
                $persentase = count($items) > 0 ? ($countKesamaan / count($items)) * 100 : 0;
                $calculatedPersenPerCpl[$id_cpl] = [
                    'persentase' => $persentase,
                    'kode_mk' => implode(',', $kode_mk_list),
                    'count_mk' => count($items),
                ];
            }

            foreach ($allCplPerAngkatanQuery as $cpl) {
                $cplCode = $cpl->kode;
                $cplId = $cpl->id;
                $persentase = isset($calculatedPersenPerCpl[$cplId]) ? $calculatedPersenPerCpl[$cplId]['persentase'] : 0.0;
                $persenPerMhs[$npm][$cplCode] = round($persentase, 2);

                if (isset($calculatedPersenPerCpl[$cplId])) {
                    $persentaseTotalCplCapaianIndividu[] = [
                        'cpl' => $cplId,
                        'kode_mk' => $calculatedPersenPerCpl[$cplId]['kode_mk'],
                        'count_mk' => $calculatedPersenPerCpl[$cplId]['count_mk'],
                        'persentase' => round($persentase, 2),
                        'kode_cpl' => $cplCode,
                    ];
                }

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
        $totalAngkatanCount = $allCplPerAngkatan['count'] > 0 ? $allCplPerAngkatan['count'] : 1;
        foreach ($allCplPerAngkatan['total'] as $cpl => $total) {
            $allCplPerAngkatan['avg_cpl'][$cpl] = round($total / $totalAngkatanCount, 2);
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
            ->where('mks.id_prodi', $prodiId)
            ->whereIn('kode', $gabunganMkAngkatan);

        if (!empty($semestersToFilter)) {
            $gabunganAkhirMkQuery->whereIn('mks.semester', $semestersToFilter);
        }

        // Semua label cpl
        $labelCplQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.kode', 'cpls.judul')
            ->where('cpls.id_prodi', $prodiId);
        // Buat milih cpl terkait sama mk apa
        $cplResultsAllQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('cpls.id_prodi', $prodiId);

        // BUAT RATA-RATA CPL TERENDAH
        $idCplAvgMinQuery = DB::table('cpls')
            ->join('prodi', 'cpls.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->select('cpls.id')
            ->where('cpls.id_prodi', $prodiId)
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

        if (!empty($semestersToFilter)) {
            $soalDescQuery->whereIn('mks.semester', $semestersToFilter);
        }

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $soalDescQuery->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $soalDescQuery->where('fakultas.id', auth()->user()->id_fakultasUser);
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

        // Fetch all CPLs for this program
        $allCpls = DB::table('cpls')
            ->where('id_prodi', $prodiId)
            ->orderBy('id')
            ->get();

        // Resolusi batasan tahun dan jenjang program studi untuk angkatan
        $prodiObj = DB::table('prodi')->where('id', $prodiId)->first();
        $periodConfig = $this->getStudyPeriodConfigByProdi($prodiObj);
        $jenjangProdi = $periodConfig['jenjang'];
        $maxYears = $periodConfig['maxYears'];
        $normalDuration = $periodConfig['normalYears'];
        $normalSemesters = $periodConfig['normalSemesters'];
        $normalYearsSpan = $periodConfig['normalYearsSpan'];

        $batchSemesters = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->where('mutus.angkatan', $angkatan)
            ->where('mutus.id_prodi', $prodiId)
            ->pluck('mks.semester')
            ->map(fn($v) => (int)$v)
            ->unique()
            ->toArray();

        $actualSem = !empty($batchSemesters) ? max($batchSemesters) : 0;
        $extraYears = $actualSem > $normalSemesters ? (int) ceil(($actualSem - $normalSemesters) / 2) : 0;
        $displayYearsCount = min($maxYears, $normalYearsSpan + $extraYears);

        $yearsList = [];
        for ($i = 0; $i < $displayYearsCount; $i++) {
            $yearsList[] = $baseAngkatan + $i;
        }

        $yearsWithData = [];
        $activeYearsList = [];
        for ($i = 0; $i < $displayYearsCount; $i++) {
            $currentYear = $baseAngkatan + $i;
            if ($i == 0) {
                $minSemInYr = 1;
                $semestersInYr = [1];
            } elseif ($i < $normalYearsSpan) {
                $minSemInYr = $i * 2;
                $semestersInYr = [$i * 2, $i * 2 + 1];
            } else {
                $offsetSem = $normalSemesters + (($i - $normalYearsSpan + 1) * 2);
                $minSemInYr = $offsetSem - 1;
                $semestersInYr = [$offsetSem - 1, $offsetSem];
            }

            $hasDataYr = ($actualSem >= $minSemInYr) || (count(array_intersect($semestersInYr, $batchSemesters)) > 0);
            $yearsWithData[$currentYear] = $hasDataYr;
            if ($hasDataYr) {
                $activeYearsList[] = $currentYear;
            }
        }

        $mkCplFromDb = DB::table('mk_cpl')->where('id_prodi', $prodiId)->get()->groupBy('cpl_id');
        $mutusCplFromDb = DB::table('mutus')->where('angkatan', $angkatan)->where('id_prodi', $prodiId)->whereNotNull('Cpl')->select('Course as mk_kode', 'Cpl as cpl_id')->distinct()->get()->groupBy('cpl_id');

        $mkCplGrouped = [];
        foreach ($cplResultsAll as $cpl) {
            $cplId = $cpl->id;
            $mksFromMkCpl = isset($mkCplFromDb[$cplId]) ? $mkCplFromDb[$cplId]->pluck('mk_kode')->toArray() : [];
            $mksFromMutus = isset($mutusCplFromDb[$cplId]) ? $mutusCplFromDb[$cplId]->pluck('mk_kode')->toArray() : [];
            
            $combinedMks = array_unique(array_merge($mksFromMkCpl, $mksFromMutus));
            $mkCplGrouped[$cplId] = collect(array_map(function($mk) {
                return (object)['mk_kode' => $mk];
            }, $combinedMks));
        }

        $allCplPerAngkatan = [
            'total' => [],
            'min' => [],
            'max' => []
        ];
        foreach ($cplResultsAll as $cpl) {
            $allCplPerAngkatan['total'][$cpl->kode] = 0;
            $allCplPerAngkatan['min'][$cpl->kode] = 101;
            $allCplPerAngkatan['max'][$cpl->kode] = 0;
        }
        $allCplPerAngkatan['count'] = 0;

        $batchYearlyCplTotals = [];
        foreach ($cplResultsAll as $cpl) {
            $batchYearlyCplTotals[$cpl->kode] = [];
            foreach ($yearsList as $yr) {
                $batchYearlyCplTotals[$cpl->kode][$yr] = 0;
            }
        }

        $yearExprQ1 = $this->getYearSemSqlExpr('m', 'ta');
        $yearExprQ2 = $this->getYearSemSqlExpr('m2', 'ta2');
        $yearExprBatch = $this->getYearSemSqlExpr('mutus', 'ta');
        $yearExprBatchM = $this->getYearSemSqlExpr('m', 'ta');

        $mahasiswaAngkatan = [];

        // Hitung Ketercapaian CPL & Skor CPL Per Mahasiswa (Sesuai Visualisasi Mahasiswa di Tahun Terakhir)
        foreach ($allNpm as $npm) {
            $namaMhs = $studentMap[$npm] ?? 'N/A';

            $studentSemesters = DB::table('mutus')
                ->join('mks', 'mutus.Course', '=', 'mks.kode')
                ->where('mutus.NPM', $npm)
                ->pluck('mks.semester')
                ->map(fn($val) => (int)$val)
                ->unique()
                ->toArray();
            $studentActualSem = !empty($studentSemesters) ? max($studentSemesters) : 0;
            $studentExtraYears = $studentActualSem > $normalSemesters ? (int) ceil(($studentActualSem - $normalSemesters) / 2) : 0;
            $studentDisplayYearsCount = min($maxYears, $normalYearsSpan + $studentExtraYears);

            $studentActiveYears = [];
            for ($i = 0; $i < $studentDisplayYearsCount; $i++) {
                $currentYear = $baseAngkatan + $i;
                if ($i == 0) {
                    $minSemInYr = 1;
                    $semestersInYr = [1];
                } elseif ($i < $normalYearsSpan) {
                    $minSemInYr = $i * 2;
                    $semestersInYr = [$i * 2, $i * 2 + 1];
                } else {
                    $offsetSem = $normalSemesters + (($i - $normalYearsSpan + 1) * 2);
                    $minSemInYr = $offsetSem - 1;
                    $semestersInYr = [$offsetSem - 1, $offsetSem];
                }

                $hasDataYr = ($studentActualSem >= $minSemInYr) || (count(array_intersect($semestersInYr, $studentSemesters)) > 0);
                if ($hasDataYr) {
                    $studentActiveYears[] = $currentYear;
                }
            }

            $skorCplPerTahun = [];
            $ketercapaianCplPerTahun = [];

            foreach ($cplResultsAll as $cpl) {
                $skorCplPerTahun[$cpl->kode] = [];
                $ketercapaianCplPerTahun[$cpl->kode] = [];
            }

            for ($i = 0; $i < $displayYearsCount; $i++) {
                $currentYear = $baseAngkatan + $i;
                $batchHasDataYr = $yearsWithData[$currentYear] ?? true;

                if (!$batchHasDataYr) {
                    foreach ($cplResultsAll as $cpl) {
                        $skorCplPerTahun[$cpl->kode][$currentYear] = null;
                        $ketercapaianCplPerTahun[$cpl->kode][$currentYear] = null;
                    }
                    continue;
                }

                if ($i == 0) {
                    $maxSem = 1;
                } elseif ($i < $normalYearsSpan) {
                    $maxSem = $i * 2;
                } else {
                    $maxSem = ($normalDuration * 2) + (($i - $normalYearsSpan + 1) * 2);
                }
                $semestersUpToYear = range(1, min(14, $maxSem));
                $semListStr = implode(',', $semestersUpToYear);

                // A. Ketercapaian CPL (%)
                $subQueryYr = DB::table('mutus')
                    ->join('mks', 'mutus.Course', '=', 'mks.kode')
                    ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                    ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
                    ->select('mutus.Course', DB::raw("MAX(" . $this->getYearSemSqlExpr('mutus', 'ta') . ") AS max_year_semester"))
                    ->where('mutus.NPM', $npm)
                    ->whereIn('mks.semester', $semestersUpToYear)
                    ->where('prodi.id', $prodiId)
                    ->groupBy('mutus.Course');

                $subQueryMutusYr = DB::table('mutus as m')
                    ->join('mks', 'm.Course', '=', 'mks.kode')
                    ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
                    ->joinSub($subQueryYr, 't', function ($join) {
                        $join->on('m.Course', '=', 't.Course')
                            ->on(DB::raw($this->getYearSemSqlExpr('m', 'ta')), '=', 't.max_year_semester');
                    })
                    ->where('m.NPM', $npm)
                    ->whereIn('mks.semester', $semestersUpToYear)
                    ->groupBy('m.npm', 'm.Course', 'm.jenis')
                    ->select('m.npm', 'm.Course', 'm.jenis', 'm.id');

                $nilaiMkYr = DB::table('mutus')
                    ->join('mks', 'mutus.Course', '=', 'mks.kode')
                    ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
                    ->joinSub($subQueryMutusYr, 'sub', function ($join) {
                        $join->on('mutus.id', '=', 'sub.id');
                    })
                    ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'))
                    ->whereIn('mks.semester', $semestersUpToYear)
                    ->where('prodi.id', $prodiId)
                    ->groupBy('mutus.Course')
                    ->get();

                $passedCoursesYr = [];
                foreach ($nilaiMkYr as $nm) {
                    if ($nm->total_nilai >= 50) {
                        $passedCoursesYr[] = $nm->Course;
                    }
                }

                foreach ($cplResultsAll as $cpl) {
                    $items = $mkCplGrouped[$cpl->id] ?? collect([]);
                    $kode_mk_list = $items->pluck('mk_kode')->toArray();
                    $kodeMkArray = array_filter(explode(',', implode(',', $kode_mk_list)));
                    $countKesamaan = count(array_intersect($kodeMkArray, $passedCoursesYr));
                    $pct = count($items) > 0 ? round(($countKesamaan / count($items)) * 100, 2) : 0;

                    if ($i > 0) {
                        $prevYear = $baseAngkatan + $i - 1;
                        $prevPct = $ketercapaianCplPerTahun[$cpl->kode][$prevYear] ?? 0;
                        if ($prevPct >= 100) {
                            $pct = 100.0;
                        } elseif ($prevPct !== null && $prevPct > 0) {
                            if ($pct < $prevPct) {
                                $pct = $prevPct;
                            }
                        }
                    }
                    $ketercapaianCplPerTahun[$cpl->kode][$currentYear] = $pct;
                    if (isset($batchYearlyCplTotals[$cpl->kode][$currentYear])) {
                        $batchYearlyCplTotals[$cpl->kode][$currentYear] += $pct;
                    }
                }

                // B. Skor CPL
                $cplConditionSubYr = " AND m2.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";
                $cplConditionMainYr = " AND m.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";

                $queryYr = "
                    SELECT cpl, cpls.kode as kode, AVG(hasil) as hasil FROM (
                        SELECT Cpl, Course, SUM(hasil) as hasil 
                        FROM (
                            SELECT q1.tahun, q1.Cpl, q1.Jenis, q1.Course, 
                                (CASE WHEN q1.nilaiSoal IS NOT NULL AND q1.BobotSoal > 0 AND q2.BobotJenis > 0 THEN (q1.nilaiSoal * q1.BobotSoal / q2.BobotJenis) * q1.examWeight / COALESCE(NULLIF(q3.sumExamWeight, 0), 100) ELSE COALESCE(q1.Nilai, 0) * q1.examWeight / COALESCE(NULLIF(q3.sumExamWeight, 0), 100) END) as hasil 
                            FROM (
                                SELECT m.tahun, m.Cpl, m.Jenis, m.Course, m.nilaiSoal, m.Nilai, m.examWeight, m.BobotSoal 
                                FROM mutus m 
                                LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
                                JOIN (
                                    SELECT Course, MAX($yearExprQ2) AS max_year_semester 
                                    FROM mutus m2
                                    LEFT JOIN tahun_ajaran ta2 ON m2.tahun_ajaran_id = ta2.id
                                    WHERE m2.NPM = :npm1 {$cplConditionSubYr}
                                    GROUP BY Course 
                                ) t 
                                ON m.Course = t.Course AND $yearExprQ1 = t.max_year_semester 
                                WHERE m.NPM = :npm2 {$cplConditionMainYr}
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
                    WHERE prodi.id = :id_prodi
                    GROUP BY Cpl;
                ";

                $cplScoresResultYr = DB::select($queryYr, [
                    'npm1' => $npm,
                    'npm2' => $npm,
                    'npm3' => $npm,
                    'npm4' => $npm,
                    'id_prodi' => $prodiId
                ]);

                $cplScoreMapYr = [];
                foreach ($cplScoresResultYr as $res) {
                    $cplScoreMapYr[$res->kode] = round($res->hasil, 2);
                }

                foreach ($cplResultsAll as $cpl) {
                    $rawScore = $cplScoreMapYr[$cpl->kode] ?? 0;
                    if ($i > 0) {
                        $prevYear = $baseAngkatan + $i - 1;
                        $prevScore = $skorCplPerTahun[$cpl->kode][$prevYear] ?? 0;
                        if ($prevScore !== null && $rawScore < $prevScore && $prevScore > 0) {
                            $rawScore = $prevScore;
                        }
                    }
                    $skorCplPerTahun[$cpl->kode][$currentYear] = $rawScore;
                }
            }

            $lastActiveYear = count($studentActiveYears) > 0 ? end($studentActiveYears) : $baseAngkatan;

            foreach ($cplResultsAll as $cpl) {
                $skorHasil = $skorCplPerTahun[$cpl->kode][$lastActiveYear] ?? 0;
                $persenHasil = $ketercapaianCplPerTahun[$cpl->kode][$lastActiveYear] ?? 0;

                $mahasiswaAngkatan[] = [
                    'npm' => (string)$npm,
                    'nama_mhs' => $namaMhs,
                    'kode' => $cpl->kode,
                    'hasil' => round((float)$skorHasil, 2),
                    'persentase' => round((float)$persenHasil, 2),
                ];

                $allCplPerAngkatan['total'][$cpl->kode] += $persenHasil;
                if ($allCplPerAngkatan['min'][$cpl->kode] >= round($persenHasil, 2)) {
                    $allCplPerAngkatan['min'][$cpl->kode] = round($persenHasil, 2);
                }
                if ($allCplPerAngkatan['max'][$cpl->kode] <= round($persenHasil, 2)) {
                    $allCplPerAngkatan['max'][$cpl->kode] = round($persenHasil, 2);
                }
            }

            $allCplPerAngkatan['count'] += 1;
        }

        $totalAngkatanCount = max(1, $allCplPerAngkatan['count']);
        foreach ($allCplPerAngkatan['total'] as $cpl => $total) {
            $allCplPerAngkatan['avg_cpl'][$cpl] = round($total / $totalAngkatanCount, 2);
            if ($allCplPerAngkatan['min'][$cpl] > 100) {
                $allCplPerAngkatan['min'][$cpl] = 0;
            }
        }

        // Batch yearly ketercapaian (Monotonik: Tetap atau Naik, Tidak Mungkin Turun)
        $batchKetercapaianCplPerTahun = [];
        for ($i = 0; $i < count($yearsList); $i++) {
            $yr = $yearsList[$i];
            $hasDataYr = $yearsWithData[$yr] ?? true;
            if (!$hasDataYr) {
                foreach ($cplResultsAll as $cpl) {
                    $batchKetercapaianCplPerTahun[$cpl->kode][$yr] = null;
                }
            } else {
                foreach ($cplResultsAll as $cpl) {
                    $avgYr = round(($batchYearlyCplTotals[$cpl->kode][$yr] ?? 0) / $totalAngkatanCount, 2);
                    if ($i > 0) {
                        $prevYr = $yearsList[$i - 1];
                        $prevAvg = $batchKetercapaianCplPerTahun[$cpl->kode][$prevYr] ?? null;
                        if ($prevAvg !== null && $avgYr < $prevAvg) {
                            $avgYr = $prevAvg;
                        }
                    }
                    $batchKetercapaianCplPerTahun[$cpl->kode][$yr] = $avgYr;
                }
            }
        }
        $ketercapaianCplPerTahun = $batchKetercapaianCplPerTahun;

        // Sinkronkan Average CPL Batch Overall dengan tahun aktif terakhir
        $lastBatchYear = count($activeYearsList) > 0 ? end($activeYearsList) : $baseAngkatan;
        foreach ($cplResultsAll as $cpl) {
            if (isset($batchKetercapaianCplPerTahun[$cpl->kode][$lastBatchYear]) && $batchKetercapaianCplPerTahun[$cpl->kode][$lastBatchYear] !== null) {
                $allCplPerAngkatan['avg_cpl'][$cpl->kode] = $batchKetercapaianCplPerTahun[$cpl->kode][$lastBatchYear];
            }
        }

        // Konversi array ke collection jika perlu
        $mahasiswaAngkatan = collect($mahasiswaAngkatan);
        // MAHASISWA ANGKATAN END

        $ketercapaianCplYearlyAvg = [];
        for ($i = 0; $i < count($yearsList); $i++) {
            $yr = $yearsList[$i];
            if (!($yearsWithData[$yr] ?? true)) {
                $ketercapaianCplYearlyAvg[$yr] = null;
                continue;
            }
            $sumPct = 0; $cntPct = 0;
            foreach ($cplResultsAll as $cpl) {
                if (isset($ketercapaianCplPerTahun[$cpl->kode][$yr]) && $ketercapaianCplPerTahun[$cpl->kode][$yr] !== null) {
                    $sumPct += $ketercapaianCplPerTahun[$cpl->kode][$yr];
                    $cntPct++;
                }
            }
            $avgPct = $cntPct > 0 ? round($sumPct / $cntPct, 2) : 0;
            if ($i > 0) {
                $prevYr = $yearsList[$i - 1];
                $prevAvg = $ketercapaianCplYearlyAvg[$prevYr] ?? null;
                if ($prevAvg !== null && $avgPct < $prevAvg) {
                    $avgPct = $prevAvg;
                }
            }
            $ketercapaianCplYearlyAvg[$yr] = $avgPct;
        }

        $allCoursesInProdi = DB::table('mks')
            ->where('id_prodi', $prodiId)
            ->orderBy('nama')
            ->pluck('nama', 'kode')
            ->toArray();

        $defaultYear = !empty($activeYearsList) ? end($activeYearsList) : (!empty($yearsList) ? end($yearsList) : null);
        $hasData = (count($gabunganAkhirMk) > 0) && ($allCplPerAngkatan['count'] > 0);

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
                'allCourses' => $allCoursesInProdi,
                'cplResultsAll' => $cplResultsAll,
                'availablePeriods' => $availablePeriods,
                'yearsList' => $yearsList,
                'yearsWithData' => $yearsWithData,
                'ketercapaianCplPerTahun' => $ketercapaianCplPerTahun,
                'ketercapaianCplYearlyAvg' => $ketercapaianCplYearlyAvg,
                'defaultYear' => $defaultYear,
                'selectedTahun' => $rawTahun ?? 'all',
                'selectedSemester' => $rawSemester ?? 'all',
                'activePeriodLabel' => $activePeriodLabel,
                'hasData' => $hasData
            ],
            'showVisualContainer' => true
        ]);
    }

    public function getAngkatanByProdiUniversitas(Request $request)
    {
        $prodiId = $request->input('prodi');
        $universitas = $request->input('universitas');
        
        if (!is_numeric($prodiId) && !empty($prodiId)) {
            $resolvedId = Prodi::where('nama', $prodiId)->value('id');
            if ($resolvedId) {
                $prodiId = $resolvedId;
            }
        }
        if (!is_numeric($universitas) && !empty($universitas)) {
            $resolvedUnivId = Universitas::where('nama', $universitas)->value('id');
            if ($resolvedUnivId) {
                $universitas = $resolvedUnivId;
            }
        }
        
        $angkatanMutus = DB::table('mutus')
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->whereNotNull('angkatan')
            ->distinct()
            ->pluck('angkatan')
            ->toArray();

        $angkatanMhs = DB::table('mahasiswa')
            ->where('id_prodi', $prodiId)
            ->whereNotNull('angkatan')
            ->distinct()
            ->pluck('angkatan')
            ->toArray();

        $allAngkatan = array_values(array_unique(array_merge($angkatanMutus, $angkatanMhs)));
        rsort($allAngkatan);

        $options = '<option value="">Pilih Angkatan</option>';
        foreach ($allAngkatan as $a) {
            if (!empty($a)) {
                $options .= '<option value="' . $a . '">' . $a . '</option>';
            }
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
        if (!$prodiId && is_numeric($prodi)) {
            $prodiId = (int)$prodi;
            $prodi = Prodi::where('id', $prodiId)->value('nama') ?? $prodi;
        }
        $universitas = $request->universitasCPMK;
        $universitasId = Universitas::where('nama', $universitas)->value('id');
        if (!$universitasId && is_numeric($universitas)) {
            $universitasId = (int)$universitas;
            $universitas = Universitas::where('id', $universitasId)->value('nama') ?? $universitas;
        }
        $universitasImg = $request->universitasImg;
        $course = $request->course;

        $mk = DB::table('mks')
            ->where('kode', $course)
            ->select('kode', 'nama')
            ->first();
        $completeCourseFormat = $mk ? ($mk->kode . ' - ' . $mk->nama) : $course;

        $batchMutus = DB::table('mutus')
            ->where(function($q) use ($prodiId, $universitasId) {
                if ($prodiId) $q->where('id_prodi', $prodiId);
                if ($universitasId) $q->where('universitas_id', $universitasId);
            })
            ->whereNotNull('angkatan')
            ->where('angkatan', '!=', '')
            ->pluck('angkatan');

        $batchMhs = DB::table('mahasiswa')
            ->where(function($q) use ($prodiId) {
                if ($prodiId) $q->where('id_prodi', $prodiId);
            })
            ->whereNotNull('angkatan')
            ->where('angkatan', '!=', '')
            ->pluck('angkatan');

        $combinedAngkatan = $batchMutus->concat($batchMhs)->unique()->filter()->sortDesc()->values();
        if ($combinedAngkatan->isEmpty()) {
            $currYear = (int)date('Y');
            $combinedAngkatan = collect([$currYear, $currYear-1, $currYear-2, $currYear-3, $currYear-4]);
        }

        $allAngkatan = $combinedAngkatan->map(function($a) {
            return (object)['angkatan' => (string)$a];
        });

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
        $hasBobotCol = \Illuminate\Support\Facades\Schema::hasColumn('cpmk_mk', 'bobot');
        $selectCols = ['cpmk_mk.cpmk_id as id', 'cpmks.judul', 'cpmks.kode'];
        if ($hasBobotCol) {
            $selectCols[] = 'cpmk_mk.bobot';
        }

        $cpmkResultAllQuery = DB::table('cpmk_mk')
            ->join('cpmks', 'cpmk_mk.cpmk_id', '=', 'cpmks.id')
            ->join('prodi', 'cpmks.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('mk_kode', $course)->select($selectCols);

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
        if ($minAvg > 100) {
            $minAvg = 0;
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

        // Hitung tabel cpmk angkatan dan metrik eksekutif
        $cpmkTableList = [];
        $totalCpmkCount = count($cpmkResultAll);
        $sumAvg = 0;
        $countAvg = 0;
        $countTercapai = 0;
        $weightedSumAvg = 0;
        $sumBobotCpmk = 0;

        foreach ($cpmkResultAll as $itemCpmk) {
            $cpmkId = $itemCpmk->id;
            $avgVal = isset($cpmkTmp[$cpmkId][0]) ? round((float) $cpmkTmp[$cpmkId][0], 2) : 0;
            $minVal = isset($cpmkTmp[$cpmkId][1]) ? round((float) $cpmkTmp[$cpmkId][1], 2) : 0;
            $maxVal = isset($cpmkTmp[$cpmkId][2]) ? round((float) $cpmkTmp[$cpmkId][2], 2) : 0;
            $bobotVal = (float) ($itemCpmk->bobot ?? 0);

            if ($avgVal >= 65) {
                $status = 'Tercapai';
                $countTercapai++;
            } elseif ($avgVal > 0) {
                $status = 'Perlu Peningkatan';
            } else {
                $status = 'Belum Ada Data';
            }

            if ($avgVal > 0) {
                $sumAvg += $avgVal;
                $countAvg++;
                if ($bobotVal > 0) {
                    $weightedSumAvg += ($avgVal * $bobotVal);
                    $sumBobotCpmk += $bobotVal;
                }
            }

            $cpmkTableList[] = [
                'id' => $cpmkId,
                'kode' => $itemCpmk->kode,
                'judul' => $itemCpmk->judul,
                'bobot' => $bobotVal,
                'avg_angkatan' => $avgVal,
                'min_angkatan' => $minVal,
                'max_angkatan' => $maxVal,
                'status' => $status,
            ];
        }

        if ($sumBobotCpmk > 0) {
            $rataRataAngkatan = round($weightedSumAvg / $sumBobotCpmk, 2);
        } else {
            $rataRataAngkatan = $countAvg > 0 ? round($sumAvg / $countAvg, 2) : 0;
        }

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
            'cpmkTableList' => $cpmkTableList,
            'totalCpmkCount' => $totalCpmkCount,
            'rataRataAngkatan' => $rataRataAngkatan,
            'countTercapai' => $countTercapai,
            'minAvg' => $minAvg,
            'kodeMinAvg' => $kodeMinAvg,
            'maxAvg' => $maxAvg,
            'kodeMaxAvg' => $kodeMaxAvg,
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
        $prodiId = $request->input('prodi');
        $angkatan = $request->input('angkatan');
        $universitas = $request->input('universitas');

        if (!is_numeric($prodiId) && !empty($prodiId)) {
            $resolvedId = Prodi::where('nama', $prodiId)->value('id');
            if ($resolvedId) {
                $prodiId = $resolvedId;
            }
        }
        if (!is_numeric($universitas) && !empty($universitas)) {
            $resolvedUnivId = Universitas::where('nama', $universitas)->value('id');
            if ($resolvedUnivId) {
                $universitas = $resolvedUnivId;
            }
        }

        $coursesFromMks = DB::table('mks')
            ->where('id_prodi', $prodiId)
            ->select('kode as course', 'nama as namaCourse')
            ->get();

        $coursesFromMutus = DB::table('mutus')
            ->leftJoin('mks', 'mutus.Course', '=', 'mks.kode')
            ->where('mutus.id_prodi', $prodiId)
            ->select('mutus.Course as course', DB::raw('COALESCE(mks.nama, mutus.Course) as namaCourse'))
            ->distinct()
            ->get();

        $course = $coursesFromMks->concat($coursesFromMutus)
            ->unique('course')
            ->sortBy('namaCourse')
            ->values();

        $options = '<option value="">Pilih Mata Kuliah</option>';
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

        $allNpm = DB::table('mutus')
            ->select('npm', 'nama_mhs')
            ->where('Course', $course)
            ->where('angkatan', $angkatan)
            ->where('id_prodi', $prodiId)
            ->where('universitas_id', $universitas)
            ->whereNotNull('npm')
            ->where('npm', '!=', '0')
            ->where('npm', '!=', '')
            ->distinct()
            ->orderBy('npm', 'asc')
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

    public function syncEvaluasi(Request $request)
    {
        try {
            $service = new \App\Services\EvaluasiSyncService();
            $service->syncAll();

            return response()->json([
                'success' => true,
                'message' => 'Sinkronisasi seluruh evaluasi OBE (Mahasiswa, Angkatan, Mata Kuliah, Transkrip, dan Dashboard) berhasil disimpan ke database!'
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error syncEvaluasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexProgramStudi(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas ?? '';
        $userUniversitasId = auth()->user()->id_universitasUser ?? null;
        $userFakultasId = auth()->user()->id_fakultasUser ?? null;

        // Tentukan fakultas yang akan dievaluasi
        $fakultasId = $request->input('fakultas_id', $userFakultasId);

        if (!$fakultasId && in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor']) && $userUniversitasId) {
            $firstFak = Fakultas::where('id_universitas', $userUniversitasId)->first();
            $fakultasId = $firstFak ? $firstFak->id : null;
        }

        $allFakultas = [];
        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor']) && $userUniversitasId) {
            $allFakultas = Fakultas::where('id_universitas', $userUniversitasId)->get();
        }

        // Ambil data evaluasi CPL kumulatif fakultas
        $dashboardController = new DashboardController();
        $fakultasCplData = null;
        if ($fakultasId) {
            $fakultasCplData = $dashboardController->getFakultasCplAnalytics($fakultasId, 'all', 'all');
        }

        // Hitung analitik komprehensif (perkembangan per tahun, 4 aspek SN-Dikti, dan soal terendah)
        $extended = $this->getExtendedFakultasAnalytics($fakultasId, $fakultasCplData);

        $fakultas = $fakultasId ? Fakultas::find($fakultasId) : null;
        $universitas = $userUniversitasId ? Universitas::find($userUniversitasId) : ($fakultas ? Universitas::find($fakultas->id_universitas) : null);

        return view('penjamin-mutu.visualisasi.indexVisualisasiProgramStudi', [
            'fakultasCplData' => $fakultasCplData,
            'fakultas' => $fakultas,
            'allFakultas' => $allFakultas,
            'fakultasId' => $fakultasId,
            'universitas' => $universitas,
            'yearlyProgression' => $extended['yearlyProgression'],
            'availableYears' => $extended['availableYears'],
            'yearlyAverages' => $extended['yearlyAverages'],
            'aspekAnalytics' => $extended['aspekAnalytics'],
            'soalTerendah' => $extended['soalTerendah']
        ]);
    }

    public function generatePDFhasilVisualProgramStudi(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas ?? '';
        $userUniversitasId = auth()->user()->id_universitasUser ?? null;
        $userFakultasId = auth()->user()->id_fakultasUser ?? null;

        $fakultasId = $request->input('fakultas_id', $userFakultasId);
        if (!$fakultasId && in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor']) && $userUniversitasId) {
            $firstFak = Fakultas::where('id_universitas', $userUniversitasId)->first();
            $fakultasId = $firstFak ? $firstFak->id : null;
        }

        $dashboardController = new DashboardController();
        $fakultasCplData = $dashboardController->getFakultasCplAnalytics($fakultasId, 'all', 'all');
        $extended = $this->getExtendedFakultasAnalytics($fakultasId, $fakultasCplData);

        $fakultas = $fakultasId ? Fakultas::find($fakultasId) : null;
        $universitas = $userUniversitasId ? Universitas::find($userUniversitasId) : ($fakultas ? Universitas::find($fakultas->id_universitas) : null);

        $selectedProdiId = $request->input('prodi_id');
        $selectedProdi = null;
        if ($selectedProdiId && $fakultasCplData && !empty($fakultasCplData['prodi_stats'])) {
            $selectedProdi = collect($fakultasCplData['prodi_stats'])->firstWhere('id', (int)$selectedProdiId);
        }

        $chartImg = $request->input('chartImg');
        $trendChartImg = $request->input('trendChartImg');
        $radarChartImg = $request->input('radarChartImg');

        $data = [
            'fakultasCplData' => $fakultasCplData,
            'fakultas' => $fakultas,
            'universitas' => $universitas,
            'selectedProdi' => $selectedProdi,
            'yearlyProgression' => $extended['yearlyProgression'],
            'availableYears' => $extended['availableYears'],
            'yearlyAverages' => $extended['yearlyAverages'],
            'aspekAnalytics' => $extended['aspekAnalytics'],
            'soalTerendah' => $extended['soalTerendah'],
            'chartImg' => $chartImg,
            'trendChartImg' => $trendChartImg,
            'radarChartImg' => $radarChartImg,
            'tanggalCetak' => date('d F Y')
        ];

        $pdf = Pdf::loadView('pdf.reportVisualisasiProgramStudi', $data);
        $pdf->setPaper('a4', 'landscape');

        $title = $selectedProdi 
            ? "Laporan Evaluasi CPL - {$selectedProdi['nama']}"
            : "Laporan Evaluasi CPL Fakultas - " . ($fakultas->nama ?? 'Fakultas');

        $safeTitle = preg_replace('~[\\\\/:*?"<>|]~', '-', $title);
        return $pdf->download("{$safeTitle}.pdf");
    }

    private function getExtendedFakultasAnalytics($fakultasId, $fakultasCplData)
    {
        if (!$fakultasCplData || empty($fakultasCplData['prodi_stats'])) {
            return [
                'yearlyProgression' => [],
                'availableYears' => [],
                'yearlyAverages' => [],
                'aspekAnalytics' => [],
                'soalTerendah' => []
            ];
        }

        $prodiIds = collect($fakultasCplData['prodi_stats'])->pluck('id')->toArray();
        $dashboardController = new DashboardController();

        // 1. Available Years (Mulai dari angkatan mahasiswa tertua di prodi fakultas atau tahun ajaran aktif)
        $minAngkatan = DB::table('mahasiswa')
            ->whereIn('id_prodi', $prodiIds)
            ->whereNotNull('angkatan')
            ->where('angkatan', '>=', 2018)
            ->min('angkatan');

        $startYear = $minAngkatan ? (int)$minAngkatan : 2022;

        $mutusYears = DB::table('mutus')
            ->whereIn('id_prodi', $prodiIds)
            ->select(DB::raw("DISTINCT SUBSTRING_INDEX(tahun, ' ', -1) as yr"))
            ->pluck('yr')
            ->filter(function($y) use ($startYear) { return is_numeric($y) && (int)$y >= $startYear; })
            ->map(function($y) { return (string)$y; })
            ->toArray();

        $taYears = DB::table('tahun_ajaran')
            ->select('tahun')
            ->distinct()
            ->pluck('tahun')
            ->filter(function($y) use ($startYear) { return is_numeric($y) && (int)$y >= $startYear && (int)$y <= ((int)date('Y') + 1); })
            ->map(function($y) { return (string)$y; })
            ->toArray();

        $availableYears = array_values(array_unique(array_merge($mutusYears, $taYears)));
        sort($availableYears);

        if (empty($availableYears)) {
            $availableYears = [(string)$startYear, (string)($startYear + 1), (string)($startYear + 2)];
        }

        // 2. Yearly Progression per Prodi (Kumulatif & Monotonik Tetap/Meningkat Seiring Berjalannya Tahun Perkuliahan)
        $yearlyProgression = [];
        $yearlyTotals = array_fill_keys($availableYears, ['sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0]);

        foreach ($fakultasCplData['prodi_stats'] as $prodi) {
            $pId = $prodi['id'];
            $pScores = [];
            $pCapaians = [];
            $runningMaxSkor = 0.0;
            $runningMaxCapaian = 0.0;

            foreach ($availableYears as $yr) {
                $offset = max(0, (int)$yr - $startYear);
                $maxSem = min(8, ($offset + 1) * 2);
                $cumSemesters = range(1, $maxSem);

                $yrData = $dashboardController->getFakultasCplAnalytics($fakultasId, 'all', $cumSemesters);
                $prodiYr = collect($yrData['prodi_stats'])->firstWhere('id', $pId);
                
                $calcSkor = $prodiYr ? (float)$prodiYr['avg_skor_cpl'] : 0.0;
                $calcCapaian = $prodiYr ? (float)$prodiYr['avg_capaian_cpl'] : 0.0;

                // Monotonik akumulatif: Tetap atau mengalami peningkatan
                $runningMaxSkor = max($runningMaxSkor, $calcSkor);
                $runningMaxCapaian = max($runningMaxCapaian, $calcCapaian);

                $pScores[$yr] = $runningMaxSkor;
                $pCapaians[$yr] = $runningMaxCapaian;

                if ($runningMaxSkor > 0) {
                    $yearlyTotals[$yr]['sum_skor'] += $runningMaxSkor;
                    $yearlyTotals[$yr]['count_skor']++;
                }
                if ($runningMaxCapaian > 0) {
                    $yearlyTotals[$yr]['sum_capaian'] += $runningMaxCapaian;
                    $yearlyTotals[$yr]['count_capaian']++;
                }
            }

            $yearlyProgression[] = [
                'id' => $pId,
                'nama' => $prodi['nama'],
                'jenjang' => $prodi['jenjang'],
                'is_aptikom' => $prodi['is_aptikom'],
                'scores' => $pScores,
                'capaians' => $pCapaians,
                'overall_skor' => $prodi['avg_skor_cpl'],
                'overall_capaian' => $prodi['avg_capaian_cpl']
            ];
        }

        $yearlyAverages = [];
        foreach ($availableYears as $yr) {
            $avgSkor = $yearlyTotals[$yr]['count_skor'] > 0 ? round($yearlyTotals[$yr]['sum_skor'] / $yearlyTotals[$yr]['count_skor'], 1) : 0;
            $avgCapaian = $yearlyTotals[$yr]['count_capaian'] > 0 ? round($yearlyTotals[$yr]['sum_capaian'] / $yearlyTotals[$yr]['count_capaian'], 1) : 0;
            $yearlyAverages[$yr] = [
                'avg_skor' => $avgSkor,
                'avg_capaian' => $avgCapaian
            ];
        }

        // 3. 4 SN-Dikti Aspects Breakdown
        $aspekSummary = [
            'Sikap' => ['count' => 0, 'sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0],
            'Pengetahuan' => ['count' => 0, 'sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0],
            'Keterampilan Umum' => ['count' => 0, 'sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0],
            'Keterampilan Khusus' => ['count' => 0, 'sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0],
        ];

        foreach ($fakultasCplData['prodi_stats'] as $p) {
            foreach ($p['cpl_details'] as $c) {
                $asp = $c['aspek'] ?? 'Lainnya';
                if (!isset($aspekSummary[$asp])) {
                    $aspekSummary[$asp] = ['count' => 0, 'sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0];
                }
                $aspekSummary[$asp]['count']++;
                if ($c['avg_skor'] > 0) {
                    $aspekSummary[$asp]['sum_skor'] += $c['avg_skor'];
                    $aspekSummary[$asp]['count_skor']++;
                }
                if ($c['avg_capaian'] > 0) {
                    $aspekSummary[$asp]['sum_capaian'] += $c['avg_capaian'];
                    $aspekSummary[$asp]['count_capaian']++;
                }
            }
        }

        $aspekAnalytics = [];
        foreach ($aspekSummary as $aspName => $d) {
            $avgSkor = $d['count_skor'] > 0 ? round($d['sum_skor'] / $d['count_skor'], 1) : 0;
            $avgCapaian = $d['count_capaian'] > 0 ? round($d['sum_capaian'] / $d['count_capaian'], 1) : 0;
            $aspekAnalytics[$aspName] = [
                'aspek' => $aspName,
                'total_cpl' => $d['count'],
                'avg_skor' => $avgSkor,
                'avg_capaian' => $avgCapaian
            ];
        }

        // 4. Questions with lowest CPL across faculty
        $lowestQuestionsQuery = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
            ->whereIn('mutus.id_prodi', $prodiIds)
            ->whereNotNull('mutus.Course')
            ->select(
                'mutus.Course',
                'mks.nama as namaCourse',
                'mutus.Jenis',
                'mutus.soal',
                'soals.pertanyaan as soalFromId',
                'prodi.nama as namaProdi',
                'mutus.idSoal',
                DB::raw('AVG(mutus.Nilai) as avg_nilai')
            )
            ->groupBy('mutus.Course', 'mks.nama', 'mutus.Jenis', 'mutus.soal', 'soals.pertanyaan', 'prodi.nama', 'mutus.idSoal')
            ->orderBy('avg_nilai', 'asc')
            ->limit(10)
            ->get();

        $soalTerendah = [];
        foreach ($lowestQuestionsQuery as $q) {
            $soalText = !empty($q->soal) ? $q->soal : (!empty($q->soalFromId) ? $q->soalFromId : '-');
            $soalTerendah[] = [
                'namaCourse' => $q->namaCourse ?: $q->Course,
                'Jenis' => $q->Jenis ?: 'Ujian',
                'soal' => $soalText,
                'prodi' => $q->namaProdi,
                'avg_nilai' => round((float)$q->avg_nilai, 1)
            ];
        }

        return [
            'yearlyProgression' => $yearlyProgression,
            'availableYears' => $availableYears,
            'yearlyAverages' => $yearlyAverages,
            'aspekAnalytics' => $aspekAnalytics,
            'soalTerendah' => $soalTerendah
        ];
    }

    public function indexFakultas(Request $request)
    {
        $user = auth()->user();
        $universitasId = $user->id_universitasUser ?? null;
        if (!$universitasId && $user->id_fakultasUser) {
            $fak = Fakultas::find($user->id_fakultasUser);
            $universitasId = $fak ? $fak->id_universitas : null;
        }
        if (!$universitasId && $user->id_prodiUser) {
            $prodi = Prodi::find($user->id_prodiUser);
            if ($prodi) {
                $fak = Fakultas::find($prodi->id_fakultas);
                $universitasId = $fak ? $fak->id_universitas : null;
            }
        }
        if (!$universitasId) {
            $firstUniv = Universitas::first();
            $universitasId = $firstUniv ? $firstUniv->id : null;
        }

        $universitas = $universitasId ? Universitas::find($universitasId) : null;
        $extended = $this->getExtendedUniversitasAnalytics($universitasId);

        return view('penjamin-mutu.visualisasi.indexVisualisasiFakultas', [
            'universitasCplData' => $extended['universitasCplData'],
            'universitas' => $universitas,
            'universitasId' => $universitasId,
            'yearlyProgression' => $extended['yearlyProgression'],
            'availableYears' => $extended['availableYears'],
            'yearlyAverages' => $extended['yearlyAverages'],
        ]);
    }

    public function generatePDFhasilVisualFakultas(Request $request)
    {
        $user = auth()->user();
        $universitasId = $user->id_universitasUser ?? null;
        if (!$universitasId && $user->id_fakultasUser) {
            $fak = Fakultas::find($user->id_fakultasUser);
            $universitasId = $fak ? $fak->id_universitas : null;
        }
        if (!$universitasId && $user->id_prodiUser) {
            $prodi = Prodi::find($user->id_prodiUser);
            if ($prodi) {
                $fak = Fakultas::find($prodi->id_fakultas);
                $universitasId = $fak ? $fak->id_universitas : null;
            }
        }
        if (!$universitasId) {
            $firstUniv = Universitas::first();
            $universitasId = $firstUniv ? $firstUniv->id : null;
        }

        $universitas = $universitasId ? Universitas::find($universitasId) : null;
        $extended = $this->getExtendedUniversitasAnalytics($universitasId);

        $selectedFakultasId = $request->input('fakultas_id');
        $selectedFakultas = null;
        if ($selectedFakultasId && !empty($extended['universitasCplData']['fakultas_stats'])) {
            $selectedFakultas = collect($extended['universitasCplData']['fakultas_stats'])->firstWhere('id', (int)$selectedFakultasId);
        }

        $chartImg = $request->input('chartImg');
        $trendChartImg = $request->input('trendChartImg');

        $data = [
            'universitasCplData' => $extended['universitasCplData'],
            'universitas' => $universitas,
            'selectedFakultas' => $selectedFakultas,
            'yearlyProgression' => $extended['yearlyProgression'],
            'availableYears' => $extended['availableYears'],
            'yearlyAverages' => $extended['yearlyAverages'],
            'chartImg' => $chartImg,
            'trendChartImg' => $trendChartImg,
            'tanggalCetak' => date('d F Y')
        ];

        $pdf = Pdf::loadView('pdf.reportVisualisasiFakultas', $data);
        $pdf->setPaper('a4', 'landscape');

        $title = $selectedFakultas 
            ? "Laporan Evaluasi CPL - {$selectedFakultas['nama']}"
            : "Laporan Evaluasi CPL Universitas - " . ($universitas->nama ?? 'Universitas');

        $safeTitle = preg_replace('~[\\\\/:*?"<>|]~', '-', $title);
        return $pdf->download("{$safeTitle}.pdf");
    }

    private function getExtendedUniversitasAnalytics($universitasId)
    {
        $universitas = $universitasId ? Universitas::find($universitasId) : null;
        if (!$universitas) {
            return [
                'universitasCplData' => null,
                'yearlyProgression' => [],
                'availableYears' => [],
                'yearlyAverages' => []
            ];
        }

        $fakultasList = Fakultas::where('id_universitas', $universitasId)->get();
        $allProdiIds = Prodi::whereIn('id_fakultas', $fakultasList->pluck('id'))->pluck('id')->toArray();
        $dashboardController = new DashboardController();

        // 1. Available Years
        $minAngkatan = DB::table('mahasiswa')
            ->whereIn('id_prodi', $allProdiIds)
            ->whereNotNull('angkatan')
            ->where('angkatan', '>=', 2018)
            ->min('angkatan');

        $startYear = $minAngkatan ? (int)$minAngkatan : 2022;

        $mutusYears = DB::table('mutus')
            ->whereIn('id_prodi', $allProdiIds)
            ->select(DB::raw("DISTINCT SUBSTRING_INDEX(tahun, ' ', -1) as yr"))
            ->pluck('yr')
            ->filter(function($y) use ($startYear) { return is_numeric($y) && (int)$y >= $startYear; })
            ->map(function($y) { return (string)$y; })
            ->toArray();

        $taYears = DB::table('tahun_ajaran')
            ->select('tahun')
            ->distinct()
            ->pluck('tahun')
            ->filter(function($y) use ($startYear) { return is_numeric($y) && (int)$y >= $startYear && (int)$y <= ((int)date('Y') + 1); })
            ->map(function($y) { return (string)$y; })
            ->toArray();

        $availableYears = array_values(array_unique(array_merge($mutusYears, $taYears)));
        sort($availableYears);
        if (empty($availableYears)) {
            $availableYears = [(string)$startYear, (string)($startYear + 1), (string)($startYear + 2)];
        }

        // 2. Fakultas stats & yearly progression
        $allFakultasStats = [];
        $yearlyProgression = [];
        $yearlyTotals = array_fill_keys($availableYears, ['sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0]);

        $totalUnivMhs = 0;
        $totalUnivProdi = 0;
        $totalUnivCpl = 0;
        $sumUnivSkor = 0;
        $countUnivSkor = 0;
        $sumUnivCapaian = 0;
        $countUnivCapaian = 0;

        foreach ($fakultasList as $fak) {
            $fakData = $dashboardController->getFakultasCplAnalytics($fak->id, 'all', 'all');
            $pStats = $fakData['prodi_stats'] ?? [];
            
            $fakSkor = (float)($fakData['summary']['faculty_avg_skor'] ?? 0);
            $fakCapaian = (float)($fakData['summary']['faculty_avg_capaian'] ?? 0);
            $fakMhs = (int)($fakData['summary']['total_mhs_evaluated'] ?? 0);
            $fakCpl = (int)($fakData['summary']['total_cpl_count'] ?? 0);
            $prodiCount = count($pStats);

            $totalUnivMhs += $fakMhs;
            $totalUnivProdi += $prodiCount;
            $totalUnivCpl += $fakCpl;

            if ($fakSkor > 0) {
                $sumUnivSkor += $fakSkor;
                $countUnivSkor++;
            }
            if ($fakCapaian > 0) {
                $sumUnivCapaian += $fakCapaian;
                $countUnivCapaian++;
            }

            // Yearly progression per fakultas (monotonik kumulatif)
            $fScores = [];
            $fCapaians = [];
            $runningMaxSkor = 0.0;
            $runningMaxCapaian = 0.0;

            foreach ($availableYears as $yr) {
                $offset = max(0, (int)$yr - $startYear);
                $maxSem = min(8, ($offset + 1) * 2);
                $cumSemesters = range(1, $maxSem);

                $yrData = $dashboardController->getFakultasCplAnalytics($fak->id, 'all', $cumSemesters);
                $calcSkor = (float)($yrData['summary']['faculty_avg_skor'] ?? 0);
                $calcCapaian = (float)($yrData['summary']['faculty_avg_capaian'] ?? 0);

                $runningMaxSkor = max($runningMaxSkor, $calcSkor);
                $runningMaxCapaian = max($runningMaxCapaian, $calcCapaian);

                $fScores[$yr] = $runningMaxSkor;
                $fCapaians[$yr] = $runningMaxCapaian;

                if ($runningMaxSkor > 0) {
                    $yearlyTotals[$yr]['sum_skor'] += $runningMaxSkor;
                    $yearlyTotals[$yr]['count_skor']++;
                }
                if ($runningMaxCapaian > 0) {
                    $yearlyTotals[$yr]['sum_capaian'] += $runningMaxCapaian;
                    $yearlyTotals[$yr]['count_capaian']++;
                }
            }

            $allFakultasStats[] = [
                'id' => $fak->id,
                'nama' => $fak->nama,
                'total_prodi' => $prodiCount,
                'total_mhs' => $fakMhs,
                'total_cpl' => $fakCpl,
                'avg_skor_cpl' => $fakSkor,
                'avg_capaian_cpl' => $fakCapaian,
                'prodi_stats' => $pStats,
                'fakultas_data' => $fakData
            ];

            $yearlyProgression[] = [
                'id' => $fak->id,
                'nama' => $fak->nama,
                'total_prodi' => $prodiCount,
                'scores' => $fScores,
                'capaians' => $fCapaians,
                'overall_skor' => $fakSkor,
                'overall_capaian' => $fakCapaian
            ];
        }

        $totalFakultasCount = count($fakultasList);
        $univAvgSkor = $totalFakultasCount > 0 ? round($sumUnivSkor / $totalFakultasCount, 1) : 0.0;
        $univAvgCapaian = $totalFakultasCount > 0 ? round($sumUnivCapaian / $totalFakultasCount, 1) : 0.0;

        $yearlyAverages = [];
        foreach ($availableYears as $yr) {
            $avgSkor = $totalFakultasCount > 0 ? round($yearlyTotals[$yr]['sum_skor'] / $totalFakultasCount, 1) : 0;
            $avgCapaian = $totalFakultasCount > 0 ? round($yearlyTotals[$yr]['sum_capaian'] / $totalFakultasCount, 1) : 0;
            $yearlyAverages[$yr] = [
                'avg_skor' => $avgSkor,
                'avg_capaian' => $avgCapaian
            ];
        }

        $universitasSummary = [
            'univ_avg_skor' => $univAvgSkor,
            'univ_avg_capaian' => $univAvgCapaian,
            'total_fakultas_count' => count($fakultasList),
            'total_prodi_count' => $totalUnivProdi,
            'total_mhs_evaluated' => $totalUnivMhs,
            'total_cpl_count' => $totalUnivCpl
        ];

        $universitasCplData = [
            'universitas' => $universitas,
            'fakultas_stats' => $allFakultasStats,
            'summary' => $universitasSummary
        ];

        return [
            'universitasCplData' => $universitasCplData,
            'yearlyProgression' => $yearlyProgression,
            'availableYears' => $availableYears,
            'yearlyAverages' => $yearlyAverages
        ];
    }
}
