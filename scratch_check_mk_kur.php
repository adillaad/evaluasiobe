<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use DB;

$count = DB::table('mk_kurikulum')->count();
echo "Total records in mk_kurikulum: {$count}\n";

$sample = DB::table('mk_kurikulum')->limit(5)->get();
echo json_encode($sample, JSON_PRETTY_PRINT) . "\n";
