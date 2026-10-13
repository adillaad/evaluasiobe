<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$targetDate = '2026-10-12';

// Cek kolom tabel cpmks
echo "=== Kolom tabel cpmks ===\n";
$cols = DB::select('SHOW COLUMNS FROM cpmks');
foreach ($cols as $c) {
    echo "  " . $c->Field . " | " . $c->Type . "\n";
}

// Cek kolom tabel cpl_cpmk
echo "\n=== Kolom tabel cpl_cpmk ===\n";
$cols2 = DB::select('SHOW COLUMNS FROM cpl_cpmk');
foreach ($cols2 as $c) {
    echo "  " . $c->Field . " | " . $c->Type . "\n";
}
