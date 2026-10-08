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

        // Normalisasi otoritas alias Koordinator/Kepala Program Studi
        $normalizedOtoritas = $otoritas;
        if (in_array('Koordinator Program Studi', $otoritas) && !in_array('Kepala Program Studi', $normalizedOtoritas)) {
            $normalizedOtoritas[] = 'Kepala Program Studi';
        }
        if (in_array('Kepala Program Studi', $otoritas) && !in_array('Koordinator Program Studi', $normalizedOtoritas)) {
            $normalizedOtoritas[] = 'Koordinator Program Studi';
        }

        if (in_array($userOtoritas, $normalizedOtoritas)) {
            return $next($request);
        }

        // Koordinator Program Studi (Kepala Program Studi) memiliki semua hak akses fitur Dosen
        if (in_array('Dosen', $otoritas) && in_array($userOtoritas, ['Koordinator Program Studi', 'Kepala Program Studi'])) {
            return $next($request);
        }

        return redirect('/');
    }
}
