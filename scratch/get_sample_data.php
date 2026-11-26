<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProfilLulusan;
use App\Models\CPL;
use App\Models\MK;
use App\Models\CPMK;

echo "=== PROFIL LULUSAN (Kurikulum 2020, Prodi 4) ===" . PHP_EOL;
$pls = ProfilLulusan::where('kurikulum_id', 1)->take(3)->get();
foreach ($pls as $pl) {
    echo "ID: {$pl->id} | Kode: {$pl->kode} | Nama: {$pl->namaProfil} | Deskripsi: {$pl->deskripsi}" . PHP_EOL;
}

echo PHP_EOL . "=== CPL (Kurikulum 2020, Prodi 4) ===" . PHP_EOL;
$cpls = CPL::where('id_kurikulum', 1)->take(5)->get();
foreach ($cpls as $c) {
    echo "ID: {$c->id} | Kode: {$c->kode} | Aspek: {$c->aspek} | Judul: {$c->judul}" . PHP_EOL;
}

echo PHP_EOL . "=== MATA KULIAH (Kurikulum 2020, Prodi 4) ===" . PHP_EOL;
$mks = MK::where('id_kurikulum', 1)->take(6)->get();
foreach ($mks as $m) {
    echo "Kode: {$m->kode} | Nama: {$m->nama} | SKS Teori: {$m->bobot_teori} | SKS Prak: {$m->bobot_praktikum} | Semester: {$m->semester} | Rumpun: {$m->rumpun}" . PHP_EOL;
}
