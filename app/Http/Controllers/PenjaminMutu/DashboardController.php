<?php

namespace App\Http\Controllers\PenjaminMutu;

use App\Http\Controllers\Controller;
use App\Models\CPL;
use App\Models\CPLMK;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\MK;
use App\Models\Prodi;
use App\Models\RPS;
use App\Models\Soal;
use App\Models\Universitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function list(Request $request)
    {
        $otoritas = auth()->user()->otoritas->otoritas;

        $query = Soal::query()
            ->join('prodi', 'soals.prodiId', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id');

        if (in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor'])) {
            $query->where('fakultas.id_universitas', auth()->user()->id_universitasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan'])) {
            $query->where('fakultas.id', auth()->user()->id_fakultasUser);
        } elseif (in_array($otoritas, ['Penjamin Mutu Program Studi', 'Kepala Program Studi'])) {
            $query->where('prodi.id', auth()->user()->id_prodiUser);
        }
        // Ambil data soal
        $soal = $query->get();

        // Kelompokkan soal berdasarkan status
        $valid = $soal->where('status', 'Valid');
        $tolak = $soal->where('status', 'Tolak');
        $belum = $soal->where('status', 'Belum');

        return view('penjamin-mutu.dashboard', compact(
            'soal', 'valid', 'belum', 'tolak'
        ));
    }

    public function getDashboardPeriods($otoritas = null, $idUniv = null, $idFakultas = null, $idProdi = null)
    {
        $baseAngkatan = 2022;
        $numYears = 4; // Dibatasi 4 tahun akademik (Semester 1 s.d. Semester 8)

        $mutusQuery = DB::table('mutus')
            ->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->whereNotNull('mutus.npm');

        if ($otoritas && in_array($otoritas, ['Penjamin Mutu Universitas', 'Wakil Rektor']) && $idUniv) {
            $mutusQuery->where('fakultas.id_universitas', $idUniv);
        } elseif ($otoritas && in_array($otoritas, ['Penjamin Mutu Fakultas', 'Wakil Dekan']) && $idFakultas) {
            $mutusQuery->where('fakultas.id', $idFakultas);
        } elseif ($idProdi) {
            $mutusQuery->where('prodi.id', $idProdi);
        }

        $existingSemesters = $mutusQuery->distinct()->pluck('mks.semester')->map(fn($v) => (int)$v)->toArray();

        $yearsList = [];
        $allSemestersList = [];

        for ($i = 0; $i < $numYears; $i++) {
            $calYear = $baseAngkatan + $i;
            $nextYear = $calYear + 1;
            $academicYearStr = "{$calYear}/{$nextYear}";

            $sem1Num = $i * 2 + 1;
            $sem2Num = $i * 2 + 2;

            $hasSem1 = in_array($sem1Num, $existingSemesters);
            $hasSem2 = in_array($sem2Num, $existingSemesters);
            $hasYear = $hasSem1 || $hasSem2;

            $semestersInYear = [
                [
                    'semNumber'    => $sem1Num,
                    'jenis'        => 'Ganjil',
                    'tahun'        => (string)$calYear,
                    'academicYear' => $academicYearStr,
                    'label'        => "Semester {$sem1Num} (Ganjil {$academicYearStr})",
                    'hasData'      => $hasSem1,
                ],
                [
                    'semNumber'    => $sem2Num,
                    'jenis'        => 'Genap',
                    'tahun'        => (string)$calYear,
                    'academicYear' => $academicYearStr,
                    'label'        => "Semester {$sem2Num} (Genap {$academicYearStr})",
                    'hasData'      => $hasSem2,
                ]
            ];

            $yearsList[] = [
                'tahun'        => (string)$calYear,
                'academicYear' => $academicYearStr,
                'yearIndex'    => $i + 1,
                'label'        => $academicYearStr,
                'hasData'      => $hasYear,
                'semesters'    => $semestersInYear
            ];

            $allSemestersList[] = $semestersInYear[0];
            $allSemestersList[] = $semestersInYear[1];
        }

        return [
            'years'        => $yearsList,
            'allSemesters' => $allSemestersList
        ];
    }

    private function getYearSemSqlExpr($tableAlias = 'mutus', $taAlias = 'ta')
    {
        return "CONCAT(
            COALESCE(CONVERT($taAlias.tahun USING utf8mb4) COLLATE utf8mb4_unicode_ci, CONVERT(SUBSTRING_INDEX($tableAlias.tahun, ' ', -1) USING utf8mb4) COLLATE utf8mb4_unicode_ci),
            CASE WHEN COALESCE(CONVERT($taAlias.jenis_semester USING utf8mb4) COLLATE utf8mb4_unicode_ci, CONVERT(SUBSTRING_INDEX($tableAlias.tahun, ' ', 1) USING utf8mb4) COLLATE utf8mb4_unicode_ci) = 'Genap' THEN '1' ELSE '0' END
        )";
    }

    public function getFakultasCplAnalytics($fakultasId, $tahun = 'all', $semester = 'all')
    {
        $fakultas = Fakultas::find($fakultasId);
        if (!$fakultas) {
            return null;
        }

        $baseAngkatan = 2022;
        $semestersToFilter = [];

        if ($semester && $semester !== 'all' && is_numeric($semester)) {
            $semestersToFilter = [(int)$semester];
        } elseif ($tahun && $tahun !== 'all' && $tahun !== '') {
            $parsedT = (int)explode('/', $tahun)[0];
            $yearOffset = max(0, $parsedT - $baseAngkatan);
            $sem1 = $yearOffset * 2 + 1;
            $sem2 = $yearOffset * 2 + 2;
            $semestersToFilter = [$sem1, $sem2];
        }

        $prodis = Prodi::where('id_fakultas', $fakultasId)->get();
        $prodiStats = [];
        $totalSkorFakultas = 0;
        $totalCapaianFakultas = 0;
        $activeProdiCount = 0;
        $allUniqueNpmsInFaculty = [];
        $totalCplCount = 0;

        $yearSemExprMutus = $this->getYearSemSqlExpr('mutus', 'ta');
        $yearSemExprM = $this->getYearSemSqlExpr('m', 'ta');
        $yearExprQ1 = $this->getYearSemSqlExpr('m', 'ta');
        $yearExprQ2 = $this->getYearSemSqlExpr('m2', 'ta2');

        foreach ($prodis as $prodi) {
            $cpls = CPL::where('id_prodi', $prodi->id)->get();
            $totalCplCount += $cpls->count();

            // Ambil SEMUA mahasiswa terdaftar di program studi (mahasiswa + mutus)
            $mhsNpms = DB::table('mahasiswa')
                ->where('id_prodi', $prodi->id)
                ->whereNotNull('NPM')
                ->pluck('NPM')
                ->toArray();

            $mutuNpms = DB::table('mutus')
                ->where('id_prodi', $prodi->id)
                ->whereNotNull('npm')
                ->distinct()
                ->pluck('npm')
                ->toArray();

            $allNpm = array_values(array_unique(array_merge($mhsNpms, $mutuNpms)));
            $totalMhs = count($allNpm);

            foreach ($allNpm as $npmVal) {
                $allUniqueNpmsInFaculty[$npmVal] = true;
            }

            if ($totalMhs === 0 || $cpls->isEmpty()) {
                $emptyCpls = [];
                foreach ($cpls as $cpl) {
                    $emptyCpls[] = [
                        'id' => $cpl->id,
                        'kode' => $cpl->kode ?? "CPL-{$cpl->id}",
                        'aspek' => $cpl->aspek ?? 'Umum',
                        'judul' => $cpl->judul ?? $cpl->deskripsi ?? '-',
                        'deskripsi' => $cpl->deskripsi ?? $cpl->judul ?? '-',
                        'avg_skor' => 0.0,
                        'avg_capaian' => 0.0,
                        'status' => 'Belum Ada Nilai',
                        'status_class' => 'secondary'
                    ];
                }

                $prodiStats[] = [
                    'id' => $prodi->id,
                    'nama' => $prodi->nama,
                    'jenjang' => $prodi->jenjang ?? '-',
                    'is_aptikom' => (bool) $prodi->is_aptikom,
                    'total_mhs' => $totalMhs,
                    'total_cpl' => $cpls->count(),
                    'avg_skor_cpl' => 0.0,
                    'avg_capaian_cpl' => 0.0,
                    'status' => 'Belum Ada Data',
                    'status_class' => 'secondary',
                    'status_badge_bg' => '#64748b',
                    'cpl_details' => $emptyCpls
                ];
                continue;
            }

            // 1. Capaian CPL (% ketercapaian MK pendukung CPL)
            $allCplCapaianTotal = [];
            foreach ($cpls as $cpl) {
                $allCplCapaianTotal[$cpl->id] = 0;
            }

            foreach ($allNpm as $npm) {
                $subQuery = DB::table('mutus')
                    ->join('mks', 'mutus.Course', '=', 'mks.kode')
                    ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
                    ->select('mutus.Course', DB::raw("MAX({$yearSemExprMutus}) AS max_year_semester"))
                    ->where('mutus.NPM', $npm);

                if (!empty($semestersToFilter)) {
                    $subQuery->whereIn('mks.semester', $semestersToFilter);
                }
                $subQuery->groupBy('mutus.Course');

                $subQueryMutus = DB::table('mutus as m')
                    ->join('mks', 'm.Course', '=', 'mks.kode')
                    ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
                    ->joinSub($subQuery, 't', function ($join) use ($yearSemExprM) {
                        $join->on('m.Course', '=', 't.Course')
                            ->on(DB::raw($yearSemExprM), '=', 't.max_year_semester');
                    })
                    ->where('m.NPM', $npm);

                if (!empty($semestersToFilter)) {
                    $subQueryMutus->whereIn('mks.semester', $semestersToFilter);
                }

                $subQueryMutus->groupBy('m.npm', 'm.Course', 'm.jenis')
                    ->select('m.npm', 'm.Course', 'm.jenis', 'm.id');

                $nilaiMk = DB::table('mutus')
                    ->joinSub($subQueryMutus, 'sub', fn($j) => $j->on('mutus.id', '=', 'sub.id'))
                    ->select('mutus.Course', DB::raw('ROUND(SUM(mutus.examWeight / 100 * mutus.Nilai), 2) as total_nilai'))
                    ->groupBy('mutus.Course')
                    ->get();

                $nilaiMkLulus = [];
                foreach ($nilaiMk as $nilai) {
                    if ($nilai->total_nilai >= 50) {
                        $nilaiMkLulus[$nilai->Course] = $nilai->total_nilai;
                    }
                }

                $cplNilaiMkQuery = DB::table('mutus')
                    ->select('Course', 'Cpl')
                    ->where('npm', $npm)
                    ->whereIn('Course', array_keys($nilaiMkLulus))
                    ->groupBy('Course', 'Cpl')
                    ->get();

                $cplUnique = $cplNilaiMkQuery->pluck('Cpl')->unique()->toArray();

                $cplResults = DB::table('mk_cpl')
                    ->whereIn('mk_cpl.cpl_id', $cplUnique)
                    ->where('mk_cpl.id_prodi', $prodi->id)
                    ->get()
                    ->groupBy('cpl_id');

                foreach ($cplResults as $id_cpl => $items) {
                    $kodeMkList = $items->pluck('mk_kode')->toArray();
                    $countKesamaan = count(array_intersect($kodeMkList, array_keys($nilaiMkLulus)));
                    $persentase = count($items) > 0 ? (($countKesamaan / count($items)) * 100) : 0;

                    if (isset($allCplCapaianTotal[$id_cpl])) {
                        $allCplCapaianTotal[$id_cpl] += $persentase;
                    }
                }
            }

            // 2. Skor CPL (Asesmen Berbobot)
            $cplScores = [];
            $cplConditionSub = "";
            $cplConditionMain = "";
            $baseBindings = [
                'id_prodi' => $prodi->id
            ];

            if (!empty($semestersToFilter)) {
                $semListStr = implode(',', array_map('intval', $semestersToFilter));
                $cplConditionSub .= " AND m2.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";
                $cplConditionMain .= " AND m.Course IN (SELECT kode FROM mks WHERE semester IN ({$semListStr})) ";
            }

            foreach ($allNpm as $npm) {
                $bindings = array_merge($baseBindings, [
                    'npm1' => $npm,
                    'npm2' => $npm,
                    'npm3' => $npm,
                    'npm4' => $npm,
                ]);

                $sql = "
                    SELECT q5.cpl, cpls.kode as kode_cpl, AVG(q5.hasil) as avg_score
                    FROM (
                        SELECT q4.Cpl, q4.Course, SUM(q4.hasil) as hasil 
                        FROM (
                            SELECT q1.tahun, q1.Cpl, q1.Jenis, q1.Course, 
                                (q1.nilaiSoal * q1.BobotSoal / NULLIF(q2.BobotJenis, 0)) * q1.examWeight / NULLIF(q3.sumExamWeight, 0) as hasil 
                            FROM (
                                SELECT m.tahun, m.Cpl, m.Jenis, m.Course, m.nilaiSoal, m.examWeight, m.BobotSoal 
                                FROM mutus m 
                                LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
                                JOIN (
                                    SELECT m2.Course, MAX({$yearExprQ2}) AS max_year_semester 
                                    FROM mutus m2
                                    LEFT JOIN tahun_ajaran ta2 ON m2.tahun_ajaran_id = ta2.id
                                    WHERE m2.NPM = :npm1 {$cplConditionSub}
                                    GROUP BY m2.Course 
                                ) t 
                                ON m.Course = t.Course AND {$yearExprQ1} = t.max_year_semester 
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
                        GROUP BY q4.Cpl, q4.Course, q4.tahun
                    ) q5
                    JOIN cpls ON q5.cpl = cpls.id
                    WHERE cpls.id_prodi = :id_prodi
                    GROUP BY q5.cpl, cpls.kode
                ";

                $rows = DB::select($sql, $bindings);
                foreach ($rows as $row) {
                    if (!isset($cplScores[$row->cpl])) {
                        $cplScores[$row->cpl] = [
                            'kode' => $row->kode_cpl,
                            'total_score' => 0,
                            'count' => 0
                        ];
                    }
                    $cplScores[$row->cpl]['total_score'] += (float) $row->avg_score;
                    $cplScores[$row->cpl]['count']++;
                }
            }

            $cplDetails = [];
            $totalSkorProdi = 0;
            $totalCapaianProdi = 0;
            $cplWithScoreCount = 0;

            foreach ($cpls as $cpl) {
                $avgSkor = 0.0;
                if (isset($cplScores[$cpl->id]) && $cplScores[$cpl->id]['count'] > 0) {
                    $avgSkor = round($cplScores[$cpl->id]['total_score'] / $totalMhs, 1);
                }

                $avgCapaian = 0.0;
                if (isset($allCplCapaianTotal[$cpl->id])) {
                    $avgCapaian = round($allCplCapaianTotal[$cpl->id] / $totalMhs, 1);
                }

                if ($avgSkor >= 65.0) {
                    $statusCpl = 'Standar Kelulusan Tercapai';
                    $statusCplClass = 'success';
                } elseif ($avgSkor > 0) {
                    $statusCpl = 'Perlu Peningkatan';
                    $statusCplClass = 'warning';
                } else {
                    $statusCpl = 'Belum Ada Nilai';
                    $statusCplClass = 'secondary';
                }

                if ($avgSkor > 0 || $avgCapaian > 0) {
                    $cplWithScoreCount++;
                }

                $totalSkorProdi += $avgSkor;
                $totalCapaianProdi += $avgCapaian;

                $cplDetails[] = [
                    'id' => $cpl->id,
                    'kode' => $cpl->kode,
                    'aspek' => $cpl->aspek ?? '-',
                    'judul' => $cpl->judul ?? ($cpl->deskripsi ?? '-'),
                    'deskripsi' => $cpl->judul ?? ($cpl->deskripsi ?? '-'),
                    'avg_skor' => $avgSkor,
                    'avg_capaian' => $avgCapaian,
                    'status' => $statusCpl,
                    'status_class' => $statusCplClass
                ];
            }

            $avgSkorProdi = $cpls->count() > 0 ? round($totalSkorProdi / $cpls->count(), 1) : 0.0;
            $avgCapaianProdi = $cpls->count() > 0 ? round($totalCapaianProdi / $cpls->count(), 1) : 0.0;

            if ($avgSkorProdi >= 65.0) {
                $prodiStatus = 'Standar Kelulusan CPL';
                $prodiStatusClass = 'success';
                $prodiBadgeBg = '#10b981';
            } elseif ($avgSkorProdi > 0) {
                $prodiStatus = 'Perlu Peningkatan';
                $prodiStatusClass = 'warning';
                $prodiBadgeBg = '#f59e0b';
            } else {
                $prodiStatus = 'Belum Ada Data';
                $prodiStatusClass = 'secondary';
                $prodiBadgeBg = '#64748b';
            }

            $totalSkorFakultas += $avgSkorProdi;
            $totalCapaianFakultas += $avgCapaianProdi;

            if ($avgSkorProdi > 0 || $avgCapaianProdi > 0) {
                $activeProdiCount++;
            }

            $prodiStats[] = [
                'id' => $prodi->id,
                'nama' => $prodi->nama,
                'jenjang' => $prodi->jenjang ?? '-',
                'is_aptikom' => (bool) $prodi->is_aptikom,
                'total_mhs' => $totalMhs,
                'total_cpl' => $cpls->count(),
                'avg_skor_cpl' => $avgSkorProdi,
                'avg_capaian_cpl' => $avgCapaianProdi,
                'status' => $prodiStatus,
                'status_class' => $prodiStatusClass,
                'status_badge_bg' => $prodiBadgeBg,
                'cpl_details' => $cplDetails
            ];
        }

        $totalProdiInFaculty = count($prodiStats);
        $facultyAvgSkor = $totalProdiInFaculty > 0 ? round($totalSkorFakultas / $totalProdiInFaculty, 1) : 0.0;
        $facultyAvgCapaian = $totalProdiInFaculty > 0 ? round($totalCapaianFakultas / $totalProdiInFaculty, 1) : 0.0;

        if ($facultyAvgSkor >= 65.0) {
            $facultyStatus = 'Standar Kelulusan CPL';
            $facultyStatusClass = 'success';
            $facultyBadgeBg = '#10b981';
        } elseif ($facultyAvgSkor > 0) {
            $facultyStatus = 'Perlu Peningkatan';
            $facultyStatusClass = 'warning';
            $facultyBadgeBg = '#f59e0b';
        } else {
            $facultyStatus = 'Belum Ada Data';
            $facultyStatusClass = 'secondary';
            $facultyBadgeBg = '#64748b';
        }

        $sortedProdis = collect($prodiStats)->filter(fn($p) => $p['avg_skor_cpl'] > 0)->sortByDesc('avg_skor_cpl');
        $highestProdi = $sortedProdis->first() ?? null;
        $lowestProdi = $sortedProdis->last() ?? null;

        $chartLabels = array_map(fn($p) => $p['nama'], $prodiStats);
        $chartSkorData = array_map(fn($p) => $p['avg_skor_cpl'], $prodiStats);
        $chartCapaianData = array_map(fn($p) => $p['avg_capaian_cpl'], $prodiStats);

        return [
            'fakultas' => $fakultas,
            'prodi_stats' => $prodiStats,
            'summary' => [
                'total_prodi' => $totalProdiInFaculty,
                'active_prodi_count' => $totalProdiInFaculty,
                'active_with_data_count' => $activeProdiCount,
                'faculty_avg_skor' => $facultyAvgSkor,
                'faculty_avg_capaian' => $facultyAvgCapaian,
                'faculty_status' => $facultyStatus,
                'faculty_status_class' => $facultyStatusClass,
                'faculty_badge_bg' => $facultyBadgeBg,
                'total_mhs_evaluated' => count($allUniqueNpmsInFaculty),
                'total_cpl_count' => $totalCplCount,
                'highest_prodi' => $highestProdi,
                'lowest_prodi' => $lowestProdi,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'skor_data' => $chartSkorData,
                'capaian_data' => $chartCapaianData,
            ]
        ];
    }

    public function getUniversitasCplAnalytics($universitasId, $tahun = 'all', $semester = 'all')
    {
        $universitas = Universitas::find($universitasId);
        if (!$universitas) {
            return null;
        }

        $baseAngkatan = 2022;
        $semestersToFilter = [];

        if ($semester && $semester !== 'all' && is_numeric($semester)) {
            $semestersToFilter = [(int)$semester];
        } elseif ($tahun && $tahun !== 'all' && $tahun !== '') {
            $parsedT = (int)explode('/', $tahun)[0];
            $yearOffset = max(0, $parsedT - $baseAngkatan);
            $sem1 = $yearOffset * 2 + 1;
            $sem2 = $yearOffset * 2 + 2;
            $semestersToFilter = [$sem1, $sem2];
        }

        $faculties = Fakultas::where('id_universitas', $universitasId)->get();
        $facultyStats = [];
        $totalSkorUniv = 0;
        $totalCapaianUniv = 0;
        $activeFacultyCount = 0;
        $totalCplCountUniv = 0;
        $totalProdiCountUniv = 0;

        foreach ($faculties as $f) {
            $fData = $this->getFakultasCplAnalytics($f->id, $tahun, $semester);
            if (!$fData) continue;

            $totalProdiCountUniv += $fData['summary']['total_prodi'];
            $totalCplCountUniv += $fData['summary']['total_cpl_count'];

            $totalSkorUniv += $fData['summary']['faculty_avg_skor'];
            $totalCapaianUniv += $fData['summary']['faculty_avg_capaian'];

            if ($fData['summary']['faculty_avg_skor'] > 0 || $fData['summary']['faculty_avg_capaian'] > 0) {
                $activeFacultyCount++;
            }

            $facultyStats[] = [
                'id' => $f->id,
                'nama' => $f->nama,
                'total_prodi' => $fData['summary']['total_prodi'],
                'active_prodi_count' => $fData['summary']['active_prodi_count'],
                'total_mhs' => $fData['summary']['total_mhs_evaluated'],
                'total_cpl' => $fData['summary']['total_cpl_count'],
                'avg_skor_cpl' => $fData['summary']['faculty_avg_skor'],
                'avg_capaian_cpl' => $fData['summary']['faculty_avg_capaian'],
                'prodi_stats' => $fData['prodi_stats'],
            ];
        }

        // Query total unique NPMs in university across all faculties and prodis
        $mhsUnivNpms = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('fakultas.id_universitas', $universitasId)
            ->whereNotNull('mahasiswa.NPM')
            ->pluck('mahasiswa.NPM')
            ->toArray();

        $mutusUnivNpms = DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
            ->where('fakultas.id_universitas', $universitasId)
            ->whereNotNull('mutus.npm')
            ->distinct()
            ->pluck('mutus.npm')
            ->toArray();

        $totalMhsUniv = count(array_unique(array_merge($mhsUnivNpms, $mutusUnivNpms)));

        $totalFacultyInUniv = count($faculties);
        $univAvgSkor = $totalFacultyInUniv > 0 ? round($totalSkorUniv / $totalFacultyInUniv, 1) : 0.0;
        $univAvgCapaian = $totalFacultyInUniv > 0 ? round($totalCapaianUniv / $totalFacultyInUniv, 1) : 0.0;

        if ($univAvgSkor >= 65.0) {
            $univStatus = 'Standar Kelulusan CPL';
            $univStatusClass = 'success';
            $univBadgeBg = '#10b981';
        } elseif ($univAvgSkor > 0) {
            $univStatus = 'Perlu Peningkatan';
            $univStatusClass = 'warning';
            $univBadgeBg = '#f59e0b';
        } else {
            $univStatus = 'Belum Ada Data';
            $univStatusClass = 'secondary';
            $univBadgeBg = '#64748b';
        }

        $chartLabels = array_map(fn($f) => $f['nama'], $facultyStats);
        $chartSkorData = array_map(fn($f) => $f['avg_skor_cpl'], $facultyStats);
        $chartCapaianData = array_map(fn($f) => $f['avg_capaian_cpl'], $facultyStats);

        return [
            'universitas' => $universitas,
            'faculty_stats' => $facultyStats,
            'summary' => [
                'total_fakultas' => $totalFacultyInUniv,
                'active_fakultas_count' => $totalFacultyInUniv,
                'active_with_data_count' => $activeFacultyCount,
                'total_prodi_count' => $totalProdiCountUniv,
                'total_mhs_evaluated' => $totalMhsUniv,
                'total_cpl_count' => $totalCplCountUniv,
                'univ_avg_skor' => $univAvgSkor,
                'univ_avg_capaian' => $univAvgCapaian,
                'univ_status' => $univStatus,
                'univ_status_class' => $univStatusClass,
                'univ_badge_bg' => $univBadgeBg,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'skor_data' => $chartSkorData,
                'capaian_data' => $chartCapaianData,
            ]
        ];
    }

    public function chart()
    {
        $rpss = RPS::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $mks = collect();
        $cpp = null;
        $warnap = null;
        $borderp = null;
        $cpk = null;
        $warnak = null;
        $borderk = null;
        $pengetahuan = null;
        $keterampilan = null;
        foreach ($rpss as $rps) {
            $id_mk = $rps->mk->kode;
            $mk = MK::firstWhere('kode',$id_mk);
            $mks->push($mk);
        }
        $cplmks = collect();
        foreach ($mks as $mk) {
            $kode_mk = $mk->kode;
            $temp = CPLMK::where('kode_mk', $kode_mk)->get();
            foreach ($temp as $cplmk) {
                $cplmks->push($cplmk);
            }
        }
        $cpls = CPL::where('id_prodi', auth()->user()->id_prodiUser)->get();
        $i = -1;
        foreach ($cpls as $cpl) {
            if ($cpl->aspek == 'Pengetahuan') {
                $pengetahuan[++$i] = $cpl->kode;
            }
        }
        $i = -1;
        foreach ($cpls as $cpl) {
            if ($cpl->aspek == 'Keterampilan') {
                $keterampilan[++$i] = $cpl->kode;
            }
        }

        $i = -1;
        foreach ($cpls as $cpl) {
            if ($cpl->aspek == 'Pengetahuan') {
                $cpl_pengetahuan[++$i] = $cpl->id;
            }
        }
        $i = -1;
        foreach ($cpls as $cpl) {
            if ($cpl->aspek == 'Keterampilan') {
                $cpl_keterampilan[++$i] = $cpl->id;
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

        $list_border = [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
        ];

        if(isset($cpl_pengetahuan)){
            $sump = 0;
            foreach ($cpl_pengetahuan as $no => $cpls) {
                $cpp[$no] = 0;
                $sump += 1;
            }
            foreach ($cpl_pengetahuan as $no => $cpls) {
                foreach ($cplmks as $cplmk) {
                    if ($cplmk->id_cpl == $cpls) {
                        $cpp[$no] += 1;
                    }
                }
            }
            $tmp = -1;
            for ($i = 0; $i < $sump; $i++) {
                $warnap[$i] = $list_warna[++$tmp];
                if ($tmp == 5) {
                    $tmp = 0;
                }
            }

            $tmp = -1;
            for ($i = 0; $i < $sump; $i++) {
                $borderp[$i] = $list_border[++$tmp];
                if ($tmp == 5) {
                    $tmp = 0;
                }
            }
        }
        if(isset($cpl_keterampilan)){
            $sumk = 0;
            foreach ($cpl_keterampilan as $no => $cpls) {
                $cpk[$no] = 0;
                $sumk += 1;
            }
            foreach ($cpl_keterampilan as $no => $cpls) {
                foreach ($cplmks as $cplmk) {
                    if ($cplmk->id_cpl == $cpls) {
                        $cpk[$no] += 1;
                    }
                }
            }

            $tmp = -1;
            for ($i = 0; $i < $sumk; $i++) {
                $warnak[$i] = $list_warna[++$tmp];
                if ($tmp == 5) {
                    $tmp = 0;
                }
            }
            $tmp = -1;
            for ($i = 0; $i < $sumk; $i++) {
                $borderk[$i] = $list_border[++$tmp];
                if ($tmp == 5) {
                    $tmp = 0;
                }
            }
        }

        $dt = ([
            'pengetahuan' => $pengetahuan,
            'keterampilan' => $keterampilan,
            'jumlahp' => $cpp,
            'warnap' => $warnap,
            'borderp' => $borderp,
            'jumlahk' => $cpk,
            'warnak' => $warnak,
            'borderk' => $borderk,
        ]);

        return response()->json($dt);
    }
}
