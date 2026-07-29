<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Kode Anda di sini sudah sempurna.
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $allRoutePrefixes = [
                    'Admin'                       => 'admin.',
                    'Admin Universitas'           => 'admin-universitas.',
                    'Penjamin Mutu Universitas'   => 'penjamin-mutu.universitas.',
                    'Penjamin Mutu Fakultas'      => 'penjamin-mutu.fakultas.',
                    'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.',
                    'Kepala Program Studi'        => 'kepala-program-studi.',
                    'Dosen'                       => 'dosen.',
                ];

                $userOtoritas = Auth::user()->otoritas->otoritas;
                $currentPrefix = $allRoutePrefixes[$userOtoritas] ?? 'admin.';

                $view->with([
                    'currentPrefix'    => $currentPrefix,
                    'userOtoritas'     => $userOtoritas,
                    'allRoutePrefixes' => $allRoutePrefixes,
                ]);

            } else {
                $view->with([
                    'currentPrefix'    => 'admin.',
                    'userOtoritas'     => null,
                    'allRoutePrefixes' => [],
                ]);
            }
        });
    }
}