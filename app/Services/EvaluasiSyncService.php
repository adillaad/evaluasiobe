<?php

namespace App\Services;

use App\Models\VisualisasiMahasiswa;
use App\Models\VisualisasiAngkatan;
use App\Models\VisualisasiMataKuliah;
use App\Models\VisualisasiProdi;
use App\Models\VisualisasiFakultas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluasiSyncService
{
    /**
     * Expression to resolve maximum year and semester between mutus and tahun_ajaran.
     */
    private function getYearSemSqlExpr($tableAlias = 'mutus', $taAlias = 'ta')
    {
        return "CONCAT(
            COALESCE(CONVERT($taAlias.tahun USING utf8mb4) COLLATE utf8mb4_unicode_ci, CONVERT(SUBSTRING_INDEX($tableAlias.tahun, ' ', -1) USING utf8mb4) COLLATE utf8mb4_unicode_ci),
            CASE WHEN COALESCE(CONVERT($taAlias.jenis_semester USING utf8mb4) COLLATE utf8mb4_unicode_ci, CONVERT(SUBSTRING_INDEX($tableAlias.tahun, ' ', 1) USING utf8mb4) COLLATE utf8mb4_unicode_ci) = 'Genap' THEN '1' ELSE '0' END
        )";
    }

    /**
     * Helper to resolve semester numbers to filter based on angkatan and filter inputs.
     */
    private function resolveSemesterFilters($baseAngkatan, $rawTahun = 'all', $rawSemester = 'all')
    {
        $semestersToFilter = [];
        if ($rawSemester && $rawSemester !== 'all' && is_numeric($rawSemester)) {
            $semestersToFilter = [(int)$rawSemester];
        } elseif ($rawTahun && $rawTahun !== 'all') {
            $yearOffset = 0;
            if (is_numeric($rawTahun)) {
                $val = (int)$rawTahun;
                $yearOffset = ($val >= 2000) ? ($val - $baseAngkatan) : ($val - 1);
            } elseif (strpos($rawTahun, '/') !== false) {
                $startYear = (int)explode('/', $rawTahun)[0];
                $yearOffset = $startYear - $baseAngkatan;
            }
            $sem1 = $yearOffset * 2 + 1;
            $sem2 = $yearOffset * 2 + 2;
            $semestersToFilter = [$sem1, $sem2];
        }
        return $semestersToFilter;
    }

    /**
     * 1. Synchronize Visualisasi per Mahasiswa.
     */
    public function syncMahasiswa($npm, $rawTahun = 'all', $rawSemester = 'all')
    {
        $mhs = DB::table('mahasiswa')->where('NPM', $npm)->first();
        if (!$mhs) {
            return false;
        }

        $baseAngkatan = (int)$mhs->angkatan > 1900 ? (int)$mhs->angkatan : (int)substr($npm, 0, 2) + 2000;
        if ($baseAngkatan < 2000) {
            $baseAngkatan = (int)date('Y') - 4;
        }

        $semestersToFilter = $this->resolveSemesterFilters($baseAngkatan, $rawTahun, $rawSemester);

        // A. CPL Calculation
        $cpls = DB::table('cpls')->where('id_prodi', $mhs->id_prodi)->get();
        $yearExprQ1 = $this->getYearSemSqlExpr('m', 'ta');
        $yearExprQ2 = $this->getYearSemSqlExpr('m2', 'ta2');

        $semConditionQ1 = !empty($semestersToFilter) ? " AND mks.semester IN (" . implode(',', $semestersToFilter) . ") " : "";
        $semConditionQ2 = !empty($semestersToFilter) ? " AND mks2.semester IN (" . implode(',', $semestersToFilter) . ") " : "";

        $q1 = "
            SELECT m.cpl, SUM((m.BobotSoal * m.nilaiSoal) / q1.BobotJenisCPMK * m.examWeight / q2.sumExamWeight) AS r3
            FROM mutus m
            JOIN mks ON m.Course = mks.kode
            LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
            JOIN (
                SELECT m2.Course, MAX($yearExprQ2) AS max_year_semester
                FROM mutus m2
                JOIN mks mks2 ON m2.Course = mks2.kode
                LEFT JOIN tahun_ajaran ta2 ON m2.tahun_ajaran_id = ta2.id
                WHERE m2.npm = :npm1 {$semConditionQ2}
                GROUP BY m2.Course
            ) t ON m.Course = t.Course AND $yearExprQ1 = t.max_year_semester
            JOIN (
                SELECT Cpmk, Jenis, tahun, SUM(BobotSoal) AS BobotJenisCPMK
                FROM mutus
                WHERE npm = :npm2
                GROUP BY Cpmk, Jenis, tahun
            ) q1 ON m.cpmk = q1.Cpmk AND m.Jenis = q1.Jenis AND m.tahun = q1.tahun
            JOIN (
                SELECT tahun, Cpmk, SUM(examWeight) AS sumExamWeight
                FROM (
                    SELECT DISTINCT tahun, Cpmk, Jenis, examWeight
                    FROM mutus
                    WHERE npm = :npm3
                ) AS subquery
                GROUP BY tahun, Cpmk
            ) q2 ON q1.cpmk = q2.Cpmk AND q1.tahun = q2.tahun
            WHERE m.npm = :npm4 {$semConditionQ1}
            GROUP BY m.cpl;
        ";

        $cplScores = DB::select($q1, [
            'npm1' => $npm,
            'npm2' => $npm,
            'npm3' => $npm,
            'npm4' => $npm,
        ]);

        $rekapCpl = [];
        $totalCplScore = 0;
        $cplCount = 0;
        $scoreDict = [];

        foreach ($cplScores as $cs) {
            $scoreDict[$cs->cpl] = (float)$cs->r3;
        }

        foreach ($cpls as $cpl) {
            $skor = isset($scoreDict[$cpl->id]) ? round($scoreDict[$cpl->id], 2) : 0;
            $status = ($skor >= 75) ? 'Sangat Baik' : (($skor >= 51) ? 'Cukup' : (($skor > 0) ? 'Perlu Peningkatan' : 'Belum Ada Data'));

            $rekapCpl[] = [
                'cpl_id' => $cpl->id,
                'kode' => $cpl->kode ?? 'CPL',
                'judul' => $cpl->judul ?? '',
                'skor' => $skor,
                'status' => $status,
            ];

            if ($skor > 0) {
                $totalCplScore += $skor;
                $cplCount++;
            }
        }

        $avgCpl = $cplCount > 0 ? round($totalCplScore / $cplCount, 2) : 0;

        // B. CPMK Calculation
        $yearExprCpmkM = $this->getYearSemSqlExpr('m', 'ta');
        $yearExprCpmkM2 = $this->getYearSemSqlExpr('m2', 'ta2');

        $qCpmk = "
            SELECT m.cpmk, cpmks.kode, cpmks.judul, m.Course,
                SUM((m.BobotSoal * m.nilaiSoal) / q1.BobotJenisCPMK * m.examWeight / q2.sumExamWeight) AS r3
            FROM mutus m
            LEFT JOIN cpmks ON m.cpmk = cpmks.id
            LEFT JOIN tahun_ajaran ta ON m.tahun_ajaran_id = ta.id
            INNER JOIN (
                SELECT Course, MAX($yearExprCpmkM2) AS max_year_semester
                FROM mutus m2
                LEFT JOIN tahun_ajaran ta2 ON m2.tahun_ajaran_id = ta2.id
                WHERE m2.NPM = :npm1
                GROUP BY Course
            ) t ON m.Course = t.Course AND $yearExprCpmkM = t.max_year_semester
            JOIN (
                SELECT Cpmk, Jenis, tahun, SUM(BobotSoal) AS BobotJenisCPMK
                FROM mutus
                WHERE NPM = :npm2
                GROUP BY Cpmk, Jenis, tahun
            ) q1 ON m.cpmk = q1.Cpmk AND m.Jenis = q1.Jenis AND m.tahun = q1.tahun
            JOIN (
                SELECT tahun, Cpmk, SUM(examWeight) AS sumExamWeight
                FROM (
                    SELECT DISTINCT tahun, Cpmk, Jenis, examWeight
                    FROM mutus
                    WHERE NPM = :npm3
                ) AS subquery
                GROUP BY tahun, Cpmk
            ) q2 ON q1.cpmk = q2.Cpmk AND q1.tahun = q2.tahun
            WHERE m.NPM = :npm4
            GROUP BY m.cpmk, cpmks.kode, cpmks.judul, m.Course
        ";

        $cpmkScores = DB::select($qCpmk, [
            'npm1' => $npm,
            'npm2' => $npm,
            'npm3' => $npm,
            'npm4' => $npm,
        ]);

        $rekapCpmk = [];
        $totalCpmkScore = 0;
        foreach ($cpmkScores as $cp) {
            $skor = round((float)$cp->r3, 2);
            $totalCpmkScore += $skor;
            $rekapCpmk[] = [
                'cpmk_id' => $cp->cpmk,
                'kode' => $cp->kode ?? 'CPMK',
                'judul' => $cp->judul ?? '',
                'kode_mk' => $cp->Course,
                'skor' => $skor,
                'status' => ($skor >= 75) ? 'Sangat Baik' : (($skor >= 51) ? 'Cukup' : 'Perlu Peningkatan'),
            ];
        }

        $avgCpmk = count($rekapCpmk) > 0 ? round($totalCpmkScore / count($rekapCpmk), 2) : 0;

        // C. Rekap Mata Kuliah & IPK
        $courseKodes = DB::table('mutus')->where('npm', $npm)->distinct()->pluck('Course');
        $rekapMk = [];
        $totalSks = 0;
        $totalBobotSks = 0;

        foreach ($courseKodes as $ck) {
            $mkRow = DB::table('mks')->where('kode', $ck)->first();
            $sks = $mkRow ? (int)$mkRow->bobot_teori + (int)$mkRow->bobot_praktikum : 3;
            $totalSks += $sks;

            // Simple avg NA
            $na = DB::table('mutus')->where('npm', $npm)->where('Course', $ck)->avg('Nilai') ?? 0;
            $na = round($na, 2);

            $huruf = 'E';
            $bobot = 0.0;
            if ($na >= 76) { $huruf = 'A'; $bobot = 4.0; }
            elseif ($na >= 71) { $huruf = 'B+'; $bobot = 3.5; }
            elseif ($na >= 66) { $huruf = 'B'; $bobot = 3.0; }
            elseif ($na >= 61) { $huruf = 'C+'; $bobot = 2.5; }
            elseif ($na >= 56) { $huruf = 'C'; $bobot = 2.0; }
            elseif ($na >= 50) { $huruf = 'D'; $bobot = 1.0; }

            $totalBobotSks += ($bobot * $sks);

            $rekapMk[] = [
                'kode' => $ck,
                'nama' => $mkRow->nama ?? $ck,
                'sks' => $sks,
                'semester' => $mkRow->semester ?? 1,
                'nilai_akhir' => $na,
                'huruf' => $huruf,
                'bobot' => $bobot,
                'status' => ($na >= 50) ? 'Lulus' : 'Tidak Lulus',
            ];
        }

        $ipkObe = $totalSks > 0 ? round($totalBobotSks / $totalSks, 2) : 0.00;

        VisualisasiMahasiswa::updateOrCreate(
            [
                'npm' => $npm,
                'tahun_filter' => (string)$rawTahun,
                'semester_filter' => (string)$rawSemester,
            ],
            [
                'id_prodi' => $mhs->id_prodi,
                'angkatan' => $mhs->angkatan,
                'ipk_obe' => $ipkObe,
                'avg_cpl' => $avgCpl,
                'avg_cpmk' => $avgCpmk,
                'total_sks' => $totalSks,
                'total_mk' => count($rekapMk),
                'rekap_cpl_json' => $rekapCpl,
                'rekap_cpmk_json' => $rekapCpmk,
                'rekap_mk_json' => $rekapMk,
                'last_calculated_at' => now(),
            ]
        );

        return true;
    }

    /**
     * 2. Synchronize Visualisasi per Angkatan.
     */
    public function syncAngkatan($idProdi, $angkatan, $rawTahun = 'all', $rawSemester = 'all')
    {
        $cpls = DB::table('cpls')->where('id_prodi', $idProdi)->get();
        if ($cpls->isEmpty()) return false;

        $cplRekap = [];
        $totalCplScore = 0;

        foreach ($cpls as $cpl) {
            $mhsScores = DB::table('visualisasi_mahasiswas')
                ->where('id_prodi', $idProdi)
                ->where('angkatan', $angkatan)
                ->where('tahun_filter', (string)$rawTahun)
                ->where('semester_filter', (string)$rawSemester)
                ->get();

            $scoresList = [];
            foreach ($mhsScores as $ms) {
                $cplData = collect($ms->rekap_cpl_json)->firstWhere('cpl_id', $cpl->id);
                if ($cplData && isset($cplData['skor']) && $cplData['skor'] > 0) {
                    $scoresList[] = (float)$cplData['skor'];
                }
            }

            $avg = count($scoresList) > 0 ? round(array_sum($scoresList) / count($scoresList), 2) : 0;
            $min = count($scoresList) > 0 ? min($scoresList) : 0;
            $max = count($scoresList) > 0 ? max($scoresList) : 0;
            $lulusCount = count(array_filter($scoresList, fn($s) => $s >= 65));
            $pctLulus = count($scoresList) > 0 ? round(($lulusCount / count($scoresList)) * 100, 1) : 0;

            $totalCplScore += $avg;
            $cplRekap[] = [
                'cpl_id' => $cpl->id,
                'kode' => $cpl->kode,
                'judul' => $cpl->judul,
                'avg_skor' => $avg,
                'min_skor' => $min,
                'max_skor' => $max,
                'total_mahasiswa' => count($scoresList),
                'persentase_lulus' => $pctLulus,
            ];
        }

        $overallAvgCpl = count($cplRekap) > 0 ? round($totalCplScore / count($cplRekap), 2) : 0;

        VisualisasiAngkatan::updateOrCreate(
            [
                'id_prodi' => $idProdi,
                'angkatan' => $angkatan,
                'tahun_filter' => (string)$rawTahun,
                'semester_filter' => (string)$rawSemester,
            ],
            [
                'avg_skor_cpl' => $overallAvgCpl,
                'rekap_cpl_json' => $cplRekap,
                'rekap_cpmk_json' => [],
                'distribusi_kelulusan_json' => [],
                'last_calculated_at' => now(),
            ]
        );

        return true;
    }

    /**
     * 3. Synchronize Visualisasi per Mata Kuliah.
     */
    public function syncMataKuliah($kodeMk, $angkatan, $idProdi, $rawTahun = 'all', $rawSemester = 'all')
    {
        $mk = DB::table('mks')->where('kode', $kodeMk)->first();
        if (!$mk) return false;

        $cpmks = DB::table('cpmks')
            ->join('cpmk_mk', 'cpmks.id', '=', 'cpmk_mk.cpmk_id')
            ->where('cpmk_mk.mk_kode', $kodeMk)
            ->select('cpmks.*')
            ->get();

        $cpmkRekap = [];
        $totalCpmkScore = 0;

        foreach ($cpmks as $cpmk) {
            $avgScore = DB::table('mutus')
                ->where('Course', $kodeMk)
                ->where('angkatan', $angkatan)
                ->where('cpmk', $cpmk->id)
                ->avg('Nilai') ?? 0;

            $avgScore = round($avgScore, 2);
            $totalCpmkScore += $avgScore;

            $cpmkRekap[] = [
                'cpmk_id' => $cpmk->id,
                'kode' => $cpmk->kode,
                'judul' => $cpmk->judul,
                'avg_skor' => $avgScore,
                'status' => ($avgScore >= 75) ? 'Sangat Baik' : (($avgScore >= 51) ? 'Cukup' : 'Perlu Peningkatan'),
            ];
        }

        $overallAvgMk = count($cpmkRekap) > 0 ? round($totalCpmkScore / count($cpmkRekap), 2) : 0;

        VisualisasiMataKuliah::updateOrCreate(
            [
                'id_prodi' => $idProdi,
                'angkatan' => $angkatan,
                'kode_mk' => $kodeMk,
                'tahun_filter' => (string)$rawTahun,
                'semester_filter' => (string)$rawSemester,
            ],
            [
                'avg_skor_mk' => $overallAvgMk,
                'rekap_cpmk_json' => $cpmkRekap,
                'rekap_cpl_json' => [],
                'distribusi_nilai_json' => [],
                'last_calculated_at' => now(),
            ]
        );

        return true;
    }

    /**
     * 4. Synchronize Visualisasi per Program Studi.
     */
    public function syncProdi($idProdi, $tahunAjaran = 'all', $semesterFilter = 'all')
    {
        $cpls = DB::table('cpls')->where('id_prodi', $idProdi)->get();
        if ($cpls->isEmpty()) return false;

        $radarCpl = [];
        $totalCplScore = 0;

        foreach ($cpls as $cpl) {
            $avg = DB::table('visualisasi_angkatans')
                ->where('id_prodi', $idProdi)
                ->get()
                ->flatMap(function($va) use ($cpl) {
                    $item = collect($va->rekap_cpl_json)->firstWhere('cpl_id', $cpl->id);
                    return $item ? [$item['avg_skor']] : [];
                })
                ->avg() ?? 0;

            $avg = round($avg, 2);
            $totalCplScore += $avg;

            $radarCpl[] = [
                'cpl_id' => $cpl->id,
                'kode' => $cpl->kode,
                'judul' => $cpl->judul,
                'avg_skor' => $avg,
            ];
        }

        $avgAll = count($radarCpl) > 0 ? round($totalCplScore / count($radarCpl), 2) : 0;

        VisualisasiProdi::updateOrCreate(
            [
                'id_prodi' => $idProdi,
                'tahun_ajaran' => (string)$tahunAjaran,
                'semester_filter' => (string)$semesterFilter,
            ],
            [
                'avg_cpl_keseluruhan' => $avgAll,
                'radar_cpl_json' => $radarCpl,
                'rekap_angkatan_json' => [],
                'last_calculated_at' => now(),
            ]
        );

        return true;
    }

    /**
     * 5. Synchronize Visualisasi per Fakultas.
     */
    public function syncFakultas($idFakultas, $idUniversitas = 1, $tahunAjaran = 'all', $semesterFilter = 'all')
    {
        $prodis = DB::table('prodi')->where('id_fakultas', $idFakultas)->get();
        if ($prodis->isEmpty()) return false;

        $prodiRekap = [];
        $totalScore = 0;

        foreach ($prodis as $p) {
            $visProdi = DB::table('visualisasi_prodis')
                ->where('id_prodi', $p->id)
                ->first();

            $score = $visProdi ? (float)$visProdi->avg_cpl_keseluruhan : 0;
            $totalScore += $score;

            $prodiRekap[] = [
                'prodi_id' => $p->id,
                'nama' => $p->nama,
                'jenjang' => $p->jenjang,
                'avg_cpl' => $score,
            ];
        }

        $avgFakultas = count($prodiRekap) > 0 ? round($totalScore / count($prodiRekap), 2) : 0;

        VisualisasiFakultas::updateOrCreate(
            [
                'id_fakultas' => $idFakultas,
                'id_universitas' => $idUniversitas,
                'tahun_ajaran' => (string)$tahunAjaran,
                'semester_filter' => (string)$semesterFilter,
            ],
            [
                'avg_cpl_fakultas' => $avgFakultas,
                'rekap_prodi_json' => $prodiRekap,
                'radar_cpl_json' => [],
                'last_calculated_at' => now(),
            ]
        );

        return true;
    }

    /**
     * Complete synchronization of all 5 modules across the database.
     */
    public function syncAll()
    {
        Log::info('Starting full Visualisasi OBE synchronization across 5 menus...');

        // 1. Sync Mahasiswa
        $mahasiswas = DB::table('mahasiswa')->select('NPM', 'angkatan', 'id_prodi')->get();
        $cohorts = [];

        foreach ($mahasiswas as $mhs) {
            $this->syncMahasiswa($mhs->NPM, 'all', 'all');

            if ($mhs->id_prodi && $mhs->angkatan) {
                $cohorts[$mhs->id_prodi . '|' . $mhs->angkatan] = [
                    'id_prodi' => $mhs->id_prodi,
                    'angkatan' => $mhs->angkatan
                ];
            }
        }

        // 2. Sync Angkatan & Mata Kuliah
        foreach ($cohorts as $coh) {
            $this->syncAngkatan($coh['id_prodi'], $coh['angkatan'], 'all', 'all');

            $mkList = DB::table('mutus')
                ->where('id_prodi', $coh['id_prodi'])
                ->where('angkatan', $coh['angkatan'])
                ->select('Course')
                ->distinct()
                ->pluck('Course');

            foreach ($mkList as $kodeMk) {
                $this->syncMataKuliah($kodeMk, $coh['angkatan'], $coh['id_prodi']);
            }
        }

        // 3. Sync Prodi
        $prodis = DB::table('prodi')->pluck('id')->toArray();
        foreach ($prodis as $pId) {
            $this->syncProdi($pId, 'all', 'all');
        }

        // 4. Sync Fakultas
        $fakultas = DB::table('fakultas')->get();
        foreach ($fakultas as $fak) {
            $this->syncFakultas($fak->id, $fak->id_universitas, 'all', 'all');
        }

        Log::info('Visualisasi OBE synchronization completed successfully for 5 menus.');
        return true;
    }
}
