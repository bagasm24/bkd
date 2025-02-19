<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSessionExpired
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah session 'nidn' ada
        if (!$request->session()->has('nidn')) {
            // Jika tidak ada, arahkan ke halaman login
            return redirect()->route('login')->with('error', 'Session expired, please login again.');
        }

        // Melanjutkan request jika session masih ada
        return $next($request);
    }
}
