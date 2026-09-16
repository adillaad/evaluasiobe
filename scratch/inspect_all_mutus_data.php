<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Mutu;
use Illuminate\Support\Facades\DB;

$prodis = Prodi::where('id_fakultas', 1)->get();
$prodiIds = $prodis->pluck('id')->toArray();

echo "=== MUTUS RECORDS FOR FAKULTAS 1 ===\n";

$mutus = DB::table('mutus')
    ->leftJoin('mks', 'mutus.Course', '=', 'mks.kode')
    ->whereIn('mutus.id_prodi', $prodiIds)
    ->select('mutus.id_prodi', 'mutus.npm', 'mutus.Course', 'mks.nama as mk_nama', 'mks.semester as mk_sem', 'mutus.tahun', 'mutus.angkatan', 'mutus.Nilai')
    ->get();

echo "Total mutus records: " . count($mutus) . "\n\n";

$byTahun = $mutus->groupBy('tahun');
foreach ($byTahun as $th => $rows) {
    echo "Tahun: '{$th}' (" . count($rows) . " rows):\n";
    $courses = $rows->groupBy('Course');
    foreach ($courses as $c => $cRows) {
        $first = $cRows->first();
        echo "   - MK: {$c} ({$first->mk_nama}), Sem MK: {$first->mk_sem}, Angkatan: {$first->angkatan}, Avg Nilai: " . round($cRows->avg('Nilai'), 1) . " (" . count($cRows) . " records)\n";
    }
}

// Check mutus across ALL prodis in the whole database
echo "\n=== MUTUS ACROSS ALL PRODIS IN DB ===\n";
$allMutus = DB::table('mutus')
    ->leftJoin('mks', 'mutus.Course', '=', 'mks.kode')
    ->leftJoin('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    ->select('mutus.tahun', 'mutus.angkatan', 'mks.semester as mk_sem', 'prodi.nama as prodi_nama', 'fakultas.nama as fak_nama', DB::raw('count(*) as count'))
    ->groupBy('mutus.tahun', 'mutus.angkatan', 'mks.semester', 'prodi.nama', 'fakultas.nama')
    ->get();

foreach ($allMutus as $m) {
    echo "Fakultas: {$m->fak_nama} | Prodi: {$m->prodi_nama} | Tahun: '{$m->tahun}' | Angkatan: '{$m->angkatan}' | Sem MK: {$m->mk_sem} | Total: {$m->count}\n";
}
