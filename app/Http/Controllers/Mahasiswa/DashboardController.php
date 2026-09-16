<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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
            ->map(function ($v) {
                return (int)$v;
            })
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
                'semNumber'    => $s,
                'jenis'        => $jenis,
                'tahun'        => (string)$semYear,
                'academicYear' => (string)$semYear,
                'label'        => "Semester {$s} ({$jenis} {$semYear})",
                'hasData'      => $hasSem
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
                'tahun'        => (string)$calYear,
                'academicYear' => (string)$calYear,
                'yearIndex'    => $i + 1,
                'label'        => (string)$calYear,
                'hasData'      => $hasYear,
                'semesters'    => $semestersInYear
            ];
        }

        return [
            'years'        => $yearsList,
            'allSemesters' => $allSemestersList
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $mahasiswa = Mahasiswa::with('prodi')
            ->where('Nama', $user->name)
            ->where('id_prodi', $user->id_prodiUser)
            ->first();

        if (!$mahasiswa) {
            $emptyAcademicBreakdown = [
                'semesters' => [],
                'summary' => [
                    'total_sks' => 0,
                    'total_sks_lulus' => 0,
                    'ipk_akhir' => 0.0,
                    'ips_terakhir' => 0.0,
                    'semester_terakhir' => '-',
                    'periode_terakhir' => '-'
                ]
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'            => false,
                    'has_data'           => false,
                    'academicBreakdown'  => $emptyAcademicBreakdown,
                    'sksLulus'           => 0,
                    'ipk'                => '0.00',
                    'ips'                => '0.00',
                    'avgSkorCpmk'        => 0,
                    'avgSkorCpl'         => 0,
                    'avgKetercapaianCpl' => 0,
                    'topCpmks'           => [],
                    'topProfesi'         => [],
                ]);
            }

            return view('mahasiswa.dashboard', [
                'mahasiswa'          => null,
                'academicBreakdown'  => $emptyAcademicBreakdown,
                'sksLulus'           => 0,
                'ipk'                => 0,
                'ips'                => 0,
                'ipsSubtext'         => 'Belum Ada Data',
                'avgSkorCpmk'        => 0,
                'avgCpmk'            => 0,
                'avgSkorCpl'         => 0,
                'avgCpl'             => 0,
                'avgKetercapaianCpl' => 0,
                'cplBreakdown'       => [],
                'topCpmks'           => [],
                'topProfesi'         => [],
                'hasData'            => false,
            ]);
        }

        $academicBreakdown = $mahasiswa->getAcademicBreakdown();
        $competencyData = $mahasiswa->getCompetencyData();
        $allCpmks = collect($competencyData['cpmks'] ?? []);
        $allCpls  = collect($competencyData['cpls'] ?? []);

        $ketercapaianCplData = $mahasiswa->calculateKetercapaianCpl();
        $avgKetercapaianCpl  = $ketercapaianCplData['avg'] ?? 0.0;

        $sksLulus = $academicBreakdown['summary']['total_sks_lulus'] ?? $mahasiswa->calculateSksLulus();
        $ipk = $academicBreakdown['summary']['ipk_akhir'] ?? $mahasiswa->calculateIPK();
        $ips = $academicBreakdown['summary']['ips_terakhir'] ?? 0.0;
        
        $ipsSubtext = $academicBreakdown['summary']['periode_terakhir'] !== '-' 
            ? $academicBreakdown['summary']['periode_terakhir'] 
            : ($academicBreakdown['summary']['semester_terakhir'] !== '-' ? $academicBreakdown['summary']['semester_terakhir'] : 'Semester Terakhir');

        $avgSkorCpmk = round($allCpmks->avg('nilai') ?? 0, 1);
        $avgSkorCpl  = round($allCpls->avg('nilai') ?? 0, 1);
        $topCpmks = $allCpmks->sortByDesc('nilai')->take(5)->values();
        $topProfesi = $mahasiswa->getTopProfesi($allCpmks);

        $hasData = ($sksLulus > 0 || $ipk > 0 || $ips > 0 || $allCpmks->isNotEmpty() || $allCpls->isNotEmpty() || $avgKetercapaianCpl > 0);


        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'            => true,
                'has_data'           => $hasData,
                'academicBreakdown'  => $academicBreakdown,
                'sksLulus'           => $sksLulus,
                'ipk'                => number_format($ipk, 2),
                'ips'                => number_format($ips, 2),
                'ipsSubtext'         => $ipsSubtext,
                'avgSkorCpmk'        => $avgSkorCpmk,
                'avgCpmk'            => $avgSkorCpmk,
                'avgSkorCpl'         => $avgSkorCpl,
                'avgCpl'             => $avgSkorCpl,
                'avgKetercapaianCpl' => $avgKetercapaianCpl,
                'cplBreakdown'       => $ketercapaianCplData['items'] ?? [],
                'topCpmks'           => $topCpmks,
                'topProfesi'         => $topProfesi,
            ]);
        }

        return view('mahasiswa.dashboard', [
            'mahasiswa'          => $mahasiswa,
            'academicBreakdown'  => $academicBreakdown,
            'sksLulus'           => $sksLulus,
            'ipk'                => $ipk,
            'ips'                => $ips,
            'ipsSubtext'         => $ipsSubtext,
            'avgSkorCpmk'        => $avgSkorCpmk,
            'avgCpmk'            => $avgSkorCpmk,
            'avgSkorCpl'         => $avgSkorCpl,
            'avgCpl'             => $avgSkorCpl,
            'avgKetercapaianCpl' => $avgKetercapaianCpl,
            'cplBreakdown'       => $ketercapaianCplData['items'] ?? [],
            'topCpmks'           => $topCpmks,
            'topProfesi'         => $topProfesi,
            'hasData'            => $hasData,
        ]);
    }
}

