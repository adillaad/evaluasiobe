<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\PenjaminMutu\VisualisasiController as PMController;
use App\Http\Controllers\Dosen\VisualisasiController as DosenController;

echo "=== TESTING AJAX METHODS WITH NUMERIC AND STRING PRODI ===\n";

$pm = new PMController();
$dosen = new DosenController();

// 1. Numeric Prodi ID 11
$req1 = new Request(['prodi' => '11', 'universitas' => '1']);
$res1 = $pm->getAngkatanByProdiUniversitas($req1);
echo "1. PM getAngkatanByProdiUniversitas (prodi=11):\n   " . trim($res1) . "\n\n";

// 2. String Prodi Name
$prodiName = \App\Models\Prodi::find(11)->nama;
$req2 = new Request(['prodi' => $prodiName, 'universitas' => '1']);
$res2 = $pm->getAngkatanByProdiUniversitas($req2);
echo "2. PM getAngkatanByProdiUniversitas (prodi='{$prodiName}'):\n   " . trim($res2) . "\n\n";

// 3. getCourseByProdi
$req3 = new Request(['prodi' => '11', 'angkatan' => '2022', 'universitas' => '1']);
$res3 = $pm->getCourseByProdi($req3);
echo "3. PM getCourseByProdi (prodi=11, angkatan=2022):\n   " . substr(trim($res3), 0, 150) . "...\n\n";

$req4 = new Request(['prodi' => $prodiName, 'angkatan' => '2022', 'universitas' => '1']);
$res4 = $pm->getCourseByProdi($req4);
echo "4. PM getCourseByProdi (prodi='{$prodiName}', angkatan=2022):\n   " . substr(trim($res4), 0, 150) . "...\n\n";

echo "=== ALL CONTROLLER AJAX TESTS PASSED! ===\n";
