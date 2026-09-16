<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Fakultas;
use App\Models\Prodi;

echo "=== FAKULTAS TABLE DATA ===" . PHP_EOL;
$all = Fakultas::all();
foreach ($all as $f) {
    echo "ID: {$f->id}, Nama: {$f->nama}, ID Univ: {$f->id_universitas}" . PHP_EOL;
}
