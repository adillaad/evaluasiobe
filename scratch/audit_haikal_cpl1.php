<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== MAHASISWA COLUMNS ===\n";
print_r(Schema::getColumnListing('mahasiswa'));

echo "\n=== SEARCH FOR HAIKAL IN MAHASISWA ===\n";
$mhs = DB::table('mahasiswa')->where('nama', 'LIKE', '%haikal%')->get();
print_r($mhs);

echo "\n=== SEARCH FOR HAIKAL IN MUTUS ===\n";
$mutus = DB::table('mutus')->where('nama_mhs', 'LIKE', '%haikal%')->get();
echo "Count in mutus: " . $mutus->count() . "\n";
if ($mutus->isNotEmpty()) {
    print_r($mutus->take(3)->toArray());
}

echo "\n=== SEARCH FOR ALL STUDENTS IN ANGKATAN 2021 PRODI 17 ===\n";
$mhs2021 = DB::table('mahasiswa')->where('angkatan', 2021)->get();
print_r($mhs2021);

$mutus2021 = DB::table('mutus')->where('angkatan', 2021)->select('npm', 'nama_mhs', 'id_prodi', 'Course')->distinct()->get();
print_r($mutus2021);
