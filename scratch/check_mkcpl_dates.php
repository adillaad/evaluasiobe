<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if updated_at has any values for old data
echo "=== Rows with null created_at but non-null updated_at ===\n";
$rows = DB::table('mk_cpl')
    ->whereNull('created_at')
    ->whereNotNull('updated_at')
    ->count();
echo "Count: $rows\n";

echo "\n=== Rows with BOTH null created_at AND null updated_at ===\n";
$rows = DB::table('mk_cpl')
    ->whereNull('created_at')
    ->whereNull('updated_at')
    ->count();
echo "Count: $rows\n";

// Check which migrations added the mk_cpl data - find earliest created data
echo "\n=== Earliest non-null created_at in mk_cpl ===\n";
$earliest = DB::table('mk_cpl')->whereNotNull('created_at')->min('created_at');
echo "Earliest: $earliest\n";

echo "\n=== Latest non-null created_at in mk_cpl ===\n";
$latest = DB::table('mk_cpl')->whereNotNull('created_at')->max('created_at');
echo "Latest: $latest\n";
