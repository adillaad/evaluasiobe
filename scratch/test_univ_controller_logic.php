<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Http\Controllers\PenjaminMutu\DashboardController;

$dashboardController = new DashboardController();
$universitasId = 57; // Unila

echo "=== TEST EXTENDED UNIVERSITAS ANALYTICS ===" . PHP_EOL;

$universitas = Universitas::find($universitasId);
$fakultasList = Fakultas::where('id_universitas', $universitasId)->get();
$allProdiIds = Prodi::whereIn('id_fakultas', $fakultasList->pluck('id'))->pluck('id')->toArray();

// 1. Available Years across university
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

echo "Available Years: " . implode(', ', $availableYears) . PHP_EOL;

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

    // Yearly progression per fakultas (monotonik & kumulatif)
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

$univAvgSkor = $countUnivSkor > 0 ? round($sumUnivSkor / $countUnivSkor, 1) : 0.0;
$univAvgCapaian = $countUnivCapaian > 0 ? round($sumUnivCapaian / $countUnivCapaian, 1) : 0.0;

$yearlyAverages = [];
foreach ($availableYears as $yr) {
    $avgSkor = $yearlyTotals[$yr]['count_skor'] > 0 ? round($yearlyTotals[$yr]['sum_skor'] / $yearlyTotals[$yr]['count_skor'], 1) : 0;
    $avgCapaian = $yearlyTotals[$yr]['count_capaian'] > 0 ? round($yearlyTotals[$yr]['sum_capaian'] / $yearlyTotals[$yr]['count_capaian'], 1) : 0;
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

echo PHP_EOL . "=== SUMMARY ===" . PHP_EOL;
print_r($universitasSummary);

echo PHP_EOL . "=== YEARLY PROGRESSION ===" . PHP_EOL;
foreach ($yearlyProgression as $yp) {
    echo "Fakultas: {$yp['nama']} | Overall: {$yp['overall_skor']} | Scores: " . json_encode($yp['scores']) . PHP_EOL;
}
