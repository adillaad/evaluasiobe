<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$npm = '2118051001'; // Haikal
$prodiId = 17;

// Let's get dictionaryCpl for Haikal
$haikalMutus = DB::table('mutus')->where('npm', $npm)->get();
echo "Total rows for Haikal in mutus: " . count($haikalMutus) . "\n";

// Check distinct CPLs and their scores
$cplScores = [];
foreach ($haikalMutus as $m) {
    if (!isset($cplScores[$m->Cpl])) {
        $cplScores[$m->Cpl] = [];
    }
    $cplScores[$m->Cpl][] = $m->Nilai;
}
foreach ($cplScores as $cpl => $scores) {
    echo "CPL {$cpl}: Average = " . round(array_sum($scores)/count($scores), 2) . " (Count: " . count($scores) . ")\n";
}

// Check what the controller does:
// Controller gets $dictionaryCpl (map of CPL ID => score)
// For Haikal, the lowest CPL with score > 0 or 0:
// Let's see what $minCplKey is:
$minCplKey = '156'; // or similar

// Run the exact query from VisualisasiController
$soalDesc = DB::table('mutus')
    ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    ->join('mks', 'mutus.Course', '=', 'mks.kode')
    ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
    ->select('mutus.id', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
    ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
    ->where('mutus.npm', $npm)
    ->where('mutus.cpl', '156')
    ->distinct()
    ->get();

echo "\nRows for CPL 156:\n";
foreach ($soalDesc as $r) {
    echo "ID: {$r->id} | Course: {$r->Course} ({$r->namaCourse}) | Jenis: {$r->Jenis} | mutus.soal: " . var_export($r->soal, true) . " | idSoal: " . var_export($r->idSoal, true) . " | soalFromId: " . var_export($r->soalFromId, true) . "\n";
}

$soalDesc153 = DB::table('mutus')
    ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    ->join('mks', 'mutus.Course', '=', 'mks.kode')
    ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
    ->select('mutus.id', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
    ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
    ->where('mutus.npm', $npm)
    ->where('mutus.cpl', '153')
    ->distinct()
    ->get();

echo "\nRows for CPL 153:\n";
foreach ($soalDesc153 as $r) {
    echo "ID: {$r->id} | Course: {$r->Course} ({$r->namaCourse}) | Jenis: {$r->Jenis} | mutus.soal: " . var_export($r->soal, true) . " | idSoal: " . var_export($r->idSoal, true) . " | soalFromId: " . var_export($r->soalFromId, true) . "\n";
}

$soalDesc159 = DB::table('mutus')
    ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    ->join('mks', 'mutus.Course', '=', 'mks.kode')
    ->leftJoin('tahun_ajaran as ta', 'mutus.tahun_ajaran_id', '=', 'ta.id')
    ->select('mutus.id', 'mutus.soal', 'mutus.Jenis', 'mutus.Course', 'mks.nama as namaCourse', 'mutus.idSoal', 'soals.pertanyaan as soalFromId')
    ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
    ->where('mutus.npm', $npm)
    ->where('mutus.cpl', '159')
    ->distinct()
    ->get();

echo "\nRows for CPL 159:\n";
foreach ($soalDesc159 as $r) {
    echo "ID: {$r->id} | Course: {$r->Course} ({$r->namaCourse}) | Jenis: {$r->Jenis} | mutus.soal: " . var_export($r->soal, true) . " | idSoal: " . var_export($r->idSoal, true) . " | soalFromId: " . var_export($r->soalFromId, true) . "\n";
}
