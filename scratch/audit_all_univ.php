<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use App\Http\Controllers\PenjaminMutu\DashboardController;

$vc = new VisualisasiController();
$dc = new DashboardController();

$univList = Universitas::all();
echo "=== ALL UNIVERSITAS IN DB ===\n";
foreach ($univList as $u) {
    echo "Universitas ID: {$u->id}, Nama: {$u->nama}\n";
    $ext = (new ReflectionClass($vc))->getMethod('getExtendedUniversitasAnalytics');
    $ext->setAccessible(true);
    $res = $ext->invoke($vc, $u->id);

    $uData = $res['universitasCplData'];
    if ($uData) {
        echo "Summary:\n";
        echo "  - Univ Avg Skor: " . $uData['summary']['univ_avg_skor'] . "\n";
        echo "  - Univ Avg Capaian: " . $uData['summary']['univ_avg_capaian'] . "%\n";
        echo "  - Total Fakultas: " . $uData['summary']['total_fakultas_count'] . "\n";
        echo "  - Total Prodi: " . $uData['summary']['total_prodi_count'] . "\n";
        echo "  - Total Mhs: " . $uData['summary']['total_mhs_evaluated'] . "\n";
        echo "  - Total CPL: " . $uData['summary']['total_cpl_count'] . "\n";
        echo "Fakultas List:\n";
        foreach ($uData['fakultas_stats'] as $f) {
            echo "  * Fakultas [{$f['id']}] {$f['nama']}: Avg Skor = {$f['avg_skor_cpl']}, Avg Capaian = {$f['avg_capaian_cpl']}%\n";
            foreach ($f['prodi_stats'] as $p) {
                echo "    > Prodi [{$p['id']}] {$p['nama']} ({$p['jenjang']}) | Skor: {$p['avg_skor_cpl']} | Capaian: {$p['avg_capaian_cpl']}% | Mhs: {$p['total_mhs']} | CPLs: {$p['total_cpl']}\n";
                foreach ($p['cpl_details'] as $c) {
                    if ($c['avg_skor'] > 0 || $c['avg_capaian'] > 0) {
                        echo "      - {$c['kode']} ({$c['aspek']}): Skor = {$c['avg_skor']}, Capaian = {$c['avg_capaian']}%\n";
                    }
                }
            }
        }
    }
    echo "\n----------------------------------------\n";
}
