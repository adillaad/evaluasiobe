<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mk = DB::table('mks')->first();
echo "MKS columns:\n";
print_r(array_keys((array)$mk));

$cpl = DB::table('cpls')->first();
echo "CPLS columns:\n";
print_r(array_keys((array)$cpl));

$cpmk = DB::table('cpmks')->first();
echo "CPMKS columns:\n";
print_r(array_keys((array)$cpmk));

$mutu = DB::table('mutus')->first();
echo "MUTUS columns:\n";
print_r(array_keys((array)$mutu));

$mhs = DB::table('mahasiswas')->first();
echo "MAHASISWAS columns:\n";
print_r(array_keys((array)$mhs));
