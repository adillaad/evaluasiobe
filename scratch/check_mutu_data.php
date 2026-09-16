<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$mhs = Mahasiswa::where('NPM', '2217051008')->first();
$mutu = DB::table('mutus')->where('npm', $mhs->NPM)->first();
print_r($mutu);
