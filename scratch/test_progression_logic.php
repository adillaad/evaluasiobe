<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenjaminMutu\DashboardController;
use App\Models\Prodi;

$dc = new DashboardController();
$fmipaProdis = Prodi::where('id_fakultas', 6)->get();

echo "=== CUMULATIVE VS PERIODIC YEARLY PROGRESSION ===" . PHP_EOL;

// Test standard full
$full = $dc->getFakultasCplAnalytics(6, 'all', 'all');
echo "Full Overall Skor S1 Ilkom: " . ($full['prodi_stats'][0]['avg_skor_cpl'] ?? 0) . PHP_EOL;

// Test years 2022, 2023, 2024, 2025, 2026
$years = ['2022', '2023', '2024', '2025', '2026'];
foreach ($years as $yr) {
    $data = $dc->getFakultasCplAnalytics(6, $yr, 'all');
    $skor = $data['prodi_stats'][0]['avg_skor_cpl'] ?? 0;
    echo "Single Year {$yr} (semesters): Skor = {$skor}" . PHP_EOL;
}
