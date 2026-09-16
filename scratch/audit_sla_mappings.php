<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. CHECK cpmk_mk TABLE ===\n";
$cpmkMk = DB::table('cpmk_mk')->get();
echo "Total records in cpmk_mk: " . $cpmkMk->count() . "\n";

$pbiCpmkMk = $cpmkMk->filter(function($r) {
    return strpos($r->mk_kode, 'PBI') !== false || strpos($r->mk_kode, '1ANP') !== false;
});

echo "PBI records in cpmk_mk: " . $pbiCpmkMk->count() . "\n";
foreach ($pbiCpmkMk as $r) {
    $cpmk = DB::table('cpmks')->where('id', $r->cpmk_id)->first();
    $cpl = $cpmk ? DB::table('cpls')->where('id', $cpmk->cpl_id)->first() : null;
    $mk = DB::table('mks')->where('kode', $r->mk_kode)->first();
    echo "MK: {$r->mk_kode} (" . ($mk->nama ?? 'none') . ") => CPMK ID {$r->cpmk_id} (" . ($cpmk->kode ?? 'none') . ") => CPL ID " . ($cpmk->cpl_id ?? 'none') . " (" . ($cpl->kode ?? 'none') . ")\n";
}

echo "\n=== 2. CHECK ALL MAPPINGS FOR SECOND LANGUAGE ACQUISITION IN ALL TABLES ===\n";
$slaMkList = DB::table('mks')->where('nama', 'LIKE', '%Second Language%')->get();
foreach ($slaMkList as $smk) {
    echo "\nMK Code: {$smk->kode}, Nama: {$smk->nama}, Prodi: {$smk->id_prodi}\n";
    
    // In cpmk_mk
    $cpmksInMk = DB::table('cpmk_mk')->where('mk_kode', $smk->kode)->get();
    echo " -> cpmk_mk count: " . $cpmksInMk->count() . "\n";
    foreach ($cpmksInMk as $cmk) {
        $cpmkObj = DB::table('cpmks')->where('id', $cmk->cpmk_id)->first();
        $cplObj = $cpmkObj ? DB::table('cpls')->where('id', $cpmkObj->cpl_id)->first() : null;
        echo "    * CPMK: " . ($cpmkObj->kode ?? $cmk->cpmk_id) . " ({$cpmkObj->judul}) -> CPL: " . ($cplObj->kode ?? $cpmkObj->cpl_id) . " (ID: " . ($cpmkObj->cpl_id ?? 'none') . ")\n";
    }

    // In mk_cpl
    $mkCpls = DB::table('mk_cpl')->where('mk_kode', $smk->kode)->get();
    echo " -> mk_cpl count: " . $mkCpls->count() . "\n";
    foreach ($mkCpls as $mc) {
        $cplObj = DB::table('cpls')->where('id', $mc->cpl_id)->first();
        echo "    * CPL: " . ($cplObj->kode ?? $mc->cpl_id) . " (ID: {$mc->cpl_id})\n";
    }
}
