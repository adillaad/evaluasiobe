<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ilkomProdi = \DB::table('prodi')->where('nama', 'LIKE', '%Ilmu Komputer%')->get();
foreach ($ilkomProdi as $p) {
    echo "PRODI ILKOM ID: {$p->id}, NAMA: {$p->nama}, FAKULTAS ID: {$p->id_fakultas}\n";
    $fak = \DB::table('fakultas')->where('id', $p->id_fakultas)->first();
    echo "  FAKULTAS: {$fak->nama}, UNIV ID: {$fak->id_universitas}\n";
}
