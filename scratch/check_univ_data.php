<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;

echo "=== CHECK UNIVERSITAS & FAKULTAS DATA ===" . PHP_EOL;

$univs = Universitas::all();
echo "Universitas count: " . $univs->count() . PHP_EOL;
foreach ($univs as $u) {
    echo "ID: {$u->id}, Nama: {$u->nama}" . PHP_EOL;
}

$fakultas = Fakultas::all();
echo PHP_EOL . "Fakultas count: " . $fakultas->count() . PHP_EOL;
foreach ($fakultas as $f) {
    $prodiCount = Prodi::where('id_fakultas', $f->id)->count();
    echo "ID: {$f->id}, Nama: {$f->nama}, ID Univ: {$f->id_univ}, Total Prodi: {$prodiCount}" . PHP_EOL;
}

$univUsers = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'like', '%Universitas%');
})->get();

echo PHP_EOL . "Universitas Users count: " . $univUsers->count() . PHP_EOL;
foreach ($univUsers as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Otoritas: " . ($u->otoritas->otoritas ?? '-') . ", Univ ID: {$u->id_universitasUser}" . PHP_EOL;
}
