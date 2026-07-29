<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Other
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
        $routeMap = [
            'Admin' => 'admin.home',
            'Admin Universitas' => 'admin-universitas.home',
            'Wakil Rektor' => 'wakil-rektor.home',
            'Wakil Dekan' => 'wakil-dekan.home',
            'Kepala Program Studi' => 'kepala-program-studi.home',
            'Dosen' => 'dosen.home',
            'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.home',
            'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.home',
            'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.home',
        ];
        
        $otoritas = auth()->user()->otoritas->otoritas;
        
        return isset($routeMap[$otoritas]) 
            ? redirect()->route($routeMap[$otoritas])
            : redirect('/');
    }
}
