<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Membuat kunci unik untuk rate limiter berdasarkan email dan IP
        $throttleKey = 'password.reset|' . Str::lower($request->input('email')) . '|' . $request->ip();

        // Cek apakah sudah terlalu banyak percobaan
        if (RateLimiter::tooManyAttempts($throttleKey, 1)) { // Maksimal 1 percobaan
            // Jika ya, dapatkan sisa waktu tunggu dalam detik
            $seconds = RateLimiter::availableIn($throttleKey);

            // Kirim pesan error validasi, sama seperti error lainnya
            throw ValidationException::withMessages([
                'email' => __('Terlalu banyak percobaan. Silakan coba lagi dalam :seconds detik.', ['seconds' => $seconds]),
            ]);
        }

        RateLimiter::hit($throttleKey, 1 * 60);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Jika user tidak ditemukan, kirim kembali error validasi
            // Ini adalah cara standar dan bersih di Laravel
            throw ValidationException::withMessages([
                'email' => __('Email yang Anda masukkan tidak terdaftar.'),
            ]);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
