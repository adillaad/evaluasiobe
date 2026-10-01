<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pmfUsers = \DB::table('user_otoritas')
    ->join('users', 'user_otoritas.user_id', '=', 'users.id')
    ->where('user_otoritas.otoritas', 'Penjamin Mutu Fakultas')
    ->select('users.*', 'user_otoritas.otoritas')
    ->get();

echo "User Penjamin Mutu Fakultas yang sudah ada:\n";
foreach ($pmfUsers as $u) {
    echo "- Name: " . $u->name . " | Email: " . $u->email . " | ID Fakultas: " . $u->id_fakultasUser . "\n";
}

// Buat / Reset akun demo testing Penjamin Mutu Fakultas
$demoEmail = 'pmf.demo@evaluasiobe.test';
$user = \App\Models\User::where('email', $demoEmail)->first();

$fakultas = \DB::table('fakultas')->where('id', 14)->first() ?: \DB::table('fakultas')->first();

if (!$user) {
    $user = \App\Models\User::create([
        'name' => 'Demo Penjamin Mutu Fakultas',
        'email' => $demoEmail,
        'password' => bcrypt('password123'),
        'id_fakultasUser' => $fakultas ? $fakultas->id : 14,
        'id_universitasUser' => $fakultas ? $fakultas->id_universitas : 62,
    ]);
} else {
    $user->password = bcrypt('password123');
    $user->id_fakultasUser = $fakultas ? $fakultas->id : 14;
    $user->id_universitasUser = $fakultas ? $fakultas->id_universitas : 62;
    $user->save();
}

// Assign role Penjamin Mutu Fakultas di user_otoritas
\DB::table('user_otoritas')->updateOrInsert(
    ['user_id' => $user->id, 'otoritas' => 'Penjamin Mutu Fakultas'],
    ['nama_otoritas' => 'Penjamin Mutu Fakultas', 'active' => 1, 'updated_at' => now()]
);

// Assign prodi-prodi milik fakultas tersebut di prodi_user
$prodisFak = \DB::table('prodi')->where('id_fakultas', $user->id_fakultasUser)->get();
\DB::table('prodi_user')->where('user_id', $user->id)->delete();
foreach ($prodisFak as $p) {
    \DB::table('prodi_user')->insert([
        'user_id' => $user->id,
        'prodi_id' => $p->id,
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "\n============================================\n";
echo "AKUN DEMO PENJAMIN MUTU FAKULTAS BERHASIL DISIAPKAN\n";
echo "============================================\n";
echo "Email    : " . $user->email . "\n";
echo "Password : password123\n";
echo "Role     : Penjamin Mutu Fakultas\n";
echo "Fakultas : " . ($fakultas ? $fakultas->nama : 'FKIP') . "\n";
echo "Daftar Prodi di Fakultas ini:\n";
foreach ($prodisFak as $p) {
    echo "  - [" . $p->id . "] " . $p->nama . "\n";
}
