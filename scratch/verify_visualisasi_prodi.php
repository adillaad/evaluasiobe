<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=== TEST VISUALISASI PER PROGRAM STUDI ===\n\n";

// 1. Test Route Registration
echo "1. Testing Route Registration:\n";
$routes = ['penjamin-mutu.fakultas.visualisasi.visual-program-studi', 'penjamin-mutu.universitas.visualisasi.visual-program-studi'];
foreach ($routes as $r) {
    if (Route::has($r)) {
        echo "   [SUCCESS] Route '{$r}' is registered.\n";
    } else {
        echo "   [FAIL] Route '{$r}' is NOT registered!\n";
    }
}

// 2. Find a Penjamin Mutu Fakultas user
echo "\n2. Finding Penjamin Mutu Fakultas User:\n";
$user = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Penjamin Mutu Fakultas');
})->first();

if (!$user) {
    echo "   [INFO] No Penjamin Mutu Fakultas user found, checking Wakil Dekan...\n";
    $user = User::whereHas('otoritas', function($q) {
        $q->where('otoritas', 'Wakil Dekan');
    })->first();
}

if (!$user) {
    echo "   [INFO] Testing with any user...\n";
    $user = User::first();
}

$otoritasName = $user->otoritas ? $user->otoritas->otoritas : 'N/A';
echo "   Using user: {$user->name} ({$otoritasName}), ID Fakultas: " . ($user->id_fakultasUser ?? 'null') . "\n";
auth()->login($user);

// 3. Test Controller Method Execution
echo "\n3. Testing VisualisasiController::indexProgramStudi:\n";
$controller = new VisualisasiController();
$request = new Request();
try {
    $response = $controller->indexProgramStudi($request);
    echo "   [SUCCESS] Controller returned view: " . $response->name() . "\n";
    $data = $response->getData();
    echo "   View Data keys: " . implode(', ', array_keys($data)) . "\n";
    if (!empty($data['fakultasCplData'])) {
        echo "   Fakultas: " . ($data['fakultasCplData']['fakultas']->nama ?? 'N/A') . "\n";
        echo "   Prodi count: " . count($data['fakultasCplData']['prodi_stats']) . "\n";
        echo "   Summary avg skor: " . $data['fakultasCplData']['summary']['faculty_avg_skor'] . "\n";
        echo "   Summary avg capaian: " . $data['fakultasCplData']['summary']['faculty_avg_capaian'] . "%\n";
    } else {
        echo "   [INFO] fakultasCplData is null (no faculty associated).\n";
    }
} catch (\Throwable $e) {
    echo "   [ERROR] Controller failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// 4. Test View Rendering
echo "\n4. Testing View Rendering:\n";
try {
    $html = $response->render();
    echo "   [SUCCESS] View rendered successfully! Length: " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "   [ERROR] View render failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// 5. Test with filters
echo "\n5. Testing with semester filter:\n";
try {
    $reqFiltered = new Request(['tahun' => '2022', 'semester' => '1']);
    $respFiltered = $controller->indexProgramStudi($reqFiltered);
    $htmlFiltered = $respFiltered->render();
    echo "   [SUCCESS] Filtered view rendered successfully! Length: " . strlen($htmlFiltered) . " bytes\n";
} catch (\Throwable $e) {
    echo "   [ERROR] Filtered view failed: " . $e->getMessage() . "\n";
}

echo "\n=== ALL CHECKS COMPLETED ===\n";
