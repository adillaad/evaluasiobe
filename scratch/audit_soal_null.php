<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== 1. AUDIT HAIKAL DATA IN MUTUS (SOAL & IDSOAL) ===\n";
$haikalMutus = DB::table('mutus')->where('npm', '2118051001')->get();
echo "Total records for Haikal: " . count($haikalMutus) . "\n";
foreach ($haikalMutus as $m) {
    echo "ID: {$m->id} | MK: {$m->Course} | Jenis: {$m->Jenis} | CPL: {$m->Cpl} | CPMK: {$m->Cpmk} | soal: " . var_export($m->soal, true) . " | idSoal: " . var_export($m->idSoal, true) . " | Nilai: {$m->Nilai}\n";
}

echo "\n=== 2. SOAL TERENDAH QUERY SIMULATION FOR HAIKAL ===\n";
// Find min CPL for Haikal
// Let's check Haikal's CPL scores
$haikalCpls = $haikalMutus->groupBy('Cpl');
echo "CPLs present in Haikal mutus:\n";
foreach ($haikalCpls as $cplKey => $rows) {
    echo "- CPL Key: '{$cplKey}' (Count: " . count($rows) . ")\n";
}

echo "\n=== 3. AUDIT SOAL NULL ACROSS ENTIRE MUTUS TABLE ===\n";
$totalMutus = DB::table('mutus')->count();
$soalNullCount = DB::table('mutus')->whereNull('soal')->count();
$soalEmptyCount = DB::table('mutus')->where('soal', '')->count();
$idSoalNullCount = DB::table('mutus')->whereNull('idSoal')->count();
$bothNullCount = DB::table('mutus')->where(function($q) {
    $q->whereNull('soal')->orWhere('soal', '');
})->whereNull('idSoal')->count();

echo "Total Mutus Rows: {$totalMutus}\n";
echo "Rows with soal NULL: {$soalNullCount}\n";
echo "Rows with soal Empty String: {$soalEmptyCount}\n";
echo "Rows with idSoal NULL: {$idSoalNullCount}\n";
echo "Rows with BOTH soal (NULL/empty) AND idSoal NULL: {$bothNullCount}\n";

echo "\n=== 4. BREAKDOWN OF NULL SOAL BY PRODI & MAHASISWA ===\n";
$nullByProdi = DB::table('mutus')
    ->leftJoin('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    ->where(function($q) {
        $q->whereNull('mutus.soal')->orWhere('mutus.soal', '');
    })
    ->whereNull('mutus.idSoal')
    ->select('mutus.id_prodi', 'prodi.nama as nama_prodi', DB::raw('count(*) as count'), DB::raw('count(distinct mutus.npm) as total_mhs'))
    ->groupBy('mutus.id_prodi', 'prodi.nama')
    ->get();

foreach ($nullByProdi as $p) {
    echo "Prodi ID: {$p->id_prodi} ({$p->nama_prodi}) -> {$p->count} rows null, affecting {$p->total_mhs} mahasiswa\n";
}

echo "\n=== 5. LIST OF MAHASISWA WITH NULL SOAL ===\n";
$nullMhs = DB::table('mutus')
    ->where(function($q) {
        $q->whereNull('soal')->orWhere('soal', '');
    })
    ->whereNull('idSoal')
    ->select('npm', 'nama_mhs', 'angkatan', 'id_prodi', DB::raw('count(*) as total_null_rows'))
    ->groupBy('npm', 'nama_mhs', 'angkatan', 'id_prodi')
    ->get();

echo "Total Mahasiswa with null soal: " . count($nullMhs) . "\n";
foreach ($nullMhs as $m) {
    echo "- NPM: {$m->npm} | Nama: {$m->nama_mhs} | Angkatan: {$m->angkatan} | Prodi ID: {$m->id_prodi} | Null Rows: {$m->total_null_rows}\n";
}

echo "\n=== 6. CHECK SOALS TABLE (APAKAH ADA DATA SOAL DI DATABASE?) ===\n";
$totalSoals = DB::table('soals')->count();
echo "Total rows in soals table: {$totalSoals}\n";
$sampleSoals = DB::table('soals')->take(5)->get();
print_r($sampleSoals);
