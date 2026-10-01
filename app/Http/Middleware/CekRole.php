<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$otoritas)
    {
        $userOtoritas = $request->user()->otoritas->otoritas ?? '';

        if (in_array($userOtoritas, $otoritas)) {
            return $next($request);
        }

        // Kaprodi (Kepala Program Studi) memiliki semua hak akses fitur Dosen
        if (in_array('Dosen', $otoritas) && $userOtoritas === 'Kepala Program Studi') {
            return $next($request);
        }

        return redirect('/');
    }
}
