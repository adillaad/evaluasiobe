<?php

namespace App\Services;

use App\Models\User;
use App\Models\Prodi;
use App\Models\UserOtoritas;
use Illuminate\Support\Facades\DB;
use Exception;

class UserContextService
{
    /**
     * Mengganti prodi aktif untuk seorang user.
     * Jika user berpindah ke prodi yang BUKAN prodi utamanya (misal: sebagai Dosen Pengampu),
     * maka otoritas aktifnya otomatis berpindah menjadi 'Dosen'.
     * Jika berpindah kembali ke prodi utamanya, otoritas aktifnya kembali ke otoritas administratif utamanya (Kaprodi / TPMPS / dll).
     *
     * @param User $user User yang akan diubah konteksnya.
     * @param int $newProdiId ID dari prodi baru yang akan diaktifkan.
     * @return void
     * @throws Exception Jika terjadi kesalahan validasi atau database.
     */
    public function switchActiveProdi(User $user, int $newProdiId): void
    {
        // 1. Inisialisasi primary_prodi_id jika belum ada
        if (!$user->primary_prodi_id && $user->id_prodiUser) {
            $user->update(['primary_prodi_id' => $user->id_prodiUser]);
            $user->refresh();
        }

        // 2. Validasi: Pastikan user terhubung dengan prodi atau miliki hak akses level universitas/admin.
        $hasAccess = $user->prodis()->where('prodi_id', $newProdiId)->exists();
        $userOtoritasName = optional($user->otoritas)->otoritas;

        if (!$hasAccess && in_array($userOtoritasName, ['Admin', 'Admin Universitas', 'Penjamin Mutu Universitas'])) {
            DB::table('prodi_user')->insertOrIgnore([
                'user_id' => $user->id,
                'prodi_id' => $newProdiId,
                'active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $hasAccess = true;
        }

        if (!$hasAccess) {
            throw new Exception("Akses ditolak: User tidak terdaftar di prodi ini.");
        }

        // 3. Ambil data prodi baru beserta relasi fakultasnya.
        $newProdi = Prodi::with('fakultas')->find($newProdiId);
        if (!$newProdi || !$newProdi->fakultas) {
            throw new Exception("Data Prodi atau Fakultas tidak lengkap.");
        }

        // 4. Mulai Transaksi Database
        DB::transaction(function () use ($user, $newProdi, $newProdiId) {
            // a. Non-aktifkan semua entri di tabel pivot prodi_user untuk user ini.
            DB::table('prodi_user')
                ->where('user_id', $user->id)
                ->update(['active' => false]);

            // b. Aktifkan prodi yang baru di tabel pivot prodi_user.
            DB::table('prodi_user')
                ->where('user_id', $user->id)
                ->where('prodi_id', $newProdi->id)
                ->update(['active' => true]);

            // c. Tentukan Otoritas/Role berdasarkan apakah prodi baru adalah prodi utama atau prodi dosen pengampu
            $primaryProdiId = $user->primary_prodi_id ?? $user->id_prodiUser;

            if ($primaryProdiId && (int)$newProdiId !== (int)$primaryProdiId) {
                // User beralih ke prodi di mana dia adalah Dosen Pengampu
                // Pastikan otoritas 'Dosen' ada untuk user ini
                $dosenOtoritas = DB::table('user_otoritas')
                    ->where('user_id', $user->id)
                    ->where('otoritas', 'Dosen')
                    ->first();

                if (!$dosenOtoritas) {
                    DB::table('user_otoritas')->insert([
                        'user_id' => $user->id,
                        'otoritas' => 'Dosen',
                        'nama_otoritas' => 'Dosen',
                        'active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Deaktivasi semua otoritas dan aktifkan 'Dosen'
                DB::table('user_otoritas')
                    ->where('user_id', $user->id)
                    ->update(['active' => false]);

                DB::table('user_otoritas')
                    ->where('user_id', $user->id)
                    ->where('otoritas', 'Dosen')
                    ->update(['active' => true]);

            } else {
                // User beralih kembali ke prodi utamanya -> kembalikan ke role terakhir yang digunakannya di prodi utama
                $lastOtoritasId = session('last_primary_otoritas_id_' . $user->id);
                $targetOtoritas = null;

                if ($lastOtoritasId) {
                    $targetOtoritas = DB::table('user_otoritas')
                        ->where('user_id', $user->id)
                        ->where('id', $lastOtoritasId)
                        ->first();
                }

                // Jika tidak ada di session, gunakan otoritas yang saat ini ditandai active=true jika bukan Dosen, atau otoritas pertama
                if (!$targetOtoritas) {
                    $targetOtoritas = DB::table('user_otoritas')
                        ->where('user_id', $user->id)
                        ->where('active', true)
                        ->first();
                }

                if (!$targetOtoritas) {
                    $targetOtoritas = DB::table('user_otoritas')
                        ->where('user_id', $user->id)
                        ->first();
                }

                if ($targetOtoritas) {
                    DB::table('user_otoritas')
                        ->where('user_id', $user->id)
                        ->update(['active' => false]);

                    DB::table('user_otoritas')
                        ->where('id', $targetOtoritas->id)
                        ->update(['active' => true]);
                }
            }

            // d. Sinkronkan data ke kolom di tabel 'users'.
            $user->update([
                'id_prodiUser' => $newProdi->id,
                'id_fakultasUser' => $newProdi->id_fakultas,
                'id_universitasUser' => $newProdi->fakultas->id_universitas,
            ]);
        });
    }
}