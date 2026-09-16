<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;

$univ = Universitas::first();
echo "Universitas: {$univ->id} - {$univ->nama}\n";

$fakultas = Fakultas::where('id_universitas', $univ->id)->get();
echo "Total Fakultas in DB for Univ {$univ->id}: " . $fakultas->count() . "\n";
foreach ($fakultas as $f) {
    $prodis = Prodi::where('id_fakultas', $f->id)->get();
    echo "  - Fakultas [{$f->id}] {$f->nama} (Total Prodi: {$prodis->count()})\n";
    foreach ($prodis as $p) {
        $mhsCount = DB::table('mahasiswa')->where('id_prodi', $p->id)->count();
        $mutuCount = DB::table('mutus')->where('id_prodi', $p->id)->count();
        echo "      * Prodi [{$p->id}] {$p->nama} (Mhs: {$mhsCount}, Mutus: {$mutuCount})\n";
    }
}
