<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\MK;

$users = User::whereHas('otoritas', function($q) {
    $q->whereIn('otoritas', ['Admin Universitas', 'Penjamin Mutu Universitas']);
})->get();

foreach ($users as $user) {
    echo "=========================================\n";
    echo "USER: {$user->name} (Email: {$user->email} | id_univ: {$user->id_universitasUser})\n";

    $query = MK::with(['kurikulum', 'prodi.fakultas.universitas'])
        ->leftJoin('prodi', 'mks.id_prodi', '=', 'prodi.id')
        ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id')
        ->leftJoin('universitas', 'fakultas.id_universitas', '=', 'universitas.id')
        ->leftJoin('kurikulums', 'mks.id_kurikulum', '=', 'kurikulums.id')
        ->select('mks.*');

    if ($user->otoritas->otoritas === 'Admin Universitas') {
        $query->where(function($q) use ($user) {
            $q->where('fakultas.id_universitas', $user->id_universitasUser)
              ->orWhereNull('mks.id_prodi');
        });
    }

    $mks = $query->whereIn('mks.kode', ['KIP825106', 'COM625109', 'COM825102', 'COM625107', 'COM825206', 'COM625306', 'COM625209'])->get();

    echo "Count found: " . count($mks) . "\n";
    foreach ($mks as $mk) {
        $kurTahun = optional($mk->kurikulum)->tahun ?? '-';
        $prodiNama = $mk->prodi ? $mk->prodi->nama : 'MK Universitas';
        $fakultasNama = optional(optional($mk->prodi)->fakultas)->nama ?? 'Universitas';
        echo "  Kode: {$mk->kode} | Prodi: {$prodiNama} | Kur: {$kurTahun} | Fak: {$fakultasNama}\n";
    }
}
