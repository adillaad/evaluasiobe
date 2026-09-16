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

$pmUser = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Penjamin Mutu Universitas');
})->first() ?? User::first();
auth()->login($pmUser);

$students = Mahasiswa::take(3)->get();

foreach ($students as $mhs) {
    $prodi = Prodi::find($mhs->id_prodi);
    
    // 1. Dashboard Mahasiswa
    $compData = $mhs->getCompetencyData();
    $cpls = collect($compData['cpls']);
    $avgCpl = round($cpls->avg('nilai') ?? 0, 2);

    echo "NPM: {$mhs->NPM} ({$mhs->Nama})\n";
    echo "Dashboard Avg CPL: {$avgCpl}\n";
    foreach ($cpls as $c) {
        echo "  - {$c['kode']}: {$c['nilai']}\n";
    }

    // 2. Visualisasi Mahasiswa
    $pmController = new PMVisualController();
    $req = new Request([
        'universitas' => 1,
        'prodi' => $mhs->id_prodi,
        'angkatan' => $mhs->angkatan,
        'npm' => $mhs->NPM,
        'tahun' => 'all',
        'semester' => 'all'
    ]);

    $res = $pmController->hasilVisualMahasiswa($req);
    $data = $res->getData();
    $visAvgCpl = is_array($data) ? ($data['rataRataCpl'] ?? 0) : ($data->rataRataCpl ?? 0);
    $visCpls = is_array($data) ? ($data['cpls'] ?? []) : ($data->cpls ?? []);

    echo "Visualisasi Avg CPL: {$visAvgCpl}\n";
    foreach ($visCpls as $c) {
        $kode = is_object($c) ? $c->kode : ($c['kode'] ?? '-');
        $nilai = is_object($c) ? $c->nilai : ($c['nilai'] ?? 0);
        echo "  - {$kode}: {$nilai}\n";
    }
    echo "Selisih: " . abs($avgCpl - $visAvgCpl) . "\n\n";
}
