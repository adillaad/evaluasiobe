<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Http\Controllers\Mahasiswa\DashboardController as MhsDashController;
use App\Http\Controllers\PenjaminMutu\VisualisasiController as PMVisualController;
use Illuminate\Http\Request;

echo "=== AUDIT KESELARASAN PERHITUNGAN DASHBOARD MAHASISWA VS VISUALISASI ===\n\n";

$allMhs = Mahasiswa::all();
echo "Total Mahasiswa di database: " . $allMhs->count() . "\n\n";

foreach ($allMhs as $mhs) {
    $prodi = Prodi::find($mhs->id_prodi);
    $isAptikom = (bool)($prodi->is_aptikom ?? false);

    // 1. Dashboard Mahasiswa
    $compData = $mhs->getCompetencyData();
    $cpmks = collect($compData['cpmks']);
    $cpls = collect($compData['cpls']);
    $sks = $mhs->calculateSksLulus();
    $ipk = $mhs->calculateIPK();
    $avgCpmk = round($cpmks->avg('nilai') ?? 0, 2);
    $avgCpl = round($cpls->avg('nilai') ?? 0, 2);

    echo "----------------------------------------------------------------------\n";
    echo "NPM: {$mhs->NPM} | Nama: {$mhs->Nama} | Prodi: {$prodi->nama} (" . ($isAptikom ? 'APTIKOM' : 'Non-APTIKOM') . ")\n";
    echo "  Dashboard Mahasiswa:\n";
    echo "    - SKS Lulus: {$sks}\n";
    echo "    - IPK: {$ipk}\n";
    echo "    - Rata-rata CPMK: {$avgCpmk} (Jumlah CPMK: {$cpmks->count()})\n";
    echo "    - Rata-rata CPL: {$avgCpl} (Jumlah CPL: {$cpls->count()})\n";
    
    // Print CPL details
    echo "    - Detail CPL Dashboard:\n";
    foreach ($cpls as $c) {
        echo "        * {$c['kode']}: {$c['nilai']} ({$c['status']})\n";
    }

    // 2. Call Visualisasi Mahasiswa logic
    $user = User::first();
    auth()->login($user);
    $pmController = new PMVisualController();

    // Call hasilVisualMahasiswa
    $req = new Request([
        'universitas' => 1,
        'prodi' => $mhs->id_prodi,
        'angkatan' => $mhs->angkatan,
        'npm' => $mhs->NPM,
        'tahun' => 'all',
        'semester' => 'all'
    ]);

    try {
        $viewResult = $pmController->hasilVisualMahasiswa($req);
        $data = $viewResult->getData();

        $visCpls = $data['cpls'] ?? [];
        $visAvgCpl = $data['rataRataCpl'] ?? 0;

        echo "  Visualisasi Mahasiswa:\n";
        echo "    - Rata-rata CPL Visualisasi: {$visAvgCpl}\n";
        echo "    - Detail CPL Visualisasi:\n";
        if (is_array($visCpls)) {
            foreach ($visCpls as $c) {
                $kode = is_object($c) ? $c->kode : ($c['kode'] ?? '-');
                $nilai = is_object($c) ? $c->nilai : ($c['nilai'] ?? 0);
                echo "        * {$kode}: {$nilai}\n";
            }
        }

        // Compare
        $diff = abs((float)$avgCpl - (float)$visAvgCpl);
        if ($diff < 0.05) {
            echo "  >>> STATUS: SELARAS (Match!)\n";
        } else {
            echo "  >>> STATUS: ADA SELISIH (Dashboard: {$avgCpl} vs Visualisasi: {$visAvgCpl})\n";
        }

    } catch (\Throwable $e) {
        echo "  Visualisasi Error: " . $e->getMessage() . "\n";
    }
}
