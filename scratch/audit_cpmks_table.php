<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CPMKS SCHEMA ===\n";
print_r(Schema::getColumnListing('cpmks'));

echo "\n=== CPLMKS SCHEMA ===\n";
print_r(Schema::getColumnListing('cplmks'));

echo "\n=== CPL_MK_CPMK_PENILAIAN SCHEMA ===\n";
print_r(Schema::getColumnListing('cpl_mk_cpmk_penilaian'));

echo "\n=== CPMKS with IDs 1120-1129 ===\n";
$cpmkList = DB::table('cpmks')->whereIn('id', [1120, 1121, 1122, 1123, 1124, 1125, 1126, 1127, 1128, 1129])->get();
print_r($cpmkList->toArray());

echo "\n=== ALL CPLMKS FOR PRODI 17 ===\n";
$cplmks = DB::table('cplmks')->get();
$pbiCplmks = $cplmks->filter(function($c) {
    return strpos($c->kode_mk ?? '', 'PBI') !== false || strpos($c->course ?? '', 'PBI') !== false;
});
echo "PBI cplmks: " . $pbiCplmks->count() . "\n";
print_r($pbiCplmks->toArray());

echo "\n=== ALL CPL_MK_CPMK_PENILAIAN FOR PRODI 17 ===\n";
$penilaian = DB::table('cpl_mk_cpmk_penilaian')->get();
$pbiPenilaian = $penilaian->filter(function($c) {
    return strpos($c->kode_mk ?? '', 'PBI') !== false;
});
echo "PBI penilaian count: " . $pbiPenilaian->count() . "\n";
print_r($pbiPenilaian->toArray());
