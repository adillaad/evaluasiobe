<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PenjaminMutu\VisualisasiController;
use Illuminate\Http\Request;
use App\Models\User;

$user = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Penjamin Mutu Universitas');
})->first();

if (!$user) {
    $user = User::first();
}
auth()->login($user);

$c = new VisualisasiController();
$req = new Request([
    'npm' => '2118051001',
    'prodi' => '17',
    'angkatan' => '2021',
    'universitas' => '62'
]);

$res = $c->hasilVisualMahasiswa($req);
$data = $res->getData(true);

echo "Response keys:\n";
print_r(array_keys($data));
if (isset($data['result'])) {
    echo "Result keys:\n";
    print_r(array_keys($data['result']));
    echo "\nCPL Results:\n";
    foreach ($data['result']['cplResults'] ?? [] as $r) {
        echo " - CPL: {$r['kode']} (ID: {$r['id']}) => Nilai: {$r['nilai']}, Persentase: {$r['persentase']}%\n";
    }
} else {
    print_r($data);
}
