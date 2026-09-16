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

$fakultasId = 1;
$prodis = Prodi::where('id_fakultas', $fakultasId)->get();
$prodiIds = $prodis->pluck('id')->toArray();

$mutusYears = DB::table('mutus')
    ->whereIn('id_prodi', $prodiIds)
    ->select(DB::raw("DISTINCT SUBSTRING_INDEX(tahun, ' ', -1) as yr"))
    ->pluck('yr')
    ->filter(fn($y) => is_numeric($y) && (int)$y >= 2020)
    ->map(fn($y) => (string)$y)
    ->toArray();

$taYears = DB::table('tahun_ajaran')
    ->select('tahun')
    ->distinct()
    ->pluck('tahun')
    ->filter(fn($y) => is_numeric($y) && (int)$y >= 2021 && (int)$y <= (int)date('Y'))
    ->map(fn($y) => (string)$y)
    ->toArray();

$availableYears = array_values(array_unique(array_merge($mutusYears, $taYears)));
sort($availableYears);

echo "Computed Available Years: " . json_encode($availableYears) . "\n\n";

$dash = new \App\Http\Controllers\PenjaminMutu\DashboardController();
foreach ($prodis as $p) {
    echo "PRODI: {$p->nama}\n";
    foreach ($availableYears as $yr) {
        $yrData = $dash->getFakultasCplAnalytics($fakultasId, $yr, 'all');
        $prodiYr = collect($yrData['prodi_stats'])->firstWhere('id', $p->id);
        $skor = $prodiYr ? $prodiYr['avg_skor_cpl'] : 0;
        echo "   - Tahun {$yr}: Skor = {$skor}\n";
    }
}
