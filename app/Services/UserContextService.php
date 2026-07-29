<?php

namespace App\Services;

use App\Models\User;
use App\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Exception;

class UserContextService
{
    /**
     * Mengganti prodi aktif untuk seorang user.
     * Semua operasi dibungkus dalam transaksi database untuk keamanan.
     *
     * @param User $user User yang akan diubah konteksnya.
     * @param int $newProdiId ID dari prodi baru yang akan diaktifkan.
     * @return void
     * @throws Exception Jika terjadi kesalahan validasi atau database.
     */
    public function switchActiveProdi(User $user, int $newProdiId): void
    {
        // 1. Validasi: Pastikan user memang terhubung dengan prodi yang akan diaktifkan.
        // Ini adalah langkah keamanan untuk mencegah user mengaktifkan prodi yang bukan miliknya.
        $hasAccess = $user->prodis()->where('prodi_id', $newProdiId)->exists();
        if (!$hasAccess) {
            throw new Exception("Akses ditolak: User tidak terdaftar di prodi ini.");
        }

        // 2. Validasi: Ambil data prodi baru beserta relasi fakultasnya.
        // Eager loading `fakultas` agar lebih efisien.
        $newProdi = Prodi::with('fakultas')->find($newProdiId);
        if (!$newProdi || !$newProdi->fakultas) {
            throw new Exception("Data Prodi atau Fakultas tidak lengkap.");
        }

        // 3. Mulai Transaksi: Ini adalah bagian terpenting.
        // Semua perintah di dalam blok ini dianggap sebagai satu kesatuan.
        // Jika salah satu gagal, semua akan dibatalkan (rollback).
        DB::transaction(function () use ($user, $newProdi) {
            
            // a. Non-aktifkan semua entri di tabel pivot untuk user ini.
            // Ini membersihkan status aktif yang lama.
            $user->prodis()->updateExistingPivot(
                $user->prodis()->pluck('prodi.id'), // Ambil semua ID prodi milik user
                ['active' => false]
            );

            // b. Aktifkan prodi yang baru di tabel pivot.
            $user->prodis()->updateExistingPivot($newProdi->id, ['active' => true]);

            // c. Sinkronkan data ke kolom lama di tabel 'users'.
            // Ini untuk memastikan semua kode lama Anda tetap berfungsi.
            $user->update([
                'id_prodiUser' => $newProdi->id,
                'id_fakultasUser' => $newProdi->id_fakultas,
                'id_universitasUser' => $newProdi->fakultas->id_universitas,
            ]);
        });
    }
}