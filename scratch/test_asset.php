<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "asset('/assets/img/pp/User-Profile.png'): " . asset('/assets/img/pp/User-Profile.png') . "\n";
echo "asset('assets/img/pp/User-Profile.png'):  " . asset('assets/img/pp/User-Profile.png') . "\n";
