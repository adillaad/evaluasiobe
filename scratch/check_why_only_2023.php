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

$user = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Penjamin Mutu Fakultas');
})->first();

$fakultasId = $user->id_fakultasUser ?? 1;
$prodis = Prodi::where('id_fakultas', $fakultasId)->get();
$prodiIds = $prodis->pluck('id')->toArray();

echo "Fakultas: {$fakultasId}, Prodis: " . implode(',', $prodiIds) . "\n\n";

// Check distinct 'tahun' raw values in mutus table
$mutusTahun = DB::table('mutus')
    ->whereIn('id_prodi', $prodiIds)
    ->select('tahun', 'angkatan', DB::raw('count(*) as total'), DB::raw('avg(Nilai) as avg_nilai'))
    ->groupBy('tahun', 'angkatan')
    ->get();

echo "Raw 'tahun' and 'angkatan' in mutus:\n";
foreach ($mutusTahun as $m) {
    echo "  - [tahun: {$m->tahun}, angkatan: {$m->angkatan}] => {$m->total} records, avg Nilai: {$m->avg_nilai}\n";
}

echo "\nCheck scores per year in mutus using getFakultasCplAnalytics:\n";
$dash = new \App\Http\Controllers\PenjaminMutu\DashboardController();
foreach (['2021', '2022', '2023', '2024', '2025', 'all'] as $yr) {
    $res = $dash->getFakultasCplAnalytics($fakultasId, $yr, 'all');
    $withData = $res['summary']['active_with_data_count'];
    $facultyAvg = $res['summary']['faculty_avg_skor'];
    echo "  - Year '{$yr}': active with data = {$withData}, faculty avg = {$facultyAvg}\n";
    foreach ($res['prodi_stats'] as $ps) {
        if ($ps['avg_skor_cpl'] > 0 || $ps['avg_capaian_cpl'] > 0) {
            echo "      * Prodi {$ps['nama']}: skor={$ps['avg_skor_cpl']}, mhs={$ps['total_mhs']}, cpls=" . count($ps['cpl_details']) . "\n";
        }
    }
}

// Check all distinct years in mutus table across the entire database
$allMutusYears = DB::table('mutus')
    ->select('tahun', DB::raw('count(*) as total'))
    ->groupBy('tahun')
    ->get();
echo "\nAll mutus in DB:\n";
foreach ($allMutusYears as $m) {
    echo "  - {$m->tahun} ({$m->total})\n";
}
