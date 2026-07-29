<?php

namespace App\Http\Middleware;

use Closure;

class PenjaminMutuFakultas extends AuthorityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    protected function requiredAuthority()
    {
        return 'Penjamin Mutu Fakultas';
    }
}
