<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Universitas;
use App\Models\Prodi;

echo "=== STARTING ACCURATE VISUALISASI TEST & VERIFICATION ===\n";

// Clear view cache
Artisan::call('view:clear');
echo "View cache cleared successfully.\n\n";

$dosenUser = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'Dosen');
})->first() ?? User::first();

$pmUser = User::whereHas('otoritas', function($q) {
    $q->where('otoritas', 'like', '%Penjamin Mutu%');
})->first() ?? User::first();

$universitasObj = Universitas::first() ?? (object)['id' => 1, 'nama' => 'Universitas Test', 'img' => ''];
$prodiList = Prodi::all();

$viewsToTest = [
    [
        'name' => 'penjamin-mutu.visualisasi.indexVisualisasi',
        'user' => $pmUser,
        'type' => 'index'
    ],
    [
        'name' => 'dosen.visualisasi.indexVisualisasi',
        'user' => $dosenUser,
        'type' => 'index'
    ],
    [
        'name' => 'penjamin-mutu.visualisasi.indexVisualisasiAngkatan',
        'user' => $pmUser,
        'type' => 'index'
    ],
    [
        'name' => 'dosen.visualisasi.indexVisualisasiAngkatan',
        'user' => $dosenUser,
        'type' => 'index'
    ],
    [
        'name' => 'penjamin-mutu.visualisasi.indexVisualisasiMataKuliah',
        'user' => $pmUser,
        'type' => 'index'
    ],
    [
        'name' => 'dosen.visualisasi.indexVisualisasiMataKuliah',
        'user' => $dosenUser,
        'type' => 'index'
    ],
    [
        'name' => 'penjamin-mutu.visualisasi.hasilVisualisasiCpmkMahasiswa',
        'user' => $pmUser,
        'type' => 'result'
    ],
    [
        'name' => 'dosen.visualisasi.hasilVisualisasiCpmkMahasiswa',
        'user' => $dosenUser,
        'type' => 'result'
    ],
    [
        'name' => 'penjamin-mutu.visualisasi.hasilVisualisasiCpmkAngkatan',
        'user' => $pmUser,
        'type' => 'result'
    ],
    [
        'name' => 'dosen.visualisasi.hasilVisualisasiCpmkAngkatan',
        'user' => $dosenUser,
        'type' => 'result'
    ],
    [
        'name' => 'pdf.reportVisualisasiCPMKAngkatan',
        'user' => $pmUser,
        'type' => 'pdf'
    ],
];

foreach ($viewsToTest as $item) {
    $viewName = $item['name'];
    $user = $item['user'];
    $type = $item['type'];
    auth()->login($user);
    echo "Testing compilation of [{$viewName}] (as {$user->name} / {$user->otoritas->otoritas})... ";
    try {
        if ($type === 'index') {
            $mockData = [
                'universitas' => $universitasObj,
                'prodi' => $prodiList,
            ];
        } else {
            $mockData = [
                'universitas' => 'Universitas Test',
                'universitasImg' => '',
                'prodi' => 'Teknik Informatika',
                'allMhs' => [
                    (object)['npm' => '12345678', 'nama' => 'Budi']
                ],
                'allNamaNpmData' => [
                    ['npm' => '12345678', 'nama' => 'Budi']
                ],
                'allAngkatan' => ['2020', '2021', '2022', '2023'],
                'angkatan' => '2021',
                'namaProdi' => 'Teknik Informatika',
                'nama' => 'Budi',
                'npm' => '12345678',
                'npmMhs' => '12345678',
                'course' => 'Pemrograman Web',
                'courseKode' => 'IF101',
                'courseNama' => 'Pemrograman Web',
                'completeCourseFormat' => 'IF101 - Pemrograman Web',
                'rataRataMhs' => 78.5,
                'rataRataAngkatan' => 74.2,
                'kodeMaxCpmk' => 'CPMK 2',
                'maxCpmk' => 90.0,
                'kodeMinCpmk' => 'CPMK 1',
                'minCpmk' => 50.0,
                'kodeMaxAvg' => 'CPMK 2',
                'maxAvg' => 90.0,
                'kodeMinAvg' => 'CPMK 1',
                'minAvg' => 50.0,
                'totalCpmkCount' => 2,
                'countTercapai' => 2,
                'cpmkTmp' => [
                    1 => [75.5, 60.0, 90.0, 'CPMK 1'],
                    2 => [82.0, 70.0, 95.0, 'CPMK 2']
                ],
                'cpmkTableList' => [
                    [
                        'id' => 1,
                        'kode' => 'CPMK 1',
                        'judul' => 'Mampu memahami konsep dasar',
                        'bobot' => 50,
                        'nilai_mhs' => 75.5,
                        'avg_angkatan' => 70.0,
                        'min_angkatan' => 50.0,
                        'max_angkatan' => 90.0,
                        'status' => 'Tercapai',
                        'posisi' => 'Di Atas Rata-rata'
                    ],
                    [
                        'id' => 2,
                        'kode' => 'CPMK 2',
                        'judul' => 'Mampu menerapkan implementasi',
                        'bobot' => 50,
                        'nilai_mhs' => 82.0,
                        'avg_angkatan' => 75.0,
                        'min_angkatan' => 60.0,
                        'max_angkatan' => 95.0,
                        'status' => 'Tercapai',
                        'posisi' => 'Di Atas Rata-rata'
                    ],
                ],
                'cpmkResultAll' => [
                    (object)['id' => 1, 'kode' => 'CPMK 1', 'judul' => 'Mampu memahami konsep dasar'],
                    (object)['id' => 2, 'kode' => 'CPMK 2', 'judul' => 'Mampu menerapkan implementasi']
                ],
                'kodeMinAvgAngkatan' => [50, 0, 0, 'CPMK 1'],
                'kodeMaxAvgAngkatan' => [90, 0, 0, 'CPMK 2'],
                'soalDesc' => [
                    (object)['Jenis' => 'Tugas 1', 'soal' => 'Buat REST API', 'idSoal' => 10, 'namaCourse' => 'Pemrograman Web']
                ],
                'soalTerendah' => [
                    ['Jenis' => 'Kuis 1', 'soal' => 'Apa itu MVC?', 'idSoal' => 11, 'id' => 1, 'namaCourse' => 'Pemrograman Web', 'types_of_assessment' => 'Kuis 1', 'question' => 'Apa itu MVC?', 'no' => 1]
                ],
                'soalDescBatch' => [
                    (object)['Jenis' => 'UAS', 'soal' => 'Proyek Akhir', 'idSoal' => 12, 'namaCourse' => 'Pemrograman Web']
                ],
                'allNpm' => [
                    (object)['npm' => '12345678', 'nama_mhs' => 'Budi', 'nama' => 'Budi']
                ],
                'radarChartAngkatanImg' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY44YAAAAASUVORK5CYII=',
                'radarChartMahasiswaImg' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY44YAAAAASUVORK5CYII=',
                'rincianCpmkAngkatan' => [
                    ['kode' => 'CPMK 1', 'avgScore' => '75.50'],
                    ['kode' => 'CPMK 2', 'avgScore' => '82.00'],
                ],
                'rincianCpmkMahasiswa' => [
                    ['kode' => 'CPMK 1', 'avgScore' => '78.00'],
                    ['kode' => 'CPMK 2', 'avgScore' => '85.00'],
                ],
                'descriptions' => [
                    'CPMK 1: Mampu memahami konsep dasar',
                    'CPMK 2: Mampu menerapkan implementasi'
                ],
            ];
        }
        
        $rendered = View::make($viewName, $mockData)->render();
        echo "OK (" . strlen($rendered) . " bytes)\n";
    } catch (\Throwable $e) {
        echo "FAILED: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
    }
}

echo "\n=== ALL CHECKS COMPLETED ===\n";
