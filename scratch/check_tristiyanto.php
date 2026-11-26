<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Kurikulum;

$users = User::with('otoritas', 'prodi')->where('name', 'like', '%tristiyanto%')->orWhere('email', 'like', '%tristiyanto%')->get();
foreach ($users as $u) {
    echo "User: " . $u->name . " | Email: " . $u->email . " | Prodi: " . ($u->prodi->nama ?? '-') . " | Otoritas: " . ($u->otoritas->otoritas ?? '-') . PHP_EOL;
}

$kurs = Kurikulum::where('tahun', 'like', '%2021%')->get();
foreach ($kurs as $k) {
    echo "Kurikulum: " . $k->tahun . " (ID: " . $k->id . ", Prodi ID: " . $k->id_prodi . ")" . PHP_EOL;
}
