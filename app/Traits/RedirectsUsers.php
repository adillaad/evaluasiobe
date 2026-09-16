<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

trait RedirectsUsers
{
    /**
     * Redirect users based on their role.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        // Ambil otoritas aktif (active = true). Jika tidak ada, ambil otoritas pertama milik pengguna
        $activeOtoritas = $user->otoritas()->where('active', true)->first() ?? $user->otoritas()->first();

        // Jika pengguna tidak punya otoritas sama sekali
        if (!$activeOtoritas) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Anda tidak memiliki otoritas akses. Silakan hubungi admin.']);
        }

        // Pastikan otoritas aktif tersebut ditandai active = true di DB jika belum
        if (!$activeOtoritas->active) {
            $user->otoritas()->update(['active' => false]);
            $activeOtoritas->update(['active' => true]);
        }

        $otoritasName = $activeOtoritas->otoritas;

        // Peta dari nama otoritas ke nama route
        $routes = [
            'Admin' => 'admin.home',
            'Admin Universitas' => 'admin-universitas.daftar-akun-prodi',
            'Wakil Rektor' => 'wakil-rektor.home',
            'Wakil Dekan' => 'wakil-dekan.home',
            'Kepala Program Studi' => 'kepala-program-studi.home',
            'Dosen' => 'dosen.home',
            'Penjamin Mutu' => 'penjamin-mutu.universitas.home',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.home',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.home',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.home',
            'Mahasiswa' => 'mahasiswa.home',
        ];

        // Cek jika pengguna terdaftar di lebih dari 1 prodi
        $userProdisCount = $user->prodis()->count();
        if ($userProdisCount > 1) {
            return redirect()->route('gate.menu');
        }

        if (isset($routes[$otoritasName])) {
            return redirect()->route($routes[$otoritasName]);
        }

        auth()->logout();
        return redirect()->route('login')->withErrors(['email' => 'Otoritas tidak valid.']);
    }
}