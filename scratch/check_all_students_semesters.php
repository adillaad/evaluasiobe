<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;

$allMhs = Mahasiswa::all();

foreach ($allMhs as $mhs) {
    $prodi = Prodi::find($mhs->id_prodi);
    
    // Group mutus records by course and academic year / semester
    $courses = DB::table('mutus as m')
        ->leftJoin('mks', 'm.Course', '=', 'mks.kode')
        ->leftJoin('tahun_ajaran as ta', 'm.tahun_ajaran_id', '=', 'ta.id')
        ->where(function($q) use ($mhs) {
            $q->where('m.npm', $mhs->NPM)->orWhere('m.NPM', $mhs->NPM);
        })
        ->select(
            'm.Course',
            'mks.nama as nama_mk',
            'mks.semester as mk_semester',
            DB::raw('(mks.bobot_teori + mks.bobot_praktikum) as sks'),
            'm.tahun_ajaran_id',
            'ta.tahun as ta_tahun',
            'ta.jenis_semester as ta_jenis_semester',
            'm.tahun as mutu_tahun'
        )
        ->distinct()
        ->get();

    echo "Mahasiswa: {$mhs->Nama} (NPM: {$mhs->NPM}, Angkatan: {$mhs->angkatan}, Prodi: {$prodi->nama})\n";
    echo "Total MKs: " . $courses->count() . "\n";
    foreach ($courses as $c) {
        $tahunLabel = !empty($c->ta_tahun) ? "{$c->ta_jenis_semester} - {$c->ta_tahun}/" . ($c->ta_tahun + 1) : ($c->mutu_tahun ?: "Semester {$c->mk_semester}");
        echo "  - MK: {$c->Course} ({$c->nama_mk}), SKS: {$c->sks}, Sem MK: {$c->mk_semester}, Periode: {$tahunLabel}\n";
    }
    echo "\n";
}
