<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('email', 'ahmad@gmail.com')->first();
if (!$user) {
    $user = \App\Models\User::where('name', 'like', '%ahmad%')->first();
}
echo "User:\n";
print_r($user ? $user->toArray() : 'Not found');

if ($user) {
    echo "\nProdi User Pivot:\n";
    print_r(\DB::table('prodi_user')->where('user_id', $user->id)->get()->toArray());

    echo "\nFakultas User:\n";
    print_r(\DB::table('fakultas')->where('id', $user->id_fakultasUser)->get()->toArray());
}
