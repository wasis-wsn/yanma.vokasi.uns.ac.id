<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Mendapatkan peran pengguna saat ini
        $userRole = Auth::user()->roles->gate_name;

        // Memeriksa apakah peran pengguna saat ini ada dalam daftar peran yang diperbolehkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika peran pengguna tidak ditemukan dalam daftar peran yang diperbolehkan, maka kembalikan respon Forbidden
        abort(403, 'Unauthorized action.');
    }
}
