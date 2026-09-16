<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MKS IN PRODI 17 WITH NAME Second Language Acquisition ===\n";
$mks = DB::table('mks')->where('nama', 'LIKE', '%Second Language%')->get();
foreach ($mks as $mk) {
    echo "MK Kode: [{$mk->kode}] | Nama: {$mk->nama} | Kurikulum ID: {$mk->id_kurikulum} | Prodi ID: {$mk->id_prodi}\n";
    
    // cpmk_mk
    $cpmkMks = DB::table('cpmk_mk')->where('mk_kode', $mk->kode)->get();
    echo "  -- CPMKs linked in cpmk_mk (" . count($cpmkMks) . "):\n";
    foreach ($cpmkMks as $cm) {
        $cpmk = DB::table('cpmks')->where('id', $cm->cpmk_id)->first();
        $cpl = $cpmk ? DB::table('cpls')->where('id', $cpmk->cpl_id)->first() : null;
        echo "     CPMK ID: {$cm->cpmk_id} | Kode CPMK: " . ($cpmk ? $cpmk->kode : '-') . " | Judul CPMK: " . ($cpmk ? $cpmk->judul : '-') . " | CPL ID: " . ($cpmk ? $cpmk->cpl_id : '-') . " | CPL Kode: " . ($cpl ? $cpl->kode : '-') . " | CPL Judul: " . ($cpl ? $cpl->judul : '-') . "\n";
    }

    // mk_cpl
    $mkCpls = DB::table('mk_cpl')->where('mk_kode', $mk->kode)->get();
    echo "  -- CPLs linked in mk_cpl (" . count($mkCpls) . "):\n";
    foreach ($mkCpls as $mc) {
        $cpl = DB::table('cpls')->where('id', $mc->cpl_id)->first();
        echo "     CPL ID: {$mc->cpl_id} | CPL Kode: " . ($cpl ? $cpl->kode : '-') . " | CPL Judul: " . ($cpl ? $cpl->judul : '-') . "\n";
    }
}

echo "\n=== HAIKAL MUTUS RECORDS ===\n";
$haikalMutus = DB::table('mutus')->where('nama_mhs', 'LIKE', '%Haikal%')->orWhere('npm', 'LIKE', '%2118051001%')->get();
foreach ($haikalMutus as $hm) {
    echo "ID: {$hm->id} | NPM: {$hm->npm} | Nama: {$hm->nama_mhs} | Angkatan: {$hm->angkatan} | Course (Kode MK): {$hm->Course} | Jenis: {$hm->Jenis} | Nilai: {$hm->Nilai} | Cpl (Kode): {$hm->Cpl} | Cpmk (Kode): {$hm->Cpmk} | Tahun: {$hm->tahun}\n";
}

echo "\n=== ALL CPLS IN PRODI 17 ===\n";
$cpls = DB::table('cpls')->where('id_prodi', 17)->orderBy('id')->get();
foreach ($cpls as $c) {
    echo "ID: {$c->id} | Kode: {$c->kode} | Judul: {$c->judul} | Kurikulum: {$c->id_kurikulum}\n";
}
