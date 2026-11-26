<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MK;

$mks = MK::where('id_kurikulum', 1)
    ->where('nama', 'not like', '%DUMMY%')
    ->take(10)
    ->get(['kode', 'nama', 'bobot_teori', 'bobot_praktikum', 'semester', 'rumpun']);

foreach ($mks as $m) {
    echo "Kode: {$m->kode} | Nama: {$m->nama} | SKS Teori: {$m->bobot_teori} | Prak: {$m->bobot_praktikum} | Sem: {$m->semester} | Rumpun: {$m->rumpun}" . PHP_EOL;
}
