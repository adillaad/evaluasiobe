<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CPMK;
use App\Models\SubCpmk;
use Illuminate\Support\Facades\DB;

echo "=== CPMKs with non-empty uraian ===\n";
$cpmks = CPMK::whereNotNull('uraian')->where('uraian', '!=', '')->with(['cpl', 'subCpmks'])->get();
echo "Total: " . $cpmks->count() . "\n";
foreach ($cpmks as $cp) {
    $cplJudul = $cp->cpl ? $cp->cpl->kode : '-';
    $kurId = $cp->cpl ? $cp->cpl->id_kurikulum : '-';
    echo "- [ID: {$cp->id}] Kode: {$cp->kode} (CPL: {$cplJudul}, Kur: {$kurId}): {$cp->uraian}\n";
    foreach ($cp->subCpmks as $sub) {
        echo "   * [Sub-CPMK: {$sub->kode}] {$sub->uraian}\n";
    }
}

echo "\n=== Sub-CPMKs with non-empty uraian ===\n";
$subCpmks = SubCpmk::whereNotNull('uraian')->where('uraian', '!=', '')->take(30)->get();
echo "Total with non-empty uraian: " . SubCpmk::whereNotNull('uraian')->where('uraian', '!=', '')->count() . "\n";
foreach ($subCpmks as $sub) {
    echo "- [Sub-CPMK ID: {$sub->id}] Kode: {$sub->kode} (CPMK ID: {$sub->cpmk_id}): {$sub->uraian}\n";
}
