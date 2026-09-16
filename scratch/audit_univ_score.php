<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Http\Controllers\PenjaminMutu\DashboardController;

$controller = new DashboardController();

$universitasList = Universitas::all();
echo "=== UNIVERSITAS IN DATABASE ===\n";
foreach ($universitasList as $u) {
    echo "ID: {$u->id}, Nama: {$u->nama}\n";
    $analytics = $controller->getUniversitasCplAnalytics($u->id);
    if ($analytics) {
        echo "  - Summary:\n";
        echo "    * Total Fakultas: " . $analytics['summary']['total_fakultas_count'] . "\n";
        echo "    * Total Prodi: " . $analytics['summary']['total_prodi_count'] . "\n";
        echo "    * Total Mahasiswa Evaluated: " . $analytics['summary']['total_mhs_evaluated'] . "\n";
        echo "    * Univ Avg Skor: " . $analytics['summary']['univ_avg_skor'] . " / 100\n";
        echo "    * Univ Avg Capaian: " . $analytics['summary']['univ_avg_capaian'] . "%\n";
        echo "  - Breakdown per Fakultas:\n";
        foreach ($analytics['fakultas_stats'] as $f) {
            echo "    * Fakultas: {$f['nama']} (ID: {$f['id']})\n";
            echo "      - Avg Skor: {$f['avg_skor_cpl']}, Avg Capaian: {$f['avg_capaian_cpl']}%\n";
            echo "      - Total Prodi: {$f['total_prodi']}, Total Mhs: {$f['total_mhs']}\n";
            echo "      - Prodi List:\n";
            foreach ($f['prodi_stats'] as $p) {
                echo "        > [{$p['id']}] {$p['nama']} ({$p['jenjang']}) | Skor: {$p['avg_skor_cpl']} | Capaian: {$p['avg_capaian_cpl']}% | Mhs: {$p['total_mhs']} | Total CPL: {$p['total_cpl']}\n";
            }
        }
    }
    echo "\n";
}
