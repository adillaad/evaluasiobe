<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== 1. CHECK CPMK TABLE FOR PBI250006 (Second Language Acquisition) ===\n";
$cpmks = DB::table('cpmk')->where('id_mk', 'PBI250006')->orWhere('kode', 'LIKE', '%PBI250006%')->orWhere('kode_mk', 'PBI250006')->get();
echo "Count: " . $cpmks->count() . "\n";
foreach ($cpmks as $cp) {
    print_r($cp);
}

echo "\n=== 2. CHECK ALL CPMK FOR PRODI 17 ===\n";
if (Schema::hasTable('cpmk')) {
    $allCpmk = DB::table('cpmk')->get();
    echo "Total CPMK in DB: " . $allCpmk->count() . "\n";
    $pbiCpmk = $allCpmk->filter(function($c) {
        return (isset($c->id_prodi) && $c->id_prodi == 17) || (isset($c->kode_mk) && strpos($c->kode_mk, 'PBI') !== false) || (isset($c->id_mk) && strpos($c->id_mk, 'PBI') !== false);
    });
    echo "PBI CPMK count: " . $pbiCpmk->count() . "\n";
    foreach ($pbiCpmk as $pc) {
        $cpl = DB::table('cpls')->where('id', $pc->id_cpl ?? 0)->first();
        echo "CPMK ID: {$pc->id}, Kode: {$pc->kode}, MK: " . ($pc->id_mk ?? $pc->kode_mk ?? '') . ", id_cpl: " . ($pc->id_cpl ?? '') . " (" . ($cpl->kode ?? 'no cpl') . "), Judul: {$pc->judul}\n";
    }
}

echo "\n=== 3. CHECK PEMETAAN / KURIKULUM TABLES ===\n";
$tables = ['pemetaan_cpl', 'cpl_mk', 'matrix_cpl', 'pemetaan_cpmk', 'kurikulums'];
foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        echo "Table '{$t}' exists, count: " . DB::table($t)->count() . "\n";
        $records = DB::table($t)->take(5)->get();
        print_r($records->toArray());
    }
}

echo "\n=== 4. CHECK CPMK IDs FROM MUTUS (1120-1129) ===\n";
$cpmkIds = [1120, 1121, 1122, 1123, 1124, 1125, 1126, 1127, 1128, 1129];
$matchedCpmks = DB::table('cpmk')->whereIn('id', $cpmkIds)->get();
foreach ($matchedCpmks as $mc) {
    $cpl = DB::table('cpls')->where('id', $mc->id_cpl ?? 0)->first();
    echo "ID: {$mc->id}, Kode: {$mc->kode}, id_mk: " . ($mc->id_mk ?? $mc->kode_mk ?? '') . ", id_cpl: " . ($mc->id_cpl ?? '') . " (" . ($cpl->kode ?? 'none') . ")\n";
}
