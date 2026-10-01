<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
$brokenCount = 0;
foreach ($users as $u) {
    $rawImg = $u->img;
    if (empty($rawImg)) {
        echo "User {$u->id} ({$u->email}): EMPTY img\n";
        $brokenCount++;
        continue;
    }
    $fullPath = public_path('assets/img/pp/' . $rawImg);
    if (!file_exists($fullPath)) {
        echo "User {$u->id} ({$u->email}): FILE NOT FOUND -> '{$rawImg}'\n";
        $brokenCount++;
    }
}
echo "Total broken profile image paths: {$brokenCount}\n";
