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
use App\Http\Controllers\Dosen\VisualisasiController as DosenVisualController;
use App\Http\Controllers\Mahasiswa\TranskripKompetensiController;
use Illuminate\Http\Request;

echo "=== AUDIT PERHITUNGAN DASHBOARD MAHASISWA VS VISUALISASI ===\n\n";

$students = Mahasiswa::take(5)->get();

foreach ($students as $mhs) {
    echo "------------------------------------------------------------\n";
    echo "Mahasiswa: {$mhs->Nama} (NPM: {$mhs->NPM}, Angkatan: {$mhs->angkatan}, Prodi ID: {$mhs->id_prodi})\n";
    $prodi = Prodi::find($mhs->id_prodi);
    $isAptikom = (bool)($prodi->is_aptikom ?? false);
    echo "Prodi: {$prodi->nama} (is_aptikom: " . ($isAptikom ? 'YES' : 'NO') . ")\n";

    // 1. Dashboard Mahasiswa Calculation
    $competencyData = $mhs->getCompetencyData();
    $allCpmks = collect($competencyData['cpmks']);
    $allCpls = collect($competencyData['cpls']);
    $ketercapaianCpl = $mhs->calculateKetercapaianCpl();
    $sksLulus = $mhs->calculateSksLulus();
    $ipk = $mhs->calculateIPK();
    $avgSkorCpmk = round($allCpmks->avg('nilai') ?? 0, 1);
    $avgSkorCpl = round($allCpls->avg('nilai') ?? 0, 1);

    echo "1. Dashboard Mahasiswa (Cumulative):\n";
    echo "   - SKS Lulus: {$sksLulus}\n";
    echo "   - IPK: {$ipk}\n";
    echo "   - Rata-rata Skor CPMK: {$avgSkorCpmk} (Total CPMK: " . $allCpmks->count() . ")\n";
    echo "   - Rata-rata Skor CPL: {$avgSkorCpl} (Total CPL: " . $allCpls->count() . ")\n";
    echo "   - Ketercapaian CPL (%): {$ketercapaianCpl['avg']}%\n";

    // 2. Transkrip Kompetensi Calculation
    $transkripController = new TranskripKompetensiController();
    $tkData = $transkripController->buildCompetencyData($mhs, $prodi, null);
    $tkData = $transkripController->prepareDataForBlade($tkData, $prodi);
    $tkData = $transkripController->addIpkAndTotals($tkData);

    echo "2. Transkrip Kompetensi (Cumulative):\n";
    echo "   - SKS: " . ($tkData['total_sks'] ?? '-') . "\n";
    echo "   - IPK: " . ($tkData['ipk'] ?? '-') . "\n";
    $tkCpmks = collect($tkData['cpmks'] ?? []);
    $tkCpls = collect($tkData['cpls'] ?? []);
    echo "   - Rata-rata Skor CPMK: " . round($tkCpmks->avg('nilai') ?? 0, 1) . "\n";
    echo "   - Rata-rata Skor CPL: " . round($tkCpls->avg('nilai') ?? 0, 1) . "\n";

    // 3. Visualisasi Mahasiswa Calculation (VisualisasiController)
    $pmController = new PMVisualController();
    // Let's check how VisualisasiController calculates CPL/CPMK for student
    echo "\n";
}
