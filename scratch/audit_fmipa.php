<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Mahasiswa;
use App\Models\Mk;
use App\Models\Cpl;
use App\Http\Controllers\PenjaminMutu\DashboardController;

echo "=== FAKULTAS 6: FMIPA PRODIS ===" . PHP_EOL;
$prodis = Prodi::where('id_fakultas', 6)->get();
foreach ($prodis as $p) {
    $mhsCount = Mahasiswa::where('id_prodi', $p->id)->count();
    $angList = Mahasiswa::where('id_prodi', $p->id)->distinct()->pluck('angkatan')->toArray();
    sort($angList);
    $cpls = Cpl::where('id_prodi', $p->id)->count();
    $mks = Mk::where('id_prodi', $p->id)->count();
    echo "Prodi ID: {$p->id} | {$p->nama} ({$p->jenjang}) | Total Mhs: {$mhsCount} (Angkatan: " . implode(', ', $angList) . ") | CPL: {$cpls} | MK: {$mks}" . PHP_EOL;
}

echo PHP_EOL . "=== FMIPA ANALYTICS ===" . PHP_EOL;
$dc = new DashboardController();
$analytics = $dc->getFakultasCplAnalytics(6);

echo "Summary:" . PHP_EOL;
print_r($analytics['summary']);

echo PHP_EOL . "Prodi Stats in FMIPA:" . PHP_EOL;
foreach ($analytics['prodi_stats'] as $ps) {
    echo "Prodi ID: {$ps['id']} | {$ps['nama']} ({$ps['jenjang']}) | Mhs: {$ps['total_mhs']} | CPL: {$ps['total_cpl']} | Skor: {$ps['avg_skor_cpl']} | Capaian: {$ps['avg_capaian_cpl']}" . PHP_EOL;
    echo "  - CPL count: " . count($ps['cpl_details']) . PHP_EOL;
}
