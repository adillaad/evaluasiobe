<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Universitas;
use App\Models\Fakultas;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use Illuminate\Http\Request;

echo "=== TESTING VISUALISASI PER FAKULTAS (TINGKAT UNIVERSITAS) ===\n";

// 1. Check user universitas or find first universitas
$user = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'like', '%Universitas%');
})->first();

if (!$user) {
    $user = User::first();
}

auth()->login($user);
echo "Logged in as: " . $user->name . " (" . ($user->otoritas->otoritas ?? '-') . ")\n";

$controller = new VisualisasiController();
$request = Request::create('/visualisasi/visual-fakultas', 'GET');

// Test indexFakultas
$response = $controller->indexFakultas($request);
echo "indexFakultas() called successfully.\n";

$data = $response->getData();
echo "Data keys: " . implode(', ', array_keys($data)) . "\n";
echo "Universitas: " . ($data['universitas']->nama ?? 'None') . "\n";
echo "Total Fakultas: " . count($data['universitasCplData']['fakultas_stats'] ?? []) . "\n";
echo "Univ Avg Skor: " . ($data['universitasCplData']['summary']['univ_avg_skor'] ?? 0) . "\n";
echo "Univ Avg Capaian: " . ($data['universitasCplData']['summary']['univ_avg_capaian'] ?? 0) . "%\n";
echo "Available Years: " . implode(', ', $data['availableYears'] ?? []) . "\n";
echo "Aspek count: " . count($data['aspekAnalytics'] ?? []) . "\n";
echo "Soal terendah count: " . count($data['soalTerendah'] ?? []) . "\n";

// Test View Compilation
echo "\nTesting view compilation...\n";
$viewRendered = view('penjamin-mutu.visualisasi.indexVisualisasiFakultas', $data)->render();
echo "indexVisualisasiFakultas.blade.php rendered successfully! (Length: " . strlen($viewRendered) . " bytes)\n";

// Test PDF Compilation
echo "\nTesting PDF view compilation...\n";
$pdfData = [
    'universitasCplData' => $data['universitasCplData'],
    'universitas' => $data['universitas'],
    'selectedFakultas' => null,
    'yearlyProgression' => $data['yearlyProgression'],
    'availableYears' => $data['availableYears'],
    'yearlyAverages' => $data['yearlyAverages'],
    'aspekAnalytics' => $data['aspekAnalytics'],
    'soalTerendah' => $data['soalTerendah'],
    'chartImg' => '',
    'trendChartImg' => '',
    'tanggalCetak' => date('d F Y')
];
$pdfViewRendered = view('pdf.reportVisualisasiFakultas', $pdfData)->render();
echo "reportVisualisasiFakultas.blade.php rendered successfully! (Length: " . strlen($pdfViewRendered) . " bytes)\n";

// Test Single Fakultas PDF Compilation
if (!empty($data['universitasCplData']['fakultas_stats'])) {
    $pdfData['selectedFakultas'] = $data['universitasCplData']['fakultas_stats'][0];
    $pdfSingleRendered = view('pdf.reportVisualisasiFakultas', $pdfData)->render();
    echo "reportVisualisasiFakultas (Single Fakultas) rendered successfully! (Length: " . strlen($pdfSingleRendered) . " bytes)\n";
}

echo "\nALL TESTS PASSED PERFECTLY!\n";
