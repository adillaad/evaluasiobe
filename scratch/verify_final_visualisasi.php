<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Universitas;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

echo "=== 1. TESTING BLADE VIEW COMPILATION ===\n";

// Test PM Universitas Visualisasi Fakultas
$pmUniv = User::first();
auth()->login($pmUniv);

$controller = new VisualisasiController();

// 1. Univ View
try {
    $req = new Request();
    $res = $controller->indexFakultas($req);
    $viewData = $res->getData();
    $rendered = View::make($res->name(), $viewData)->render();
    echo "  [OK] indexVisualisasiFakultas rendered successfully (" . strlen($rendered) . " bytes)\n";
} catch (\Throwable $e) {
    echo "  [FAIL] indexVisualisasiFakultas: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

// 2. Prodi View
$pmFakultas = User::first();
auth()->login($pmFakultas);

try {
    $req = new Request();
    $res = $controller->indexProgramStudi($req);
    $viewData = $res->getData();
    $rendered = View::make($res->name(), $viewData)->render();
    echo "  [OK] indexVisualisasiProgramStudi rendered successfully (" . strlen($rendered) . " bytes)\n";
} catch (\Throwable $e) {
    echo "  [FAIL] indexVisualisasiProgramStudi: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== 2. TESTING PDF GENERATION ===\n";

// 3. PDF Fakultas (Univ level)
try {
    $reqPdfUniv = new Request([
        'universitas_id' => 1,
    ]);
    $pdfRes = $controller->generatePDFhasilVisualFakultas($reqPdfUniv);
    $content = $pdfRes->getContent();
    echo "  [OK] generatePDFhasilVisualFakultas (Univ) produced PDF: " . strlen($content) . " bytes\n";
} catch (\Throwable $e) {
    echo "  [FAIL] generatePDFhasilVisualFakultas (Univ): " . $e->getMessage() . "\n";
}

// 4. PDF Single Fakultas
try {
    $fak = Fakultas::first();
    $reqPdfFak = new Request([
        'universitas_id' => 1,
        'fakultas_id' => $fak->id ?? 1,
    ]);
    $pdfRes = $controller->generatePDFhasilVisualFakultas($reqPdfFak);
    $content = $pdfRes->getContent();
    echo "  [OK] generatePDFhasilVisualFakultas (Single Fak) produced PDF: " . strlen($content) . " bytes\n";
} catch (\Throwable $e) {
    echo "  [FAIL] generatePDFhasilVisualFakultas (Single Fak): " . $e->getMessage() . "\n";
}

// 5. PDF Prodi (Fakultas level)
try {
    $reqPdfProdi = new Request([
        'fakultas_id' => $fak->id ?? 1,
    ]);
    $pdfRes = $controller->generatePDFhasilVisualProgramStudi($reqPdfProdi);
    $content = $pdfRes->getContent();
    echo "  [OK] generatePDFhasilVisualProgramStudi (Fakultas) produced PDF: " . strlen($content) . " bytes\n";
} catch (\Throwable $e) {
    echo "  [FAIL] generatePDFhasilVisualProgramStudi (Fakultas): " . $e->getMessage() . "\n";
}

echo "\n=== ALL VERIFICATIONS COMPLETED ===\n";
