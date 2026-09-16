<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenjaminMutu\DashboardController;
use App\Models\Prodi;

$dc = new DashboardController();

echo "=== TEST CUMULATIVE PROGRESSION ===" . PHP_EOL;

// 2022: Sem 1, 2
// 2023: Sem 1, 2, 3, 4
// 2024: Sem 1, 2, 3, 4, 5, 6
// 2025: Sem 1, 2, 3, 4, 5, 6, 7, 8

$baseAngkatan = 2022;
$years = ['2022', '2023', '2024', '2025', '2026'];

foreach ($years as $i => $yr) {
    // If cumulative semesters:
    $offset = (int)$yr - $baseAngkatan;
    $maxSem = ($offset + 1) * 2;
    $semesters = range(1, $maxSem);
    
    // We can call getFakultasCplAnalytics with custom semesters or compute
    echo "Year {$yr} (Cum Sem 1-{$maxSem}): ";
    // Let's see
}
