<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TABLES IN DB ===\n";
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tblName = array_values((array)$table)[0];
    if (strpos($tblName, 'cpl') !== false || strpos($tblName, 'cpmk') !== false || strpos($tblName, 'mk') !== false) {
        echo "- " . $tblName . "\n";
    }
}

echo "\n=== MKS MATCHING Second Language Acquisition ===\n";
$mks = DB::table('mks')->where('nama', 'LIKE', '%Second Language%')->get();
foreach ($mks as $mk) {
    echo "Kode: {$mk->kode} | Nama: {$mk->nama} | Prodi: {$mk->id_prodi} | Kurikulum: {$mk->id_kurikulum}\n";
}

echo "\n=== CPMK_MK / RELASI MK DENGAN CPMK ===\n";
// Let's check table cpmk_mk or similar
$hasCpmkMk = Schema::hasTable('cpmk_mk');
$hasMkCpmk = Schema::hasTable('mk_cpmk');
echo "Has cpmk_mk: " . ($hasCpmkMk ? 'YES' : 'NO') . " | Has mk_cpmk: " . ($hasMkCpmk ? 'YES' : 'NO') . "\n";

if ($hasCpmkMk) {
    $pivotCols = DB::table('cpmk_mk')->first();
    echo "cpmk_mk columns: " . implode(', ', array_keys((array)$pivotCols)) . "\n";
    foreach ($mks as $mk) {
        echo "MK Kode: {$mk->kode}\n";
        $pivots = DB::table('cpmk_mk')->where('mk_kode', $mk->kode)->orWhere('kode_mk', $mk->kode)->get();
        if ($pivots->isEmpty()) {
            // maybe by other col
            $pivots = DB::table('cpmk_mk')->get();
        }
    }
}
