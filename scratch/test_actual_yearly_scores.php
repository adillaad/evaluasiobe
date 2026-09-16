<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Mutu;
use Illuminate\Support\Facades\DB;

$fakultasId = 1;
$prodis = Prodi::where('id_fakultas', $fakultasId)->get();

// Let's test computing CPL score for each year matching mutus.tahun or ta.tahun
$years = ['2021', '2022', '2023', '2024', '2025'];

foreach ($prodis as $prodi) {
    echo "PRODI: {$prodi->nama}\n";
    $cpls = DB::table('cpls')->where('id_prodi', $prodi->id)->get();
    if ($cpls->isEmpty()) {
        echo "   (No CPL)\n";
        continue;
    }

    $allNpm = DB::table('mutus')->where('id_prodi', $prodi->id)->whereNotNull('npm')->distinct()->pluck('npm')->toArray();
    if (empty($allNpm)) {
        echo "   (No Mahasiswa in mutus)\n";
        continue;
    }

    foreach ($years as $yr) {
        // Query score for this year
        $scoresPerNpm = [];
        foreach ($allNpm as $npm) {
            $sql = "
                SELECT q5.cpl, AVG(q5.hasil) as avg_score
                FROM (
                    SELECT q4.Cpl, q4.Course, SUM(q4.hasil) as hasil 
                    FROM (
                        SELECT q1.tahun, q1.Cpl, q1.Jenis, q1.Course, 
                            (q1.nilaiSoal * q1.BobotSoal / NULLIF(q2.BobotJenis, 0)) * q1.examWeight / NULLIF(q3.sumExamWeight, 0) as hasil 
                        FROM (
                            SELECT m.tahun, m.Cpl, m.Jenis, m.Course, m.nilaiSoal, m.examWeight, m.BobotSoal 
                            FROM mutus as m
                            LEFT JOIN tahun_ajaran as ta ON m.tahun_ajaran_id = ta.id
                            WHERE m.npm = :npm1 
                              AND m.id_prodi = :id_prodi1
                              AND (m.tahun LIKE :yr_like1 OR ta.tahun = :yr_exact1)
                        ) as q1
                        INNER JOIN (
                            SELECT m2.tahun, m2.Jenis, m2.Course, SUM(m2.BobotSoal) as BobotJenis 
                            FROM mutus as m2
                            LEFT JOIN tahun_ajaran as ta2 ON m2.tahun_ajaran_id = ta2.id
                            WHERE m2.npm = :npm2 
                              AND m2.id_prodi = :id_prodi2
                              AND (m2.tahun LIKE :yr_like2 OR ta2.tahun = :yr_exact2)
                            GROUP BY m2.tahun, m2.Jenis, m2.Course
                        ) as q2 ON q1.Jenis = q2.Jenis AND q1.Course = q2.Course AND q1.tahun = q2.tahun
                        INNER JOIN (
                            SELECT m3.tahun, m3.Course, SUM(DISTINCT m3.examWeight) as sumExamWeight 
                            FROM mutus as m3
                            LEFT JOIN tahun_ajaran as ta3 ON m3.tahun_ajaran_id = ta3.id
                            WHERE m3.npm = :npm3 
                              AND m3.id_prodi = :id_prodi3
                              AND (m3.tahun LIKE :yr_like3 OR ta3.tahun = :yr_exact3)
                            GROUP BY m3.tahun, m3.Course
                        ) as q3 ON q1.Course = q3.Course AND q1.tahun = q3.tahun
                        GROUP BY q1.tahun, q1.Cpl, q1.Jenis, q1.Course, q1.nilaiSoal, q1.BobotSoal, q2.BobotJenis, q1.examWeight, q3.sumExamWeight
                    ) as q4
                    GROUP BY q4.tahun, q4.Cpl, q4.Course
                ) as q5
                GROUP BY q5.cpl
            ";

            $res = DB::select($sql, [
                'npm1' => $npm, 'id_prodi1' => $prodi->id, 'yr_like1' => "%{$yr}%", 'yr_exact1' => $yr,
                'npm2' => $npm, 'id_prodi2' => $prodi->id, 'yr_like2' => "%{$yr}%", 'yr_exact2' => $yr,
                'npm3' => $npm, 'id_prodi3' => $prodi->id, 'yr_like3' => "%{$yr}%", 'yr_exact3' => $yr,
            ]);

            if (!empty($res)) {
                $avgN = collect($res)->avg('avg_score');
                $scoresPerNpm[] = $avgN;
            }
        }

        $avgProdiYr = count($scoresPerNpm) > 0 ? round(array_sum($scoresPerNpm) / count($scoresPerNpm), 1) : 0;
        echo "   - Tahun {$yr}: Skor CPL = {$avgProdiYr} (Evaluated for " . count($scoresPerNpm) . " mhs)\n";
    }
}
