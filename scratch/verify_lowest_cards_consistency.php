<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$viewsToTest = [
    'penjamin-mutu.visualisasi.indexVisualisasi',
    'dosen.visualisasi.indexVisualisasi',
    'penjamin-mutu.visualisasi.indexVisualisasiAngkatan',
    'dosen.visualisasi.indexVisualisasiAngkatan',
    'penjamin-mutu.visualisasi.indexVisualisasiMataKuliah',
    'dosen.visualisasi.indexVisualisasiMataKuliah',
    'penjamin-mutu.visualisasi.hasilVisualisasiCpmkMahasiswa',
    'dosen.visualisasi.hasilVisualisasiCpmkMahasiswa',
    'penjamin-mutu.visualisasi.hasilVisualisasiCpmkAngkatan',
    'dosen.visualisasi.hasilVisualisasiCpmkAngkatan',
    'pdf.reportVisualisasiMahasiswa',
    'pdf.reportVisualisasiAngkatan',
    'pdf.reportVisualisasiMataKuliah',
    'pdf.reportVisualisasiCPMKMahasiswa',
    'pdf.reportVisualisasiCPMKAngkatan',
];

echo "=== TESTING BLADE COMPILATION FOR ALL VISUALISASI VIEWS ===\n";

$blade = app('view');
$errors = 0;

foreach ($viewsToTest as $v) {
    try {
        $path = $blade->getFinder()->find($v);
        $compiled = app('blade.compiler')->compileString(file_get_contents($path));
        echo "✓ View [{$v}] compiled successfully.\n";
    } catch (\Throwable $e) {
        echo "✗ View [{$v}] FAILED: " . $e->getMessage() . "\n";
        $errors++;
    }
}

if ($errors === 0) {
    echo "\n>>> ALL " . count($viewsToTest) . " VIEWS COMPILED SUCCESSFULLY! <<<\n";
} else {
    echo "\n>>> {$errors} VIEWS HAD ERRORS <<<\n";
}
