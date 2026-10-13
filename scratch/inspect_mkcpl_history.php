<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 1. All tables (look for log / audit / history tables)
$tables = array_map(fn($t) => array_values((array)$t)[0], DB::select('SHOW TABLES'));
echo "== Tables possibly holding history ==\n";
foreach ($tables as $t) {
    if (preg_match('/log|audit|activit|histor|backup|trash|revision|event/i', $t)) {
        echo "  $t (" . DB::table($t)->count() . " rows) cols: " . implode(',', Schema::getColumnListing($t)) . "\n";
    }
}

// 2. mk_cpl rows by id, with timestamps (to see id ordering vs dates)
echo "\n== mk_cpl rows (all prodi), ordered by id ==\n";
$rows = DB::table('mk_cpl')->orderBy('id')->get(['id','mk_kode','cpl_id','id_prodi','created_at','updated_at']);
echo "Total all prodi: " . $rows->count() . "\n";
foreach ($rows as $r) {
    if ($r->id_prodi == 11) {
        echo sprintf("  id=%-5s %-10s cpl=%-4s c=%-20s u=%s\n", $r->id, $r->mk_kode, $r->cpl_id, $r->created_at ?? 'NULL', $r->updated_at ?? 'NULL');
    }
}

// 3. Do related tables have timestamps for prodi 11?
echo "\n== Related timestamps ==\n";
foreach (['mks' => 'id_prodi', 'cpls' => 'id_prodi', 'rpss' => 'id_prodi', 'cpmks' => 'id_prodi'] as $t => $col) {
    if (!Schema::hasTable($t)) continue;
    $hasC = Schema::hasColumn($t, 'created_at');
    if (!$hasC) { echo "  $t: no created_at\n"; continue; }
    $q = DB::table($t)->where($col, 11);
    echo "  $t: total=" . (clone $q)->count()
        . " null_created=" . (clone $q)->whereNull('created_at')->count()
        . " min=" . (clone $q)->min('created_at') . " max=" . (clone $q)->max('created_at') . "\n";
}
