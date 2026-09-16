<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Mahasiswa;

echo "=== USER DENGAN OTORITAS PENJAMIN MUTU FAKULTAS ===" . PHP_EOL;
$users = User::with('otoritas', 'fakultas')->whereHas('otoritas', function($q) {
    $q->where('otoritas', 'LIKE', '%Fakultas%');
})->get();

foreach ($users as $u) {
    $fName = $u->fakultas->nama ?? 'NULL';
    echo "ID: {$u->id} | Name: {$u->name} | Username: {$u->username} | Role: {$u->otoritas->otoritas} | id_fakultasUser: {$u->id_fakultasUser} ({$fName})" . PHP_EOL;
}

echo PHP_EOL . "=== PRODIS PER FAKULTAS ===" . PHP_EOL;
$faks = Fakultas::with('prodis')->get();
foreach ($faks as $f) {
    echo "Fakultas ID: {$f->id} | {$f->nama}" . PHP_EOL;
    foreach ($f->prodis as $p) {
        $mhsCount = Mahasiswa::where('id_prodi', $p->id)->count();
        echo "   - Prodi ID: {$p->id} | {$p->nama} ({$p->jenjang}) | Total Mhs: {$mhsCount}" . PHP_EOL;
    }
}
