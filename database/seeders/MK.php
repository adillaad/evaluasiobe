<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class MK extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mks')->insert([
            'kode' => 'COM' . Str::random(6),
            'nama' => Str::random(4) . " " . Str::random(5),
            'deskripsi' => Str::random(255),
            'rumpun' => 'Wajib',
            'semester' => '3',
            'prasyarat' => Str::random(4) . " " . Str::random(5),
            'kurikulum' => '3006',
            'bobot_teori' => '2',
            'bobot_praktikum' => '1',
            'dosen' => 'Romdoni',
            'materi' => Str::random(255),
            'pustaka_utama' => Str::random(9) . " " . Str::random(12),
            'pustaka_pendukung' => Str::random(9) . " " . Str::random(12),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
