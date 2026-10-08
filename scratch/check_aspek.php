<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cpls = DB::table('cpls')->where('id_prodi', 20)->get(['id', 'kode', 'aspek']);
foreach ($cpls as $c) {
    echo "ID: {$c->id}, Kode: {$c->kode}, Aspek: " . var_export($c->aspek, true) . PHP_EOL;
}
