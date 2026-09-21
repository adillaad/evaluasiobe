<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'neni.hasnunidah@fkip.unila.ac.id')->first();
auth()->login($user);

$req = new \Illuminate\Http\Request();
$filterData = (new \App\Http\Controllers\PenjaminMutu\AsesmenController())->NA_MK($req)->getData();

$paginatedMKs = $filterData['paginatedMKs'];
echo "Total Paginated MKs: {$paginatedMKs->total()}\n";
echo "Per page: {$paginatedMKs->perPage()}\n";
echo "Last page: {$paginatedMKs->lastPage()}\n\n";

for ($p = 1; $p <= $paginatedMKs->lastPage(); $p++) {
    $reqPage = new \Illuminate\Http\Request(['page' => $p]);
    $resPage = (new \App\Http\Controllers\PenjaminMutu\AsesmenController())->NA_MK($reqPage);
    $pData = $resPage->getData();
    $pMKs = $pData['paginatedMKs'];
    $penilaian = $pData['penilaian'];
    $grouped = $penilaian->groupBy('mk_kode');
    $instrumens = $pData['instrumens'];
    
    echo "--- PAGE {$p} ({$pMKs->count()} items) ---\n";
    foreach ($pMKs->items() as $mkItem) {
        $rows = $grouped->get($mkItem->mk_kode, collect());
        $validInstrumens = $rows->isNotEmpty() ? $instrumens->whereIn('id', $rows->pluck('id')) : collect();
        
        $hasData = ($rows->isNotEmpty() && $validInstrumens->isNotEmpty());
        echo "  MK: {$mkItem->mk_kode} - {$mkItem->mk_nama} | Rows: {$rows->count()} | ValidInstr: {$validInstrumens->count()} | HAS DATA: " . ($hasData ? 'YES' : 'NO') . "\n";
    }
}
