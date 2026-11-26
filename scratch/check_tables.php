<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = ['universitas', 'fakultas', 'prodi', 'users', 'user_otoritas', 'kurikulums', 'profil_lulusan', 'profil_cpl', 'cpls', 'bks', 'bk_cpl', 'bk_mk', 'mks', 'mk_cpl', 'mk_kurikulum', 'cpmks', 'cpmk_mk', 'sub_cpmk', 'mk_sub_cpmk'];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "Table: " . $table . PHP_EOL;
        $columns = Schema::getColumnListing($table);
        echo "Columns: " . implode(', ', $columns) . PHP_EOL;
    } else {
        echo "Table NOT FOUND: " . $table . PHP_EOL;
    }
    echo "-----------------------------------" . PHP_EOL;
}
