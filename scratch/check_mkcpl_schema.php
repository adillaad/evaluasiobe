<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== mk_cpl schema ===\n";
$cols = DB::select('SHOW COLUMNS FROM mk_cpl');
foreach ($cols as $c) {
    echo $c->Field . ' | ' . $c->Type . ' | Null:' . $c->Null . ' | Default:' . $c->Default . "\n";
}

echo "\n=== Sample mk_cpl data (first 10) ===\n";
$rows = DB::table('mk_cpl')->limit(10)->get();
foreach ($rows as $row) {
    echo "id:{$row->id} | mk_kode:{$row->mk_kode} | cpl_id:{$row->cpl_id} | created_at:{$row->created_at} | updated_at:{$row->updated_at}\n";
}

echo "\n=== Count rows with null created_at ===\n";
$nullCount = DB::table('mk_cpl')->whereNull('created_at')->count();
echo "Null created_at: $nullCount\n";

$totalCount = DB::table('mk_cpl')->count();
echo "Total rows: $totalCount\n";
