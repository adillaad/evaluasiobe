<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$imgs = \App\Models\User::select('img', \DB::raw('count(*) as total'))
    ->groupBy('img')
    ->get();

foreach ($imgs as $item) {
    echo "IMG: [" . var_export($item->img, true) . "] => Total: " . $item->total . "\n";
}
