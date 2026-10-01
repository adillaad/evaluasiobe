<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updated = \App\Models\User::whereNull('img')
    ->orWhere('img', '')
    ->update(['img' => 'User-Profile.png']);

echo "Updated {$updated} users to default 'User-Profile.png'\n";
