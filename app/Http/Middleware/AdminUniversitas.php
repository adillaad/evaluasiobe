<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminUniversitas
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->otoritas->otoritas == 'Admin Universitas') {
            return $next($request);
        } elseif (auth()->user()->otoritas->otoritas == 'Admin') {
            return redirect()->route('admin.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Wakil Dekan') {
            return redirect()->route('wakil-dekan.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Kepala Program Studi') {
            return redirect()->route('kepala-program-studi.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Dosen') {
            return redirect()->route('dosen.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Penjamin Mutu Universitas') {
            return redirect()->route('penjamin-mutu.universitas.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Penjamin Mutu Fakultas') {
            return redirect()->route('penjamin-mutu.fakultas.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Penjamin Mutu Program Studi') {
            return redirect()->route('penjamin-mutu.program-studi.home');
        }
        return redirect('/');
    }
}
