<?php

namespace App\Providers;

use App\Models\Theme;
use App\Models\Universitas;
use App\Support\AptikomTheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
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
        $viewsToHandle = [
            'guest.template',
            'admin.template',
            'dosen.template',
            'penjamin-mutu.template',
            'mahasiswa.template',
        ];

        foreach ($viewsToHandle as $viewName) {
            View::composer($viewName, function ($view) {
                try {
                    if (auth()->user()->id_universitasUser) {
                        $theme = Theme::where('universitas_id', auth()->user()->id_universitasUser)
                            ->first();

                        $university = Universitas::find(auth()->user()->id_universitasUser);

                        $defaultLogo = asset('/assets/img/logo_unila.png');
                        
                        // Tambah pengecekan untuk university->img kosong atau null
                        $logoPath = $university && !empty($university->img)
                            ? url($university->img)
                            : $defaultLogo;

                        $universityThemeColor = $theme ? $theme->theme_color : '';

                        $view->with([
                            'themeColor' => AptikomTheme::resolve(auth()->user(), $universityThemeColor),
                            'themeScript' => true,
                            'universityLogo' => $logoPath,
                            'universityName' => $university ? $university->nama : 'Universitas Lampung'
                        ]);
                    } else {
                        $view->with([
                            'themeColor' => AptikomTheme::resolve(auth()->user()),
                            'themeScript' => true,
                            'universityLogo' => asset('/assets/img/logo_unila.png'),
                            'universityName' => 'Universitas Lampung'
                        ]);
                    }
                } catch (\Exception $e) {
                    $view->with([
                        'themeColor' => AptikomTheme::resolve(auth()->user()),
                        'themeScript' => true,
                        'universityLogo' => asset('/assets/img/logo_unila.png'),
                        'universityName' => 'Default University'
                    ]);
                }
            });
        }
    }
}
