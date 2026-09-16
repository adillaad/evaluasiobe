<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;

$mhs = Mahasiswa::where('NPM', '2217051008')->first();
$prodi = Prodi::find($mhs->id_prodi);
$isAptikom = (bool)($prodi->is_aptikom ?? false);

echo "Mahasiswa: {$mhs->Nama} ({$mhs->NPM}) - Angkatan: {$mhs->angkatan}\n";

// Get all distinct courses with their semester / academic year
$records = DB::table('mutus as m')
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

print_r($records->toArray());
