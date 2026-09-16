<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('id', 149)->first(); // Penjamin Mutu Universitas
auth()->login($user);

$controller = app()->make(App\Http\Controllers\PenjaminMutu\VisualisasiController::class);

foreach ([2020, 2021, 2022, 2023] as $angkatan) {
    $req = new Illuminate\Http\Request([
        'angkatan' => $angkatan,
        'universitas' => $user->universitas->nama ?? '',
        'prodi' => 4,
    ]);
    $res = $controller->getNpmByAngkatan($req);
    echo "Penjamin Mutu Univ (Prodi 4, Angkatan {$angkatan}): \n" . substr($res, 0, 150) . "...\n";
}

$userKaprodi = App\Models\User::where('id', 141)->first(); // Kaprodi Prodi 4
auth()->login($userKaprodi);
foreach ([2020, 2021, 2022, 2023] as $angkatan) {
    $req = new Illuminate\Http\Request([
        'angkatan' => $angkatan,
        'universitas' => $userKaprodi->universitas->nama ?? '',
        'prodi' => 4,
    ]);
    $res = $controller->getNpmByAngkatan($req);
    echo "Kaprodi (Prodi 4, Angkatan {$angkatan}): \n" . substr($res, 0, 150) . "...\n";
}
