<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::with('otoritas')->get();
foreach ($users as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Otoritas: " . ($u->otoritas->otoritas ?? 'None') . ", Prodi: {$u->id_prodiUser}, Univ: {$u->id_universitasUser}\n";
}

$angkatans = DB::table('mahasiswa')->select('angkatan')->distinct()->get();
echo "Mahasiswa angkatans: " . json_encode($angkatans) . "\n";

$mutusAngkatans = DB::table('mutus')->select('angkatan')->distinct()->get();
echo "Mutus angkatans: " . json_encode($mutusAngkatans) . "\n";
