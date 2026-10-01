<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ahmad = \App\Models\User::where('email', 'ahmad@gmail.com')->with(['prodi', 'prodis'])->first();
if (!$ahmad) {
    echo "User ahmad@gmail.com not found\n";
    exit;
}

echo "AHMAD ID: {$ahmad->id}\n";
echo "AHMAD NAME: {$ahmad->name}\n";
echo "AHMAD id_prodiUser: {$ahmad->id_prodiUser}\n";
echo "AHMAD id_fakultasUser: {$ahmad->id_fakultasUser}\n";
echo "AHMAD id_universitasUser: {$ahmad->id_universitasUser}\n";
echo "AHMAD OTORITAS (user_otoritas table):\n";
$rows = \DB::table('user_otoritas')->where('user_id', $ahmad->id)->get();
foreach ($rows as $r) {
    print_r($r);
}
echo "AHMAD PRODIS (user_prodi table):\n";
foreach ($ahmad->prodis as $p) {
    echo "  - {$p->nama} (id: {$p->id})\n";
}
