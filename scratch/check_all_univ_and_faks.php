<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;

$univs = Universitas::all();
echo "Total Universitas in DB: " . $univs->count() . "\n";
foreach ($univs as $u) {
    echo "Univ [{$u->id}] {$u->nama}\n";
    $faks = Fakultas::where('id_universitas', $u->id)->get();
    echo "  Total Fakultas: " . $faks->count() . "\n";
    foreach ($faks as $f) {
        $prodis = Prodi::where('id_fakultas', $f->id)->get();
        echo "    - Fak [{$f->id}] {$f->nama} (Prodi: {$prodis->count()})\n";
    }
}

$allFaks = Fakultas::all();
echo "\nTotal All Fakultas in DB: " . $allFaks->count() . "\n";
foreach ($allFaks as $f) {
    echo "Fak [{$f->id}] {$f->nama} (id_universitas: {$f->id_universitas})\n";
}
