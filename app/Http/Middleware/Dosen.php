<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Dosen
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
        if (auth()->user()->otoritas->otoritas == 'Dosen') {
            return $next($request);
        } elseif (auth()->user()->otoritas->otoritas == 'Wakil Dekan') {
            return redirect()->name('wakil-dekan.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Kepala Program Studi') {
            return redirect()->name('kepala-program-studi.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Admin') {
            return redirect()->name('admin.home');
        } elseif (auth()->user()->otoritas->otoritas == 'Admin Universitas') {
            return redirect()->route('admin-universitas.home');
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
