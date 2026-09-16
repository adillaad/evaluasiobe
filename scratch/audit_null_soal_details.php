<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DETAIL DATA MUTUS YANG MEMILIKI SOAL NULL ===\n";

$nulls = DB::table('mutus')
    ->whereNull('soal')
    ->orWhere('soal', '')
    ->get();

echo "Total records with null/empty soal: " . count($nulls) . "\n\n";

// Breakdown by Sumber / Konversi / Jenis
$byJenis = $nulls->groupBy('Jenis')->map->count();
echo "Breakdown by Jenis Asesmen:\n";
foreach ($byJenis as $jenis => $count) {
    echo "- Jenis: '{$jenis}' -> {$count} rows\n";
}

$byCourse = $nulls->groupBy('Course')->map->count();
echo "\nBreakdown by Course (Kode MK):\n";
foreach ($byCourse as $course => $count) {
    $mk = DB::table('mks')->where('kode', $course)->first();
    echo "- Course: '{$course}' (" . ($mk ? $mk->nama : 'Unknown') . ") -> {$count} rows\n";
}

$bySumber = $nulls->groupBy('sumber')->map->count();
echo "\nBreakdown by Sumber:\n";
foreach ($bySumber as $sumber => $count) {
    echo "- Sumber: " . var_export($sumber, true) . " -> {$count} rows\n";
}

$byKonversi = $nulls->groupBy('konversi_metode_id')->map->count();
echo "\nBreakdown by konversi_metode_id:\n";
foreach ($byKonversi as $k => $count) {
    echo "- konversi_metode_id: " . var_export($k, true) . " -> {$count} rows\n";
}

echo "\n=== ALL 10 STUDENTS AFFECTED BY NULL SOAL ===\n";
$students = DB::table('mutus')
    ->whereNull('soal')
    ->orWhere('soal', '')
    ->select('npm', 'nama_mhs', 'angkatan', 'id_prodi')
    ->distinct()
    ->get();

foreach ($students as $s) {
    $prodi = DB::table('prodi')->where('id', $s->id_prodi)->first();
    $totalRows = DB::table('mutus')->where('npm', $s->npm)->count();
    $nullRows = DB::table('mutus')->where('npm', $s->npm)->where(fn($q) => $q->whereNull('soal')->orWhere('soal', ''))->count();
    echo "• Mahasiswa: {$s->nama_mhs} | NPM: {$s->npm} | Angkatan: {$s->angkatan} | Prodi: " . ($prodi ? $prodi->nama : "Prodi {$s->id_prodi}") . " | Null Soal: {$nullRows}/{$totalRows} baris\n";
}
