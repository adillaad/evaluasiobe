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

$prodis = Prodi::where('id_fakultas', 1)->get();
$prodiIds = $prodis->pluck('id')->toArray();

// Let's check how mutus.tahun contains years
$yearsInMutus = DB::table('mutus')
    ->whereIn('id_prodi', $prodiIds)
    ->select('tahun')
    ->distinct()
    ->pluck('tahun');

echo "Mutus tahun in fakultas 1: " . json_encode($yearsInMutus) . "\n";

// Let's check all available years in tahun_ajaran table
$taYears = DB::table('tahun_ajaran')
    ->select('tahun')
    ->distinct()
    ->pluck('tahun')
    ->filter(fn($y) => is_numeric($y))
    ->sort()
    ->values()
    ->toArray();

echo "Tahun ajaran table years: " . json_encode($taYears) . "\n";

// Let's check mutus across all prodis in the entire DB for all faculties
$allFakultas = Fakultas::all();
foreach ($allFakultas as $f) {
    $fProdiIds = Prodi::where('id_fakultas', $f->id)->pluck('id')->toArray();
    $fYears = DB::table('mutus')
        ->whereIn('id_prodi', $fProdiIds)
        ->select('tahun')
        ->distinct()
        ->pluck('tahun');
    echo "Fakultas [{$f->nama}]: mutus tahun = " . json_encode($fYears) . "\n";
}
