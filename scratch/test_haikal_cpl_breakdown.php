<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use Illuminate\Http\Request;
use App\Models\User;

$user = User::first();
auth()->login($user);

$c = new VisualisasiController();
$req = new Request([
    'npm' => '2118051001',
    'prodi' => '17',
    'angkatan' => '2021',
    'universitas' => '62'
]);

$res = $c->hasilVisualMahasiswa($req);
$data = $res->getData(true)['result'];

echo "=== SKOR CPL OVERALL ===\n";
print_r($data['skorCplOverall']);

echo "\n=== KETERCAPAIAN CPL OVERALL ===\n";
print_r($data['ketercapaianCplOverall']);

echo "\n=== KETERCAPAIAN CPL PER TAHUN ===\n";
print_r($data['ketercapaianCplPerTahun']);

echo "\n=== SKOR CPL PER TAHUN ===\n";
print_r($data['skorCplPerTahun']);
