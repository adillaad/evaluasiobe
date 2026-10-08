<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$vis = new \App\Http\Controllers\PenjaminMutu\VisualisasiController();
$dash = new \App\Http\Controllers\PenjaminMutu\DashboardController();
$ref = new ReflectionClass($vis);

echo "==========================================================================================" . PHP_EOL;
echo "CROSSCHECK 1: VISUALISASI PROGRAM STUDI (FKIP ID 16)" . PHP_EOL;
echo "==========================================================================================" . PHP_EOL;

$fData16 = $dash->getFakultasCplAnalytics(16);
$methodFak = $ref->getMethod('getExtendedFakultasAnalytics');
$methodFak->setAccessible(true);
$ext16 = $methodFak->invoke($vis, 16, $fData16);

echo "A. Summary Cards:" . PHP_EOL;
echo "   - Faculty Avg Skor: " . $fData16['summary']['faculty_avg_skor'] . " / 100" . PHP_EOL;
echo "   - Faculty Avg Capaian: " . $fData16['summary']['faculty_avg_capaian'] . "%" . PHP_EOL;
echo "   - Total Prodi: " . $fData16['summary']['total_prodi'] . PHP_EOL;
echo "   - Total Butir CPL: " . $fData16['summary']['total_cpl_count'] . PHP_EOL;

echo PHP_EOL . "B. Tabel Progresi & Line Chart (Tahun: " . json_encode($ext16['availableYears']) . "):" . PHP_EOL;
foreach ($ext16['yearlyProgression'] as $yp) {
    echo "   - {$yp['nama']} ({$yp['jenjang']}):" . PHP_EOL;
    echo "       Skor Tahunan: " . json_encode($yp['scores']) . PHP_EOL;
    echo "       Capaian Tahunan: " . json_encode($yp['capaians']) . PHP_EOL;
    echo "       Overall Skor: {$yp['overall_skor']}, Overall Capaian: {$yp['overall_capaian']}%" . PHP_EOL;
}
echo "   - Footer Rata-rata Skor Fakultas: " . json_encode(array_column($ext16['yearlyAverages'], 'avg_skor')) . PHP_EOL;
echo "   - Footer Rata-rata Capaian Fakultas: " . json_encode(array_column($ext16['yearlyAverages'], 'avg_capaian')) . PHP_EOL;

echo PHP_EOL . "C. 4 Aspek SN-Dikti:" . PHP_EOL;
foreach ($ext16['aspekAnalytics'] as $asp) {
    echo "   - {$asp['aspek']}: Total CPL={$asp['total_cpl']}, Avg Skor={$asp['avg_skor']}, Avg Capaian={$asp['avg_capaian']}%" . PHP_EOL;
}

echo PHP_EOL . "D. Soal Terendah (Count: " . count($ext16['soalTerendah']) . "):" . PHP_EOL;
foreach (array_slice($ext16['soalTerendah'], 0, 3) as $st) {
    echo "   - [{$st['namaCourse']}] {$st['soal']}: Nilai={$st['avg_nilai']}" . PHP_EOL;
}

echo PHP_EOL . "==========================================================================================" . PHP_EOL;
echo "CROSSCHECK 2: VISUALISASI UNIVERSITAS (UNIVERSITAS LAMPUNG ID 57)" . PHP_EOL;
echo "==========================================================================================" . PHP_EOL;

$methodUniv = $ref->getMethod('getExtendedUniversitasAnalytics');
$methodUniv->setAccessible(true);
$extUniv57 = $methodUniv->invoke($vis, 57);

echo "A. Summary Cards Universitas:" . PHP_EOL;
echo "   - Univ Avg Skor: " . $extUniv57['universitasCplData']['summary']['univ_avg_skor'] . " / 100" . PHP_EOL;
echo "   - Univ Avg Capaian: " . $extUniv57['universitasCplData']['summary']['univ_avg_capaian'] . "%" . PHP_EOL;
echo "   - Total Fakultas: " . $extUniv57['universitasCplData']['summary']['total_fakultas_count'] . PHP_EOL;
echo "   - Total Prodi: " . $extUniv57['universitasCplData']['summary']['total_prodi_count'] . PHP_EOL;
echo "   - Total Mhs: " . $extUniv57['universitasCplData']['summary']['total_mhs_evaluated'] . PHP_EOL;
echo "   - Total CPL: " . $extUniv57['universitasCplData']['summary']['total_cpl_count'] . PHP_EOL;

echo PHP_EOL . "B. Tabel Progresi Fakultas & Line Chart (Tahun: " . json_encode($extUniv57['availableYears']) . "):" . PHP_EOL;
foreach ($extUniv57['yearlyProgression'] as $yp) {
    echo "   - {$yp['nama']} (Total Prodi: {$yp['total_prodi']}):" . PHP_EOL;
    echo "       Skor Tahunan: " . json_encode($yp['scores']) . PHP_EOL;
    echo "       Capaian Tahunan: " . json_encode($yp['capaians']) . PHP_EOL;
    echo "       Overall Skor: {$yp['overall_skor']} / 100, Overall Capaian: {$yp['overall_capaian']}%" . PHP_EOL;
}
echo "   - Footer Rata-rata Skor Universitas: " . json_encode(array_column($extUniv57['yearlyAverages'], 'avg_skor')) . PHP_EOL;
echo "   - Footer Rata-rata Capaian Universitas: " . json_encode(array_column($extUniv57['yearlyAverages'], 'avg_capaian')) . PHP_EOL;
