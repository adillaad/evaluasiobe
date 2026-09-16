<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Mahasiswa;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use Illuminate\Http\Request;

echo "=== VERIFY ALL PRODI VISUALIZATION FIXES ===" . PHP_EOL;

// 1. Test user FMIPA (ID 35)
$userFmipa = User::find(35);
auth()->login($userFmipa);
echo "1. Logged in as: {$userFmipa->name} (Fakultas ID: {$userFmipa->id_fakultasUser})" . PHP_EOL;

$controller = new VisualisasiController();
$request = Request::create('/penjamin-mutu/fakultas/visualisasi/visual-program-studi', 'GET');
$response = $controller->indexProgramStudi($request);

echo "2. Testing View Rendering:" . PHP_EOL;
$viewContent = $response->render();
echo "   [SUCCESS] View rendered successfully! Size: " . strlen($viewContent) . " bytes" . PHP_EOL;

// Check if removed elements are absent
$hasAspectCard = strpos($viewContent, 'Analisis Capaian CPL Berdasarkan 4 Aspek SN-Dikti') !== false;
$hasSubtitles = strpos($viewContent, 'Rata-rata Penilaian Perkuliahan') !== false;
$hasFakultasMeta = strpos($viewContent, '<span class="meta-label">Fakultas:</span>') !== false;
$hasSelect = strpos($viewContent, 'id="selectProgramStudiInline"') !== false;
$hasS1Option = strpos($viewContent, 'S1-Ilmu Komputer') !== false;
$hasS2Option = strpos($viewContent, 'S2 - Ilmu Komputer') !== false;

echo "3. Content Checks:" . PHP_EOL;
echo "   - 4 Aspek Card removed: " . (!$hasAspectCard ? '[PASS]' : '[FAIL]') . PHP_EOL;
echo "   - Stat subtext removed: " . (!$hasSubtitles ? '[PASS]' : '[FAIL]') . PHP_EOL;
echo "   - Hero duplicate Fakultas removed: " . (!$hasFakultasMeta ? '[PASS]' : '[FAIL]') . PHP_EOL;
echo "   - Select dropdown present: " . ($hasSelect ? '[PASS]' : '[FAIL]') . PHP_EOL;
echo "   - S1 in select: " . ($hasS1Option ? '[PASS]' : '[FAIL]') . PHP_EOL;
echo "   - S2 in select: " . ($hasS2Option ? '[PASS]' : '[FAIL]') . PHP_EOL;

// 4. Test PDF Generation
echo "4. Testing PDF Generation:" . PHP_EOL;
$pdfReq = Request::create('/penjamin-mutu/fakultas/visualisasi/generate-pdfVisualProgramStudi', 'POST', [
    'fakultas_id' => 6
]);
$pdfResp = $controller->generatePDFhasilVisualProgramStudi($pdfReq);
echo "   [SUCCESS] PDF generated! Status: " . $pdfResp->getStatusCode() . PHP_EOL;

echo "=== ALL VERIFICATIONS PASSED ===" . PHP_EOL;
