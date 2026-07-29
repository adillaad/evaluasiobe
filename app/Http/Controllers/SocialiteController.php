<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    use RedirectsUsers;

    private function googleLoginDisabledResponse()
    {
        if (! config('services.google.login_enabled')) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Login dengan Google saat ini tidak tersedia. Silakan gunakan email dan kata sandi.']);
        }

        return null;
    }

    public function redirectToGoogle()
    {
        if ($response = $this->googleLoginDisabledResponse()) {
            return $response;
        }

        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
    }

    public function connectToGoogle()
    {
        if ($response = $this->googleLoginDisabledResponse()) {
            return $response;
        }

        return Socialite::driver('google')->with(['prompt' => 'consent'])->redirect();
    }

    public function handleGoogleCallback()
    {
        if ($response = $this->googleLoginDisabledResponse()) {
            return $response;
        }

        try {
            $googleUser = Socialite::driver('google')->user();
            // dd($googleUser);
            
            if(Auth::check()) {
                // SKENARIO 1: PENGGUNA SUDAH LOGIN (PROSES CONNECT AKUN)
                $user = Auth::user();
                // Cek apakah akun Google ini sudah ditautkan ke pengguna LAIN.
                $existingLink = User::where('google_id', $googleUser->getId())->first();
                if ($existingLink) {
                    return redirect('/profile')->with('error', 'Akun Google ini sudah terhubung dengan pengguna lain.');
                }

                // Jika aman, update data pengguna yang sedang login
                $user->google_id = $googleUser->getId();
                $user->save();

                return redirect('/profile')->with('google', 'Akun berhasil terhubung dengan Google.');
            }else {
                // SKENARIO 2: PENGGUNA ADALAH TAMU (PROSES LOGIN)
                $existingUser = User::where('google_id', $googleUser->getId())->first();
                // dd($googleUser);
                if ($existingUser) {
                    Auth::login($existingUser);
                    request()->session()->regenerate();
                    return $this->redirectBasedOnRole($existingUser);
                }

                return redirect()->route('login')->withErrors(['email' => 'Akun Google ini belum terdaftar.']);
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Terjadi kesalahan tidak terduga saat login dengan Google.']);
        }
    }
}
