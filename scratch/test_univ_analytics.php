<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Http\Controllers\PenjaminMutu\DashboardController;

$dc = new DashboardController();

echo "=== TEST UNIVERSITY CPL ANALYTICS (UNILA ID 57) ===" . PHP_EOL;

$univId = 57;
$fakultasList = Fakultas::where('id_universitas', $univId)->get();

$allFakultasStats = [];
$totalUnivMhs = 0;
$totalUnivProdi = 0;
$totalUnivCpl = 0;
$sumUnivSkor = 0;
$countUnivSkor = 0;
$sumUnivCapaian = 0;
$countUnivCapaian = 0;

foreach ($fakultasList as $fak) {
    $fakData = $dc->getFakultasCplAnalytics($fak->id, 'all', 'all');
    $pStats = $fakData['prodi_stats'] ?? [];
    
    $fakSkor = $fakData['summary']['faculty_avg_skor'] ?? 0;
    $fakCapaian = $fakData['summary']['faculty_avg_capaian'] ?? 0;
    $fakMhs = $fakData['summary']['total_mhs_evaluated'] ?? 0;
    $fakCpl = $fakData['summary']['total_cpl_count'] ?? 0;
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

    $allFakultasStats[] = [
        'id' => $fak->id,
        'nama' => $fak->nama,
        'total_prodi' => $prodiCount,
        'total_mhs' => $fakMhs,
        'total_cpl' => $fakCpl,
        'avg_skor' => $fakSkor,
        'avg_capaian' => $fakCapaian,
        'prodi_stats' => $pStats,
        'fakultas_data' => $fakData
    ];

    echo "Fakultas: {$fak->nama} | Prodi: {$prodiCount} | Mhs: {$fakMhs} | Skor: {$fakSkor} | Capaian: {$fakCapaian}%" . PHP_EOL;
}

$univAvgSkor = $countUnivSkor > 0 ? round($sumUnivSkor / $countUnivSkor, 1) : 0;
$univAvgCapaian = $countUnivCapaian > 0 ? round($sumUnivCapaian / $countUnivCapaian, 1) : 0;

echo PHP_EOL . "=== UNILA SUMMARY ===" . PHP_EOL;
echo "Total Fakultas: " . count($fakultasList) . PHP_EOL;
echo "Total Prodi: {$totalUnivProdi}" . PHP_EOL;
echo "Total Mhs: {$totalUnivMhs}" . PHP_EOL;
echo "Univ Avg Skor: {$univAvgSkor}" . PHP_EOL;
echo "Univ Avg Capaian: {$univAvgCapaian}%" . PHP_EOL;
