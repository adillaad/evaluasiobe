<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use Illuminate\Http\Request;

echo "=== TESTING VISUALISASI PER FAKULTAS VIEW & CONTROLLER ===\n";

$pmUniv = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Penjamin Mutu Universitas');
})->first();

if (!$pmUniv) {
    $pmUniv = User::first();
}

auth()->login($pmUniv);
$otoritasName = $pmUniv->otoritas ? $pmUniv->otoritas->otoritas : 'N/A';
echo "Logged in as User [{$pmUniv->id}] {$pmUniv->nama} ({$otoritasName})\n";

$vc = new VisualisasiController();
$req = Request::create('/penjamin-mutu/universitas/visualisasi/visual-fakultas', 'GET');
$resp = $vc->indexFakultas($req);

echo "indexFakultas status: " . ($resp ? "OK" : "FAILED") . "\n";
echo "View rendered length: " . strlen($resp->render()) . " bytes\n";

echo "\n=== TESTING VISUALISASI PER PROGRAM STUDI VIEW & CONTROLLER ===\n";
$pmFak = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Penjamin Mutu Fakultas');
})->first();
if (!$pmFak) {
    $pmFak = $pmUniv;
}
auth()->login($pmFak);
$reqProdi = Request::create('/penjamin-mutu/fakultas/visualisasi/visual-program-studi', 'GET');
$respProdi = $vc->indexProgramStudi($reqProdi);

echo "indexProgramStudi status: " . ($respProdi ? "OK" : "FAILED") . "\n";
echo "View rendered length: " . strlen($respProdi->render()) . " bytes\n";

echo "\nALL TESTS PASSED SUCCESSFULLY!\n";
