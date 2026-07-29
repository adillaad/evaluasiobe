<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $legacy = filter_var(env('LANDING_LEGACY', false), FILTER_VALIDATE_BOOLEAN);

        $beritas = Berita::where('status', 'published')
            ->latest()
            ->when(! $legacy, fn ($query) => $query->take(2))
            ->get();

        $beritas->transform(function ($berita) {
            $berita->konten = Str::limit(strip_tags($berita->konten), 90, '...');
            return $berita;
        });

        $view = $legacy ? 'guest.backup.landing-page' : 'guest.landing-page';

        return view($view, compact('beritas'));
    }
}
