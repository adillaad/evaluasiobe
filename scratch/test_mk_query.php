<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate logged in user with id_prodiUser = 17
$user = \App\Models\User::where('id_prodiUser', 17)->first();
auth()->login($user);

$mk = \App\Models\MK::find('UNI101001');
echo "Kode: " . $mk->kode . "\n";
echo "Nama: " . $mk->nama . "\n";
echo "Kurikulum via \$mk->kurikulum: " . ($mk->kurikulum->tahun ?? 'NULL') . "\n";
