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

echo "=== TEST ALL UNIVERSITIES ===" . PHP_EOL;

foreach (Universitas::all() as $u) {
    echo "--- Univ: {$u->nama} (ID {$u->id}) ---" . PHP_EOL;
    $fakultas = Fakultas::where('id_universitas', $u->id)->get();
    foreach ($fakultas as $f) {
        $fData = $dc->getFakultasCplAnalytics($f->id, 'all', 'all');
        echo "   Fakultas: {$f->nama} (ID {$f->id}) | Prodi: " . count($fData['prodi_stats']) . " | Mhs: {$fData['summary']['total_mhs_evaluated']} | Skor: {$fData['summary']['faculty_avg_skor']}" . PHP_EOL;
    }
}
