<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('user_otoritas')) {
            // 1. Perluas enum untuk memasukkan 'Koordinator Program Studi'
            DB::statement("ALTER TABLE `user_otoritas` MODIFY COLUMN `otoritas` ENUM(
                'Admin',
                'Admin Universitas',
                'Wakil Rektor',
                'Wakil Dekan',
                'Kepala Program Studi',
                'Koordinator Program Studi',
                'Dosen',
                'Penjamin Mutu Universitas',
                'Penjamin Mutu Fakultas',
                'Penjamin Mutu Program Studi',
                'Mahasiswa'
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");

            // 2. Update data dari 'Kepala Program Studi' ke 'Koordinator Program Studi'
            DB::table('user_otoritas')
                ->where('otoritas', 'Kepala Program Studi')
                ->update(['otoritas' => 'Koordinator Program Studi']);

            // 3. Update nama_otoritas jika bernilai 'Kaprodi' atau 'Kepala Program Studi'
            DB::table('user_otoritas')
                ->whereIn('nama_otoritas', ['Kaprodi', 'Kepala Program Studi'])
                ->update(['nama_otoritas' => 'Koorprodi']);
        }

        // 4. Update kolom jabatan di tabel users jika bernilai 'Kaprodi' atau 'Kepala Program Studi'
        if (Schema::hasTable('users')) {
            DB::table('users')
                ->whereIn('jabatan', ['Kaprodi', 'Kepala Program Studi'])
                ->update(['jabatan' => 'Koordinator Program Studi']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_otoritas')) {
            DB::table('user_otoritas')
                ->where('otoritas', 'Koordinator Program Studi')
                ->update(['otoritas' => 'Kepala Program Studi']);

            DB::table('user_otoritas')
                ->where('nama_otoritas', 'Koorprodi')
                ->update(['nama_otoritas' => 'Kaprodi']);

            DB::statement("ALTER TABLE `user_otoritas` MODIFY COLUMN `otoritas` ENUM(
                'Admin',
                'Admin Universitas',
                'Wakil Rektor',
                'Wakil Dekan',
                'Kepala Program Studi',
                'Dosen',
                'Penjamin Mutu Universitas',
                'Penjamin Mutu Fakultas',
                'Penjamin Mutu Program Studi',
                'Mahasiswa'
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");
        }

        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('jabatan', 'Koordinator Program Studi')
                ->update(['jabatan' => 'Kaprodi']);
        }
    }
};
