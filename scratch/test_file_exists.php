<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pathWithNull = public_path('assets/img/pp/' . null);
echo "Path with null: " . $pathWithNull . "\n";
echo "file_exists: " . (file_exists($pathWithNull) ? 'TRUE' : 'FALSE') . "\n";
echo "is_file:     " . (is_file($pathWithNull) ? 'TRUE' : 'FALSE') . "\n";
