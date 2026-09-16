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
echo "Fakultas ID: {$fakultasId}\n";

$prodis = Prodi::where('id_fakultas', $fakultasId)->get();
echo "Total Prodis: " . count($prodis) . "\n";

// Check distinct years in mutus for these prodis
$prodiIds = $prodis->pluck('id')->toArray();
$yearsInMutus = DB::table('mutus')
    ->whereIn('id_prodi', $prodiIds)
    ->select(DB::raw("DISTINCT SUBSTRING_INDEX(tahun, ' ', -1) as yr"))
    ->pluck('yr')
    ->filter()
    ->sort()
    ->values();

echo "Years in mutus: " . json_encode($yearsInMutus) . "\n";

// Check distinct angkatan in mutus
$angkatans = DB::table('mutus')
    ->whereIn('id_prodi', $prodiIds)
    ->whereNotNull('angkatan')
    ->where('angkatan', '!=', '')
    ->select('angkatan')
    ->distinct()
    ->pluck('angkatan')
    ->sort()
    ->values();

echo "Angkatans in mutus: " . json_encode($angkatans) . "\n";

// Check years in tahun_ajaran table
$taYears = DB::table('tahun_ajaran')
    ->select('tahun')
    ->distinct()
    ->pluck('tahun')
    ->sort()
    ->values();

echo "Tahun Ajaran table years: " . json_encode($taYears) . "\n";
