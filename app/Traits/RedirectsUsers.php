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
        $activeOtoritas = $user->otoritas;

        // Jika pengguna tidak punya otoritas sama sekali
        if (!$activeOtoritas) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Anda tidak memiliki otoritas akses. Silakan hubungi admin.']);
        }

        // Peta dari nama otoritas ke nama route
        $routes = [
            'Admin' => 'admin.home',
            'Admin Universitas' => 'admin-universitas.home',
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

        $otoritasName = $activeOtoritas->otoritas;

        if (isset($routes[$otoritasName])) {
            return redirect()->route($routes[$otoritasName]);
        }

        auth()->logout();
        return redirect()->route('login')->withErrors(['email' => 'Otoritas tidak valid.']);
    }
}