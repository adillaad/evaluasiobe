<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MK;

$mks = MK::whereIn('kode', ['IPA825109', 'KIP825202', 'COM825104', 'COM625216', 'COM625214', 'COM625205'])->get();
foreach ($mks as $mk) {
    echo "Kode: {$mk->kode} | Nama: {$mk->nama} | id_prodi: " . var_export($mk->id_prodi, true) . " | id_kurikulum: " . var_export($mk->id_kurikulum, true) . "\n";
}
