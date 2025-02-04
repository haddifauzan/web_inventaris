<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan pengguna sudah login
        if (Auth::id() != 1) { 
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }

        // Lanjutkan permintaan jika login
        return $next($request);
    }
}
