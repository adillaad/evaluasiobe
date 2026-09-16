<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;

function getStudentAcademicBreakdown(Mahasiswa $mhs) {
    $prodi = Prodi::find($mhs->id_prodi);
    $isAptikom = (bool)($prodi->is_aptikom ?? false);
    $baseAngkatan = (int)$mhs->angkatan > 1900 ? (int)$mhs->angkatan : (int)substr($mhs->NPM, 0, 2) + 2000;

    // Get all records with course, semester, tahun_ajaran
    $allMutus = DB::table('mutus as m')
        ->leftJoin('mks', 'm.Course', '=', 'mks.kode')
        ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
        ->where(function($q) use ($mhs) {
            $q->where('m.npm', $mhs->NPM)->orWhere('m.NPM', $mhs->NPM);
        })
        ->select(
            'm.Course',
            'mks.nama as nama_mk',
            'mks.semester as mk_semester',
            DB::raw('(mks.bobot_teori + mks.bobot_praktikum) as sks'),
            'm.tahun_ajaran_id',
            'ta.tahun as ta_tahun',
            'ta.jenis_semester as ta_jenis_semester',
            'm.tahun as mutu_tahun'
        )
        ->distinct()
        ->get();

    if ($allMutus->isEmpty()) {
        return ['semesters' => [], 'summary' => ['total_sks' => 0, 'ipk' => 0]];
    }

    // Group courses by semester/academic year
    // Determine academic period label for each course
    $coursesByPeriod = [];
    $allCourseKodes = $allMutus->pluck('Course')->unique()->toArray();

    $gradeCache = [];
    foreach ($allCourseKodes as $cKode) {
        $finalGrade = $mhs->calculateFinalGrade($cKode, $isAptikom);
        [$letterGrade, $gradeWeight] = $prodi->convertGrade($finalGrade);
        $gradeCache[$cKode] = [
            'finalGrade' => $finalGrade,
            'letter' => $letterGrade ?? 'E',
            'weight' => (float)($gradeWeight ?? 0.0),
        ];
    }

    // Sort mutus by semester number
    $grouped = [];
    foreach ($allMutus as $row) {
        $semNum = (int)($row->mk_semester ?: 1);
        
        // Resolve Academic Year Label
        if (!empty($row->ta_tahun) && !empty($row->ta_jenis_semester)) {
            $nextY = (int)$row->ta_tahun + 1;
            $periodLabel = "{$row->ta_jenis_semester} - {$row->ta_tahun}/{$nextY}";
            $academicYear = "{$row->ta_tahun}/{$nextY}";
            $semesterType = $row->ta_jenis_semester;
        } elseif (!empty($row->mutu_tahun) && preg_match('/(Ganjil|Genap)\s*(\d{4})/i', $row->mutu_tahun, $m)) {
            $y = (int)$m[2];
            $nextY = $y + 1;
            $periodLabel = "{$m[1]} - {$y}/{$nextY}";
            $academicYear = "{$y}/{$nextY}";
            $semesterType = ucfirst(strtolower($m[1]));
        } else {
            // Estimate based on angkatan and semester number
            $yearOffset = (int)floor(($semNum - 1) / 2);
            $calYear = $baseAngkatan + $yearOffset;
            $nextYear = $calYear + 1;
            $semesterType = ($semNum % 2 === 1) ? 'Ganjil' : 'Genap';
            $periodLabel = "{$semesterType} - {$calYear}/{$nextYear}";
            $academicYear = "{$calYear}/{$nextYear}";
        }

        $key = "Sem_{$semNum}";
        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'semNumber' => $semNum,
                'periodLabel' => $periodLabel,
                'academicYear' => $academicYear,
                'semesterType' => $semesterType,
                'courses' => []
            ];
        }

        // Avoid duplicate courses in same semester
        if (!isset($grouped[$key]['courses'][$row->Course])) {
            $g = $gradeCache[$row->Course] ?? ['finalGrade' => 0, 'letter' => 'E', 'weight' => 0];
            $sks = (float)($row->sks ?: 0);
            $mutu = $sks * $g['weight'];
            $grouped[$key]['courses'][$row->Course] = [
                'kode' => $row->Course,
                'nama' => $row->nama_mk ?: $row->Course,
                'sks' => $sks,
                'nilai_akhir' => $g['finalGrade'],
                'nilai_huruf' => $g['letter'],
                'bobot' => $g['weight'],
                'mutu' => $mutu,
                'is_lulus' => $prodi->isLulus($g['finalGrade']),
            ];
        }
    }

    // Sort by semester number
    ksort($grouped, SORT_NATURAL);

    $cumulativeSks = 0;
    $cumulativeMutu = 0;
    $cumulativeSksLulus = 0;
    $resultSemesters = [];

    foreach ($grouped as $semKey => $semData) {
        $semSks = 0;
        $semMutu = 0;
        $semSksLulus = 0;

        foreach ($semData['courses'] as $c) {
            $semSks += $c['sks'];
            $semMutu += $c['mutu'];
            if ($c['is_lulus']) {
                $semSksLulus += $c['sks'];
            }
        }

        $ips = $semSks > 0 ? round($semMutu / $semSks, 2) : 0.0;

        $cumulativeSks += $semSks;
        $cumulativeMutu += $semMutu;
        $cumulativeSksLulus += $semSksLulus;
        $ipk = $cumulativeSks > 0 ? round($cumulativeMutu / $cumulativeSks, 2) : 0.0;

        $resultSemesters[] = [
            'semester_angka' => $semData['semNumber'],
            'semester_label' => "Semester {$semData['semNumber']}",
            'periode_label' => $semData['periodLabel'], // contoh: "Genap - 2022/2023"
            'tahun_akademik' => $semData['academicYear'],
            'jenis_semester' => $semData['semesterType'],
            'sks_semester' => $semSks,
            'sks_lulus_semester' => $semSksLulus,
            'bobot_mutu_semester' => round($semMutu, 2),
            'ips' => $ips,
            'sks_kumulatif' => $cumulativeSks,
            'sks_lulus_kumulatif' => $cumulativeSksLulus,
            'bobot_mutu_kumulatif' => round($cumulativeMutu, 2),
            'ipk_kumulatif' => $ipk,
            'courses' => array_values($semData['courses']),
        ];
    }

    return [
        'semesters' => $resultSemesters,
        'summary' => [
            'total_sks' => $cumulativeSks,
            'total_sks_lulus' => $cumulativeSksLulus,
            'ipk_akhir' => !empty($resultSemesters) ? end($resultSemesters)['ipk_kumulatif'] : 0.0,
            'ips_terakhir' => !empty($resultSemesters) ? end($resultSemesters)['ips'] : 0.0,
        ]
    ];
}

$haikal = Mahasiswa::where('NPM', '2118051001')->first();
$res = getStudentAcademicBreakdown($haikal);
echo "=== BREAKDOWN HAIKAL ===\n";
echo json_encode($res, JSON_PRETTY_PRINT);
