<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]|null  ...$guards
     * @return mixed
     */

    /**
     * Authority to route mapping
     */
    protected $routeMap = [
        'Admin' => 'admin.home',
        'Admin Universitas' => 'admin-universitas.home',
        'Wakil Rektor' => 'wakil-rektor.home',
        'Wakil Dekan' => 'wakil-dekan.home',
        'Kepala Program Studi' => 'kepala-program-studi.home',
        'Penjamin Mutu Universitas' => 'penjamin-mutu.universitas.home',
        'Penjamin Mutu Fakultas' => 'penjamin-mutu.fakultas.home',
        'Penjamin Mutu Program Studi' => 'penjamin-mutu.program-studi.home',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]|null  ...$guards
     * @return mixed
     */

    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $authority = auth()->user()->otoritas->otoritas;
                
                // Other authorities are redirected based on mapping
                if (isset($this->routeMap[$authority])) {
                    return redirect()->route($this->routeMap[$authority]);
                }
            }
        }

        return $next($request);
    }
}
