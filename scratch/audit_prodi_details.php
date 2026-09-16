<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Mk;
use App\Models\Nilai;
use App\Models\Cpl;
use App\Models\Krs;

echo "=== ALL STUDENTS IN SYSTEM ===" . PHP_EOL;
$allMhs = Mahasiswa::with('prodi.fakultas')->get();
foreach ($allMhs as $m) {
    $pName = $m->prodi->nama ?? '-';
    $fName = $m->prodi->fakultas->nama ?? '-';
    echo "NPM: {$m->npm} | Nama: {$m->nama} | Angkatan: {$m->angkatan} | Prodi: {$pName} | Fakultas: {$fName}" . PHP_EOL;
}

echo PHP_EOL . "=== S2 ILMU KOMPUTER AUDIT ===" . PHP_EOL;
$s2 = Prodi::where('nama', 'LIKE', '%S2%')->orWhere('jenjang', 'S2')->get();
foreach ($s2 as $p) {
    $mhsCount = Mahasiswa::where('id_prodi', $p->id)->count();
    $mkCount = Mk::where('id_prodi', $p->id)->count();
    $cplCount = Cpl::where('id_prodi', $p->id)->count();
    $nilaiCount = Nilai::whereHas('mahasiswa', function($q) use ($p) {
        $q->where('id_prodi', $p->id);
    })->count();
    echo "Prodi: {$p->nama} ({$p->jenjang}) [ID: {$p->id}]" . PHP_EOL;
    echo "  - Mahasiswa: {$mhsCount}" . PHP_EOL;
    echo "  - MK: {$mkCount}" . PHP_EOL;
    echo "  - CPL: {$cplCount}" . PHP_EOL;
    echo "  - Nilai/Asesmen: {$nilaiCount}" . PHP_EOL;
}

echo PHP_EOL . "=== ALL PRODIS IN FAKULTAS 1 ===" . PHP_EOL;
$fProdis = Prodi::where('id_fakultas', 1)->get();
foreach ($fProdis as $p) {
    echo "Prodi: ID {$p->id} | {$p->nama} ({$p->jenjang})" . PHP_EOL;
}
