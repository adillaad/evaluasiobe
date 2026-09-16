<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenjaminMutu\DashboardController;
use App\Models\Prodi;

$dc = new DashboardController();

echo "=== TEST MONOTONIC CUMULATIVE CALCULATION ===" . PHP_EOL;

$startYear = 2022;
$years = ['2022', '2023', '2024', '2025', '2026'];

$runningMaxSkor = 0;
foreach ($years as $idx => $yr) {
    $offset = (int)$yr - $startYear;
    $maxSem = min(8, ($offset + 1) * 2);
    $cumSemesters = range(1, $maxSem);

    $yrData = $dc->getFakultasCplAnalytics(6, 'all', $cumSemesters);
    $p = collect($yrData['prodi_stats'])->firstWhere('id', 11);
    $calcSkor = $p ? (float)$p['avg_skor_cpl'] : 0;
    $runningMaxSkor = max($runningMaxSkor, $calcSkor);

    echo "Tahun {$yr} (Sem 1-{$maxSem}): Calc = {$calcSkor}, Monotonic Running Max = {$runningMaxSkor}" . PHP_EOL;
}
