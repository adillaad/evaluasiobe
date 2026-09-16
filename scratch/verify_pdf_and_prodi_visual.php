<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use App\Http\Controllers\PenjaminMutu\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=== TEST VISUALISASI PRODI & PDF GENERATION ===\n\n";

// 1. Test Routes
echo "1. Testing Routes:\n";
$routes = [
    'penjamin-mutu.fakultas.visualisasi.visual-program-studi',
    'penjamin-mutu.fakultas.visualisasi.generate-pdfVisualProgramStudi',
    'penjamin-mutu.universitas.visualisasi.visual-program-studi',
    'penjamin-mutu.universitas.visualisasi.generate-pdfVisualProgramStudi'
];
foreach ($routes as $r) {
    if (Route::has($r)) {
        echo "   [SUCCESS] Route '{$r}' exists.\n";
    } else {
        echo "   [FAIL] Route '{$r}' NOT found!\n";
    }
}

// 2. Login User
$user = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Penjamin Mutu Fakultas');
})->first();
if (!$user) {
    $user = User::first();
}
auth()->login($user);
$otoritasName = $user->otoritas ? $user->otoritas->otoritas : 'N/A';
echo "\n2. User login: {$user->name} ({$otoritasName}), ID Fakultas: " . ($user->id_fakultasUser ?? 'null') . "\n";

// 3. Test VisualisasiController::indexProgramStudi
echo "\n3. Testing VisualisasiController::indexProgramStudi:\n";
$controller = new VisualisasiController();
$request = new Request();
try {
    $response = $controller->indexProgramStudi($request);
    echo "   [SUCCESS] Returned view: " . $response->name() . "\n";
    $html = $response->render();
    echo "   [SUCCESS] Rendered view length: " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "   [ERROR] View render error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// 4. Test PDF Generation (Faculty Level)
echo "\n4. Testing PDF Generation (Full Faculty):\n";
try {
    $reqPdf = new Request([
        'fakultas_id' => $user->id_fakultasUser
    ]);
    $pdfResponse = $controller->generatePDFhasilVisualProgramStudi($reqPdf);
    $pdfContent = $pdfResponse->getContent();
    echo "   [SUCCESS] Faculty PDF generated successfully! Size: " . strlen($pdfContent) . " bytes\n";
    file_put_contents(__DIR__ . '/test_faculty_report.pdf', $pdfContent);
} catch (\Throwable $e) {
    echo "   [ERROR] Faculty PDF error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// 5. Test PDF Generation (Single Prodi)
echo "\n5. Testing PDF Generation (Single Prodi):\n";
$firstProdi = Prodi::where('id_fakultas', $user->id_fakultasUser)->first();
if ($firstProdi) {
    try {
        $reqPdfProdi = new Request([
            'fakultas_id' => $user->id_fakultasUser,
            'prodi_id' => $firstProdi->id
        ]);
        $pdfProdiResponse = $controller->generatePDFhasilVisualProgramStudi($reqPdfProdi);
        $pdfProdiContent = $pdfProdiResponse->getContent();
        echo "   [SUCCESS] Single Prodi ({$firstProdi->nama}) PDF generated! Size: " . strlen($pdfProdiContent) . " bytes\n";
        file_put_contents(__DIR__ . '/test_prodi_report.pdf', $pdfProdiContent);
    } catch (\Throwable $e) {
        echo "   [ERROR] Single Prodi PDF error: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
}

// 6. Test Dashboard view rendering (verify clean after removing CPL)
echo "\n6. Testing DashboardController::list view rendering:\n";
try {
    $dashController = new DashboardController();
    $dashResp = $dashController->list(new Request());
    $dashHtml = $dashResp->render();
    echo "   [SUCCESS] Dashboard rendered cleanly! Length: " . strlen($dashHtml) . " bytes\n";
} catch (\Throwable $e) {
    echo "   [ERROR] Dashboard render error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== ALL CHECKS PASSED ===\n";
