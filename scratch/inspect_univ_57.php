<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenjaminMutu\DashboardController;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;

$dc = new DashboardController();
$vc = new VisualisasiController();

$res = $dc->getUniversitasCplAnalytics(57);
echo "=== Univ 57 (Universitas Lampung) ===\n";
echo "Total Fakultas: " . count($res['faculty_stats']) . "\n";
foreach ($res['faculty_stats'] as $f) {
    echo "- Fak [{$f['id']}] {$f['nama']}: total_prodi={$f['total_prodi']}, total_mhs={$f['total_mhs']}, avg_skor={$f['avg_skor_cpl']}, avg_capaian={$f['avg_capaian_cpl']}\n";
    foreach ($f['prodi_stats'] as $p) {
        echo "    * Prodi [{$p['id']}] {$p['nama']}: total_mhs={$p['total_mhs']}, avg_skor={$p['avg_skor_cpl']}, avg_capaian={$p['avg_capaian_cpl']}\n";
    }
}
echo "\nSummary:\n";
print_r($res['summary']);
