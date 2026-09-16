<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MATA KULIAH DENGAN NAMA SECOND LANGUAGE ACQUISITION ===\n";
$mks = DB::table('mks')->where('nama', 'LIKE', '%Second Language%')->get();
foreach ($mks as $mk) {
    echo "ID: {$mk->id} | Kode: {$mk->kode} | Nama: {$mk->nama} | Prodi: {$mk->prodi_id}\n";
    
    // CPMK links via cpmk_mk
    $cpmkMks = DB::table('cpmk_mk')->where('mk_id', $mk->id)->get();
    echo "  CPMKs in cpmk_mk (" . count($cpmkMks) . "):\n";
    foreach ($cpmkMks as $cm) {
        $cpmk = DB::table('cpmks')->where('id', $cm->cpmk_id)->first();
        $cpl = $cpmk ? DB::table('cpls')->where('id', $cpmk->cpl_id)->first() : null;
        echo "    - CPMK ID: {$cm->cpmk_id} | Kode CPMK: " . ($cpmk ? $cpmk->kode : '-') . " | CPL ID: " . ($cpmk ? $cpmk->cpl_id : '-') . " | Kode CPL: " . ($cpl ? $cpl->kode : '-') . " | Judul CPL: " . ($cpl ? $cpl->judul : '-') . "\n";
    }

    // Direct mk_cpl mapping
    $mkCpls = DB::table('mk_cpl')->where('mk_id', $mk->id)->get();
    echo "  CPLs in mk_cpl (" . count($mkCpls) . "):\n";
    foreach ($mkCpls as $mc) {
        $cpl = DB::table('cpls')->where('id', $mc->cpl_id)->first();
        echo "    - CPL ID: {$mc->cpl_id} | Kode CPL: " . ($cpl ? $cpl->kode : '-') . " | Judul: " . ($cpl ? $cpl->judul : '-') . "\n";
    }
}

echo "\n=== DATA MAHASISWA HAIKAL (NPM 2118051001) DI TABEL MUTUS ===\n";
$haikal = DB::table('mahasiswas')->where('nama', 'LIKE', '%Haikal%')->first();
if ($haikal) {
    echo "Mahasiswa ID: {$haikal->id} | Nama: {$haikal->nama} | NIM: {$haikal->nim} | Prodi ID: {$haikal->prodi_id} | Angkatan: {$haikal->angkatan}\n";
    $mutus = DB::table('mutus')->where('mahasiswa_id', $haikal->id)->get();
    foreach ($mutus as $m) {
        $mk = DB::table('mks')->where('id', $m->mk_id)->first();
        $cpl = DB::table('cpls')->where('id', $m->cpl_id)->first();
        $cpmk = DB::table('cpmks')->where('id', $m->cpmk_id)->first();
        echo "Mutu ID: {$m->id} | MK: [{$mk?->kode}] {$mk?->nama} (MK ID: {$m->mk_id}) | CPL: [{$cpl?->kode}] (CPL ID: {$m->cpl_id}) | CPMK: [{$cpmk?->kode}] (CPMK ID: {$m->cpmk_id}) | Nilai: {$m->nilai} | Bobot: {$m->bobot} | Evaluasi: {$m->evaluasi} | Tahun: {$m->tahun}\n";
    }
}

echo "\n=== LIST CPL PRODI 17 ===\n";
$cpls = DB::table('cpls')->where('prodi_id', 17)->get();
foreach ($cpls as $cpl) {
    echo "ID: {$cpl->id} | Kode: {$cpl->kode} | Judul: {$cpl->judul}\n";
}
