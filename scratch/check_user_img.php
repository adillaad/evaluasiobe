<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'pmf.demo@evaluasiobe.test')->first();
if ($user) {
    echo "USER ID: " . $user->id . "\n";
    echo "USER IMG: [" . var_export($user->img, true) . "]\n";
} else {
    echo "User not found\n";
}
