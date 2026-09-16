<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = [
    'Penjamin Mutu Univ' => 149,
    'Penjamin Mutu Fakultas' => 124,
    'Penjamin Mutu Prodi' => 127,
    'Kaprodi' => 141,
    'Dosen' => 150
];

foreach ($roles as $roleName => $userId) {
    $user = App\Models\User::find($userId);
    auth()->login($user);
    echo "Testing View for role {$roleName} (User ID {$userId})...\n";

    if ($roleName === 'Dosen') {
        $view = view('dosen.visualisasi.indexVisualisasi', [
            'user' => $user,
            'title' => 'Visualisasi Mahasiswa',
            'userOtoritas' => $user->otoritas->otoritas ?? 'Dosen'
        ])->render();
    } else {
        $view = view('penjamin-mutu.visualisasi.indexVisualisasi', [
            'user' => $user,
            'title' => 'Visualisasi Mahasiswa',
            'prodi' => DB::table('prodi')->get(),
            'userOtoritas' => $user->otoritas->otoritas ?? ''
        ])->render();
    }

    echo "  -> View render OK! (Length: " . strlen($view) . " bytes)\n";
}
echo "All role views rendered successfully!\n";
