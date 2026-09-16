<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;

echo "=== TAHUN AJARAN TABLE ===" . PHP_EOL;
$tas = DB::table('tahun_ajaran')->get();
foreach ($tas as $ta) {
    echo "ID: {$ta->id} | Tahun: {$ta->tahun} | Semester: {$ta->jenis_semester}" . PHP_EOL;
}

echo PHP_EOL . "=== MUTUS TABLE TAHUN FOR PRODI 11 & 12 ===" . PHP_EOL;
$mutus = DB::table('mutus')->whereIn('id_prodi', [11, 12])->select('tahun', 'Course', DB::raw('count(*) as c'))->groupBy('tahun', 'Course')->get();
foreach ($mutus as $m) {
    echo "Tahun: {$m->tahun} | Course: {$m->Course} | Count: {$m->c}" . PHP_EOL;
}

echo PHP_EOL . "=== MAHASISWA ANGKATAN FOR PRODI 11 ===" . PHP_EOL;
$mhs = Mahasiswa::where('id_prodi', 11)->get();
foreach ($mhs as $m) {
    echo "NPM: {$m->npm} | Nama: {$m->nama} | Angkatan: {$m->angkatan}" . PHP_EOL;
}
