<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

abstract class AuthorityMiddleware
{
    /**
     * Map of authority types to their respective route names
     */
    protected $routeMap = [
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

    /**
     * Get the authority of the current user
     */
    protected function getUserAuthority()
    {
        return auth()->user()->otoritas->otoritas;
    }

    /**
     * Handle authority-based redirection
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $authority = $this->getUserAuthority();
        
        // If user has the required authority, proceed
        if ($authority === $this->requiredAuthority()) {
            return $next($request);
        }
        
        // Otherwise, redirect based on their authority
        return isset($this->routeMap[$authority]) 
            ? redirect()->route($this->routeMap[$authority])
            : redirect('/');
    }

    /**
     * Define the required authority for this middleware
     */
    abstract protected function requiredAuthority();
}
