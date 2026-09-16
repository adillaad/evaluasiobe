<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Mutu;
use App\Http\Controllers\PenjaminMutu\DashboardController;
use Illuminate\Support\Facades\DB;

$fakultasId = 1;
$dashboardController = new DashboardController();

// 1. Get base fakultas CPL analytics
$fakultasCplData = $dashboardController->getFakultasCplAnalytics($fakultasId, 'all', 'all');
echo "Base analytics computed for: " . ($fakultasCplData['fakultas']->nama ?? 'N/A') . "\n";
echo "Total Prodis: " . count($fakultasCplData['prodi_stats']) . "\n";

// 2. Compute Yearly Progression per Prodi
$prodiIds = collect($fakultasCplData['prodi_stats'])->pluck('id')->toArray();

// Get distinct available years across mutus
$availableYears = DB::table('mutus')
    ->whereIn('id_prodi', $prodiIds)
    ->select(DB::raw("DISTINCT SUBSTRING_INDEX(tahun, ' ', -1) as yr"))
    ->pluck('yr')
    ->filter(function($y) { return is_numeric($y) && (int)$y >= 2020; })
    ->map(function($y) { return (string)$y; })
    ->sort()
    ->values()
    ->toArray();

if (empty($availableYears)) {
    $availableYears = ['2022', '2023', '2024'];
}

echo "Available Years: " . json_encode($availableYears) . "\n";

// Calculate score per prodi per year
$yearlyProgression = [];
$yearlyTotals = array_fill_keys($availableYears, ['sum_skor' => 0, 'count_skor' => 0, 'sum_capaian' => 0, 'count_capaian' => 0]);

foreach ($fakultasCplData['prodi_stats'] as $prodi) {
    $pId = $prodi['id'];
    $pScores = [];
    $pCapaians = [];
    $prevYearVal = null;
    $trend = 'stable';

    foreach ($availableYears as $yr) {
        $yrData = $dashboardController->getFakultasCplAnalytics($fakultasId, $yr, 'all');
        $prodiYr = collect($yrData['prodi_stats'])->firstWhere('id', $pId);
        
        $skorYr = $prodiYr ? (float)$prodiYr['avg_skor_cpl'] : 0;
        $capaianYr = $prodiYr ? (float)$prodiYr['avg_capaian_cpl'] : 0;

        $pScores[$yr] = $skorYr;
        $pCapaians[$yr] = $capaianYr;

        if ($skorYr > 0) {
            $yearlyTotals[$yr]['sum_skor'] += $skorYr;
            $yearlyTotals[$yr]['count_skor']++;
        }
        if ($capaianYr > 0) {
            $yearlyTotals[$yr]['sum_capaian'] += $capaianYr;
            $yearlyTotals[$yr]['count_capaian']++;
        }
    }

    // Determine trend from first active year to latest active year
    $activeYearScores = array_filter($pScores, function($v) { return $v > 0; });
    if (count($activeYearScores) >= 2) {
        $firstScore = reset($activeYearScores);
        $lastScore = end($activeYearScores);
        if ($lastScore > $firstScore + 1.0) $trend = 'up';
        elseif ($lastScore < $firstScore - 1.0) $trend = 'down';
    }

    $yearlyProgression[] = [
        'id' => $pId,
        'nama' => $prodi['nama'],
        'jenjang' => $prodi['jenjang'],
        'is_aptikom' => $prodi['is_aptikom'],
        'scores' => $pScores,
        'capaians' => $pCapaians,
        'overall_skor' => $prodi['avg_skor_cpl'],
        'overall_capaian' => $prodi['avg_capaian_cpl'],
        'trend' => $trend
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

echo "\nYearly Progression computed for " . count($yearlyProgression) . " prodis.\n";
echo "Yearly Averages: " . json_encode($yearlyAverages) . "\n";

// 3. Compute 4 Aspek CPL Analytics (Sikap, Pengetahuan, Keterampilan Umum, Keterampilan Khusus)
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
        'avg_capaian' => $avgCapaian,
        'status' => $avgSkor >= 65 ? 'Memenuhi Standar' : ($avgSkor > 0 ? 'Perlu Peningkatan' : 'Belum Ada Nilai')
    ];
}

echo "\nAspek Analytics:\n";
print_r($aspekAnalytics);

// 4. Questions with lowest CPL across faculty prodis
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

echo "\nLowest Questions Count: " . count($soalTerendah) . "\n";
print_r($soalTerendah);
