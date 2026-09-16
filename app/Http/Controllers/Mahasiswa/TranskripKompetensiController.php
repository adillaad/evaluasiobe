<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class TranskripKompetensiController extends Controller
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

    private function getAvailablePeriodsForStudent(string $npm, ?int $angkatan = null, $prodiParam = null): array
    {
        $baseAngkatan = $angkatan ? (int)$angkatan : (int)('20' . substr($npm, 0, 2));

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
            ->whereNotNull('mks.semester')
            ->select('mks.semester')
            ->distinct()
            ->pluck('mks.semester')
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

    private function parsePeriodFilters(Request $request, string $npm, ?int $angkatan = null): array
    {
        $rawTahun = $request->input('tahun', 'all');
        $rawSemester = $request->input('semester', 'all');

        $semestersToFilter = null;
        $activePeriodLabel = 'Kumulatif (Semua Semester)';
        $isSingleSemester = false;
        $baseAngkatan = $angkatan ? (int)$angkatan : (int)('20' . substr($npm, 0, 2));

        if ($rawSemester && $rawSemester !== 'all') {
            $isSingleSemester = true;
            $semNumber = (int)$rawSemester;
            $semestersToFilter = [$semNumber];

            $yearOffset = (int)floor(($semNumber - 1) / 2);
            $calYear = $baseAngkatan + $yearOffset;
            $nextYear = $calYear + 1;
            $semType = ($semNumber % 2 === 1) ? 'Ganjil' : 'Genap';
            $activePeriodLabel = "Semester {$semNumber} ({$semType} {$calYear}/{$nextYear})";
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

        return [
            'rawTahun'          => $rawTahun,
            'rawSemester'       => $rawSemester,
            'semestersToFilter' => $semestersToFilter,
            'activePeriodLabel' => $activePeriodLabel,
            'isSingleSemester'  => $isSingleSemester,
        ];
    }

    public function index(Request $request)
    {
        $user       = Auth::user();
        $mahasiswa  = $this->guardMahasiswa($user); //validasi user mahasiswa aktif

        [$mahasiswaData, $prodi, $universitas] = $this->loadMahasiswaContext($mahasiswa->NPM);
        
        $availablePeriods = $this->getAvailablePeriodsForStudent($mahasiswa->NPM, $mahasiswaData->angkatan ?? null, $prodi);
        $rawTahun = $request->input('tahun', 'all');
        $rawSemester = $request->input('semester', 'all');

        //kemudian menyimpan di session untuk request berikutnya
        session([
            'current_npm'        => $mahasiswaData->npm,
            'current_is_aptikom' => (bool) ($prodi->is_aptikom ?? false),
            'current_jenjang'    => $prodi->jenjang ?? null,
        ]);

        return view('mahasiswa.transkrip-kompetensi', compact('mahasiswaData', 'prodi', 'universitas', 'availablePeriods', 'rawTahun', 'rawSemester'));
    }

    public function getCompetencyData(Request $request)
    {
        try {
            //validasi npm
            $npm = $request->input('npm') ?? session('current_npm');

            if (!$npm) {
                return $this->jsonError('NPM tidak ditemukan. Silakan muat ulang halaman.');
            }

            if (!preg_match('/^[0-9]+$/', $npm)) {
                return $this->jsonError('Format NPM tidak valid.');
            }

            $prodiInfo = $this->getProdiInfo($npm);
            if (!$prodiInfo) {
                return $this->jsonError('Data program studi tidak ditemukan.');
            }
            //cek prodi
            $prodi = Prodi::find($prodiInfo->prodi_id);

            $mahasiswa = Mahasiswa::where('NPM', $npm)->firstOrFail();
            $mahasiswaData = DB::table('mutus')->where('npm', $npm)->select('angkatan')->first();
            $filterInfo = $this->parsePeriodFilters($request, $npm, $mahasiswaData->angkatan ?? null);

            $rawTahun = $filterInfo['rawTahun'];
            $rawSemester = $filterInfo['rawSemester'];

            // Force clear cache key to ensure fresh calculation on every request
            $cacheKey  = "transkrip_data_{$npm}_{$rawTahun}_{$rawSemester}_" . ($prodi->is_aptikom ? 'aptikom' : 'nonaptikom');
            Cache::forget($cacheKey);
            $fromCache = false;

            // Lakukan kalkulasi fresh
            $data      = $this->buildCompetencyData($mahasiswa, $prodi, $filterInfo['semestersToFilter']); //hitung fresh
            $data      = $this->prepareDataForBlade($data, $prodi);
            $data      = $this->addIpkAndTotals($data);


        $data['activePeriodLabel'] = $filterInfo['activePeriodLabel'];
        $data['isSingleSemester']  = $filterInfo['isSingleSemester'];
        $data['selectedTahun']     = $rawTahun;
        $data['selectedSemester']  = $rawSemester;

        Log::info('getCompetencyData sukses', [
            'npm'           => $npm,
            'period'        => $filterInfo['activePeriodLabel'],
            'type'          => $prodi->is_aptikom ? 'APTIKOM' : 'Non-APTIKOM',
            'jumlah_course' => count($data['courses'] ?? []),
            'from_cache'    => $fromCache,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'cached'  => $fromCache,
            'type'    => $prodi->is_aptikom ? 'aptikom' : 'non_aptikom',
        ]);
    } catch (\Throwable $e) {
        Log::error('getCompetencyData ERROR', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    }
}

private function buildCompetencyData(Mahasiswa $mahasiswa, Prodi $prodi, ?array $semestersToFilter = null): array
{
    //ambil mk dari mutus
    $query = DB::table('mutus')
        ->where(function($q) use ($mahasiswa) {
            $q->where('mutus.npm', $mahasiswa->NPM)->orWhere('mutus.NPM', $mahasiswa->NPM);
        });

    if (!empty($semestersToFilter)) {
        $query->join('mks', 'mutus.Course', '=', 'mks.kode')
            ->whereIn('mks.semester', $semestersToFilter);
    }

    $courses = $query->select('mutus.Course')
        ->groupBy('mutus.Course')
        ->pluck('mutus.Course');

    if ($courses->isEmpty()) {
        return $this->emptyStructure();
    }

    $courseData    = [];
    $allCpmkScores = [];
    $allCplScores  = [];

    foreach ($courses as $courseKode) {
        $mkInfo  = $this->getMkInfo($courseKode, $prodi->is_aptikom);
        $records = $mahasiswa->getCourseRecords($courseKode);
        if ($records->isEmpty()) continue;

        $latestRecord = $records->first();
        $totalSks     = ($mkInfo->bobot_teori ?? 0) + ($mkInfo->bobot_praktikum ?? 0);

        // --- CPMK: hitung dulu $cpmk data isinya cpmk ini saja perhitungannya
        $cpmkData = $this->buildCpmkData($records, $prodi, $allCpmkScores);

        // --- CPL: sekarang pakai $cpmkData, bukan records mentah ---
        $cplData = $this->buildCplData($records, $prodi, $allCplScores, $cpmkData);

        // --- Final Grade ---
        $finalGrade = $prodi->is_aptikom
            ? $this->calcFinalGradeAptikom($records, $courseKode)
            : $this->calcFinalGradeNonAptikom($records);
        //konversi nilai angka ke huruf mutu
        [$letterGrade, $gradeWeight] = $prodi->convertGrade($finalGrade);
        $mutu = $gradeWeight !== null ? $gradeWeight * $totalSks : null;
        //semua data mk dikumpukan disini
        $courseData[] = [
            'kode'        => $courseKode,
            'nama'        => $mkInfo->nama ?? $courseKode,
            'tahun'       => $latestRecord->tahun ?? null,
            'kurikulum'   => $mkInfo->tahun_kurikulum ?? $mkInfo->kurikulum ?? null,
            'semester'    => $mkInfo->semester ?? $mkInfo->smt ?? null,
            'sks'         => $totalSks,
            'grade_huruf' => $letterGrade,
            'bobot_mutu'  => $gradeWeight,
            'mutu'        => $mutu,
            'cpmks'       => $cpmkData,
            'cpls'        => $cplData,
            'nilai_akhir' => round($finalGrade, 2),
            'status'      => $prodi->getStatusKelulusan($finalGrade),
        ];
    }

    $finalCpmkScores = [];
    foreach ($allCpmkScores as $cpmkId => $d) {
        $avg = array_sum($d['scores']) / count($d['scores']);
        $finalCpmkScores[] = [
            'id'        => $cpmkId,
            'kode'      => $d['kode'],
            'deskripsi' => $d['deskripsi'],
            'nilai'     => round($avg, 2),
            'status'    => $prodi->getStatusKompetensi($avg),
        ];
    }

    $avgCpmk = count($finalCpmkScores) > 0
        ? round(array_sum(array_column($finalCpmkScores, 'nilai')) / count($finalCpmkScores), 2)
        : 0;

    $finalCplScores = [];
    foreach ($allCplScores as $cplId => $d) {
        $avg = array_sum($d['scores']) / count($d['scores']);
        $finalCplScores[] = [
            'id'        => $cplId,
            'kode'      => $d['kode'],
            'deskripsi' => $d['deskripsi'],
            'nilai'     => round($avg, 2),
            'status'    => $prodi->getStatusKompetensi($avg),
        ];
    }

    $avgCpl = count($finalCplScores) > 0
        ? round(array_sum(array_column($finalCplScores, 'nilai')) / count($finalCplScores), 2)
        : 0;

    return [
        'courses'  => $courseData, //data per mk
        'cpmks'    => $finalCpmkScores, //semua data cpmk (rata-rata kumulatif lintas MK)
        'cpls'     => $finalCplScores, //semua cpl
        'avg_cpmk' => $avgCpmk,
        'avg_cpl'  => $avgCpl,
        'type'     => $prodi->is_aptikom ? 'aptikom' : 'non_aptikom',
    ];
}

/**
 * Hitung skor semua CPMK dari satu set record MK.
 * Menggunakan Mahasiswa::calcWeightedScore() agar rumus tidak terduplikasi.
 */
private function buildCpmkData(
    \Illuminate\Support\Collection $records,
    Prodi $prodi,
    array &$allCpmkScores
): array {
    $result = [];
    if ($records->isEmpty()) return $result;

    $courseKode = $records->first()->Course;
    $npm = $records->first()->npm ?? $records->first()->NPM;

    $allCourseRecords = DB::table('mutus')
        ->where(function($q) use ($npm) {
            $q->where('npm', $npm)->orWhere('NPM', $npm);
        })
        ->where('Course', $courseKode)
        ->get();

    if ($allCourseRecords->isEmpty()) {
        $allCourseRecords = $records;
    } else if ($records->contains(fn($r) => ($r->sumber ?? '') === 'konversi' || !empty($r->konversi_metode_id))) {
        $allCourseRecords = $allCourseRecords->filter(fn($r) => ($r->sumber ?? '') === 'konversi' || !empty($r->konversi_metode_id));
    }

    $konversiMetodeIds = $allCourseRecords->where('sumber', 'konversi')
        ->whereNotNull('konversi_metode_id')
        ->pluck('konversi_metode_id')
        ->unique()
        ->toArray();

    $cpmkToKmMap = [];
    $konversiCpmkIds = [];
    if (!empty($konversiMetodeIds)) {
        $mappedCpmkRows = DB::table('konversi_cpmk_metode')
            ->whereIn('konversi_metode_id', $konversiMetodeIds)
            ->get();

        foreach ($mappedCpmkRows as $mapRow) {
            $cId = (int)$mapRow->cpmk_id;
            $konversiCpmkIds[] = $cId;
            $cpmkToKmMap[$cId][] = $mapRow->konversi_metode_id;
        }
    }

    $directCpmkIds = [];
    foreach ($allCourseRecords->pluck('Cpmk')->filter() as $cVal) {
        $cValStr = trim((string)$cVal);
        if (is_numeric($cValStr)) {
            $directCpmkIds[] = (int)$cValStr;
        } else {
            $cIdFromDb = DB::table('cpmks')->where('kode', $cValStr)->value('id');
            if ($cIdFromDb) {
                $directCpmkIds[] = (int)$cIdFromDb;
            }
        }
    }

    $allUniqueCpmkIds = array_unique(array_merge($directCpmkIds, $konversiCpmkIds));
    $mahasiswaDummy = new Mahasiswa();

    foreach ($allUniqueCpmkIds as $cpmkId) {
        $cpmkId = (int)$cpmkId;
        $kmIdsForThisCpmk = array_unique($cpmkToKmMap[$cpmkId] ?? []);

        if (empty($kmIdsForThisCpmk)) {
            $kmIdsForThisCpmk = DB::table('konversi_cpmk_metode as kcm')
                ->join('konversi_metode as km', 'kcm.konversi_metode_id', '=', 'km.id')
                ->join('penilaian_konversi as pk', 'km.penilaian_konversi_id', '=', 'pk.id')
                ->where('pk.mk_kode', $courseKode)
                ->where('kcm.cpmk_id', $cpmkId)
                ->pluck('km.id')
                ->unique()
                ->toArray();
        }

        $hasKonversiInCourse = $allCourseRecords->contains(fn($r) => !empty($r->konversi_metode_id) || ($r->sumber ?? '') === 'konversi');
        $hasRegularInCourse  = $allCourseRecords->contains(fn($r) => empty($r->konversi_metode_id) && ($r->sumber ?? '') !== 'konversi');

        if ($hasKonversiInCourse && !$hasRegularInCourse && !empty($kmIdsForThisCpmk)) {
            $cpmkRecords = $allCourseRecords->whereIn('konversi_metode_id', $kmIdsForThisCpmk);
        } else {
            $cpmkRecords = $allCourseRecords->filter(function($r) use ($cpmkId, $kmIdsForThisCpmk) {
                $rCpmkId = null;
                if (!empty($r->Cpmk)) {
                    $cValStr = trim((string)$r->Cpmk);
                    if (is_numeric($cValStr)) {
                        $rCpmkId = (int)$cValStr;
                    } else {
                        $rCpmkId = DB::table('cpmks')->where('kode', $cValStr)->value('id');
                    }
                }

                $isMatchByCpmk = ($rCpmkId !== null && (int)$rCpmkId === (int)$cpmkId);
                $isMatchByKonversi = (!empty($r->konversi_metode_id) && in_array($r->konversi_metode_id, $kmIdsForThisCpmk));

                return $isMatchByCpmk || $isMatchByKonversi;
            })->unique('id');
        }

        if ($cpmkRecords->isEmpty()) continue;

        $score = $mahasiswaDummy->calcWeightedScore($cpmkRecords);
        if ($score <= 0) continue;

        $detail = DB::table('cpmks')->where('id', $cpmkId)->first();
        $status = $prodi->getStatusKompetensi($score);
        $entry  = [
            'id'         => $cpmkId,
            'kode'       => $detail?->kode     ?? 'CPMK-' . $cpmkId,
            'deskripsi'  => $detail?->judul    ?? 'Tidak ada deskripsi',
            'indikator'  => $detail?->indikator ?? '',
            'kriteria'   => $detail?->kriteria  ?? '',
            'nilai'      => round($score, 2),
            'status'     => $status,
        ];

        $result[] = $entry;

        if (!isset($allCpmkScores[$cpmkId])) {
            $allCpmkScores[$cpmkId] = [
                'id'        => $cpmkId,
                'kode'      => $entry['kode'],
                'deskripsi' => $entry['deskripsi'],
                'scores'    => [],
            ];
        }
        $allCpmkScores[$cpmkId]['scores'][] = $entry['nilai'];
    }

    return $result;
}

    /**
     * Hitung skor semua CPL dari satu set record MK.
     */
    private function buildCplData(
        \Illuminate\Support\Collection $records,
        Prodi $prodi,
        array &$allCplScores,
        array $cpmkData   // ← hasil buildCpmkData(), bukan records mentah
    ): array {
        // 1. Mapping cpmk_id → cpl_id dari records reguler maupun dari master cpmks
        $cpmkToCpl = $records
            ->whereNotNull('Cpl')
            ->whereNotNull('Cpmk')
            ->mapWithKeys(fn($r) => [(string) $r->Cpmk => (string) $r->Cpl])
            ->toArray();

        $cpmkIdsToResolve = array_filter(array_column($cpmkData, 'id'), fn($id) => !isset($cpmkToCpl[(string)$id]));
        if (!empty($cpmkIdsToResolve)) {
            $dbCpmks = DB::table('cpmks')->whereIn('id', $cpmkIdsToResolve)->get(['id', 'cpl_id']);
            foreach ($dbCpmks as $cRow) {
                if (!empty($cRow->cpl_id)) {
                    $cpmkToCpl[(string)$cRow->id] = (string)$cRow->cpl_id;
                }
            }
        }

        // 2. Kelompokkan nilai CPMK (yang sudah dihitung) berdasarkan CPL-nya
        $cplGroups = [];
        foreach ($cpmkData as $cpmkEntry) {
            $cpmkId = (string) $cpmkEntry['id'];
            $cplId  = $cpmkToCpl[$cpmkId] ?? null;
            if (!$cplId) continue;

            $cplGroups[$cplId][] = $cpmkEntry['nilai'];
        }

        // 3. Rata-ratakan nilai CPMK per CPL → entry CPL
        $result = [];
        foreach ($cplGroups as $cplId => $nilaiList) {
            $score = array_sum($nilaiList) / count($nilaiList);
            if ($score <= 0) continue;

            $detail = DB::table('cpls')->where('id', $cplId)->first();
            $status = $prodi->getStatusKompetensi($score);

            $entry = [
                'id'        => $cplId,
                'kode'      => $detail?->kode  ?? 'CPL-' . $cplId,
                'deskripsi' => $detail?->judul ?? 'Tidak ada deskripsi',
                'nilai'     => round($score, 2),
                'status'    => $status,
            ];

            $result[] = $entry;

            $allCplScores[$cplId]['scores'][]  = $score;
            $allCplScores[$cplId]['kode']      = $entry['kode'];
            $allCplScores[$cplId]['deskripsi'] = $entry['deskripsi'];
            $allCplScores[$cplId]['status']    = $entry['status'];
        }

        return $result;
    }

    /**
     * Final grade APTIKOM: menggunakan totalBobotUtuh dari tabel mapping
     * untuk pembagi yang jujur terhadap instrumen penuh.
     */
    private function calcFinalGradeAptikom(
        \Illuminate\Support\Collection $records,
        string $courseKode
    ): float {
        $total = 0.0;

        foreach ($records->groupBy('Jenis') as $jenis => $group) {
            $examWeight = $group->first()->examWeight ?? 0;

            $totalBobotUtuh = DB::table('cpl_mk_cpmk_penilaian as cmcp')
                ->join('penilaian_metode as pm', 'pm.cpl_mk_cpmk_penilaian_id', '=', 'cmcp.id')
                ->join('metode_penilaian as mp', 'mp.id', '=', 'pm.metode_id')
                ->where('cmcp.mk_kode', $courseKode)
                ->where('mp.nama', $jenis)
                ->sum('pm.bobot');

            $pembagi = $totalBobotUtuh > 0 ? $totalBobotUtuh : $group->sum('BobotSoal');
            if ($pembagi <= 0) continue;

            $nilaiJenis = $group->sum(fn($r) => ($r->BobotSoal / $pembagi) * ($r->nilaiSoal ?? 0));
            $total     += ($examWeight / 100) * $nilaiJenis;
        }

        return round($total, 2);
    }

    /**
     * Final grade Non-APTIKOM: rata-rata tertimbang sederhana dari nilaiSoal.
     */
    private function calcFinalGradeNonAptikom(\Illuminate\Support\Collection $records): float
    {
        $finalGrade  = 0.0;
        $totalWeight = 0;

        foreach ($records->groupBy('Jenis') as $group) {
            $weight      = $group->first()->examWeight ?? 0;
            $nilai       = $group->avg('Nilai') ?? 0;
            $finalGrade += ($weight / 100) * $nilai;
            $totalWeight += $weight;
        }

        if ($totalWeight > 0 && $totalWeight != 100) {
            $finalGrade = $finalGrade * (100 / $totalWeight);
        }

        return round($finalGrade, 2);
    }

    private function prepareDataForBlade(array $data, Prodi $prodi): array
    {
        $data['cpl_status']     = $prodi->getStatusKompetensi($data['avg_cpl']);
        $data['progress_class'] = Prodi::progressClass($data['avg_cpl']);

        $data['cpls'] = array_map(fn($cpl) => array_merge($cpl, [
            'badge_class' => Prodi::badgeClassKompetensi($cpl['status']),
        ]), $data['cpls'] ?? []);

        $data['cpmks'] = array_map(fn($cpmk) => array_merge($cpmk, [
            'badge_class' => Prodi::badgeClassKompetensi($cpmk['status']),
        ]), $data['cpmks'] ?? []);

        $data['courses'] = array_map(function ($course) use ($prodi) {
            $course['grade_color']  = Prodi::gradeColorClass($course['grade_huruf']);
            $course['status_badge'] = Prodi::badgeClassKelulusan($course['status']);

            $course['cpmks'] = array_map(fn($c) => array_merge($c, [
                'badge_class' => Prodi::badgeClassKompetensi($c['status']),
            ]), $course['cpmks'] ?? []);

            $course['cpls'] = array_map(fn($c) => array_merge($c, [
                'badge_class' => Prodi::badgeClassKompetensi($c['status']),
            ]), $course['cpls'] ?? []);

            return $course;
        }, $data['courses'] ?? []);

        return $data;
    }

    private function addIpkAndTotals(array $data): array
    {
        $totalSks   = 0;
        $totalMutu  = 0;

        foreach ($data['courses'] ?? [] as $c) {
            $sks        = (float) ($c['sks'] ?? 0);
            $bobot      = (float) ($c['bobot_mutu'] ?? 0);
            $totalSks  += $sks;
            $totalMutu += $bobot * $sks;
        }

        $data['total_sks']  = $totalSks;
        $data['total_mutu'] = $totalMutu;
        $data['ipk']        = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0;

        return $data;
    }

    /** Guard: validasi user adalah mahasiswa aktif, return model Mahasiswa. */
    private function guardMahasiswa(\App\Models\User $user): Mahasiswa
    {
        $isMahasiswa = DB::table('user_otoritas')
            ->where('user_id', $user->id)
            ->where('otoritas', 'Mahasiswa')
            ->where('active', 1)
            ->exists();

        if (!$isMahasiswa) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Mahasiswa.');
        }

        $mahasiswa = Mahasiswa::findForUser($user);

        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        return $mahasiswa;
    }

    /** Load mahasiswaData (dari mutus), prodi, dan universitas sekaligus. */
    private function loadMahasiswaContext(string $npm): array
    {
        $mahasiswaData = DB::table('mutus')
            ->where('npm', $npm)
            ->select('npm', 'nama_mhs', 'angkatan', 'id_prodi', 'universitas_id')
            ->first();

        if (!$mahasiswaData) {
            abort(404, 'Data mahasiswa tidak ditemukan di sistem penilaian.');
        }

        $prodi      = Prodi::findOrFail($mahasiswaData->id_prodi);
        $universitas = DB::table('universitas')->where('id', $mahasiswaData->universitas_id)->first();

        return [$mahasiswaData, $prodi, $universitas];
    }

    /** Ambil info prodi dari tabel mutus (join prodi). */
    private function getProdiInfo(string $npm): ?object
    {
        return DB::table('mutus')
            ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
            ->where('mutus.npm', $npm)
            ->select('prodi.id as prodi_id', 'prodi.is_aptikom', 'prodi.jenjang')
            ->first();
    }

    /** Ambil info MK (dengan fallback kurikulum yang kuat). */
    private function getMkInfo(string $courseKode, bool $isAptikom): object
    {
        $mk = DB::table('mks')
            ->leftJoin('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
            ->where('mks.kode', $courseKode)
            ->select('mks.*', 'kurikulums.tahun as tahun_kurikulum')
            ->first();

        if (!$mk) {
            $mk = (object) ['kode' => $courseKode];
        }

        if (empty($mk->tahun_kurikulum) && !empty($mk->kurikulum)) {
            $mk->tahun_kurikulum = $mk->kurikulum;
        }

        if (empty($mk->tahun_kurikulum)) {
            $pkKur = DB::table('penilaian_konversi')
                ->join('kurikulums', 'penilaian_konversi.kurikulum_id', '=', 'kurikulums.id')
                ->where('penilaian_konversi.mk_kode', $courseKode)
                ->value('kurikulums.tahun');

            if ($pkKur) {
                $mk->tahun_kurikulum = $pkKur;
            }
        }

        return $mk;
    }

    /** Resolve data lengkap untuk print/download — dipakai oleh dua method PDF. */
    private function resolveTranskripData(?Request $request = null): array
    {
        $npm = session('current_npm');
        if (!$npm && Auth::check()) {
            $mhsAuth = Mahasiswa::findForUser(Auth::user());
            $npm = $mhsAuth?->NPM;
        }
        abort_unless($npm, 404, 'NPM tidak ditemukan.');

        // loadMahasiswaContext mengembalikan object dengan field:
        // npm, nama_mhs, angkatan, id_prodi, universitas_id
        [$mahasiswaData, $prodi, $universitas] = $this->loadMahasiswaContext($npm);

        $filterInfo = $this->parsePeriodFilters($request ?? request(), $npm, $mahasiswaData->angkatan ?? null);

        $mahasiswa     = Mahasiswa::where('NPM', $npm)->firstOrFail();
        $transkripData = $this->buildCompetencyData($mahasiswa, $prodi, $filterInfo['semestersToFilter']);
        $transkripData = $this->prepareDataForBlade($transkripData, $prodi);
        $transkripData = $this->addIpkAndTotals($transkripData);
        $transkripData['activePeriodLabel'] = $filterInfo['activePeriodLabel'];
        $transkripData['isSingleSemester']  = $filterInfo['isSingleSemester'];

        // Kembalikan $mahasiswaData (bukan $mahasiswa model)
        // agar field npm, nama_mhs, angkatan tersedia di Blade
        return [$mahasiswaData, $prodi, $universitas, $transkripData];
    }

    public function printTranskrip(Request $request)
    {
        [$mahasiswaData, $prodi, $universitas, $transkripData] = $this->resolveTranskripData($request);

        return view('mahasiswa.pdftranskrip_kompetensi', [
            'mahasiswaData'  => $mahasiswaData,
            'prodi'          => $prodi,
            'universitas'    => $universitas,
            'transkripData'  => $transkripData,
            'isPrint'        => true,
        ]);
    }

    public function downloadTranskripPdf(Request $request)
    {
        [$mahasiswaData, $prodi, $universitas, $transkripData] = $this->resolveTranskripData($request);

        $pdf = Pdf::loadView('mahasiswa.pdftranskrip_kompetensi', [
            'mahasiswaData'  => $mahasiswaData,
            'prodi'          => $prodi,
            'universitas'    => $universitas,
            'transkripData'  => $transkripData,
            'isPrint'        => false,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Transkrip-Kompetensi-' . ($mahasiswaData->npm ?? 'mahasiswa') . '.pdf');
    }

    private function emptyStructure(): array
    {
        return ['courses' => [], 'cpmks' => [], 'cpls' => [], 'avg_cpmk' => 0, 'avg_cpl' => 0, 'type' => null];
    }

    private function jsonError(string $message, int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
