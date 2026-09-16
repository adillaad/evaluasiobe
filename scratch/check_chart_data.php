<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Mutu;
use Illuminate\Support\Facades\DB;

$dash = new \App\Http\Controllers\PenjaminMutu\DashboardController();
$data = $dash->getFakultasCplAnalytics(1, 'all', 'all');

echo "Chart Data in fakultasCplData:\n";
print_r($data['chart_data']);

echo "\nProdi Stats:\n";
foreach ($data['prodi_stats'] as $ps) {
    echo "Prodi: {$ps['nama']}, Skor: {$ps['avg_skor_cpl']}, Capaian: {$ps['avg_capaian_cpl']}\n";
}
