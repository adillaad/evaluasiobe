<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Universitas;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Http\Controllers\PenjaminMutu\DashboardController;

$universitasId = 57;
$fakultasList = Fakultas::where('id_universitas', $universitasId)->get();
$allProdiIds = Prodi::whereIn('id_fakultas', $fakultasList->pluck('id'))->pluck('id')->toArray();

$lowestQuestionsQuery = DB::table('mutus')
    ->join('mks', 'mutus.Course', '=', 'mks.kode')
    ->join('prodi', 'mutus.id_prodi', '=', 'prodi.id')
    ->join('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
    ->leftJoin('soals', 'mutus.idSoal', '=', 'soals.id')
    ->whereIn('mutus.id_prodi', $allProdiIds)
    ->whereNotNull('mutus.Course')
    ->select(
        'mutus.Course',
        'mks.nama as namaCourse',
        'mutus.Jenis',
        'mutus.soal',
        'soals.pertanyaan as soalFromId',
        'prodi.nama as namaProdi',
        'fakultas.nama as namaFakultas',
        'mutus.idSoal',
        DB::raw('AVG(mutus.Nilai) as avg_nilai')
    )
    ->groupBy('mutus.Course', 'mks.nama', 'mutus.Jenis', 'mutus.soal', 'soals.pertanyaan', 'prodi.nama', 'fakultas.nama', 'mutus.idSoal')
    ->orderBy('avg_nilai', 'asc')
    ->limit(10)
    ->get();

echo "=== LOWEST QUESTIONS UNIV ===" . PHP_EOL;
foreach ($lowestQuestionsQuery as $q) {
    $soalText = !empty($q->soal) ? $q->soal : (!empty($q->soalFromId) ? $q->soalFromId : '-');
    echo "Fakultas: {$q->namaFakultas} | Prodi: {$q->namaProdi} | MK: {$q->namaCourse} | Soal: {$soalText} | Nilai: " . round($q->avg_nilai, 1) . PHP_EOL;
}
