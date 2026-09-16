<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Http\Controllers\PenjaminMutu\VisualisasiController as PMVisualController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$user = User::first();
auth()->login($user);

$students = Mahasiswa::take(5)->get();

foreach ($students as $mhs) {
    $prodi = Prodi::find($mhs->id_prodi);
    $univId = DB::table('mutus')->where('npm', $mhs->NPM)->value('universitas_id');

    // 1. Dashboard Mahasiswa
    $compData = $mhs->getCompetencyData();
    $cpls = collect($compData['cpls']);
    $avgCpl = round($cpls->avg('nilai') ?? 0, 2);

    // 2. Visualisasi Mahasiswa
    $pmController = new PMVisualController();
    $req = new Request([
        'universitas' => $univId,
        'prodi' => $mhs->id_prodi,
        'angkatan' => $mhs->angkatan,
        'npm' => $mhs->NPM,
        'tahun' => 'all',
        'semester' => 'all'
    ]);

    $res = $pmController->hasilVisualMahasiswa($req);
    $viewData = (array)$res->getData();
    $visAvgCpl = $viewData['rataRataCpl'] ?? 0;
    $visCpls = $viewData['cpls'] ?? [];

    echo "======================================================================\n";
    echo "NPM: {$mhs->NPM} ({$mhs->Nama}) - Prodi: {$prodi->nama}\n";
    echo "Dashboard Avg CPL: {$avgCpl}\n";
    echo "Visualisasi Avg CPL: {$visAvgCpl}\n";
    echo "Difference: " . abs((float)$avgCpl - (float)$visAvgCpl) . "\n";
    echo "Detail Dashboard CPL:\n";
    foreach ($cpls as $c) {
        $cArr = (array)$c;
        echo "  - {$cArr['kode']}: {$cArr['nilai']}\n";
    }
    echo "Detail Visualisasi CPL:\n";
    foreach ($visCpls as $c) {
        $cArr = (array)$c;
        echo "  - " . ($cArr['kode'] ?? '-') . ": " . ($cArr['nilai'] ?? 0) . "\n";
    }
}
