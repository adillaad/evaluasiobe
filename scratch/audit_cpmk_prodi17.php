<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ALL CPMKS FOR PRODI 17 ===\n";
$cpmks = DB::table('cpmks')->where('id_prodi', 17)->get();
foreach ($cpmks as $cp) {
    $cpl = DB::table('cpls')->where('id', $cp->cpl_id)->first();
    echo "CPMK ID: {$cp->id}, Kode: {$cp->kode}, Judul: {$cp->judul}, cpl_id: {$cp->cpl_id} (" . ($cpl->kode ?? 'NOT FOUND') . ")\n";
}

echo "\n=== ALL CPLS FOR PRODI 17 ===\n";
$cpls = DB::table('cpls')->where('id_prodi', 17)->get();
foreach ($cpls as $c) {
    $cpmkCount = DB::table('cpmks')->where('cpl_id', $c->id)->count();
    $mutusCount = DB::table('mutus')->where('Cpl', $c->id)->count();
    echo "CPL ID: {$c->id}, Kode: {$c->kode}, Judul: {$c->judul}\n";
    echo "  -> Linked CPMKs in DB: {$cpmkCount}, Linked Records in Mutus: {$mutusCount}\n";
}

echo "\n=== ALL CPLMKS IN DATABASE ===\n";
$cplmks = DB::table('cplmks')->get();
echo "Total cplmks: " . $cplmks->count() . "\n";
foreach ($cplmks as $cm) {
    echo "ID: {$cm->id}, kode_mk: {$cm->kode_mk}, id_cpl: {$cm->id_cpl}, id_prodi: {$cm->id_prodi}\n";
}

echo "\n=== ALL MK_CPL IN DATABASE ===\n";
if (\Illuminate\Support\Facades\Schema::hasTable('mk_cpl')) {
    $mkCpl = DB::table('mk_cpl')->get();
    echo "Total mk_cpl: " . $mkCpl->count() . "\n";
    foreach ($mkCpl as $mc) {
        print_r($mc);
    }
}
