<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$targetDate = '2026-10-12';

// Cek akun tristiyanto
$user = DB::table('users')->where('email', 'tristiyanto.1981@fmipa.unila.ac.id')->first();
if (!$user) {
    echo "User tristiyanto tidak ditemukan!\n";
    exit;
}
$idProdi = $user->id_prodi ?? $user->primary_prodi_id ?? '-';
echo "User: {$user->name} (id: {$user->id}, prodi: {$idProdi})\n\n";

// Tabel-tabel yang punya timestamps dan relevan untuk dosen
$tables = [
    'mk_cpl'    => ['mk_kode', 'cpl_id'],
    'cpmk_mk'   => ['mk_kode', 'cpmk_id'],
    'mutus'     => ['id_user', 'nim'],
    'rpss'      => ['kode_mk', 'pengembang'],
];

foreach ($tables as $table => $cols) {
    echo "=== Tabel: $table (tanggal $targetDate) ===\n";
    
    // Cek apakah tabel punya created_at
    try {
        $rows = DB::table($table)
            ->whereDate('created_at', $targetDate)
            ->get();
        
        if ($rows->isEmpty()) {
            echo "  Tidak ada data yang diinput pada tanggal ini.\n";
        } else {
            echo "  Ditemukan " . $rows->count() . " baris:\n";
            foreach ($rows as $row) {
                $info = [];
                foreach ($cols as $col) {
                    if (isset($row->$col)) {
                        $info[] = "$col:" . $row->$col;
                    }
                }
                echo "    id:{$row->id} | " . implode(' | ', $info) . " | created_at:{$row->created_at}\n";
            }
        }
    } catch (\Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Cek tabel users - login terakhir atau updated_at
echo "=== Update terakhir akun tristiyanto ===\n";
echo "  users.updated_at: {$user->updated_at}\n";
