<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenjaminMutu\DashboardController;
use App\Models\Prodi;

$dc = new DashboardController();

echo "=== MONOTONIC CUMULATIVE SIMULATION ===" . PHP_EOL;

$baseAngkatan = 2022;
$years = ['2022', '2023', '2024', '2025', '2026'];

$semestersCum = [];
foreach ($years as $idx => $yr) {
    $offset = (int)$yr - $baseAngkatan;
    $maxSem = min(8, ($offset + 1) * 2);
    $semList = range(1, $maxSem);
    
    // Test with semesters
    $data = $dc->getFakultasCplAnalytics(6, 'all', 'all'); // Let's see how passing semesters array works if we support it or calculate
}
