<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Prodi;
use App\Models\User;

$prodis = Prodi::with('fakultas.universitas')->whereIn('id', [11, 12, 20])->get();
foreach ($prodis as $p) {
    echo "Prodi ID: {$p->id} | Nama: {$p->nama} | Fakultas ID: " . ($p->fakultas ? $p->fakultas->id . " ({$p->fakultas->nama})" : "NULL") . " | Univ ID: " . ($p->fakultas && $p->fakultas->universitas ? $p->fakultas->universitas->id . " ({$p->fakultas->universitas->nama})" : "NULL") . "\n";
}

echo "\n=== USERS WITH OTORITAS Admin Universitas ===\n";
$users = User::whereHas('otoritas', function($q){ $q->where('otoritas', 'Admin Universitas'); })->get();
foreach ($users as $u) {
    echo "User ID: {$u->id} | Name: {$u->name} | id_universitasUser: {$u->id_universitasUser}\n";
}
